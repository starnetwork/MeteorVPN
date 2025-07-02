<?php

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

function meteorvpn_ClientArea(array $params)
{
    $username = $params['username'];
    $vars     = [];
    $vars['LANG'] = meteorvpn_getLanguage();
    // Handle desktop‑enrollment POST
    if (isset($_POST['meteorvpn_enroll'])) {
        try {
            $rsp = meteorvpn_callAPI('POST', "/api/v1/user/{$username}/start_desktop", ['email'=>$params['clientsdetails']['email'],'send_enrollment_notification'=>false], $params);
            $vars['enrollment_token'] = $rsp['enrollment_token'];
            $vars['enrollment_url']   = $rsp['enrollment_url'];
        } catch (Exception $e) {
            $vars['error'] = $e->getMessage();
        }
    }

    // Fetch basic status for display (non‑blocking if already have error)
    if (!isset($vars['error'])) {
        try {
            $info = meteorvpn_callAPI('GET', "/api/v1/user/{$username}", null, $params);
            $vars['is_active'] = $info['user']['is_active'];
            $vars['enrolled']  = $info['user']['enrolled'];
        } catch (Exception $e) {
            $vars['error'] = $e->getMessage();
        }
    }

    return [
        'tabOverviewReplacementTemplate' => 'templates/overview.tpl',
        'templateVariables' => $vars,
    ];
}
