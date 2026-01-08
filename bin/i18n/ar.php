<?php
/**
 * Arabic translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'لا توجد حزم للتحديث',
    'all_up_to_date' => 'جميع الحزم محدثة',
    'all_have_conflicts' => 'جميع الحزم القديمة لديها تعارضات في التبعيات',
    'all_ignored' => 'جميع الحزم القديمة يتم تجاهلها',
    'all_ignored_or_conflicts' => 'جميع الحزم القديمة يتم تجاهلها أو لديها تعارضات في التبعيات',
    
    // Commands
    'suggested_commands' => 'الأوامر المقترحة:',
    'suggested_commands_conflicts' => 'الأوامر المقترحة لحل تعارضات التبعيات:',
    'includes_transitive' => '(يتضمن التبعيات العابرة اللازمة لحل التعارضات)',
    'update_transitive_first' => '(قم بتحديث هذه التبعيات العابرة أولاً، ثم أعد محاولة تحديث الحزم المفلترة)',
    
    // Framework and packages
    'detected_framework' => 'قيود الإطار المكتشفة:',
    'ignored_packages_prod' => 'الحزم المتجاهلة (prod):',
    'ignored_packages_dev' => 'الحزم المتجاهلة (dev):',
    'dependency_analysis' => 'تحليل فحص التبعيات:',
    'all_outdated_before' => 'جميع الحزم القديمة (قبل فحص التبعيات):',
    'filtered_by_conflicts' => 'مفلتر حسب تعارضات التبعيات:',
    'suggested_transitive' => 'تحديثات التبعيات العابرة المقترحة لحل التعارضات:',
    'packages_passed_check' => 'الحزم التي اجتازت فحص التبعيات:',
    'none' => '(لا شيء)',
    'conflicts_with' => 'يتعارض مع:',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'إجمالي الحزم القديمة: %d',
    'debug_require_packages' => 'حزم require: %d',
    'debug_require_dev_packages' => 'حزم require-dev: %d',
    'debug_detected_symfony' => 'قيود Symfony المكتشفة: %s (من extra.symfony.require)',
    'debug_processing_package' => 'معالجة الحزمة: %s (مثبتة: %s, الأحدث: %s)',
    'debug_action_ignored' => 'الإجراء: تم تجاهله (في قائمة التجاهل وليس في قائمة التضمين)',
    'debug_action_skipped' => 'الإجراء: تم تخطيه (لم يتم العثور على إصدار متوافق بسبب قيود التبعيات)',
    'debug_action_added' => 'الإجراء: تمت الإضافة إلى تبعيات %s: %s',
    'debug_no_compatible_version' => 'لم يتم العثور على إصدار متوافق لـ %s (مقترح: %s)',
    
    // Release info
    'release_info' => 'معلومات الإصدار',
    'release_changelog' => 'سجل التغييرات',
    'release_view_on_github' => 'عرض على GitHub',
];

