<?php
/**
 * Translation loader for PHP
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

/**
 * Detect system language
 * 
 * @return string Language code (31 languages supported)
 */
function detectLanguage(): string
{
    // Try to get language from environment variables (in order of priority)
    $lang = getenv('LC_MESSAGES') ?: getenv('LC_ALL') ?: getenv('LANG') ?: 'en_US';
    
    // Extract language code (e.g., "es_ES.UTF-8" -> "es", "en_US.UTF-8" -> "en")
    $langCode = strtolower(substr($lang, 0, 2));
    
    // Supported languages (31 total - all implemented)
    $supported = ['en', 'es', 'pt', 'it', 'fr', 'de', 'pl', 'ru', 'ro', 'el', 'da', 'nl', 'cs', 'sv', 'no', 'fi', 'tr', 'zh', 'ja', 'ko', 'ar', 'hu', 'sk', 'uk', 'hr', 'bg', 'he', 'hi', 'vi', 'id', 'th'];
    
    // Return detected language if supported, otherwise default to English
    return in_array($langCode, $supported, true) ? $langCode : 'en';
}

/**
 * Load translations for a specific language
 * 
 * @param string $lang Language code
 * @return array Translations array
 */
function loadTranslations(string $lang): array
{
    static $cache = [];
    
    // Check cache first
    if (isset($cache[$lang])) {
        return $cache[$lang];
    }
    
    // Validate language code
    $supported = ['en', 'es', 'pt', 'it', 'fr', 'de', 'pl', 'ru', 'ro', 'el', 'da', 'nl', 'cs', 'sv', 'no', 'fi', 'tr', 'zh', 'ja', 'ko', 'ar', 'hu', 'sk', 'uk', 'hr', 'bg', 'he', 'hi', 'vi', 'id', 'th'];
    if (!in_array($lang, $supported, true)) {
        $lang = 'en';
    }
    
    // Get directory of this file
    $i18nDir = __DIR__;
    $translationFile = $i18nDir . '/' . $lang . '.php';
    
    // Load translations
    if (file_exists($translationFile)) {
        $translations = require $translationFile;
        $cache[$lang] = is_array($translations) ? $translations : [];
    } else {
        // Fallback to English if file doesn't exist
        $enFile = $i18nDir . '/en.php';
        $translations = file_exists($enFile) ? require $enFile : [];
        $cache[$lang] = is_array($translations) ? $translations : [];
    }
    
    return $cache[$lang];
}

/**
 * Translate a message
 * 
 * @param string $key Translation key
 * @param array $params Parameters for sprintf
 * @param string|null $lang Force language (optional)
 * @return string Translated message
 */
function t(string $key, array $params = [], ?string $lang = null): string
{
    // Get language from config or detect
    if ($lang === null) {
        // Try to get from config file first
        $configFile = getenv('CONFIG_FILE') ?: '';
        if ($configFile && file_exists($configFile)) {
            $lang = readConfigValue($configFile, 'language');
        }
        
        // If not in config, detect from system
        if (empty($lang)) {
            $lang = detectLanguage();
        }
    }
    
    // Load translations
    $translations = loadTranslations($lang);
    
    // Get translation
    $message = $translations[$key] ?? $key;
    
    // Apply parameters if provided
    if (!empty($params)) {
        $message = vsprintf($message, $params);
    }
    
    return $message;
}

/**
 * Read config value from YAML (helper function, should be available in process-updates.php)
 * This is a fallback if the function doesn't exist
 */
if (!function_exists('readConfigValue')) {
    function readConfigValue(string $yamlPath, string $key, mixed $default = null)
    {
        if (!file_exists($yamlPath)) {
            return $default;
        }
        
        $content = file_get_contents($yamlPath);
        if ($content === false) {
            return $default;
        }
        
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Skip empty lines and pure comment lines
            if (empty($trimmedLine) || strpos($trimmedLine, '#') === 0) {
                continue;
            }
            
            // Check for key: value pattern
            if (preg_match('/^' . preg_quote($key, '/') . ':\s*(.+)$/', $trimmedLine, $matches)) {
                $value = trim($matches[1]);
                // Handle boolean values
                if (strtolower($value) === 'true') {
                    return true;
                }
                if (strtolower($value) === 'false') {
                    return false;
                }
                // Return as string
                return $value;
            }
        }
        
        return $default;
    }
}

