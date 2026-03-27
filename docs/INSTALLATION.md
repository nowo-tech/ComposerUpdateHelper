# Installation

## Composer

Install as a development dependency in the project where you want to generate update commands:

```bash
composer require --dev nowo-tech/composer-update-helper
```

After installation, the plugin copies `generate-composer-require.sh` and (when missing) `generate-composer-require.yaml` into your project root. Commit these files so the whole team can run the helper.

## Requirements

- PHP `>=8.1 <8.6`
- Composer 2.x

## Next steps

- [Configuration](CONFIGURATION.md)
- [Usage](USAGE.md)
