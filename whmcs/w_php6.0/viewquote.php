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
require("includes/gatewayfunctions.php");
require("includes/quotefunctions.php");
require("includes/invoicefunctions.php");
require("includes/clientfunctions.php");
require("includes/countries.php");
$whmcs = WHMCS_Application::getinstance();
$whmcsAppConfig = $whmcs->getApplicationConfig();
$id = (int) $whmcs->get_req_var('id');
if( !isset($_SESSION['uid']) && !isset($_SESSION['adminid']) )
{
    $pagetitle = $_LANG['clientareatitle'];
    $pageicon = "images/clientarea_big.gif";
    $pagetitle = $_LANG['quotestitle'];
    $breadcrumbnav = "<a href=\"index.php\">" . $_LANG['globalsystemname'] . "</a> > <a href=\"clientarea.php\">" . $_LANG['clientareatitle'] . "</a> > <a href=\"clientarea.php?action=quotes\">" . $_LANG['quotes'] . "</a> > <a href=\"viewquote.php?id=" . $id . "\">" . $pagetitle . "</a>";
    initialiseClientArea($pagetitle, $pageicon, $breadcrumbnav);
    $goto = 'viewquote';
    require("login.php");
    exit();
}
$smarty = new WHMCS_Smarty();
$smarty->assign('template', $CONFIG['Template']);
$smarty->assign('LANG', $_LANG);
$smarty->assign('logo', $CONFIG['LogoURL']);
if( $action == 'accept' )
{
    if( !$agreetos && $CONFIG['EnableTOSAccept'] )
    {
        $smarty->assign('agreetosrequired', true);
    }
    else
    {
        if( get_query_val('tblquotes', '', array( 'id' => $id, 'userid' => $_SESSION['uid'], 'stage' => array( 'sqltype' => 'NEQ', 'value' => 'Draft' ), 'stage' => array( 'sqltype' => 'NEQ', 'value' => 'Accepted' ) )) )
        {
            update_query('tblquotes', array( 'stage' => 'Accepted', 'dateaccepted' => "now()" ), array( 'id' => $id ));
            logActivity("Quote Accepted - Quote ID: " . $id);
            $quote_data = get_query_vals('tblquotes', "*", array( 'id' => $id ));
            if( $quote_data['userid'] )
            {
                $clientsdetails = getClientsDetails($quote_data['userid'], 'billing');
            }
            else
            {
                $clientsdetails = $quote_data;
            }
            $pdfdata = genQuotePDF($id);
            $messageArr = array( 'emailquote' => true, 'quote_number' => $id, 'quote_subject' => $quote_data['subject'], 'quote_date_created' => $quote_data['datecreated'], 'invoice_num' => '', 'client_first_name' => $clientsdetails['firstname'], 'client_last_name' => $clientsdetails['lastname'], 'client_company_name' => $clientsdetails['companyname'], 'client_email' => $clientsdetails['email'], 'client_address1' => $clientsdetails['address1'], 'client_address2' => $clientsdetails['address2'], 'client_city' => $clientsdetails['city'], 'client_state' => $clientsdetails['state'], 'client_postcode' => $clientsdetails['postcode'], 'client_country' => $clientsdetails['country'], 'client_phonenumber' => $clientsdetails['phonenumber'], 'client_id' => $clientsdetails['userid'], 'client_language' => $clientsdetails['language'], 'quoteattachmentdata' => $pdfdata );
            sendMessage("Quote Accepted", $_SESSION['uid'], $messageArr);
            sendAdminMessage("Quote Accepted Notification", array( 'quote_number' => $id, 'quote_subject' => $quote_data['subject'], 'quote_date_created' => $quote_data['datecreated'], 'client_id' => $vars['userid'], 'clientname' => $clientsdetails['firstname'] . " " . $clientsdetails['lastname'], 'client_email' => $clientsdetails['email'], 'client_company_name' => $clientsdetails['companyname'], 'client_address1' => $clientsdetails['address1'], 'client_address2' => $clientsdetails['address2'], 'client_city' => $clientsdetails['city'], 'client_state' => $clientsdetails['state'], 'client_postcode' => $clientsdetails['postcode'], 'client_country' => $clientsdetails['country'], 'client_phonenumber' => $clientsdetails['phonenumber'], 'client_ip' => $clientsdetails['ip'], 'client_hostname' => $clientsdetails['host'] ), 'account');
            run_hook('acceptQuote', array( 'quoteid' => $id, 'invoiceid' => $invoiceid ));
        }
        else
        {
            $smarty->assign('error', 'on');
            $template_output = $smarty->fetch($whmcs->getClientAreaTplName() . "/viewquote.tpl");
            echo $template_output;
            exit();
        }
    }
}
if( isset($_SESSION['adminid']) )
{
    $result = select_query('tblquotes', '', array( 'id' => $id ));
}
else
{
    $result = select_query('tblquotes', '', array( 'id' => $id, 'userid' => $_SESSION['uid'], 'stage' => array( 'sqltype' => 'NEQ', 'value' => 'Draft' ) ));
}
$data = mysql_fetch_array($result);
$id = $data['id'];
$stage = $data['stage'];
$userid = $data['userid'];
$date = $data['datecreated'];
$validuntil = $data['validuntil'];
$subtotal = $data['subtotal'];
$total = $data['total'];
$status = $data['status'];
$proposal = $data['proposal'];
$notes = $data['customernotes'];
$currency = $data['currency'];
if( !$id )
{
    $smarty->assign('error', 'on');
    $template_output = $smarty->fetch($whmcs->getClientAreaTplName() . "/viewquote.tpl");
    echo $template_output;
    exit();
}
$currency = getCurrency($userid, $currency);
$date = fromMySQLDate($date, 0, 1);
$validuntil = fromMySQLDate($validuntil, 0, 1);
if( $userid )
{
    $clientsdetails = getClientsDetails($userid, 'billing');
}
else
{
    $clientsdetails = array(  );
    $clientsdetails['firstname'] = $data['firstname'];
    $clientsdetails['lastname'] = $data['lastname'];
    $clientsdetails['companyname'] = $data['companyname'];
    $clientsdetails['email'] = $data['email'];
    $clientsdetails['address1'] = $data['address1'];
    $clientsdetails['address2'] = $data['address2'];
    $clientsdetails['city'] = $data['city'];
    $clientsdetails['state'] = $data['state'];
    $clientsdetails['postcode'] = $data['postcode'];
    $clientsdetails['country'] = $data['country'];
    $clientsdetails['phonenumber'] = $data['phonenumber'];
}
if( $CONFIG['TaxEnabled'] )
{
    $tax = $data['tax1'];
    $tax2 = $data['tax2'];
    $taxdata = getTaxRate(1, $clientsdetails['state'], $clientsdetails['country']);
    $smarty->assign('taxname', $taxdata['name']);
    $smarty->assign('taxrate', $taxdata['rate']);
    $taxdata2 = getTaxRate(2, $clientsdetails['state'], $clientsdetails['country']);
    $smarty->assign('taxname2', $taxdata2['name']);
    $smarty->assign('taxrate2', $taxdata2['rate']);
}
$clientsdetails['country'] = $countries[$clientsdetails['country']];
$smarty->assign('clientsdetails', $clientsdetails);
$smarty->assign('companyname', $CONFIG['CompanyName']);
$smarty->assign('pagetitle', $_LANG['quotenumber'] . $id);
$smarty->assign('quoteid', $id);
$smarty->assign('quotenum', $id);
$smarty->assign('payto', nl2br($CONFIG['InvoicePayTo']));
$smarty->assign('datecreated', $date);
$smarty->assign('datedue', $duedate);
$smarty->assign('subtotal', formatCurrency($subtotal));
$smarty->assign('discount', $discount);
$smarty->assign('discount', $discount) . "%";
$smarty->assign('tax', formatCurrency($tax));
$smarty->assign('tax2', formatCurrency($tax2));
$smarty->assign('total', formatCurrency($total));
$smarty->assign('stage', $stage);
$smarty->assign('validuntil', $validuntil);
$quoteitems = array(  );
$result = select_query('tblquoteitems', 'quantity,description,unitprice,discount,taxable', array( 'quoteid' => $id ), 'id', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $qty = $data[0];
    $description = $data[1];
    $unitprice = $data[2];
    $discountpc = $discount = $data[3];
    $taxed = $data[4] ? true : false;
    if( 1 < $qty )
    {
        $description = $qty . " x " . $description . " @ " . $unitprice . $_LANG['invoiceqtyeach'];
        $amount = $qty * $unitprice;
    }
    else
    {
        $amount = $unitprice;
    }
    $discount = ($amount * $discount) / 100;
    if( $discount )
    {
        $amount -= $discount;
    }
    $quoteitems[] = array( 'description' => nl2br($description), 'unitprice' => formatCurrency($unitprice), 'discount' => 0 < $discount ? formatCurrency($discount) : '', 'discountpc' => $discountpc, 'amount' => formatCurrency($amount), 'taxed' => $taxed );
}
$smarty->assign('id', $id);
$smarty->assign('quoteitems', $quoteitems);
$smarty->assign('proposal', nl2br($proposal));
$smarty->assign('notes', nl2br($notes));
$smarty->assign('accepttos', $CONFIG['EnableTOSAccept']);
$smarty->assign('tosurl', $CONFIG['TermsOfService']);
$template_output = $smarty->fetch($whmcs->getClientAreaTplName() . "/viewquote.tpl");
echo $template_output;