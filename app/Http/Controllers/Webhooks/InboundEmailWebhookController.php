<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Mail\InboundEmailRejected;
use App\Mail\InboundTicketReceived;
use App\Mail\NewTicketSubmitted;
use App\Models\InboundEmailMessage;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InboundEmailWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        if (! config('inbound_email.enabled')) {
            abort(404);
        }

        $provider = strtolower((string) config('inbound_email.provider', 'mailgun'));
        if ($provider !== 'mailgun') {
            return response()->json(['message' => 'Unsupported provider'], 422);
        }

        if (! $this->verifyMailgunSignature($request)) {
            Log::warning('Inbound email signature verification failed', [
                'provider' => $provider,
                'sender' => $request->input('sender'),
            ]);

            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $messageId = $this->extractMessageId($request);
        if (! $messageId) {
            return response()->json(['message' => 'Missing provider message id'], 422);
        }

        $sender = $this->normalizeSenderEmail((string) $request->input('sender', ''));
        if (! $sender) {
            return response()->json(['message' => 'Invalid sender'], 422);
        }

        $inboundMessage = InboundEmailMessage::firstOrCreate(
            [
                'provider' => $provider,
                'provider_message_id' => $messageId,
            ],
            [
                'sender_email' => $sender,
                'status' => 'processing',
            ]
        );

        if (! $inboundMessage->wasRecentlyCreated) {
            return response()->json(['message' => 'Duplicate message ignored'], 202);
        }

        $allowedDomain = strtolower((string) config('inbound_email.allowed_domain', 'mwmotor.com'));
        if (Str::after($sender, '@') !== $allowedDomain) {
            $inboundMessage->update([
                'status' => 'rejected_domain',
                'failure_reason' => 'Sender domain not allowed',
                'processed_at' => now(),
            ]);

            if (config('inbound_email.send_rejection_notice')) {
                Mail::to($sender)->send(new InboundEmailRejected(
                    (string) config('inbound_email.rejection_notice_subject'),
                    (string) config('inbound_email.rejection_notice_body')
                ));
            }

            Log::info('Inbound email rejected for sender domain', [
                'provider_message_id' => $messageId,
                'sender' => $sender,
            ]);

            return response()->json(['message' => 'Sender not allowed'], 202);
        }

        $subject = trim((string) $request->input('subject', ''));
        $subject = $subject !== '' ? $subject : '(no subject)';

        $description = $this->extractDescription($request);

        [$ticket, $user, $userCreated, $temporaryPassword, $storedAttachmentCount, $skippedAttachmentCount] = DB::transaction(function () use ($request, $sender, $subject, $description) {
            $userCreated = false;
            $temporaryPassword = null;

            $user = User::where('email', $sender)->first();
            if (! $user) {
                $temporaryPassword = Str::password(24, letters: true, numbers: true, symbols: true, spaces: false);

                $user = new User;
                $user->name = $this->nameFromEmail($sender);
                $user->email = $sender;
                $user->role = 'employee';
                $user->must_change_password = true;
                $user->email_verified_at = now();
                $user->password = Hash::make($temporaryPassword);
                $user->save();

                $userCreated = true;
            }

            $ticket = Ticket::create([
                'requester_email' => $sender,
                'subject' => $subject,
                'description' => $description,
                'category' => 'Email',
                'priority' => 'Normal',
                'status' => 'Open',
            ]);

            [$storedAttachmentCount, $skippedAttachmentCount] = $this->storeInboundAttachments($request, $ticket);

            return [$ticket, $user, $userCreated, $temporaryPassword, $storedAttachmentCount, $skippedAttachmentCount];
        });

        Mail::to($sender)->send(new InboundTicketReceived($ticket, $user, $temporaryPassword));

        $opsEmail = env('OPS_NOTIFY_EMAIL');
        if ($opsEmail) {
            Mail::to($opsEmail)->send(new NewTicketSubmitted($ticket));
        }

        $inboundMessage->update([
            'sender_email' => $sender,
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'user_created' => $userCreated,
            'status' => 'processed',
            'processed_at' => now(),
        ]);

        Log::info('Inbound email processed', [
            'provider_message_id' => $messageId,
            'sender' => $sender,
            'ticket_id' => $ticket->id,
            'user_created' => $userCreated,
            'stored_attachment_count' => $storedAttachmentCount,
            'skipped_attachment_count' => $skippedAttachmentCount,
        ]);

        return response()->json([
            'message' => 'Inbound email processed',
            'ticket_id' => $ticket->id,
        ], 202);
    }

    private function verifyMailgunSignature(Request $request): bool
    {
        $timestamp = (string) $request->input('timestamp', '');
        $token = (string) $request->input('token', '');
        $signature = (string) $request->input('signature', '');
        $signingKey = (string) config('inbound_email.mailgun.signing_key', '');

        if ($timestamp === '' || $token === '' || $signature === '' || $signingKey === '') {
            return false;
        }

        $computed = hash_hmac('sha256', $timestamp.$token, $signingKey);

        return hash_equals($computed, $signature);
    }

    private function extractMessageId(Request $request): ?string
    {
        $value = trim((string) ($request->input('Message-Id')
            ?: $request->input('message-id')
            ?: $request->input('message_id')
            ?: $request->input('Message-ID')));

        if ($value === '') {
            return null;
        }

        return trim($value, "<> ");
    }

    private function normalizeSenderEmail(string $senderRaw): ?string
    {
        $senderRaw = trim($senderRaw);
        if ($senderRaw === '') {
            return null;
        }

        if (preg_match('/<([^>]+)>/', $senderRaw, $matches) === 1) {
            $senderRaw = $matches[1];
        }

        $normalized = strtolower(trim($senderRaw, " \t\n\r\0\x0B\"'"));

        return filter_var($normalized, FILTER_VALIDATE_EMAIL) ? $normalized : null;
    }

    private function extractDescription(Request $request): string
    {
        $plainFields = ['stripped-text', 'body-plain', 'TextBody', 'text'];
        foreach ($plainFields as $field) {
            $value = trim((string) $request->input($field, ''));
            if ($value !== '') {
                return $value;
            }
        }

        $htmlFields = ['stripped-html', 'body-html', 'HtmlBody', 'html'];
        foreach ($htmlFields as $field) {
            $html = trim((string) $request->input($field, ''));
            if ($html === '') {
                continue;
            }

            $plain = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $plain = trim(preg_replace('/\s+/', ' ', $plain) ?? '');

            if ($plain !== '') {
                return $plain;
            }
        }

        return '(no content)';
    }

    private function nameFromEmail(string $email): string
    {
        $local = Str::before($email, '@');
        $humanized = str_replace(['.', '_', '-'], ' ', $local);

        return Str::title($humanized);
    }

    private function storeInboundAttachments(Request $request, Ticket $ticket): array
    {
        $disk = (string) config('inbound_email.attachments.disk', 'local');
        $maxBytes = ((int) config('inbound_email.attachments.max_total_size_mb', 20)) * 1024 * 1024;
        $allowedMimes = array_map(
            static fn ($mime) => strtolower((string) $mime),
            (array) config('inbound_email.attachments.allowed_mime_types', [])
        );

        $files = $this->flattenFiles($request->allFiles());

        $totalBytes = 0;
        $stored = 0;
        $skipped = 0;

        foreach ($files as $file) {
            if (! $file->isValid()) {
                $skipped++;

                continue;
            }

            $size = max(0, (int) $file->getSize());
            $mime = strtolower((string) ($file->getMimeType() ?: $file->getClientMimeType() ?: ''));

            if ($mime === '' || ! in_array($mime, $allowedMimes, true)) {
                $skipped++;

                continue;
            }

            if ($totalBytes + $size > $maxBytes) {
                $skipped++;

                continue;
            }

            $path = $file->store("tickets/{$ticket->id}/inbound", $disk);

            TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $mime,
                'size' => $size,
                'storage_disk' => $disk,
                'storage_path' => $path,
                'source' => 'inbound_email',
            ]);

            $stored++;
            $totalBytes += $size;
        }

        return [$stored, $skipped];
    }

    private function flattenFiles(array $allFiles): array
    {
        $flattened = [];

        array_walk_recursive($allFiles, function ($file) use (&$flattened) {
            if ($file instanceof UploadedFile) {
                $flattened[] = $file;
            }
        });

        return $flattened;
    }
}
