# Observability and logging

## App logs

- App logging is configured through Laravel logging channels.
- During local development, `composer dev` runs `php artisan pail` for live logs.

## Ticket-specific logging

- Ticket/reply attachment metadata is logged in `TicketController`.
- Attachment files are stored under `storage/app/public/tickets/...`.

## Troubleshooting checklist

1. Check logs for validation/mailer/storage errors.
2. Validate queue/mailer configuration for the current environment.
3. Confirm requester vs ops role behavior and route middleware.
