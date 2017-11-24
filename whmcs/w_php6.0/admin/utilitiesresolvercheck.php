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
$aInt = new WHMCS_Admin("Domain Resolver Checker");
$aInt->title = $aInt->lang('utilitiesresolvercheck', 'domainresolverchecktitle');
$aInt->sidebar = 'utilities';
$aInt->icon = 'domainresolver';
$aInt->helplink = "Domain Resolver Checker";
$aInt->requiredFiles(array( 'modulefunctions' ));
ob_start();
echo "\n<p>";
echo $aInt->lang('utilitiesresolvercheck', 'pagedesc');
echo "</p>\n\n";
if( $step == '' )
{
    echo "\n<p align=\"center\">\n<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "?step=2\">\n<select name=\"server\" onChange=\"submit()\"><option value=\"\">Check All";
    $result = select_query('tblservers', '', array( 'disabled' => '0' ), 'name', 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $serverid = $data['id'];
        $servername = $data['name'];
        $activeserver = $data['active'];
        $servermaxaccounts = $data['maxaccounts'];
        $query2 = "SELECT COUNT(id) FROM tblhosting WHERE server=" . (int) $serverid . " AND domainstatus!='Pending' AND domainstatus!='Terminated'";
        $result2 = full_query($query2);
        $data2 = mysql_fetch_array($result2);
        $servernumaccounts = $data2[0];
        echo "<option value=\"" . $serverid . "\"";
        if( $server == $serverid )
        {
            echo " selected";
        }
        echo ">" . $servername . " (" . $servernumaccounts . " Accounts)";
    }
    echo "</select>\n<input type=\"submit\" value=\"";
    echo $aInt->lang('utilitiesresolvercheck', 'runcheck');
    echo "\" class=\"button\">\n</form>\n</p>\n\n";
}
else
{
    if( $step == '2' )
    {
        check_token("WHMCS.admin.default");
        echo "\n<form method=\"post\" action=\"sendmessage.php?type=product&multiple=true\" id=\"resolverfrm\">\n\n<div class=\"tablebg\">\n<table class=\"datatable\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n<tr><th width=\"20\"></th><th>";
        echo $aInt->lang('fields', 'domain');
        echo "</th><th>";
        echo $aInt->lang('fields', 'ipaddress');
        echo "</th><th>";
        echo $aInt->lang('utilitiesresolvercheck', 'package');
        echo "</th><th>";
        echo $aInt->lang('fields', 'status');
        echo "</th><th>";
        echo $aInt->lang('fields', 'client');
        echo "</th></tr>\n";
        if( $server )
        {
            $where = array( 'id' => $server );
        }
        $result = select_query('tblservers', '', $where, 'name', 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $serverid = $data['id'];
            $servername = $data['name'];
            $serverip = $data['ipaddress'];
            $serverassignedips = $data['assignedips'];
            $serverusername = $data['username'];
            $serverpassword = $data['password'];
            $serverassignedips = explode("\n", $serverassignedips);
            array_walk($serverassignedips, 'assignedips_trim');
            $serverassignedips[] = $serverip;
            echo "<tr><td colspan=\"6\" style=\"background-color:#efefef;font-weight:bold;\">" . $servername . " - " . $serverip . "</td></tr>";
            $serviceid = '';
            $result2 = select_query('tblhosting', "tblhosting.id AS serviceid,tblhosting.domain,tblhosting.domainstatus,tblhosting.userid,tblproducts.name,tblclients.firstname,tblclients.lastname,tblclients.companyname", "server='" . $serverid . "' AND domain!='' AND (domainstatus='Active' OR domainstatus='Suspended')", 'domain', 'ASC', '', "tblproducts ON tblhosting.packageid=tblproducts.id INNER JOIN tblclients ON tblhosting.userid=tblclients.id");
            while( $data = mysql_fetch_array($result2) )
            {
                $serviceid = $data['serviceid'];
                $domain = $data['domain'];
                $package = $data['name'];
                $status = $data['domainstatus'];
                $userid = $data['userid'];
                $firstname = $data['firstname'];
                $lastname = $data['lastname'];
                $companyname = $data['companyname'];
                $client = $firstname . " " . $lastname;
                if( $companyname )
                {
                    $client .= " (" . $companyname . ")";
                }
                $ipaddress = gethostbyname($domain);
                $bgcolor = !in_array($ipaddress, $serverassignedips) ? " style=\"background-color:#ffebeb\"" : '';
                echo "<tr style=\"text-align:center;\"><td" . $bgcolor . "><input type=\"checkbox\" name=\"selectedclients[]\" value=\"" . $serviceid . "\"></td><td" . $bgcolor . "><a href=\"clientshosting.php?userid=" . $userid . "&id=" . $serviceid . "\">" . $domain . "</a></td><td" . $bgcolor . ">" . $ipaddress . "</td><td" . $bgcolor . ">" . $package . "</td><td" . $bgcolor . ">" . $status . "</td><td" . $bgcolor . "><a href=\"clientssummary.php?userid=" . $userid . "\">" . $client . "</a></td></tr>";
            }
            if( !$serviceid )
            {
                echo "<tr bgcolor=\"#ffffff\"><td colspan=\"6\" align=\"center\">" . $aInt->lang('global', 'norecordsfound') . "</td></tr>";
            }
        }
        echo "</table>\n</div>\n\n<p>";
        echo $aInt->lang('global', 'withselected');
        echo ": <input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'sendmessage');
        echo "\" class=\"button\" /> <input type=\"button\" value=\"";
        echo $aInt->lang('utilitiesresolvercheck', 'terminateonserver');
        echo "\" onclick=\"showDialog('terminateaccts')\" class=\"button\" style=\"color:#cc0000;font-weight:bold;\" /></p>\n\n</form>\n\n<p>";
        echo $aInt->lang('utilitiesresolvercheck', 'dediipwarning');
        echo "</p>\n\n";
        echo $aInt->jqueryDialog('terminateaccts', addslashes($aInt->lang('utilitiesresolvercheck', 'terminateonserver')), addslashes($aInt->lang('utilitiesresolvercheck', 'delsureterminateonserver')), array( 'Yes' => "window.location='?step=terminate&'+\$('#resolverfrm').serialize();", 'No' => '' ));
    }
    else
    {
        if( $step == 'terminate' )
        {
            check_token("WHMCS.admin.default");
            echo "<h3>" . $aInt->lang('utilitiesresolvercheck', 'terminatingaccts') . "</h3>\n<ul>";
            foreach( $selectedclients as $serviceid )
            {
                $result = select_query('tblhosting', "tblhosting.id AS serviceid,tblhosting.domain,tblhosting.domainstatus,tblhosting.userid,tblproducts.name,tblclients.firstname,tblclients.lastname,tblclients.companyname,tblproducts.servertype", array( "tblhosting.id" => $serviceid ), '', '', '', "tblproducts ON tblhosting.packageid=tblproducts.id INNER JOIN tblclients ON tblhosting.userid=tblclients.id");
                $data = mysql_fetch_array($result);
                $serviceid = $data['serviceid'];
                $domain = $data['domain'];
                $package = $data['name'];
                $status = $data['domainstatus'];
                $userid = $data['userid'];
                $firstname = $data['firstname'];
                $lastname = $data['lastname'];
                $companyname = $data['companyname'];
                $module = $data['servertype'];
                $client = $firstname . " " . $lastname;
                if( $companyname )
                {
                    $client .= " (" . $companyname . ")";
                }
                if( $module )
                {
                    if( !isValidforPath($module) )
                    {
                        exit( "Invalid Server Module Name" );
                    }
                    $modulepath = ROOTDIR . '/modules/servers/' . $module . '/' . $module . ".php";
                    if( file_exists($modulepath) )
                    {
                        require_once($modulepath);
                    }
                }
                $result = ServerTerminateAccount($serviceid);
                if( $result != 'success' )
                {
                    $result = "Failed: " . $result;
                }
                else
                {
                    $result = "Successful!";
                }
                echo "<li>" . $client . " - " . $package . " (" . $domain . ") - " . $result . "</li>";
            }
            echo "\n</ul>\n<p><b>" . $aInt->lang('utilitiesresolvercheck', 'terminatingacctsdone') . "</b><br />" . $aInt->lang('utilitiesresolvercheck', 'terminatingacctsdonedesc') . "</p>";
        }
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->jquerycode = $jquerycode;
$aInt->jscode = $jscode;
$aInt->display();
function assignedips_trim(&$value)
{
    $value = trim($value);
}