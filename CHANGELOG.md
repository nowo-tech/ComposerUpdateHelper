# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- Updated installation documentation to use `--dev` flag
  - Package should be installed as development dependency: `composer require --dev nowo-tech/composer-update-helper`

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
