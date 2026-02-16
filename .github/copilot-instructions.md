# Copilot instructions for `pitstop`

## Project shape

- Laravel 12 + Breeze app for internal operations ticketing.
- Main domain code is in `app/Http/Controllers`, `app/Models`, and `resources/views`.
- Role-based authorization uses `employee`, `ops`, `admin` via middleware aliases in `bootstrap/app.php`.

## Domain model and data flow

- Ticket ownership is by `tickets.requester_email` (not `user_id`).
- Replies in `ticket_replies` use `author_role` (`ops` or `requester`).
- Unread state is persisted in `ticket_views.last_viewed_at` and updated in ticket show actions.
- Mail notifications:
    - new ticket -> `App\\Mail\\NewTicketSubmitted`
    - reply -> `App\\Mail\\TicketReplied`
- Attachments are stored under `storage/app/public/tickets/...`; metadata is currently logged, not persisted.

## Route/controller conventions

- Main routes are in `routes/web.php`; auth routes in `routes/auth.php`.
- Ops routes are prefixed `/ops/tickets/*` and use `TicketController::ops*` methods.
- Keep route names stable (`ops.tickets.*`, `my_tickets.*`, `tickets.*`, `admin.users.*`) because Blade templates depend on them.

## Frontend conventions

- Blade files are feature-foldered under `resources/views`.
- Pages typically include page-specific CSS in `@vite([...])`.
- Some view behavior is inline script in Blade (not centralized JS modules).

## Developer workflow

- Bootstrap: `composer setup`
- Full dev stack: `composer dev`
- Tests: `composer test`
- PHP lint/format checks: `composer lint`, `composer format:check`
- Frontend lint/format checks: `npm run lint`, `npm run format:check`

## Environment details

- `OPS_NOTIFY_EMAIL` drives ops mail notifications.
- `ALLOWED_SIGNUP_DOMAINS` controls signup domain allowlist via `config/signup.php`.
- When introducing new env vars, update `.env.example` and relevant config/docs together.
