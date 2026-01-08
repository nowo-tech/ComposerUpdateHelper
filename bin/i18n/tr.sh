#!/bin/bash
# Turkish translations
#
# This file contains Turkish translations for bash scripts
# Format: declare -A TRANSLATIONS_TR=([key]='value' ...)

declare -A TRANSLATIONS_TR=(
    # Main messages
    ['loading_config']='Yapılandırma yükleniyor...'
    ['checking_outdated']='Eski paketler kontrol ediliyor...'
    ['processing']='Paketler işleniyor...'
    ['processing_php']='Paketler PHP betiği ile işleniyor...'
    ['running']='Çalıştırılıyor...'
    ['update_completed']='Güncelleme tamamlandı.'
    ['no_outdated']='Eski doğrudan bağımlılık yok.'

    # Configuration
    ['found_config']='Yapılandırma dosyası bulundu: '
    ['no_config']='Yapılandırma dosyası bulunamadı (varsayılanlar kullanılıyor)'

    # Errors
    ['composer_not_found']='Composer yüklü değil veya PATH\'de değil.'
    ['composer_json_not_found']='Mevcut dizinde composer.json bulunamadı.'
    ['processor_not_found']='vendor veya betik dizininde process-updates.php bulunamadı.'
    ['please_install']='Çalıştırın: composer install'
    ['unknown_option']='Bilinmeyen seçenek:'
    ['use_help']='Kullanım bilgisi için --help veya -h kullanın.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Mevcut dizin:'
    ['debug_searching_config']='Yapılandırma dosyaları aranıyor:'
    ['debug_composer_executed']='Composer outdated komutu çalıştırıldı'
    ['debug_json_length']='OUTDATED_JSON uzunluğu:'
    ['debug_empty_json']='Composer outdated boş JSON döndürdü'
    ['debug_passing_to_php']='PHP betiğine aktarılıyor:'
    ['debug_output_length']='PHP betik çıktı uzunluğu:'
    ['debug_processor_found']='PHP işlemci bulundu:'
)

