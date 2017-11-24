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
if( !function_exists('addContact') )
{
    require(ROOTDIR . "/includes/clientfunctions.php");
}
$result = select_query('tblclients', 'id', array( 'id' => $clientid ));
$data = mysql_fetch_array($result);
if( !$data[0] )
{
    $apiresults = array( 'result' => 'error', 'message' => "Client ID Not Found" );
}
else
{
    $permissions = $permissions ? explode(',', $permissions) : array(  );
    if( count($permissions) )
    {
        $result = select_query('tblclients', 'id', array( 'email' => $email ));
        $data = mysql_fetch_array($result);
        $result = select_query('tblcontacts', 'id', array( 'email' => $email, 'subaccount' => '1' ));
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
    $contactid = addContact($clientid, $firstname, $lastname, $companyname, $email, $address1, $address2, $city, $state, $postcode, $country, $phonenumber, $password2, $permissions, $generalemails, $productemails, $domainemails, $invoiceemails, $supportemails);
    $apiresults = array( 'result' => 'success', 'contactid' => $contactid );
}