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
$aInt = new WHMCS_Admin("CSV Downloads");
header("Pragma: public");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0, private");
header("Cache-Control: private", false);
header("Content-Type: application/octet-stream");
header("Content-Transfer-Encoding: binary");
$report = $whmcs->get_req_var('report');
$type = $whmcs->get_req_var('type');
$print = $whmcs->get_req_var('print');
$currencyid = $whmcs->get_req_var('currencyid');
$month = $whmcs->get_req_var('month');
$year = $whmcs->get_req_var('year');
if( $report )
{
    require("../includes/reportfunctions.php");
    $chart = new WHMCSChart();
    $currencies = array(  );
    $result = select_query('tblcurrencies', '', '', 'code', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $code = $data['code'];
        $currencies[$id] = $code;
        if( !$currencyid && $data['default'] )
        {
            $currencyid = $id;
        }
        if( $data['default'] )
        {
            $defaultcurrencyid = $id;
        }
    }
    $currency = getCurrency('', $currencyid);
    $months = array( '', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
    $month = (int) $month;
    $year = (int) $year;
    if( !$month )
    {
        $month = date('m');
    }
    if( !$year )
    {
        $year = date('Y');
    }
    $currentmonth = $months[(int) $month];
    $currentyear = $year;
    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
    $gateways = new WHMCS_Gateways();
    $data = $reportdata = $chartsdata = $args = array(  );
    $report = preg_replace("/[^0-9a-z-_]/i", '', $report);
    $reportfile = "../modules/reports/" . $report . ".php";
    if( file_exists($reportfile) )
    {
        require($reportfile);
        $rows = $trow = array(  );
        foreach( $reportdata['tableheadings'] as $heading )
        {
            $trow[] = $heading;
        }
        $rows[] = $trow;
        if( $reportdata['tablevalues'] )
        {
            foreach( $reportdata['tablevalues'] as $values )
            {
                $trow = array(  );
                foreach( $values as $value )
                {
                    if( substr($value, 0, 2) == "**" )
                    {
                        $trow[] = csv_clean(substr($value, 2));
                    }
                    else
                    {
                        $trow[] = csv_clean($value);
                    }
                }
                $rows[] = $trow;
            }
        }
        header("Content-disposition: attachment; filename=" . $report . '_export_' . date('Ymd') . ".csv");
        echo strip_tags($reportdata['title']) . "\n";
        foreach( $rows as $row )
        {
            echo implode(',', $row) . "\n";
        }
    }
    else
    {
        exit( "Report File Not Found" );
    }
}
else
{
    if( $type == 'pdfbatch' )
    {
        require(ROOTDIR . "/includes/countries.php");
        require(ROOTDIR . "/includes/clientfunctions.php");
        require(ROOTDIR . "/includes/invoicefunctions.php");
        $result = select_query('tblpaymentgateways', 'gateway,value', array( 'setting' => 'name' ), 'order', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $gatewaysarray[$data['gateway']] = $data['value'];
        }
        $invoice = new WHMCS_Invoice();
        $invoice->pdfCreate($aInt->lang('reports', 'pdfbatch') . " " . date('Y-m-d'));
        $orderby = 'id';
        if( $sortorder == "Invoice Number" )
        {
            $orderby = 'invoicenum';
        }
        else
        {
            if( $sortorder == "Date Paid" )
            {
                $orderby = 'datepaid';
            }
            else
            {
                if( $sortorder == "Due Date" )
                {
                    $orderby = 'duedate';
                }
                else
                {
                    if( $sortorder == "Client ID" )
                    {
                        $orderby = 'userid';
                    }
                    else
                    {
                        if( $sortorder == "Client Name" )
                        {
                            $orderby = "tblclients`.`firstname` ASC,`tblclients`.`lastname";
                        }
                    }
                }
            }
        }
        $clientWhere = is_numeric($userid) && 0 < $userid ? " AND tblinvoices.userid=" . (int) $userid : '';
        if( $filterby == "Date Created" )
        {
            $filterby = 'date';
        }
        else
        {
            if( $filterby == "Due Date" )
            {
                $filterby = 'duedate';
            }
            else
            {
                $filterby = 'datepaid';
                $dateto .= " 23:59:59";
            }
        }
        $statuses_in_clause = db_build_in_array($statuses);
        $paymentmethods_in_clause = db_build_in_array($paymentmethods);
        $batchpdf_where_clause = "tblinvoices." . $filterby . " >= '" . toMySQLDate($datefrom) . "' AND tblinvoices." . $filterby . "<='" . toMySQLDate($dateto) . "' AND tblinvoices.status IN (" . $statuses_in_clause . ")" . " AND tblinvoices.paymentmethod IN (" . $paymentmethods_in_clause . ")" . $clientWhere;
        $batchpdfresult = select_query('tblinvoices', "tblinvoices.id", $batchpdf_where_clause, $orderby, 'ASC', '', "tblclients ON tblclients.id=tblinvoices.userid");
        $numrows = mysql_num_rows($batchpdfresult);
        if( !$numrows )
        {
            redir("report=pdf_batch&noresults=1", "reports.php");
        }
        else
        {
            header("Content-Disposition: attachment; filename=\"" . $aInt->lang('reports', 'pdfbatch') . " " . date('Y-m-d') . ".pdf\"");
        }
        while( $data = mysql_fetch_array($batchpdfresult) )
        {
            $invoice->pdfInvoicePage($data['id']);
        }
        $pdfdata = $invoice->pdfOutput();
        echo $pdfdata;
    }
}
/**
 * Filter a string for CSV display.
 *
 * Perform filters on a string so it can be properly displayed as a cell in a
 * CSV document:
 * * Decode all HTML entities.
 * * Remove all HTML tags.
 * * Escape quotation marks with a second quotation mark.
 * * Encapsulate the string in quotation marks if it contains a comma, so the
 * comma won't split the string into two CSV cells.
 *
 * @param string $var
 * @return string
 */
function csv_clean($var)
{
    $var = WHMCS_Input_Sanitize::decode($var);
    $var = strip_tags($var);
    $var = str_replace("\"", "\"\"", $var);
    if( strstr($var, ',') )
    {
        $var = "\"" . $var . "\"";
    }
    return $var;
}
function csv_output($query)
{
    global $fields;
    $result = full_query($query);
    while( $data = mysql_fetch_array($result) )
    {
        foreach( $fields as $field )
        {
            echo csv_clean($data[$field]) . ',';
        }
        echo "\n";
    }
}