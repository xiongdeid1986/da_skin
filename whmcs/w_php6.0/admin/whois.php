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
$aInt = new WHMCS_Admin("WHOIS Lookups");
$aInt->title = $aInt->lang('whois', 'title');
$aInt->sidebar = 'utilities';
$aInt->icon = 'domains';
$aInt->requiredFiles(array( 'domainfunctions', 'whoisfunctions' ));
if( $action == 'checkavailability' )
{
    check_token("WHMCS.admin.default");
    $result = lookupDomain($sld, $tld);
    echo $result['result'];
    exit();
}
$code = "<form method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "\">\n<p align=\"center\" style=\"font-size:18px;\">www. <input type=\"text\" name=\"domain\" value=\"" . $domain . "\" size=\"40\" style=\"font-size:18px;\" /> <input type=\"submit\" value=\"Lookup Domain\" class=\"btn\" /></p>\n</form>";
if( $domain = $whmcs->get_req_var('domain') )
{
    check_token("WHMCS.admin.default");
    $domains = new WHMCS_Domains();
    $domainparts = $domains->splitAndCleanDomainInput($domain);
    $isValid = $domains->checkDomainisValid($domainparts);
    if( $isValid )
    {
        $whois = new WHMCS_WHOIS();
        $whois->init();
        if( $whois->canLookupTLD($domainparts['tld']) )
        {
            $result = $whois->lookup($domainparts);
            if( $result['result'] == 'available' )
            {
                $code .= "<p align=\"center\" style=\"font-size:18px;color:#669900;\">" . sprintf($aInt->lang('whois', 'available'), $checkdomain) . "</p>";
            }
            else
            {
                if( $result['result'] == 'error' )
                {
                    $code .= "<p align=\"center\" style=\"font-size:18px;color:#cc0000;\">" . $aInt->lang('whois', 'error') . "</p><p align=\"center\">" . $result['errordetail'] . "</p>";
                }
                else
                {
                    $code .= "<p align=\"center\" style=\"font-size:18px;color:#cc0000;\">" . sprintf($aInt->lang('whois', 'unavailable'), $checkdomain) . "</p>";
                    $code .= "<p><strong>" . $aInt->lang('whois', 'whois') . "</strong></p><p>" . $result['whois'] . "</p>";
                }
            }
        }
        else
        {
            $code .= "<p align=\"center\" style=\"font-size:18px;color:#cc0000;\">" . sprintf($aInt->lang('whois', 'invalidtld'), $domainparts['tld']) . "</p>";
        }
    }
    else
    {
        $code .= "<p align=\"center\" style=\"font-size:18px;color:#cc0000;\">" . $aInt->lang('whois', 'invaliddomain') . "</p>";
    }
}
$aInt->content = $code;
$aInt->display();