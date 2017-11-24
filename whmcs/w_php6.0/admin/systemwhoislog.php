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
$aInt = new WHMCS_Admin("View WHOIS Lookup Log");
$aInt->title = $aInt->lang('system', 'whois');
$aInt->sidebar = 'utilities';
$aInt->icon = 'logs';
$aInt->sortableTableInit('date');
$numrows = get_query_val('tblwhoislog', "COUNT(*)", '');
$result = select_query('tblwhoislog', '', '', 'id', 'DESC', $page * $limit . ',' . $limit);
while( $data = mysql_fetch_array($result) )
{
    $id = $data['id'];
    $date = $data['date'];
    $domain = $data['domain'];
    $ip = $data['ip'];
    $tabledata[] = array( fromMySQLDate($date, true), "<a href=\"#\" onclick=\"\$('#frmWhoisDomain').val('" . addslashes($domain) . "');\$('#frmWhois').submit();return false\">" . $domain . "</a>", "<a href=\"http://www.geoiptool.com/en/?IP=" . $ip . "\" target=\"_blank\">" . $ip . "</a>" );
}
$content = $aInt->sortableTable(array( $aInt->lang('fields', 'date'), $aInt->lang('fields', 'domain'), $aInt->lang('fields', 'ipaddress') ), $tabledata);
$content .= "\n<form method=\"post\" action=\"whois.php\" target=\"_blank\" id=\"frmWhois\">\n<input type=\"hidden\" name=\"domain\" value=\"\" id=\"frmWhoisDomain\" />\n</form>\n";
$aInt->content = $content;
$aInt->display();