# Development workflow

## Daily commands

- Start development stack: `composer dev`
- Start background stack: `make stack-up`
- Check background stack: `make stack-status`
- Run backend tests: `composer test`
- Run lint checks: `composer lint && npm run lint` (includes docs lint)
- Run format checks: `composer format:check && npm run format:check`

## Dockerized fallback

When PHP/Composer are unavailable on the host:

- `make setup-docker`
- `make quality-docker`

## Branching convention

- `feature/<short-name>`
- `fix/<short-name>`
- `chore/<short-name>`

## Pull requests

- Keep changes focused and testable
- Include linked issue/context
- Include screenshots for UI changes
- Update docs for workflow/behavior changes
