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
function saveQuote($id, $subject, $stage, $datecreated, $validuntil, $clienttype, $userid, $firstname, $lastname, $companyname, $email, $address1, $address2, $city, $state, $postcode, $country, $phonenumber, $currency, $lineitems, $proposal, $customernotes, $adminnotes, $updatepriceonly = '')
{
    global $CONFIG;
    if( !$id )
    {
        $id = insert_query('tblquotes', array( 'subject' => $subject, 'stage' => $stage, 'datecreated' => toMySQLDate($datecreated), 'validuntil' => toMySQLDate($validuntil), 'lastmodified' => "now()" ));
        run_hook('QuoteCreated', array( 'quoteid' => $id, 'status' => $stage ));
    }
    else
    {
        run_hook('QuoteStatusChange', array( 'quoteid' => $id, 'status' => $stage ));
    }
    if( $clienttype == 'new' )
    {
        $userid = 0;
        $fortax_state = $state;
        $fortax_country = $country;
    }
    else
    {
        $clientsdetails = getClientsDetails($userid);
        $fortax_state = $clientsdetails['state'];
        $fortax_country = $clientsdetails['country'];
    }
    $taxlevel1 = getTaxRate(1, $fortax_state, $fortax_country);
    $taxlevel2 = getTaxRate(2, $fortax_state, $fortax_country);
    $subtotal = 0;
    $taxableamount = 0;
    if( $lineitems )
    {
        foreach( $lineitems as $linedata )
        {
            $line_id = $linedata['id'];
            $line_desc = $linedata['desc'];
            $line_qty = $linedata['qty'];
            $line_up = $linedata['up'];
            $line_discount = $linedata['discount'];
            $line_taxable = $linedata['taxable'];
            if( $line_id )
            {
                update_query('tblquoteitems', array( 'description' => $line_desc, 'quantity' => $line_qty, 'unitprice' => $line_up, 'discount' => $line_discount, 'taxable' => $line_taxable ), array( 'id' => $line_id ));
            }
            else
            {
                insert_query('tblquoteitems', array( 'quoteid' => $id, 'description' => $line_desc, 'quantity' => $line_qty, 'unitprice' => $line_up, 'discount' => $line_discount, 'taxable' => $line_taxable ));
            }
            $lineitemamount = $line_qty * $line_up * (1 - $line_discount / 100);
            $subtotal += $lineitemamount;
            if( $line_taxable )
            {
                $taxableamount += $lineitemamount;
            }
        }
    }
    else
    {
        $result = select_query('tblquoteitems', '', array( 'quoteid' => $id ), 'id', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $line_qty = $data['quantity'];
            $line_unitprice = $data['unitprice'];
            $line_discount = $data['discount'];
            $line_taxable = $data['taxable'];
            $lineitemamount = round($line_qty * $line_unitprice * (1 - $line_discount / 100), 2);
            $subtotal += $lineitemamount;
            if( $line_taxable )
            {
                $taxableamount += $lineitemamount;
            }
        }
    }
    if( 0 < $taxlevel1['rate'] )
    {
        if( $CONFIG['TaxType'] == 'Inclusive' )
        {
            $tax1 = format_as_currency($taxableamount / (100 + $taxlevel1['rate']) * $taxlevel1['rate']);
        }
        else
        {
            $tax1 = format_as_currency($taxableamount * $taxlevel1['rate'] / 100);
        }
    }
    if( 0 < $taxlevel2['rate'] )
    {
        if( $CONFIG['TaxType'] == 'Inclusive' )
        {
            $tax2 = format_as_currency($taxableamount / (100 + $taxlevel2['rate']) * $taxlevel2['rate']);
        }
        else
        {
            if( $CONFIG['TaxL2Compound'] )
            {
                $tax2 = format_as_currency(($taxableamount + $tax1) * $taxlevel2['rate'] / 100);
            }
            else
            {
                $tax2 = format_as_currency($taxableamount * $taxlevel2['rate'] / 100);
            }
        }
    }
    if( $CONFIG['TaxType'] == 'Inclusive' )
    {
        $total = $subtotal;
        $subtotal = $subtotal - $tax1 - $tax2;
    }
    else
    {
        $total = $subtotal + $tax1 + $tax2;
    }
    if( $updatepriceonly )
    {
        update_query('tblquotes', array( 'subtotal' => $subtotal, 'tax1' => $tax1, 'tax2' => $tax2, 'total' => $total ), array( 'id' => $id ));
    }
    else
    {
        update_query('tblquotes', array( 'subject' => $subject, 'stage' => $stage, 'datecreated' => toMySQLDate($datecreated), 'validuntil' => toMySQLDate($validuntil), 'lastmodified' => "now()", 'userid' => $userid, 'firstname' => $firstname, 'lastname' => $lastname, 'companyname' => $companyname, 'email' => $email, 'address1' => $address1, 'address2' => $address2, 'city' => $city, 'state' => $state, 'postcode' => $postcode, 'country' => $country, 'phonenumber' => $phonenumber, 'currency' => $currency, 'subtotal' => $subtotal, 'tax1' => $tax1, 'tax2' => $tax2, 'total' => $total, 'proposal' => $proposal, 'customernotes' => $customernotes, 'adminnotes' => $adminnotes ), array( 'id' => $id ));
    }
    return $id;
}
function genQuotePDF($id)
{
    global $whmcs;
    global $CONFIG;
    global $_LANG;
    global $currency;
    $companyname = $CONFIG['CompanyName'];
    $companyurl = $CONFIG['Domain'];
    $companyaddress = $CONFIG['InvoicePayTo'];
    $companyaddress = explode("\n", $companyaddress);
    $quotenumber = $id;
    $result = select_query('tblquotes', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $subject = $data['subject'];
    $stage = $data['stage'];
    $datecreated = fromMySQLDate($data['datecreated']);
    $validuntil = fromMySQLDate($data['validuntil']);
    $userid = $data['userid'];
    $proposal = $data['proposal'] ? $data['proposal'] . "\n" : '';
    $notes = $data['customernotes'] ? $data['customernotes'] . "\n" : '';
    $currency = getCurrency($userid, $data['currency']);
    if( $userid )
    {
        getUsersLang($userid);
        $stage = getQuoteStageLang($stage);
        $clientsdetails = getClientsDetails($userid);
    }
    else
    {
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
    $taxlevel1 = getTaxRate(1, $clientsdetails['state'], $clientsdetails['country']);
    $taxlevel2 = getTaxRate(2, $clientsdetails['state'], $clientsdetails['country']);
    require(ROOTDIR . "/includes/countries.php");
    $clientsdetails['country'] = $countries[$clientsdetails['country']];
    $subtotal = formatCurrency($data['subtotal']);
    $tax1 = formatCurrency($data['tax1']);
    $tax2 = formatCurrency($data['tax2']);
    $total = formatCurrency($data['total']);
    $lineitems = array(  );
    $result = select_query('tblquoteitems', '', array( 'quoteid' => $id ), 'id', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $line_id = $data['id'];
        $line_desc = $data['description'];
        $line_qty = $data['quantity'];
        $line_unitprice = $data['unitprice'];
        $line_discount = $data['discount'];
        $line_taxable = $data['taxable'];
        $line_total = format_as_currency($line_qty * $line_unitprice * (1 - $line_discount / 100));
        $lineitems[] = array( 'id' => $line_id, 'description' => htmlspecialchars(WHMCS_Input_Sanitize::decode($line_desc)), 'qty' => $line_qty, 'unitprice' => $line_unitprice, 'discount' => $line_discount, 'taxable' => $line_taxable, 'total' => formatCurrency($line_total) );
    }
    $tplvars = array(  );
    $tplvars['companyname'] = $companyname;
    $tplvars['companyurl'] = $companyurl;
    $tplvars['companyaddress'] = $companyaddress;
    $tplvars['paymentmethod'] = $paymentmethod;
    $tplvars['quotenumber'] = $quotenumber;
    $tplvars['subject'] = $subject;
    $tplvars['stage'] = $stage;
    $tplvars['datecreated'] = $datecreated;
    $tplvars['validuntil'] = $validuntil;
    $tplvars['userid'] = $userid;
    $tplvars['clientsdetails'] = $clientsdetails;
    $tplvars['proposal'] = $proposal;
    $tplvars['notes'] = $notes;
    $tplvars['taxlevel1'] = $taxlevel1;
    $tplvars['taxlevel2'] = $taxlevel2;
    $tplvars['subtotal'] = $subtotal;
    $tplvars['tax1'] = $tax1;
    $tplvars['tax2'] = $tax2;
    $tplvars['total'] = $total;
    $tplvars = WHMCS_Input_Sanitize::decode($tplvars);
    $tplvars['lineitems'] = $lineitems;
    $invoice = new WHMCS_Invoice();
    $invoice->pdfCreate($_LANG['quotenumber'] . $id);
    $invoice->pdfAddPage("quotepdf.tpl", $tplvars);
    $pdfdata = $invoice->pdfOutput();
    return $pdfdata;
}
function sendQuotePDF($id)
{
    global $CONFIG;
    global $_LANG;
    global $currency;
    $result = select_query('tblquotes', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $subject = $data['subject'];
    $stage = $data['stage'];
    $datecreated = fromMySQLDate($data['datecreated']);
    $validuntil = fromMySQLDate($data['validuntil']);
    $userid = $data['userid'];
    $notes = $data['customernotes'] . "\n";
    if( $userid )
    {
        $clientsdetails = getClientsDetails($userid);
    }
    else
    {
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
    $pdfdata = genquotepdf($id);
    $sysurl = $CONFIG['SystemSSLURL'] ? $CONFIG['SystemSSLURL'] : $CONFIG['SystemURL'];
    $quote_link = "<a href=\"" . $sysurl . "/viewquote.php?id=" . $id . "\">" . $sysurl . "/viewquote.php?id=" . $id . "</a>";
    sendMessage("Quote Delivery with PDF", 1, array( 'emailquote' => true, 'quote_number' => $id, 'quote_subject' => $subject, 'quote_date_created' => $datecreated, 'quote_valid_until' => $validuntil, 'client_id' => $userid, 'client_first_name' => $clientsdetails['firstname'], 'client_last_name' => $clientsdetails['lastname'], 'client_company_name' => $clientsdetails['companyname'], 'client_email' => $clientsdetails['email'], 'client_address1' => $clientsdetails['address1'], 'client_address2' => $clientsdetails['address2'], 'client_city' => $clientsdetails['city'], 'client_state' => $clientsdetails['state'], 'client_postcode' => $clientsdetails['postcode'], 'client_country' => $clientsdetails['country'], 'client_phonenumber' => $clientsdetails['phonenumber'], 'client_language' => $clientsdetails['language'], 'quoteattachmentdata' => $pdfdata, 'quote_link' => $quote_link ));
    update_query('tblquotes', array( 'stage' => 'Delivered' ), array( 'id' => $id ));
}
function convertQuotetoInvoice($id, $invoicetype, $invoiceduedate, $depositpercent, $depositduedate, $finalduedate, $sendemail)
{
    global $CONFIG;
    global $_LANG;
    $result = select_query('tblquotes', '', array( 'id' => $id ));
    $data = mysql_fetch_array($result);
    $userid = $data['userid'];
    $firstname = $data['firstname'];
    $lastname = $data['lastname'];
    $companyname = $data['companyname'];
    $email = $data['email'];
    $address1 = $data['address1'];
    $address2 = $data['address2'];
    $city = $data['city'];
    $state = $data['state'];
    $postcode = $data['postcode'];
    $country = $data['country'];
    $phonenumber = $data['phonenumber'];
    $currency = $data['currency'];
    if( $userid )
    {
        getUsersLang($userid);
        $clientsdetails = getClientsDetails($userid);
        $state = $clientsdetails['state'];
        $country = $clientsdetails['country'];
    }
    else
    {
        if( !function_exists('addClient') )
        {
            require(ROOTDIR . "/clientfunctions.php");
        }
        $_SESSION['currency'] = $currency;
        $userid = addClient($firstname, $lastname, $companyname, $email, $address1, $address2, $city, $state, $postcode, $country, $phonenumber, substr(md5($id), 0, 10), 0, '', 'on');
    }
    if( $CONFIG['TaxEnabled'] == 'on' )
    {
        $taxlevel1 = getTaxRate(1, $state, $country);
        $taxlevel2 = getTaxRate(2, $state, $country);
        $taxrate = $taxlevel1['rate'];
        $taxrate2 = $taxlevel2['rate'];
    }
    $subtotal = $data['subtotal'];
    $tax1 = $data['tax1'];
    $tax2 = $data['tax2'];
    $total = $data['total'];
    $result = select_query('tblpaymentgateways', 'gateway', array( 'setting' => 'name' ), 'order', 'ASC');
    $data = mysql_fetch_array($result);
    $gateway = $data['gateway'];
    $duedate = $finaldate = '';
    if( $invoicetype == 'deposit' )
    {
        if( $depositduedate )
        {
            $duedate = toMySQLDate($depositduedate);
        }
        $finaldate = $finalduedate ? toMySQLDate($finalduedate) : date('Y-m-d');
    }
    else
    {
        if( $invoiceduedate )
        {
            $duedate = toMySQLDate($invoiceduedate);
        }
    }
    if( !$duedate )
    {
        $duedate = date('Y-m-d');
    }
    $invoiceid = insert_query('tblinvoices', array( 'date' => "now()", 'duedate' => $duedate, 'userid' => $userid, 'status' => 'Unpaid', 'paymentmethod' => $gateway, 'taxrate' => $taxrate, 'taxrate2' => $taxrate2, 'subtotal' => $subtotal, 'tax' => $tax1, 'tax2' => $tax2, 'total' => $total, 'notes' => $_LANG['quoteref'] . $id ));
    if( $finaldate )
    {
        $finalinvoiceid = insert_query('tblinvoices', array( 'date' => "now()", 'duedate' => $finaldate, 'userid' => $userid, 'status' => 'Unpaid', 'paymentmethod' => $gateway, 'taxrate' => $taxrate, 'taxrate2' => $taxrate2, 'subtotal' => $subtotal, 'tax' => $tax1, 'tax2' => $tax2, 'total' => $total, 'notes' => $_LANG['quoteref'] . $id ));
    }
    $result = select_query('tblquoteitems', '', array( 'quoteid' => $id ), 'id', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $line_id = $data['id'];
        $line_desc = $data['description'];
        $line_qty = $data['quantity'];
        $line_unitprice = $data['unitprice'];
        $line_discount = $data['discount'];
        $line_taxable = $data['taxable'];
        $line_total = format_as_currency($line_qty * $line_unitprice * (1 - $line_discount / 100));
        $lineitemdesc = $line_qty . " x " . $line_desc . " @ " . $line_unitprice;
        if( 0 < $line_discount )
        {
            $lineitemdesc .= " - " . $line_discount . "% " . $_LANG['orderdiscount'];
        }
        if( $finalinvoiceid )
        {
            $originalamount = $line_total;
            $line_total = $originalamount * $depositpercent / 100;
            $final_amount = $originalamount - $line_total;
            insert_query('tblinvoiceitems', array( 'invoiceid' => $finalinvoiceid, 'userid' => $userid, 'description' => $lineitemdesc . " (" . (100 - $depositpercent) . "% " . $_LANG['quotefinalpayment'] . ")", 'amount' => $final_amount, 'taxed' => $line_taxable ));
            $lineitemdesc .= " (" . $depositpercent . "% " . $_LANG['quotedeposit'] . ")";
        }
        insert_query('tblinvoiceitems', array( 'invoiceid' => $invoiceid, 'userid' => $userid, 'description' => $lineitemdesc, 'amount' => $line_total, 'taxed' => $line_taxable ));
    }
    if( !function_exists('updateInvoiceTotal') )
    {
        require(ROOTDIR . "/includes/invoicefunctions.php");
    }
    updateInvoiceTotal($invoiceid);
    if( $finalinvoiceid )
    {
        updateInvoiceTotal($finalinvoiceid);
    }
    if( defined('APICALL') )
    {
        $source = 'api';
        $user = WHMCS_Session::get('adminid');
    }
    else
    {
        if( defined('ADMINAREA') )
        {
            $source = 'adminarea';
            $user = WHMCS_Session::get('adminid');
        }
        else
        {
            $source = 'clientarea';
            $user = WHMCS_Session::get('uid');
        }
    }
    $invoiceArr = array( 'source' => $source, 'user' => $user, 'invoiceid' => $invoiceid );
    run_hook('InvoiceCreation', $invoiceArr);
    if( $sendemail )
    {
        run_hook('InvoiceCreationPreEmail', $invoiceArr);
        sendMessage("Invoice Created", $invoiceid);
    }
    run_hook('InvoiceCreated', $invoiceArr);
    if( $finalinvoiceid )
    {
        $invoiceArr = array( 'source' => $source, 'user' => $user, 'invoiceid' => $finalinvoiceid );
        run_hook('InvoiceCreation', $invoiceArr);
        if( $sendemail )
        {
            run_hook('InvoiceCreationPreEmail', $invoiceArr);
            sendMessage("Invoice Created", $finalinvoiceid);
        }
        run_hook('InvoiceCreated', $invoiceArr);
    }
    WHMCS_Invoices::adjustincrementfornextinvoice($invoiceid);
    update_query('tblquotes', array( 'userid' => $userid, 'stage' => 'Accepted' ), array( 'id' => $id ));
    return $invoiceid;
}
function getQuoteStageLang($stage)
{
    global $_LANG;
    $translation = $_LANG['quotestage' . strtolower(str_replace(" ", '', $stage))];
    if( !$translation )
    {
        $translation = $stage;
    }
    return $translation;
}