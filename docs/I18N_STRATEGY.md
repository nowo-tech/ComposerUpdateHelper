# Internationalization (i18n) Strategy

## File Structure

```
bin/
├── i18n/
│   ├── en.php          # English (default)
│   ├── es.php          # Spanish
│   ├── pt.php          # Portuguese
│   ├── it.php          # Italian
│   ├── fr.php          # French
│   ├── de.php          # German
│   ├── loader.php      # Translation loader for PHP
│   ├── translations.sh # Translation loader for Bash
│   ├── en.sh           # English translations for Bash
│   └── es.sh           # Spanish translations for Bash
├── generate-composer-require.sh
└── process-updates.php
```

## Configuration

### In `generate-composer-require.yaml`:
```yaml
# Language for output messages
# Supported: en, es, pt, it, fr, de
# If not set, will auto-detect from system (LANG, LC_ALL, LC_MESSAGES)
# Default: en (English)
# ⚠️  WARNING: i18n feature is currently in DEVELOPMENT MODE
language: es
```

## Language Detection Flow

1. **Read from YAML** (priority 1)
   - If `language: es` is in YAML, use Spanish
   
2. **Detect from system** (priority 2)
   - Read `LC_MESSAGES`, `LC_ALL`, `LANG`
   - Extract language code (e.g., `es_ES.UTF-8` → `es`)
   
3. **Fallback** (priority 3)
   - If not detected or not supported → `en` (English)

## Implementation

### PHP (process-updates.php)
- Function `detectLanguage()`: Detects system language
- Function `loadTranslations($lang)`: Loads PHP translation file
- Function `t($key, $params = [])`: Translates message
- Each `i18n/XX.php` file returns associative array

### Bash (generate-composer-require.sh)
- Function `detect_language()`: Detects system language
- Function `load_translations($lang)`: Loads translations from file
- Function `t($key)`: Translates message
- Separate files: `i18n/en.sh`, `i18n/es.sh`, etc.

## Translation File Format

### PHP (i18n/es.php):
```php
<?php
return [
    'no_packages_update' => 'No hay paquetes para actualizar',
    'all_up_to_date' => 'todos los paquetes están actualizados',
    'suggested_commands' => 'Comandos sugeridos:',
    // ... more translations
];
```

### Bash (i18n/es.sh):
```bash
declare -A TRANSLATIONS_ES=(
    ['no_packages_update']='No hay paquetes para actualizar'
    ['all_up_to_date']='todos los paquetes están actualizados'
    ['suggested_commands']='Comandos sugeridos:'
    # ... more translations
)
```

## Messages to Translate

### PHP (process-updates.php):
- Main output messages
- Debug messages (DEBUG: ...)
- Labels and descriptions

### Bash (generate-composer-require.sh):
- Informative messages
- Error messages
- Debug messages
- Help text

## Status: IN DEVELOPMENT

⚠️ This feature is in development mode and may change.

