# Upgrading Guide

This guide will help you upgrade Composer Update Helper to newer versions.

## General Upgrade Process

1. **Update the package**:
   ```bash
   composer update nowo-tech/composer-update-helper
   ```

2. **Review the CHANGELOG**:
   Check [CHANGELOG.md](CHANGELOG.md) for breaking changes and new features.

3. **Update your scripts** (if needed):
   The `generate-composer-require.sh` script is automatically updated during installation.
   If you have custom modifications, you may need to reapply them.

## Version-Specific Upgrade Notes

### Upgrading to 2.0.24+ (Unreleased)

#### What's New
This release significantly improves abandoned package detection to provide comprehensive visibility into all abandoned packages in your project.

**New Features:**
- **Comprehensive Abandoned Package Detection**: Now detects abandoned packages for ALL installed packages, not just outdated ones
  - New section "All abandoned packages installed:" appears before dependency analysis
  - Shows all abandoned packages from your `composer.json` (both `require` and `require-dev`)
  - Includes replacement package information when available
  - Separates packages by prod/dev labels for clarity
  - Example output:
    ```
    ⚠️ All abandoned packages installed:
       - old-package:1.0.0 (prod) (⚠️ Package is abandoned, replaced by: new-package/name)
       - deprecated-lib:2.5.0 (dev) (⚠️ Package is abandoned)
    ```
- **Enhanced Detection for Outdated Packages**: Improved to check ALL outdated packages for abandoned status
  - Previously only checked packages with dependency conflicts
  - Now checks all outdated packages regardless of conflicts
  - Provides more complete picture of package maintenance status

**Changed:**
- **Detection Flow**: Restructured to check abandoned status at two points:
  1. All installed packages (before dependency analysis)
  2. Outdated packages (during dependency analysis)
  - Avoids duplicate detection
  - Better organization of information in output
- **Fixed save-impact-to-file configuration**: When `save-impact-to-file: true` is set in YAML, automatically enables `show-impact-analysis` to ensure data is generated

**Fixed:**
- **Impact analysis file location**: Fixed issue where `composer-update-impact.txt` was saved in the current working directory instead of the script's directory
  - Now saves the file in the same directory where the script is located
  - Example: If script is in `/usr/src/app/symfony/generate-composer-require.sh`, file is saved to `/usr/src/app/symfony/composer-update-impact.txt`
  - Ensures the file is always generated next to the script, regardless of where the command is executed from

#### Migration Notes
- **No action required**: These are enhancements to existing functionality
- **New output section**: You'll now see "All abandoned packages installed:" section at the beginning of the output
- **More comprehensive information**: You'll get visibility into all abandoned packages, not just those with conflicts or updates available

#### Breaking Changes
- None

### Upgrading to 2.0.23+

#### What's New
This release adds command-line options configuration in YAML and extends wildcard dependency checking. See [CHANGELOG.md](CHANGELOG.md#2023) for complete details.

**New Features:**
- **Command-line options configuration in YAML**: Configure default values for all command-line options in `generate-composer-require.yaml`
  - Set your preferred defaults once, then override them when needed via command-line arguments
  - Available options: `show-release-info`, `show-release-detail`, `show-impact-analysis`, `save-impact-to-file`, `verbose`, `debug`
  - Example: Set `show-release-info: true` in YAML to always show release info by default
  - Command-line arguments always override YAML configuration
  - See [Configuration Guide](docs/CONFIGURATION.md#command-line-options-configuration) for complete details
- **Wildcard Dependency Checking**: Extended dependency conflict detection to support wildcard constraints (`^`, `~`, `*`)
  - Previously, dependency checking was skipped for packages with wildcard constraints
  - Now properly validates wildcard constraints against dependent package requirements
  - Improves conflict detection accuracy for framework constraints and version ranges

**Changed:**
- **Improved type hints**: Better type safety with union types in PHPDoc for `ConfigLoader::readConfigValue()`
  - PHPDoc uses `string|int|float|bool|null` union types for better IDE support
  - Code remains compatible with PHP 7.4+ (union types only in documentation)
  - More specific than generic `mixed` type

#### Migration Notes
- **No action required**: These are new features that enhance existing functionality
- **YAML configuration for command-line options** (optional): You can now set default values for command-line options in your `generate-composer-require.yaml`:
  ```yaml
  # Set defaults for command-line options
  show-release-info: true          # Always show release info by default
  show-impact-analysis: true       # Always show impact analysis by default
  verbose: false                   # Don't show verbose output by default
  ```
  - Command-line arguments always override YAML configuration
  - Example: If you set `show-release-info: true` in YAML but run `./generate-composer-require.sh --no-release-info`, release info will be disabled for that run
- **Wildcard dependency checking**: Now works automatically for all constraint types (`^`, `~`, `*`). No configuration needed.

#### Breaking Changes
- None

### Upgrading to 2.0.22+

#### What's New
This release adds several new features to help resolve dependency conflicts and provide better guidance when automatic solutions aren't available. See [CHANGELOG.md](CHANGELOG.md#unreleased) for complete details.

**New Features:**
- **Command-line options configuration in YAML**: Configure default values for all command-line options in `generate-composer-require.yaml`
  - Set your preferred defaults once, then override them when needed via command-line arguments
  - Available options: `show-release-info`, `show-release-detail`, `show-impact-analysis`, `save-impact-to-file`, `verbose`, `debug`
  - Example: Set `show-release-info: true` in YAML to always show release info by default
  - Command-line arguments always override YAML configuration
  - See [Configuration Guide](docs/CONFIGURATION.md#command-line-options-configuration) for complete details
- **Wildcard Dependency Checking**: Extended dependency conflict detection to support wildcard constraints (`^`, `~`, `*`)
  - Previously, dependency checking was skipped for packages with wildcard constraints
  - Now properly validates wildcard constraints against dependent package requirements
  - Improves conflict detection accuracy for framework constraints and version ranges
- **Conflict Impact Analysis**: Analyzes which packages would be affected when updating conflicting packages
  - Shows direct and transitive affected packages
  - **Optional feature**: Use `--show-impact` or `--impact` flag to enable (disabled by default to reduce verbosity)
  - **Save to file**: Use `--save-impact` flag to save analysis to `composer-update-impact.txt` (automatically added to `.gitignore`)
  - Provides complete dependency chain visualization
- **Outdated packages count**: Shows summary of total outdated packages found
  - Displays: `✅ Found X outdated package(s)`
  - Automatically shown when packages are found
- **Abandoned Package Detection**: Automatically detects and warns about abandoned packages
  - Detects abandoned packages for all installed packages (not just outdated ones)
  - Shows comprehensive list of all abandoned packages before dependency analysis
  - Also detects abandoned status for outdated packages during conflict analysis
- **Fallback Version Suggestions**: Suggests compatible older versions when conflicts are detected
- **Alternative Package Suggestions**: Suggests alternative packages when updates are blocked
- **Maintainer Contact Suggestions**: Provides maintainer contact information when manual intervention is needed
- **Modular Architecture**: Refactored code into modular classes for better maintainability

#### Migration Notes
- **No action required**: These are new features that enhance existing conflict detection
- **YAML configuration for command-line options** (optional): You can now set default values for command-line options in your `generate-composer-require.yaml`:
  ```yaml
  # Set defaults for command-line options
  show-release-info: true          # Always show release info by default
  show-impact-analysis: true       # Always show impact analysis by default
  verbose: false                   # Don't show verbose output by default
  ```
  - Command-line arguments always override YAML configuration
  - Example: If you set `show-release-info: true` in YAML but run `./generate-composer-require.sh --no-release-info`, release info will be disabled for that run
- All new features are automatic and require no configuration (except impact analysis which requires `--show-impact` flag)
- Abandoned package detection, fallback suggestions, and alternative packages appear automatically when conflicts are detected
- **Impact analysis is optional**: By default, impact analysis is disabled to reduce output verbosity. Use `--show-impact` flag if you need detailed impact information
- **Save impact to file**: Use `--save-impact` flag to save impact analysis to a text file for later review or documentation. The file `composer-update-impact.txt` is automatically added to `.gitignore`
- **Wildcard dependency checking**: Now works automatically for all constraint types (`^`, `~`, `*`). No configuration needed.

#### Breaking Changes
- None

> **Note**: For detailed information about all changes, see [CHANGELOG.md](CHANGELOG.md#unreleased).

### Upgrading to 2.0.21+

#### Added
- **Update Cases and Scenarios documentation**: New comprehensive documentation explaining all update scenarios
  - Complete guide to all supported cases (see [UPDATE_CASES.md](UPDATE_CASES.md) for current count)
  - Documentation of partially supported and not-yet-supported scenarios
  - Manual intervention guidance for contacting package maintainers
- **Implementation Roadmap**: Detailed action plan for implementing not-yet-supported features
  - Prioritized implementation plan ordered by complexity and feasibility
  - Complete timeline and effort estimates for future features
  - Translation requirements for all 31 supported languages

> **Note**: The number of fully supported cases has increased to 13 in version 2.0.22+ with the addition of Abandoned Package Detection, Alternative Package Suggestions, and Maintainer Contact Suggestions.

#### Changed
- **Improved dependency conflict messages**: Enhanced clarity of dependency conflict messages
  - Conflict messages now explicitly show which package requires which version constraint
  - Example: Instead of `(conflicts with 1 package: lexik/jwt-authentication-bundle (^1.2))`, you'll now see `(conflicts with 1 package: lexik/jwt-authentication-bundle requires rector/rector ^1.2)`
  - Makes it much easier to understand why a package update is being filtered and what needs to be updated

#### Migration Notes
- **No action required**: This is a UX improvement with no functional changes
- The conflict detection logic remains the same, only the message format has been improved

#### Breaking Changes
- None

### Upgrading to 2.0.20+

#### Fixed
- **Output alignment in dependency analysis section**: Fixed vertical alignment issue in the dependency conflict analysis output
  - The "Filtered by dependency conflicts" and "Packages that passed dependency check" header lines now have consistent alignment
  - Improved visual consistency across all output sections
  - Better readability of dependency analysis results

#### Migration Notes
- **No action required**: This is a visual formatting fix with no functional changes

#### Breaking Changes
- None

### Upgrading to 2.0.19+ (Unreleased)

#### Added
- **Extended language support**: 20 additional languages are now fully implemented (10 high-priority + 10 medium-priority)
  - **High-priority**: Dutch (nl), Czech (cs), Swedish (sv), Norwegian (no), Finnish (fi), Turkish (tr), Chinese (zh), Japanese (ja), Korean (ko), Arabic (ar)
  - **Medium-priority**: Hungarian (hu), Slovak (sk), Ukrainian (uk), Croatian (hr), Bulgarian (bg), Hebrew (he), Hindi (hi), Vietnamese (vi), Indonesian (id), Thai (th)
  - Complete translations for PHP scripts, Bash scripts, and help files
  - All languages are ready for use in production
  - Total of 31 fully supported languages: English (en) 🇬🇧 🇺🇸, Spanish (es) 🇪🇸, Portuguese (pt) 🇵🇹 🇧🇷, Italian (it) 🇮🇹, French (fr) 🇫🇷, German (de) 🇩🇪 🇦🇹, Polish (pl) 🇵🇱, Russian (ru) 🇷🇺, Romanian (ro) 🇷🇴, Greek (el) 🇬🇷, Danish (da) 🇩🇰, Dutch (nl) 🇳🇱 🇧🇪, Czech (cs) 🇨🇿, Swedish (sv) 🇸🇪, Norwegian (no) 🇳🇴, Finnish (fi) 🇫🇮, Turkish (tr) 🇹🇷, Chinese (zh) 🇨🇳 🇹🇼, Japanese (ja) 🇯🇵, Korean (ko) 🇰🇷, Arabic (ar) 🇸🇦 🇪🇬, Hungarian (hu) 🇭🇺, Slovak (sk) 🇸🇰, Ukrainian (uk) 🇺🇦, Croatian (hr) 🇭🇷, Bulgarian (bg) 🇧🇬, Hebrew (he) 🇮🇱, Hindi (hi) 🇮🇳, Vietnamese (vi) 🇻🇳, Indonesian (id) 🇮🇩, Thai (th) 🇹🇭

#### Changed
- **Dependency checking feature promoted to production**: The `check-dependencies` feature is now considered production-ready
  - All "development mode" warnings have been removed
  - Feature has been thoroughly tested and is stable
  - Recommended for all users (enabled by default)
  - Comprehensive dependency conflict detection, including transitive dependencies and `self.version` constraints
  - Full support for both `require` and `require-dev` dependency detection
- **Script architecture refactoring**: Main script is now more lightweight
  - Created `script-helper.sh` in vendor to contain complex helper functions
  - Reduced main script from ~502 lines to ~240 lines (52% reduction)
  - All complex logic moved to vendor helper, keeping the main script minimal
  - Helper script remains in vendor; only `generate-composer-require.sh` is copied to project root
- **Improved output formatting**: Enhanced dependency conflict display
  - Fixed double line break after "Processing packages" message
  - Fixed spacing around emoji numbers in filtered packages section
  - Enhanced conflict information: now shows the number of packages with conflicts (e.g., "conflicts with 1 package" or "conflicts with 2 packages")
  - Better visual formatting for dependency conflict analysis section
- **Documentation enhancements**: Improved visual presentation of language support
  - Added country flag emojis to all language listings in documentation (README.md, CHANGELOG.md, UPGRADING.md, CONFIGURATION.md)
  - Languages with multiple countries now display all relevant flags (e.g., English 🇬🇧 🇺🇸 🇨🇦 🇦🇺, Spanish 🇪🇸 🇲🇽 🇦🇷 🇨🇴)
  - Makes language selection more intuitive and visually appealing
  - All 31 languages are now fully implemented and documented with their respective flags
- **Documentation reorganization**: Improved README structure and organization
  - Reduced README.md from ~700 to 240 lines (66% reduction) for better readability
  - Created dedicated documentation files in `docs/` directory:
    - `CONFIGURATION.md` - Complete configuration guide (package configuration, language settings, dependency checking)
    - `USAGE.md` - Comprehensive usage guide (all options, release information, environment variables)
    - `FRAMEWORKS.md` - Framework support details (Symfony, Laravel, Yii, CakePHP, Laminas, CodeIgniter, Slim)
    - `DEVELOPMENT.md` - Development setup and CI/CD guide (Docker, testing, make commands)
  - README.md now focuses on essential information with links to detailed documentation
  - Better organization makes it easier to find specific information
  - All documentation is now properly categorized and cross-referenced

#### Fixed
- **i18n synchronization**: Fixed help messages and debug messages not synchronizing with language configuration
  - Help messages (`--help`) now correctly detect and use the language defined in `generate-composer-require.yaml`
  - Debug messages now correctly synchronize with the configured language
  - The system now detects the configuration file before initializing translations
  - Ensures consistent language usage across all script output (help, debug, regular messages)
- **Test compatibility**: Updated `ScriptTest.php` to match refactored script structure
  - Updated assertions to check for `PROCESSOR_PATHS` instead of `PROCESSOR_PHP`
  - Updated path assertion to match new script structure
- **Implicitly nullable parameters**: Fixed PHP deprecation warnings
  - All nullable parameters are now explicitly declared using `?type` or `mixed`
  - Improves compatibility with modern PHP static analysis tools
  - Eliminates deprecation warnings in IDEs like VS Code with Intelephense
- **PHPUnit configuration compatibility**: Fixed PHPUnit warnings for PHPUnit 10/11 compatibility
  - Removed deprecated `<filter><whitelist>` element from `phpunit.xml.dist`
  - Updated schema from PHPUnit 9.6 to PHPUnit 11.5
  - **Separated PHPUnit configuration into two files**:
    - `phpunit.xml.dist`: Main configuration without coverage section (used by `composer test`)
    - `phpunit.coverage.xml.dist`: Configuration with coverage section (used by `composer test-coverage`)
  - **PHPUnit 11 compatibility**: Updated coverage configuration to use modern PHPUnit 11 structure
    - Moved `<include>` directive from `<coverage>` to `<source>` tag (PHPUnit 11 requirement)
    - Removed deprecated `processUncoveredFiles` attribute from both configuration files
  - Fixed "No filter is configured, code coverage will not be processed" warning
  - Tests and coverage now run cleanly in Docker environment without warnings
  - Verified: 132 tests passing, 99.58% line coverage (478/480 lines), 90.91% method coverage (20/22 methods)

#### Changed
- **Code coverage command**: Updated `composer test-coverage` to use dedicated configuration file
  - Now uses `--configuration=phpunit.coverage.xml.dist` to load coverage-specific configuration
  - Prevents PHPUnit warnings about missing filter configuration
  - Ensures consistent coverage report generation
  - Coverage configuration is now isolated in a separate file for better maintainability
  - Verified in Docker: Coverage reports generate correctly with HTML and Clover XML formats
- **Code quality**: Enhanced type safety and modern PHP standards compliance
  - Better type hints throughout the codebase
  - Improved static analysis compatibility

#### Migration Notes
- **No action required**: The feature remains enabled by default
- If you previously disabled it due to development mode warnings, you can safely re-enable it
- The feature is now fully supported and recommended for production use
- **Language support**: All 31 languages are now fully available. You can use any of the supported languages by setting `language: {code}` in your `generate-composer-require.yaml` file.
- **i18n usage**: To use translations, add `language: es` (or other supported code) to your `generate-composer-require.yaml`:
  ```yaml
  # Language for output messages
  # Supported: en, es, pt, it, fr, de, pl, ru, ro, el, da, nl, cs, sv, no, fi, tr, zh, ja, ko, ar, hu, sk, uk, hr, bg, he, hi, vi, id, th (31 languages)
  # If not set, will auto-detect from system (LANG, LC_ALL, LC_MESSAGES)
  # Default: en (English)
  # ⚠️  WARNING: i18n feature is currently in DEVELOPMENT MODE
  language: es
  ```
- **Debug mode**: Use `--debug` flag to see detailed i18n information:
  ```bash
  ./generate-composer-require.sh --debug
  ```
  This will show:
  - Detected language
  - Translation function availability
  - Loaded translations count
  - Test translations
- **i18n synchronization**: Help messages (`--help`) and debug messages now correctly synchronize with the language defined in `generate-composer-require.yaml`
  - The system now detects the configuration file before initializing translations
  - Help messages will display in the configured language
  - Debug messages will use the configured language
  - All script output is now consistently translated based on your configuration
- Code coverage: 99.58% line coverage (478/480 lines), 90.91% method coverage (20/22 methods)

#### Breaking Changes
- None

### Upgrading to 2.0.18+

#### Fixed
- **Dependency conflict detection order**: Critical fix where the system now correctly verifies dependent package constraints before checking package requirements
  - Versions that don't satisfy dependent package constraints are now correctly rejected
  - This prevents suggesting incompatible updates that would fail during installation
  - Example: `phpdocumentor/reflection-docblock:6.0.0` is now correctly filtered when dependent packages require `^5.6` or `^5.0`
- **Dependency detection in require-dev**: Fixed issue where dependencies in `require-dev` sections were not being detected
  - The system now finds all dependent packages, regardless of whether they're in production or development dependencies

#### Changed
- **Enhanced conflict information**: Filtered packages now show which dependent packages cause the conflict
  - You'll now see messages like: `package:version (prod) (conflicts with: dependent1 (^1.0), dependent2 (^2.0))`
  - This makes it much easier to understand why a package is being filtered
- **Output formatting improvements**: Multiple enhancements for better readability
  - Standardized spacing before emojis throughout the output
  - Consistent indentation across all sections (framework constraints, ignored packages, dependency analysis)
  - Visual enhancement: Package counts now use emoji numbers (1️⃣, 2️⃣, 3️⃣, etc.) for better visibility
  - Example: `⚠️  2️⃣ Filtered by dependency conflicts:` makes it easier to quickly identify the number of filtered packages
- **Debug logging**: Added comprehensive debug messages for better troubleshooting
  - Includes detailed information about conflict detection and tracking
  - Shows which dependent packages cause conflicts for each filtered package

#### Migration Notes
- **No action required**: These are bug fixes that improve accuracy
- If you notice packages are now being filtered that weren't before, it's because the system is now correctly detecting conflicts
- The enhanced conflict information will help you understand what needs to be updated

#### Breaking Changes
- None

### Upgrading to 2.0.17+

#### Added
- **Transitive dependency update suggestions**: When packages are filtered due to dependency conflicts, the system now automatically suggests updating the required transitive dependencies
  - The system detects when a package requires a newer version of a transitive dependency (e.g., `spomky-labs/otphp:^11.4` when `11.3.0` is installed)
  - The system also detects `self.version` constraints (e.g., `scheb/2fa-email` requiring `scheb/2fa-bundle: self.version`)
  - Suggested commands now include both transitive dependencies and filtered packages in a single command
  - This makes it much easier to resolve dependency conflicts
- **Enhanced output messages**: More precise messages explaining why no packages are available for update

#### Changed
- **Improved dependency conflict resolution**: Commands now automatically include all related packages
  - Previously, you might need to update transitive dependencies manually
  - Now, the suggested commands include everything needed in one go
- **Code improvements**: Internal refactoring for better maintainability (no user-facing changes)

#### Migration Notes
- **No action required**: These are enhancements that improve the dependency conflict resolution workflow
- When you see dependency conflicts, you'll now get actionable commands that include all necessary updates
- Simply run the suggested commands to resolve conflicts

#### Breaking Changes
- None

### Upgrading to 2.0.16+

#### Changed
- **Code coverage threshold**: CI/CD coverage requirement adjusted from 100% to 99%
  - This change is internal and does not affect functionality
  - Reflects best practices for defensive code that cannot be easily tested

#### Removed
- **Dead code elimination**: Removed unreachable code paths
  - No functional impact
  - Improves code maintainability

#### Migration Notes
- **No action required**: This is an internal improvement with no user-facing changes

### Upgrading to 2.0.13+

#### Fixed
- **Duplicate command output**: Fixed issue where suggested composer commands appeared twice in the output
  - Commands now appear only once in the suggested commands section
  - This improves output clarity and readability

#### Migration Notes
- **No action required**: This is a bug fix that improves output clarity
- Existing functionality remains unchanged
- The `--run` flag continues to work as expected

#### Breaking Changes
- None

### Upgrading to 2.0.12+

#### Changed
- **CI/CD coverage threshold**: Code coverage requirement lowered from 100% to 90%
  - This is a CI/CD configuration change only, no functional changes
  - Current coverage: 92.36% (exceeds the new minimum requirement)
  - All existing functionality remains unchanged

#### Migration Notes
- **No action required**: This change only affects CI/CD pipeline validation
- No changes to package functionality or API
- Tests continue to validate code quality

#### Breaking Changes
- None

### Upgrading to 2.0.11+

#### Fixed
- **Migration logic improvement**: Migration now correctly preserves user-defined packages in YAML configuration
  - If you have both TXT and YAML files with different packages, the YAML will be preserved
  - The TXT file will remain until you manually merge the packages or delete it
  - This ensures your custom configuration is never overwritten during migration

#### Migration Notes
- **No action required**: This is a bug fix that improves migration safety
- If you have both TXT and YAML files, the YAML configuration will be preserved
- You can manually merge packages from TXT to YAML if needed, or delete the TXT file

#### Breaking Changes
- None

### Upgrading to 2.0.10+

#### Changed
- **Output formatting refactored**: All output formatting is now done in PHP processor
  - Shell script is now even lighter (283 lines vs 396 lines)
  - All formatting logic centralized in PHP for better maintainability
  - Same output and functionality, just better organized

#### Migration Notes
- **No action required**: The refactoring is transparent to users
- Output format remains exactly the same
- All features work identically

#### Breaking Changes
- None

### Upgrading to 2.0.9+

#### Fixed
- **Script auto-update**: The `generate-composer-require.sh` script is now automatically updated when you run `composer update`
  - Previously, the script was only installed on first installation (`composer install`)
  - Now, the script is compared using MD5 hash and updated if content differs on both `composer install` and `composer update`
  - This ensures you always have the latest version of the script

#### Changed
- **Refactored architecture**: The script has been split into a lightweight wrapper and a PHP processor
  - The script in your repo is now lighter (~396 lines vs ~971 lines, 59% reduction)
  - Complex logic is now in `vendor/nowo-tech/composer-update-helper/bin/process-updates.php` (~622 lines)
  - The script automatically detects and uses the PHP processor
  - No manual configuration needed

#### Migration Notes
- **No action required**: The script will be automatically updated on your next `composer update`
- If you have custom modifications to the script, they will be overwritten
- Consider committing your customizations to a separate script or fork if needed
- The refactoring is transparent to users - the script automatically detects the PHP processor in vendor
- If you encounter issues, ensure `composer install` has been run
- The script will show a clear error message if the PHP processor is not found

#### Breaking Changes
- None - fully backward compatible

### Upgrading to 2.0.8+

#### Changed
- **Improved debug messages**: Debug and verbose modes now provide clearer messages when no packages are found
  - Helps identify when packages are commented out in YAML configuration
  - Makes troubleshooting configuration issues easier

#### Documentation
- **YAML configuration clarification**: README now explicitly explains that only uncommented packages are read
  - Lines starting with `#` are treated as comments and ignored
  - Examples show the difference between commented and uncommented packages

**No action required**: This is a documentation and messaging improvement. Existing configurations continue to work as before.

### Upgrading to 2.0.7+

#### Added
- **Verbose and Debug modes**: New `-v, --verbose` and `--debug` options for troubleshooting
  - Use `--verbose` or `-v` to see detailed information about configuration files and packages
  - Use `--debug` for very detailed debug information (includes verbose mode)
  - All debug/verbose output is sent to stderr to avoid interfering with normal output
- **Support for `.yml` extension**: You can now use either `.yaml` or `.yml` for configuration files
  - Priority: `.yaml` first, then `.yml`, then `.txt` (backward compatibility)
  - Allows users to use either extension based on their preference

#### Changed
- **Configuration file location**: Script now searches for configuration files in the current directory (where `composer.json` is located)
  - Previously searched in the script's directory (`$(dirname "$0")`)
  - Now searches in the current working directory (where the script is executed)
  - This ensures configuration files are found correctly regardless of script location
- **Release information default behavior**: Release information is now **disabled by default**
  - If you were relying on release information being shown automatically, you now need to use `--release-info` or `--release-detail`
  - This improves performance by default (no API calls are made)
  - Use `--release-info` to show summary with release and changelog links
  - Use `--release-detail` to show full release changelog

#### Fixed
- **Configuration file reading**: Fixed issue where script couldn't find YAML files in the project directory
  - Script now correctly searches in the current directory instead of script directory
  - Resolves issues where configuration files weren't being read properly
- **Migration verification**: Fixed issue where migration verification failed when YAML had packages in `include` section
  - Migration now correctly compares only `ignore` section with TXT content
  - TXT file is now properly deleted after successful migration

#### Migration Notes
- **No action required**: Existing configuration files will continue to work
- **File location**: If your configuration file is in the script directory, move it to the project root (where `composer.json` is)
- **Extension**: You can rename `.yaml` to `.yml` if preferred (both are supported)

#### Action Required
- **If you use release information**: Add `--release-info` or `--release-detail` flag to your commands
  - Example: `./generate-composer-require.sh --release-info`
  - Example: `./generate-composer-require.sh --run --release-detail`

### Upgrading to 2.0.6+

#### Added
- **Verification**: Complete verification of YAML include/ignore functionality
- **Utility scripts**: New utility scripts in `tests/` directory for development and testing

#### Changed
- **Documentation improvements**: Enhanced README.md with better release information documentation
- **YAML template**: Updated comments for clarity

**No action required**: This is a documentation and utility scripts release. No configuration changes needed.

### Upgrading to 2.0.5+

#### New Feature
- **Package inclusion**: New `include` section in `generate-composer-require.yaml`
  - You can now force include packages even if they are in the `ignore` list
  - The `include` section has priority over the `ignore` section
  - Useful for fine-grained control over which packages to update

**Example configuration:**
```yaml
ignore:
  - symfony/*  # Ignore all Symfony packages

include:
  - symfony/security-bundle  # But force include this one
```

#### Improved
- **YAML parsing**: Enhanced reading of YAML configuration files
  - Better handling of inline comments (e.g., `- package/name  # comment`)
  - Support for different indentation levels
  - More robust section detection
  - Improved handling of empty lines and comment-only lines

**No action required**: Existing configurations will continue to work. The new `include` section is optional.

### Upgrading to 2.0.4+

#### Changed
- **Migration verification**: Migration now verifies that packages were correctly migrated before deleting the old `.ignore.txt` file
  - Compares packages from TXT and YAML files to ensure data integrity
  - Only deletes TXT file if migration is verified as correct
  - Shows warning if verification fails and preserves TXT file for safety

#### Fixed
- **`.gitignore` behavior**: `.sh` and `.yaml` files are no longer added to `.gitignore`
  - These files should be committed to the repository for team collaboration
  - Plugin now removes old `.ignore.txt` entries from `.gitignore` if they exist
  - Plugin also removes `.sh` and `.yaml` entries if they were previously added (cleanup)

#### Migration Notes
- Migration is now safer with verification step
- Files should be committed to repository (not ignored)
- Old `.gitignore` entries are automatically cleaned up

### Upgrading to 2.0.2+

#### Fixed
- **Improved migration logic**: Migration now works even if YAML file already exists
  - Previously, migration only occurred if YAML didn't exist
  - Now migrates if YAML exists but is empty or contains only template (no user packages)
  - Prevents data loss: won't overwrite YAML files with user-defined packages
  - If you updated to v2.0.1 and migration didn't occur, updating to v2.0.2 will trigger migration

#### Migration Behavior
- **Safe migration**: The plugin detects if YAML is empty or template-only before migrating
- **Data protection**: If YAML has user-defined packages, migration is skipped to prevent data loss
- **Automatic retry**: If migration didn't happen in v2.0.1, it will work in v2.0.2

### Upgrading to 2.0.1+

#### Fixed
- **Automatic cleanup**: The old `.ignore.txt` file is now automatically deleted after successful migration to `.yaml` format
  - No manual cleanup required
  - Ensures clean migration without leftover files

### Upgrading to 2.0.0+

#### Breaking Changes

- **Configuration file format changed from TXT to YAML**
  - Old format: `generate-composer-require.ignore.txt`
  - New format: `generate-composer-require.yaml`
  - This is a **breaking change** in format, but migration is automatic

#### New Features

- **YAML configuration format**: More structured and extensible configuration file
- **Automatic migration**: Existing `.ignore.txt` files are automatically migrated to `.yaml` format
- **Backward compatibility**: The script still supports reading old TXT format if YAML doesn't exist

#### Migration Steps

1. Update the package:
   ```bash
   composer update nowo-tech/composer-update-helper
   ```

2. **Automatic migration**: If you have an existing `generate-composer-require.ignore.txt` file, it will be automatically migrated to `generate-composer-require.yaml` during the update.
   - **v2.0.2+**: Migration works even if YAML already exists (if YAML is empty or template-only)
   - Migration is skipped if YAML has user-defined packages (to prevent data loss)

3. Verify the migration:
   ```bash
   # Check that the new YAML file exists
   cat generate-composer-require.yaml
   ```

4. **Automatic cleanup**: The old `.ignore.txt` file is automatically deleted after successful migration (in v2.0.1+)

5. The script will now use the YAML file automatically. No changes needed to your workflow.

#### Configuration Format

**Old format (TXT)**:
```txt
# Packages to ignore
doctrine/orm
symfony/security-bundle
```

**New format (YAML)**:
```yaml
# Composer Update Helper Configuration
ignore:
  - doctrine/orm
  - symfony/security-bundle
```

#### What Happens During Migration

- Your existing ignore list is preserved
- The new `.yaml` file is created with your packages migrated (or updated if empty/template-only)
- The old `.ignore.txt` file is automatically deleted after successful migration (v2.0.1+)
- The script automatically uses the YAML file if it exists
- `.gitignore` is updated to include the new YAML file and remove the old TXT entry
- **v2.0.2+**: Migration works even if YAML exists, but only if YAML is empty or template-only (protects user data)

### Upgrading to 1.3.4+

#### New Features

- **Help option**: The script now includes a `--help` or `-h` flag to display comprehensive usage information
  - Shows all available options, examples, and framework support details
  - Works without requiring Composer to be installed

#### Performance Improvements

- **Optimized emoji handling**: Emojis are now defined as variables at script initialization
  - Reduces script size and improves execution performance
  - Better maintainability

#### Breaking Changes

- None

#### Migration Steps

1. Update the package:
   ```bash
   composer update nowo-tech/composer-update-helper
   ```

2. Try the new help option:
   ```bash
   ./generate-composer-require.sh --help
   ```

3. No other changes required - the script is fully backward compatible

### Upgrading to 1.3.3+

#### New Features

- **Release Information**: The script can show GitHub release links and changelogs when requested.
  - Use `--release-info` to show summary (package, release link, changelog link)
  - Use `--release-detail` to see full changelog details
  - Use `--no-release-info` to explicitly skip release information (default behavior)
  - **Note**: In v2.0.7+, release information is disabled by default for better performance

#### Breaking Changes

- None

#### Migration Steps

1. Update the package:
   ```bash
   composer update nowo-tech/composer-update-helper
   ```

2. Test the new release information feature:
   ```bash
   ./generate-composer-require.sh
   ```

3. If you want to skip release information (old behavior):
   ```bash
   ./generate-composer-require.sh --no-release-info
   ```

### Upgrading to 1.3.0+

#### Breaking Changes

- PHPUnit updated to 11.0

#### Migration Steps

1. Update the package:
   ```bash
   composer update nowo-tech/composer-update-helper
   ```

2. Update your test suites to be compatible with PHPUnit 11.0

### Upgrading to 1.2.0+

#### Breaking Changes

- Multi-framework support added
- Framework version constraints are now automatically detected

#### Migration Steps

1. Update the package:
   ```bash
   composer update nowo-tech/composer-update-helper
   ```

2. The script will automatically detect your framework and respect version constraints

3. No manual configuration needed

## Troubleshooting

### Script not found after upgrade

If the script is missing after upgrading:

```bash
composer install
```

This will reinstall the script files.

### Permission errors

If you get permission errors:

```bash
chmod +x generate-composer-require.sh
```

### Conflicts with custom modifications

If you've modified the script and it conflicts with the new version:

1. Backup your custom script:
   ```bash
   cp generate-composer-require.sh generate-composer-require.sh.backup
   ```

2. Reinstall the package:
   ```bash
   composer reinstall nowo-tech/composer-update-helper
   ```

3. Reapply your custom modifications if needed

## Getting Help

If you encounter issues during upgrade:

1. Check the [CHANGELOG](CHANGELOG.md) for known issues
2. Review the [README](../README.md) for usage examples
3. Open an issue on GitHub with:
   - Your current version
   - Target version
   - Error messages
   - Steps to reproduce

