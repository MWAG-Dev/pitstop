Purpose
-------
This file gives concise, project-specific guidance to AI coding agents working on this repository so they can be productive immediately.

Quick summary
-------------
- Tech: Laravel (app/), PHP 8.2, Vite + Tailwind (resources/ + package.json), Blade views.
- Data model: `Ticket` and `TicketReply` (app/Models) power the workflow; Mailables in `app/Mail` notify users.
- Dev scripts: Composer scripts in `composer.json` (`setup`, `dev`, `test`) and `npm run dev` / `npm run build`.

Big picture (what to know first)
--------------------------------
- This is a single Laravel app. Main flows:
  - Public ticket creation: `TicketController@create` / `store` (routes/web.php).
  - Requester views: `MyTicketsController` (routes `/my-tickets`).
  - Ops views: `TicketController::ops*` routes protected by `ops` middleware (see routes/web.php).
- Key files to read for domain logic: [app/Models/Ticket.php](app/Models/Ticket.php), [app/Models/TicketReply.php](app/Models/TicketReply.php), mailers [app/Mail/NewTicketSubmitted.php](app/Mail/NewTicketSubmitted.php) and [app/Mail/TicketReplied.php](app/Mail/TicketReplied.php).
- Templates: email templates live in `resources/views/emails/*`. Ticket UI lives under `resources/views/tickets`, `resources/views/my_tickets`, and components in `resources/views/components`.

Developer workflows (concrete commands)
-------------------------------------
- Full setup (fresh clone):

  composer install
  cp .env.example .env
  php artisan key:generate
  php artisan migrate
  npm install
  npm run build

  (Or run the scripted helper: `composer run-script setup` or `composer setup`.)

- Local dev (mirrors the `dev` composer script):

  composer run dev

  This uses `concurrently` to run: `php artisan serve`, `php artisan queue:listen --tries=1`, `php artisan pail --timeout=0`, and `npm run dev` (Vite dev server). You can also run the pieces separately while developing.

- Tests: `composer test` or `php artisan test` (uses phpunit). Run focused tests in `tests/Feature`.

Project-specific patterns & conventions
-------------------------------------
- Eloquent models use `protected $fillable` (see `Ticket` and `TicketReply`). Respect these when creating/updating models.
- `Ticket::replies()` sorts replies by `created_at` — preserve ordering when refactoring views or APIs.
- Mailables use promoted constructor properties and `->view('emails.*')`. If you change a Mailable signature, update callers in controllers and jobs.
- Routes separate ops vs requester flows; changes to ops routes usually require `ops` middleware awareness (see [routes/web.php](routes/web.php)).
- Assets: Vite + Tailwind. Editing `resources/js/app.js` or CSS files requires `npm run dev` (development) or `npm run build` (production).

Integration points & runtime notes
---------------------------------
- Mail is used for notifications (app/Mail). Mailables are `Queueable` — the app expects a queue worker in dev (`php artisan queue:listen`).
- `php artisan pail` appears in the dev script; leave it running in the dev environment unless instructed otherwise.
- Database migrations and seeders live in `database/migrations` and `database/seeders`. The composer post-create hooks create `database/database.sqlite` when needed.

How to make safe edits (practical checklist for the agent)
-------------------------------------------------------
1. Run tests after changes: `composer test`.
2. If you touch models or mailers, update corresponding views in `resources/views` and routes in `routes/web.php`.
3. If you add a new route, register it in `routes/web.php` and ensure appropriate middleware (`auth`, `verified`, `ops`) is applied.
4. If you change frontend assets, run `npm run build` and update `resources/views/layouts` if necessary.

Examples (concrete references)
------------------------------
- When changing ticket notification subjects, update [app/Mail/NewTicketSubmitted.php](app/Mail/NewTicketSubmitted.php) and the view `resources/views/emails/new_ticket_submitted.blade.php`.
- To add an ops-only endpoint, follow existing patterns in `routes/web.php` and controller methods like `opsIndex` / `opsShow` on `TicketController`.

If anything is unclear
----------------------
- Ask for clarification on expected behavior before changing public APIs or email wording. Provide file references and small diffs.

Done — please review and tell me which parts need more detail or examples.
