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
$whmcspath = "../";
require(dirname(__FILE__) . "/config.php");
require($whmcspath . "/init.php");
require(ROOTDIR . "/includes/registrarfunctions.php");
$cron = WHMCS_Cron::init();
$cron->raiseLimits();
WHMCS_Session::release();
$cronreport = "Domain Synchronisation Cron Report for " . date("d-m-Y H:i:s") . "<br />\n<br />\n";
if( !$CONFIG['DomainSyncEnabled'] )
{
    logActivity("Domain Sync Cron: Disabled. Run Aborted.");
    exit();
}
$registrarconfigops = array(  );
logActivity("Domain Sync Cron: Starting");
$transfersreport = '';
$result = select_query('tbldomains', 'id,domain,registrar,registrationperiod,status,dnsmanagement,emailforwarding,idprotection', "registrar!='' AND status='Pending Transfer'", 'id', 'ASC');
$curlerrorregistrars = array(  );
while( $data = mysql_fetch_array($result) )
{
    $domainid = $data['id'];
    $domain = $data['domain'];
    $registrar = $data['registrar'];
    $regperiod = $data['registrationperiod'];
    $status = $data['status'];
    $domainparts = explode(".", $domain, 2);
    $params = is_array($registrarconfigops[$registrar]) ? $registrarconfigops[$registrar] : $registrarconfigops[$registrar];
    $params['domainid'] = $domainid;
    $params['domain'] = $domain;
    $params['sld'] = $domainparts[0];
    $params['tld'] = $domainparts[1];
    $params['registrar'] = $registrar;
    $params['regperiod'] = $regperiod;
    $params['status'] = $status;
    $params['dnsmanagement'] = $data['dnsmanagement'];
    $params['emailforwarding'] = $data['emailforwarding'];
    $params['idprotection'] = $data['idprotection'];
    loadRegistrarModule($registrar);
    if( function_exists($registrar . '_TransferSync') && !in_array($registrar, $curlerrorregistrars) )
    {
        $transfersreport .= " - " . $domain . ": ";
        $updateqry = array(  );
        $response = call_user_func($registrar . '_TransferSync', $params);
        if( !$response['error'] )
        {
            if( $response['active'] || $response['completed'] )
            {
                $transfersreport .= "Transfer Completed";
                $updateqry['status'] = 'Active';
                if( !$response['expirydate'] && function_exists($registrar . '_Sync') && !in_array($registrar, $curlerrorregistrars) )
                {
                    $response = call_user_func($registrar . '_Sync', $params);
                }
                if( $response['expirydate'] )
                {
                    $updateqry['expirydate'] = $response['expirydate'];
                    $transfersreport .= " - In Sync";
                }
                if( $CONFIG['DomainSyncNextDueDate'] && $response['expirydate'] )
                {
                    $newexpirydate = $response['expirydate'];
                    if( $CONFIG['DomainSyncNextDueDateDays'] )
                    {
                        $newexpirydate = explode('-', $newexpirydate);
                        $newexpirydate = date('Y-m-d', mktime(0, 0, 0, $newexpirydate[1], $newexpirydate[2] - $CONFIG['DomainSyncNextDueDateDays'], $newexpirydate[0]));
                    }
                    $updateqry['nextinvoicedate'] = $newexpirydate;
                    $updateqry['nextduedate'] = $updateqry['nextinvoicedate'];
                }
            }
            else
            {
                if( $response['failed'] )
                {
                    $transfersreport .= "Transfer Failed";
                    $updateqry['status'] = 'Cancelled';
                    $failurereason = $response['reason'];
                    if( !$failurereason )
                    {
                        $failurereason = $_LANG['domaintrffailreasonunavailable'];
                    }
                    sendMessage("Domain Transfer Failed", $domainid, array( 'domain_transfer_failure_reason' => $failurereason ));
                }
                else
                {
                    $transfersreport .= "Transfer Still In Progress";
                }
            }
            if( !$CONFIG['DomainSyncNotifyOnly'] && count($updateqry) )
            {
                update_query('tbldomains', $updateqry, array( 'id' => $domainid ));
                if( $updateqry['status'] == 'Active' )
                {
                    sendMessage("Domain Transfer Completed", $domainid);
                }
            }
        }
        else
        {
            if( $response['error'] && strtolower(substr($response['error'], 0, 4)) == 'curl' )
            {
                if( !in_array($registrar, $curlerrorregistrars) )
                {
                    $curlerrorregistrars[] = $registrar;
                }
                $transfersreport .= "Error: " . $response['error'];
            }
            else
            {
                if( $response['error'] )
                {
                    $transfersreport .= "Error: " . $response['error'];
                }
            }
        }
        $transfersreport .= "<br />\n";
    }
}
if( $transfersreport )
{
    $cronreport .= "Transfer Status Checks<br />\n" . $transfersreport . "<br />\n";
}
$cronreport .= "Active Domain Syncs<br />\n";
$totalunsynced = get_query_val('tbldomains', "COUNT(id)", "registrar!='' AND status='Active' AND synced=0", 'id', 'ASC', '0,50');
if( !$totalunsynced )
{
    update_query('tbldomains', array( 'synced' => '0' ), '');
}
$result = select_query('tbldomains', 'id,domain,expirydate,nextduedate,registrar,status', "registrar!='' AND status='Active' AND synced=0", "status` DESC, `id", 'ASC', '0,50');
while( $data = mysql_fetch_array($result) )
{
    $domainid = $data['id'];
    $domain = $data['domain'];
    $registrar = $data['registrar'];
    $expirydate = $data['expirydate'];
    $nextduedate = $data['nextduedate'];
    $status = $data['status'];
    $domainparts = explode(".", $domain, 2);
    $params = is_array($registrarconfigops[$registrar]) ? $registrarconfigops[$registrar] : $registrarconfigops[$registrar];
    $params['domainid'] = $domainid;
    $params['domain'] = $domain;
    $params['sld'] = $domainparts[0];
    $params['tld'] = $domainparts[1];
    $params['registrar'] = $registrar;
    $params['status'] = $status;
    loadRegistrarModule($registrar);
    $updateqry = array(  );
    $updateqry['synced'] = '1';
    $response = $synceditems = array(  );
    if( function_exists($registrar . '_Sync') && !in_array($registrar, $curlerrorregistrars) )
    {
        $response = call_user_func($registrar . '_Sync', $params);
        if( !$response['error'] )
        {
            if( $response['active'] && $status != 'Active' )
            {
                $updateqry['status'] = 'Active';
                $synceditems[] = "Status Changed to Active";
            }
            if( $response['expired'] && $status != 'Expired' )
            {
                $updateqry['status'] = 'Expired';
                $synceditems[] = "Status Changed to Expired";
            }
            if( $response['cancelled'] && $status == 'Active' )
            {
                $updateqry['status'] = 'Cancelled';
                $synceditems[] = "Status Changed to Cancelled";
            }
            if( $response['expirydate'] && $expirydate != $response['expirydate'] )
            {
                $updateqry['expirydate'] = $response['expirydate'];
                $synceditems[] = "Expiry Date updated to " . fromMySQLDate($response['expirydate']);
            }
            if( $CONFIG['DomainSyncNextDueDate'] && $response['expirydate'] )
            {
                $newexpirydate = $response['expirydate'];
                if( $CONFIG['DomainSyncNextDueDateDays'] )
                {
                    $newexpirydate = explode('-', $newexpirydate);
                    $newexpirydate = date('Y-m-d', mktime(0, 0, 0, $newexpirydate[1], $newexpirydate[2] - $CONFIG['DomainSyncNextDueDateDays'], $newexpirydate[0]));
                }
                if( $newexpirydate != $nextduedate )
                {
                    $updateqry['nextinvoicedate'] = $newexpirydate;
                    $updateqry['nextduedate'] = $updateqry['nextinvoicedate'];
                    $synceditems[] = "Next Due Date updated to " . fromMySQLDate($newexpirydate);
                }
            }
        }
    }
    if( $CONFIG['DomainSyncNotifyOnly'] )
    {
        $updateqry = array( 'synced' => '1' );
    }
    update_query('tbldomains', $updateqry, array( 'id' => $domainid ));
    $cronreport .= " - " . $domain . ": ";
    if( !count($response) )
    {
        if( in_array($registrar, $curlerrorregistrars) )
        {
            $cronreport .= "Sync Skipped Due to cURL Error";
        }
        else
        {
            $cronreport .= "Sync Not Supported by Registrar Module";
        }
    }
    else
    {
        if( $response['error'] && strtolower(substr($response['error'], 0, 4)) == 'curl' )
        {
            if( !in_array($registrar, $curlerrorregistrars) )
            {
                $curlerrorregistrars[] = $registrar;
            }
            $cronreport .= "Error: " . $response['error'];
        }
        else
        {
            if( $response['error'] )
            {
                $cronreport .= "Error: " . $response['error'];
            }
            else
            {
                if( !function_exists($registrar . '_TransfersSync') && $status == "Pending Transfer" && $response['active'] )
                {
                    sendMessage("Domain Transfer Completed", $domainid);
                }
                $cronreport .= count($synceditems) ? ($CONFIG['DomainSyncNotifyOnly'] ? "Out of Sync " : "Updated ") . implode(", ", $synceditems) : "In Sync";
            }
        }
    }
    $cronreport .= "<br />\n";
}
logActivity("Domain Sync Cron: Completed");
sendAdminNotification('system', "WHMCS Domain Synchronisation Cron Report", $cronreport);