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
    'suggested_commands_grouped' => 'الأوامر المقترحة (حاول التثبيت معًا - قد يحل Composer التعارضات بشكل أفضل):',
    'grouped_install_explanation' => '(تثبيت عدة حزم معًا يساعد أحيانًا Composer في حل التعارضات)',
    'grouped_install_warning' => '(Note: قد يفشل هذا إذا كانت هناك تعارضات مع الحزم المثبتة التي لا يمكن تحديثها)',
    'copy_command_hint' => '(Select the command to copy)',
    'packages_need_maintainer_update' => '(The following packages need updates from their maintainers)',
    'package_needs_update_for_grouped' => '%s (installed: %s) needs update to support: %s (requires: %s)',
    'suggest_contact_maintainer' => '💡 Consider contacting the maintainer of %s',
    'repository_url' => '📦 Repository: %s',
    'maintainers' => '👤 Maintainers: %s',
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
    'no_compatible_dependent_versions' => 'لم يتم العثور على إصدارات متوافقة من الحزم التابعة:',
    'no_compatible_version_explanation' => '     - {depPackage}: لم يتم العثور على إصدار يدعم {requiredBy}',
    'latest_checked_constraint' => '       (يتطلب الإصدار الأخير الذي تم التحقق منه: {constraint})',
    'all_versions_require' => '       (جميع الإصدارات المتاحة تتطلب: {constraint})',
    'packages_passed_check' => 'الحزم التي اجتازت فحص التبعيات:',
    'none' => '(لا شيء)',
    'conflicts_with' => 'يتعارض مع:',
    'package_abandoned' => 'الحزمة مهجورة',
    'abandoned_packages_section' => 'تم العثور على حزم مهجورة:',
    'all_installed_abandoned_section' => 'جميع الحزم المهجورة المثبتة:',
    'replaced_by' => 'استبدلت بـ: %s',
    'alternative_solutions' => 'حلول بديلة:',
    'compatible_with_conflicts' => 'متوافق مع التبعيات المتعارضة',
    'alternative_packages' => 'الحزم البديلة:',
    'recommended_replacement' => 'استبدال موصى به',
    'similar_functionality' => 'وظائف مماثلة',
    
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
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ التحقق من تعارضات التبعيات...',
    'checking_abandoned_packages' => '⏳ التحقق من الحزم المهجورة...',
    'checking_all_abandoned_packages' => '⏳ التحقق من جميع الحزم المثبتة للحالة المهجورة...',
    'searching_fallback_versions' => '⏳ البحث عن إصدارات احتياطية...',
    'searching_alternative_packages' => '⏳ البحث عن حزم بديلة...',
    'checking_maintainer_info' => '⏳ التحقق من معلومات المطور...',
    
    // Impact analysis
    'impact_analysis' => 'تحليل التأثير: تحديث {package} إلى {version} سيؤثر على:',
    'impact_analysis_saved' => '✅ تم حفظ تحليل التأثير في: %s',
    'found_outdated_packages' => 'تم العثور على %d حزمة قديمة',
];

