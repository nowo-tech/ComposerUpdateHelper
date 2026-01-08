# Framework Support

Composer Update Helper automatically detects your framework and respects version constraints to prevent breaking updates.

## Symfony

Respects `extra.symfony.require` in `composer.json`:

```json
{
    "extra": {
        "symfony": {
            "require": "8.0.*"
        }
    }
}
```

## Laravel

Automatically detects `laravel/framework` version and limits all `laravel/*` and `illuminate/*` packages:

```json
{
    "require": {
        "laravel/framework": "^12.0"
    }
}
```

## Other Frameworks

| Framework | Core Package | Limited Packages |
|-----------|--------------|------------------|
| **Yii** | `yiisoft/yii2` | `yiisoft/*` |
| **CakePHP** | `cakephp/cakephp` | `cakephp/*` |
| **Laminas** | `laminas/laminas-mvc` | `laminas/*` |
| **CodeIgniter** | `codeigniter4/framework` | `codeigniter4/*` |
| **Slim** | `slim/slim` | `slim/*` |

## Example Output

```
🔧 Detected framework constraints:
  - symfony 8.0.*
  - laravel 12.0.*

⏭️  Ignored packages (prod):
  - doctrine/orm:3.0.0

🔧 Suggested commands:
  composer require --with-all-dependencies symfony/console:7.1.8
```

