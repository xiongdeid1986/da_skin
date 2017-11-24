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
/**
 * Smarty factory that wraps the Smarty library
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Smarty extends Smarty
{
    public $compiler_class = 'WHMCS_Smarty_Compiler';
    /**
     * Build a Smarty object.
     *
     * Perform routines common to all Smarty templates invoked by WHMCS.
     *
     * @param bool $admin
     */
    public function __construct($admin = false)
    {
        $whmcs = WHMCS_Application::getinstance();
        $whmcsAppConfig = $whmcs->getApplicationConfig();
        parent::__construct();
        $this->caching = 0;
        $this->template_dir = ROOTDIR . ($admin ? '/' . $whmcs->get_admin_folder_name() : '') . '/templates/';
        $this->compile_dir = $whmcsAppConfig['templates_compiledir'];
    }
    /**
     * Handle Smarty errors.
     *
     * Override `Smarty::trigger_error()` to suppress Smarty's error and write
     * the error to our logs.
     *
     * @param string $error_msg
     * @param int $error_type
     */
    public function trigger_error($error_msg, $error_type = E_USER_WARNING)
    {
        $msg = htmlentities($error_msg);
        if( function_exists('logActivity') )
        {
            $error_msg = trim(str_replace(array( "(Smarty_Compiler.class.php, line 319)", "(Smarty_Compiler.class.php, line 446)", "(Smarty_Compiler.class.php, line 590)" ), '', $error_msg));
            $msg = htmlentities($error_msg);
            logActivity("Smarty Error: " . $msg);
        }
        else
        {
            trigger_error("Smarty error: " . $msg, $error_type);
        }
    }
    /**
     * Deletes all Smarty template cache and compiled template files
     *
     * Then recreates the index.php file in the template compile folder to prevent directory listing
     */
    public function clearAllCaches()
    {
        $this->clear_all_cache();
        $this->clear_compiled_tpl();
        $src = "<?php\nheader(\"Location: ../index.php\");";
        $whmcs = WHMCS_Application::getinstance();
        try
        {
            $file = new WHMCS_File($whmcs->getTemplatesCacheDir() . "index.php");
            $file->create($src);
        }
        catch( Exception $e )
        {
        }
    }
}