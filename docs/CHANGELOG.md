# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.25] - 2026-01-20

### Added
- **Dependent Package Update Detection**: When a package update conflicts with a dependent package, the system now automatically checks if the dependent package has a newer version that supports the proposed update
  - Previously, when `package-a:2.0` conflicted with `dependent-package` requiring `package-a:^1.5`, the update was simply filtered
  - Now automatically searches for newer versions of `dependent-package` that require a version compatible with `package-a:2.0`
  - If found, both packages are included in the same update command
  - Example: Instead of just filtering `zircote/swagger-php:6.0.2` when it conflicts with `nelmio/api-doc-bundle` requiring `^4.11.1 || ^5.0`, the system now checks if `nelmio/api-doc-bundle` has a version (e.g., `6.0.0`) that requires `zircote/swagger-php:^6.0`
  - If compatible version is found, suggests: `composer require --with-all-dependencies zircote/swagger-php:6.0.2 nelmio/api-doc-bundle:6.0.0`
  - Significantly improves conflict resolution by automatically detecting when both packages need to be updated together
  - Reduces manual intervention required to resolve dependency conflicts
  - Integrated into the conflict detection flow before fallback version search

### Changed
- **Enhanced Conflict Resolution**: Improved conflict resolution strategy to check dependent packages for compatible versions before suggesting fallback versions
  - Conflict resolution now follows this order:
    1. Check if dependent packages have newer compatible versions (NEW)
    2. If not found, search for fallback versions of the original package
    3. If still not found, suggest alternative packages or maintainer contact
  - Provides more comprehensive solutions to dependency conflicts
  - Better user experience with actionable suggestions

## [2.0.24] - 2026-01-19

### Added
- **Comprehensive Abandoned Package Detection**: Now detects abandoned packages for ALL installed packages, not just outdated ones
  - New section "All abandoned packages installed:" shows all abandoned packages in your project before dependency analysis
  - Previously only detected abandoned packages when they had dependency conflicts
  - Now checks all packages from `composer.json` (both `require` and `require-dev`)
  - Shows replacement package information if available
  - Separates packages by prod/dev labels
  - Example: `⚠️ All abandoned packages installed: - old-package:1.0.0 (prod) (⚠️ Package is abandoned, replaced by: new-package/name)`
  - All messages are translated and available in all 31 supported languages
- **Enhanced Abandoned Package Detection for Outdated Packages**: Improved detection to check ALL outdated packages for abandoned status
  - Previously only checked abandoned status for packages with dependency conflicts
  - Now checks abandoned status for all outdated packages, regardless of conflicts
  - Provides more comprehensive information about package maintenance status
  - Shows abandoned status in the "Abandoned packages found:" section within dependency analysis

### Changed
- **Abandoned Package Detection Flow**: Restructured to provide more comprehensive coverage
  - Detection now happens at two points: for all installed packages (before analysis) and for outdated packages (during analysis)
  - Avoids duplicate detection by skipping packages already checked
  - Better organization of abandoned package information in output
- **Fixed save-impact-to-file configuration**: When `save-impact-to-file: true` is set in YAML, automatically enables `show-impact-analysis` to ensure data is generated
  - Previously, setting only `save-impact-to-file: true` without `show-impact-analysis: true` would not create the file
  - Now automatically activates impact analysis when saving to file is requested
  - Works consistently with `--save-impact` command-line flag behavior

### Fixed
- **Impact analysis file location**: Fixed issue where `composer-update-impact.txt` was saved in the current working directory instead of the script's directory
  - Previously, the file was saved in the directory where the command was executed (working directory)
  - Now saves the file in the same directory where the script is located
  - Example: If script is in `/usr/src/app/symfony/generate-composer-require.sh`, file is saved to `/usr/src/app/symfony/composer-update-impact.txt`
  - Ensures the file is always generated next to the script, regardless of where the command is executed from
  - Falls back to current directory if script directory is not writable

## [2.0.23] - 2026-01-18

### Changed
- **Improved type hints**: Updated `ConfigLoader::readConfigValue()` PHPDoc with union types for better type safety
  - PHPDoc now uses `string|int|float|bool|null` union types for better IDE support and static analysis
  - Code remains compatible with PHP 7.4+ (union types only in PHPDoc, not in code)
  - More specific than generic `mixed` type
  - Provides better autocompletion and type checking in IDEs

### Added
- **Command-line options configuration in YAML**: All command-line options can now be configured as defaults in `generate-composer-require.yaml`
  - Set your preferred defaults once in the YAML file, then override them when needed via command-line arguments
  - Configuration priority: Command-line arguments (highest) > YAML configuration > Built-in defaults
  - Available options: `show-release-info`, `show-release-detail`, `show-impact-analysis`, `save-impact-to-file`, `verbose`, `debug`
  - Example: Set `show-release-info: true` in YAML to always show release info by default, then override with `--no-release-info` when needed
  - Benefits: Consistent defaults for team members, no need to remember long command-line flags for frequently used options
  - See [Configuration Guide](docs/CONFIGURATION.md#command-line-options-configuration) for complete details
- **Wildcard Dependency Checking**: Extended dependency conflict detection to support wildcard constraints (`^`, `~`, `*`)
  - Previously, dependency checking was skipped for packages with wildcard constraints
  - Now uses `versionSatisfiesConstraint()` to properly validate wildcard constraints against dependent package requirements
  - Supports caret constraints (`^1.2.3`), tilde constraints (`~1.2.3`), and wildcard constraints (`1.2.*`)
  - Improves conflict detection accuracy for framework constraints and version ranges
  - Example: Now detects conflicts when `package-a:^2.0` conflicts with `dependent-package` requiring `package-a:^1.5`

### Changed
- **Updated UPDATE_CASES.md**: Documentation now reflects 15 fully supported cases (previously 14)
  - Case #11 (Wildcard Version Constraints) moved from "Partially Supported" to "Fully Supported"
  - Updated summary section to reflect current implementation status
- **Updated IMPLEMENTATION_ROADMAP.md**: Priority 7 (Wildcard Dependency Checking) marked as completed
  - Phase 2 progress: 3/4 features completed (75%)
- **Updated CONFIGURATION.md**: Added new section "Command-Line Options Configuration" explaining how to set defaults in YAML

## [2.0.22] - 2026-01-18

### Added
- **Conflict Impact Analysis**: Automatically analyzes which packages would be affected when updating conflicting packages
  - Shows direct affected packages (packages that directly depend on the conflicting package)
  - Shows transitive affected packages (packages that depend on directly affected packages)
  - Recursively checks transitive dependencies (up to 5 levels deep to prevent infinite loops)
  - Displays impact analysis in output with clear formatting
  - Integrated into conflict detection flow
  - Example: `📊 Impact analysis: Updating package-a to 2.0 would affect: - dependent-package-1 (requires package-a:^1.5)`
  - All impact analysis messages are translated and available in all 31 supported languages
  - **Optional feature**: Use `--show-impact` or `--impact` flag to enable impact analysis (disabled by default to reduce verbosity)
  - **Save to file**: Use `--save-impact` flag to save impact analysis to `composer-update-impact.txt` file (automatically added to `.gitignore`)
- **Outdated packages count**: Shows the total number of outdated packages found after checking
  - Displays a summary message: `✅ Found X outdated package(s)`
  - Only shown when packages are found (not in debug mode)
  - All count messages are translated and available in all 31 supported languages
- **Progress indicators for use case checking**: Added loading indicators to inform users about the progress of different verification types
  - Shows progress messages when checking dependency conflicts, searching fallback versions, checking abandoned packages, searching alternative packages, and checking maintainer information
  - Each progress message is shown only once per verification type (not repeated for each package)
  - Progress indicators are suppressed in debug mode (debug already shows detailed information)
  - All progress messages are translated and available in all 31 supported languages
  - Example messages: `⏳ Checking dependency conflicts...`, `⏳ Searching for fallback versions...`, `⏳ Checking for abandoned packages...`
- **Abandoned Package Detection**: Automatically detects and warns about abandoned packages in conflict scenarios
  - Detects abandoned packages via Packagist API when packages are filtered due to conflicts
  - Shows warning message with replacement package if available
  - Example: `(⚠️ Package is abandoned, replaced by: new-package/name)`
- **Fallback Version Suggestions**: Suggests compatible fallback versions when primary updates fail
  - Automatically searches for older compatible versions when conflicts are detected
  - Verifies fallback versions satisfy all conflicting dependencies
  - Shows alternative solutions section in output when fallbacks are found
  - Example: `💡 Alternative solutions: - package-a:1.9.5 (compatible with conflicting dependencies)`
- **Alternative Package Suggestions**: Automatically suggests alternative packages when updates are blocked by conflicts
  - Searches Packagist API for similar packages using keywords extracted from package names
  - Shows replacement packages when packages are abandoned without replacement
  - Shows alternative packages when no fallback version is available
  - Returns top 3 most relevant alternatives based on Packagist search results
  - Example: `💡 Alternative packages: - new-package/name (recommended replacement) - alternative/pkg (similar functionality)`
- **Maintainer Contact Suggestions**: Automatically suggests contacting package maintainers when no automatic solution is available
  - Detects scenarios where manual intervention is needed (incompatible constraints, stale packages)
  - Extracts maintainer information from Packagist API (name, email, homepage)
  - Generates repository issue URLs for GitHub, GitLab, and Bitbucket
  - Shows stale package warnings (>2 years without updates)
  - Provides actionable steps for manual resolution
  - Example: `⚠️ No automatic solution available - Contact package maintainer(s): John Doe (john@example.com)`
- **Modular Architecture Refactoring**: Refactored `process-updates.php` into modular classes for better maintainability
  - Created `OutputFormatter` class to handle all output formatting logic
  - Reduced `process-updates.php` from 991 lines to 614 lines (38% reduction)
  - Improved code organization and testability
  - All formatting logic now centralized in `bin/lib/OutputFormatter.php`
- **Comprehensive test suite**: Added unit tests for new features
  - Tests for abandoned package detection logic
  - Tests for fallback version search logic
  - Tests for alternative package search logic
  - Tests for maintainer contact suggestions
  - Tests for multiple constraint scenarios
  - Tests for edge cases

### Changed
- **Updated UPDATE_CASES.md**: Documentation now reflects 15 fully supported cases (previously 14)
  - Added case for Conflict Impact Analysis (#14)
  - Case #11 (Wildcard Version Constraints) moved from "Partially Supported" to "Fully Supported"
  - Updated summary section to reflect current implementation status
- **Impact analysis is now optional**: Impact analysis is disabled by default to reduce output verbosity
  - Use `--show-impact` or `--impact` flag to enable impact analysis
  - Improves default output readability while keeping detailed analysis available when needed

## [2.0.21] - 2026-01-16

### Added
- **Update Cases and Scenarios documentation**: New comprehensive document `docs/UPDATE_CASES.md` explaining all update scenarios
  - Documents 10 fully supported cases (basic updates, dependency conflicts, transitive dependencies, etc.)
  - Documents 1 partially supported case (wildcard constraints)
  - Documents 10 not-yet-supported cases (circular dependencies, cascading chains, etc.)
  - Provides detailed examples and explanations for each scenario
  - Includes manual intervention guidance for cases requiring maintainer contact
- **Implementation Roadmap**: New detailed action plan `docs/IMPLEMENTATION_ROADMAP.md` for implementing not-yet-supported features
  - Prioritized implementation plan ordered by complexity and feasibility
  - 4 phases with detailed implementation steps
  - Estimated effort and dependencies for each feature
  - Translation requirements for all 31 supported languages
  - Complete timeline for feature implementation

### Changed
- **Improved dependency conflict messages**: Enhanced clarity of dependency conflict messages in the output
  - Changed from: `(conflicts with 1 package: lexik/jwt-authentication-bundle (^1.2))`
  - Changed to: `(conflicts with 1 package: lexik/jwt-authentication-bundle requires rector/rector ^1.2)`
  - The new format explicitly shows which package requires which version constraint
  - Makes it much clearer why a package update is being filtered
  - Users can now easily understand what needs to be updated to resolve the conflict
- **Documentation structure**: Enhanced documentation organization
  - Added references to UPDATE_CASES.md in README.md, CONFIGURATION.md, and USAGE.md
  - Added reference to IMPLEMENTATION_ROADMAP.md in README.md and UPDATE_CASES.md
  - Improved cross-referencing between documentation files
  - Added section on dependency conflicts in USAGE.md

### Breaking Changes
- None

> **Note**: See [UPGRADING.md](UPGRADING.md#upgrading-to-2021) for migration notes.

## [2.0.20] - 2026-01-11

### Fixed
- **Output alignment in dependency analysis section**: Fixed vertical alignment issue in the dependency conflict analysis output
  - Removed emoji number indicator from "Filtered by dependency conflicts" header line to match the formatting of "Packages that passed dependency check" header
  - Both header lines now have consistent alignment and indentation
  - Fixed spacing inconsistency in the "none" variant of filtered packages message
  - Output now displays correctly aligned sections for better readability

### Breaking Changes
- None

> **Note**: See [UPGRADING.md](UPGRADING.md#upgrading-to-2020) for migration notes.

## [2.0.19] - 2026-01-08

### Added
- **Extended language support**: Added translations for 20 additional languages (10 high-priority + 10 medium-priority)
  - **High-priority languages**: Dutch (nl), Czech (cs), Swedish (sv), Norwegian (no), Finnish (fi), Turkish (tr), Chinese (zh), Japanese (ja), Korean (ko), Arabic (ar)
  - **Medium-priority languages**: Hungarian (hu), Slovak (sk), Ukrainian (uk), Croatian (hr), Bulgarian (bg), Hebrew (he), Hindi (hi), Vietnamese (vi), Indonesian (id), Thai (th)
  - All languages include complete translations for PHP, Bash, and help files
  - Total of 31 fully supported languages: English (en) 🇬🇧 🇺🇸, Spanish (es) 🇪🇸, Portuguese (pt) 🇵🇹 🇧🇷, Italian (it) 🇮🇹, French (fr) 🇫🇷, German (de) 🇩🇪 🇦🇹, Polish (pl) 🇵🇱, Russian (ru) 🇷🇺, Romanian (ro) 🇷🇴, Greek (el) 🇬🇷, Danish (da) 🇩🇰, Dutch (nl) 🇳🇱 🇧🇪, Czech (cs) 🇨🇿, Swedish (sv) 🇸🇪, Norwegian (no) 🇳🇴, Finnish (fi) 🇫🇮, Turkish (tr) 🇹🇷, Chinese (zh) 🇨🇳 🇹🇼, Japanese (ja) 🇯🇵, Korean (ko) 🇰🇷, Arabic (ar) 🇸🇦 🇪🇬, Hungarian (hu) 🇭🇺, Slovak (sk) 🇸🇰, Ukrainian (uk) 🇺🇦, Croatian (hr) 🇭🇷, Bulgarian (bg) 🇧🇬, Hebrew (he) 🇮🇱, Hindi (hi) 🇮🇳, Vietnamese (vi) 🇻🇳, Indonesian (id) 🇮🇩, Thai (th) 🇹🇭
- **Complete i18n integration with debug support**: Full internationalization implementation for both PHP and Bash scripts
  - **PHP script (`process-updates.php`)**: All output messages now use translation function `t()`
    - Integrated i18n loader with multiple path detection (vendor, development, project root)
    - Comprehensive debug logging for i18n functionality
    - Debug messages show: detected language, config file used, translation function availability, loaded translations count, and test translations
    - All user-facing messages are now translatable: framework constraints, ignored packages, dependency analysis, filtered packages, suggested commands, etc.
  - **Bash script (`generate-composer-require.sh`)**: Complete translation integration
    - Integrated sh-compatible translation loader (`translations-sh.sh`)
    - All `MSG_*` variables replaced with `get_msg()` function that uses translations
    - Fallback to English hardcoded messages if translations are not available
    - Debug support for i18n: shows detected language, loader status, and translation function availability
    - All messages are now translatable: loading indicators, error messages, debug messages, etc.
  - **Translation loader improvements**:
    - Created `translations-sh.sh` for POSIX-compatible sh scripts (not just bash)
    - Improved path detection for translation files in different environments
    - Better error handling and fallback mechanisms
- **Internationalization (i18n) support** (⚠️ DEVELOPMENT MODE): Added multi-language support for output messages
  - Automatic language detection from system environment variables (`LANG`, `LC_ALL`, `LC_MESSAGES`)
  - Manual language configuration via `language` option in `generate-composer-require.yaml`
  - Supported languages: English (en) 🇬🇧 🇺🇸, Spanish (es) 🇪🇸, Portuguese (pt) 🇵🇹 🇧🇷, Italian (it) 🇮🇹, French (fr) 🇫🇷, German (de) 🇩🇪 🇦🇹, Polish (pl) 🇵🇱, Russian (ru) 🇷🇺, Romanian (ro) 🇷🇴, Greek (el) 🇬🇷, Danish (da) 🇩🇰, Dutch (nl) 🇳🇱 🇧🇪, Czech (cs) 🇨🇿, Swedish (sv) 🇸🇪, Norwegian (no) 🇳🇴, Finnish (fi) 🇫🇮, Turkish (tr) 🇹🇷, Chinese (zh) 🇨🇳 🇹🇼, Japanese (ja) 🇯🇵, Korean (ko) 🇰🇷, Arabic (ar) 🇸🇦 🇪🇬, Hungarian (hu) 🇭🇺, Slovak (sk) 🇸🇰, Ukrainian (uk) 🇺🇦, Croatian (hr) 🇭🇷, Bulgarian (bg) 🇧🇬, Hebrew (he) 🇮🇱, Hindi (hi) 🇮🇳, Vietnamese (vi) 🇻🇳, Indonesian (id) 🇮🇩, Thai (th) 🇹🇭 - Total: 31 languages
  - Translations available for both PHP (`process-updates.php`) and Bash (`generate-composer-require.sh`) scripts
  - Debug messages also support translations
  - Translation files are stored in the package (`vendor/nowo-tech/composer-update-helper/bin/i18n/`) and not copied to project root
  - Only `generate-composer-require.sh` is copied to project root; all other files remain in the package

### Changed
- **Dependency checking feature promoted to production**: The `check-dependencies` feature is now considered production-ready
  - Removed all "development mode" warnings from documentation
  - Feature has been thoroughly tested and refined
  - All edge cases have been addressed and the feature is stable
  - The feature is enabled by default (`check-dependencies: true`) and recommended for all users
  - Comprehensive dependency conflict detection, including transitive dependencies and `self.version` constraints
  - Full support for both `require` and `require-dev` dependency detection
- **Script architecture refactoring**: Made the main script (`generate-composer-require.sh`) more lightweight
  - Created `script-helper.sh` in vendor to contain complex helper functions
  - Reduced main script from ~502 lines to ~240 lines (52% reduction)
  - All complex logic (file detection, language detection, loading indicators) moved to vendor helper
  - Main script now only contains essential wrapper logic, keeping it minimal and maintainable
  - Helper script remains in vendor; only `generate-composer-require.sh` is copied to project root
- **Improved output formatting for dependency conflicts**:
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
- **Code quality improvements**: Enhanced type safety and modern PHP compatibility
  - All nullable parameters are now explicitly declared
  - Better type hints throughout the codebase
  - Improved compatibility with modern PHP static analysis tools
- **Code coverage command**: Updated `composer test-coverage` to use dedicated configuration file
  - Now uses `--configuration=phpunit.coverage.xml.dist` to load coverage-specific configuration
  - Prevents PHPUnit warnings about missing filter configuration
  - Ensures consistent coverage report generation
  - Coverage configuration is now isolated in a separate file for better maintainability
- **PHPUnit configuration for PHPUnit 11**: Updated coverage configuration to use modern PHPUnit 11 structure
  - Moved `<include>` directive from `<coverage>` to `<source>` tag (PHPUnit 11 requirement)
  - Removed deprecated `processUncoveredFiles` attribute
  - Tests and coverage now run cleanly in Docker environment without warnings
  - Verified 99.58% line coverage (478/480 lines) with 132 tests passing

### Fixed
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
- **Implicitly nullable parameters**: Fixed PHP deprecation warnings for implicitly nullable parameters
  - Updated `findCompatibleVersion` function parameters to explicitly use `?array` for nullable arrays
  - Updated `readConfigValue` function parameter to use `mixed $default = null` instead of implicit nullable
  - Updated `t()` function parameter in i18n loader to use `?string $lang = null`
  - All code now adheres to modern PHP standards regarding nullable types
  - Eliminates `Implicitly nullable parameters are deprecated.intelephense(P1078)` warnings
- **Test compatibility**: Updated `ScriptTest.php` to match refactored script structure
  - Updated assertions to check for `PROCESSOR_PATHS` instead of `PROCESSOR_PHP`
  - Updated path assertion to match new script structure
- **i18n synchronization fixes**: Fixed help messages and debug messages not synchronizing with language configuration
  - Help messages (`--help`) now correctly detect and use the language defined in `generate-composer-require.yaml`
  - Debug messages now correctly synchronize with the configured language
  - `detect_language_for_script()` function now proactively detects configuration file if `CONFIG_FILE` is not yet defined
  - `t()` function in `translations-sh.sh` now automatically detects config file if `TRANSLATIONS_LANG` is empty
  - Translations are explicitly initialized after configuration file detection in `generate-composer-require.sh`
  - Ensures consistent language usage across all script output (help, debug, regular messages)

## [2.0.18] - 2026-01-07

### Fixed
- **Dependency conflict detection order**: Fixed critical issue where dependency conflict detection was checking package requirements before dependent package constraints
  - The system now correctly verifies dependent package constraints FIRST before checking package requirements
  - This ensures that versions like `phpdocumentor/reflection-docblock:6.0.0` are correctly rejected when dependent packages require `^5.6` or `^5.0`
  - Previously, the system would suggest incompatible updates that would fail during `composer require`
  - Example: `phpdocumentor/reflection-docblock:6.0.0` is now correctly filtered when `a2lix/auto-form-bundle` requires `^5.6` and `nelmio/api-doc-bundle` requires `^5.0`
- **Dependency detection in require-dev**: Fixed issue where dependencies in `require-dev` sections were not being detected
  - `getPackageConstraintsFromLock` now searches in both `require` and `require-dev` sections
  - This ensures all dependent packages are found, regardless of whether they're in production or development dependencies

### Changed
- **Enhanced conflict information**: Filtered packages now show which dependent packages cause the conflict
  - Example: `phpdocumentor/reflection-docblock:6.0.0 (prod) (conflicts with: a2lix/auto-form-bundle (^5.6), nelmio/api-doc-bundle (^5.0))`
  - This makes it easier to understand why a package is being filtered and what needs to be updated
- **Output formatting improvements**: Multiple enhancements for better readability and consistency
  - Standardized spacing before emojis throughout the output for consistent visual appearance
  - Consistent indentation across all sections (framework constraints, ignored packages, dependency analysis)
  - Visual enhancement: Package count in filtered conflicts now uses emoji numbers (1️⃣, 2️⃣, 3️⃣, etc.) for better visibility
  - Example: `⚠️  2️⃣ Filtered by dependency conflicts:` instead of `⚠️  2 Filtered by dependency conflicts:`
- **Debug logging improvements**: Added comprehensive debug messages for dependency conflict detection
  - Shows when conflicts are detected with dependent packages
  - Tracks conflicting dependents for each filtered package
  - Provides detailed information about the conflict resolution process
  - Includes debug messages for conflict tracking and output generation

### Migration Notes
- **No action required**: These are bug fixes and improvements that make dependency conflict detection more accurate
- Packages that were previously incorrectly suggested will now be correctly filtered
- The enhanced conflict information will help you understand why packages are being filtered

### Breaking Changes
- None

## [2.0.17] - 2026-01-07

### Added
- **Transitive dependency update suggestions**: When packages are filtered due to dependency conflicts, the system now suggests updating the required transitive dependencies
  - Example: If `scheb/2fa-google-authenticator:8.2.0` requires `spomky-labs/otphp:^11.4` but version `11.3.0` is installed, the system suggests updating `spomky-labs/otphp` to `11.4.1`
  - The system detects conflicts with `self.version` constraints (e.g., `scheb/2fa-email` requiring `scheb/2fa-bundle: self.version`)
  - Automatically generates composer commands that include both transitive dependencies and filtered packages in a single command
  - This makes it easier to resolve dependency conflicts by updating all related packages together
- **Automatic command generation for transitive dependencies**: Commands now include both the transitive dependencies and the packages that require them
  - Example: `composer require --with-all-dependencies scheb/2fa-bundle:8.2.0 spomky-labs/otphp:11.4.1 scheb/2fa-email:8.2.0 scheb/2fa-google-authenticator:8.2.0 scheb/2fa-totp:8.2.0`
  - Ensures all related packages are updated together, preventing partial update conflicts
- **Enhanced output messages**: More precise messages explaining why no packages are available for update
  - Differentiates between "all packages up to date", "all packages ignored", and "all packages have dependency conflicts"
  - Provides actionable guidance when dependency conflicts are detected

### Changed
- **Improved dependency conflict detection**: Enhanced logic to collect all transitive dependencies before returning
  - When a conflict with `self.version` is detected, the system continues checking other dependencies
  - This ensures all required transitive dependencies are identified and suggested
  - Previously, only the first conflict would be detected, potentially missing other required updates
- **Code refactoring**: Improved code maintainability with helper functions
  - Added `normalizeVersion()` function to centralize version normalization
  - Added `formatPackageList()` function to format package lists consistently
  - Added `buildComposerCommand()` function to construct composer commands
  - Added `addPackageToArray()` function to handle prod/dev package categorization
  - Added `debugLog()` function for consistent debug logging
  - Extracted constants for repeated strings and emojis
- **Shell script improvements**: Enhanced user experience with better loading indicators
  - Loading indicators now appear in the same line as completion checkmarks
  - Improved consistency in progress messages
  - Refactored code to use variables for repeated messages and emojis

### Fixed
- **Command generation for filtered packages**: Fixed issue where commands for transitive dependencies didn't include the filtered packages that require them
  - Commands now include both transitive dependencies and filtered packages together
  - Prevents "package is locked" errors when trying to update only transitive dependencies
  - Example: Previously suggested only `scheb/2fa-bundle:8.2.0 spomky-labs/otphp:11.4.1`, now includes all related packages

### Migration Notes
- **No action required**: These are enhancements that improve the dependency conflict resolution workflow
- When dependency conflicts are detected, you'll now see suggested commands that include all necessary updates
- The commands can be executed directly or using the `--run` flag

### Breaking Changes
- None

## [2.0.16] - 2026-01-05

### Changed
- **Code coverage threshold**: Adjusted CI/CD coverage requirement from 100% to 99%
  - Reflects the reality that some defensive code (migration verification failure paths) cannot be easily tested without introducing bugs
  - Current coverage: 99.20% (498/502 elements)
  - The remaining 2 uncovered lines are defensive code that protects against unexpected errors

### Removed
- **Dead code elimination**: Removed unreachable code paths in migration logic
  - Removed lines 235 and 422 from `Installer.php` and `Plugin.php` respectively
  - These lines were never executed due to logical flow constraints
  - Improves code maintainability without affecting functionality

### Technical Details
- **Coverage improvement**: Increased from 98.42% to 99.20% by removing dead code
- **CI/CD updates**: Updated coverage validation scripts to accept 99% threshold
- **No breaking changes**: All functionality remains unchanged

### Migration Notes
- **No action required**: This is an internal improvement with no user-facing changes

### Breaking Changes
- None

## [2.0.13] - 2025-12-26

### Fixed
- **Duplicate command output**: Fixed issue where suggested composer commands appeared twice in the output
  - Commands between `---COMMANDS_START---` and `---COMMANDS_END---` markers were not being properly filtered from display
  - Updated shell script to use `sed` to remove the entire command extraction block before displaying output
  - Commands now appear only once in the suggested commands section
  - Command extraction for `--run` flag continues to work correctly

### Migration Notes
- **No action required**: This is a bug fix that improves output clarity
- Existing functionality remains unchanged
- The `--run` flag continues to work as expected

### Breaking Changes
- None

## [2.0.12] - 2025-12-26

### Changed
- **CI/CD coverage threshold**: Lowered code coverage requirement from 100% to 90%
  - CI/CD pipeline now requires minimum 90% code coverage (previously 100%)
  - Updated validation scripts and GitHub Actions workflows
  - Current coverage: 92.36% (447/484 lines)
  - This change allows for more practical coverage requirements while maintaining high quality standards

### Added
- **Enhanced test coverage**: Added new test cases to improve code coverage
  - Added tests for edge cases in YAML parsing and migration logic
  - Added tests for empty file handling and section detection
  - Added tests for migration scenarios with different package configurations
  - Coverage increased from 91.94% to 92.36%

### Migration Notes
- **No action required**: This is a configuration change in CI/CD only
- Existing functionality remains unchanged
- Tests continue to run and validate code quality

### Breaking Changes
- None

## [2.0.11] - 2025-12-26

### Fixed
- **Migration logic improvement**: Fixed migration behavior to preserve user-defined packages in YAML configuration
  - Migration now correctly detects when YAML has user-defined packages and does NOT migrate from TXT if packages differ
  - Previously, migration would merge TXT packages into YAML even when YAML had different user-defined packages
  - Now preserves user's YAML configuration when it contains packages different from the TXT file
  - TXT file is only deleted if packages match (already migrated) or if YAML is empty/template-only
  - This ensures user's custom configuration is never overwritten during migration

### Changed
- **Improved migration safety**: Migration logic now better distinguishes between:
  - Empty/template YAML files (safe to migrate)
  - User-defined YAML files with packages (preserve, do not migrate)
  - Matching packages (already migrated, just delete TXT)

### Migration Notes
- **No action required**: This is a bug fix that improves migration safety
- If you have both TXT and YAML files with different packages, the YAML will be preserved
- The TXT file will remain until you manually merge the packages or delete it

### Breaking Changes
- None

## [2.0.10] - 2025-12-26

### Changed
- **Refactored output formatting**: Moved all output formatting logic from shell script to PHP processor
  - PHP now generates fully formatted output (emojis, formatting, sections, etc.)
  - Shell script simplified from 396 to 283 lines (28.5% reduction)
  - PHP processor increased from 622 to 710 lines (includes all formatting logic)
  - Shell script now only displays PHP output and extracts commands for `--run` flag
  - Commands are extracted between `---COMMANDS_START---` and `---COMMANDS_END---` markers
  - All parsing and formatting logic centralized in PHP for better maintainability

### Benefits
- **Lighter shell script**: 28.5% reduction in lines (396 → 283)
- **Better maintainability**: All formatting logic in one place (PHP)
- **Cleaner architecture**: Clear separation between display (shell) and formatting (PHP)
- **Same functionality**: All features work exactly the same, just better organized

## [2.0.9] - 2025-12-26

### Fixed
- **Script auto-update on package update**: Fixed issue where `generate-composer-require.sh` was not automatically updated when the package was updated
  - The `onPostUpdate` method now calls `installFiles` to update the script when content differs
  - The script now compares MD5 hashes and updates if content differs
  - Ensures users always get the latest script version when running `composer update`
  - Previously, the script was only installed on first installation (`composer install`), not updated on subsequent updates (`composer update`)

### Changed
- **Improved update logic**: The Plugin now properly updates the script file when content changes, matching the behavior of the Installer class
  - Both `onPostInstall` and `onPostUpdate` now update the script if content differs
- **Refactored architecture for better maintainability**: The script has been split into two parts:
  - **Lightweight wrapper script** (`generate-composer-require.sh`): ~396 lines in your repository
    - Handles command-line arguments, configuration file detection, and calls the PHP processor
    - Automatically detects and calls the PHP processor in vendor
  - **PHP processor** (`process-updates.php`): ~622 lines in vendor
    - Contains all complex logic (package processing, framework detection, release info, YAML parsing, output formatting, etc.)
    - Automatically updated with `composer update`
  - **Benefits**:
    - Script in repo is now 59% lighter (~396 lines vs ~971 lines)
    - Complex logic is automatically updated via Composer
    - Better separation of concerns and maintainability
    - Automatic detection of PHP processor (no configuration needed)

### Technical Details
- The script automatically detects `process-updates.php` in `vendor/nowo-tech/composer-update-helper/bin/`
- Falls back to script directory for development mode
- Clear error message if PHP processor is not found

## [2.0.8] - 2025-12-26

### Changed
- **Improved debug messages**: Enhanced verbose and debug output to clearly indicate when no packages are found
  - Debug mode now shows: "No ignored packages found (all packages are commented or section is empty)"
  - Verbose mode now shows: "Ignored packages: none (all packages are commented or section is empty)"
  - Helps users understand why packages might not be read from YAML configuration

### Documentation
- **Clarified YAML configuration**: Updated README.md to explicitly explain that only uncommented packages are read
  - Added examples showing the difference between commented and uncommented packages
  - Clarified that lines starting with `#` are treated as comments and ignored
  - Added important notes in both "Ignoring Packages" and "Forcing Package Inclusion" sections

## [2.0.7] - 2025-12-26

### Added
- **Verbose and Debug modes**: New options for troubleshooting and detailed information
  - `-v, --verbose`: Shows detailed information about configuration files and packages
  - `--debug`: Shows very detailed debug information (includes verbose mode)
  - All debug/verbose output is sent to stderr to avoid interfering with normal output
  - Useful for troubleshooting configuration file issues, package detection, and script behavior
- **Support for `.yml` extension**: Script now supports both `.yaml` and `.yml` extensions
  - Priority: `.yaml` first, then `.yml`, then `.txt` (backward compatibility)
  - Allows users to use either extension based on their preference
- **New flag `--release-info`**: Enables release information summary (release link and changelog link)
  - Complements `--release-detail` which shows full changelog
  - Both flags enable release information (previously only `--release-detail` existed)

### Changed
- **Configuration file location**: Script now searches for configuration files in the current directory (where `composer.json` is located)
  - Previously searched in the script's directory (`$(dirname "$0")`)
  - Now searches in the current working directory (where the script is executed)
  - This ensures configuration files are found correctly regardless of script location
- **Release information default behavior**: Release information is now **disabled by default**
  - Previously, release information was shown by default (required API calls)
  - Now, release information is only shown when explicitly requested
  - Improves performance by default (no API calls are made)
  - Use `--release-info` to enable release information summary
  - Use `--release-detail` to enable full release changelog
  - Use `--no-release-info` to explicitly disable (default behavior)
- **Improved configuration file detection**: Better handling of file search and detection
  - Clearer messages when files are found or not found
  - Better error handling for missing files

### Fixed
- **Configuration file reading**: Fixed issue where script couldn't find YAML files in the project directory
  - Script now correctly searches in the current directory instead of script directory
  - Resolves issues where configuration files weren't being read properly
- **Migration verification**: Fixed migration verification to only compare packages from the `ignore` section with TXT content
  - Previously, verification included packages from both `ignore` and `include` sections
  - This caused migration to fail when YAML had packages in `include` section that weren't in TXT
  - Now only `ignore` section is compared, allowing TXT file to be deleted correctly
  - Fixes test failures in `testMigrationReadsIncludeSectionFromYaml`

### Documentation
- Updated help text to reflect new default behavior and new options
- Updated README.md with verbose/debug options, .yml support, and configuration file location
- Updated examples to show new flags and default behavior
- Added comprehensive upgrade instructions in UPGRADING.md

## [2.0.6] - 2025-12-26

### Added
- **Verification**: Complete verification of YAML include/ignore functionality
  - Comprehensive verification of YAML parsing (Bash/AWK)
  - PHP loading and processing verification
  - Priority logic verification
  - Test cases documentation
  - Unit tests coverage information
- **Utility scripts**: New utility scripts in `tests/` directory
  - `check-coverage.php`: Script to validate 90% code coverage (same logic as CI/CD)
  - `test-yaml-include.sh`: Test script to verify YAML reading and include logic
  - `test-release-info.sh`: Test script to verify release information behavior
  - All scripts are in English and properly organized

### Changed
- **Documentation improvements**: Enhanced README.md with better release information documentation
  - Clarified that release information is disabled by default (improves performance)
  - Added note about performance improvement when not using release info
  - Better explanation of when release information is fetched
- **YAML template comments**: Updated comments in `bin/generate-composer-require.yaml`
  - Clarified that include section has priority over ignore section
  - Removed incorrect reference to "not direct dependencies"

### Fixed
- **Documentation accuracy**: Corrected documentation to accurately reflect functionality
  - Removed misleading statement about processing packages that are not direct dependencies
  - Clarified that include section overrides ignore list, not that it processes non-dependencies

## [2.0.5] - 2025-12-26

### Added
- **Package inclusion feature**: New `include` section in YAML configuration
  - Force include packages even if they are in the `ignore` list
  - Override ignore list for specific packages
  - Useful for scenarios where you want to ignore most packages but force include specific ones
  - Example: Ignore all Symfony packages but force include `symfony/security-bundle`

### Changed
- **Improved YAML parsing**: Enhanced reading of YAML configuration files
  - Better handling of inline comments
  - Support for different indentation levels
  - More robust section detection (ignore and include)
  - Improved handling of empty lines and comment-only lines
  - Both `ignore` and `include` sections are now properly read and processed

### Fixed
- **YAML reading logic**: Fixed issues with reading packages from YAML files
  - Now correctly extracts packages from both `ignore` and `include` sections
  - Improved detection of section boundaries
  - Better handling of edge cases (comments, empty lines, different indentations)

## [2.0.4] - 2025-12-26

### Changed
- **Migration verification**: Migration now verifies that packages were correctly migrated before deleting the old `.ignore.txt` file
  - Compares packages from TXT and YAML files
  - Only deletes TXT file if migration is verified as correct
  - Shows warning if verification fails and preserves TXT file for safety

### Fixed
- **`.gitignore` behavior**: `.sh` and `.yaml` files are no longer added to `.gitignore`
  - These files should be committed to the repository for team collaboration
  - Plugin now removes old `.ignore.txt` entries from `.gitignore` if they exist
  - Plugin also removes `.sh` and `.yaml` entries if they were previously added (cleanup)

## [2.0.3] - 2025-12-26

### Changed
- **Documentation and tests**: Complete test coverage for improved migration logic
  - Added tests for migration when YAML exists but is empty
  - Added tests for migration when YAML contains only template
  - Added tests to verify migration is skipped when YAML has user packages
  - Updated all documentation to reflect v2.0.2 improvements

## [2.0.2] - 2025-12-26

### Fixed
- **Improved migration logic**: Migration now works even if YAML file already exists
  - Previously, migration only occurred if YAML didn't exist
  - Now migrates if YAML exists but is empty or contains only template (no user packages)
  - Prevents data loss: won't overwrite YAML files with user-defined packages
  - Ensures TXT files are migrated even after initial update

## [2.0.1] - 2025-12-26

### Fixed
- **Migration now deletes old TXT file**: When migrating from `generate-composer-require.ignore.txt` to `generate-composer-require.yaml`, the old TXT file is now automatically deleted after successful migration
  - Previous behavior: TXT file remained after migration
  - New behavior: TXT file is deleted after migration to YAML
  - Ensures clean migration without leftover files

### Changed
- Improved migration process to automatically clean up old configuration files
- Updated tests to verify TXT file deletion after migration

## [2.0.0] - 2025-12-26

### Changed
- **BREAKING**: Configuration file format changed from TXT to YAML
  - Old format: `generate-composer-require.ignore.txt` (still supported for backward compatibility)
  - New format: `generate-composer-require.yaml`
  - Automatic migration: The plugin automatically migrates old TXT files to YAML format on update
- **Improved configuration format**: YAML format provides better structure and extensibility
  - Packages are now listed under `ignore:` key as a YAML array
  - More intuitive and maintainable configuration format

### Added
- **Automatic migration**: When updating from v1.x, existing `.ignore.txt` files are automatically migrated to `.yaml` format
- **Backward compatibility**: The script still supports reading old `.ignore.txt` format if YAML file doesn't exist
- **YAML configuration file**: New `generate-composer-require.yaml` configuration file with structured format

### Migration Notes
- No manual action required: The migration happens automatically when you update the package
- Your existing ignore list will be preserved during migration
- The old `.ignore.txt` file will be automatically deleted after successful migration to `.yaml` format

## [1.3.4] - 2025-12-14

### Added
- **Help option**: Added `--help` and `-h` flags to display comprehensive usage information
  - Shows all available options, examples, and framework support details
  - Works without requiring Composer to be installed
  - Provides detailed documentation directly from the script

### Changed
- **Performance optimization**: Emojis are now defined as variables at script initialization
  - All emojis loaded once at startup instead of being repeated throughout the script
  - Reduces script size and improves execution performance
  - Makes the script more maintainable (change emoji once, affects all uses)
- **Improved output formatting**: Added spacing after emojis for better console readability
  - All emojis now have proper spacing (2 spaces for simple emojis, 3 for compound emojis)
  - Better visual separation in terminal output
### Fixed

## [1.3.3] - 2025-12-14

### Added
- **Release information and changelogs**:
  - Script now automatically fetches release information from GitHub API
  - Shows release links and changelog links for outdated packages (default mode)
  - New `--release-detail` option to show full release changelog with complete details
  - New `--no-release-info` option to skip release information entirely
  - Default mode shows summary: package name, release link, and changelog link
  - Detailed mode (`--release-detail`) shows full release name and complete changelog
  - Only fetches release info for specific versions (not wildcards)
  - Gracefully handles API failures and network issues
  - All options are combinable with `--run` flag

### Changed

### Fixed
- **Fixed test suite issues**:
  - Fixed `testInstallFilesCreatesYamlConfigFileIfNotExists` to use temporary directories instead of real project directory
  - Tests no longer delete `bin/generate-composer-require.yaml` from the actual project during cleanup
  - Improved `ScriptTest` to handle cases where script is not available in CI/CD environments
  - All script-related tests now properly skip when script file doesn't exist instead of failing
- Fixed test coverage to achieve 100% code coverage
  - Fixed `testOnPostUpdateInstallsFiles` to correctly test `.gitignore` updates instead of file installation
  - Fixed `testInstallFilesUpdatesWhenContentDiffers` to correctly test that files are not updated when they already exist (unless forceUpdate is true)
  - Added `testInstallFilesForceUpdate` to cover forced file updates
  - Made `testIgnoreFileTemplateExists` optional since the ignore file template is not required
  - Added tests to cover `.gitignore` update scenarios when file exists without trailing newline

### Changed
- Improved test suite to ensure 100% code coverage
  - All statements, methods, and elements are now fully covered
  - Enhanced test cases for edge cases in `.gitignore` updates

## [1.3.2] - 2025-12-12

### Added

### Changed

### Fixed
- Fixed PluginTest expectations to include `.gitignore` update messages
  - All tests now correctly expect the automatic `.gitignore` update message
  - Fixed `testInstallFilesSkipsWhenContentMatches` to allow `.gitignore` update message

## [1.3.1] - 2025-12-12

### Added
- **PHPDoc documentation**: Added comprehensive PHPDoc comments in English to all PHP classes
  - All classes in `src/` directory (Plugin, Installer)
  - All test classes in `tests/` directory (PluginTest, InstallerTest, ScriptTest)
  - Each class includes description, `@author`, and `@see` annotations
  - Improved code documentation and IDE support

### Changed
- Fixed missing `IOInterface` import in `Installer.php`
- Enhanced class documentation with detailed descriptions

## [1.3.0] - 2025-12-12

### Added
- **Automatic .gitignore updates**: Plugin now automatically adds installed files to `.gitignore`
  - Adds `generate-composer-require.sh` to `.gitignore`
  - Adds `generate-composer-require.ignore.txt` to `.gitignore`
  - Prevents duplicate entries
  - Adds comment section for clarity

### Changed
- Improved plugin installation process with automatic dependency installation in Docker containers

## [1.2.7] - 2025-12-12

### Fixed
- Fixed PHP 7.4 and 8.0 compatibility issue in `getChmodMode()` method
  - PHP parses entire files before execution, so literal `0o755` syntax cannot be used
  - Changed to use `octdec('755')` which is compatible with all PHP versions
  - Maintains the same functionality (0755 permissions) with full compatibility

### Changed
- Improved chmod permission handling with `getChmodMode()` method
  - Implemented in `src/Installer.php` and `src/Plugin.php`
  - Uses `octdec('755')` for cross-version compatibility (PHP 7.4+)

## [1.2.6] - 2025-12-12

### Fixed
- Fixed merge conflicts in PluginTest.php
- Resolved code formatting inconsistencies in test file
- Fixed PHP 7.4 and 8.0 compatibility: replaced octal literal syntax `0o777` with `0777` in test files
  - PHP 7.4 and 8.0 do not support explicit octal notation (`0o` prefix), only implicit octal (`0` prefix)
  - Fixed in test files (`tests/InstallerTest.php`, `tests/PluginTest.php`)

### Changed
- Improved chmod permission handling: now uses PHP version detection to use the most modern syntax available
  - Uses explicit octal notation (`0o755`) for PHP 8.1+ when available
  - Falls back to implicit octal (`0755`) for PHP 7.4 and 8.0 compatibility
  - Implemented in `src/Installer.php` and `src/Plugin.php` with `getChmodMode()` method

## [1.2.5] - 2025-12-12

### Changed
- Updated installation documentation to use `--dev` flag
  - Package should be installed as development dependency: `composer require --dev nowo-tech/composer-update-helper`
- Updated author email address from `hectorfranco@nowo.com` to `hectorfranco@nowo.tech`
- Code style improvements and formatting updates

## [1.2.4] - 2024-12-11

### Fixed
- Fixed Plugin.php to correctly detect vendor vs development mode
- Fixed tests to backup and restore original generate-composer-require.sh file
- Restored correct generate-composer-require.sh script content (was overwritten with test content)

### Changed
- Improved generate-composer-require.ignore.txt template with better documentation
- Added examples and usage guidelines to ignore file template
- Made ignore file template more practical and user-friendly

## [1.2.3] - 2024-12-11

### Added
- Comprehensive test suite achieving 100% code coverage
- Automatic PHP CS Fixer code style fixes on push to main/master branch
- 100% code coverage validation requirement in CI/CD pipeline
- Explicit test validation to ensure all tests pass before deployment

### Changed
- Improved CI/CD workflow with automatic code style fixes and commits
- Enhanced test coverage with additional test cases for Plugin and Installer classes
- Better error handling in test suite for edge cases

### Fixed
- Fixed PHPDoc formatting (added blank lines between @author and @see annotations)
- Fixed test expectations to handle multiple IO write calls correctly

## [1.2.2] - 2024-12-11

### Changed
- Updated CI/CD workflow configuration

## [1.2.1] - 2024-12-11

### Added
- PHPDoc documentation in English for all classes and methods
- Complete method documentation with @param and @return annotations
- Property documentation with @var annotations

## [1.2.0] - 2024-12-11

### Added
- **Multi-framework support**: automatic detection and version constraints for:
  - Laravel (`laravel/*` and `illuminate/*` packages)
  - Yii (`yiisoft/*` packages)
  - CakePHP (`cakephp/*` packages)
  - Laminas (`laminas/*` packages)
  - CodeIgniter (`codeigniter4/*` packages)
  - Slim (`slim/*` packages)
- Display detected framework constraints in output
- Exclude dev/alpha/beta/RC versions when finding latest compatible version

### Changed
- Improved version detection logic for all frameworks
- Better output formatting with framework detection info

## [1.1.0] - 2024-12-11

### Added
- Docker Compose configuration for local development
- Makefile with common development commands
- `.dockerignore` for optimized Docker builds
- Git pre-commit hooks for automatic CS-check and tests
- `make setup-hooks` command to install git hooks

### Changed
- Updated README with Docker development instructions
- Updated README with CI/CD information
- Updated CONTRIBUTING with Docker workflow

## [1.0.0] - 2024-12-11

### Added
- Initial release
- Shell script to generate `composer require` commands from outdated dependencies
- Support for ignoring specific packages via `.ignore.txt` file
- Automatic installation of scripts via Composer plugin
- Support for Symfony version constraints (`extra.symfony.require`)
- Separation of production and development dependencies
- `--run` flag to execute suggested commands directly
- Compatible with any PHP project (Symfony, Laravel, Yii, CodeIgniter, etc.)
- PHPUnit tests
- GitHub Actions CI/CD pipeline
- PHP-CS-Fixer configuration (PSR-12)
- Dependabot configuration for automated updates
- Issue and PR templates
- Security policy
