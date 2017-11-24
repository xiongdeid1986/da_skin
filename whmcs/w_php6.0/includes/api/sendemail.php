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
$validCustomEmailTypes = array( 'general', 'product', 'domain', 'invoice', 'support', 'affiliate' );
$incomingEmailTplName = $whmcs->get_req_var('messagename');
$incomingRelId = $whmcs->get_req_var('id');
$incomingCustomType = $whmcs->get_req_var('customtype');
$incomingCustomSubject = $whmcs->get_req_var('customsubject');
$incomingCustomMsg = $whmcs->get_req_var('custommessage');
$incomingCustomVars = $whmcs->get_req_var('customvars');
$incomingNonNl2Br = $whmcs->get_req_var('nonl2br');
if( !$incomingEmailTplName && !$incomingCustomType )
{
    $apiresults = array( 'result' => 'error', 'message' => "You must provide either an existing email template name or a custom message type" );
}
else
{
    if( $incomingCustomType )
    {
        if( !in_array($incomingCustomType, $validCustomEmailTypes) )
        {
            $apiresults = array( 'result' => 'error', 'message' => "Invalid message type provided" );
            return NULL;
        }
        if( !$incomingCustomSubject )
        {
            $apiresults = array( 'result' => 'error', 'message' => "A subject is required for a custom message" );
            return NULL;
        }
        if( !$incomingCustomMsg )
        {
            $apiresults = array( 'result' => 'error', 'message' => "A message body is required for a custom message" );
            return NULL;
        }
    }
    if( !$incomingRelId || !is_numeric($incomingRelId) )
    {
        $apiresults = array( 'result' => 'error', 'message' => "A related ID is required" );
    }
    else
    {
        if( $incomingCustomType )
        {
            $messageBody = WHMCS_Input_Sanitize::decode($incomingCustomMsg);
            if( !$incomingNonNl2Br )
            {
                $messageBody = nl2br($messageBody);
            }
            delete_query('tblemailtemplates', array( 'name' => "Mass Mail Template" ));
            insert_query('tblemailtemplates', array( 'name' => "Mass Mail Template", 'type' => $incomingCustomType, 'subject' => WHMCS_Input_Sanitize::encode($incomingCustomSubject), 'message' => WHMCS_Input_Sanitize::encode($messageBody) ));
            $messageNameToSend = "Mass Mail Template";
        }
        else
        {
            $messageNameToSend = $incomingEmailTplName;
            $data = get_query_vals('tblemailtemplates', "id, disabled", array( 'name' => $messageNameToSend, 'language' => '' ));
            if( !$data[0] )
            {
                $apiresults = array( 'result' => 'error', 'message' => "Email Template not found" );
                return NULL;
            }
            if( $data[1] )
            {
                $apiresults = array( 'result' => 'error', 'message' => "Email Template is disabled" );
                return NULL;
            }
        }
        $customVars = array(  );
        if( $incomingCustomVars )
        {
            if( is_array($incomingCustomVars) )
            {
                $customVars = $incomingCustomVars;
            }
            else
            {
                $customVars = safe_unserialize(base64_decode($incomingCustomVars));
            }
        }
        $sendingResult = sendMessage($messageNameToSend, $incomingRelId, $customVars);
        if( $sendingResult )
        {
            $apiresults = array( 'result' => 'success' );
        }
        else
        {
            $apiresults = array( 'result' => 'error', 'message' => "Sending Failed. Please see documentation." );
        }
        if( $incomingCustomType )
        {
            delete_query('tblemailtemplates', array( 'name' => "Mass Mail Template" ));
        }
    }
}