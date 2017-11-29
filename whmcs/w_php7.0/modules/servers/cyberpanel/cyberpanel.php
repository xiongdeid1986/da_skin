<?php
/**
 * CyberPanel whmcs module
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// include API Class
include($_SERVER['DOCUMENT_ROOT'] . "/modules/servers/cyberpanel/api.php");
logModuleCall('cyberpanel',"add",dirname( __FILE__ )  . "/api.php","sds");


function cyberpanel_MetaData()
{
    return array(
        'DisplayName' => 'CyberPanel',
        'APIVersion' => '1.0',
        'RequiresServer' => true,
        'DefaultNonSSLPort' => '8090',
        'DefaultSSLPort' => '8090',
        'ServiceSingleSignOnLabel' => 'Login as User',
        'AdminSingleSignOnLabel' => 'Login as Admin',
    );
}

function cyberpanel_ConfigOptions()
{
    return array(
        'Package Name' => array(
            'Type' => 'text',
            'Default' => 'Default',
            'Description' => 'Enter package name for this account',
        )
    );
}


function cyberpanel_CreateAccount(array $params)
{
    try {
    	// Set all parameters
        $adminUser = $params["serverusername"];
        $adminPass = $params["serverpassword"];
        $websiteOwner = $params["username"];
        $ownerPassword = $params["password"];
        $ownerEmail = $params["clientsdetails"]["email"];
        $domainName = $params["domain"];
        $adminPass = $params["serverpassword"];
        $packageName = $params['configoption1'];

        $api = new CyberApi();
        $new_account = $api->create_new_account($params, $adminUser, $adminPass, $domainName, $ownerEmail, $packageName, $websiteOwner, $ownerPassword);

        // Checking for errors
        if (!$new_account["createWebSiteStatus"]){
        	return $new_account["error_message"];
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function cyberpanel_SuspendAccount(array $params)
{
    try {
        // Set all parameters
        $adminUser = $params["serverusername"];
        $adminPass = $params["serverpassword"];
        $domainName = $params["domain"];
        $status = "Suspend";

        $api = new CyberApi();
        $account = $api->change_account_status($params, $adminUser, $adminPass, $domainName, $status);

        // Checking for errors
        if (!$account["websiteStatus"]){
        	return $account["error_message"];
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function cyberpanel_UnsuspendAccount(array $params)
{
    try {
        // Set all parameters
        $adminUser = $params["serverusername"];
        $adminPass = $params["serverpassword"];
        $domainName = $params["domain"];
        $status = "Unsuspend";

        $api = new CyberApi();
        $account = $api->change_account_status($params, $adminUser, $adminPass, $domainName, $status);

        // Checking for errors
        if (!$account["websiteStatus"]){
        	return $account["error_message"];
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}
function cyberpanel_TerminateAccount(array $params)
{
    try {
        // Set all parameters
        $adminUser = $params["serverusername"];
        $adminPass = $params["serverpassword"];
        $domainName = $params["domain"];

        $api = new CyberApi();
        $del_account = $api->terminate_account($params, $adminUser, $adminPass, $domainName);

        // Checking for errors
        if (!$del_account["websiteDeleteStatus"]){
        	return $del_account["error_message"];
        }        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function cyberpanel_ChangePassword(array $params)
{
    try {
        // Set all parameters
        $adminUser = $params["serverusername"];
        $adminPass = $params["serverpassword"];
        $websiteOwner = $params["username"];
        $ownerPassword = $params["password"];

        $api = new CyberApi();
        $account = $api->change_account_password($params, $adminUser, $adminPass, $websiteOwner, $ownerPassword);

        // Checking for errors
        if (!$account["changeStatus"]){
        	return $account["error_message"];
        }        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}


function cyberpanel_ChangePackage(array $params)
{
    try {
        // Set all parameters
        $adminUser = $params["serverusername"];
        $adminPass = $params["serverpassword"];
        $domainName = $params["domain"];
        $packageName = $params['configoption1'];

        $api = new CyberApi();
        $account = $api->change_account_package($params, $adminUser, $adminPass, $domainName, $packageName);

        // Checking for errors
        if (!$account["changePackage"]){
        	return $account["error_message"];
        }        
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        return $e->getMessage();
    }

    return 'success';
}

function cyberpanel_TestConnection(array $params)
{
    try {
        $adminUser = $params["serverusername"];
        $adminPass = $params["serverpassword"];

        $api = new CyberApi();
        $test_conn = $api->verify_connection($params, $adminUser, $adminPass);

        // Checking for errors
        $errorMsg = '';
        if (!$test_conn["verifyConn"]){
        	$errorMsg =  $test_conn["error_message"];
        	$success = false;
        }
        else {
        	$success = true;
        	$errorMsg = '';
        }
    } catch (Exception $e) {
        // Record the error in WHMCS's module log.
        logModuleCall(
            'cyberpanel',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );

        $success = false;
        $errorMsg = $e->getMessage();
    }

    return array(
        'success' => $success,
        'error' => $errorMsg,
    );
}


function cyberpanel_ClientArea($params) {

    $loginform = '<form class="cyberpanel" action="' . (($params["serversecure"]) ? "https" : "http") . '://'.$params["serverhostname"].':8090/api/loginAPI" method="post" target="_blank">
<input type="hidden" name="username" value="'.$params["username"].'" />
<input type="hidden" name="password" value="'.$params["password"].'" />
<input type="hidden" name="languageSelection" value="Chinese" />
<input type="submit" value="Login to Control Panel" />
</form>';
    return $loginform;

}

function cyberpanel_AdminLink($params) {

    $loginform = '<form class="cyberpanel" action="' . (($params["serversecure"]) ? "https" : "http") . '://'.$params["serverhostname"].':8090/api/loginAPI" method="post" target="_blank">
<input type="hidden" name="username" value="'.$params["serverusername"].'" />
<input type="hidden" name="password" value="'.$params["serverpassword"].'" />
<input type="hidden" name="languageSelection" value="Chinese" />
<input type="submit" value="Login to Control Panel" />
</form>';
    return $loginform;

}


function vesta_LoginLink($params) {
    echo '<a href="'.(($params["serversecure"]) ? "https" : "http") . '://'.$params["serverhostname"]. ':8090">Control Panel</a>';
}