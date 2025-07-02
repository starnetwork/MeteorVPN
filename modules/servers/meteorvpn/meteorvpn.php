<?php
/**
 * MeteorVPN WHMCS Provisioning Module
 * Version: 1.0.0
 *
 * This module allows WHMCS to provision, suspend, unsuspend, terminate accounts
 * and change user passwords in a Defguard instance using its public REST API.
 */


if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

// Set language
function meteorvpn_getLanguage()
{
    $currentLanguage = Lang::getName();
    $languageFile = __DIR__ . "/lang/english.php";
    if (file_exists(__DIR__ . "/lang/{$currentLanguage}.php")) {
        $languageFile = __DIR__ . "/lang/{$currentLanguage}.php";
    }
    include $languageFile;
    return $_LANG['meteorvpn'];
}

include __DIR__ . '/includes/configuration.php';
include __DIR__ . '/includes/admin.php';
include __DIR__ . '/includes/client.php';
include __DIR__ . '/includes/api.php';
include __DIR__ . '/includes/provisioning.php';



