# Documentation policy

## Rule: keep repo root minimal

Only essential root documents are allowed:

- `README.md` (landing page + links)
- `LICENSE` (if present)

All other documentation **must** live under `docs/`.

## Required structure

- Onboarding/setup docs -> `docs/getting-started/`
- Design/system docs -> `docs/architecture/`
- Engineering process docs -> `docs/workflows/`
- Standards/policies -> `docs/governance/`
- Runtime and deployment docs -> `docs/operations/`
- Security docs -> `docs/security/`
- Release history -> `docs/changelog/`

## Naming and scope

- Use lowercase kebab-case filenames (e.g., `local-setup.md`)
- One topic per file
- Link to related files instead of duplicating text

## Disallowed root document patterns

Do not add non-essential root docs with extensions like:

- `.md`
- `.txt`
- `.rst`
- `.adoc`

## PR enforcement

- PRs that add non-essential root documentation files should be rejected.
- If a root exception is needed, document justification in PR description and get maintainer approval.
