# Deployment notes

## Build and runtime assumptions

- PHP 8.2+
- Node.js 20+
- Writable storage directories

## Baseline sequence

1. Install dependencies (`composer install`, `npm ci`).
2. Build assets (`npm run build`).
3. Run migrations (`php artisan migrate --force`).
4. Ensure queue worker is active if mail/jobs are used.

## Post-deploy checks

- Verify dashboard and ticket flows load without errors.
- Verify Ops queue route accessibility for `ops`/`admin` users.
- Verify mail config and env values (`OPS_NOTIFY_EMAIL`) are correct.
