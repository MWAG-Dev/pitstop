# Onboarding

## First-day checklist

1. Clone the repository and bootstrap:
    - Native host: `composer setup`
    - Dockerized fallback: `make setup-docker`
2. Confirm local env values in `.env`:
    - `OPS_NOTIFY_EMAIL`
    - `ALLOWED_SIGNUP_DOMAINS`
    - `CORS_ALLOWED_ORIGINS`
3. Start the app:
    - `composer dev` (foreground)
    - or `make stack-up` (background)
4. If accessing from another machine, open the `Laravel remote` URL printed by `make stack-up`.
5. Visit `/dashboard`, `/tickets/create`, `/my-tickets`, and `/ops/tickets` (if your role allows).
6. Run quality checks:
    - `composer lint`
    - `composer format:check`
    - `npm run lint`
    - `npm run format:check`

## Role model quick reference

- `employee`: create tickets and reply to own tickets.
- `ops`: triage queue and reply to requesters.
- `admin`: everything in `ops` plus user management and ticket delete.

## Where to read next

- Architecture: [`../architecture/overview.md`](../architecture/overview.md)
- Workflow: [`../workflows/development.md`](../workflows/development.md)
- Makefile commands: [`../workflows/makefile-commands.md`](../workflows/makefile-commands.md)
- Documentation policy: [`../governance/documentation-policy.md`](../governance/documentation-policy.md)
