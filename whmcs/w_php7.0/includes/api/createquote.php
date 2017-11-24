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
if( !function_exists('addClient') )
{
    require(ROOTDIR . "/includes/clientfunctions.php");
}
if( !function_exists('updateInvoiceTotal') )
{
    require(ROOTDIR . "/includes/invoicefunctions.php");
}
if( !function_exists('saveQuote') )
{
    require(ROOTDIR . "/includes/quotefunctions.php");
}
if( !$subject )
{
    $apiresults = array( 'result' => 'error', 'message' => "Subject is required" );
}
else
{
    $stagearray = array( 'Draft', 'Delivered', "On Hold", 'Accepted', 'Lost', 'Dead' );
    if( !in_array($stage, $stagearray) )
    {
        $apiresults = array( 'result' => 'error', 'message' => "Invalid Stage" );
    }
    else
    {
        if( !$validuntil )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Valid Until is required" );
        }
        else
        {
            if( !$datecreated )
            {
                $datecreated = date('Y-m-d');
            }
            if( $lineitems )
            {
                $lineitems = base64_decode($lineitems);
                $lineitemsarray = safe_unserialize($lineitems);
            }
            if( !$userid )
            {
                $clienttype = 'new';
            }
            $newquoteid = saveQuote('', $subject, $stage, $datecreated, $validuntil, $clienttype, $userid, $firstname, $lastname, $companyname, $email, $address1, $address2, $city, $state, $postcode, $country, $phonenumber, $currency, $lineitemsarray, $proposal, $customernotes, $adminnotes);
            $apiresults = array( 'result' => 'success', 'quoteid' => $newquoteid );
        }
    }
}