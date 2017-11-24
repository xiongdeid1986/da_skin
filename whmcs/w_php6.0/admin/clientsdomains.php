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
$aInt = new WHMCS_Admin("View Clients Domains", false);
$aInt->requiredFiles(array( 'clientfunctions', 'domainfunctions', 'gatewayfunctions', 'registrarfunctions' ));
$aInt->inClientsProfile = true;
if( !$id && $domainid )
{
    $id = $domainid;
}
if( !$userid && !$id )
{
    $userid = get_query_val('tblclients', 'id', '', 'id', 'ASC', '0,1');
}
if( $userid && !$id )
{
    $aInt->valUserID($userid);
    if( !$userid )
    {
        $aInt->gracefulExit("Invalid User ID");
    }
    $id = get_query_val('tbldomains', 'id', array( 'userid' => $userid ), 'domain', 'ASC', '0,1');
}
if( !$id )
{
    $aInt->gracefulExit($aInt->lang('domains', 'nodomainsinfo') . " <a href=\"ordersadd.php?userid=" . $userid . "\">" . $aInt->lang('global', 'clickhere') . "</a> " . $aInt->lang('orders', 'toplacenew'));
}
$domains = new WHMCS_Domains();
$domain_data = $domains->getDomainsDatabyID($id);
$id = $did = $domainid = $domain_data['id'];
$userid = $domain_data['userid'];
$aInt->valUserID($userid);
$aInt->assertClientBoundary($userid);
if( !$id )
{
    $aInt->gracefulExit("Domain ID Not Found");
}
if( $action == 'delete' )
{
    check_token("WHMCS.admin.default");
    checkPermission("Delete Clients Domains");
    run_hook('DomainDelete', array( 'userid' => $userid, 'domainid' => $id ));
    delete_query('tbldomains', array( 'id' => $id ));
    logActivity("Deleted Domain - User ID: " . $userid . " - Domain ID: " . $id);
    redir("userid=" . $userid);
}
if( $action == 'savedomain' && $domain )
{
    check_token("WHMCS.admin.default");
    checkPermission("Edit Clients Domains");
    $conf = 'success';
    $currency = getCurrency($userid);
    $result = select_query('tblpricing', 'msetupfee,qsetupfee,ssetupfee', array( 'type' => 'domainaddons', 'currency' => $currency['id'], 'relid' => 0 ));
    $data = mysql_fetch_array($result);
    $domaindnsmanagementprice = $data['msetupfee'] * $regperiod;
    $domainemailforwardingprice = $data['qsetupfee'] * $regperiod;
    $domainidprotectionprice = $data['ssetupfee'] * $regperiod;
    $result = select_query('tbldomains', 'dnsmanagement,emailforwarding,idprotection,donotrenew', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $olddnsmanagement = $data['dnsmanagement'];
    $oldemailforwarding = $data['emailforwarding'];
    $oldidprotection = $data['idprotection'];
    $olddonotrenew = $data['donotrenew'];
    if( $olddnsmanagement )
    {
        if( !$dnsmanagement )
        {
            $recurringamount -= $domaindnsmanagementprice;
            $conf = 'removeddns';
        }
    }
    else
    {
        if( $dnsmanagement )
        {
            $recurringamount += $domaindnsmanagementprice;
            $conf = 'addeddns';
        }
    }
    if( $oldemailforwarding )
    {
        if( !$emailforwarding )
        {
            $recurringamount -= $domainemailforwardingprice;
            $conf = 'removedemailforward';
        }
    }
    else
    {
        if( $emailforwarding )
        {
            $recurringamount += $domainemailforwardingprice;
            $conf = 'addedemailforward';
        }
    }
    if( $oldidprotection )
    {
        if( !$idprotection )
        {
            $recurringamount -= $domainidprotectionprice;
            $conf = 'removedidprotect';
        }
    }
    else
    {
        if( $idprotection )
        {
            $recurringamount += $domainidprotectionprice;
            $conf = 'addedidprotect';
        }
    }
    if( $autorecalc )
    {
        $domainparts = explode(".", $domain, 2);
        $temppricelist = getTLDPriceList("." . $domainparts[1], '', true, $userid);
        $recurringamount = $temppricelist[$regperiod]['renew'];
        if( $dnsmanagement )
        {
            $recurringamount += $domaindnsmanagementprice;
        }
        if( $emailforwarding )
        {
            $recurringamount += $domainemailforwardingprice;
        }
        if( $idprotection )
        {
            $recurringamount += $domainidprotectionprice;
        }
        if( $promoid )
        {
            $recurringamount -= recalcPromoAmount("D." . $domainparts[1], $userid, $id, $regperiod . 'Years', $recurringamount, $promoid);
        }
    }
    if( !$olddonotrenew && $donotrenew )
    {
        disableAutoRenew($id);
    }
    $table = 'tbldomains';
    $array = array( 'registrationdate' => toMySQLDate($regdate), 'domain' => $domain, 'firstpaymentamount' => $firstpaymentamount, 'recurringamount' => $recurringamount, 'paymentmethod' => $paymentmethod, 'registrar' => $registrar, 'registrationperiod' => $regperiod, 'expirydate' => toMySQLDate($expirydate), 'nextduedate' => toMySQLDate($nextduedate), 'subscriptionid' => $subscriptionid, 'promoid' => $promoid, 'additionalnotes' => $additionalnotes, 'status' => $status, 'dnsmanagement' => $dnsmanagement, 'emailforwarding' => $emailforwarding, 'idprotection' => $idprotection, 'donotrenew' => $donotrenew );
    if( $oldnextduedate != $nextduedate )
    {
        $array['nextinvoicedate'] = toMySQLDate($nextduedate);
        $array['reminders'] = '';
    }
    $where = array( 'id' => $id );
    update_query($table, $array, $where);
    logActivity("Domain Modified - User ID: " . $userid . " - Domain ID: " . $id, $userid);
    if( isset($domainfield) && is_array($domainfield) )
    {
        $additflds = new WHMCS_Domains_AdditionalFields();
        $additflds->setDomain($domain);
        $additflds->setFieldValues($domainfield);
        $additflds->saveToDatabase($id);
    }
    loadRegistrarModule($registrar);
    if( function_exists($registrar . '_AdminDomainsTabFieldsSave') )
    {
        $domainparts = explode(".", $domain, 2);
        $params = array(  );
        $params['domainid'] = $id;
        $params['sld'] = $domainparts[0];
        $params['tld'] = $domainparts[1];
        $params['regperiod'] = $regperiod;
        $params['registrar'] = $registrar;
        $fieldsarray = call_user_func($registrar . '_AdminDomainsTabFieldsSave', $params);
    }
    $newlockstatus = $lockstatus ? 'locked' : 'unlocked';
    run_hook('AdminClientDomainsTabFieldsSave', $_REQUEST);
    run_hook('DomainEdit', array( 'userid' => $userid, 'domainid' => $id ));
    $domainsavetemp = array( 'ns1' => $ns1, 'ns2' => $ns2, 'ns3' => $ns3, 'ns4' => $ns4, 'ns5' => $ns5, 'oldns1' => $oldns1, 'oldns2' => $oldns2, 'oldns3' => $oldns3, 'oldns4' => $oldns4, 'oldns5' => $oldns5, 'defaultns' => $defaultns, 'newlockstatus' => $newlockstatus, 'oldlockstatus' => $oldlockstatus, 'oldidprotection' => $oldidprotection, 'idprotection' => $idprotection );
    WHMCS_Session::set('domainsavetemp', $domainsavetemp);
    redir("userid=" . $userid . "&id=" . $id . "&conf=" . $conf);
}
if( !$id )
{
    $result = select_query('tbldomains', 'id', array( 'userid' => $userid ), 'domain', 'ASC', '0,1');
    $data = mysql_fetch_array($result);
    $id = $data['id'];
}
ob_start();
$did = $domain_data['id'];
$orderid = $domain_data['orderid'];
$ordertype = $domain_data['type'];
$domain = $domain_data['domain'];
$paymentmethod = $domain_data['paymentmethod'];
$gateways = new WHMCS_Gateways();
if( !$paymentmethod || !$gateways->isActiveGateway($paymentmethod) )
{
    $paymentmethod = ensurePaymentMethodIsSet($userid, $id, 'tbldomains');
}
$firstpaymentamount = $domain_data['firstpaymentamount'];
$recurringamount = $domain_data['recurringamount'];
$registrar = $domain_data['registrar'];
$regtype = $domain_data['type'];
$expirydate = $domain_data['expirydate'];
$nextduedate = $domain_data['nextduedate'];
$subscriptionid = $domain_data['subscriptionid'];
$promoid = $domain_data['promoid'];
$registrationdate = $domain_data['registrationdate'];
$registrationperiod = $domain_data['registrationperiod'];
$domainstatus = $domain_data['status'];
$additionalnotes = $domain_data['additionalnotes'];
$dnsmanagement = $domain_data['dnsmanagement'];
$emailforwarding = $domain_data['emailforwarding'];
$idprotection = $domain_data['idprotection'];
$donotrenew = $domain_data['donotrenew'];
if( !$did )
{
    $aInt->gracefulExit($aInt->lang('domains', 'domainidnotfound'));
}
$expirydate = fromMySQLDate($expirydate);
$nextduedate = fromMySQLDate($nextduedate);
$regdate = fromMySQLDate($registrationdate);
echo $aInt->jqueryDialog('renew', $aInt->lang('domains', 'renewdomain'), $aInt->lang('domains', 'renewdomainq'), array( $aInt->lang('global', 'yes') => "window.location='?userid=" . $userid . "&id=" . $id . "&regaction=renew" . generate_token('link') . "'", $aInt->lang('global', 'no') => '' ));
echo $aInt->jqueryDialog('getepp', $aInt->lang('domains', 'requestepp'), $aInt->lang('domains', 'requesteppq'), array( $aInt->lang('global', 'yes') => "window.location='?userid=" . $userid . "&id=" . $id . "&regaction=eppcode" . generate_token('link') . "'", $aInt->lang('global', 'no') => '' ));
echo $aInt->jqueryDialog('reqdelete', $aInt->lang('domains', 'requestdel'), $aInt->lang('domains', 'requestdelq'), array( $aInt->lang('global', 'yes') => "window.location='?userid=" . $userid . "&id=" . $id . "&regaction=reqdelete" . generate_token('link') . "'", $aInt->lang('global', 'no') => '' ));
echo $aInt->jqueryDialog('delete', $aInt->lang('domains', 'delete'), $aInt->lang('domains', 'deleteq'), array( $aInt->lang('global', 'yes') => "window.location='?userid=" . $userid . "&id=" . $id . "&action=delete" . generate_token('link') . "'", $aInt->lang('global', 'no') => '' ));
echo $aInt->jqueryDialog('reldomain', $aInt->lang('domains', 'releasedomain'), $aInt->lang('domains', 'releasedomainq') . "<br /><br />" . $aInt->lang('domains', 'transfertag') . ": <input type=\"text\" id=\"transtag\" size=\"20\" />", array( $aInt->lang('global', 'submit') => "window.location='?userid=" . $userid . "&id=" . $id . "&regaction=release&transtag='+\$(\"#transtag\").val()+'" . generate_token('link') . "'", $aInt->lang('global', 'cancel') => '' ));
echo $aInt->jqueryDialog('idprotectdomain', $aInt->lang('domains', 'idprotection'), $aInt->lang('domains', 'idprotectionq'), array( $aInt->lang('global', 'yes') => "window.location='?userid=" . $userid . "&id=" . $id . "&regaction=idtoggle" . generate_token('link') . "'", $aInt->lang('global', 'no') => '' ));
$domainsavetemp = WHMCS_Session::get('domainsavetemp');
WHMCS_Session::delete('domainsavetemp');
if( $conf && $domainsavetemp )
{
    $ns1 = $domainsavetemp['ns1'];
    $ns2 = $domainsavetemp['ns2'];
    $ns3 = $domainsavetemp['ns3'];
    $ns4 = $domainsavetemp['ns4'];
    $ns5 = $domainsavetemp['ns5'];
    $oldns1 = $domainsavetemp['oldns1'];
    $oldns2 = $domainsavetemp['oldns2'];
    $oldns3 = $domainsavetemp['oldns3'];
    $oldns4 = $domainsavetemp['oldns4'];
    $oldns5 = $domainsavetemp['oldns5'];
    $defaultns = $domainsavetemp['defaultns'];
    $newlockstatus = $domainsavetemp['newlockstatus'];
    $oldlockstatus = $domainsavetemp['oldlockstatus'];
    $oldidprotect = $domainsavetemp['oldidprotection'];
    $idprotect = $domainsavetemp['idprotection'];
}
else
{
    $ns1 = '';
    $ns2 = '';
    $ns3 = '';
    $ns4 = '';
    $ns5 = '';
    $oldns1 = '';
    $oldns2 = '';
    $oldns3 = '';
    $oldns4 = '';
    $oldns5 = '';
    $defaultns = '';
    $newlockstatus = '';
    $oldlockstatus = '';
    $oldidprotect = '';
    $idprotect = '';
}
WHMCS_Session::release();
switch( $conf )
{
    case 'success':
        infoBox($aInt->lang('global', 'changesuccess'), $aInt->lang('global', 'changesuccessdesc'), 'success');
        break;
    case 'addeddns':
        infoBox($aInt->lang('global', 'changesuccess'), $aInt->lang('domains', 'dnsmanagementadded'), 'success');
        break;
    case 'addedemailforward':
        infoBox($aInt->lang('global', 'changesuccess'), $aInt->lang('domains', 'emailforwardingadded'), 'success');
        break;
    case 'addedidprotect':
        infoBox($aInt->lang('global', 'changesuccess'), $aInt->lang('domains', 'idprotectionadded'), 'success');
        break;
    case 'removeddns':
        infoBox($aInt->lang('global', 'changesuccess'), $aInt->lang('domains', 'dnsmanagementremoved'), 'success');
        break;
    case 'removedemailforward':
        infoBox($aInt->lang('global', 'changesuccess'), $aInt->lang('domains', 'emailforwardingremoved'), 'success');
        break;
    case 'removedidprotect':
        infoBox($aInt->lang('global', 'changesuccess'), $aInt->lang('domains', 'idprotectionremoved'), 'success');
        break;
}
$domainregistraractions = checkPermission("Perform Registrar Operations", true) && $domains->getModule() ? true : false;
if( $domainregistraractions )
{
    $domainparts = explode(".", $domain, 2);
    $params = array(  );
    $params['domainid'] = $id;
    $params['sld'] = $domainparts[0];
    $params['tld'] = $domainparts[1];
    $params['regperiod'] = $registrationperiod;
    $params['registrar'] = $registrar;
    $params['regtype'] = $regtype;
    $adminbuttonarray = '';
    loadRegistrarModule($registrar);
    if( function_exists($registrar . '_AdminCustomButtonArray') )
    {
        $adminbuttonarray = call_user_func($registrar . '_AdminCustomButtonArray', $params);
    }
    if( $oldns1 != $ns1 || $oldns2 != $ns2 || $oldns3 != $ns3 || $oldns4 != $ns4 || $oldns5 != $ns5 || $defaultns )
    {
        $nameservers = $defaultns ? $domains->getDefaultNameservers() : array( 'ns1' => $ns1, 'ns2' => $ns2, 'ns3' => $ns3, 'ns4' => $ns4, 'ns5' => $ns5 );
        $success = $domains->moduleCall('SaveNameservers', $nameservers);
        if( !$success )
        {
            infoBox($aInt->lang('domains', 'nschangefail'), $domains->getLastError(), 'error');
        }
        else
        {
            infoBox($aInt->lang('domains', 'nschangesuccess'), $aInt->lang('domains', 'nschangeinfo'), 'success');
        }
    }
    if( !$oldlockstatus )
    {
        $oldlockstatus = $newlockstatus;
    }
    if( $newlockstatus != $oldlockstatus )
    {
        $params['lockenabled'] = $newlockstatus;
        $values = RegSaveRegistrarLock($params);
        if( $values['error'] )
        {
            infoBox($aInt->lang('domains', 'reglockfailed'), $values['error'], 'error');
        }
        else
        {
            infoBox($aInt->lang('domains', 'reglocksuccess'), $aInt->lang('domains', 'reglockinfo'), 'success');
        }
    }
    if( $regaction == 'renew' )
    {
        check_token("WHMCS.admin.default");
        $values = RegRenewDomain($params);
        WHMCS_Cookie::set('DomRenewRes', $values);
        redir("userid=" . $userid . "&id=" . $id . "&conf=renew");
    }
    if( $regaction == 'eppcode' )
    {
        check_token("WHMCS.admin.default");
        $values = RegGetEPPCode($params);
        if( $values['error'] )
        {
            infoBox($aInt->lang('domains', 'eppfailed'), $values['error'], 'error');
        }
        else
        {
            if( $values['eppcode'] )
            {
                infoBox($aInt->lang('domains', 'epprequest'), $_LANG['domaingeteppcodeis'] . " " . $values['eppcode'], 'success');
            }
            else
            {
                infoBox($aInt->lang('domains', 'epprequest'), $_LANG['domaingeteppcodeemailconfirmation'], 'success');
            }
        }
    }
    if( $regaction == 'reqdelete' )
    {
        check_token("WHMCS.admin.default");
        $values = RegRequestDelete($params);
        if( $values['error'] )
        {
            infoBox($aInt->lang('domains', 'deletefailed'), $values['error'], 'error');
        }
        else
        {
            infoBox($aInt->lang('domains', 'deletesuccess'), $aInt->lang('domains', 'deleteinfo'), 'success');
        }
    }
    if( $regaction == 'release' )
    {
        check_token("WHMCS.admin.default");
        $params['transfertag'] = $transtag;
        $values = RegReleaseDomain($params);
        $successmessage = str_replace("%s", $transtag, $aInt->lang('domains', 'releaseinfo'));
        if( $values['error'] )
        {
            infoBox($aInt->lang('domains', 'releasefailed'), $values['error'], 'error');
        }
        else
        {
            infoBox($aInt->lang('domains', 'releasesuccess'), $successmessage, 'success');
        }
    }
    if( $regaction == 'custom' )
    {
        check_token("WHMCS.admin.default");
        $values = RegCustomFunction($params, $ac);
        if( $values['error'] )
        {
            infoBox($aInt->lang('domains', 'registrarerror'), $values['error'], 'error');
        }
        else
        {
            if( !$values['message'] )
            {
                $values['message'] = $aInt->lang('domains', 'changesuccess');
            }
            infoBox($aInt->lang('domains', 'changesuccess'), $values['message'], 'success');
        }
    }
    if( ($conf == 'addedidprotect' || $conf == 'removedidprotect') && ($idprotect || $oldidprotect) )
    {
        $values = RegIDProtectToggle($params);
        if( is_array($values) )
        {
            if( $values['error'] )
            {
                infoBox($aInt->lang('domains', 'idprotectfailed'), $values['error'], 'error');
            }
            else
            {
                infoBox($aInt->lang('domains', 'idprotectsuccess'), $aInt->lang('domains', 'idprotectinfo'), 'success');
            }
        }
    }
    $success = $domains->moduleCall('GetNameservers');
    if( $success )
    {
        $nsvalues = $domains->getModuleReturn();
    }
    else
    {
        if( !$infobox )
        {
            infoBox($aInt->lang('domains', 'registrarerror'), $domains->getLastError(), 'error');
        }
    }
    if( $conf == 'renew' )
    {
        $values = WHMCS_Cookie::get('DomRenewRes', 1);
        if( $values['error'] )
        {
            infoBox($aInt->lang('domains', 'renewfailed'), $values['error'], 'error');
        }
        else
        {
            $successmessage = str_replace("%s", $registrationperiod, $aInt->lang('domains', 'renewinfo'));
            infoBox($aInt->lang('domains', 'renewsuccess'), $successmessage, 'success');
        }
    }
    $success = $domains->moduleCall('GetRegistrarLock');
    if( $success )
    {
        $lockstatus = $domains->getModuleReturn();
    }
}
$clientnotes = array(  );
$result = select_query('tblnotes', "tblnotes.*,(SELECT CONCAT(firstname,' ',lastname) FROM tbladmins WHERE tbladmins.id=tblnotes.adminid) AS adminuser", array( 'userid' => $userid, 'sticky' => '1' ), 'modified', 'DESC');
while( $data = mysql_fetch_assoc($result) )
{
    $data['created'] = fromMySQLDate($data['created'], 1);
    $data['modified'] = fromMySQLDate($data['modified'], 1);
    $data['note'] = autoHyperLink(nl2br($data['note']));
    $clientnotes[] = $data;
}
if( count($clientnotes) )
{
    echo "<div id=\"clientsimportantnotes\">";
    foreach( $clientnotes as $data )
    {
        echo "<div class=\"ticketstaffnotes\">\n    <table class=\"ticketstaffnotestable\">\n        <tr>\n            <td>" . $data['adminuser'] . "</td>\n            <td align=\"right\">" . $data['modified'] . "</td>\n        </tr>\n    </table>\n    <div>\n        " . $data['note'] . "\n        <div style=\"float:right;\"><a href=\"clientsnotes.php?userid=" . $userid . "&action=edit&id=" . $data['id'] . "\"><img src=\"images/edit.gif\" width=\"16\" height=\"16\" align=\"absmiddle\" /></a></div>\n    </div>\n</div>";
    }
    echo "</div>";
}
echo "\n<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>\n<form action=\"";
echo $whmcs->getPhpSelf();
echo "\" method=\"get\">\n<input type=\"hidden\" name=\"userid\" value=\"";
echo $userid;
echo "\">\n";
echo $aInt->lang('clientsummary', 'domains');
echo ": <select name=\"id\" onChange=\"submit();\">\n";
$result = select_query('tbldomains', '', array( 'userid' => $userid ), 'domain', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $domainlistid = $data['id'];
    $domainlistname = $data['domain'];
    $domainliststatus = $data['status'];
    echo "<option value=\"" . $domainlistid . "\"";
    if( $domainlistid == $id )
    {
        echo " selected";
    }
    if( $domainliststatus == 'Pending' )
    {
        echo " style=\"background-color:#ffffcc;\"";
    }
    else
    {
        if( $domainliststatus == 'Expired' || $domainliststatus == 'Cancelled' || $domainliststatus == 'Fraud' )
        {
            echo " style=\"background-color:#ff9999;\"";
        }
    }
    echo ">" . $domainlistname . "</option>";
}
echo "</select> <input type=\"submit\" value=\"";
echo $aInt->lang('global', 'go');
echo "\" class=\"btn btn-success\" />\n</form>\n</td><td align=\"right\">\n<input type=\"button\" onClick=\"window.open('clientsmove.php?type=domain&id=";
echo $id;
echo "','movewindow','width=500,height=200,top=100,left=100');return false\" value=\"";
echo $aInt->lang('services', 'moveservice');
echo "\" class=\"btn\" /> &nbsp;&nbsp;&nbsp;\n</td></tr></table>\n\n";
echo $infobox ? $infobox : "<img src=\"images/spacer.gif\" height=\"10\" width=\"1\" /><br />";
echo "\n<form method=\"post\" action=\"";
echo $whmcs->getPhpSelf();
echo "?action=savedomain&userid=";
echo $userid;
echo "&id=";
echo $id;
echo "\">\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'ordernum');
echo "</td><td class=\"fieldarea\">";
echo $orderid;
echo " - <a href=\"orders.php?action=view&id=";
echo $orderid;
echo "\">";
echo $aInt->lang('orders', 'vieworder');
echo "</a></td><td class=\"fieldlabel\">";
echo $aInt->lang('domains', 'regperiod');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"regperiod\" size=4 value=\"";
echo $registrationperiod;
echo "\"> ";
echo $aInt->lang('domains', 'years');
echo "</td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('orders', 'ordertype');
echo "</td><td class=\"fieldarea\">";
echo $ordertype;
echo "</td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'regdate');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"regdate\" value=\"";
echo $regdate;
echo "\" class=\"datepick\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'domain');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"domain\" size=\"30\" value=\"";
echo $domain;
echo "\"> <a href=\"http://www.";
echo $domain;
echo "\" target=\"_blank\" style=\"color:#cc0000\">www</a> <a href=\"#\" onclick=\"\$('#frmWhois').submit();return false\">";
echo $aInt->lang('domains', 'whois');
echo "</a></td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'expirydate');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"expirydate\" value=\"";
echo $expirydate;
echo "\" class=\"datepick\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'registrar');
echo "</td><td class=\"fieldarea\">";
echo getRegistrarsDropdownMenu($registrar);
echo "</td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'nextduedate');
echo "</td><td class=\"fieldarea\"><input type=\"hidden\" name=\"oldnextduedate\" value=\"";
echo $nextduedate;
echo "\"><input type=\"text\" name=\"nextduedate\" value=\"";
echo $nextduedate;
echo "\" class=\"datepick\"></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'firstpaymentamount');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"firstpaymentamount\" size=10 value=\"";
echo $firstpaymentamount;
echo "\"></td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'paymentmethod');
echo "</td><td class=\"fieldarea\">";
echo paymentMethodsSelection();
echo " <a href=\"clientsinvoices.php?userid=";
echo $userid;
echo "&domainid=";
echo $id;
echo "\">";
echo $aInt->lang('invoices', 'viewinvoices');
echo "</a></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'recurringamount');
echo "</td><td class=\"fieldarea\"><input type=\"text\" name=\"recurringamount\" size=10 value=\"";
echo $recurringamount;
echo "\"> <label><input type=\"checkbox\" name=\"autorecalc\" ";
if( $autorecalcdefault )
{
    echo " checked";
}
echo " /> ";
echo $aInt->lang('services', 'autorecalc');
echo "</label></td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'status');
echo "</td><td class=\"fieldarea\"><select name=\"status\">\n<option value=\"Pending\"";
if( $domainstatus == 'Pending' )
{
    echo " selected";
}
echo ">";
echo $aInt->lang('status', 'pending');
echo "</option>\n<option value=\"Pending Transfer\"";
if( $domainstatus == "Pending Transfer" )
{
    echo " selected";
}
echo ">";
echo $aInt->lang('status', 'pendingtransfer');
echo "</option>\n<option value=\"Active\"";
if( $domainstatus == 'Active' )
{
    echo " selected";
}
echo ">";
echo $aInt->lang('status', 'active');
echo "</option>\n<option value=\"Expired\"";
if( $domainstatus == 'Expired' )
{
    echo " selected";
}
echo ">";
echo $aInt->lang('status', 'expired');
echo "</option>\n<option value=\"Cancelled\"";
if( $domainstatus == 'Cancelled' )
{
    echo " selected";
}
echo ">";
echo $aInt->lang('status', 'cancelled');
echo "</option>\n<option value=\"Fraud\"";
if( $domainstatus == 'Fraud' )
{
    echo " selected";
}
echo ">";
echo $aInt->lang('status', 'fraud');
echo "</option>\n</select></td></tr>\n<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'promocode');
echo "</td><td class=\"fieldarea\"><select name=\"promoid\" style=\"max-width:250px;\"><option value=\"0\">";
echo $aInt->lang('global', 'none');
echo "</option>";
$currency = getCurrency($userid);
$result = select_query('tblpromotions', '', '', 'code', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $promo_id = $data['id'];
    $promo_code = $data['code'];
    $promo_type = $data['type'];
    $promo_recurring = $data['recurring'];
    $promo_value = $data['value'];
    if( $promo_type == 'Percentage' )
    {
        $promo_value .= "%";
    }
    else
    {
        $promo_value = formatCurrency($promo_value);
    }
    if( $promo_type == "Free Setup" )
    {
        $promo_value = $aInt->lang('promos', 'freesetup');
    }
    $promo_recurring = $promo_recurring ? $aInt->lang('status', 'recurring') : $aInt->lang('status', 'onetime');
    if( $promo_type == "Price Override" )
    {
        $promo_recurring = $aInt->lang('promos', 'priceoverride');
    }
    if( $promo_type == "Free Setup" )
    {
        $promo_recurring = '';
    }
    echo "<option value=\"" . $promo_id . "\"";
    if( $promo_id == $promoid )
    {
        echo " selected";
    }
    echo ">" . $promo_code . " - " . $promo_value . " " . $promo_recurring . "</option>";
}
echo "</select> (";
echo $aInt->lang('promotions', 'noaffect');
echo ")</td><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'subscriptionid');
echo "</td><td class=\"fieldarea\"><input type=\"text\" size=\"25\" name=\"subscriptionid\" value=\"";
echo $subscriptionid;
echo "\"></td></tr>\n\n";
if( $domainregistraractions )
{
    if( $domains->hasFunction('GetNameservers') )
    {
        echo "<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('domains', 'nameserver');
        echo " 1</td><td class=\"fieldarea\" colspan=\"3\"><input type=\"text\" name=\"ns1\" value=\"";
        echo $nsvalues['ns1'];
        echo "\" size=\"40\"><input type=\"hidden\" name=\"oldns1\" value=\"";
        echo $nsvalues['ns1'];
        echo "\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('domains', 'nameserver');
        echo " 2</td><td class=\"fieldarea\" colspan=\"3\"><input type=\"text\" name=\"ns2\" value=\"";
        echo $nsvalues['ns2'];
        echo "\" size=\"40\"><input type=\"hidden\" name=\"oldns2\" value=\"";
        echo $nsvalues['ns2'];
        echo "\" /> <input type=\"checkbox\" name=\"defaultns\" id=\"defaultns\" /> <label for=\"defaultns\">";
        echo $aInt->lang('domains', 'resetdefaultns');
        echo "</label></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('domains', 'nameserver');
        echo " 3</td><td class=\"fieldarea\" colspan=\"3\"><input type=\"text\" name=\"ns3\" value=\"";
        echo $nsvalues['ns3'];
        echo "\" size=\"40\"><input type=\"hidden\" name=\"oldns3\" value=\"";
        echo $nsvalues['ns3'];
        echo "\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('domains', 'nameserver');
        echo " 4</td><td class=\"fieldarea\" colspan=\"3\"><input type=\"text\" name=\"ns4\" value=\"";
        echo $nsvalues['ns4'];
        echo "\" size=\"40\"><input type=\"hidden\" name=\"oldns4\" value=\"";
        echo $nsvalues['ns4'];
        echo "\" /></td></tr>\n<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('domains', 'nameserver');
        echo " 5</td><td class=\"fieldarea\" colspan=\"3\"><input type=\"text\" name=\"ns5\" value=\"";
        echo $nsvalues['ns5'];
        echo "\" size=\"40\"><input type=\"hidden\" name=\"oldns5\" value=\"";
        echo $nsvalues['ns5'];
        echo "\" /></td></tr>\n";
    }
    if( $lockstatus )
    {
        echo "<tr><td class=\"fieldlabel\">";
        echo $aInt->lang('domains', 'reglock');
        echo "</td><td class=\"fieldarea\" colspan=\"3\"><input type=\"checkbox\" name=\"lockstatus\"";
        if( $lockstatus == 'locked' )
        {
            echo " checked";
        }
        echo "> ";
        echo $aInt->lang('global', 'ticktoenable');
        echo " <input type=\"hidden\" name=\"oldlockstatus\" value=\"";
        echo $lockstatus;
        echo "\"></td></tr>\n";
    }
    echo "<tr><td class=\"fieldlabel\">";
    echo $aInt->lang('domains', 'registrarcommands');
    echo "</td><td colspan=\"3\">\n";
    if( $domains->hasFunction('RegisterDomain') )
    {
        echo "<input type=\"button\" value=\"";
        echo $aInt->lang('domains', 'actionreg');
        echo "\" class=\"button\" onClick=\"window.location='clientsdomainreg.php?domainid=";
        echo $id;
        echo "'\"> ";
    }
    if( $domains->hasFunction('TransferDomain') )
    {
        echo "<input type=\"button\" value=\"";
        echo $aInt->lang('domains', 'transfer');
        echo "\" class=\"button\" onClick=\"window.location='clientsdomainreg.php?domainid=";
        echo $id;
        echo "&ac=transfer'\"> ";
    }
    if( $domains->hasFunction('RenewDomain') )
    {
        echo "<input type=\"button\" value=\"";
        echo $aInt->lang('domains', 'renew');
        echo "\" class=\"button\" onClick=\"showDialog('renew')\"> ";
    }
    if( $domains->hasFunction('GetContactDetails') )
    {
        echo "<input type=\"button\" value=\"";
        echo $aInt->lang('domains', 'modifydetails');
        echo "\" class=\"button\" onClick=\"window.location='clientsdomaincontacts.php?domainid=";
        echo $id;
        echo "'\"> ";
    }
    if( $domains->hasFunction('GetEPPCode') )
    {
        echo "<input type=\"button\" value=\"";
        echo $aInt->lang('domains', 'getepp');
        echo "\" class=\"button\" onClick=\"showDialog('getepp')\"> ";
    }
    if( $domains->hasFunction('RequestDelete') )
    {
        echo "<input type=\"button\" value=\"";
        echo $aInt->lang('domains', 'requestdelete');
        echo "\" class=\"button\" onClick=\"showDialog('reqdelete')\"> ";
    }
    if( $domains->hasFunction('ReleaseDomain') )
    {
        echo "<input type=\"button\" value=\"";
        echo $aInt->lang('domains', 'releasedomain');
        echo "\" class=\"button\" onClick=\"showDialog('reldomain')\"> ";
    }
    if( $domains->moduleCall('AdminCustomButtonArray') )
    {
        $adminbuttonarray = $domains->getModuleReturn();
        foreach( $adminbuttonarray as $key => $value )
        {
            echo " <input type=\"button\" value=\"";
            echo $key;
            echo "\" class=\"button\" onClick=\"window.location='";
            echo $whmcs->getPhpSelf();
            echo "?userid=";
            echo $userid;
            echo "&id=";
            echo $id;
            echo "&regaction=custom&ac=";
            echo $value . generate_token('link');
            echo "'\">";
        }
    }
    echo "</td></tr>\n";
}
echo "<tr><td class=\"fieldlabel\">";
echo $aInt->lang('domains', 'managementtools');
echo "</td><td class=\"fieldarea\" colspan=\"3\"><input type=\"checkbox\" name=\"dnsmanagement\" id=\"dnsmanagement\"";
if( $dnsmanagement )
{
    echo " checked";
}
echo "> <label for=\"dnsmanagement\">";
echo $aInt->lang('domains', 'dnsmanagement');
echo "</label> <input type=\"checkbox\" name=\"emailforwarding\" id=\"emailforwarding\"";
if( $emailforwarding )
{
    echo " checked";
}
echo "> <label for=\"emailforwarding\">";
echo $aInt->lang('domains', 'emailforwarding');
echo "</label> <input type=\"checkbox\" name=\"idprotection\" id=\"idprotection\"";
if( $idprotection )
{
    echo " checked";
}
echo "> <label for=\"idprotection\">";
echo $aInt->lang('domains', 'idprotection');
echo "</label> <input type=\"checkbox\" name=\"donotrenew\" id=\"donotrenew\"";
if( $donotrenew )
{
    echo " checked";
}
echo "> <label for=\"donotrenew\">";
echo $aInt->lang('domains', 'donotrenew');
echo "</label></td></tr>\n";
$reminderEmails = array( '', 'first', 'second', 'third', 'fourth', 'fifth' );
$reminderEmailOutput = "<tr>\n    <td class=\"fieldlabel\">\n        " . $aInt->lang('domains', 'domainReminders') . "\n    </td>\n    <td class=\"fieldarea\" colspan=\"3\">\n        <div id=\"domainReminders\" style=\"overflow-y:scroll; max-height:100px;\">\n            <table class=\"datatable\" width=\"100%\">\n                <tr>\n                    <th>" . $aInt->lang('fields', 'date') . "</th>\n                    <th>" . $aInt->lang('domains', 'reminder') . "</th>\n                    <th>" . $aInt->lang('emails', 'to') . "</th>\n                    <th>" . $aInt->lang('domains', 'sent') . "</th>\n                </tr>";
foreach( $domains->obtainEmailReminders() as $reminderMail )
{
    $reminderType = $aInt->lang('domains', $reminderEmails[$reminderMail['type']] . 'Reminder');
    if( $reminderMail == 'never' )
    {
        $recipients = $aInt->lang('domains', 'neverSent');
        $reminderDate = $sent = '';
    }
    else
    {
        $reminderDate = fromMySQLDate($reminderMail['date']);
        $recipients = $reminderMail['recipients'];
        $sent = sprintf($aInt->lang('domains', 'beforeExpiry'), $reminderMail['days_before_expiry']);
        if( $reminderMail['days_before_expiry'] < 0 )
        {
            $sent = sprintf($aInt->lang('domains', 'afterExpiry'), $reminderMail['days_before_expiry'] * (0 - 1));
        }
    }
    $reminderEmailOutput .= "<tr align=\"center\">\n    <td>" . $reminderDate . "</td>\n    <td>" . $reminderType . "</td>\n    <td width=\"50%\">" . $recipients . "</td>\n    <td>" . $sent . "</td>\n</tr>";
}
$reportLink = '';
if( checkPermission("View Reports", true) )
{
    $reportLink = sprintf("<input type=\"button\" onclick=\"%s\" value=\"%s\" />", "window.location='reports.php?report=domain_renewal_emails&client=" . $userid . "&domain=" . $domain . "'", $aInt->lang('fields', 'export'));
}
$reminderEmailOutput .= "</table></div>" . $reportLink . "</td></tr>";
echo $reminderEmailOutput;
if( function_exists($registrar . '_AdminDomainsTabFields') )
{
    $fieldsarray = call_user_func($registrar . '_AdminDomainsTabFields', $params);
    if( is_array($fieldsarray) )
    {
        foreach( $fieldsarray as $k => $v )
        {
            echo "<tr><td class=\"fieldlabel\">" . $k . "</td><td class=\"fieldarea\" colspan=\"3\">" . $v . "</td></tr>";
        }
    }
}
$hookret = run_hook('AdminClientDomainsTabFields', array( 'id' => $id ));
foreach( $hookret as $hookdat )
{
    foreach( $hookdat as $k => $v )
    {
        echo "<td class=\"fieldlabel\">" . $k . "</td><td class=\"fieldarea\" colspan=\"3\">" . $v . "</td></tr>";
    }
}
$additflds = new WHMCS_Domains_AdditionalFields();
$additflds->setDomain($domain);
$additflds->getFieldValuesFromDatabase($id);
foreach( $additflds->getFieldsForOutput() as $fieldLabel => $inputHTML )
{
    echo "<tr><td class=\"fieldlabel\">" . $fieldLabel . "</td><td class=\"fieldarea\" colspan=\"3\">" . $inputHTML . "</td></tr>";
}
echo "<tr><td class=\"fieldlabel\">";
echo $aInt->lang('fields', 'adminnotes');
echo "</td><td class=\"fieldarea\" colspan=\"3\"><textarea name=\"additionalnotes\" rows=4 style=\"width:100%;\">";
echo $additionalnotes;
echo "</textarea></td></tr>\n</table>\n\n<img src=\"images/spacer.gif\" height=\"10\" width=\"1\" /><br />\n<div align=\"center\"><input type=\"submit\" value=\"";
echo $aInt->lang('global', 'savechanges');
echo "\" class=\"btn btn-primary\" /> <input type=\"reset\" value=\"";
echo $aInt->lang('global', 'cancelchanges');
echo "\" class=\"btn\" /><br /><a href=\"#\" onClick=\"showDialog('delete');return false\" style=\"color:#cc0000\"><strong>";
echo $aInt->lang('global', 'delete');
echo "</strong></a></div>\n</form>\n\n<br>\n\n<form action=\"clientsemails.php?userid=";
echo $userid;
echo "\" method=\"post\">\n<input type=\"hidden\" name=\"action\" value=\"send\">\n<input type=\"hidden\" name=\"type\" value=\"domain\">\n<input type=\"hidden\" name=\"id\" value=\"";
echo $id;
echo "\">\n<div class=\"contentbox\">";
echo "<B>" . $aInt->lang('global', 'sendmessage') . "</B> <select name=\"messagename\"><option value=\"newmessage\">" . $aInt->lang('emails', 'newmessage') . "</option>";
$result = select_query('tblemailtemplates', '', array( 'type' => 'domain', 'language' => '' ), 'name', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $messagename = $data['name'];
    $custom = $data['custom'];
    echo "<option value=\"" . $messagename . "\"";
    if( $custom == '1' )
    {
        echo " style=\"background-color:#efefef\"";
    }
    echo ">" . $messagename . "</option>";
}
echo "</select> <input type=\"submit\" value=\"" . $aInt->lang('global', 'sendmessage') . "\">";
echo "</div>\n</form>\n";
echo "\n<form method=\"post\" action=\"whois.php\" target=\"_blank\" id=\"frmWhois\">\n<input type=\"hidden\" name=\"domain\" value=\"" . $domain . "\" />\n</form>\n";
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();