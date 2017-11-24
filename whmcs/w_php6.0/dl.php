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
define('CLIENTAREA', true);
require("init.php");
$type = $whmcs->get_req_var('type');
$viewpdf = $whmcs->get_req_var('viewpdf');
$i = (int) $whmcs->get_req_var('i');
$id = (int) $whmcs->get_req_var('id');
$fileurl = $allowedtodownload = '';
$folder_path = $file_name = $display_name = '';
$allowedtodownload = '';
if( $type == 'i' )
{
    $result = select_query('tblinvoices', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $invoiceid = $data['id'];
    $invoicenum = $data['invoicenum'];
    $userid = $data['userid'];
    if( !$invoiceid )
    {
        redir('', "clientarea.php");
    }
    require("includes/adminfunctions.php");
    if( $_SESSION['adminid'] )
    {
        if( !checkPermission("Manage Invoice", true) )
        {
            exit( "You do not have the necessary permissions to download PDF invoices. If you feel this message to be an error, please contact the system administrator." );
        }
    }
    else
    {
        if( $_SESSION['uid'] == $userid )
        {
        }
        else
        {
            downloadLogin();
        }
    }
    if( !$invoicenum )
    {
        $invoicenum = $invoiceid;
    }
    require("includes/invoicefunctions.php");
    $pdfdata = pdfInvoice($id);
    header("Pragma: public");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0, private");
    header("Cache-Control: private", false);
    header("Content-Type: application/pdf");
    header("Content-Disposition: " . ($viewpdf ? 'inline' : 'attachment') . "; filename=\"" . $_LANG['invoicefilename'] . $invoicenum . ".pdf\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . strlen($pdfdata));
    echo $pdfdata;
    exit();
}
if( $type == 'a' || $type == 'ar' )
{
    $useridOfMasterTicket = $useridOfReply = 0;
    if( $type == 'a' )
    {
        $result = select_query('tbltickets', 'id,userid,attachment', array( 'id' => $id ));
        $data = mysql_fetch_array($result);
        $ticketid = $data['id'];
        $useridOfMasterTicket = $data['userid'];
        $attachments = $data['attachment'];
    }
    else
    {
        $result = select_query('tblticketreplies', 'tid,userid,attachment', array( 'id' => $id ));
        $data = mysql_fetch_array($result);
        $ticketid = $data['tid'];
        $useridOfReply = $data['userid'];
        $attachments = $data['attachment'];
        $useridOfMasterTicket = get_query_val('tbltickets', 'userid', array( 'id' => $ticketid ));
    }
    if( !$ticketid )
    {
        exit( "Ticket ID Not Found" );
    }
    if( $_SESSION['adminid'] )
    {
        require_once(ROOTDIR . "/includes/adminfunctions.php");
        if( !checkPermission("View Support Ticket", true) )
        {
            exit( "You do not have the necessary permissions to View Support Tickets. If you feel this message to be an error, please contact the system administrator." );
        }
        require_once(ROOTDIR . "/includes/ticketfunctions.php");
        $access = validateAdminTicketAccess($ticketid);
        if( $access )
        {
            exit( "Access Denied. You do not have the required permissions to view this ticket." );
        }
    }
    else
    {
        if( $useridOfMasterTicket )
        {
            if( $useridOfMasterTicket != $_SESSION['uid'] )
            {
                downloadLogin();
                exit();
            }
        }
        else
        {
            if( $useridOfReply )
            {
                if( $useridOfReply != $_SESSION['uid'] )
                {
                    downloadLogin();
                    exit();
                }
            }
            else
            {
                $AccessedTicketIDs = WHMCS_Session::get('AccessedTicketIDs');
                $AccessedTicketIDsArray = explode(',', $AccessedTicketIDs);
                if( !in_array($ticketid, $AccessedTicketIDsArray) )
                {
                    exit( "Ticket Attachments cannot be accessed directly. Please try again using the download link provided within the ticket. If you are registered and have an account with us, you can access your tickets from our client area. Otherwise, please use the link to view the ticket which you should have received via email when the ticket was originally opened or last responded to." );
                }
            }
        }
    }
    $folder_path = $attachments_dir;
    $files = explode("|", $attachments);
    $file_name = $files[$i];
    $display_name = substr($file_name, 7);
}
else
{
    if( $type == 'd' )
    {
        $data = get_query_vals('tbldownloads', 'id,location,clientsonly,productdownload', array( 'id' => $id ));
        $downloadID = $data['id'];
        $filename = $data['location'];
        $clientsonly = $data['clientsonly'];
        $productdownload = $data['productdownload'];
        if( !$downloadID )
        {
            exit( "Invalid Download Requested" );
        }
        $userID = (int) WHMCS_Session::get('uid');
        if( !$userID && ($clientsonly || $productdownload) )
        {
            downloadLogin();
        }
        if( $productdownload )
        {
            $serviceID = (int) $whmcs->get_req_var('serviceid');
            if( $serviceID )
            {
                $servicesWhere = array( "tblhosting.id" => $serviceID, 'userid' => $userID, "tblhosting.domainstatus" => 'Active' );
                $addonsWhere = array( "tblhostingaddons.hostingid" => $serviceID, "tblhosting.userid" => $userID, "tblhostingaddons.status" => 'Active' );
            }
            else
            {
                $servicesWhere = array( 'userid' => $userID, "tblhosting.domainstatus" => 'Active' );
                $addonsWhere = array( "tblhosting.userid" => $userID, "tblhostingaddons.status" => 'Active' );
            }
            $allowAccess = false;
            $supportAndUpdatesAddons = array(  );
            $result = select_query('tblhosting', "tblhosting.id,tblproducts.downloads,tblproducts.servertype,tblproducts.configoption7", $servicesWhere, '', '', '', "tblproducts ON tblproducts.id=tblhosting.packageid");
            while( $data = mysql_fetch_array($result) )
            {
                $productServiceID = $data['id'];
                $productDownloads = $data['downloads'];
                $productModule = $data['servertype'];
                $supportAndUpdatesAddon = $data['configoption7'];
                $productDownloadsArray = unserialize($productDownloads);
                if( is_array($productDownloadsArray) && in_array($downloadID, $productDownloadsArray) )
                {
                    if( $productModule == 'licensing' && $supportAndUpdatesAddon && $supportAndUpdatesAddon != "0|None" )
                    {
                        $parts = explode("|", $supportAndUpdatesAddon);
                        $requiredAddonID = (int) $parts[0];
                        if( $requiredAddonID )
                        {
                            $supportAndUpdatesAddons[$productServiceID] = $requiredAddonID;
                        }
                    }
                    else
                    {
                        $allowAccess = true;
                    }
                }
            }
            if( !$allowAccess )
            {
                $result = select_query('tblhostingaddons', "DISTINCT tbladdons.id,tbladdons.downloads", $addonsWhere, '', '', '', "tbladdons ON tbladdons.id=tblhostingaddons.addonid INNER JOIN tblhosting ON tblhosting.id=tblhostingaddons.hostingid");
                while( $data = mysql_fetch_array($result) )
                {
                    $addondownloads = $data['downloads'];
                    $addondownloads = explode(',', $addondownloads);
                    if( in_array($downloadID, $addondownloads) )
                    {
                        $allowAccess = true;
                    }
                }
            }
            if( !$allowAccess && count($supportAndUpdatesAddons) )
            {
                foreach( $supportAndUpdatesAddons as $productServiceID => $requiredAddonID )
                {
                    $requiredAddonName = get_query_val('tbladdons', 'name', array( 'id' => $requiredAddonID ));
                    $where = "tblhosting.userid='" . $userID . "' AND tblhostingaddons.status='Active' AND (tblhostingaddons.name='" . db_escape_string($requiredAddonName) . "' OR tblhostingaddons.addonid='" . $requiredAddonID . "')";
                    if( $serviceID )
                    {
                        $where .= " AND tblhosting.id='" . $serviceID . "'";
                    }
                    $addonCount = get_query_val('tblhostingaddons', "COUNT(tblhostingaddons.id)", $where, '', '', '', "tblhosting ON tblhosting.id=tblhostingaddons.hostingid");
                    if( $addonCount )
                    {
                        $allowAccess = true;
                    }
                }
                if( !$allowAccess )
                {
                    if( $serviceID )
                    {
                        $productServiceID = $serviceID;
                        $requiredAddonID = $supportAndUpdatesAddons[$serviceID];
                    }
                    $pagetitle = $_LANG['downloadstitle'];
                    $breadcrumbnav = "<a href=\"" . $CONFIG['SystemURL'] . "/index.php\">" . $_LANG['globalsystemname'] . "</a> > <a href=\"" . $CONFIG['SystemURL'] . "/downloads.php\">" . $_LANG['downloadstitle'] . "</a>";
                    initialiseClientArea($pagetitle, '', $breadcrumbnav);
                    $smartyvalues['reason'] = 'supportandupdates';
                    $smartyvalues['serviceid'] = $productServiceID;
                    $smartyvalues['licensekey'] = get_query_val('tblhosting', 'domain', array( 'id' => $productServiceID ));
                    $smartyvalues['addonid'] = $requiredAddonID;
                    outputClientArea('downloaddenied');
                    exit();
                }
            }
            if( !$allowAccess )
            {
                $pagetitle = $_LANG['downloadstitle'];
                $breadcrumbnav = "<a href=\"" . $CONFIG['SystemURL'] . "/index.php\">" . $_LANG['globalsystemname'] . "</a> > <a href=\"" . $CONFIG['SystemURL'] . "/downloads.php\">" . $_LANG['downloadstitle'] . "</a>";
                initialiseClientArea($pagetitle, '', $breadcrumbnav);
                if( $serviceID )
                {
                    $result = select_query('tblproducts', "tblproducts.id,tblproducts.name,tblproducts.downloads", array( "tblhosting.id" => $serviceID ), '', '', '', "tblhosting ON tblhosting.packageid=tblproducts.id");
                }
                else
                {
                    $result = select_query('tblproducts', 'id,name,downloads', array( 'downloads' => array( 'sqltype' => 'NEQ', 'value' => '' ) ), "hidden` ASC,`order", 'ASC');
                }
                while( $data = mysql_fetch_array($result) )
                {
                    $downloads = $data['downloads'];
                    $downloads = unserialize($downloads);
                    if( is_array($downloads) && in_array($downloadID, $downloads) )
                    {
                        $smartyvalues['pid'] = $data['id'];
                        $smartyvalues['prodname'] = $data['name'];
                        break;
                    }
                }
                $result = select_query('tbladdons', 'id,name,downloads', array( 'downloads' => array( 'sqltype' => 'NEQ', 'value' => '' ) ));
                while( $data = mysql_fetch_array($result) )
                {
                    $downloads = $data['downloads'];
                    $downloads = explode(',', $downloads);
                    if( in_array($downloadID, $downloads) )
                    {
                        $smartyvalues['aid'] = $data['id'];
                        $smartyvalues['addonname'] = $data['name'];
                        break;
                    }
                }
                if( !$smartyvalues['prodname'] && !$smartyvalues['addonname'] )
                {
                    $smartyvalues['prodname'] = "Unable to Determine Required Product. Please contact support.";
                }
                outputClientArea('downloaddenied');
                exit();
            }
        }
        update_query('tbldownloads', array( 'downloads' => "+1" ), array( 'id' => $id ));
        if( parse_url($filename, PHP_URL_SCHEME) )
        {
            header("Location: " . $filename);
        }
        else
        {
            $fileurl = $downloads_dir . $filename;
            $folder_path = $downloads_dir;
            $file_name = $filename;
            $display_name = $filename;
        }
    }
    else
    {
        if( $type == 'f' )
        {
            $result = select_query('tblclientsfiles', 'userid,filename,adminonly', array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $userid = $data['userid'];
            $file_name = $data['filename'];
            $adminonly = $data['adminonly'];
            $folder_path = $attachments_dir;
            $display_name = substr($file_name, 11);
            if( $userid != $_SESSION['uid'] && !$_SESSION['adminid'] )
            {
                downloadLogin();
            }
            if( !$_SESSION['adminid'] && $adminonly )
            {
                exit( "Permission Denied" );
            }
        }
        else
        {
            if( $type == 'q' )
            {
                if( !$_SESSION['uid'] && !$_SESSION['adminid'] )
                {
                    downloadLogin();
                }
                $result = select_query('tblquotes', 'id,userid', array( 'id' => $id ));
                $data = mysql_fetch_array($result);
                $id = $data['id'];
                $userid = $data['userid'];
                if( $userid != $_SESSION['uid'] && !$_SESSION['adminid'] )
                {
                    exit( "Permission Denied" );
                }
                require(ROOTDIR . "/includes/clientfunctions.php");
                require(ROOTDIR . "/includes/invoicefunctions.php");
                require(ROOTDIR . "/includes/quotefunctions.php");
                $pdfdata = genQuotePDF($id);
                header("Pragma: public");
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0, private");
                header("Cache-Control: private", false);
                header("Content-Type: application/pdf");
                header("Content-Disposition: " . ($viewpdf ? 'inline' : 'attachment') . "; filename=\"" . $_LANG['quotefilename'] . $id . ".pdf\"");
                header("Content-Transfer-Encoding: binary");
                echo $pdfdata;
                exit();
            }
        }
    }
}
if( !trim($folder_path) || !trim($file_name) )
{
    redir('', "index.php");
}
$folder_path_real = realpath($folder_path);
$file_path = $folder_path . $file_name;
$file_path_real = realpath($file_path);
if( $file_path_real === false || strpos($file_path_real, $folder_path_real) !== 0 )
{
    throw new WHMCS_Exception_Fatal(sprintf("File not found. Please contact support.", ''));
}
run_hook('FileDownload', array(  ));
header("Pragma: public");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0, private");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . $display_name . "\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . filesize($file_path_real));
readfile($file_path_real);
function downloadLogin()
{
    global $smartyvalues;
    $whmcs = WHMCS_Application::getinstance();
    $pagetitle = $_LANG['downloadstitle'];
    $breadcrumbnav = "<a href=\"" . $whmcs->getSystemURL() . "\">" . $whmcs->get_lang('globalsystemname') . "</a>" . " > " . "<a href=\"" . $whmcs->getSystemURL() . "downloads.php\">" . $whmcs->get_lang('downloadstitle') . "</a>";
    initialiseClientArea($pagetitle, $pageicon, $breadcrumbnav);
    $goto = 'download';
    require("login.php");
}