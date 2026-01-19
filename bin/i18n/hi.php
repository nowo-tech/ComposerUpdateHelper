<?php
/**
 * Hindi translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'अपडेट करने के लिए कोई पैकेज नहीं',
    'all_up_to_date' => 'सभी पैकेज अप-टू-डेट हैं',
    'all_have_conflicts' => 'सभी पुराने पैकेजों में निर्भरता संघर्ष हैं',
    'all_ignored' => 'सभी पुराने पैकेज नजरअंदाज किए गए हैं',
    'all_ignored_or_conflicts' => 'सभी पुराने पैकेज नजरअंदाज किए गए हैं या उनमें निर्भरता संघर्ष हैं',
    
    // Commands
    'suggested_commands' => 'सुझाए गए कमांड:',
    'suggested_commands_conflicts' => 'निर्भरता संघर्षों को हल करने के लिए सुझाए गए कमांड:',
    'includes_transitive' => '(संघर्षों को हल करने के लिए आवश्यक सकर्मक निर्भरताएं शामिल हैं)',
    'update_transitive_first' => '(पहले इन सकर्मक निर्भरताओं को अपडेट करें, फिर फ़िल्टर किए गए पैकेजों को अपडेट करने का पुनः प्रयास करें)',
    
    // Framework and packages
    'detected_framework' => 'पता चला फ्रेमवर्क बाधाएं:',
    'ignored_packages_prod' => 'नजरअंदाज किए गए पैकेज (prod):',
    'ignored_packages_dev' => 'नजरअंदाज किए गए पैकेज (dev):',
    'dependency_analysis' => 'निर्भरता जांच विश्लेषण:',
    'all_outdated_before' => 'सभी पुराने पैकेज (निर्भरता जांच से पहले):',
    'filtered_by_conflicts' => 'निर्भरता संघर्षों द्वारा फ़िल्टर किया गया:',
    'suggested_transitive' => 'संघर्षों को हल करने के लिए सुझाए गए सकर्मक निर्भरता अपडेट:',
    'packages_passed_check' => 'निर्भरता जांच पास करने वाले पैकेज:',
    'none' => '(कोई नहीं)',
    'conflicts_with' => 'के साथ संघर्ष:',
    'package_abandoned' => 'पैकेज परित्यक्त है',
    'abandoned_packages_section' => 'परित्यक्त पैकेज मिले:',
    'all_installed_abandoned_section' => 'सभी स्थापित परित्यक्त पैकेज:',
    'replaced_by' => 'द्वारा प्रतिस्थापित: %s',
    'alternative_solutions' => 'वैकल्पिक समाधान:',
    'compatible_with_conflicts' => 'संघर्ष निर्भरताओं के साथ संगत',
    'alternative_packages' => 'वैकल्पिक पैकेज:',
    'recommended_replacement' => 'अनुशंसित प्रतिस्थापन',
    'similar_functionality' => 'समान कार्यक्षमता',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'कुल पुराने पैकेज: %d',
    'debug_require_packages' => 'require पैकेज: %d',
    'debug_require_dev_packages' => 'require-dev पैकेज: %d',
    'debug_detected_symfony' => 'पता चला Symfony बाधा: %s (extra.symfony.require से)',
    'debug_processing_package' => 'पैकेज प्रसंस्करण: %s (स्थापित: %s, नवीनतम: %s)',
    'debug_action_ignored' => 'कार्रवाई: नजरअंदाज (नजरअंदाज सूची में है और शामिल सूची में नहीं)',
    'debug_action_skipped' => 'कार्रवाई: छोड़ दिया (निर्भरता बाधाओं के कारण संगत संस्करण नहीं मिला)',
    'debug_action_added' => 'कार्रवाई: %s निर्भरताओं में जोड़ा गया: %s',
    'debug_no_compatible_version' => '%s के लिए कोई संगत संस्करण नहीं मिला (प्रस्तावित: %s)',
    
    // Release info
    'release_info' => 'रिलीज़ जानकारी',
    'release_changelog' => 'परिवर्तन लॉग',
    'release_view_on_github' => 'GitHub पर देखें',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ निर्भरता संघर्षों की जाँच कर रहा है...',
    'checking_abandoned_packages' => '⏳ परित्यक्त पैकेजों की जाँच कर रहा है...',
    'checking_all_abandoned_packages' => '⏳ सभी स्थापित पैकेजों की परित्यक्त स्थिति की जाँच कर रहा है...',
    'searching_fallback_versions' => '⏳ फ़ॉलबैक संस्करण खोज रहा है...',
    'searching_alternative_packages' => '⏳ वैकल्पिक पैकेज खोज रहा है...',
    'checking_maintainer_info' => '⏳ रखरखावकर्ता जानकारी की जाँच कर रहा है...',
    
    // Impact analysis
    'impact_analysis' => 'प्रभाव विश्लेषण: {package} को {version} में अपडेट करने से प्रभावित होगा:',
    'impact_analysis_saved' => '✅ प्रभाव विश्लेषण सहेजा गया: %s',
    'found_outdated_packages' => '%d पुराने पैकेज मिले',
];

