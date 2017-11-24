<?php //00e57
// *************************************************************************
// *                                                                       *
// * WHMCS - The Complete Client Management, Billing & Support Solution    *
// * Copyright (c) WHMCS Ltd. All Rights Reserved,                         *
// * Version: 5.3.14 (5.3.14-release.1)                                    *
// * BuildId: 0866bd1.62                                                   *
// * Build Date: 28 May 2015                                               *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * Email: info@whmcs.com                                                 *
// * Website: http://www.whmcs.com                                         *
// *                                                                       *
// *************************************************************************
// *                                                                       *
// * This software is furnished under a license and may be used and copied *
// * only  in  accordance  with  the  terms  of such  license and with the *
// * inclusion of the above copyright notice.  This software  or any other *
// * copies thereof may not be provided or otherwise made available to any *
// * other person.  No title to and  ownership of the  software is  hereby *
// * transferred.                                                          *
// *                                                                       *
// * You may not reverse  engineer, decompile, defeat  license  encryption *
// * mechanisms, or  disassemble this software product or software product *
// * license.  WHMCompleteSolution may terminate this license if you don't *
// * comply with any of the terms and conditions set forth in our end user *
// * license agreement (EULA).  In such event,  licensee  agrees to return *
// * licensor  or destroy  all copies of software  upon termination of the *
// * license.                                                              *
// *                                                                       *
// * Please see the EULA file for the full End User License Agreement.     *
// *                                                                       *
// *************************************************************************
function resellercentral_ConfigOptions()
{
    $configarray = array( "API Key" => array( 'Type' => 'text', 'Size' => '60' ), "Package Name" => array( 'Type' => 'text', 'Size' => '20' ), 'Location' => array( 'Type' => 'dropdown', 'Options' => 'US-EAST,US-CENTRAL,US-WEST,UK,ASIA,US-CLOUD' ), 'Platform' => array( 'Type' => 'dropdown', 'Options' => 'Linux,Windows' ) );
    return $configarray;
}
function resellercentral_CreateAccount($params)
{
    $location = $params['configoption3'];
    if( $params['customfields']["Website Location"] )
    {
        $location = $params['customfields']["Website Location"];
    }
    if( $location == "Chicago (USA)" )
    {
        $location = 4;
    }
    else
    {
        if( $location == "Georgia (USA)" )
        {
            $location = 4;
        }
        else
        {
            if( $location == "Texas (USA)" )
            {
                $location = 4;
            }
            else
            {
                if( $location == "Berkshire (UK)" )
                {
                    $location = 5;
                }
                else
                {
                    if( $location == "Washington DC (USA)" )
                    {
                        $location = 6;
                    }
                    else
                    {
                        if( $location == "New York (USA)" )
                        {
                            $location = 6;
                        }
                        else
                        {
                            if( $location == "California (USA)" )
                            {
                                $location = 8;
                            }
                            else
                            {
                                if( $location == "Singapore (ASIA)" )
                                {
                                    $location = 10;
                                }
                                else
                                {
                                    if( $location == 'US-EAST' )
                                    {
                                        $location = 6;
                                    }
                                    else
                                    {
                                        if( $location == 'US-CENTRAL' )
                                        {
                                            $location = 4;
                                        }
                                        else
                                        {
                                            if( $location == 'US-WEST' )
                                            {
                                                $location = 8;
                                            }
                                            else
                                            {
                                                if( $location == 'UK' )
                                                {
                                                    $location = 5;
                                                }
                                                else
                                                {
                                                    if( $location == 'ASIA' )
                                                    {
                                                        $location = 10;
                                                    }
                                                    else
                                                    {
                                                        if( $location == 'US-CLOUD' )
                                                        {
                                                            $location = 9;
                                                        }
                                                        else
                                                        {
                                                            return "No Matching Location Found";
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $fields = array( 'action' => 'create_account', 'api_key' => $params['configoption1'], 'domain' => $params['domain'], 'username' => $params['username'], 'password' => $params['password'], 'email' => $params['clientsdetails']['email'], 'location' => $location, 'package' => $params['configoption2'] );
    if( $params['configoption4'] == 'Windows' )
    {
        $fields['platform'] = '2';
    }
    $result = resellercentral_req($fields, $params['packageid'], $params['accountid']);
    return $result;
}
function resellercentral_SuspendAccount($params)
{
    $fields = array( 'action' => 'suspend_account', 'api_key' => $params['configoption1'], 'domain' => $params['domain'] );
    $result = resellercentral_req($fields);
    return $result;
}
function resellercentral_UnsuspendAccount($params)
{
    $fields = array( 'action' => 'unsuspend_account', 'api_key' => $params['configoption1'], 'domain' => $params['domain'] );
    $result = resellercentral_req($fields);
    return $result;
}
function resellercentral_req($fields, $packageid = '', $accountid = '')
{
    $action = $fields['action'];
    if( $action == 'create_account' )
    {
        $creatingaccount = true;
    }
    $url = "http://cp.hostnine.com/api/" . $action . ".php?";
    unset($fields['action']);
    $fieldstring = '';
    foreach( $fields as $key => $value )
    {
        $url .= $key . "=" . urlencode($value) . "&";
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 200);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $data = curl_exec($ch);
    if( curl_errno($ch) )
    {
        $data = curl_errno($ch) . " - " . curl_error($ch);
    }
    curl_close($ch);
    if( !$data )
    {
        $data = "No Response from API";
    }
    logModuleCall('resellercentral', $action, $fields, $data);
    if( strpos($data, 'SUCCESS') == true || strpos($data, "account has been suspended") == true || strpos($data, "account is now active") == true )
    {
        if( $creatingaccount )
        {
            $result = select_query('tblcustomfields', 'id', array( 'type' => 'product', 'relid' => (int) $packageid, 'fieldname' => "IP Address" ));
            $data2 = mysql_fetch_array($result);
            $customfieldid = $data2['id'];
            $tempdata = explode("&", $data);
            $tempdata = explode("=", $tempdata[1]);
            $tempdata = explode("<", $tempdata[1]);
            $ipaddress = $tempdata[0];
            delete_query('tblcustomfieldsvalues', array( 'fieldid' => $customfieldid, 'relid' => $accountid ));
            insert_query('tblcustomfieldsvalues', array( 'fieldid' => $customfieldid, 'relid' => $accountid, 'value' => $ipaddress ));
        }
        $result = 'success';
    }
    else
    {
        if( strpos($data, "Account Already Suspended") == true )
        {
            $result = "Account Already Suspended";
        }
        else
        {
            if( strpos($data, "a DNS entry for") == true )
            {
                $result = "An account already exists for this domain name";
            }
            else
            {
                $result = $data;
            }
        }
    }
    return $result;
}