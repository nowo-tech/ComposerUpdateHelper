# Security — Composer Update Helper

## Scope

Composer Update Helper is a **development-time** Composer plugin that reads `composer.json` / `composer.lock`, suggests `composer require` commands, and optionally runs Composer with `--run`. It operates on the **local filesystem** of the project where it is installed.

## Attack surface

- **Composer metadata**: Reads lock files and package metadata; malformed data could in theory trigger unexpected code paths—mitigated by using Composer’s APIs and stable parsing.
- **Process execution** (`--run`): Executes `composer` subprocesses. Only the project owner or CI should run this; untrusted projects must not be processed blindly.
- **Output files** (`--save-impact`, etc.): Writes to paths specified by the user; ensure paths are not attacker-controlled in automated environments without validation.

## Threats and mitigations

| Threat | Mitigation |
|--------|------------|
| Command injection via crafted `composer.json` | Treat project roots as trusted; do not point the tool at untrusted directories in automation without sandboxing. |
| Information disclosure | Output lists package names and versions; do not run with secrets in environment variables visible to untrusted logs. |
| Dependency chain attacks | Run `composer audit`; verify package integrity via Composer’s normal mechanisms. |

## Secrets and cryptography

- No network credentials are required for core functionality beyond Composer’s own access to Packagist/VCS.
- **Never** commit tokens, HTTP basic auth in URLs, or private repository credentials in example configs.

## Logging

- Verbose/debug modes may print paths. Avoid printing environment variables that could contain tokens.

## Dependencies

- Run `composer audit` before releases.
- Keep PHP and Composer constraint ranges aligned with supported versions.

## Reporting a vulnerability

Report security issues **privately** (see `composer.json` maintainers). Do not disclose exploit details in public issues before a fix is available.

## AI security audit (REQ-SEC-004)

| Field | Value |
|-------|--------|
| **Date** | 2026-07-27 |
| **Method** | Monorepo static review + prior [BUNDLES_SECURITY_ANALYSIS.md](../../BUNDLES_SECURITY_ANALYSIS.md) posture (Medium: opt-in wrapper overwrite) |
| **Grade** | **Pass (conditional)** |
| **Overall residual risk** | Medium |

### Residuals (accepted)

- Wrapper overwrite is **opt-in** (`extra.composer-update-helper.auto_update_wrapper`). Misconfiguration can replace a customized `generate-composer-require.sh`; default remains non-destructive.
- `--run` executes Composer in the project root — only trusted projects/CI should enable it.

No Critical/High findings remain open for shipping.

## Release security checklist (12.4.1)

Before tagging a release, confirm:

| Item | Notes |
|------|--------|
| **SECURITY.md** | This document is current. |
| **`.gitignore` and `.env`** | `.env` ignored; no secrets in repo. |
| **No secrets in repo** | No tokens or passwords in tracked files. |
| **Recipe / installer** | Plugin installation does not ship secrets. |
| **Input / output** | CLI flags and paths validated; subprocesses only invoke `composer` as intended. |
| **Dependencies** | `composer audit` addressed. |
| **Logging** | No secrets in logs. |
| **Cryptography** | N/A; signing keys not embedded. |
| **Permissions / exposure** | Runs as local user/CI; document in README. |
| **Limits / DoS** | Large lockfiles may take time; document for CI timeouts. |

Record confirmation in the release PR or tag notes.
