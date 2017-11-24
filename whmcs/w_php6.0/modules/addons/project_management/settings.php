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
$PMRoleID = get_query_val('tbladmins', 'roleid', array( 'id' => $_SESSION['adminid'] ));
if( !$vars['masteradmin' . $PMRoleID] )
{
    echo $headeroutput . "\n<h2>Access Denied</h2>\n<p>You must be granted Master Admin User status in the Project Management Addon Configuration area within <strong><a href=\"configaddonmods.php#project_management\">Setup > Addon Modules</a></strong> before you are allowed to access this page.</p>";
    return false;
}
if( $_POST['save'] )
{
    check_token("WHMCS.admin.default");
    delete_query('tbladdonmodules', array( 'module' => 'project_management', 'setting' => 'hourlyrate' ));
    insert_query('tbladdonmodules', array( 'module' => 'project_management', 'setting' => 'hourlyrate', 'value' => format_as_currency($_POST['hourlyrate']) ));
    delete_query('tbladdonmodules', array( 'module' => 'project_management', 'setting' => 'statusvalues' ));
    insert_query('tbladdonmodules', array( 'module' => 'project_management', 'setting' => 'statusvalues', 'value' => $_POST['statusvalues'] ));
    delete_query('tbladdonmodules', array( 'module' => 'project_management', 'setting' => 'completedstatuses' ));
    insert_query('tbladdonmodules', array( 'module' => 'project_management', 'setting' => 'completedstatuses', 'value' => implode(',', $_POST['completestatus']) ));
    delete_query('tbladdonmodules', array( 'module' => 'project_management', 'setting' => 'perms' ));
    insert_query('tbladdonmodules', array( 'module' => 'project_management', 'setting' => 'perms', 'value' => serialize($_POST['perms']) ));
    delete_query('tbladdonmodules', array( 'module' => 'project_management', 'setting' => 'clientenable' ));
    insert_query('tbladdonmodules', array( 'module' => 'project_management', 'setting' => 'clientenable', 'value' => $_POST['clientenable'] ));
    delete_query('tbladdonmodules', array( 'module' => 'project_management', 'setting' => 'clientfeatures' ));
    insert_query('tbladdonmodules', array( 'module' => 'project_management', 'setting' => 'clientfeatures', 'value' => implode(',', $_POST['clfeat']) ));
    redir("module=project_management&m=settings");
}
$adminroles = array(  );
$result = select_query('tbladminroles', '', '', 'name', 'ASC');
while( $data = mysql_fetch_array($result) )
{
    $adminroles[$data['id']] = $data['name'];
}
$permissions = project_management_permslist();
echo $headeroutput . "\n\n<form method=\"post\" action=\"" . $modulelink . "\">\n<input type=\"hidden\" name=\"save\" value=\"1\" />";
echo "\n<h2>Settings</h2>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"200\" class=\"fieldlabel\">Default Hourly Rate</td><td class=\"fieldarea\"><input type=\"text\" name=\"hourlyrate\" size=\"15\" value=\"";
echo $vars['hourlyrate'];
echo "\" /><br />Enter the standard hourly rate you charge for use in time based billing (can be overriden at the time of invoice generation)</td></tr>\n<tr><td class=\"fieldlabel\">Project Statuses</td><td class=\"fieldarea\"><input type=\"text\" name=\"statusvalues\" size=\"90\" value=\"";
echo $vars['statusvalues'];
echo "\" /><br />Enter a comma separated list of the statuses you want to setup for projects</td></tr>\n<tr><td width=\"200\" class=\"fieldlabel\">Completed Statuses</td><td class=\"fieldarea\">";
$statuses = explode(',', $vars['statusvalues']);
$completestatuses = explode(',', $vars['completedstatuses']);
foreach( $statuses as $status )
{
    echo "<label><input type=\"checkbox\" name=\"completestatus[]\" value=\"" . $status . "\"" . (in_array($status, $completestatuses) ? " checked" : '') . " /> " . current(explode("|", $status)) . "</label> ";
}
echo "<br />Choose the statuses above that should be treated as closed/completed</td></tr>\n</table>\n\n<h2>Client Area</h2>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr><td width=\"200\" class=\"fieldlabel\">Enable/Disable</td><td class=\"fieldarea\"><label><input type=\"checkbox\" name=\"clientenable\" value=\"1\"";
if( $vars['clientenable'] )
{
    echo " checked";
}
echo " /> Tick to enable Client Area Project Access</label></td></tr>\n<tr><td class=\"fieldlabel\">Allow Access To</td><td class=\"fieldarea\">\n<label><input type=\"checkbox\" name=\"clfeat[]\" value=\"tasks\"";
$clfeat = explode(',', $vars['clientfeatures']);
if( in_array('tasks', $clfeat) )
{
    echo " checked";
}
echo " /> View Project Tasks</label> <label><input type=\"checkbox\" name=\"clfeat[]\" value=\"time\"";
if( in_array('time', $clfeat) )
{
    echo " checked";
}
echo " /> View Task Time Logs</label> <label><input type=\"checkbox\" name=\"clfeat[]\" value=\"addtasks\"";
if( in_array('addtasks', $clfeat) )
{
    echo " checked";
}
echo " /> Add New Tasks</label> <label><input type=\"checkbox\" name=\"clfeat[]\" value=\"staff\"";
if( in_array('staff', $clfeat) )
{
    echo " checked";
}
echo " /> View Assigned Staff Member</label> <label><input type=\"checkbox\" name=\"clfeat[]\" value=\"files\"";
if( in_array('files', $clfeat) )
{
    echo " checked";
}
echo " /> View/Upload Files</label>\n</td></tr>\n</table>\n\n<h2>Permissions</h2>\n\n<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n<tr style=\"text-align:center;\"><td></td>";
foreach( $adminroles as $aid => $aname )
{
    echo "<td>" . $aname . "</td>";
}
echo "</tr>\n";
foreach( $permissions as $permid => $permname )
{
    echo "<tr><td width=\"200\" class=\"fieldlabel\">" . $permname . "</td>";
    foreach( $adminroles as $aid => $aname )
    {
        echo "<td class=\"fieldarea\" style=\"text-align:center;\"><input type=\"checkbox\" name=\"perms[" . $permid . "][" . $aid . "]\" value=\"1\"";
        if( $perms[$permid][$aid] )
        {
            echo " checked";
        }
        echo " /></td>";
    }
    echo "</tr>";
}
echo "</table>\n\n<p align=\"center\"><input type=\"submit\" value=\"Save Changes\" /></p>\n\n</form>\n";