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
if( !defined('WHMCS') )
{
    exit( "This file cannot be accessed directly" );
}
add_hook('AdminHomeWidgets', 1, 'widget_paypal_addon');
function widget_paypal_addon($vars)
{
    $title = "PayPal Overview";
    $params = array(  );
    $result = select_query('tbladdonmodules', 'setting,value', array( 'module' => 'paypal_addon' ));
    while( $data = mysql_fetch_array($result) )
    {
        $params[$data[0]] = $data[1];
    }
    $content = '';
    $adminroleid = get_query_val('tbladmins', 'roleid', array( 'id' => $_SESSION['adminid'] ));
    if( $params['showbalance' . $adminroleid] )
    {
        $url = "https://api-3t.paypal.com/nvp";
        $postfields = $resultsarray = array(  );
        $postfields['USER'] = $params['username'];
        $postfields['PWD'] = $params['password'];
        $postfields['SIGNATURE'] = $params['signature'];
        $postfields['METHOD'] = 'GetBalance';
        $postfields['RETURNALLCURRENCIES'] = '1';
        $postfields['VERSION'] = "56.0";
        $result = curlCall($url, $postfields);
        $resultsarray2 = explode("&", $result);
        foreach( $resultsarray2 as $line )
        {
            $line = explode("=", $line);
            $resultsarray[$line[0]] = urldecode($line[1]);
        }
        $paypalbal = array(  );
        if( strtolower($resultsarray['ACK']) != 'success' )
        {
            $paypalbal[] = "Error: " . $resultsarray['L_LONGMESSAGE0'];
        }
        else
        {
            for( $i = 0; $i <= 20; $i++ )
            {
                if( isset($resultsarray['L_AMT' . $i]) )
                {
                    $paypalbal[] = number_format($resultsarray['L_AMT' . $i], 2, ".", ',') . " " . $resultsarray['L_CURRENCYCODE' . $i];
                }
            }
        }
        $content .= "<div style=\"margin:10px;padding:10px;background-color:#EFFAE4;text-align:center;font-size:16px;color:#000;\">PayPal Balance: <b>" . implode(" ~ ", $paypalbal) . "</b></div>";
    }
    $content .= "<form method=\"post\" action=\"addonmodules.php?module=paypal_addon\">\n<div align=\"center\" style=\"margin:10px;font-size:16px;\">Lookup PayPal Transaction ID: <input type=\"text\" name=\"transid\" size=\"30\" value=\"" . $_POST['transid'] . "\" style=\"font-size:16px;\" /> <input type=\"submit\" name=\"search\" value=\"Go\" /></div>\n<div align=\"right\"><a href=\"addonmodules.php?module=paypal_addon\">Advanced Search &raquo;</a></div>\n</form>";
    return array( 'title' => $title, 'content' => $content );
}