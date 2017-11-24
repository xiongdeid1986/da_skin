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
function varilogix_fraudcall_getConfigArray()
{
    $configarray = array( 'Enable' => array( 'Type' => 'yesno', 'Description' => "Tick to enable VariLogix Fraudcall" ), "Email Address" => array( 'Type' => 'text', 'Size' => '40', 'Description' => "Enter your registered email address here" ), 'Password' => array( 'Type' => 'text', 'Size' => '20', 'Description' => "Enter your password here" ), "Profile ID" => array( 'Type' => 'text', 'Size' => '10', 'Description' => "Enter your Fraudcall Profile ID from the Fraudcall control panel here" ) );
    return $configarray;
}
function varilogix_fraudcall_doFraudCheck($params)
{
    require(dirname(__FILE__) . "/Request.php");
    require(dirname(__FILE__) . "/Call.php");
    require(dirname(__FILE__) . "/Result.php");
    global $_LANG;
    if( !isset($_GET['call_id']) )
    {
        if( isset($_POST['pin']) )
        {
            $call = new Varilogix_Call('whmcs-92d4e0', $params["Email Address"], md5($params['Password']), intval($params["Profile ID"]));
            $call->setPin($_POST['pin']);
            $call->setProductInfo($_POST['service'], $params['amount']);
            $call->setCustomerInfo($params['clientsdetails']['firstname'] . " " . $params['clientsdetails']['lastname'], $params['clientsdetails']['email'], $params['clientsdetails']['countrycode'] . $params['clientsdetails']['phonenumber'], $params['clientsdetails']['country']);
            $result = $call->call();
            switch( $result )
            {
                case 'calling':
                    redir("a=fraudcheck&call_id=" . $call->getCode());
                    break;
                case 'pass':
                    $results['code'] = $call->getCode();
                    $results['message'] = $call->getMessage();
                    break;
                case 'fail':
                    $results['error']['title'] = $_LANG['varilogixfraudcall_title'] . " " . $_LANG['varilogixfraudcall_failed'];
                    $results['error']['description'] = "<p>" . $_LANG['varilogixfraudcall_fail'] . "</p>";
                    $results['code'] = $call->getCode();
                    $results['message'] = $call->getMessage();
                    break;
                case 'error':
                    $results['error']['title'] = $_LANG['varilogixfraudcall_title'] . " " . $_LANG['varilogixfraudcall_failed'];
                    $results['error']['description'] = "<p>" . $_LANG['varilogixfraudcall_error'] . "</p>";
                    $results['code'] = $call->getCode();
                    $results['message'] = $call->getMessage();
            }
        }
        else
        {
            $pin = Varilogix_Call::generatepin();
            $results['userinput'] = 'true';
            $results['title'] = $_LANG['varilogixfraudcall_title'];
            $results['description'] = "\n        \n<center><div id=\"pinnumber\" align=\"center\">" . $_LANG['varilogixfraudcall_pincode'] . ": " . $pin . "</div></center>\n\n<p>" . $_LANG['varilogixfraudcall_description'] . "</p>\n\n<p align=\"center\"><form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "?a=fraudcheck\">\n<input type=\"hidden\" name=\"pin\" value=\"" . $pin . "\">\n<input type=\"submit\" value=\"" . $_LANG['varilogixfraudcall_callnow'] . "\">\n</form></p>\n\n";
        }
    }
    else
    {
        $result = new Varilogix_Call_Result('whmcs-92d4e0');
        $response = $result->fetch($_GET['call_id']);
        if( !isset($_REQUEST['v_att']) || $_REQUEST['v_att'] == '' )
        {
            $_REQUEST['v_att'] = 1;
        }
        switch( $response )
        {
            case 'pass':
                $results['code'] = $result->getCode();
                $results['message'] = $result->getMessage();
                break;
            case 'fail':
                $results['error']['title'] = $_LANG['varilogixfraudcall_title'] . " " . $_LANG['varilogixfraudcall_failed'];
                $results['error']['description'] = "<p>" . $_LANG['varilogixfraudcall_fail'] . "</p>";
                $results['code'] = $result->getCode();
                $results['message'] = $result->getMessage();
                break;
            case 'error':
                $results['error']['title'] = $_LANG['varilogixfraudcall_title'] . " " . $_LANG['varilogixfraudcall_failed'];
                $results['error']['description'] = "<p>" . $_LANG['varilogixfraudcall_error'] . "</p>";
                $results['code'] = $result->getCode();
                $results['message'] = $result->getMessage();
                break;
            case 'calling':
                if( intval($_REQUEST['v_att']) <= 5 )
                {
                    sleep(15);
                }
                else
                {
                    sleep(30);
                }
                redir("a=fraudcheck&call_id=" . $_GET['call_id'] . "&v_att=" . $_REQUEST['v_att']);
        }
        break;
    }
    return $results;
    break;
}
function varilogix_fraudcall_processResultsForDisplay($params)
{
    $results = explode("\n", $params['data']);
    $descarray['code'] = "Response Code";
    $descarray['message'] = "Response Message";
    foreach( $results as $value )
    {
        $result = explode(" => ", $value);
        if( $descarray[$result[0]] != '' )
        {
            $resultarray[$descarray[$result[0]]] = $result[1];
        }
    }
    return $resultarray;
}