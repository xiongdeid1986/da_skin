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
if( !function_exists('getClientsDetails') )
{
    require(ROOTDIR . "/includes/clientfunctions.php");
}
if( !function_exists('saveCustomFields') )
{
    require(ROOTDIR . "/includes/customfieldfunctions.php");
}
if( $clientemail )
{
    $result = select_query('tblclients', 'id', array( 'email' => $clientemail ));
}
else
{
    $result = select_query('tblclients', 'id', array( 'id' => $clientid ));
}
$data = mysql_fetch_array($result);
$clientid = $data['id'];
if( !$clientid )
{
    $apiresults = array( 'result' => 'error', 'message' => "Client ID Not Found" );
}
else
{
    if( $_POST['email'] )
    {
        $result = select_query('tblclients', 'id', array( 'email' => $_POST['email'], 'id' => array( 'sqltype' => 'NEQ', 'value' => $clientid ) ));
        $data = mysql_fetch_array($result);
        $result = select_query('tblcontacts', 'id', array( 'email' => $_POST['email'], 'subaccount' => '1' ));
        $data2 = mysql_fetch_array($result);
        if( $data['id'] || $data2['id'] )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Duplicate Email Address" );
            return NULL;
        }
    }
    if( isset($_POST['taxexempt']) )
    {
        $_POST['taxexempt'] = $_POST['taxexempt'] ? 'on' : '';
    }
    if( isset($_POST['latefeeoveride']) )
    {
        $_POST['latefeeoveride'] = $_POST['latefeeoveride'] ? 'on' : '';
    }
    if( isset($_POST['overideduenotices']) )
    {
        $_POST['overideduenotices'] = $_POST['overideduenotices'] ? 'on' : '';
    }
    if( isset($_POST['separateinvoices']) )
    {
        $_POST['separateinvoices'] = $_POST['separateinvoices'] ? 'on' : '';
    }
    if( isset($_POST['disableautocc']) )
    {
        $_POST['disableautocc'] = $_POST['disableautocc'] ? 'on' : '';
    }
    $updatequery = '';
    $fieldsarray = array( 'firstname', 'lastname', 'companyname', 'email', 'address1', 'address2', 'city', 'state', 'postcode', 'country', 'phonenumber', 'credit', 'taxexempt', 'notes', 'status', 'language', 'currency', 'groupid', 'taxexempt', 'latefeeoveride', 'overideduenotices', 'billingcid', 'separateinvoices', 'disableautocc', 'datecreated', 'securityqid', 'bankname', 'banktype', 'lastlogin', 'ip', 'host', 'gatewayid' );
    foreach( $fieldsarray as $fieldname )
    {
        if( isset($_POST[$fieldname]) )
        {
            $updatequery .= $fieldname . "='" . db_escape_string($_POST[$fieldname]) . "',";
        }
    }
    if( $_POST['password2'] )
    {
        $updatequery .= "password='" . generateClientPW($_POST['password2']) . "',";
    }
    if( $_POST['securityqans'] )
    {
        $updatequery .= "securityqans='" . encrypt($_POST['securityqans']) . "',";
    }
    if( ($whmcs->get_req_var('clearcreditcard') || $whmcs->get_req_var('cardtype')) && !function_exists('updateCCDetails') )
    {
        require(ROOTDIR . "/includes/ccfunctions.php");
    }
    if( $_POST['cardtype'] )
    {
        updateCCDetails($clientid, $_POST['cardtype'], $_POST['cardnum'], $_POST['cvv'], $_POST['expdate'], $_POST['startdate'], $_POST['issuenumber']);
    }
    if( $whmcs->get_req_var('clearcreditcard') )
    {
        updateCCDetails($clientid, '', '', '', '', '', '', '', true);
    }
    $fieldsarray = array( 'bankcode', 'bankacct' );
    foreach( $fieldsarray as $fieldname )
    {
        if( isset($_POST[$fieldname]) )
        {
            $updatequery .= $fieldname . "=AES_ENCRYPT('" . db_escape_string($_POST[$fieldname]) . "','" . $cchash . "'),";
        }
    }
    $query = "UPDATE tblclients SET " . substr($updatequery, 0, 0 - 1) . " WHERE id=" . (int) $clientid;
    $result = full_query($query);
    if( $customfields )
    {
        $customfields = base64_decode($customfields);
        $customfields = safe_unserialize($customfields);
        saveCustomFields($clientid, $customfields);
    }
    if( $paymentmethod )
    {
        clientChangeDefaultGateway($clientid, $paymentmethod);
    }
    $apiresults = array( 'result' => 'success', 'clientid' => $_POST['clientid'] );
}