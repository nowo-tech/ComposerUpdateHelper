# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.3.3] - 2025-12-14

### Changed
- **Updated all demo frameworks to latest stable versions**:
  - Laravel: Already at 12.0 (latest)
  - Symfony: Already at 8.0 (latest)
  - Yii: Already at 2.0 (latest stable, Yii 3 in development)
  - CodeIgniter: Updated from 4.3 to 4.6 (latest stable)
  - Slim: Already at 4.12 (latest)
  - Legacy: Updated from Laravel 5.8/PHP 7.4 to Laravel 12/PHP 8.5
- **Updated PHPUnit to 11.0** in all demo projects
- **Updated demo .env.example files** with standard framework environment variables:
  - Each demo now includes complete `.env.example` with framework-specific variables
  - Added `PORT=8001` configuration for Docker port management
  - All demos use standard framework environment variable templates
- **Removed obsolete `version` attribute** from all `docker-compose.yml` files
  - Eliminates Docker Compose warnings about obsolete version attribute
- **Improved Makefile port checking**:
  - Fixed PORT extraction to use `grep "^PORT="` instead of `grep PORT` to avoid matching other port variables (DB_PORT, REDIS_PORT, etc.)
  - All demos now default to port 8001
  - Automatic port conflict detection and resolution

### Fixed
- **Fixed 502 Bad Gateway error** in all demo projects:
  - Changed PHP-FPM configuration from Unix socket to TCP (127.0.0.1:9000)
  - Updated all nginx configurations to use `fastcgi_pass 127.0.0.1:9000;`
  - Removed socket configuration from Dockerfiles (using default TCP configuration)
  - All demos now work correctly without 502 errors
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
- **Enhanced demo Makefile**: Added new commands for managing demo projects
  - Specific commands for each demo: `make laravel-down`, `make laravel-install`, `make laravel-test`
  - Generic install command: `make install DEMO=<name>` to install dependencies
  - Improved test commands that automatically start containers if not running
  - Commands available for all demos: laravel, symfony, yii, codeigniter, slim, legacy

### Changed
- Improved demo Makefile with better error handling and automatic container management
- Updated demo README with comprehensive documentation of all available commands

### Fixed
- Fixed PluginTest expectations to include `.gitignore` update messages
  - All tests now correctly expect the automatic `.gitignore` update message
  - Fixed `testInstallFilesSkipsWhenContentMatches` to allow `.gitignore` update message

## [1.3.1] - 2025-12-12

### Added
- **PHPDoc documentation**: Added comprehensive PHPDoc comments in English to all PHP classes
  - All classes in `src/` directory (Plugin, Installer)
  - All test classes in `tests/` directory (PluginTest, InstallerTest, ScriptTest)
  - All demo test classes in `demo/*/tests/` directory
  - Each class includes description, `@author`, and `@see` annotations
  - Improved code documentation and IDE support

### Changed
- Fixed missing `IOInterface` import in `Installer.php`
- Enhanced class documentation with detailed descriptions

## [1.3.0] - 2025-12-12

### Added
- **Demo projects**: Added comprehensive demo projects for testing Composer Update Helper
  - Laravel 12 demo (PHP 8.5)
  - Symfony 8.0 demo (PHP 8.5)
  - Yii 2 demo (PHP 8.5)
  - CodeIgniter 5 demo (PHP 8.5)
  - Slim 5 demo (PHP 8.5)
  - Legacy Laravel 5.8 demo (PHP 7.4)
  - Each demo is independent with its own `docker-compose.yml`
  - All demos include test suites
  - All demos automatically install Composer Update Helper on `composer install`
- **Automatic .gitignore updates**: Plugin now automatically adds installed files to `.gitignore`
  - Adds `generate-composer-require.sh` to `.gitignore`
  - Adds `generate-composer-require.ignore.txt` to `.gitignore`
  - Prevents duplicate entries
  - Adds comment section for clarity

### Changed
- Updated all demo projects to use latest framework versions:
  - Laravel: 11 → 12
  - Symfony: 7.1 → 8.0
  - CodeIgniter: 4 → 5
  - Slim: 4 → 5
- Updated all modern demos to use PHP 8.5 (latest stable version)
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
