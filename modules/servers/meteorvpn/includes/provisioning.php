<?php
/* -----------------------------------------------------------------------------
 |  WHMCS provisioning functions
 |-----------------------------------------------------------------------------*/
/**
 * CreateAccount: called when an order is accepted.
 */
function meteorvpn_CreateAccount(array $params)
{
    try {
        // Get gateway groups
        $groups = [];

        foreach ($params['customfields'] as $key => $value) {
            if (preg_match('/^group-(.+)$/', $key, $matches)) {
                $suffix = $matches[1]; // This is the part after 'group-'
                if (!empty($value)) {
                    $groups[] = $suffix; // Enabled group
                }
            }
        }
        
        $userData = [
            'username'   => $params['username'],
            'last_name'  => $params['clientsdetails']['lastname'],
            'first_name' => $params['clientsdetails']['firstname'],
            'email'      => $params['clientsdetails']['email'],
            'password'   => $params['password'] ?: null, // Optional – MeteorVPN can email enrollment
            //'phone'      => $params['clientsdetails']['phonenumber'] ?: null,
        ];

        meteorvpn_validateUserData($userData);

        // Remove null values to satisfy strict schema
        $payload = array_filter($userData, fn($v) => $v !== null);
        // Create the user
        meteorvpn_callAPI('POST', '/api/v1/user', $payload, $params);

        // Add user to selected groups
        foreach ($groups as $groupName) {
            meteorvpn_callAPI('POST', '/api/v1/group/' . $groupName, ['username'=>$params['username']], $params);
        }

        // If no group selected add to default group
        if(empty($groups) && !empty($params['configoption5'])){
            meteorvpn_callAPI('POST', '/api/v1/group/' . $params['configoption5'], ['username'=>$params['username']], $params);
        }
    } catch (Exception $e) {
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

function meteorvpn_SuspendAccount(array $params)
{
    try {
        $username = $params['username'];
        $details  = meteorvpn_callAPI('GET', "/api/v1/user/{$username}", null, $params);
        if (!$details['user']['is_active']) {
            return 'success'; // Already disabled
        }
        $details['user']['is_active'] = false;
        meteorvpn_callAPI('PUT', "/api/v1/user/{$username}", $details['user'], $params);
        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'meteorvpn',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}

function meteorvpn_UnsuspendAccount(array $params)
{
    try {
        $username = $params['username'];
        $details  = meteorvpn_callAPI('GET', "/api/v1/user/{$username}", null, $params);
        if ($details['user']['is_active']) {
            return 'success';
        }
        $details['user']['is_active'] = true;
        meteorvpn_callAPI('PUT', "/api/v1/user/{$username}", $details['user'], $params);
        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'meteorvpn',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}

function meteorvpn_TerminateAccount(array $params)
{
    try {
        $username = $params['username'];
        meteorvpn_callAPI('DELETE', "/api/v1/user/{$username}", null, $params);
        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'meteorvpn',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}

function meteorvpn_ChangePassword(array $params)
{
    try {
        $username   = $params['username'];
        $newPass    = $params['password'];
        if (strlen($newPass) < 8) {
            throw new Exception('Password must be at least 8 characters');
        }
        meteorvpn_callAPI('PUT', "/api/v1/user/{$username}/password", ['new_password' => $newPass], $params);
        return 'success';
    } catch (Exception $e) {
        logModuleCall(
            'meteorvpn',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}