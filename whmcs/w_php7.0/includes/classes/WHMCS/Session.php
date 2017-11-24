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
 * WHMCS Sessions Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Session
{
    private $last_session_data = array(  );
    public function __construct()
    {
    }
    private function getSessionName($instanceid)
    {
        $instanceid = 'WHMCS' . $instanceid;
        return $instanceid;
    }
    public function create($instanceid)
    {
        session_name($this->getSessionName($instanceid));
        session_set_cookie_params(0, '/', null, false, true);
        if( session_start() )
        {
            return session_id();
        }
        return '';
    }
    public static function get($key)
    {
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : '';
    }
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
        return true;
    }
    public static function delete($key)
    {
        if( array_key_exists($key, $_SESSION) )
        {
            unset($_SESSION[$key]);
            return true;
        }
        return false;
    }
    /**
     * Retrieves and deletes a saved session value.
     *
     * @param string $key
     *
     * @return mixed
     */
    public static function getAndDelete($key)
    {
        $value = self::get($key);
        self::delete($key);
        return $value;
    }
    public static function rotate()
    {
        return session_regenerate_id();
    }
    public static function destroy()
    {
        session_unset();
        session_destroy();
    }
    public static function nullify()
    {
        $this->last_session_data = $_SESSION;
        $_SESSION = array(  );
    }
    public static function release()
    {
        session_write_close();
    }
}