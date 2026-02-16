# Local setup

## Requirements

- PHP 8.2+
- Composer
- Node.js 20+

## First run

1. `composer setup`
2. Confirm `.env` contains required app-specific values:
    - `OPS_NOTIFY_EMAIL`
    - `ALLOWED_SIGNUP_DOMAINS`
3. `composer dev`

## Verification

- App: open `APP_URL`
- Tests: `composer test`
- Lint/format checks: `composer lint && npm run lint && npm run format:check`
