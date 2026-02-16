# Onboarding

## First-day checklist

1. Clone the repository and run `composer setup`.
2. Confirm local env values in `.env`:
    - `OPS_NOTIFY_EMAIL`
    - `ALLOWED_SIGNUP_DOMAINS`
3. Start the app with `composer dev`.
4. Visit `/dashboard`, `/tickets/create`, `/my-tickets`, and `/ops/tickets` (if your role allows).
5. Run quality checks:
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
- Documentation policy: [`../governance/documentation-policy.md`](../governance/documentation-policy.md)
