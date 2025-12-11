# Contributing

Thank you for considering contributing to Composer Update Helper!

## Maintainer

This project is maintained by [Héctor Franco Aceituno](https://github.com/HecFranco) at [Nowo.tech](https://nowo.tech).

## Development Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/nowo-tech/composer-update-helper.git
   cd composer-update-helper
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Run tests:
   ```bash
   composer test
   ```

## Pull Request Process

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run tests and code style checks:
   ```bash
   composer test
   composer cs-check
   ```
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## Coding Standards

- Follow PSR-12 coding style
- Add tests for new features
- Update documentation as needed
- Keep commits atomic and descriptive

## Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Check code style
composer cs-check

# Fix code style
composer cs-fix
```

## Reporting Issues

When reporting issues, please include:
- PHP version
- Composer version
- Operating system
- Steps to reproduce
- Expected vs actual behavior

## Contact

For questions or suggestions, you can reach out to:
- GitHub: [@HecFranco](https://github.com/HecFranco)
- Organization: [nowo-tech](https://github.com/nowo-tech)
