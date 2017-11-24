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
 * Load a gateway module
 *
 * Modules names must contain only valid characters:
 *  - alphanumeric
 *  - hyphen
 *  - underscore
 *
 * @param string $paymentMethod Basename of the file to include
 * @return boolean True success, otherwise false
 */
function loadGatewayModule($paymentMethod)
{
    $paymentMethod = WHMCS_Gateways::makesafename($paymentMethod);
    if( !$paymentMethod )
    {
        return false;
    }
    $basePath = fetchGatewayModuleDirectory();
    $expectedFile = $basePath . '/' . $paymentMethod . ".php";
    $state = false;
    if( file_exists($expectedFile) )
    {
        ob_start();
        $state = include_once($expectedFile) !== false;
        ob_end_clean();
    }
    return $state;
}
function fetchGatewayModuleDirectory()
{
    return ROOTDIR . '/modules/gateways';
}
function paymentMethodsSelection($blankSelection = '', $tabIndex = false)
{
    global $paymentmethod;
    if( $tabIndex )
    {
        $tabIndex = " tabindex=\"" . $tabIndex . "\"";
    }
    $code = "<select name=\"paymentmethod\"" . $tabIndex . ">";
    if( $blankSelection )
    {
        $code .= "<option value=\"\">" . $blankSelection . "</option>";
    }
    $result = select_query('tblpaymentgateways', 'gateway,value', array( 'setting' => 'name' ), 'order', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $gateway = $data['gateway'];
        $value = $data['value'];
        $code .= "<option value=\"" . $gateway . "\"";
        if( $paymentmethod == $gateway )
        {
            $code .= " selected";
        }
        $code .= ">" . $value . "</option>";
    }
    $code .= "</select>";
    return $code;
}
function checkActiveGateway()
{
    if( count(getGatewaysArray()) )
    {
        return true;
    }
    return false;
}
function getGatewaysArray()
{
    $gateways = array(  );
    $result = select_query('tblpaymentgateways', 'gateway,value', array( 'setting' => 'name' ), 'order', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $gateways[$data['gateway']] = $data['value'];
    }
    return $gateways;
}
function getGatewayName($moduleName)
{
    return get_query_val('tblpaymentgateways', 'value', array( 'gateway' => $moduleName, 'setting' => 'name' ));
}
/**
 * Obtain an array of enabled payment gateways.
 *
 * @param array $disabledGateways an array containing a list of disabled payment gateways
 * @return array An array containing the payment gateways that are enabled.
 */
function showPaymentGatewaysList($disabledGateways = array(  ))
{
    $result = select_query('tblpaymentgateways', "gateway, value", array( 'setting' => 'name' ), 'order', 'ASC');
    $gatewayList = array(  );
    while( $data = mysql_fetch_array($result) )
    {
        $showPaymentGateway = $data['gateway'];
        $showPaymentGWValue = $data['value'];
        $gatewayType = get_query_val('tblpaymentgateways', 'value', array( 'setting' => 'type', 'gateway' => $showPaymentGateway ));
        $isVisible = (string) get_query_val('tblpaymentgateways', 'value', array( 'setting' => 'visible', 'gateway' => $showPaymentGateway ));
        if( $isVisible && !in_array($showPaymentGateway, $disabledGateways) )
        {
            $gatewayList[$showPaymentGateway] = array( 'sysname' => $showPaymentGateway, 'name' => $showPaymentGWValue, 'type' => $gatewayType );
        }
    }
    return $gatewayList;
}
function getVariables($gateway)
{
    return getGatewayVariables($gateway);
}
function getGatewayVariables($gateway, $invoiceId = '')
{
    $invoice = new WHMCS_Invoice($invoiceId);
    try
    {
        $params = $invoice->initialiseGatewayAndParams($gateway);
    }
    catch( WHMCS_Exception_Module_NotActivated $e )
    {
        logActivity("Failed to initialise payment gateway module: " . $e->getMessage());
        throw new WHMCS_Exception_Fatal("Gateway Module \"" . WHMCS_Input_Sanitize::makesafeforoutput($gateway) . "\" Not Activated");
    }
    if( $invoiceId )
    {
        $params = array_merge($params, $invoice->getGatewayInvoiceParams());
    }
    $params = WHMCS_Input_Sanitize::converttocompathtml($params);
    return $params;
}
function logTransaction($gateway, $data, $result)
{
    global $params;
    $invoiceData = '';
    if( $params['invoiceid'] )
    {
        $invoiceData .= "Invoice ID => " . $params['invoiceid'] . "\n";
    }
    if( $params['clientdetails']['userid'] )
    {
        $invoiceData .= "User ID => " . $params['clientdetails']['userid'] . "\n";
    }
    if( $params['amount'] )
    {
        $invoiceData .= "Amount => " . $params['amount'] . "\n";
    }
    if( is_array($data) )
    {
        $logData = '';
        foreach( $data as $key => $value )
        {
            $logData .= $key . " => " . $value . "\n";
        }
    }
    else
    {
        $logData = $data;
    }
    $array = array( 'date' => "now()", 'gateway' => $gateway, 'data' => $invoiceData . $logData, 'result' => $result );
    insert_query('tblgatewaylog', $array);
    run_hook('LogTransaction', $array);
}
function checkCbInvoiceID($invoiceId, $gateway = 'Unknown')
{
    $result = select_query('tblinvoices', 'id', array( 'id' => $invoiceId ));
    $data = mysql_fetch_array($result);
    $id = $data['id'];
    if( !$id )
    {
        logtransaction($gateway, $_REQUEST, "Invoice ID Not Found");
        exit();
    }
    return $id;
}
function checkCbTransID($transactionId)
{
    $result = select_query('tblaccounts', 'id', array( 'transid' => $transactionId ));
    $numRows = mysql_num_rows($result);
    if( $numRows )
    {
        exit();
    }
}
function callback3DSecureRedirect($invoiceId, $success = false)
{
    global $CONFIG;
    $systemUrl = $CONFIG['SystemSSLURL'] ? $CONFIG['SystemSSLURL'] : $CONFIG['SystemURL'];
    if( $success )
    {
        $redirectPage = $systemUrl . "/viewinvoice.php?id=" . $invoiceId . "&paymentsuccess=true";
    }
    else
    {
        $redirectPage = $systemUrl . "/viewinvoice.php?id=" . $invoiceId . "&paymentfailed=true";
    }
    echo "<html>\n    <head>\n        <title>" . $CONFIG['CompanyName'] . "</title>\n    </head>\n    <body onload=\"document.frmResultPage.submit();\">\n        <form name=\"frmResultPage\" method=\"post\" action=\"" . $redirectPage . "\" target=\"_parent\">\n            <noscript>\n                <br>\n                <br>\n                <center>\n                    <p style=\"color:#cc0000;\"><b>Processing Your Transaction</b></p>\n                    <p>JavaScript is currently disabled or is not supported by your browser.</p>\n                    <p>Please click Submit to continue the processing of your transaction.</p>\n                    <input type=\"submit\" value=\"Submit\">\n                </center>\n            </noscript>\n        </form>\n    </body>\n</html>";
    exit();
}
function getRecurringBillingValues($invoiceId)
{
    global $CONFIG;
    if( !function_exists('getBillingCycleMonths') )
    {
        include_once(ROOTDIR . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . "invoicefunctions.php");
    }
    $firstCyclePeriod = '';
    $firstCycleUnits = '';
    $invoiceId = (int) $invoiceId;
    $result = select_query('tblinvoiceitems', "tblinvoiceitems.relid," . "tblhosting.userid," . "tblhosting.billingcycle," . "tblhosting.packageid," . "tblhosting.regdate," . "tblhosting.nextduedate", array( 'invoiceid' => $invoiceId, 'type' => 'Hosting' ), "tblinvoiceitems`.`id", 'ASC', '', "tblhosting ON tblhosting.id=tblinvoiceitems.relid");
    $data = mysql_fetch_array($result);
    $relatedId = $data['relid'];
    $userId = $data['userid'];
    $billingCycle = $data['billingcycle'];
    $packageId = $data['packageid'];
    $registrationDate = $data['regdate'];
    $nextDueDate = $data['nextduedate'];
    if( !$relatedId || $billingCycle == "One Time" || $billingCycle == "Free Account" )
    {
        return false;
    }
    $result = select_query('tblinvoices', 'total,taxrate,taxrate2,paymentmethod,' . "(SELECT SUM(amountin)-SUM(amountout) FROM tblaccounts WHERE invoiceid=tblinvoices.id) AS amountpaid", array( 'id' => $invoiceId ));
    $data = mysql_fetch_array($result);
    $total = $data['total'];
    $taxRate = $data['taxrate'];
    $taxRate2 = $data['taxrate2'];
    $paymentMethod = $data['paymentmethod'];
    $amountPaid = $data['amountpaid'];
    $firstPaymentAmount = $total - $amountPaid;
    $recurringCyclePeriod = getBillingCycleMonths($billingCycle);
    $recurringCycleUnits = 'Months';
    if( 12 <= $recurringCyclePeriod )
    {
        $recurringCyclePeriod = $recurringCyclePeriod / 12;
        $recurringCycleUnits = 'Years';
    }
    $recurringAmount = 0;
    $query = "SELECT tblhosting.amount,tblinvoiceitems.taxed" . " FROM tblinvoiceitems" . " INNER JOIN tblhosting ON tblhosting.id=tblinvoiceitems.relid" . " WHERE tblinvoiceitems.invoiceid=" . $invoiceId . " AND tblinvoiceitems.type='Hosting'" . " AND tblhosting.billingcycle='" . db_escape_string($billingCycle) . "'";
    $result = full_query($query);
    while( $data = mysql_fetch_array($result) )
    {
        $productAmount = $data[0];
        $taxed = $data[1];
        if( $CONFIG['TaxType'] == 'Exclusive' && $taxed )
        {
            if( $CONFIG['TaxL2Compound'] )
            {
                $productAmount = $productAmount + ($productAmount * $taxRate) / 100;
                $productAmount = $productAmount + ($productAmount * $taxRate2) / 100;
            }
            else
            {
                $productAmount = $productAmount + ($productAmount * $taxRate) / 100 + ($productAmount * $taxRate2) / 100;
            }
        }
        $recurringAmount += $productAmount;
    }
    $query = "SELECT tblhostingaddons.recurring,tblhostingaddons.tax" . " FROM tblinvoiceitems" . " INNER JOIN tblhostingaddons ON tblhostingaddons.id=tblinvoiceitems.relid" . " WHERE tblinvoiceitems.invoiceid=" . $invoiceId . " AND tblinvoiceitems.type='Addon'" . " AND tblhostingaddons.billingcycle='" . db_escape_string($billingCycle) . "'";
    $result = full_query($query);
    while( $data = mysql_fetch_array($result) )
    {
        $addonAmount = $data[0];
        $addonTax = $data[1];
        if( $CONFIG['TaxType'] == 'Exclusive' && $addonTax )
        {
            if( $CONFIG['TaxL2Compound'] )
            {
                $addonAmount = $addonAmount + ($addonAmount * $taxRate) / 100;
                $addonAmount = $addonAmount + ($addonAmount * $taxRate2) / 100;
            }
            else
            {
                $addonAmount = $addonAmount + ($addonAmount * $taxRate) / 100 + ($addonAmount * $taxRate2) / 100;
            }
        }
        $recurringAmount += $addonAmount;
    }
    if( in_array($billingCycle, array( 'Annually', 'Biennially', 'Triennially' )) )
    {
        $cycleregperiods = array( 'Annually' => '1', 'Biennially' => '2', 'Triennially' => '3' );
        $query = "SELECT SUM(tbldomains.recurringamount)" . " FROM tblinvoiceitems" . " INNER JOIN tbldomains ON tbldomains.id=tblinvoiceitems.relid" . " WHERE tblinvoiceitems.invoiceid=" . $invoiceId . " AND tblinvoiceitems.type IN ('DomainRegister','DomainTransfer','Domain')" . " AND tbldomains.registrationperiod='" . db_escape_string($cycleregperiods[$billingCycle]) . "'";
        $result = full_query($query);
        $data = mysql_fetch_array($result);
        $domainAmount = $data[0];
        if( $CONFIG['TaxType'] == 'Exclusive' && $CONFIG['TaxDomains'] )
        {
            if( $CONFIG['TaxL2Compound'] )
            {
                $domainAmount = $domainAmount + ($domainAmount * $taxRate) / 100;
                $domainAmount = $domainAmount + ($domainAmount * $taxRate2) / 100;
            }
            else
            {
                $domainAmount = $domainAmount + ($domainAmount * $taxRate) / 100 + ($domainAmount * $taxRate2) / 100;
            }
        }
        $recurringAmount += $domainAmount;
    }
    $result = select_query('tblinvoices', 'duedate', array( 'id' => $invoiceId ));
    $data = mysql_fetch_array($result);
    $invoiceDueDate = $data['duedate'];
    $invoiceDueDate = str_replace('-', '', $invoiceDueDate);
    $overdue = $invoiceDueDate < date('Ymd');
    $result = select_query('tblproducts', 'proratabilling,proratadate,proratachargenextmonth', array( 'id' => $packageId ));
    $data = mysql_fetch_array($result);
    $proRataBilling = $data['proratabilling'];
    $proRataDate = $data['proratadate'];
    $proRataChargeNextMonth = $data['proratachargenextmonth'];
    if( $registrationDate == $nextDueDate && $proRataBilling )
    {
        $orderYear = substr($registrationDate, 0, 4);
        $orderMonth = substr($registrationDate, 5, 2);
        $orderDay = substr($registrationDate, 8, 2);
        $proRataValues = getProrataValues($billingCycle, 0, $proRataDate, $proRataChargeNextMonth, $orderDay, $orderMonth, $orderYear, $userId);
        $firstCyclePeriod = $proRataValues['days'];
        $firstCycleUnits = 'Days';
    }
    if( !$firstCyclePeriod )
    {
        $firstCyclePeriod = $recurringCyclePeriod;
    }
    if( !$firstCycleUnits )
    {
        $firstCycleUnits = $recurringCycleUnits;
    }
    $result = select_query('tblpaymentgateways', 'value', array( 'gateway' => $paymentMethod, 'setting' => 'convertto' ));
    $data = mysql_fetch_array($result);
    $convertTo = $data[0];
    if( $convertTo )
    {
        $currency = getCurrency($userId);
        $firstPaymentAmount = convertCurrency($firstPaymentAmount, $currency['id'], $convertTo);
        $recurringAmount = convertCurrency($recurringAmount, $currency['id'], $convertTo);
    }
    $firstPaymentAmount = format_as_currency($firstPaymentAmount);
    $recurringAmount = format_as_currency($recurringAmount);
    $recurringBillingValues = array(  );
    $recurringBillingValues['primaryserviceid'] = $relatedId;
    if( $firstPaymentAmount != $recurringAmount )
    {
        $recurringBillingValues['firstpaymentamount'] = $firstPaymentAmount;
        $recurringBillingValues['firstcycleperiod'] = $firstCyclePeriod;
        $recurringBillingValues['firstcycleunits'] = $firstCycleUnits;
    }
    $recurringBillingValues['recurringamount'] = $recurringAmount;
    $recurringBillingValues['recurringcycleperiod'] = $recurringCyclePeriod;
    $recurringBillingValues['recurringcycleunits'] = $recurringCycleUnits;
    $recurringBillingValues['overdue'] = $overdue;
    return $recurringBillingValues;
}
/**
 * Find an invoice id tied to a service id or transaction id.
 *
 * @param string $serviceID Service ID tied to an invoice.
 * @param string $transID Transaction ID tied to an invoice (default: empty string).
 *
 * @return int|null.
 */
function findInvoiceID($serviceID, $transID = '')
{
    $serviceID = (int) $serviceID;
    $query = "SELECT tblinvoices.id \n        FROM tblinvoiceitems \n        INNER JOIN tblinvoices \n        ON tblinvoices.id=tblinvoiceitems.invoiceid \n        WHERE tblinvoiceitems.relid=" . $serviceID . " AND tblinvoiceitems.type='Hosting' AND tblinvoices.status='Unpaid' \n        ORDER BY tblinvoices.id ASC";
    $result = full_query($query);
    $data = mysql_fetch_array($result);
    $invoiceID = $data[0];
    if( !$invoiceID )
    {
        $query = "SELECT tblinvoices.id\n            FROM tblinvoiceitems \n            INNER JOIN tblinvoices \n            ON tblinvoices.id=tblinvoiceitems.invoiceid \n            WHERE tblinvoiceitems.relid=" . $serviceID . " AND tblinvoiceitems.type='Hosting' AND tblinvoices.status='Paid' \n            ORDER BY tblinvoices.id DESC";
        $result = full_query($query);
        $data = mysql_fetch_array($result);
        $invoiceID = $data[0];
    }
    if( !$invoiceID && !empty($transID) )
    {
        $query = "SELECT tblinvoices.id\n            FROM tblinvoiceitems \n            INNER JOIN tblinvoices \n            ON tblinvoices.id=tblinvoiceitems.invoiceid \n            INNER JOIN tblhosting \n            ON tblhosting.id=tblinvoiceitems.relid \n            WHERE tblhosting.subscriptionid='" . db_escape_string($transID) . "' AND tblinvoiceitems.type='Hosting' AND tblinvoices.status='Unpaid' \n            ORDER BY tblinvoices.id ASC";
        $result = full_query($query);
        $data = mysql_fetch_array($result);
        $invoiceID = $data[0];
    }
    if( !$invoiceID && !empty($transID) )
    {
        $query = "SELECT tblinvoices.id\n            FROM tblinvoiceitems \n            INNER JOIN tblinvoices \n            ON tblinvoices.id=tblinvoiceitems.invoiceid \n            INNER JOIN tblhosting \n            ON tblhosting.id=tblinvoiceitems.relid \n            WHERE tblhosting.subscriptionid='" . db_escape_string($transID) . "' AND tblinvoiceitems.type='Hosting' AND tblinvoices.status='Paid' \n            ORDER BY tblinvoices.id DESC";
        $result = full_query($query);
        $data = mysql_fetch_array($result);
        $invoiceID = $data[0];
    }
    return $invoiceID;
}