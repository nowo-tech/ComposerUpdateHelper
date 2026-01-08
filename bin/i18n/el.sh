#!/bin/bash
# Greek translations
#
# This file contains Greek translations for bash scripts
# Format: declare -A TRANSLATIONS_EL=([key]='value' ...)

declare -A TRANSLATIONS_EL=(
    # Main messages
    ['loading_config']='Φόρτωση διαμόρφωσης...'
    ['checking_outdated']='Έλεγχος ξεπερασμένων πακέτων...'
    ['processing']='Επεξεργασία πακέτων...'
    ['processing_php']='Επεξεργασία πακέτων με σενάριο PHP...'
    ['running']='Εκτέλεση...'
    ['update_completed']='Ενημέρωση ολοκληρώθηκε.'
    ['no_outdated']='Δεν υπάρχουν ξεπερασμένες άμεσες εξαρτήσεις.'

    # Configuration
    ['found_config']='Βρέθηκε αρχείο διαμόρφωσης: '
    ['no_config']='Δεν βρέθηκε αρχείο διαμόρφωσης (χρήση προεπιλεγμένων τιμών)'

    # Errors
    ['composer_not_found']='Το Composer δεν είναι εγκατεστημένο ή δεν βρίσκεται στο PATH.'
    ['composer_json_not_found']='Το composer.json δεν βρέθηκε στον τρέχοντα κατάλογο.'
    ['processor_not_found']='Δεν ήταν δυνατή η εύρεση του process-updates.php στο vendor ή στον κατάλογο σεναρίων.'
    ['please_install']='Εκτελέστε: composer install'
    ['unknown_option']='Άγνωστη επιλογή:'
    ['use_help']='Χρησιμοποιήστε --help ή -h για πληροφορίες χρήσης.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Τρέχων κατάλογος:'
    ['debug_searching_config']='Αναζήτηση αρχείων διαμόρφωσης:'
    ['debug_composer_executed']='Εκτελέστηκε η εντολή composer outdated'
    ['debug_json_length']='Μήκος OUTDATED_JSON:'
    ['debug_empty_json']='Το composer outdated επέστρεψε κενό JSON'
    ['debug_passing_to_php']='Μετάδοση στο σενάριο PHP:'
    ['debug_output_length']='Μήκος εξόδου σεναρίου PHP:'
    ['debug_processor_found']='Επεξεργαστής PHP βρέθηκε σε:'
)

