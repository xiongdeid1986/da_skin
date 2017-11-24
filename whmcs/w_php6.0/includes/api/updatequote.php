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
if( !function_exists('addTransaction') )
{
    require(ROOTDIR . "/includes/invoicefunctions.php");
}
if( !function_exists('saveQuote') )
{
    require(ROOTDIR . "/includes/quotefunctions.php");
}
$result = select_query('tblquotes', '', array( 'id' => $quoteid ));
$data = mysql_fetch_array($result);
$quoteid = $data['id'];
if( !$quoteid )
{
    $apiresults = array( 'result' => 'error', 'message' => "Quote ID Not Found" );
}
else
{
    $stage = is_null($stage) ? $data['stage'] : $stage;
    $stagearray = array( 'Draft', 'Delivered', "On Hold", 'Accepted', 'Lost', 'Dead' );
    if( $stage && !in_array($stage, $stagearray) )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Invalid Stage" );
    }
    else
    {
        $subject = is_null($subject) ? $data['subject'] : $subject;
        $validuntil = is_null($validuntil) ? fromMySQLDate($data['validuntil']) : fromMySQLDate($validuntil);
        $userid = is_null($userid) ? $data['userid'] : $userid;
        if( !$userid )
        {
            $clienttype = 'new';
            $firstname = is_null($firstname) ? $data['firstname'] : $firstname;
            $lastname = is_null($lastname) ? $data['lastname'] : $lastname;
            $companyname = is_null($companyname) ? $data['companyname'] : $companyname;
            $email = is_null($email) ? $data['email'] : $email;
            $address1 = is_null($address1) ? $data['address1'] : $address1;
            $address2 = is_null($address2) ? $data['address2'] : $address2;
            $city = is_null($city) ? $data['city'] : $city;
            $state = is_null($state) ? $data['state'] : $state;
            $postcode = is_null($postcode) ? $data['postcode'] : $postcode;
            $country = is_null($country) ? $data['country'] : $country;
            $phonenumber = is_null($phonenumber) ? $data['phonenumber'] : $phonenumber;
            $currency = is_null($currency) ? $data['currency'] : $currency;
        }
        $proposal = is_null($proposal) ? $data['proposal'] : $proposal;
        $customernotes = is_null($customernotes) ? $data['customernotes'] : $customernotes;
        $adminnotes = is_null($adminnotes) ? $data['adminnotes'] : $adminnotes;
        $datecreated = fromMySQLDate($data['datecreated']);
        if( $lineitems )
        {
            $lineitems = base64_decode($lineitems);
            $lineitemsarray = safe_unserialize($lineitems);
        }
        saveQuote($quoteid, $subject, $stage, $datecreated, $validuntil, $clienttype, $userid, $firstname, $lastname, $companyname, $email, $address1, $address2, $city, $state, $postcode, $country, $phonenumber, $currency, $lineitemsarray, $proposal, $customernotes, $adminnotes);
        $apiresults = array( 'result' => 'success' );
    }
}