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
require("../../../init.php");
$result = select_query('mod_licensing', '', array( 'licensekey' => $_POST['licensekey'] ));
$data = mysql_fetch_array($result);
$licenseid = $data['id'];
$serviceid = $data['serviceid'];
$validdomain = $data['validdomain'];
$validip = $data['validip'];
$validdirectory = $data['validdirectory'];
$reissues = $data['reissues'];
$status = $data['status'];
$result = select_query('tblhosting', "tblproducts.id,tblproducts.configoption4,tblproducts.configoption5,tblproducts.configoption6,tblproducts.configoption8,tblproducts.configoption9", array( "tblhosting.id" => $serviceid ), '', '', '', "tblproducts ON tblhosting.packageid=tblproducts.id");
$data = mysql_fetch_array($result);
$pid = $data['id'];
$allowdomainconflict = $data['configoption4'];
$allowipconflict = $data['configoption5'];
$allowdirectoryconflict = $data['configoption6'];
$licensing_secretkey = $data['configoption8'];
$licensing_freetrial = $data['configoption9'];
if( !$ip )
{
    $ip = $_SERVER['REMOTE_ADDR'];
}
if( !$licenseid )
{
    echo "<status>Invalid</status>";
    insert_query('mod_licensinglog', array( 'licenseid' => $licenseid, 'domain' => $_POST['domain'], 'ip' => $_POST['ip'], 'path' => $_POST['dir'], 'message' => "Invalid Key - " . $_POST['licensekey'], 'datetime' => "now()" ));
    exit();
}
update_query('mod_licensing', array( 'lastaccess' => "now()" ), array( 'id' => $licenseid ));
if( $status == 'Expired' )
{
    echo "<status>Expired</status>";
    licensing_getlicreturndata($licenseid);
    insert_query('mod_licensinglog', array( 'licenseid' => $licenseid, 'domain' => $_POST['domain'], 'ip' => $_POST['ip'], 'path' => $_POST['dir'], 'message' => "License Expired", 'datetime' => "now()" ));
    exit();
}
if( $status == 'Suspended' )
{
    echo "<status>Suspended</status>";
    licensing_getlicreturndata($licenseid);
    insert_query('mod_licensinglog', array( 'licenseid' => $licenseid, 'domain' => $_POST['domain'], 'ip' => $_POST['ip'], 'path' => $_POST['dir'], 'message' => "License Suspended", 'datetime' => "now()" ));
    exit();
}
if( $status == 'Reissued' )
{
    if( substr($domain, 0, 4) == "www." )
    {
        $domain = substr($domain, 4);
    }
    $validdomain = $domain . ",www." . $domain;
    $validip = $ip;
    $validdirectory = $dir;
    update_query('mod_licensing', array( 'validdomain' => $validdomain, 'validip' => $validip, 'validdirectory' => $validdirectory, 'status' => 'Active' ), array( 'id' => $licenseid ));
    if( 0 < $reissues )
    {
        insert_query('mod_licensinglog', array( 'licenseid' => $licenseid, 'domain' => $_POST['domain'], 'ip' => $_POST['ip'], 'path' => $_POST['dir'], 'message' => "License Reissued", 'datetime' => "now()" ));
    }
}
if( $status == 'Reissued' || $status == 'Active' )
{
    if( $licensing_freetrial )
    {
        $trialmatches = array(  );
        $result = select_query('mod_licensing', "mod_licensing.*", "mod_licensing.id!=" . (int) $licenseid . " AND tblhosting.packageid=" . (int) $pid . " AND mod_licensing.validdomain LIKE '%" . db_escape_string($domain) . "%' AND mod_licensing.validdomain!=''", '', '', '', "tblhosting ON tblhosting.id=mod_licensing.serviceid");
        while( $data = mysql_fetch_array($result) )
        {
            $triallicenseid = $data['id'];
            $trialvaliddomains = explode(',', $data['validdomain']);
            if( in_array($domain, $trialvaliddomains) )
            {
                $trialmatches[] = $triallicenseid;
            }
        }
        if( count($trialmatches) )
        {
            echo "<status>Suspended</status>";
            licensing_getlicreturndata($licenseid);
            update_query('mod_licensing', array( 'status' => 'Suspended' ), array( 'id' => $licenseid ));
            update_query('tblhosting', array( 'status' => 'Suspended', 'suspendreason' => "Duplicate Free Trial Use" ), array( 'id' => $serviceid ));
            insert_query('mod_licensinglog', array( 'licenseid' => $licenseid, 'domain' => $_POST['domain'], 'ip' => $_POST['ip'], 'path' => $_POST['dir'], 'message' => "License Suspended for Duplicate Trials Use (" . implode(',', $trialmatches) . ")", 'datetime' => "now()" ));
            exit();
        }
    }
    $result = select_query('mod_licensingbans', '', array( 'value' => $domain ));
    $data = mysql_fetch_array($result);
    $banid = $data['id'];
    $bannotes = $data['notes'];
    if( $banid )
    {
        echo "<status>Suspended</status>";
        licensing_getlicreturndata($licenseid);
        update_query('mod_licensing', array( 'status' => 'Suspended' ), array( 'id' => $licenseid ));
        update_query('tblhosting', array( 'status' => 'Suspended', 'suspendreason' => "Banned Domain/IP" ), array( 'id' => $serviceid ));
        insert_query('mod_licensinglog', array( 'licenseid' => $licenseid, 'domain' => $_POST['domain'], 'ip' => $_POST['ip'], 'path' => $_POST['dir'], 'message' => "Banned Domain/IP (" . $bannotes . ")", 'datetime' => "now()" ));
        exit();
    }
}
$validdomains = licensing_explode($validdomain);
$validips = licensing_explode($validip);
$validdirs = licensing_explode($validdirectory);
if( !$allowdomainconflict && !in_array($domain, $validdomains) )
{
    echo "<status>Invalid</status>\n<message>Domain Invalid</message>";
    insert_query('mod_licensinglog', array( 'licenseid' => $licenseid, 'domain' => $_POST['domain'], 'ip' => $_POST['ip'], 'path' => $_POST['dir'], 'message' => "Domain Invalid", 'datetime' => "now()" ));
}
else
{
    if( !$allowipconflict && !in_array($ip, $validips) )
    {
        echo "<status>Invalid</status>\n<message>IP Address Invalid</message>";
        insert_query('mod_licensinglog', array( 'licenseid' => $licenseid, 'domain' => $_POST['domain'], 'ip' => $_POST['ip'], 'path' => $_POST['dir'], 'message' => "IP Address Invalid", 'datetime' => "now()" ));
    }
    else
    {
        if( !$allowdirectoryconflict && !in_array($dir, $validdirs) )
        {
            echo "<status>Invalid</status>\n<message>Directory Invalid</message>";
            insert_query('mod_licensinglog', array( 'licenseid' => $licenseid, 'domain' => $_POST['domain'], 'ip' => $_POST['ip'], 'path' => $_POST['dir'], 'message' => "Directory Invalid", 'datetime' => "now()" ));
        }
        else
        {
            echo "<status>Active</status>";
            licensing_getlicreturndata($licenseid);
        }
    }
}
function licensing_explode($vals)
{
    $vals = explode(',', $vals);
    foreach( $vals as $k => $v )
    {
        $vals[$k] = trim($v, " \t\n\r");
    }
    return $vals;
}
function licensing_getlicreturndata($licenseid)
{
    global $licensing_secret_key;
    global $licensing_secretkey;
    $result = select_query('mod_licensing', '', array( 'id' => $licenseid ));
    $data = mysql_fetch_array($result);
    $serviceid = $data['serviceid'];
    $licensekey = $data['licensekey'];
    $validdomain = $data['validdomain'];
    $validip = $data['validip'];
    $validdirectory = $data['validdirectory'];
    $status = $data['status'];
    $validdomain = implode(',', licensing_explode($validdomain));
    $validip = implode(',', licensing_explode($validip));
    $validdirectory = implode(',', licensing_explode($validdirectory));
    $result = select_query('tblhosting', '', array( 'id' => $serviceid ));
    $data = mysql_fetch_array($result);
    $productid = ltrim($data['packageid'], '0');
    $nextduedate = $data['nextduedate'];
    $regdate = $data['regdate'];
    $billingcycle = $data['billingcycle'];
    $userid = $data['userid'];
    $result = select_query('tblproducts', '', array( 'id' => $productid ));
    $data = mysql_fetch_array($result);
    $productname = $data['name'];
    $result = select_query('tblclients', '', array( 'id' => $userid ));
    $data = mysql_fetch_array($result);
    $firstname = $data['firstname'];
    $lastname = $data['lastname'];
    $companyname = $data['companyname'];
    $email = $data['email'];
    $configoptions = '';
    $result = full_query("SELECT tblproductconfigoptions.optionname, tblproductconfigoptions.optiontype, tblproductconfigoptionssub.optionname, tblhostingconfigoptions.qty FROM tblhostingconfigoptions INNER JOIN tblproductconfigoptions ON tblproductconfigoptions.id = tblhostingconfigoptions.configid INNER JOIN tblproductconfigoptionssub ON tblproductconfigoptionssub.id = tblhostingconfigoptions.optionid INNER JOIN tblhosting ON tblhosting.id=tblhostingconfigoptions.relid INNER JOIN tblproductconfiglinks ON tblproductconfiglinks.gid=tblproductconfigoptions.gid WHERE tblhostingconfigoptions.relid=" . (int) $serviceid . " AND tblproductconfiglinks.pid=tblhosting.packageid ORDER BY tblproductconfigoptions.`order`,tblproductconfigoptions.id ASC");
    while( $data = mysql_fetch_array($result) )
    {
        if( $data[1] == '3' )
        {
            if( $data[3] )
            {
                $data[2] = 'Yes';
            }
            else
            {
                $data[2] = '';
            }
        }
        else
        {
            if( $data[1] == '4' )
            {
                $data[2] = $data[3];
            }
        }
        $configoptions .= $data[0] . "=" . $data[2] . "|";
    }
    $configoptions = substr($configoptions, 0, 0 - 1);
    $customfields = '';
    $result = full_query("SELECT tblcustomfields.fieldname,tblcustomfieldsvalues.value FROM tblcustomfields,tblcustomfieldsvalues WHERE tblcustomfields.id=tblcustomfieldsvalues.fieldid AND tblcustomfields.type='product' AND tblcustomfieldsvalues.relid=" . (int) $serviceid);
    while( $data = mysql_fetch_array($result) )
    {
        $customfields .= $data[0] . "=" . $data[1] . "|";
    }
    $customfields = substr($customfields, 0, 0 - 1);
    $addons = '';
    $result = full_query("SELECT addonid, name, nextduedate, status FROM tblhostingaddons WHERE tblhostingaddons.hostingid=" . (int) $serviceid);
    while( $data = mysql_fetch_assoc($result) )
    {
        if( !$data['name'] )
        {
            $result2 = select_query('tbladdons', 'name', array( 'id' => $data['addonid'] ));
            $data2 = mysql_fetch_assoc($result2);
            $data['name'] = $data2['name'];
        }
        $addons .= "name=" . $data['name'] . ";nextduedate=" . $data['nextduedate'] . ";status=" . $data['status'] . "|";
    }
    $addons = substr($addons, 0, 0 - 1);
    $md5hash = isset($_POST['check_token']) ? md5($licensing_secretkey . $_POST['check_token']) : '';
    $xmlresp = "\n<registeredname>" . $firstname . " " . $lastname . "</registeredname>\n<companyname>" . $companyname . "</companyname>\n<email>" . $email . "</email>\n<serviceid>" . $serviceid . "</serviceid>\n<productid>" . $productid . "</productid>\n<productname>" . $productname . "</productname>\n<regdate>" . $regdate . "</regdate>\n<nextduedate>" . $nextduedate . "</nextduedate>\n<billingcycle>" . $billingcycle . "</billingcycle>\n<validdomain>" . $validdomain . "</validdomain>\n<validip>" . $validip . "</validip>\n<validdirectory>" . $validdirectory . "</validdirectory>\n<configoptions>" . $configoptions . "</configoptions>\n<customfields>" . $customfields . "</customfields>\n<addons>" . $addons . "</addons>\n<md5hash>" . $md5hash . "</md5hash>";
    echo $xmlresp;
    $hookresults = run_hook('LicensingAddonVerify', array( 'licenseid' => $licenseid, 'serviceid' => $serviceid, 'xmlresponse' => "<status>Active</status>\n" . $xmlresp ));
    foreach( $hookresults as $hookmergefields )
    {
        foreach( $hookmergefields as $k => $v )
        {
            echo "<" . $k . ">" . $v . "</" . $k . ">\n";
        }
    }
}