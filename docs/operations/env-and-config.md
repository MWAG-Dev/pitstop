# Environment and configuration

## Core env values

- `APP_URL`: base URL used in generated links; set to a reachable host when operating beyond localhost.
- `OPS_NOTIFY_EMAIL`: target for Ops notifications from ticket creation/replies.
- `ALLOWED_SIGNUP_DOMAINS`: comma-separated allowlist used by signup validation.
- `CORS_PATHS`: comma-separated route patterns where CORS headers are applied (default `*`).
- `CORS_ALLOWED_METHODS`: allowed CORS methods (default `*`).
- `CORS_ALLOWED_ORIGINS`: comma-separated origins allowed for browser cross-origin requests (default `*`).
- `CORS_ALLOWED_ORIGIN_PATTERNS`: regex-style origin patterns when exact hostnames are not practical.
- `CORS_ALLOWED_HEADERS`: allowed request headers for preflight requests (default `*`).
- `CORS_MAX_AGE`: preflight cache duration in seconds.
- `CORS_SUPPORTS_CREDENTIALS`: set `true` only when using cookies/auth across origins with explicit origins.

## Config paths

- Signup domain parsing: `config/signup.php`
- CORS policy: `config/cors.php`
- Mail behavior: `config/mail.php` and mailables in `app/Mail/`
- Role middleware aliases: `bootstrap/app.php`

## Change rule

When adding or changing env-driven behavior:

1. Update `.env.example`.
2. Update relevant config/controller docs.
3. Add or update tests where behavior changes.

## Remote-host access guidance

- Development default is permissive (`CORS_ALLOWED_ORIGINS=*`) for easy remote testing.
- For production, prefer explicit origin allowlists (for example `https://ops.example.com,https://portal.example.com`).
- If `CORS_SUPPORTS_CREDENTIALS=true`, avoid wildcard origins and use explicit origins.
- For remote browser troubleshooting, verify the app URL/asset host in page source matches your server IP or DNS.
