<?php

namespace Tests\Feature;

use App\Mail\InboundTicketReceived;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InboundEmailWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('inbound_email.enabled', true);
        config()->set('inbound_email.provider', 'mailgun');
        config()->set('inbound_email.allowed_domain', 'mwmotor.com');
        config()->set('inbound_email.mailgun.signing_key', 'test-signing-key');
        config()->set('inbound_email.send_rejection_notice', false);
        config()->set('inbound_email.attachments.disk', 'local');
        config()->set('inbound_email.attachments.max_total_size_mb', 20);
        config()->set('inbound_email.attachments.allowed_mime_types', [
            'application/pdf',
            'text/plain',
        ]);
    }

    public function test_allowed_sender_creates_ticket(): void
    {
        Mail::fake();

        $payload = $this->mailgunPayload([
            'sender' => 'Jane Sender <jane@mwmotor.com>',
            'subject' => 'Need help with CRM',
            'body-plain' => 'Please investigate CRM timeout.',
            'Message-Id' => '<msg-allowed-1@example.com>',
        ]);

        $response = $this->post(route('webhooks.inbound-email'), $payload);

        $response->assertStatus(202);

        $this->assertDatabaseHas('tickets', [
            'requester_email' => 'jane@mwmotor.com',
            'subject' => 'Need help with CRM',
            'description' => 'Please investigate CRM timeout.',
            'category' => 'Email',
            'priority' => 'Normal',
            'status' => 'Open',
        ]);
    }

    public function test_disallowed_sender_is_rejected_and_no_ticket_created(): void
    {
        Mail::fake();

        $payload = $this->mailgunPayload([
            'sender' => 'external@gmail.com',
            'Message-Id' => '<msg-disallowed-1@example.com>',
        ]);

        $response = $this->post(route('webhooks.inbound-email'), $payload);

        $response->assertStatus(202);

        $this->assertDatabaseCount('tickets', 0);
        $this->assertDatabaseHas('inbound_email_messages', [
            'provider' => 'mailgun',
            'provider_message_id' => 'msg-disallowed-1@example.com',
            'status' => 'rejected_domain',
        ]);
    }

    public function test_new_sender_creates_user_ticket_and_confirmation_with_password(): void
    {
        Mail::fake();

        $payload = $this->mailgunPayload([
            'sender' => 'new.person@mwmotor.com',
            'subject' => 'Phone issue',
            'body-plain' => 'Desk phone has no dial tone.',
            'Message-Id' => '<msg-new-user-1@example.com>',
        ]);

        $response = $this->post(route('webhooks.inbound-email'), $payload);

        $response->assertStatus(202);

        $user = User::where('email', 'new.person@mwmotor.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue((bool) $user->must_change_password);

        $ticket = Ticket::where('requester_email', 'new.person@mwmotor.com')->first();
        $this->assertNotNull($ticket);

        Mail::assertSent(InboundTicketReceived::class, function (InboundTicketReceived $mail) use ($ticket, $user) {
            return $mail->ticket->is($ticket)
                && $mail->user->is($user)
                && is_string($mail->temporaryPassword)
                && strlen($mail->temporaryPassword) >= 20;
        });
    }

    public function test_existing_user_gets_confirmation_without_password(): void
    {
        Mail::fake();

        User::factory()->create([
            'email' => 'existing@mwmotor.com',
            'must_change_password' => false,
            'role' => 'employee',
        ]);

        $payload = $this->mailgunPayload([
            'sender' => 'existing@mwmotor.com',
            'subject' => 'Website issue',
            'body-plain' => 'The marketing page is slow.',
            'Message-Id' => '<msg-existing-user-1@example.com>',
        ]);

        $response = $this->post(route('webhooks.inbound-email'), $payload);

        $response->assertStatus(202);

        Mail::assertSent(InboundTicketReceived::class, function (InboundTicketReceived $mail) {
            return $mail->user->email === 'existing@mwmotor.com'
                && $mail->temporaryPassword === null;
        });
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $payload = [
            'sender' => 'jane@mwmotor.com',
            'subject' => 'Invalid signature test',
            'body-plain' => 'Should not be accepted.',
            'Message-Id' => '<msg-invalid-signature-1@example.com>',
            'timestamp' => (string) time(),
            'token' => 'invalid-token',
            'signature' => 'not-valid',
        ];

        $response = $this->post(route('webhooks.inbound-email'), $payload);

        $response->assertStatus(401);
        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_duplicate_message_id_does_not_create_duplicate_tickets(): void
    {
        Mail::fake();

        $payload = $this->mailgunPayload([
            'sender' => 'dupe@mwmotor.com',
            'subject' => 'Duplicate test',
            'body-plain' => 'Do not duplicate.',
            'Message-Id' => '<msg-dupe-1@example.com>',
        ]);

        $first = $this->post(route('webhooks.inbound-email'), $payload);
        $second = $this->post(route('webhooks.inbound-email'), $payload);

        $first->assertStatus(202);
        $second->assertStatus(202);

        $this->assertDatabaseCount('tickets', 1);
        $this->assertDatabaseCount('inbound_email_messages', 1);
    }

    public function test_attachments_are_stored_and_linked_to_ticket(): void
    {
        Mail::fake();
        Storage::fake('local');

        $payload = $this->mailgunPayload([
            'sender' => 'attach@mwmotor.com',
            'subject' => 'Attachment test',
            'body-plain' => 'Includes files.',
            'Message-Id' => '<msg-attachment-1@example.com>',
            'attachment-1' => UploadedFile::fake()->create('evidence.pdf', 200, 'application/pdf'),
            'attachment-2' => UploadedFile::fake()->create('malware.exe', 10, 'application/x-msdownload'),
        ]);

        $response = $this->post(route('webhooks.inbound-email'), $payload);

        $response->assertStatus(202);

        $ticket = Ticket::where('requester_email', 'attach@mwmotor.com')->firstOrFail();

        $this->assertDatabaseCount('ticket_attachments', 1);
        $attachment = TicketAttachment::where('ticket_id', $ticket->id)->firstOrFail();
        Storage::disk('local')->assertExists($attachment->storage_path);

        $this->assertSame('application/pdf', $attachment->mime_type);
        $this->assertSame('inbound_email', $attachment->source);
    }

    private function mailgunPayload(array $overrides = []): array
    {
        $timestamp = $overrides['timestamp'] ?? (string) time();
        $token = $overrides['token'] ?? 'test-token';

        $payload = array_merge([
            'sender' => 'sender@mwmotor.com',
            'subject' => 'Test subject',
            'body-plain' => 'Test body',
            'Message-Id' => '<msg-default@example.com>',
            'timestamp' => $timestamp,
            'token' => $token,
        ], $overrides);

        $payload['signature'] = hash_hmac('sha256', (string) $payload['timestamp'].(string) $payload['token'], 'test-signing-key');

        return $payload;
    }
}
