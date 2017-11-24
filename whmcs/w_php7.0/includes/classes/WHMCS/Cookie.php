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
 * WHMCS Cookie Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Cookie
{
    public function __construct()
    {
    }
    public static function get($name, $treatAsArray = false)
    {
        $val = array_key_exists('WHMCS' . $name, $_COOKIE) ? $_COOKIE['WHMCS' . $name] : '';
        if( $treatAsArray )
        {
            $val = json_decode(base64_decode($val), true);
            $val = is_array($val) ? htmlspecialchars_array($val) : array(  );
        }
        return $val;
    }
    public static function set($name, $value, $expires = 0, $secure = false)
    {
        if( is_array($value) )
        {
            $value = base64_encode(json_encode($value));
        }
        if( !is_numeric($expires) )
        {
            if( substr($expires, 0 - 1) == 'm' )
            {
                $expires = time() + substr($expires, 0, 0 - 1) * 30 * 24 * 60 * 60;
            }
            else
            {
                $expires = 0;
            }
        }
        return setcookie('WHMCS' . $name, $value, $expires, '/', null, $secure, true);
    }
    public static function delete($name)
    {
        unset($_COOKIE['WHMCS' . $name]);
        return self::set($name, null, 0 - 86400);
    }
}