# Development workflow

## Daily commands

- Start development stack: `composer dev`
- Run backend tests: `composer test`
- Run lint checks: `composer lint && npm run lint` (includes docs lint)
- Run format checks: `composer format:check && npm run format:check`

## Branching convention

- `feature/<short-name>`
- `fix/<short-name>`
- `chore/<short-name>`

## Pull requests

- Keep changes focused and testable
- Include linked issue/context
- Include screenshots for UI changes
- Update docs for workflow/behavior changes
