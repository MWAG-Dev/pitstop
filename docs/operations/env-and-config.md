# Environment and configuration

## Core env values

- `OPS_NOTIFY_EMAIL`: target for Ops notifications from ticket creation/replies.
- `ALLOWED_SIGNUP_DOMAINS`: comma-separated allowlist used by signup validation.

## Config paths

- Signup domain parsing: `config/signup.php`
- Mail behavior: `config/mail.php` and mailables in `app/Mail/`
- Role middleware aliases: `bootstrap/app.php`

## Change rule

When adding or changing env-driven behavior:

1. Update `.env.example`.
2. Update relevant config/controller docs.
3. Add or update tests where behavior changes.
