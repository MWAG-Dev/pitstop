# Contributing

## Before opening a PR

1. Ensure tests pass: `composer test` (or `make test-docker`)
2. Ensure style checks pass:
    - `composer lint`
    - `npm run lint`
    - `npm run lint:docs`
    - `composer format:check`
    - `npm run format:check`
    - or one-shot gates: `make quality` / `make quality-docker`
3. Update docs under `docs/` when behavior, architecture, or workflows change.

## PR quality bar

- Clear summary and scope
- Mention affected routes/controllers/models
- Include rollback considerations for migrations/schema changes
- Avoid unrelated refactors in feature fixes
