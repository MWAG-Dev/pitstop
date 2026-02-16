# Architecture overview

PitStop is a Laravel monolith with role-based ticket workflows.

## Core modules

- `TicketController` — ticket creation + Ops queue operations
- `MyTicketsController` — requester-facing list/detail/reply
- `AdminUserController` — user and role administration

## Authorization model

- Roles: `employee`, `ops`, `admin`
- Middleware aliases in `bootstrap/app.php`:
    - `ops` -> `App\\Http\\Middleware\\EnsureOps`
    - `admin` -> `App\\Http\\Middleware\\EnsureAdmin`

## Domain model notes

- Ticket ownership is by `requester_email` (not `user_id`)
- `ticket_replies.author_role` indicates `ops` vs `requester`
- Unread tracking is per user/ticket via `ticket_views.last_viewed_at`
