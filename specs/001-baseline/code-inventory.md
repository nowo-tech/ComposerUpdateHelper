# Code inventory — 100% traceability

**Baseline spec**: [`spec.md`](spec.md)  
**Package**: `nowo-tech/composer-update-helper`  
**Last audited**: 2026-07-28

## Plugin (`src/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Plugin.php` | Composer plugin | FR-PLUGIN-001 |
| `Installer.php` | Script installer | FR-INST-001 |
| `SafeFileReader.php` | Shared filesystem reads | FR-PLUGIN-001, FR-INST-001 |

## Coverage summary

| Category | Files | Mapped |
| --- | ---: | ---: |
| Plugin (`src/`) | 3 | 3 |
| **Total production sources** | **3** | **3** |

**Note:** Analysis libraries and shell scripts under `bin/` are shipped artifacts documented in README but outside Spec Kit `src/` inventory (REQ-SPECKIT-001).
