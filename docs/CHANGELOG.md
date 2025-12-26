# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
