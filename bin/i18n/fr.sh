#!/bin/bash
# French translations
#
# This file contains French translations for bash scripts
# Format: declare -A TRANSLATIONS_FR=([key]='value' ...)

declare -A TRANSLATIONS_FR=(
    # Main messages
    ['loading_config']='Chargement de la configuration...'
    ['checking_outdated']='Vérification des paquets obsolètes...'
    ['processing']='Traitement des paquets...'
    ['processing_php']='Traitement des paquets avec script PHP...'
    ['running']='Exécution...'
    ['update_completed']='Mise à jour terminée.'
    ['no_outdated']='Aucune dépendance directe obsolète.'

    # Configuration
    ['found_config']='Fichier de configuration trouvé: '
    ['no_config']='Aucun fichier de configuration trouvé (utilisation des valeurs par défaut)'

    # Errors
    ['composer_not_found']='Composer n\'est pas installé ou n\'est pas dans le PATH.'
    ['composer_json_not_found']='composer.json introuvable dans le répertoire actuel.'
    ['processor_not_found']='Impossible de trouver process-updates.php dans vendor ou le répertoire des scripts.'
    ['please_install']='Exécuter: composer install'
    ['unknown_option']='Option inconnue:'
    ['use_help']='Utiliser --help ou -h pour les informations d\'utilisation.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Répertoire actuel:'
    ['debug_searching_config']='Recherche de fichiers de configuration:'
    ['debug_composer_executed']='Commande composer outdated exécutée'
    ['debug_json_length']='Longueur de OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated a renvoyé un JSON vide'
    ['debug_passing_to_php']='Passage au script PHP:'
    ['debug_output_length']='Longueur de la sortie du script PHP:'
    ['debug_processor_found']='Processeur PHP trouvé dans:'
)

