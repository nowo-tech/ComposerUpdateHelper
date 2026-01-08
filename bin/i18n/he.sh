#!/bin/bash
# Hebrew translations
#
# This file contains Hebrew translations for bash scripts
# Format: declare -A TRANSLATIONS_HE=([key]='value' ...)

declare -A TRANSLATIONS_HE=(
    # Main messages
    ['loading_config']='טוען הגדרות...'
    ['checking_outdated']='בודק חבילות מיושנות...'
    ['processing']='מעבד חבילות...'
    ['processing_php']='מעבד חבילות עם סקריפט PHP...'
    ['running']='מריץ...'
    ['update_completed']='העדכון הושלם.'
    ['no_outdated']='אין תלויות ישירות מיושנות.'

    # Configuration
    ['found_config']='קובץ הגדרות נמצא: '
    ['no_config']='קובץ הגדרות לא נמצא (שימוש בערכי ברירת מחדל)'

    # Errors
    ['composer_not_found']='Composer לא מותקן או לא ב-PATH.'
    ['composer_json_not_found']='composer.json לא נמצא בתיקייה הנוכחית.'
    ['processor_not_found']='לא ניתן למצוא process-updates.php ב-vendor או בתיקיית סקריפטים.'
    ['please_install']='הרץ: composer install'
    ['unknown_option']='אפשרות לא ידועה:'
    ['use_help']='השתמש ב---help או -h למידע על השימוש.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='תיקייה נוכחית:'
    ['debug_searching_config']='מחפש קבצי הגדרות:'
    ['debug_composer_executed']='פקודת composer outdated בוצעה'
    ['debug_json_length']='אורך OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated החזיר JSON ריק'
    ['debug_passing_to_php']='מעביר לסקריפט PHP:'
    ['debug_output_length']='אורך פלט סקריפט PHP:'
    ['debug_processor_found']='מעבד PHP נמצא ב:'
)

