<?php
/**
 * Greek translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Δεν υπάρχουν πακέτα προς ενημέρωση',
    'all_up_to_date' => 'όλα τα πακέτα είναι ενημερωμένα',
    'all_have_conflicts' => 'όλα τα ξεπερασμένα πακέτα έχουν συγκρούσεις εξαρτήσεων',
    'all_ignored' => 'όλα τα ξεπερασμένα πακέτα αγνοούνται',
    'all_ignored_or_conflicts' => 'όλα τα ξεπερασμένα πακέτα αγνοούνται ή έχουν συγκρούσεις εξαρτήσεων',
    
    // Commands
    'suggested_commands' => 'Προτεινόμενες εντολές:',
    'suggested_commands_conflicts' => 'Προτεινόμενες εντολές για επίλυση συγκρούσεων εξαρτήσεων:',
    'suggested_commands_grouped' => 'Προτεινόμενες εντολές (δοκιμάστε να εγκαταστήσετε μαζί - το Composer μπορεί να λύσει καλύτερα τις συγκρούσεις):',
    'grouped_install_explanation' => '(Η εγκατάσταση πολλών πακέτων μαζί βοηθά μερικές φορές το Composer να λύσει συγκρούσεις)',
    'includes_transitive' => '(Περιλαμβάνει μεταβατικές εξαρτήσεις απαραίτητες για επίλυση συγκρούσεων)',
    'update_transitive_first' => '(Ενημερώστε πρώτα αυτές τις μεταβατικές εξαρτήσεις, στη συνέχεια δοκιμάστε ξανά να ενημερώσετε τα φιλτραρισμένα πακέτα)',
    
    // Framework and packages
    'detected_framework' => 'Εντοπισμένοι περιορισμοί πλαισίου:',
    'ignored_packages_prod' => 'Αγνοημένα πακέτα (prod):',
    'ignored_packages_dev' => 'Αγνοημένα πακέτα (dev):',
    'dependency_analysis' => 'Ανάλυση ελέγχου εξαρτήσεων:',
    'all_outdated_before' => 'Όλα τα ξεπερασμένα πακέτα (πριν από τον έλεγχο εξαρτήσεων):',
    'filtered_by_conflicts' => 'Φιλτραρισμένα κατά συγκρούσεις εξαρτήσεων:',
    'suggested_transitive' => 'Προτεινόμενες ενημερώσεις μεταβατικών εξαρτήσεων για επίλυση συγκρούσεων:',
    'no_compatible_dependent_versions' => 'Δεν βρέθηκαν συμβατές εκδόσεις εξαρτημένων πακέτων:',
    'no_compatible_version_explanation' => '     - {depPackage}: Δεν βρέθηκε έκδοση που υποστηρίζει {requiredBy}',
    'latest_checked_constraint' => '       (Η τελευταία ελεγμένη έκδοση απαιτεί: {constraint})',
    'all_versions_require' => '       (Όλες οι διαθέσιμες εκδόσεις απαιτούν: {constraint})',
    'packages_passed_check' => 'Πακέτα που πέρασαν τον έλεγχο εξαρτήσεων:',
    'none' => '(κανένα)',
    'conflicts_with' => 'σύγκρουση με:',
    'package_abandoned' => 'Το πακέτο έχει εγκαταλειφθεί',
    'abandoned_packages_section' => 'Βρέθηκαν εγκαταλελειμμένα πακέτα:',
    'all_installed_abandoned_section' => 'Όλα τα εγκατεστημένα εγκαταλελειμμένα πακέτα:',
    'replaced_by' => 'αντικαταστάθηκε από: %s',
    'alternative_solutions' => 'Εναλλακτικές λύσεις:',
    'compatible_with_conflicts' => 'συμβατό με συγκρουόμενες εξαρτήσεις',
    'alternative_packages' => 'Εναλλακτικά πακέτα:',
    'recommended_replacement' => 'συνιστώμενη αντικατάσταση',
    'similar_functionality' => 'παρόμοια λειτουργικότητα',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Total outdated packages: %d',
    'debug_require_packages' => 'require packages: %d',
    'debug_require_dev_packages' => 'require-dev packages: %d',
    'debug_detected_symfony' => 'Detected Symfony constraint: %s (from extra.symfony.require)',
    'debug_processing_package' => 'Processing package: %s (installed: %s, latest: %s)',
    'debug_action_ignored' => 'Action: IGNORED (in ignore list and not in include list)',
    'debug_action_skipped' => 'Action: SKIPPED (no compatible version found due to dependency constraints)',
    'debug_action_added' => 'Action: ADDED to %s dependencies: %s',
    'debug_no_compatible_version' => 'No compatible version found for %s (proposed: %s)',
    
    // Release info
    'release_info' => 'Πληροφορίες Έκδοσης',
    'release_changelog' => 'Αρχείο Αλλαγών',
    'release_view_on_github' => 'Προβολή στο GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Έλεγχος συγκρούσεων εξαρτήσεων...',
    'checking_abandoned_packages' => '⏳ Έλεγχος εγκαταλελειμμένων πακέτων...',
    'checking_all_abandoned_packages' => '⏳ Έλεγχος όλων των εγκατεστημένων πακέτων για εγκαταλελειμμένη κατάσταση...',
    'searching_fallback_versions' => '⏳ Αναζήτηση εφεδρικών εκδόσεων...',
    'searching_alternative_packages' => '⏳ Αναζήτηση εναλλακτικών πακέτων...',
    'checking_maintainer_info' => '⏳ Έλεγχος πληροφοριών συντηρητή...',
    
    // Impact analysis
    'impact_analysis' => 'Ανάλυση αντίκτυπου: Η ενημέρωση του {package} στην {version} θα επηρέαζε:',
    'impact_analysis_saved' => '✅ Ανάλυση αντίκτυπου αποθηκεύτηκε σε: %s',
    'found_outdated_packages' => 'Βρέθηκαν %d ξεπερασμένα πακέτα',
];

