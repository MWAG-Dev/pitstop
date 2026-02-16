# Local setup

## Requirements

- PHP 8.2+
- Composer
- Node.js 20+

## First run

1. Preferred bootstrap options:
    - Native host: `composer setup`
    - Dockerized PHP/Composer: `make setup-docker`
2. Confirm `.env` contains required app-specific values:
    - `OPS_NOTIFY_EMAIL`
    - `ALLOWED_SIGNUP_DOMAINS`
    - `CORS_ALLOWED_ORIGINS` (use explicit origins for production)
3. Start the stack:
    - Foreground native: `composer dev`
    - Background mixed stack: `make stack-up`
4. For remote browser access, use the URL printed by `make stack-up` (for example `http://<server-ip>:8001`).

## Verification

- App: open `APP_URL`
- Tests: `composer test`
- Lint/format checks: `composer lint && npm run lint && npm run format:check`

## Background stack controls

- `make stack-status` — check app + Vite background process health
- `make stack-logs` — inspect Laravel container logs and Vite logs
- `make stack-down` — stop background app + Vite processes
- `make stack-up STACK_APP_PORT=8088` — launch on a different app port when 8001 is in use
- `make stack-up VITE_PORT=5175` — launch Vite on a different port when 5173 is in use
