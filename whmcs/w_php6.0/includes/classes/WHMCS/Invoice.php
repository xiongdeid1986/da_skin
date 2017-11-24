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
/**
 * WHMCS Invoice Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Invoice
{
    public $pdf = '';
    private $invoiceid = '';
    private $data = array(  );
    private $output = array(  );
    private $totalbalance = 0;
    protected $gateway = null;
    public function __construct($invoiceid = '')
    {
        if( $invoiceid )
        {
            $this->setID($invoiceid);
        }
    }
    public function setID($invoiceid)
    {
        $this->invoiceid = $invoiceid;
        return true;
    }
    public function getID()
    {
        return (int) $this->invoiceid;
    }
    public function loadData($force = true)
    {
        if( !$force && count($this->data) )
        {
            return false;
        }
        $result = select_query('tblinvoices', "tblinvoices.*,(SELECT value FROM tblpaymentgateways WHERE gateway=tblinvoices.paymentmethod AND setting='name' LIMIT 1) AS gateway,IFNULL((SELECT SUM(amountin-amountout) FROM tblaccounts WHERE invoiceid=tblinvoices.id),0) as amountpaid", array( 'id' => $this->invoiceid ));
        $data = mysql_fetch_assoc($result);
        if( !$data['id'] )
        {
            return false;
        }
        $data['invoiceid'] = $data['id'];
        $data['invoicenumorig'] = $data['invoicenum'];
        if( !$data['invoicenum'] )
        {
            $data['invoicenum'] = $data['id'];
        }
        $data['paymentmodule'] = $data['paymentmethod'];
        $data['paymentmethod'] = $data['gateway'];
        $data['balance'] = sprintf("%01.2f", $data['total'] - $data['amountpaid']);
        $this->data = $data;
        return true;
    }
    public function getData($var = '')
    {
        $this->loadData(false);
        return isset($this->data[$var]) ? $this->data[$var] : $this->data;
    }
    public function getStatuses()
    {
        return array( 'Unpaid', 'Paid', 'Cancelled', 'Refunded', 'Collections' );
    }
    public function isAllowed($uid = '')
    {
        $this->loadData(false);
        if( !$uid )
        {
            $uid = $_SESSION['uid'];
        }
        if( !$uid || $this->data['userid'] != $uid )
        {
            return false;
        }
        return true;
    }
    public function formatForOutput()
    {
        global $whmcs;
        global $currency;
        $this->output = $this->data;
        $array = array( 'date', 'duedate', 'datepaid' );
        foreach( $array as $v )
        {
            $this->output[$v] = substr($this->output[$v], 0, 10) != '0000-00-00' ? fromMySQLDate($this->output[$v], $v == 'datepaid' ? '1' : '0', 1) : '';
        }
        $this->output['datecreated'] = $this->output['date'];
        $this->output['datedue'] = $this->output['duedate'];
        $currency = getCurrency($this->getData('userid'));
        $array = array( 'subtotal', 'credit', 'tax', 'tax2', 'total', 'balance', 'amountpaid' );
        foreach( $array as $v )
        {
            $this->output[$v] = formatCurrency($this->output[$v]);
        }
        if( !function_exists('getClientsDetails') )
        {
            require(ROOTDIR . "/includes/clientfunctions.php");
        }
        $clientsdetails = getClientsDetails($this->getData('userid'), 'billing');
        $clientsdetails['country'] = $clientsdetails['countryname'];
        $this->output['clientsdetails'] = $clientsdetails;
        $customfields = array(  );
        $result = select_query('tblcustomfields', "tblcustomfields.id,tblcustomfields.fieldname,(SELECT value FROM tblcustomfieldsvalues WHERE tblcustomfieldsvalues.fieldid=tblcustomfields.id AND tblcustomfieldsvalues.relid=" . (int) $this->getData('userid') . ") AS value", array( 'type' => 'client', 'showinvoice' => 'on' ));
        while( $data = mysql_fetch_assoc($result) )
        {
            if( $data['value'] )
            {
                $customfields[] = $data;
            }
        }
        $this->output['customfields'] = $customfields;
        if( (0 < $this->getData('taxrate') || 0 < $this->getData('taxrate2')) && !function_exists('getTaxRate') )
        {
            $whmcs->load_function('invoice');
        }
        if( 0 < $this->getData('taxrate') )
        {
            $taxname = getTaxRate(1, $clientsdetails['state'], $clientsdetails['countrycode']);
            $this->output['taxname'] = $taxname['name'];
        }
        else
        {
            $this->output['taxrate'] = '0';
        }
        if( 0 < $this->getData('taxrate2') )
        {
            $taxname = getTaxRate(2, $clientsdetails['state'], $clientsdetails['countrycode']);
            $this->output['taxname2'] = $taxname['name'];
        }
        else
        {
            $this->output['taxrate2'] = '0';
        }
        $this->output['statuslocale'] = $whmcs->get_lang('invoices' . strtolower($this->output['status']));
        $this->output['pagetitle'] = $whmcs->get_lang('invoicenumber') . $this->getData('invoicenum');
        $this->output['payto'] = nl2br($whmcs->get_config('InvoicePayTo'));
        $this->output['notes'] = nl2br($this->output['notes']);
        $this->output['subscrid'] = get_query_val('tblinvoiceitems', "tblhosting.subscriptionid", "tblinvoiceitems.type='Hosting' AND tblinvoiceitems.invoiceid=" . $this->getData('id') . " AND tblhosting.subscriptionid!=''", "tblhosting`.`id", 'ASC', '', "tblhosting ON tblhosting.id=tblinvoiceitems.relid");
        $clienttotals = get_query_vals('tblinvoices', "SUM(credit),SUM(total)", array( 'userid' => $this->getData('userid'), 'status' => 'Unpaid' ));
        $alldueinvoicespayments = get_query_val('tblaccounts', "SUM(amountin-amountout)", "invoiceid IN (SELECT id FROM tblinvoices WHERE userid=" . (int) $this->getData('userid') . " AND status='Unpaid')");
        $this->output['clienttotaldue'] = formatCurrency($clienttotals[0] + $clienttotals[1]);
        $this->output['clientpreviousbalance'] = formatCurrency($clienttotals[1] - $this->getData('total'));
        $this->output['clientbalancedue'] = formatCurrency($clienttotals[1] - $alldueinvoicespayments);
        $lastpayment = get_query_vals('tblaccounts', "(amountin-amountout),transid", array( 'invoiceid' => $this->getData('id') ), 'id', 'DESC');
        $this->output['lastpaymentamount'] = formatCurrency($lastpayment[0]);
        $this->output['lastpaymenttransid'] = $lastpayment[1];
    }
    public function getOutput($pdf = false)
    {
        $this->loadData(false);
        $this->formatForOutput();
        if( $pdf )
        {
            $this->makePDFFriendly();
        }
        return $this->output;
    }
    /**
     * Check gateway module is active and load settings
     *
     * Validates the gateway module is active, can be loaded and settings
     * can be retrieved for it.
     *
     * If no gateway module name is passed in, it will be auto populated
     * based upon the payment method the invoice is assigned to.
     *
     * In addition, when no gateway is explicitly defined, if the
     * gateway module the invoice is set to is no longer active, we will
     * attempt to automatically pick the first active gateway.
     *
     * @param string $passedInGatewayModuleName (optional)
     *
     * @throws WHMCS_Exception_Module_NotActivated When explicit passed in gateway is not active
     * @throws WHMCS_Exception_Information When no active gateway modules found
     * @throws WHMCS_Exception_Module_NotServicable Gateway module file is missing or invalid
     * @throws WHMCS_Exception_Module_InvalidConfiguration No gateway settings found
     *
     * @return string[]
     */
    public function initialiseGatewayAndParams($passedInGatewayModuleName = '')
    {
        global $whmcs;
        global $_LANG;
        $this->gateway = new WHMCS_Module_Gateway();
        if( $passedInGatewayModuleName )
        {
            $gatewaymodule = $passedInGatewayModuleName;
        }
        else
        {
            $gatewaymodule = $this->getData('paymentmodule');
        }
        if( !$this->gateway->isActiveGateway($gatewaymodule) )
        {
            if( $passedInGatewayModuleName )
            {
                throw new WHMCS_Exception_Module_NotActivated("Gateway Module '" . WHMCS_Input_Sanitize::makesafeforoutput($gatewaymodule) . "' Not Activated");
            }
            $gatewaymodule = $this->gateway->getFirstAvailableGateway();
            if( !$gatewaymodule )
            {
                throw new WHMCS_Exception_Information("No Gateway Modules are Currently Active");
            }
            update_query('tblinvoices', array( 'paymentmethod' => $gatewaymodule ), array( 'id' => $this->getID() ));
        }
        if( !$this->gateway->load($gatewaymodule) )
        {
            logActivity("Gateway Module '" . $gatewaymodule . "' is Missing");
            throw new WHMCS_Exception_Module_NotServicable("Gateway Module '" . WHMCS_Input_Sanitize::makesafeforoutput($gatewaymodule) . "' is Missing or Invalid");
        }
        $params = $this->gateway->loadSettings();
        if( !$params )
        {
            throw new WHMCS_Exception_Module_InvalidConfiguration("No Gateway Settings Found");
        }
        $params['companyname'] = $whmcs->get_config('CompanyName');
        if( $whmcs->get_config('SystemSSLURL') )
        {
            $params['systemurl'] = $whmcs->get_config('SystemSSLURL');
        }
        else
        {
            $params['systemurl'] = $whmcs->get_config('SystemURL');
        }
        $params['langpaynow'] = $_LANG['invoicespaynow'];
        return $params;
    }
    public function getGatewayInvoiceParams($params = array(  ))
    {
        global $whmcs;
        global $_LANG;
        if( count($params) < 1 )
        {
            try
            {
                $params = $this->initialiseGatewayAndParams();
            }
            catch( Exception $e )
            {
                logActivity("Failed to initialise payment gateway module: " . $e->getMessage());
                throw new WHMCS_Exception_Fatal("Could not initialise payment gateway. Please contact support.");
            }
        }
        $invoiceid = $this->getID();
        $userid = $this->getData('userid');
        $invoicenum = $this->getData('invoicenum');
        $balance = $this->getData('balance');
        $result = select_query('tblclients', "tblclients.currency,tblcurrencies.code", array( "tblclients.id" => $userid ), '', '', '', "tblcurrencies ON tblcurrencies.id=tblclients.currency");
        $data = mysql_fetch_array($result);
        $invoice_currency_id = $data['currency'];
        $invoice_currency_code = $data['code'];
        $params['invoiceid'] = $invoiceid;
        $params['invoicenum'] = $invoicenum;
        $params['amount'] = $balance;
        $params['description'] = $params['companyname'] . " - " . $_LANG['invoicenumber'] . ($invoicenum ? $invoicenum : $invoiceid);
        $params['returnurl'] = $params['systemurl'] . "/viewinvoice.php?id=" . $invoiceid;
        $client = new WHMCS_Client($userid);
        $clientsdetails = $client->getDetails('billing');
        $clientsdetails['state'] = $clientsdetails['statecode'];
        $params['clientdetails'] = $clientsdetails;
        if( $params['convertto'] )
        {
            $result = select_query('tblcurrencies', 'code', array( 'id' => (int) $params['convertto'] ));
            $data = mysql_fetch_array($result);
            $converto_currency_code = $data['code'];
            $converto_amount = convertCurrency($balance, $invoice_currency_id, $params['convertto']);
            $params['amount'] = format_as_currency($converto_amount);
            $params['currency'] = $converto_currency_code;
            $params['basecurrencyamount'] = format_as_currency($balance);
            $params['basecurrency'] = $invoice_currency_code;
        }
        if( !$params['currency'] )
        {
            $params['amount'] = format_as_currency($balance);
            $params['currency'] = $invoice_currency_code;
        }
        return $params;
    }
    public function getPaymentLink()
    {
        try
        {
            $params = $this->initialiseGatewayAndParams();
        }
        catch( Exception $e )
        {
            logActivity("Failed to initialise payment gateway module: " . $e->getMessage());
            return false;
        }
        $params = $this->getGatewayInvoiceParams($params);
        if( !$this->gateway->functionExists('link') )
        {
            eval("function " . $this->gateway->getLoadedModule() . "_link(\$params) { return '<form method=\"post\" action=\"'.\$params['systemurl'].'/creditcard.php\" name=\"paymentfrm\"><input type=\"hidden\" name=\"invoiceid\" value=\"'.\$params['invoiceid'].'\"><input type=\"submit\" value=\"'.\$params['langpaynow'].'\"></form>'; }");
        }
        $paymentbutton = $this->gateway->call('link', $params);
        return $paymentbutton;
    }
    public function getLineItems($entitydecode = false)
    {
        global $whmcs;
        getUsersLang($this->getData('userid'));
        $invoiceid = $this->getID();
        $invoiceitems = array(  );
        if( $whmcs->get_config('GroupSimilarLineItems') )
        {
            $result = full_query("SELECT COUNT(*),id,type,relid,description,amount,taxed FROM tblinvoiceitems WHERE invoiceid=" . (int) $invoiceid . " GROUP BY `description`,`amount` ORDER BY id ASC");
        }
        else
        {
            $result = select_query('tblinvoiceitems', '0,id,type,relid,description,amount,taxed', array( 'invoiceid' => $invoiceid ), 'id', 'ASC');
        }
        while( $data = mysql_fetch_array($result) )
        {
            $qty = $data[0];
            $description = $data[4];
            $amount = $data[5];
            $taxed = $data[6] ? true : false;
            if( 1 < $qty )
            {
                $description = $qty . " x " . $description . " @ " . $amount . $whmcs->get_lang('invoiceqtyeach');
                $amount *= $qty;
            }
            if( $entitydecode )
            {
                $description = htmlspecialchars(WHMCS_Input_Sanitize::decode($description));
            }
            else
            {
                $description = nl2br($description);
            }
            $invoiceitems[] = array( 'id' => $data[1], 'type' => $data[2], 'relid' => $data[3], 'description' => $description, 'rawamount' => $amount, 'amount' => formatCurrency($amount), 'taxed' => $taxed );
        }
        return $invoiceitems;
    }
    public function getTransactions()
    {
        $invoiceid = $this->invoiceid;
        $transactions = array(  );
        $result = select_query('tblaccounts', "id,date,transid,amountin,amountout,(SELECT value FROM tblpaymentgateways WHERE gateway=tblaccounts.gateway AND setting='name' LIMIT 1) AS gateway", array( 'invoiceid' => $invoiceid ), "date` ASC,`id", 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $tid = $data['id'];
            $date = $data['date'];
            $gateway = $data['gateway'];
            $amountin = $data['amountin'];
            $amountout = $data['amountout'];
            $transid = $data['transid'];
            $date = fromMySQLDate($date, 0, 1);
            if( !$gateway )
            {
                $gateway = '-';
            }
            $transactions[] = array( 'id' => $tid, 'date' => $date, 'gateway' => $gateway, 'transid' => $transid, 'amount' => formatCurrency($amountin - $amountout) );
        }
        return $transactions;
    }
    public function pdfCreate()
    {
        $this->pdf = new WHMCS_PDF();
        return $this->pdf;
    }
    public function makePDFFriendly()
    {
        global $whmcs;
        $this->output['companyname'] = $whmcs->get_config('CompanyName');
        $this->output['companyurl'] = $whmcs->get_config('Domain');
        $companyaddress = $whmcs->get_config('InvoicePayTo');
        $this->output['companyaddress'] = explode("\n", $companyaddress);
        if( trim($this->output['notes']) )
        {
            $this->output['notes'] = str_replace("<br />", '', $this->output['notes']) . "\n";
        }
        $this->output = WHMCS_Input_Sanitize::decode($this->output);
        return true;
    }
    public function pdfInvoicePage($invoiceid = '')
    {
        global $whmcs;
        global $currency;
        if( $invoiceid )
        {
            $this->setID($invoiceid);
            $invoiceexists = $this->loadData();
            if( !$invoiceexists )
            {
                return false;
            }
        }
        $this->pdf->SetTitle($whmcs->get_lang('invoicenumber') . $this->getData('invoicenum'));
        $tplvars = $this->getOutput(true);
        $invoiceitems = $this->getLineItems(true);
        $tplvars['invoiceitems'] = $invoiceitems;
        $transactions = $this->getTransactions();
        $tplvars['transactions'] = $transactions;
        $this->pdfAddPage("invoicepdf.tpl", $tplvars);
        return true;
    }
    public function pdfAddPage($tplfile, $tplvars)
    {
        global $whmcs;
        global $_LANG;
        $this->pdf->AddPage();
        $this->pdf->SetFont('freesans', '', 10);
        $this->pdf->SetTextColor(0);
        foreach( $tplvars as $k => $v )
        {
            ${$k} = $v;
        }
        $pdf =& $this->pdf;
        if( !isValidforPath($whmcs->get_config('Template')) )
        {
            exit( "Invalid System Template Name" );
        }
        include(ROOTDIR . '/templates/' . $whmcs->get_config('Template') . '/' . $tplfile);
        return true;
    }
    public function pdfOutput()
    {
        return $this->pdf->Output('', 'S');
    }
    public function getInvoices($status = '', $userid = '', $orderby = 'id', $sort = 'DESC', $limit = '')
    {
        global $whmcs;
        $where = array(  );
        if( $status )
        {
            $where[] = "status = '" . db_escape_string($status) . "'";
        }
        if( $userid )
        {
            $where[] = "userid = " . (int) $userid;
        }
        $where[] = "(select count(id) from tblinvoiceitems where invoiceid=tblinvoices.id and type='Invoice')<=0";
        $invoices = array(  );
        $result = select_query('tblinvoices', "tblinvoices.*,total-COALESCE((SELECT SUM(amountin-amountout) FROM tblaccounts WHERE tblaccounts.invoiceid=tblinvoices.id),0) AS balance", implode(" AND ", $where), $orderby, $sort, $limit);
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $invoicenum = $data['invoicenum'];
            $date = $data['date'];
            $duedate = $data['duedate'];
            $credit = $data['credit'];
            $total = $data['total'];
            $balance = $data['balance'];
            $status = $data['status'];
            if( $status == 'Unpaid' )
            {
                $this->totalbalance += $balance;
            }
            $date = fromMySQLDate($date, 0, 1);
            $duedate = fromMySQLDate($duedate, 0, 1);
            $rawstatus = strtolower($status);
            if( !$invoicenum )
            {
                $invoicenum = $id;
            }
            $invoices[] = array( 'id' => $id, 'invoicenum' => $invoicenum, 'datecreated' => $date, 'datedue' => $duedate, 'total' => formatCurrency($credit + $total), 'balance' => formatCurrency($balance), 'status' => getInvoiceStatusColour($status), 'rawstatus' => $rawstatus, 'statustext' => $whmcs->get_lang('invoices' . $rawstatus) );
        }
        return $invoices;
    }
    public function getTotalBalance()
    {
        return $this->totalbalance;
    }
    public function getTotalBalanceFormatted()
    {
        return formatCurrency($this->getTotalBalance());
    }
    public function getEmailTemplates()
    {
        $status = $this->getData('status');
        $validtpls = array(  );
        $result = select_query('tblemailtemplates', 'id,name', array( 'type' => 'invoice', 'language' => '' ), 'name', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $validtpls[$data['name']] = $data['id'];
        }
        $emailtplsoutput = array( "Invoice Created", "Credit Card Invoice Created", "Invoice Payment Reminder", "First Invoice Overdue Notice", "Second Invoice Overdue Notice", "Third Invoice Overdue Notice", "Credit Card Payment Due", "Credit Card Payment Failed", "Invoice Payment Confirmation", "Credit Card Payment Confirmation", "Invoice Refund Confirmation" );
        if( $status == 'Paid' )
        {
            $emailtplsoutput = array_merge(array( "Invoice Payment Confirmation", "Credit Card Payment Confirmation" ), $emailtplsoutput);
        }
        if( $status == 'Refunded' )
        {
            $emailtplsoutput = array_merge(array( "Invoice Refund Confirmation" ), $emailtplsoutput);
        }
        $returntpls = array(  );
        foreach( $emailtplsoutput as $tplname )
        {
            if( array_key_exists($tplname, $validtpls) )
            {
                $returntpls[] = array( 'name' => $tplname );
                unset($validtpls[$tplname]);
            }
        }
        foreach( $validtpls as $tplname => $k )
        {
            $returntpls[] = array( 'name' => $tplname );
        }
        return $returntpls;
    }
    public function getFriendlyPaymentMethod()
    {
        global $aInt;
        $credit = $this->getData('credit');
        $result = select_query('tblaccounts', "COUNT(id),SUM(amountin)-SUM(amountout)", array( 'invoiceid' => $this->getData('id') ));
        $data = mysql_fetch_array($result);
        $transcount = $data[0];
        $amountpaid = $data[1];
        if( $this->getData('status') == 'Unpaid' )
        {
            $paymentmethodfriendly = $this->getData('paymentmethod');
        }
        else
        {
            if( $transcount == 0 )
            {
                $paymentmethodfriendly = $aInt->lang('invoices', 'notransapplied');
            }
            else
            {
                $paymentmethodfriendly = $this->getData('paymentmethod');
            }
        }
        if( 0 < $credit )
        {
            if( $total == 0 )
            {
                $paymentmethodfriendly = $aInt->lang('invoices', 'fullypaidcredit');
            }
            else
            {
                $paymentmethodfriendly .= " + " . $aInt->lang('invoices', 'partialcredit');
            }
        }
        return $paymentmethodfriendly;
    }
    public function getBalanceFormatted()
    {
        global $currency;
        $userid = $this->getData('userid');
        $currency = getCurrency($userid);
        $balance = $this->getData('balance');
        return "<span class=\"" . (0 < $balance ? 'textred' : 'textgreen') . "\">" . formatCurrency($balance) . "</span>";
    }
    public function sendEmailTpl($tplname)
    {
        return sendMessage($tplname, $this->getData('id'));
    }
    public function isAddFundsInvoice()
    {
        $numaddfunditems = get_query_val('tblinvoiceitems', "COUNT(id)", array( 'invoiceid' => $this->getID(), 'type' => 'AddFunds' ));
        $numtotalitems = get_query_val('tblinvoiceitems', "COUNT(id)", array( 'invoiceid' => $this->getID() ));
        return $numaddfunditems == $numtotalitems ? true : false;
    }
}