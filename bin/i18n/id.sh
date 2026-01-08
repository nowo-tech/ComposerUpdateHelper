#!/bin/bash
# Indonesian translations
#
# This file contains Indonesian translations for bash scripts
# Format: declare -A TRANSLATIONS_ID=([key]='value' ...)

declare -A TRANSLATIONS_ID=(
    # Main messages
    ['loading_config']='Memuat konfigurasi...'
    ['checking_outdated']='Memeriksa paket usang...'
    ['processing']='Memproses paket...'
    ['processing_php']='Memproses paket dengan skrip PHP...'
    ['running']='Menjalankan...'
    ['update_completed']='Pembaruan selesai.'
    ['no_outdated']='Tidak ada dependensi langsung yang usang.'

    # Configuration
    ['found_config']='File konfigurasi ditemukan: '
    ['no_config']='File konfigurasi tidak ditemukan (menggunakan nilai default)'

    # Errors
    ['composer_not_found']='Composer tidak terpasang atau tidak ada di PATH.'
    ['composer_json_not_found']='composer.json tidak ditemukan di direktori saat ini.'
    ['processor_not_found']='Tidak dapat menemukan process-updates.php di vendor atau direktori skrip.'
    ['please_install']='Jalankan: composer install'
    ['unknown_option']='Opsi tidak dikenal:'
    ['use_help']='Gunakan --help atau -h untuk informasi penggunaan.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Direktori saat ini:'
    ['debug_searching_config']='Mencari file konfigurasi:'
    ['debug_composer_executed']='Perintah composer outdated dieksekusi'
    ['debug_json_length']='Panjang OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated mengembalikan JSON kosong'
    ['debug_passing_to_php']='Meneruskan ke skrip PHP:'
    ['debug_output_length']='Panjang keluaran skrip PHP:'
    ['debug_processor_found']='Prosesor PHP ditemukan di:'
)

