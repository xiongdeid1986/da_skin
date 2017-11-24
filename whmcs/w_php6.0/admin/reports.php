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
$aInt = new WHMCS_Admin("View Reports");
$aInt->title = 'Reports';
$aInt->sidebar = 'reports';
$aInt->icon = 'reports';
$aInt->requiredFiles(array( 'reportfunctions' ));
$aInt->helplink = 'Reports';
$report = $whmcs->get_req_var('report');
$displaygraph = $whmcs->get_req_var('displaygraph');
$print = $whmcs->get_req_var('print');
$currencyid = $whmcs->get_req_var('currencyid');
$month = $whmcs->get_req_var('month');
$year = $whmcs->get_req_var('year');
if( $displaygraph )
{
    $displaygraph = preg_replace("/[^0-9a-z-_]/i", '', $displaygraph);
    $graphfile = "../modules/reports/" . $displaygraph . ".php";
    if( file_exists($graphfile) )
    {
        require($graphfile);
        exit();
    }
    exit( "Graph File Not Found" );
}
if( $print )
{
    echo "<html>\n<head>\n<title>WHMCompleteSolution - Printer Friendly Report</title>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=";
    echo $CONFIG['Charset'];
    echo "\">\n<style>\nbody,td {\n    font-family: Tahoma;\n    font-size: 11px;\n}\nh1,h2 {\n    font-size: 16px;\n}\na {\n    color: #000000;\n}\n</style>\n</head>\n<body>\n<p><img src=\"";
    echo $CONFIG['LogoURL'];
    echo "\"></p>\n";
}
else
{
    $text_reports = $graph_reports = array(  );
    $dh = opendir("../modules/reports/");
    while( false !== ($file = readdir($dh)) )
    {
        if( $file != "index.php" && is_file("../modules/reports/" . $file) )
        {
            $file = str_replace(".php", '', $file);
            if( substr($file, 0, 5) != 'graph' )
            {
                $nicename = str_replace('_', " ", $file);
                $nicename = titleCase($nicename);
                $text_reports[$file] = $nicename;
            }
        }
    }
    closedir($dh);
    asort($text_reports);
    $aInt->assign('text_reports', $text_reports);
    $aInt->assign('graph_reports', $graph_reports);
    ob_start();
}
if( $report )
{
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
    $requeststr = "?" . http_build_query($_REQUEST);
    $chart = new WHMCSChart();
    $gateways = new WHMCS_Gateways();
    $data = $reportdata = $chartsdata = $args = array(  );
    if( $month == '1' )
    {
        $prevMonthLink = "month=12&year=" . ($year - 1);
        $prevMonthText = "&laquo; December " . ($year - 1);
    }
    else
    {
        $prevMonthLink = "month=" . ($month - 1) . "&year=" . $year;
        $prevMonthText = "&laquo; " . $months[$month - 1] . " " . $year;
    }
    if( $year . str_pad($month, 2, '0', STR_PAD_LEFT) < date('Ym') )
    {
        if( $month == '12' )
        {
            $nextMonthLink = "month=1&year=" . ($year + 1);
            $nextMonthText = "January " . ($year + 1) . " &raquo;";
        }
        else
        {
            $nextMonthLink = "month=" . ($month + 1) . "&year=" . $year;
            $nextMonthText = $months[$month + 1] . " " . $year . " &raquo;";
        }
    }
    else
    {
        $nextMonthLink = $nextMonthText = '';
    }
    $prevYearLink = "year=" . ($year - 1);
    $prevYearText = "&laquo; " . ($year - 1);
    if( $year + 1 <= date('Y') )
    {
        $nextYearLink = "year=" . ($year + 1);
        $nextYearText = ($year + 1) . " &raquo;";
    }
    else
    {
        $nextYearLink = $nextYearText = '';
    }
    $moduleType = $whmcs->get_req_var('moduletype');
    $moduleName = $whmcs->get_req_var('modulename');
    $subDirectory = $whmcs->get_req_var('subdir');
    $reportPath = ROOTDIR . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR;
    if( $moduleType == 'addons' )
    {
        if( !isValidforPath($moduleName) || $subDirectory && !isValidforPath($subDirectory) )
        {
            redir();
        }
        $reportPath .= 'addons' . DIRECTORY_SEPARATOR . $moduleName;
        if( $subDirectory )
        {
            $reportPath .= DIRECTORY_SEPARATOR . $subDirectory;
        }
    }
    else
    {
        $reportPath .= 'reports';
    }
    $reportPath .= DIRECTORY_SEPARATOR . preg_replace("/[^0-9a-z-_]/i", '', $report) . ".php";
    if( file_exists($reportPath) )
    {
        require($reportPath);
    }
    else
    {
        redir();
    }
    if( !is_array($reportdata) )
    {
        exit( "\$reportdata must be returned as an array" );
    }
    if( array_key_exists('title', $reportdata) )
    {
        echo "<h2>" . $reportdata['title'] . "</h2>";
    }
    if( array_key_exists('description', $reportdata) )
    {
        echo "<p>" . $reportdata['description'] . "</p>";
    }
    if( array_key_exists('currencyselections', $reportdata) )
    {
        $requestArray = $_REQUEST;
        if( array_key_exists('currencyid', $requestArray) )
        {
            unset($requestArray['currencyid']);
        }
        $requestString = http_build_query($requestArray);
        $currencieslist = '';
        foreach( $currencies as $listid => $listname )
        {
            if( $currencyid == $listid )
            {
                $currencieslist .= "<b>";
            }
            else
            {
                $currencieslist .= "<a href=\"?" . $requestString . "&currencyid=" . $listid . "\">";
            }
            $currencieslist .= $listname . "</b></a> | ";
        }
        echo "<p align=\"center\">Choose Currency: " . substr($currencieslist, 0, 0 - 3) . "</p>";
    }
    if( array_key_exists('headertext', $reportdata) )
    {
        echo $reportdata['headertext'] . "<br /><br />";
    }
    if( array_key_exists('tableheadings', $reportdata) && is_array($reportdata['tableheadings']) )
    {
        echo "<table width=100% ";
        if( $print )
        {
            echo "border=1 cellspacing=0";
        }
        else
        {
            echo "cellspacing=1";
        }
        echo " bgcolor=\"#cccccc\"><tr bgcolor=\"#efefef\" style=\"text-align:center;font-weight:bold;\">";
        foreach( $reportdata['tableheadings'] as $heading )
        {
            echo "<td>" . $heading . "</td>";
        }
        if( array_key_exists('drilldown', $reportdata) && is_array($reportdata['drilldown']) )
        {
            echo "<td>Drill Down</td>";
        }
        echo "</tr>";
        if( array_key_exists('tablesubheadings', $reportdata) && is_array($reportdata['tablesubheadings']) )
        {
            echo "<tr bgcolor=\"#efefef\" style=\"text-align:center;font-weight:bold;\">";
            foreach( $reportdata['tablesubheadings'] as $heading )
            {
                echo "<td>" . $heading . "</td>";
            }
            if( is_array($reportdata['drilldown']) )
            {
                echo "<td>Drill Down</td>";
            }
            echo "</tr>";
        }
        $columncount = count($reportdata['tableheadings']);
        if( array_key_exists('drilldown', $reportdata) && is_array($reportdata['drilldown']) )
        {
            $columncount++;
        }
        if( array_key_exists('tablevalues', $reportdata) && is_array($reportdata['tablevalues']) )
        {
            foreach( $reportdata['tablevalues'] as $num => $values )
            {
                if( isset($values[0]) && $values[0] == 'HEADER' )
                {
                    echo "<tr bgcolor=\"#efefef\" style=\"text-align:center;font-weight:bold;\">";
                    foreach( $values as $k => $value )
                    {
                        if( 0 < $k )
                        {
                            echo "<td>" . $value . "</td>";
                        }
                    }
                    echo "</tr>";
                }
                else
                {
                    $rowbgcolor = "#ffffff";
                    if( isset($values[0]) && strlen($values[0]) == 7 && substr($values[0], 0, 1) == "#" )
                    {
                        $rowbgcolor = $values[0];
                        unset($values[0]);
                    }
                    echo "<tr bgcolor=\"" . $rowbgcolor . "\" style=\"text-align:center;\">";
                    foreach( $values as $value )
                    {
                        if( substr($value, 0, 2) == "**" )
                        {
                            echo "<td bgcolor=\"#efefef\" colspan=\"" . $columncount . "\" align=\"left\">&nbsp;" . substr($value, 2) . "</td>";
                        }
                        else
                        {
                            echo "<td>" . $value . "</td>";
                        }
                    }
                    if( array_key_exists('drilldown', $reportdata) && is_array($reportdata['drilldown'][$num]['tableheadings']) )
                    {
                        echo "<td><a href=\"#\" onclick=\"\$('#drilldown" . $num . "').fadeToggle();return false\">Drill Down</a></td>";
                    }
                    echo "</tr>";
                    if( array_key_exists('drilldown', $reportdata) && is_array($reportdata['drilldown'][$num]['tableheadings']) )
                    {
                        echo "<tr bgcolor=\"#FFFFCC\" id=\"drilldown" . $num . "\" style=\"display:none;\"><td colspan=\"" . $columncount . "\" style=\"padding:20px;\">";
                        echo "<table width=100% ";
                        if( $print == 'true' )
                        {
                            echo "border=1 cellspacing=0";
                        }
                        else
                        {
                            echo "cellspacing=1";
                        }
                        echo " bgcolor=\"#cccccc\"><tr bgcolor=\"#efefef\" style=\"text-align:center;font-weight:bold;\">";
                        foreach( $reportdata['drilldown'][$num]['tableheadings'] as $value )
                        {
                            echo "<td>" . $value . "</td>";
                        }
                        if( !isset($reportdata['drilldown'][$num]['tablevalues']) )
                        {
                            echo "<tr bgcolor=\"#ffffff\"><td align=\"center\" colspan=\"" . $columncount . "\">No Records Found</td></tr>";
                        }
                        else
                        {
                            foreach( $reportdata['drilldown'][$num]['tablevalues'] as $num => $values )
                            {
                                echo "<tr bgcolor=\"#ffffff\" style=\"text-align:center;\">";
                                foreach( $values as $value )
                                {
                                    echo "<td>" . $value . "</td>";
                                }
                                echo "</tr>";
                            }
                        }
                        echo "</tr></table></td></tr>";
                    }
                }
            }
        }
        else
        {
            echo "<tr bgcolor=\"#ffffff\" style=\"text-align:center;\"><td colspan=\"" . $columncount . "\">" . $aInt->lang('reports', 'nodata') . "</td></tr>";
        }
        echo "</table>";
    }
    if( array_key_exists('monthspagination', $reportdata) && $reportdata['monthspagination'] )
    {
        $requestArrayForYears = $_REQUEST;
        if( array_key_exists('month', $requestArrayForYears) )
        {
            unset($requestArray['month']);
        }
        if( array_key_exists('year', $requestArrayForYears) )
        {
            unset($requestArray['year']);
        }
        $requestString = http_build_query($requestArrayForYears);
        echo "<br /><table width=\"90%\" align=\"center\"><tr><td>" . "<a href=\"?" . $requestString . "&" . $prevMonthLink . "\">" . $prevMonthText . "</a></td><td align=\"right\">" . "<a href=\"?" . $requestString . "&" . $nextMonthLink . "\">" . $nextMonthText . "</a></td></tr></table>";
    }
    if( array_key_exists('yearspagination', $reportdata) && $reportdata['yearspagination'] )
    {
        $requestArrayForMonths = $_REQUEST;
        if( array_key_exists('year', $requestArrayForMonths) )
        {
            unset($requestArrayForMonths['year']);
        }
        $requestString = http_build_query($requestArrayForMonths);
        echo "<br /><table width=\"90%\" align=\"center\"><tr><td>" . "<a href=\"?" . $requestString . "&" . $prevYearLink . "\">" . $prevYearText . "</a></td><td align=\"right\">" . "<a href=\"?" . $requestString . "&" . $nextYearLink . "\">" . $nextYearText . "</a></td></tr></table>";
    }
    if( is_array($data) && array_key_exists('footertext', $data) )
    {
        echo "<p>" . $data['footertext'] . "</p>";
    }
    if( array_key_exists('footertext', $reportdata) )
    {
        echo $reportdata['footertext'];
    }
}
else
{
    echo "<p>" . $aInt->lang('reports', 'description') . "</p>";
    $reports = array( 'General' => array( 'daily_performance', 'disk_usage_summary', 'monthly_orders', 'product_suspensions', 'promotions_usage', '', '' ), 'Billing' => array( 'aging_invoices', 'credits_reviewer', 'direct_debit_processing', 'sales_tax_liability', '', '' ), 'Income' => array( 'annual_income_report', 'income_forecast', 'income_by_product', 'monthly_transactions', 'sales_tax_liability', 'server_revenue_forecasts', '' ), 'Clients' => array( 'new_customers', 'client_sources', 'client_statement', 'clients_by_country', 'top_10_clients_by_income', 'affiliates_overview', '', '', '' ), 'Support' => array( 'support_ticket_replies', 'ticket_feedback_scores', 'ticket_feedback_comments', 'ticket_ratings_reviewer', 'ticket_tags', '' ), 'Exports' => array( 'clients', 'domains', 'invoices', 'services', 'transactions', 'pdf_batch', '', '', '' ) );
    foreach( $reports as $type => $reports_array )
    {
        echo "<h2 align=\"center\">" . $type . "</h2>";
        $reps = array(  );
        $btnclass = '';
        if( $type == 'General' )
        {
            $btnclass = 'btn-info';
        }
        if( $type == 'Exports' )
        {
            $btnclass = 'btn-inverse';
        }
        foreach( $reports_array as $report_name )
        {
            if( isset($text_reports[$report_name]) )
            {
                $reps[] = "<input type=\"button\" value=\"" . $text_reports[$report_name] . "\" class=\"btn " . $btnclass . "\" onclick=\"window.location='reports.php?report=" . $report_name . "'\" />";
                unset($text_reports[$report_name]);
            }
        }
        echo "<div align=\"center\" style=\"padding:0 0 10px 0;\">" . implode(" ", $reps) . "</div>";
    }
    if( count($text_reports) )
    {
        echo "<h2 align=\"center\">Other</h2>";
        $reps = array(  );
        foreach( $text_reports as $report_name => $discard )
        {
            if( isset($text_reports[$report_name]) )
            {
                $reps[] = "<input type=\"button\" value=\"" . $text_reports[$report_name] . "\" class=\"btn\" onclick=\"window.location='reports.php?report=" . $report_name . "'\" />";
            }
        }
        echo "<div align=\"center\" style=\"padding:0 0 10px 0;\">" . implode(" ", $reps) . "</div>";
    }
}
if( $report )
{
    echo "<p>" . $aInt->lang('reports', 'generatedon') . " " . fromMySQLDate(date("Y-m-d H:i:s"), 'time') . "</p>\n<p align=\"center\">";
    if( $print == 'true' )
    {
        echo "<a href=\"javascript:window.close()\">" . $aInt->lang('reports', 'closewindow') . "</a>";
    }
    else
    {
        $requestString = http_build_query($_REQUEST);
        echo "<strong>" . $aInt->lang('reports', 'tools') . "</strong> &nbsp;&nbsp;&nbsp; <a href=\"csvdownload.php?" . $requestString . "\"><img src=\"images/icons/csvexports.png\" align=\"absmiddle\" border=\"0\" /> " . $aInt->lang('reports', 'exportcsv') . "</a> &nbsp;&nbsp;&nbsp; <a href=\"" . $whmcs->getPhpSelf() . "?" . $requestString . "&print=true\" target=\"_blank\"><img src=\"images/icons/print.png\" align=\"absmiddle\" border=\"0\" /> " . $aInt->lang('reports', 'printableversion') . "</a>";
    }
    echo "</p>";
}
if( $print )
{
    echo "\n</body>\n</html>";
}
else
{
    $content = ob_get_contents();
    ob_end_clean();
    $aInt->content = $content;
    $aInt->display();
}