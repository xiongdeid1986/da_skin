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
 * Customizations to the Smarty compiler.
 *
 * Since Smarty compiles its code and templates, any Smarty functionality that
 * we want to override in `WHMCS_Smarty` must also exist in
 * `WHMCS_Smarty_Compiler`.
 *
 * Functions overriden by our Smarty class should take the form:
 *
 * ```php
 * public function function_to_override(_method signature_)
 * {
 *     $this->proxyToOverride(__FUNCTION__, func_get_args());
 * }
 * ```
 *
 * See discussion on pull request #509 for more information.
 *
 * @see https://git.whmcs.com/WHMCS/Application/pull/509
 */
class WHMCS_Smarty_Compiler extends Smarty_Compiler
{
    /**
     * Override Smarty::trigger_error()
     *
     * @param string $error_msg
     * @param int $error_type
     */
    public function trigger_error($error_msg, $error_type = E_USER_WARNING)
    {
        $_args = func_get_args();
        $this->proxyToOverride('trigger_error', $_args);
    }
    /**
     * Dispatch functions to the WHMCS_Smarty class.
     *
     * @param string $func
     * @param array $args
     * @return mixed
     */
    protected function proxyToOverride($func, $args)
    {
        $smarty = new WHMCS_Smarty();
        return call_user_func_array(array( $smarty, $func ), $args);
    }
}