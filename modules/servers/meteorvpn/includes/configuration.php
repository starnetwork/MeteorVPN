<?php

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/* -----------------------------------------------------------------------------
 |  Module meta‑data & configuration options
 |-----------------------------------------------------------------------------*/
function meteorvpn_MetaData()
{
    return [
        'DisplayName'   => 'MeteorVPN Provisioning',
        'APIVersion'    => '1.1',
        'RequiresServer' => false,
    ];
}

/*
 * Configuration fields
 */
function meteorvpn_ConfigOptions()
{
    return [
        'API Base URL' => [
            'Type'        => 'text',
            'Size'        => '50',
            'Default'     => 'https://meteorvpn.example.com',
        ],
        'API Token' => [
            'Type'        => 'password',
            'Size'        => '50',
        ],
        'Verify TLS' => [
            'Type'        => 'yesno',
            'Default'     => 'on',
        ],
        'Debug' => [
            'Type'        => 'yesno',
            'Default'     => 'on',
        ],
        'Default Group Name' => [
            'Type'        => 'text',
            'Size'        => '50',
        ],
    ];
}