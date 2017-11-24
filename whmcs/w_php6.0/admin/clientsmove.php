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
$aInt = new WHMCS_Admin("Edit Clients Products/Services");
$aInt->title = $aInt->lang('clients', 'transferownership');
ob_start();
if( $action == '' )
{
    echo "<script type=\"text/javascript\">\n\$(document).ready(function(){\n    \$(\"#clientsearchval\").keyup(function () {\n        var useridsearchlength = \$(\"#clientsearchval\").val().length;\n        if (useridsearchlength>2) {\n        \$.post(\"search.php\", { clientsearch: 1, value: \$(\"#clientsearchval\").val(), token: \"" . generate_token('plain') . "\" },\n            function(data){\n                if (data) {\n                    \$(\"#clientsearchresults\").html(data);\n                    \$(\"#clientsearchresults\").slideDown(\"slow\");\n                }\n            });\n        }\n    });\n});\nfunction searchselectclient(userid,name,email) {\n    \$(\"#newuserid\").val(userid);\n    \$(\"#clientsearchresults\").slideUp();\n}\n</script>\n";
    if( $error )
    {
        echo "<div class=\"errorbox\">" . $aInt->lang('clients', 'invalidowner') . "</div><br />";
    }
    echo "\n<form method=\"post\" action=\"";
    echo $whmcs->getPhpSelf();
    echo "?action=transfer&type=";
    echo $type;
    echo "&id=";
    echo $id;
    echo "\">\n";
    echo $aInt->lang('clients', 'transferchoose');
    echo "<br /><br />\n<div align=\"center\">\n";
    echo $aInt->lang('fields', 'clientid');
    echo ": <input type=\"text\" name=\"newuserid\" id=\"newuserid\" size=\"10\" /> <input type=\"submit\" value=\"";
    echo $aInt->lang('domains', 'transfer');
    echo "\" class=\"button\" /><br /><br />\n";
    echo $aInt->lang('global', 'clientsintellisearch');
    echo ": <input type=\"text\" id=\"clientsearchval\" size=\"25\" />\n</div>\n<br />\n<div id=\"clientsearchresults\">\n<div class=\"searchresultheader\">Search Results</div>\n<div class=\"searchresult\" align=\"center\">Matches will appear here as you type</div>\n</div>\n</form>\n\n";
}
else
{
    check_token("WHMCS.admin.default");
    $newuserid = trim($newuserid);
    $result = select_query('tblclients', 'id', array( 'id' => $newuserid ));
    $data = mysql_fetch_array($result);
    $newuserid = $data['id'];
    if( !$newuserid )
    {
        redir("type=" . $type . "&id=" . $id . "&error=1");
    }
    if( $type == 'hosting' )
    {
        $result = select_query('tblhosting', 'userid', array( 'id' => $id ));
        $data = mysql_fetch_array($result);
        $userid = $data['userid'];
        logActivity("Moved Service ID: " . $id . " from User ID: " . $userid . " to User ID: " . $newuserid, $newuserid);
        update_query('tblhosting', array( 'userid' => $newuserid ), array( 'id' => $id ));
        echo "<script language=\"javascript\">\nwindow.opener.location.href = \"clientshosting.php?userid=";
        echo $newuserid;
        echo "&id=";
        echo $id;
        echo "\";\nwindow.close();\n</script>\n";
    }
    else
    {
        if( $type == 'domain' )
        {
            $result = select_query('tbldomains', 'userid', array( 'id' => $id ));
            $data = mysql_fetch_array($result);
            $userid = $data['userid'];
            logActivity("Moved Domain ID: " . $id . " from User ID: " . $userid . " to User ID: " . $newuserid, $newuserid);
            update_query('tbldomains', array( 'userid' => $newuserid ), array( 'id' => $id ));
            echo "<script language=\"javascript\">\nwindow.opener.location.href = \"clientsdomains.php?userid=";
            echo $newuserid;
            echo "&id=";
            echo $id;
            echo "\";\nwindow.close();\n</script>\n";
        }
    }
}
$content = ob_get_contents();
ob_end_clean();
$aInt->content = $content;
$aInt->displayPopUp();