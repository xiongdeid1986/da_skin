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
if( !defined('WHMCS') )
{
    exit( "This file cannot be accessed directly" );
}
if( !function_exists('generateClientPW') )
{
    require(ROOTDIR . "/includes/clientfunctions.php");
}
$result = select_query('tblcontacts', 'id,subaccount', array( 'id' => $contactid ));
$data = mysql_fetch_array($result);
$subaccount = $data['subaccount'];
if( !$data[0] )
{
    $apiresults = array( 'result' => 'error', 'message' => "Contact ID Not Found" );
}
else
{
    if( ($subaccount || $_REQUEST['subaccount']) && $_REQUEST['email'] )
    {
        $result = select_query('tblclients', 'id', array( 'email' => $_REQUEST['email'] ));
        $data = mysql_fetch_array($result);
        $result = select_query('tblcontacts', 'id', array( 'email' => $_REQUEST['email'], 'subaccount' => '1', 'id' => array( 'sqltype' => 'NEQ', 'value' => $contactid ) ));
        $data2 = mysql_fetch_array($result);
        if( $data['id'] || $data2['id'] )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Duplicate Email Address" );
            return NULL;
        }
    }
    if( $generalemails )
    {
        $generalemails = '1';
    }
    if( $productemails )
    {
        $productemails = '1';
    }
    if( $domainemails )
    {
        $domainemails = '1';
    }
    if( $invoiceemails )
    {
        $invoiceemails = '1';
    }
    if( $supportemails )
    {
        $supportemails = '1';
    }
    $updateqry = array(  );
    if( isset($_REQUEST['firstname']) )
    {
        $updateqry['firstname'] = $firstname;
    }
    if( isset($_REQUEST['lastname']) )
    {
        $updateqry['lastname'] = $lastname;
    }
    if( isset($_REQUEST['companyname']) )
    {
        $updateqry['companyname'] = $companyname;
    }
    if( isset($_REQUEST['email']) )
    {
        $updateqry['email'] = $email;
    }
    if( isset($_REQUEST['address1']) )
    {
        $updateqry['address1'] = $address1;
    }
    if( isset($_REQUEST['address2']) )
    {
        $updateqry['address2'] = $address2;
    }
    if( isset($_REQUEST['city']) )
    {
        $updateqry['city'] = $city;
    }
    if( isset($_REQUEST['state']) )
    {
        $updateqry['state'] = $state;
    }
    if( isset($_REQUEST['postcode']) )
    {
        $updateqry['postcode'] = $postcode;
    }
    if( isset($_REQUEST['country']) )
    {
        $updateqry['country'] = $country;
    }
    if( isset($_REQUEST['phonenumber']) )
    {
        $updateqry['phonenumber'] = $phonenumber;
    }
    if( isset($_REQUEST['subaccount']) )
    {
        $whmcs = WHMCS_Application::getinstance();
        $updateqry['subaccount'] = $whmcs->get_req_var('subaccount');
    }
    if( isset($_REQUEST['password2']) )
    {
        $updateqry['password'] = generateClientPW($password2);
    }
    if( isset($_REQUEST['permissions']) )
    {
        $updateqry['permissions'] = $permissions;
    }
    if( isset($_REQUEST['generalemails']) )
    {
        $updateqry['generalemails'] = $generalemails;
    }
    if( isset($_REQUEST['productemails']) )
    {
        $updateqry['productemails'] = $productemails;
    }
    if( isset($_REQUEST['domainemails']) )
    {
        $updateqry['domainemails'] = $domainemails;
    }
    if( isset($_REQUEST['invoiceemails']) )
    {
        $updateqry['invoiceemails'] = $invoiceemails;
    }
    if( isset($_REQUEST['supportemails']) )
    {
        $updateqry['supportemails'] = $supportemails;
    }
    update_query('tblcontacts', $updateqry, array( 'id' => $contactid ));
    $apiresults = array( 'result' => 'success', 'contactid' => $contactid );
}