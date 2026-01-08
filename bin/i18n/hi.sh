#!/bin/bash
# Hindi translations
#
# This file contains Hindi translations for bash scripts
# Format: declare -A TRANSLATIONS_HI=([key]='value' ...)

declare -A TRANSLATIONS_HI=(
    # Main messages
    ['loading_config']='कॉन्फ़िगरेशन लोड हो रहा है...'
    ['checking_outdated']='पुराने पैकेजों की जांच हो रही है...'
    ['processing']='पैकेज प्रसंस्करण...'
    ['processing_php']='PHP स्क्रिप्ट के साथ पैकेज प्रसंस्करण...'
    ['running']='चल रहा है...'
    ['update_completed']='अपडेट पूर्ण.'
    ['no_outdated']='कोई पुरानी प्रत्यक्ष निर्भरताएं नहीं.'

    # Configuration
    ['found_config']='कॉन्फ़िगरेशन फ़ाइल मिली: '
    ['no_config']='कॉन्फ़िगरेशन फ़ाइल नहीं मिली (डिफ़ॉल्ट मानों का उपयोग)'

    # Errors
    ['composer_not_found']='Composer स्थापित नहीं है या PATH में नहीं है.'
    ['composer_json_not_found']='वर्तमान निर्देशिका में composer.json नहीं मिला.'
    ['processor_not_found']='vendor या स्क्रिप्ट निर्देशिका में process-updates.php नहीं मिल सका.'
    ['please_install']='चलाएं: composer install'
    ['unknown_option']='अज्ञात विकल्प:'
    ['use_help']='उपयोग जानकारी के लिए --help या -h का उपयोग करें.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='वर्तमान निर्देशिका:'
    ['debug_searching_config']='कॉन्फ़िगरेशन फ़ाइलें खोज रहा है:'
    ['debug_composer_executed']='composer outdated कमांड निष्पादित'
    ['debug_json_length']='OUTDATED_JSON लंबाई:'
    ['debug_empty_json']='Composer outdated ने खाली JSON लौटाया'
    ['debug_passing_to_php']='PHP स्क्रिप्ट को पास कर रहा है:'
    ['debug_output_length']='PHP स्क्रिप्ट आउटपुट लंबाई:'
    ['debug_processor_found']='PHP प्रोसेसर मिला:'
)

