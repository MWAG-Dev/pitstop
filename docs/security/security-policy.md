# Security policy

## Scope

This application handles internal ticket content and user identity data.

## Security boundaries

- Authenticated + verified access required for app workflows.
- Role-enforced boundaries via middleware aliases (`ops`, `admin`).
- Signup is domain-restricted via `ALLOWED_SIGNUP_DOMAINS`.

## Development guardrails

- Do not commit secrets or real credentials.
- Keep `.env` local only; use `.env.example` for placeholders.
- Validate authorization on new routes/controllers before merging.
