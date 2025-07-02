<?php

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

function meteorvpn_AdminServicesTabFields(array $params)
{
    try {
        $info = meteorvpn_callAPI('GET', "/api/v1/user/" . $params['username'], null, $params);
        $data = '<pre> ' .print_r($info['devices'][0]['networks'], true). '</pre>';
        if(!$info['devices'][0]['networks']) $data = 'No devices found.';
        
        return array(
            'Data:' => $data,
        );
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'meteorvpn',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
    }

    return array();
}

function meteorvpn_AdminServicesTabFieldsSave(array $params)
{
    try {
        // Get gateway groups enabled for the client
        $groups = [];

        foreach ($params['customfields'] as $key => $value) {
            if (preg_match('/^group-(.+)$/', $key, $matches)) {
                $suffix = $matches[1]; // This is the part after 'group-'
                if (!empty($value)) {
                    $groups[] = $suffix; // Enabled group
                }
            }
        }

        // Get current groups info from API
        $info = meteorvpn_callAPI('GET', "/api/v1/group-info", null, $params);

        foreach($info as $group)
        {
            if($group['is_admin']) continue;
            
            if(in_array($params['username'], $group['members']))
            {
                if(in_array($group['name'], $groups)) continue; // Already in the selected group, do nothing.
                else {
                    // Exists in the group and needs to be removed.
                    meteorvpn_callAPI('DELETE', '/api/v1/group/' . $group['name'] . '/user/' . $params['username'], null, $params);
                } 
            } else {
                if(in_array($group['name'], $groups)) {
                    // User is not in the group and needs to be added.
                    meteorvpn_callAPI('POST', '/api/v1/group/' . $group['name'], ['username'=>$params['username']], $params);
                }
            }
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'meteorvpn',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}