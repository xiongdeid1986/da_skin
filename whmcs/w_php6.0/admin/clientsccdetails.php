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
$aInt = new WHMCS_Admin("View Credit Card Details");
$aInt->title = $aInt->lang('clients', 'ccdetails');
$aInt->requiredFiles(array( 'ccfunctions', 'clientfunctions' ));
ob_start();
$ccstoredisabled = $whmcs->get_config('CCNeverStore');
if( $ccstoredisabled )
{
    echo "<p>" . $aInt->lang('clients', 'ccstoredisabled') . "</p><p align=\"center\"><input type=\"button\" value=\"" . $aInt->lang('addons', 'closewindow') . "\" class=\"button\" onclick=\"window.close()\" /></p>";
}
else
{
    $validhash = '';
    if( $action == 'clear' )
    {
        check_token("WHMCS.admin.default");
        checkPermission("Update/Delete Stored Credit Card");
        updateCCDetails($userid, '', '', '', '', '', '', '', true);
    }
    else
    {
        if( $_POST['action'] == 'save' )
        {
            check_token("WHMCS.admin.default");
            checkPermission("Update/Delete Stored Credit Card");
            $errormessage = updateCCDetails($userid, $cctype, $ccnumber, $cardcvv, $ccexpirymonth . $ccexpiryyear, $ccstartmonth . $ccstartyear, $ccissuenum);
            if( !$errormessage )
            {
                $errormessage = "<B>" . $aInt->lang('global', 'success') . "</B> - " . $aInt->lang('clients', 'ccdetailschanged');
            }
        }
    }
    if( $fullcc )
    {
        check_token("WHMCS.admin.default");
        checkPermission("Decrypt Full Credit Card Number");
        $referrer = $_SERVER['HTTP_REFERER'];
        $pos = strpos($referrer, "?");
        if( $pos )
        {
            $referrer = substr($referrer, 0, $pos);
        }
        $adminfolder = $whmcs->get_admin_folder_name();
        if( $CONFIG['SystemURL'] . '/' . $adminfolder . "/clientsccdetails.php" != $referrer && $CONFIG['SystemSSLURL'] . '/' . $adminfolder . "/clientsccdetails.php" != $referrer )
        {
            echo "<p>" . $aInt->lang('global', 'invalidaccessattempt') . "</p>";
            exit();
        }
        if( $cchash != $cc_encryption_hash )
        {
            $errormessage = "<B>" . $aInt->lang('global', 'error') . "</B> - " . $aInt->lang('clients', 'incorrecthash');
        }
        else
        {
            $validhash = 'true';
            logActivity("Viewed Decrypted Credit Card Number for User ID " . $userid);
        }
    }
    if( $errormessage )
    {
        echo "<p align=\"center\" style=\"color:#cc0000;\">" . str_replace("<li>", " - ", $errormessage) . "</p>";
    }
    $data = getCCDetails($userid);
    $cardtype = $data['cardtype'];
    $cardnum = $validhash ? $data['fullcardnum'] : $data['cardnum'];
    $cardexp = $data['expdate'];
    $cardissuenum = $data['issuenumber'];
    $cardstart = $data['startdate'];
    $gatewayid = $data['gatewayid'];
    echo "<table>\n<tr><td colspan=\"2\"><b>";
    echo $aInt->lang('clients', 'existingccdetails');
    echo "</b></td></tr>\n<tr><td>";
    echo $aInt->lang('fields', 'cardtype');
    echo ":</td><td>";
    echo $cardtype;
    echo "</td></tr>\n<tr><td>";
    echo $aInt->lang('fields', 'cardnum');
    echo ":</td><td>";
    echo $cardnum;
    if( $gatewayid )
    {
        echo " *";
    }
    echo "</td></tr>\n<tr><td>";
    echo $aInt->lang('fields', 'expdate');
    echo ":</td><td>";
    echo $cardexp;
    echo "</td></tr>\n";
    if( $cardissuenum )
    {
        echo "<tr><td>";
        echo $aInt->lang('fields', 'issueno');
        echo ":</td><td>";
        echo $cardissuenum;
        echo "</td></tr>";
    }
    if( $cardstart )
    {
        echo "<tr><td>";
        echo $aInt->lang('fields', 'startdate');
        echo ":</td><td>";
        echo $cardstart;
        echo "</td></tr>";
    }
    echo "<tr><td colspan=\"2\"><br><b>";
    echo $aInt->lang('clients', 'viewfullcardno');
    echo "</b></td></tr>\n<tr><td colspan=\"2\">\n";
    if( $data['fullcardnum'] )
    {
        echo $aInt->lang('clients', 'entercchash');
        echo "<br><br><div align=\"center\"><form method=\"post\" action=\"";
        echo $whmcs->getPhpSelf();
        echo "\">";
        generate_token();
        echo "<input type=\"hidden\" name=\"userid\" value=\"";
        echo $userid;
        echo "\"><input type=\"hidden\" name=\"fullcc\" value=\"true\"><textarea name=\"cchash\" cols=\"40\" rows=\"3\"></textarea><br><input type=\"submit\" value=\"";
        echo $aInt->lang('global', 'submit');
        echo "\" class=\"button\" /></div></form>\n";
    }
    else
    {
        if( $gatewayid )
        {
            echo "<strong>" . $aInt->lang('fields', 'gatewayid') . "</strong><br />\"" . $gatewayid . "\"<br /><br />" . $aInt->lang('clients', 'ccstoredremotely');
        }
    }
    echo "</td></tr>\n<tr><td colspan=\"2\"><br><b>";
    echo $aInt->lang('clients', 'enternewcc');
    echo "</b></td></tr>\n<tr><td><form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "\">\n<input type=\"hidden\" name=\"action\" value=\"save\">\n<input type=\"hidden\" name=\"userid\" value=\"";
    echo $userid;
    echo "\">\n";
    generate_token();
    echo $aInt->lang('fields', 'cardtype');
    echo ":</td><td><select name=\"cctype\" id=\"cctype\">\n";
    $acceptedcctypes = $CONFIG['AcceptedCardTypes'];
    $acceptedcctypes = explode(',', $acceptedcctypes);
    foreach( $acceptedcctypes as $cctype )
    {
        echo "<option>" . $cctype . "</option>";
    }
    echo "</select></td></tr>\n<tr><td nowrap>";
    echo $aInt->lang('fields', 'cardnum');
    echo ":</td><td><input type=\"text\" name=\"ccnumber\" size=\"25\" autocomplete=\"off\"></td></tr>\n<tr><td>";
    echo $aInt->lang('fields', 'expdate');
    echo ":</td><td><input type=\"text\" name=\"ccexpirymonth\" size=\"2\" maxlength=\"2\">/<input type=\"text\" name=\"ccexpiryyear\" size=\"2\" maxlength=\"2\"> (";
    echo $aInt->lang('fields', 'mmyy');
    echo ")</td></tr>\n";
    if( $CONFIG['ShowCCIssueStart'] )
    {
        echo "<tr><td>";
        echo $aInt->lang('fields', 'issueno');
        echo ":</td><td><input type=\"text\" name=\"ccissuenum\" size=\"5\" maxlength=\"4\"></td></tr>\n<tr><td>";
        echo $aInt->lang('fields', 'startdate');
        echo ":</td><td><input type=\"text\" name=\"ccstartmonth\" size=\"2\" maxlength=\"2\">/<input type=\"text\" name=\"ccstartyear\" size=\"2\" maxlength=\"2\"> (";
        echo $aInt->lang('fields', 'mmyy');
        echo ")</td></tr>\n";
    }
    echo "<tr><td nowrap>";
    echo $aInt->lang('fields', 'cardcvv');
    echo ":</td><td><input type=\"text\" name=\"cardcvv\" id=\"cardcvv\" size=\"5\" autocomplete=\"off\"></td></tr>\n</table>\n<script language=\"JavaScript\">\nfunction confirmClear() {\nif (confirm(\"";
    echo $aInt->lang('clients', 'ccdeletesure');
    echo "\")) {\nwindow.location='";
    echo $whmcs->getPhpSelf();
    echo "?userid=";
    echo $userid;
    echo "&action=clear";
    echo generate_token('link');
    echo "';\n}}\n</script>\n<p align=center><input type=\"submit\" value=\"";
    echo $aInt->lang('global', 'savechanges');
    echo "\" class=\"button\" /> <input type=\"button\" value=\"";
    echo $aInt->lang('addons', 'closewindow');
    echo "\" class=\"button\" onclick=\"window.close()\" /><br /><input type=\"button\" value=\"";
    echo $aInt->lang('clients', 'cleardetails');
    echo "\" class=\"button\" onClick=\"confirmClear();return false;\" style=\"color:#cc0000;\" /></p>\n</form>\n<script type=\"text/javascript\" src=\"../includes/jscript/creditcard.js\"></script>\n\n";
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->displayPopUp();