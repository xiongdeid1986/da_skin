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
$hooks = array(  );
$hooks = loadhookfiles();
$moduleshooks = explode(',', isset($CONFIG['ModuleHooks']) ? $CONFIG['ModuleHooks'] : '');
foreach( $moduleshooks as $moduleshook )
{
    $moduleshook = ROOTDIR . '/modules/servers/' . $moduleshook . "/hooks.php";
    if( file_exists($moduleshook) )
    {
        hook_log('', "Hook File Loaded: %s", $moduleshook);
        include($moduleshook);
    }
}
$moduleshooks = explode(',', isset($CONFIG['RegistrarModuleHooks']) ? $CONFIG['RegistrarModuleHooks'] : '');
foreach( $moduleshooks as $moduleshook )
{
    $moduleshook = ROOTDIR . '/modules/registrars/' . $moduleshook . "/hooks.php";
    if( file_exists($moduleshook) )
    {
        hook_log('', "Hook File Loaded: %s", $moduleshook);
        include($moduleshook);
    }
}
$addonmoduleshooks = explode(',', isset($CONFIG['AddonModulesHooks']) ? $CONFIG['AddonModulesHooks'] : '');
foreach( $addonmoduleshooks as $addonmoduleshook )
{
    $addonmoduleshook = ROOTDIR . '/modules/addons/' . $addonmoduleshook . "/hooks.php";
    if( file_exists($addonmoduleshook) )
    {
        hook_log('', "Hook File Loaded: %s", $addonmoduleshook);
        include($addonmoduleshook);
    }
}
function sort_array_by_priority($a, $b)
{
    return $a['priority'] < $b['priority'] ? 0 - 1 : 1;
}
/**
 * Handles verbose debug logging from hook calls
 *
 * @param string $hook_name
 * @param string $msg
 * @param string $input1 (Optional)
 * @param string $input2 (Optional)
 * @param string $input3 (Optional)
 */
function hook_log($hook_name, $msg, $input1 = '', $input2 = '', $input3 = '')
{
    if( $hook_name == 'LogActivity' )
    {
        return NULL;
    }
    $HooksDebugMode = WHMCS_Application::getinstance()->get_config('HooksDebugMode');
    if( defined('HOOKSLOGGING') || $HooksDebugMode )
    {
        $msg = "Hooks Debug: " . $msg;
        if( defined('INCRONRUN') )
        {
            $msg = "Cron Job: " . $msg;
        }
        logActivity(sprintf($msg, $input1, $input2, $input3));
    }
}
function run_hook($hook_name, $args)
{
    global $hooks;
    if( !is_array($hooks) )
    {
        hook_log($hook_name, "Hook File: the hooks list has been mutated to %s", ucfirst(gettype($hooks)));
        $hooks = array(  );
    }
    hook_log($hook_name, "Called Hook Point %s", $hook_name);
    if( !array_key_exists($hook_name, $hooks) )
    {
        hook_log($hook_name, "No Hook Functions Defined", $hook_name);
        return array(  );
    }
    unset($rollbacks);
    $rollbacks = array(  );
    reset($hooks[$hook_name]);
    $results = array(  );
    while( list($key, $hook) = each($hooks[$hook_name]) )
    {
        array_push($rollbacks, $hook['rollback_function']);
        if( function_exists($hook['hook_function']) )
        {
            hook_log($hook_name, "Hook Point %s - Calling Hook Function %s", $hook_name, $hook['hook_function']);
            $res = call_user_func($hook['hook_function'], $args);
            if( $res )
            {
                $results[] = $res;
                hook_log($hook_name, "Hook Completed - Returned True");
            }
            else
            {
                hook_log($hook_name, "Hook Completed - Returned False");
            }
        }
        else
        {
            hook_log($hook_name, "Hook Function %s Not Found", $hook['hook_function']);
        }
    }
    return $results;
}
function add_hook($hook_name, $priority, $hook_function, $rollback_function = '')
{
    global $hooks;
    if( !is_array($hooks) )
    {
        hook_log($hook_name, "Hook File: the hooks list has been mutated to %s", ucfirst(gettype($hooks)));
        $hooks = array(  );
    }
    if( !array_key_exists($hook_name, $hooks) )
    {
        $hooks[$hook_name] = array(  );
    }
    array_push($hooks[$hook_name], array( 'priority' => $priority, 'hook_function' => $hook_function, 'rollback_function' => $rollback_function ));
    hook_log($hook_name, "Hook Defined for Point: %s - Priority: %s - Function Name: %s", $hook_name, $priority, $hook_function);
    uasort($hooks[$hook_name], 'sort_array_by_priority');
}
function remove_hook($hook_name, $priority, $hook_function, $rollback_function)
{
    global $hooks;
    if( !is_array($hooks) )
    {
        hook_log($hook_name, "Hook File: the hooks list has been mutated to %s", ucfirst(gettype($hooks)));
        $hooks = array(  );
    }
    if( array_key_exists($hook_name, $hooks) )
    {
        reset($hooks[$hook_name]);
        while( list($key, $hook) = each($hooks[$hook_name]) )
        {
            if( 0 <= $priority && $priority == $hook['priority'] || $hook_function && $hook_function == $hook['hook_function'] || $rollback_function && $rollback_function == $hook['rollback_function'] )
            {
                unset($hooks[$hook_name][$key]);
            }
        }
    }
}
function clear_hooks($hook_name)
{
    global $hooks;
    if( !is_array($hooks) )
    {
        hook_log($hook_name, "Hook File: the hooks list has been mutated to %s", ucfirst(gettype($hooks)));
        $hooks = array(  );
    }
    if( array_key_exists($hook_name, $hooks) )
    {
        unset($hooks[$hook_name]);
    }
}
function run_validate_hook(&$validate, $hook_name, $args)
{
    $hookerrors = run_hook($hook_name, $args);
    $errormessage = '';
    if( is_array($hookerrors) && count($hookerrors) )
    {
        foreach( $hookerrors as $hookerrors2 )
        {
            if( is_array($hookerrors2) )
            {
                $validate->addErrors($hookerrors2);
            }
            else
            {
                $validate->addError($hookerrors2);
            }
        }
    }
}
/**
 * Process the results from a pre or after hook action.
 *
 * Check for errors in the results from a hook's pre or after action. Return
 * whether or not to abort the hook with success message. Throw an exception if
 * the pre or post action errored out.
 *
 * @throws WHMCS_Exception if the pre or post hook function returned an error.
 * @param string $moduleName The name of the module running these hooks.
 * @param string $function The name of the function being hooked in the module.
 * @param array $hookResults The result array from all hooks run.
 * @return bool
 */
function processHookResults($moduleName, $function, $hookResults = array(  ))
{
    if( !empty($hookResults) )
    {
        $hookErrors = array(  );
        $abortWithSuccess = false;
        foreach( $hookResults as $hookResult )
        {
            if( !empty($hookResult['abortWithError']) )
            {
                $hookErrors[] = $hookResult['abortWithError'];
            }
            if( array_key_exists('abortWithSuccess', $hookResult) && $hookResult['abortWithSuccess'] === true )
            {
                $abortWithSuccess = true;
            }
        }
        if( count($hookErrors) )
        {
            throw new WHMCS_Exception(implode(" ", $hookErrors));
        }
        if( $abortWithSuccess )
        {
            logActivity("Function " . $moduleName . '-' . ">" . $function . "() Aborted by Action Hook Code");
            return true;
        }
    }
    return false;
}
/**
 * Retrieve the hooks list by reading the hook files
 *
 * NOTE: this will destroy any previous value assigned to $hooks in global
 * scope, an thus it should only be called once.  Please use getHooks() in
 * your code as it knows how to safely and efficiently give you the hook list
 *
 *
 * @TODO: use exceptions at the next maturation of the hooks system
 *
 * @return array
 */
function loadHookFiles()
{
    global $hooks;
    $hooks = array(  );
    $hooksdir = ROOTDIR . '/includes/hooks/';
    $dh = opendir($hooksdir);
    while( false !== ($hookfile = readdir($dh)) )
    {
        if( is_file($hooksdir . $hookfile) )
        {
            $extension = pathinfo($hookfile, PATHINFO_EXTENSION);
            if( $extension == 'php' )
            {
                hook_log('', "Hook File Loaded: %s", $hooksdir . $hookfile);
                include($hooksdir . $hookfile);
                if( !is_array($hooks) )
                {
                    hook_log($hook_name, "Hook File: %s mutated the hooks list from Array to %s", $hooksdir . $hookfile, ucfirst(gettype($hooks)));
                    $hooks = array(  );
                }
            }
        }
    }
    closedir($dh);
    return $hooks;
}