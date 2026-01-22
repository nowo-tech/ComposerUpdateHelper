<?php
/**
 * Hebrew translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'אין חבילות לעדכון',
    'all_up_to_date' => 'כל החבילות מעודכנות',
    'all_have_conflicts' => 'לכל החבילות המיושנות יש התנגשויות תלויות',
    'all_ignored' => 'כל החבילות המיושנות מתעלמות',
    'all_ignored_or_conflicts' => 'כל החבילות המיושנות מתעלמות או יש להן התנגשויות תלויות',
    
    // Commands
    'suggested_commands' => 'פקודות מומלצות:',
    'suggested_commands_conflicts' => 'פקודות מומלצות לפתרון התנגשויות תלויות:',
    'suggested_commands_grouped' => 'פקודות מומלצות (נסה להתקין יחד - Composer עשוי לפתור התנגשויות טוב יותר):',
    'grouped_install_explanation' => '(התקנת מספר חבילות יחד עוזרת לפעמים ל-Composer לפתור התנגשויות)',
    'includes_transitive' => '(כולל תלויות מעבר הנדרשות לפתרון התנגשויות)',
    'update_transitive_first' => '(עדכן תחילה את תלויות המעבר הללו, ואז נסה שוב לעדכן את החבילות המסוננות)',
    
    // Framework and packages
    'detected_framework' => 'הגבלות מסגרת שזוהו:',
    'ignored_packages_prod' => 'חבילות שמתעלמים מהן (prod):',
    'ignored_packages_dev' => 'חבילות שמתעלמים מהן (dev):',
    'dependency_analysis' => 'ניתוח בדיקת תלויות:',
    'all_outdated_before' => 'כל החבילות המיושנות (לפני בדיקת תלויות):',
    'filtered_by_conflicts' => 'מסונן לפי התנגשויות תלויות:',
    'suggested_transitive' => 'עדכוני תלויות מעבר מומלצים לפתרון התנגשויות:',
    'no_compatible_dependent_versions' => 'לא נמצאו גרסאות תואמות של חבילות תלויות:',
    'no_compatible_version_explanation' => '     - {depPackage}: לא נמצאה גרסה התומכת ב-{requiredBy}',
    'latest_checked_constraint' => '       (הגרסה האחרונה שנבדקה דורשת: {constraint})',
    'all_versions_require' => '       (כל הגרסאות הזמינות דורשות: {constraint})',
    'packages_passed_check' => 'חבילות שעברו את בדיקת התלויות:',
    'none' => '(אין)',
    'conflicts_with' => 'מתנגש עם:',
    'package_abandoned' => 'החבילה ננטשה',
    'abandoned_packages_section' => 'נמצאו חבילות נטושות:',
    'all_installed_abandoned_section' => 'כל החבילות הנטושות המותקנות:',
    'replaced_by' => 'הוחלף ב: %s',
    'alternative_solutions' => 'פתרונות חלופיים:',
    'compatible_with_conflicts' => 'תואם לתלויות מתנגשות',
    'alternative_packages' => 'חבילות חלופיות:',
    'recommended_replacement' => 'תחליף מומלץ',
    'similar_functionality' => 'פונקציונליות דומה',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'סה"כ חבילות מיושנות: %d',
    'debug_require_packages' => 'חבילות require: %d',
    'debug_require_dev_packages' => 'חבילות require-dev: %d',
    'debug_detected_symfony' => 'הגבלת Symfony שזוהתה: %s (מ-extra.symfony.require)',
    'debug_processing_package' => 'עיבוד חבילה: %s (מותקן: %s, אחרון: %s)',
    'debug_action_ignored' => 'פעולה: התעלמות (ברשימת התעלמות ולא ברשימת הכללה)',
    'debug_action_skipped' => 'פעולה: דילוג (לא נמצאה גרסה תואמת עקב הגבלות תלויות)',
    'debug_action_added' => 'פעולה: נוסף לתלויות %s: %s',
    'debug_no_compatible_version' => 'לא נמצאה גרסה תואמת עבור %s (מוצע: %s)',
    
    // Release info
    'release_info' => 'מידע על שחרור',
    'release_changelog' => 'יומן שינויים',
    'release_view_on_github' => 'הצג ב-GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ בודק התנגשויות תלותיות...',
    'checking_abandoned_packages' => '⏳ בודק חבילות נטושות...',
    'checking_all_abandoned_packages' => '⏳ בודק את כל החבילות המותקנות עבור סטטוס נטוש...',
    'searching_fallback_versions' => '⏳ מחפש גרסאות גיבוי...',
    'searching_alternative_packages' => '⏳ מחפש חבילות חלופיות...',
    'checking_maintainer_info' => '⏳ בודק מידע על מפתח...',
    
    // Impact analysis
    'impact_analysis' => 'ניתוח השפעה: עדכון {package} לגרסה {version} ישפיע על:',
    'impact_analysis_saved' => '✅ ניתוח השפעה נשמר ב: %s',
    'found_outdated_packages' => 'נמצאו %d חבילות מיושנות',
];

