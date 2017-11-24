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
define('ADMINAREA', true);
require("../init.php");
$aInt = new WHMCS_Admin("Mass Mail", false);
$aInt->title = $aInt->lang('sendmessage', 'sendmessagetitle');
$aInt->sidebar = 'clients';
$aInt->icon = 'massmail';
ob_start();
$massmailquery = $query = $safeStoredQuery = $queryMadeFromEmailType = $token = null;
$userInput_massmailquery = $whmcs->get_req_var('massmailquery');
$queryMgr = new WHMCS_Token_Query("Admin.Massmail");
$preaction = $whmcs->getInstance()->get_req_var('preaction');
if( !$queryMgr->isValidTokenFormat($userInput_massmailquery) )
{
    $userInput_massmailquery = null;
}
if( $preaction == 'preview' )
{
    $action = '';
    check_token("WHMCS.admin.default");
    $email_preview = true;
    delete_query('tblemailtemplates', array( 'name' => "Mass Mail Template" ));
    if( $type == 'addon' )
    {
        $type = 'product';
    }
    insert_query('tblemailtemplates', array( 'type' => $type, 'name' => "Mass Mail Template", 'subject' => $subject, 'message' => $message, 'fromname' => '', 'fromemail' => '', 'copyto' => '' ));
    $safeStoredQuery = $queryMgr->getQuery($queryMgr->getTokenValue());
    if( $massmail && $safeStoredQuery )
    {
        $massmailquery = $safeStoredQuery;
        $result = full_query($massmailquery);
        $totalemails = mysql_num_rows($result);
        $totalsteps = ceil($totalemails / $massmailamount);
        $esttotaltime = ($totalsteps - ($step + 1)) * $massmailinterval;
        $result = full_query($massmailquery . " LIMIT 0,1");
        while( $data = mysql_fetch_array($result) )
        {
            sendMessage("Mass Mail Template", $data['id'], '', true, $_SESSION['massmail']['attachments']);
        }
    }
    else
    {
        if( $multiple )
        {
            sendMessage("Mass Mail Template", $selectedclients[0], '', true, $_SESSION['massmail']['attachments']);
        }
        else
        {
            sendMessage("Mass Mail Template", $id, '', true, $_SESSION['massmail']['attachments']);
        }
    }
    exit();
}
if( $action == 'send' )
{
    check_token("WHMCS.admin.default");
    if( !$step )
    {
        if( !$message )
        {
            infoBox($aInt->lang('sendmessage', 'validationerrortitle'), $aInt->lang('sendmessage', 'validationerrormsg'));
        }
        if( !$subject )
        {
            infoBox($aInt->lang('sendmessage', 'validationerrortitle'), $aInt->lang('sendmessage', 'validationerrorsub'));
        }
        if( !$fromemail )
        {
            infoBox($aInt->lang('sendmessage', 'validationerrortitle'), $aInt->lang('sendmessage', 'validationerroremail'));
        }
        if( !$fromname )
        {
            infoBox($aInt->lang('sendmessage', 'validationerrortitle'), $aInt->lang('sendmessage', 'validationerrorname'));
        }
    }
    if( $infobox )
    {
        $showform = true;
    }
    else
    {
        $done = false;
        $additionalMergeFields = array(  );
        if( $type == 'addon' )
        {
            $type = 'product';
            $additionalMergeFields['addonemail'] = true;
        }
        if( $save == 'on' )
        {
            insert_query('tblemailtemplates', array( 'type' => $type, 'name' => $savename, 'subject' => $subject, 'message' => $message, 'fromname' => $fromname, 'fromemail' => $fromemail, 'copyto' => $cc, 'custom' => '1' ));
            echo "<p>" . $aInt->lang('sendmessage', 'msgsavedsuccess') . "</p>";
        }
        if( !$step )
        {
            delete_query('tblemailtemplates', array( 'name' => "Mass Mail Template" ));
            insert_query('tblemailtemplates', array( 'type' => $type, 'name' => "Mass Mail Template", 'subject' => $subject, 'message' => $message, 'fromname' => $fromname, 'fromemail' => $fromemail, 'copyto' => $cc ));
            $_SESSION['massmail']['massmailamount'] = $massmailamount;
            $_SESSION['massmail']['massmailinterval'] = $massmailinterval;
            $attachments = array(  );
            if( isset($_FILES['attachments']) )
            {
                foreach( $_FILES['attachments']['name'] as $num => $filename )
                {
                    try
                    {
                        $file = new WHMCS_File_Upload('attachments', $num);
                        $prefix = "attach{RAND}_";
                        $filename = $file->move($whmcs->getAttachmentsDir(), $prefix);
                        $attachments[] = array( 'path' => $whmcs->getAttachmentsDir() . $filename, 'filename' => $filename, 'displayname' => $file->getCleanName() );
                    }
                    catch( WHMCS_Exception_File_NotUploaded $e )
                    {
                    }
                }
            }
            $_SESSION['massmail']['attachments'] = $attachments;
            $step = 0;
        }
        $mail_attachments = array(  );
        if( isset($_SESSION['massmail']['attachments']) )
        {
            foreach( $_SESSION['massmail']['attachments'] as $parts )
            {
                $mail_attachments[$parts['path']] = $parts['displayname'];
            }
        }
        if( $massmail && ($safeStoredQuery = $queryMgr->getQuery($queryMgr->getTokenValue())) )
        {
            $massmailquery = $safeStoredQuery;
            if( $emailoptout || WHMCS_Session::get('massmailemailoptout') )
            {
                WHMCS_Session::set('massmailemailoptout', true);
                $massmailquery .= " AND tblclients.emailoptout = '0'";
            }
            $sentids = $_SESSION['massmail']['sentids'];
            $massmailamount = (int) $_SESSION['massmail']['massmailamount'];
            $massmailinterval = (int) $_SESSION['massmail']['massmailinterval'];
            if( !$massmailamount )
            {
                $massmailamount = 25;
            }
            if( !$massmailinterval )
            {
                $massmailinterval = 30;
            }
            $result = full_query($massmailquery);
            $totalemails = mysql_num_rows($result);
            $totalsteps = ceil($totalemails / $massmailamount);
            $esttotaltime = ($totalsteps - ($step + 1)) * $massmailinterval;
            infoBox($aInt->lang('sendmessage', 'massmailqueue'), $totalemails . $aInt->lang('sendmessage', 'massmailspart1') . ($step + 1) . $aInt->lang('sendmessage', 'massmailspart2') . $totalsteps . $aInt->lang('sendmessage', 'massmailspart3') . $esttotaltime . $aInt->lang('sendmessage', 'massmailspart4'));
            echo $infobox;
            $result = full_query($massmailquery . " LIMIT " . (int) ($step * $massmailamount) . ',' . (int) $massmailamount);
            ob_start();
            while( $data = mysql_fetch_array($result) )
            {
                if( $data['aid'] )
                {
                    $additionalMergeFields['addonid'] = $data['aid'];
                }
                if( $sendforeach || !$sendforeach && !in_array($data['userid'], $sentids) )
                {
                    sendMessage("Mass Mail Template", $data['id'], $additionalMergeFields, true, $mail_attachments);
                    $sentids[] = $data['userid'];
                }
                else
                {
                    echo "<li>" . $aInt->lang('sendmessage', 'skippedduplicate') . $data['userid'] . "<br>";
                }
            }
            $_SESSION['massmail']['sentids'] = $sentids;
            $content = ob_get_contents();
            ob_end_clean();
            echo "<ul>" . str_replace(array( "<p>", "</p>" ), array( "<li>", "</li>" ), $content) . "</ul>";
            $totalsent = $step * $massmailamount + $massmailamount;
            if( $totalemails <= $totalsent )
            {
                $done = true;
            }
            else
            {
                $massmaillink = "sendmessage.php?action=send&sendforeach=" . $sendforeach . "&massmail=1&step=" . ($step + 1) . generate_token('link');
                echo "<p><a href=\"" . $massmaillink . "\">" . $aInt->lang('sendmessage', 'forcenextbatch') . "</a></p><meta http-equiv=\"refresh\" content=\"" . $massmailinterval . ";url=" . $massmaillink . "\">";
            }
        }
        else
        {
            if( $multiple )
            {
                foreach( $selectedclients as $selectedclient )
                {
                    $skipemail = false;
                    if( $emailoptout )
                    {
                        if( $type == 'general' )
                        {
                            $skipemail = get_query_val('tblclients', 'emailoptout', array( 'id' => $selectedclient ));
                        }
                        else
                        {
                            if( $type == 'product' )
                            {
                                $skipemail = get_query_val('tblhosting', 'emailoptout', array( "tblhosting.id" => $selectedclient ), '', '', '', "tblclients ON tblclients.id=tblhosting.userid");
                            }
                            else
                            {
                                if( $type == 'domain' )
                                {
                                    $skipemail = get_query_val('tbldomains', 'emailoptout', array( "tbldomains.id" => $selectedclient ), '', '', '', "tblclients ON tblclients.id=tbldomains.userid");
                                }
                                else
                                {
                                    if( $type == 'affiliate' )
                                    {
                                        $skipemail = get_query_val('tblaffiliates', 'emailoptout', array( "tblaffiliates.id" => $selectedclient ), '', '', '', "tblclients ON tblclients.id=tblaffiliates.clientid");
                                    }
                                }
                            }
                        }
                    }
                    if( $skipemail )
                    {
                        echo "<p>Email Skipped for ID " . $selectedclient . " due to Marketing Email Opt-Out</p>";
                    }
                    else
                    {
                        sendMessage("Mass Mail Template", $selectedclient, '', true, $mail_attachments);
                    }
                    $done = true;
                }
            }
            else
            {
                sendMessage("Mass Mail Template", $id, '', true, $mail_attachments);
                $done = true;
            }
        }
        if( $done )
        {
            echo "<p><b>" . $aInt->lang('sendmessage', 'sendingcompleted') . "</b></p>";
            delete_query('tblemailtemplates', array( 'name' => "Mass Mail Template" ));
            foreach( $_SESSION['massmail']['attachments'] as $parts )
            {
                try
                {
                    $file = new WHMCS_File($whmcs->getAttachmentsDir() . $parts['filename']);
                    $file->delete();
                }
                catch( WHMCS_Exception_File_NotFound $e )
                {
                }
            }
            unset($_SESSION['massmail']);
        }
    }
}
else
{
    $showform = true;
}
if( $showform )
{
    if( !$infobox )
    {
        unset($_SESSION['massmail']);
    }
    $todata = array(  );
    $query = '';
    if( !$type )
    {
        $type = 'general';
    }
    $queryMadeFromEmailType = '';
    if( $type == 'massmail' )
    {
        $clientstatus = db_build_in_array($clientstatus);
        $clientgroup = db_build_in_array($clientgroup);
        $clientlanguage = db_build_in_array($clientlanguage, true);
        $productids = db_build_in_array($productids);
        $productstatus = db_build_in_array($productstatus);
        $server = db_build_in_array($server);
        $addonids = db_build_in_array($addonids);
        $addonstatus = db_build_in_array($addonstatus);
        $domainstatus = db_build_in_array($domainstatus);
        if( $emailtype == 'General' )
        {
            $type = 'general';
            $query = "SELECT id,id AS userid,tblclients.firstname,tblclients.lastname,tblclients.email FROM tblclients WHERE id!=''";
            if( $clientstatus )
            {
                $query .= " AND tblclients.status IN (" . $clientstatus . ")";
            }
            if( $clientgroup )
            {
                $query .= " AND tblclients.groupid IN (" . $clientgroup . ")";
            }
            if( $clientlanguage )
            {
                $query .= " AND tblclients.language IN (" . $clientlanguage . ")";
            }
            if( is_array($customfield) )
            {
                foreach( $customfield as $k => $v )
                {
                    if( $v )
                    {
                        if( $v == 'cfon' )
                        {
                            $v = 'on';
                        }
                        if( $v == 'cfoff' )
                        {
                            $query .= " AND ((SELECT value FROM tblcustomfieldsvalues WHERE fieldid='" . db_escape_string($k) . "' AND relid=tblclients.id LIMIT 1)='' OR (SELECT value FROM tblcustomfieldsvalues WHERE fieldid='" . db_escape_string($k) . "' AND relid=tblclients.id LIMIT 1) IS NULL)";
                        }
                        else
                        {
                            $query .= " AND (SELECT value FROM tblcustomfieldsvalues WHERE fieldid='" . db_escape_string($k) . "' AND relid=tblclients.id LIMIT 1)='" . db_escape_string($v) . "'";
                        }
                    }
                }
            }
        }
        else
        {
            if( $emailtype == 'Product/Service' )
            {
                $type = 'product';
                $query = "SELECT tblhosting.id,tblhosting.userid,tblhosting.domain,tblclients.firstname,tblclients.lastname,tblclients.email FROM tblhosting INNER JOIN tblclients ON tblclients.id=tblhosting.userid INNER JOIN tblproducts ON tblproducts.id=tblhosting.packageid WHERE tblhosting.id!=''";
                if( $productids )
                {
                    $query .= " AND tblproducts.id IN (" . $productids . ")";
                }
                if( $productstatus )
                {
                    $query .= " AND tblhosting.domainstatus IN (" . $productstatus . ")";
                }
                if( $server )
                {
                    $query .= " AND tblhosting.server IN (" . $server . ")";
                }
                if( $clientstatus )
                {
                    $query .= " AND tblclients.status IN (" . $clientstatus . ")";
                }
                if( $clientgroup )
                {
                    $query .= " AND tblclients.groupid IN (" . $clientgroup . ")";
                }
                if( $clientlanguage )
                {
                    $query .= " AND tblclients.language IN (" . $clientlanguage . ")";
                }
                if( is_array($customfield) )
                {
                    foreach( $customfield as $k => $v )
                    {
                        if( $v )
                        {
                            $query .= " AND (SELECT value FROM tblcustomfieldsvalues WHERE fieldid='" . db_escape_string($k) . "' AND relid=tblclients.id LIMIT 1)='" . db_escape_string($v) . "'";
                        }
                    }
                }
            }
            else
            {
                if( $emailtype == 'Addon' )
                {
                    $type = 'addon';
                    $query = "        SELECT tblhosting.id, tblhosting.userid, tblhosting.domain, tblclients.firstname,\n                tblclients.lastname, tblclients.email, tblhostingaddons.id as aid\n                FROM tblhosting\n                INNER JOIN tblclients ON tblclients.id=tblhosting.userid\n                INNER JOIN tblhostingaddons ON tblhostingaddons.hostingid = tblhosting.id\n                WHERE tblhostingaddons.id!=''";
                    if( $addonids )
                    {
                        $query .= " AND tblhostingaddons.addonid IN (" . $addonids . ")";
                    }
                    if( $addonstatus )
                    {
                        $query .= " AND tblhostingaddons.status IN (" . $addonstatus . ")";
                    }
                    if( $clientstatus )
                    {
                        $query .= " AND tblclients.status IN (" . $clientstatus . ")";
                    }
                    if( $clientgroup )
                    {
                        $query .= " AND tblclients.groupid IN (" . $clientgroup . ")";
                    }
                    if( $clientlanguage )
                    {
                        $query .= " AND tblclients.language IN (" . $clientlanguage . ")";
                    }
                    if( is_array($customfield) )
                    {
                        foreach( $customfield as $k => $v )
                        {
                            if( $v )
                            {
                                $query .= " AND (SELECT value FROM tblcustomfieldsvalues WHERE fieldid='" . db_escape_string($k) . "' AND relid=tblclients.id LIMIT 1)='" . db_escape_string($v) . "'";
                            }
                        }
                    }
                }
                else
                {
                    if( $emailtype == 'Domain' )
                    {
                        $type = 'domain';
                        $query = "SELECT tbldomains.id,tbldomains.userid,tbldomains.domain,tblclients.firstname,tblclients.lastname,tblclients.email FROM tbldomains INNER JOIN tblclients ON tblclients.id=tbldomains.userid WHERE tbldomains.id!=''";
                        if( $domainstatus )
                        {
                            $query .= " AND tbldomains.status IN (" . $domainstatus . ")";
                        }
                        if( $clientstatus )
                        {
                            $query .= " AND tblclients.status IN (" . $clientstatus . ")";
                        }
                        if( $clientgroup )
                        {
                            $query .= " AND tblclients.groupid IN (" . $clientgroup . ")";
                        }
                        if( $clientlanguage )
                        {
                            $query .= " AND tblclients.language IN (" . $clientlanguage . ")";
                        }
                        if( is_array($customfield) )
                        {
                            foreach( $customfield as $k => $v )
                            {
                                if( $v )
                                {
                                    $query .= " AND (SELECT value FROM tblcustomfieldsvalues WHERE fieldid='" . db_escape_string($k) . "' AND relid=tblclients.id LIMIT 1)='" . db_escape_string($v) . "'";
                                }
                            }
                        }
                    }
                }
            }
        }
        $queryMadeFromEmailType = $query;
    }
    if( $queryMadeFromEmailType || $userInput_massmailquery )
    {
        if( $queryMadeFromEmailType )
        {
            $massmailquery = $queryMadeFromEmailType;
        }
        else
        {
            if( !$queryMadeFromEmailType && $queryMgr->isValidTokenFormat($userInput_massmailquery) )
            {
                $massmailquery = $queryMgr->getQuery($userInput_massmailquery);
            }
            else
            {
                $massmailquery = '';
            }
        }
        $useridsdone = array(  );
        $result = full_query($massmailquery);
        while( $data = mysql_fetch_array($result) )
        {
            if( $sendforeach || !$sendforeach && !in_array($data['userid'], $useridsdone) )
            {
                $temptodata = $data['firstname'] . " " . $data['lastname'];
                if( $data['domain'] )
                {
                    $temptodata .= " - " . $data['domain'];
                }
                $temptodata .= " &lt;" . $data['email'] . "&gt;";
                $todata[] = $temptodata;
                $useridsdone[] = $data['userid'];
            }
        }
    }
    else
    {
        if( $multiple )
        {
            if( $type == 'general' )
            {
                foreach( $selectedclients as $id )
                {
                    $result = select_query('tblclients', '', array( 'id' => $id ));
                    $data = mysql_fetch_array($result);
                    $todata[] = $data['firstname'] . " " . $data['lastname'] . " &lt;" . $data['email'] . "&gt;";
                }
            }
            else
            {
                if( $type == 'product' )
                {
                    foreach( $selectedclients as $id )
                    {
                        $result = select_query('tblhosting', "tblclients.firstname,tblclients.lastname,tblclients.email,tblhosting.domain", array( "tblhosting.id" => $id ), '', '', '', "tblclients ON tblclients.id=tblhosting.userid");
                        $data = mysql_fetch_array($result);
                        $todata[] = $data['firstname'] . " " . $data['lastname'] . " - " . $data['domain'] . " &lt;" . $data['email'] . "&gt;";
                    }
                }
                else
                {
                    if( $type == 'domain' )
                    {
                        foreach( $selectedclients as $id )
                        {
                            $result = select_query('tbldomains', "tblclients.firstname,tblclients.lastname,tblclients.email,tbldomains.domain", array( "tbldomains.id" => $id ), '', '', '', "tblclients ON tblclients.id=tbldomains.userid");
                            $data = mysql_fetch_array($result);
                            $todata[] = $data['firstname'] . " " . $data['lastname'] . " - " . $data['domain'] . " &lt;" . $data['email'] . "&gt;";
                        }
                    }
                    else
                    {
                        if( $type == 'affiliate' )
                        {
                            foreach( $selectedclients as $id )
                            {
                                $result = select_query('tblaffiliates', "tblclients.firstname,tblclients.lastname,tblclients.email", array( "tblaffiliates.id" => $id ), '', '', '', "tblclients ON tblclients.id=tblaffiliates.clientid");
                                $data = mysql_fetch_array($result);
                                $todata[] = $data['firstname'] . " " . $data['lastname'] . " - " . $data['domain'] . " &lt;" . $data['email'] . "&gt;";
                            }
                        }
                    }
                }
            }
        }
        else
        {
            if( $resend )
            {
                $result = select_query('tblemails', '', array( 'id' => $emailid ));
                $data = mysql_fetch_array($result);
                $id = $data['userid'];
                $subject = $data['subject'];
                $message = $data['message'];
                $message = str_replace("<p><a href=\"" . $CONFIG['Domain'] . "\" target=\"_blank\"><img src=\"" . $CONFIG['LogoURL'] . "\" alt=\"" . $CONFIG['CompanyName'] . "\" border=\"0\"></a></p>", '', $message);
                $message = str_replace("<p><a href=\"" . $CONFIG['Domain'] . "\" target=\"_blank\"><img src=\"" . $CONFIG['LogoURL'] . "\" alt=\"" . $CONFIG['CompanyName'] . "\" border=\"0\" /></a></p>", '', $message);
                $message = str_replace(WHMCS_Input_Sanitize::decode($CONFIG['EmailGlobalHeader']), '', $message);
                $message = str_replace(WHMCS_Input_Sanitize::decode($CONFIG['EmailGlobalFooter']), '', $message);
                $styleend = strpos($message, "</style>") + 8;
                $message = trim(substr($message, $styleend));
                $type = 'general';
            }
            if( $type == 'general' )
            {
                $result = select_query('tblclients', '', array( 'id' => $id ));
                $data = mysql_fetch_array($result);
                if( $data['email'] )
                {
                    $todata[] = $data['firstname'] . " " . $data['lastname'] . " &lt;" . $data['email'] . "&gt;";
                }
            }
            else
            {
                if( $type == 'product' )
                {
                    $query = "SELECT tblclients.id,tblclients.firstname,tblclients.lastname,tblclients.email,tblhosting.domain FROM tblhosting INNER JOIN tblclients ON tblclients.id=tblhosting.userid WHERE tblhosting.id='" . mysql_real_escape_string($id) . "'";
                    $result = full_query($query);
                    $data = mysql_fetch_array($result);
                    if( $data['email'] )
                    {
                        $todata[] = $data['firstname'] . " " . $data['lastname'] . " - " . $data['domain'] . " &lt;" . $data['email'] . "&gt;";
                    }
                }
                else
                {
                    if( $type == 'domain' )
                    {
                        $query = "SELECT tblclients.id,tblclients.firstname,tblclients.lastname,tblclients.email,tbldomains.domain FROM tbldomains INNER JOIN tblclients ON tblclients.id=tbldomains.userid WHERE tbldomains.id='" . mysql_real_escape_string($id) . "'";
                        $result = full_query($query);
                        $data = mysql_fetch_array($result);
                        if( $data['email'] )
                        {
                            $todata[] = $data['firstname'] . " " . $data['lastname'] . " - " . $data['domain'] . " &lt;" . $data['email'] . "&gt;";
                        }
                    }
                }
            }
        }
    }
    $numRecipients = count($todata);
    if( !$numRecipients )
    {
        infoBox($aInt->lang('sendmessage', 'noreceiptients'), $aInt->lang('sendmessage', 'noreceiptientsdesc'));
    }
    echo $infobox;
    if( $sub == 'loadmessage' )
    {
        $language = !$massmailquery && !$multiple && (int) $data['id'] ? get_query_val('tblclients', 'language', array( 'id' => $data['id'] )) : '';
        $result = select_query('tblemailtemplates', '', array( 'name' => $messagename, 'language' => $language ));
        $data = mysql_fetch_array($result);
        if( !$data['id'] )
        {
            $result = select_query('tblemailtemplates', '', array( 'name' => $messagename ));
            $data = mysql_fetch_array($result);
        }
        $subject = $data['subject'];
        $message = $data['message'];
        $fromname = $data['fromname'];
        $fromemail = $data['fromemail'];
        $plaintext = $data['plaintext'];
        if( $plaintext )
        {
            $message = nl2br($message);
        }
    }
    echo "\n<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "\" name=\"frmmessage\"\n    id=\"sendmsgfrm\" enctype=\"multipart/form-data\">\n    <input type=\"hidden\" name=\"action\" value=\"send\" /> <input type=\"hidden\"\n        name=\"type\" value=\"";
    echo $type;
    echo "\" />\n";
    $token = $queryMgr->generateToken();
    $queryMgr->setQuery($token, '');
    $_SESSION['massmail']['sentids'] = array(  );
    WHMCS_Session::set('massmailemailoptout', false);
    if( $massmailquery )
    {
        if( $queryMgr->isValidTokenFormat($massmailquery) )
        {
            $queryToStore = $queryMgr->getQuery($massmailquery);
        }
        else
        {
            $queryToStore = $massmailquery;
        }
        $queryMgr->setQuery($token, $queryToStore);
        echo "<input type=\"hidden\" name=\"massmailquery\" value=\"" . $token . "\">";
        echo "<input type=\"hidden\" name=\"massmail\" value=\"true\" /><input type=\"hidden\" name=\"sendforeach\" value=\"" . $sendforeach . "\" />";
    }
    else
    {
        if( $multiple )
        {
            echo "<input type=\"hidden\" name=\"multiple\" value=\"true\" />";
            foreach( $selectedclients as $selectedclient )
            {
                echo "<input type=\"hidden\" name=\"selectedclients[]\" value=\"" . $selectedclient . "\" />";
            }
        }
        else
        {
            echo "<input type=\"hidden\" name=\"id\" value=\"" . $id . "\" />";
        }
    }
    echo "\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\"\n        cellpadding=\"3\">\n        <tr>\n            <td width=\"140\" class=\"fieldlabel\">";
    echo $aInt->lang('emails', 'from');
    echo "</td>\n            <td class=\"fieldarea\"><input type=\"text\" name=\"fromname\" size=\"25\"\n                value=\"";
    if( !$fromname )
    {
        echo $CONFIG['CompanyName'];
    }
    else
    {
        echo $fromname;
    }
    echo "\">\n                <input type=\"text\" name=\"fromemail\" size=\"60\"\n                value=\"";
    if( !$fromemail )
    {
        echo $CONFIG['Email'];
    }
    else
    {
        echo $fromemail;
    }
    echo "\"></td>\n        </tr>\n        <tr>\n            <td class=\"fieldlabel\">";
    echo $aInt->lang('emails', 'recipients');
    echo "</td>\n            <td class=\"fieldarea\"><table cellspacing=\"0\" cellpadding=\"0\">\n                    <tr>\n                        <td>";
    echo "<select size=\"4\" style=\"width:450px;\"><option>" . $numRecipients . " recipients matched sending criteria.";
    if( 50 < $numRecipients )
    {
        echo " Showing first 50 only...";
    }
    echo "</option>";
    foreach( $todata as $i => $to )
    {
        echo "<option>" . $to . "</option>";
        if( 49 < $i )
        {
            break;
        }
    }
    echo "</select></td>\n                        <td> &nbsp; ";
    echo $aInt->lang('sendmessage', 'emailsentindividually1');
    echo "<br /> &nbsp; ";
    echo $aInt->lang('sendmessage', 'emailsentindividually2');
    echo "</td>\n\n                </table></td>\n            </td>\n        </tr>\n        <tr>\n            <td class=\"fieldlabel\">CC</td>\n            <td class=\"fieldarea\"><input type=\"text\" name=\"cc\" size=\"80\" value=\"\"> ";
    echo $aInt->lang('sendmessage', 'commaseparateemails');
    echo "</td>\n        </tr>\n        <tr>\n            <td class=\"fieldlabel\">Subject</td>\n            <td class=\"fieldarea\"><input type=\"text\" name=\"subject\" size=\"90\"\n                value=\"";
    echo $subject;
    echo "\" id=\"subject\"></td>\n        </tr>\n    </table>\n\n    <br>\n\n    <script langauge=\"javascript\">\nfrmmessage.subject.select();\n</script>\n\n    <textarea name=\"message\" id=\"email_msg1\" rows=\"25\" style=\"width: 100%\"\n        class=\"tinymce\">";
    echo $message;
    echo "</textarea>\n\n    <br />\n\n    <table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\"\n        cellpadding=\"3\">\n        <tr>\n            <td width=\"140\" class=\"fieldlabel\">";
    echo $aInt->lang('support', 'attachments');
    echo "</td>\n            <td class=\"fieldarea\"><div style=\"float: right;\">\n                    <input type=\"button\"\n                        value=\"";
    echo $aInt->lang('emailtpls', 'rteditor');
    echo "\"\n                        class=\"btn\" onclick=\"toggleEditor()\" />\n                </div>\n                <input type=\"file\" name=\"attachments[]\" style=\"width: 60%;\" /> <a\n                href=\"#\" id=\"addfileupload\"><img src=\"images/icons/add.png\"\n                    align=\"absmiddle\" border=\"0\" /> ";
    echo $aInt->lang('support', 'addmore');
    echo "</a><br />\n            <div id=\"fileuploads\"></div></td>\n        </tr>\n";
    if( $massmailquery || $multiple )
    {
        echo "<tr>\n            <td class=\"fieldlabel\">";
        echo $aInt->lang('sendmessage', 'marketingemail');
        echo "</td>\n            <td class=\"fieldarea\"><label><input type=\"checkbox\" id=\"emailoptout\"\n                    name=\"emailoptout\"> ";
        echo $aInt->lang('sendmessage', 'dontsendemailunsubscribe');
        echo "</label></td>\n        </tr>\n";
    }
    if( checkPermission("Create/Edit Email Templates", true) )
    {
        echo "<tr>\n            <td class=\"fieldlabel\">";
        echo $aInt->lang('sendmessage', 'savemesasge');
        echo "</td>\n            <td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"save\"> ";
        echo $aInt->lang('sendmessage', 'entersavename');
        echo ":</label>\n                <input type=\"text\" name=\"savename\" size=\"30\"></td>\n        </tr>";
    }
    if( $massmailquery )
    {
        echo "<tr>\n            <td class=\"fieldlabel\">";
        echo $aInt->lang('sendmessage', 'massmailsettings');
        echo "</td>\n            <td class=\"fieldarea\">";
        echo $aInt->lang('sendmessage', 'massmailsetting1');
        echo " <input\n                type=\"text\" name=\"massmailamount\" size=\"5\" value=\"25\" /> ";
        echo $aInt->lang('sendmessage', 'massmailsetting2');
        echo " <input\n                type=\"text\" name=\"massmailinterval\" size=\"5\" value=\"30\" /> ";
        echo $aInt->lang('sendmessage', 'massmailsetting3');
        echo "</td>\n        </tr>";
    }
    echo "</table>\n\n    <p align=\"center\">\n        <input type=\"button\"\n            value=\"";
    echo $aInt->lang('sendmessage', 'preview');
    echo "\"\n            onclick=\"previewMsg()\" class=\"btn\" /> <input type=\"submit\"\n            value=\"";
    echo $aInt->lang('global', 'sendmessage');
    echo " &raquo;\"\n            class=\"btn-primary\" />\n    </p>\n\n</form>\n\n";
    $aInt->richTextEditor();
    echo "<div id=\"emailoptoutinfo\">";
    infoBox($aInt->lang('sendmessage', 'marketingemail'), sprintf($aInt->lang('sendmessage', 'marketingemaildesc'), "{\$unsubscribe_url}"));
    echo $infobox;
    echo "</div>";
    $i = 1;
    include("mergefields.php");
    echo "\n<form method=\"post\" action=\"";
    echo $_SERVER['PHP_SELF'];
    echo "\">\n    <input type=\"hidden\" name=\"sub\" value=\"loadmessage\"> <input\n        type=\"hidden\" name=\"type\" value=\"";
    echo $type;
    echo "\">\n";
    if( $massmailquery )
    {
        if( $queryMgr->isValidTokenFormat($massmailquery) )
        {
            $queryToStore = $queryMgr->getQuery($massmailquery);
        }
        else
        {
            $queryToStore = $massmailquery;
        }
        $token = $queryMgr->generateToken();
        $queryMgr->setQuery($token, $queryToStore);
        echo "<input type=\"hidden\" name=\"massmailquery\" value=\"" . $token . "\">";
        if( $sendforeach )
        {
            echo "<input type=\"hidden\" name=\"sendforeach\" value=\"" . $sendforeach . "\">";
        }
    }
    else
    {
        if( $multiple )
        {
            echo "<input type=\"hidden\" name=\"multiple\" value=\"true\">";
            foreach( $selectedclients as $selectedclient )
            {
                echo "<input type=\"hidden\" name=\"selectedclients[]\" value=\"" . $selectedclient . "\">";
            }
        }
        else
        {
            echo "<input type=\"hidden\" name=\"id\" value=\"" . $id . "\">";
        }
    }
    echo "<div class=\"contentbox\">\n        <b>";
    echo $aInt->lang('sendmessage', 'loadsavedmsg');
    echo ":</b> <select\n            name=\"messagename\"><option value=\"\">";
    echo $aInt->lang('sendmessage', 'choose');
    echo "...";
    $query = "SELECT * FROM tblemailtemplates WHERE type='general' AND language='' ORDER BY custom,name ASC";
    $result = full_query($query);
    while( $data = mysql_fetch_array($result) )
    {
        $messid = $data['id'];
        $messagename = $data['name'];
        echo "<option style=\"background-color:#ffffff\">" . $messagename . "</option>";
    }
    if( $type != 'general' )
    {
        $result = select_query('tblemailtemplates', '', array( 'type' => $type, 'language' => '' ), "custom` ASC,`name", 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $messid = $data['id'];
            $messagename = $data['name'];
            echo "<option";
            if( $custom == '' )
            {
                echo " style=\"background-color:#efefef\"";
            }
            echo ">" . $messagename . "</option>";
        }
    }
    echo "</select> <input type=\"submit\"\n            value=\"";
    echo $aInt->lang('sendmessage', 'loadMessage');
    echo "\">\n    </div>\n</form>\n\n";
    echo $aInt->jqueryDialog('previewwnd', $aInt->lang('sendmessage', 'preview'), "<div id=\"previewwndcontent\">" . $aInt->lang('global', 'loading') . "</div>", array( $aInt->lang('global', 'ok') => '' ), '450', '700', '');
    $jquerycode .= "\$(\"#addfileupload\").click(function () {\n    \$(\"#fileuploads\").append(\"<input type=\\\"file\\\" name=\\\"attachments[]\\\" style=\\\"width:70%;\\\" /><br />\");\n    return false;\n});\n\$(\"#emailoptoutinfo\").hide();\n\$(\"#emailoptout\").click(function(){\n    if (this.checked) {\n        \$(\"#emailoptoutinfo\").slideDown(\"slow\");\n    } else {\n        \$(\"#emailoptoutinfo\").slideUp(\"slow\");\n    }\n});";
    $jscode = "function previewMsg() {\n    if (\$(\"#email_msg1\").tinymce().isHidden()) {\n        alert(\"Cannot preview message while the rich-text editor is disabled - please re-enable and then try again\");\n    } else {\n        \$(\"#previewwnd\").dialog(\"open\");\n        jQuery.post(\"sendmessage.php\", \$(\"#sendmsgfrm\").serialize()+\"&preaction=preview\",\n        function(data){\n            if (data) {\n                jQuery(\"#previewwndcontent\").html(data);}\n            else {\n                jQuery(\"#previewwndcontent\").html(\"Syntax Error - Please check your email message for invalid template syntax or missing closing tags\");\n            }\n        });\n        return false;\n    }\n}";
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();