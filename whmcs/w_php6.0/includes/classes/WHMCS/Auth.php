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
 * Admin Authentication Authentication Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Auth
{
    private $inputusername = '';
    private $admindata = array(  );
    private $logincookie = '';
    private $hasPasswordHashField = true;
    /**
     * @type bool
     */
    public function __construct()
    {
    }
    /**
     * Get an admin for authentication purposes
     *
     * @param array $where constraints on the row lookup
     * @param resource $resource database resource
     * @param bool $restrictToEnabled weather to ignore any where clause and
     * force the constraint of only looked amongst the active admin rows
     *
     * @return bool
     */
    private function getInfo($where, $resource = null, $restrictToEnabled = true)
    {
        if( $restrictToEnabled )
        {
            $where['disabled'] = '0';
        }
        $passwordHashField = 'passwordhash,';
        $installedVersion = WHMCS_Application::getinstance()->getDBVersion();
        $lasVersionWithoutHashField = new WHMCS_Version_SemanticVersion("5.3.8-release.1");
        $schemaIsSane = WHMCS_Version_SemanticVersion::compare($installedVersion, $lasVersionWithoutHashField, ">");
        if( !$schemaIsSane )
        {
            $this->hasPasswordHashField = false;
            $passwordHashField = '';
        }
        $result = select_query('tbladmins', 'id,username,password,email,' . $passwordHashField . 'template,language,authmodule,loginattempts,disabled', $where, '', '', '', '', $resource);
        $data = mysql_fetch_assoc($result);
        $this->admindata = $data;
        return $data['id'] ? true : false;
    }
    /**
     * Get admin by an id
     *
     * @param int $adminid
     * @param resource $resource database resource
     * @param bool $restrictToEnabled weather to ignore any where clause and
     * force the constraint of only looked amongst the active admin rows
     *
     * @return bool
     */
    public function getInfobyID($adminid, $resource = null, $restrictToEnabled = true)
    {
        if( !is_numeric($adminid) )
        {
            return false;
        }
        return $this->getInfo(array( 'id' => (int) $adminid ), $resource, $restrictToEnabled);
    }
    /**
     * Get admin by username
     *
     * @param string $username
     * @param bool $restrictToEnabled weather to ignore any where clause and
     * force the constraint of only looked amongst the active admin rows
     *
     * @return bool
     */
    public function getInfobyUsername($username, $restrictToEnabled = true)
    {
        $this->inputusername = $username;
        return $this->getInfo(array( 'username' => $username ), null, $restrictToEnabled);
    }
    /**
     * Compare input password with stored value
     *
     * Validates password using newer admin password hash format if set,
     * only allowing fallback to legacy admin pw hash if not.
     *
     * @param string $password
     *
     * @return boolean
     */
    public function comparePassword($password)
    {
        $result = false;
        $password = trim($password);
        if( $password )
        {
            $hasher = new WHMCS_Security_Hash_Password();
            if( $this->isAdminPWHashSet() )
            {
                $storedSecret = $this->getAdminPWHash();
            }
            else
            {
                $storedSecret = $this->getLegacyAdminPW();
                $storedSecretInfo = $hasher->getInfo($storedSecret);
                if( $storedSecretInfo['algoName'] != WHMCS_Security_Hash_Password::HASH_MD5 && $storedSecretInfo['algoName'] != WHMCS_Security_Hash_Password::HASH_UNKNOWN )
                {
                    $password = md5($password);
                }
            }
            try
            {
                $result = $hasher->verify($password, $storedSecret);
            }
            catch( Exception $e )
            {
                logActivity("Failed to verify admin password hash: " . $e->getMessage());
            }
        }
        return $result;
    }
    /**
     * Compare input password with stored value
     *
     * Use this method when performing API authentication.  This _must_ be done
     * for backwards compatibility so that the product can update the stored
     * value in a way that is cryptographically stronger, but allows API/mobile
     * implementations to function without modification.
     *
     * @param string $password
     *
     * @return bool
     */
    public function compareApiPassword($password)
    {
        $result = false;
        $password = trim($password);
        $storedHash = $this->getLegacyAdminPW();
        if( $password && $storedHash )
        {
            $hasher = new WHMCS_Security_Hash_Password();
            try
            {
                $info = $hasher->getInfo($storedHash);
                if( $info['algoName'] == WHMCS_Security_Hash_Password::HASH_MD5 )
                {
                    $result = $hasher->assertBinarySameness($password, $this->getLegacyAdminPW());
                }
                else
                {
                    if( $info['algoName'] != WHMCS_Security_Hash_Password::HASH_UNKNOWN )
                    {
                        $result = $hasher->verify($password, $storedHash);
                    }
                }
            }
            catch( Exception $e )
            {
                logActivity("Failed to verify API password hash: " . $e->getMessage());
            }
        }
        return $result;
    }
    public function isTwoFactor()
    {
        return $this->admindata['authmodule'] ? true : false;
    }
    public function getAdminID()
    {
        return $this->admindata['id'];
    }
    public function getAdminUsername()
    {
        return $this->admindata['username'];
    }
    public function getAdminEmail()
    {
        return $this->admindata['email'];
    }
    /**
     * Get the `password` column data or blank string
     *
     * NOTE: this column, prior to 5.3.9-release.1, represented the hashed
     * secret for login page and API authentication. In 5.3.9, this field is
     * auto-migrated to contain a hash secret _only_ for API authentication
     *
     * @return string
     */
    public function getLegacyAdminPW()
    {
        return !empty($this->admindata['password']) ? $this->admindata['password'] : '';
    }
    /**
     * Get the `passwordhash` column data or blank string
     *
     * NOTE: this column, prior to 5.3.9-release.1, did not exist. In 5.3.9,
     * this field is used to store the hash secret for authentication through
     * login pages.
     *
     * @see getLegacyAdminPW() for hash secret for API authentication
     *
     * @return string
     */
    public function getAdminPWHash()
    {
        return !empty($this->admindata['passwordhash']) ? $this->admindata['passwordhash'] : '';
    }
    /**
     * Is the `passwordhash` column defined and populated with a non-empty value
     *
     * @return bool
     */
    public function isAdminPWHashSet()
    {
        $passwordHash = $this->getAdminPWHash();
        return empty($passwordHash) ? false : true;
    }
    /**
     * Cryptographically hash raw secret for use by application admin login pages
     *
     * This will update the 'passwordhash' column introduced it tbladmins in
     * 5.3.9-release.1
     *
     * @param string $password Raw secret to hash
     *
     * @return bool
     */
    public function generateNewPasswordHashAndStore($password)
    {
        $hasher = new WHMCS_Security_Hash_Password();
        $result = false;
        if( $this->hasPasswordHashField )
        {
            try
            {
                $hashedSecret = $hasher->hash($password);
                $result = update_query('tbladmins', array( 'passwordhash' => $hashedSecret ), array( 'id' => $this->getAdminID() ));
                if( $result !== false )
                {
                    $this->admindata['passwordhash'] = $hashedSecret;
                }
            }
            catch( Exception $e )
            {
                logActivity("Failed to rehash admin password: " . $e->getMessage());
            }
        }
        return $result;
    }
    /**
     * Cryptographically hash raw secret for use by API authentication
     *
     * This will update the 'password' column. That column, prior to
     * 5.3.9-release.1 was used for both API and normal app hashed secret
     * storage. In 5.3.9-release.1, the passwordhash column was introduced to
     * provide segregation and backwards compatibility with production API
     * clients (ie, customers' portals and backend systems) and native mobile
     * applications
     *
     * NOTE: As of 5.3.9-release.1, the 'raw' value passed in the $password
     * argument should be equal to the actual secret used in a login, but hashed
     * with MD5, eg
     * ```
     * $p1 = $userProvidePasswordFromLoginForm;
     * // store new hash of secret
     * $this->generateNewPasswordHashAndStore($p1)
     * // store secret so API can authenticate
     * $this->generateNewPasswordHashAndStoreForAPI(md5($p1));
     * // or rehash pre-5.3.9-release.1 stored digest
     * $old_p = $passwordColumnValueBefore5.3.9 // a plain, no key/salt MD5 digest
     * $this->generateNewPasswordHashAndStoreForAPI($old_p);
     * ```
     *
     * @param string $password 'Raw' secret to hash
     *
     * @return bool
     */
    public function generateNewPasswordHashAndStoreForApi($password)
    {
        $hasher = new WHMCS_Security_Hash_Password();
        $result = false;
        if( $this->hasPasswordHashField )
        {
            try
            {
                $hashedSecret = $hasher->hash($password);
                $result = update_query('tbladmins', array( 'password' => $hashedSecret ), array( 'id' => $this->getAdminID() ));
                if( $result !== false )
                {
                    $this->admindata['password'] = $hashedSecret;
                }
            }
            catch( Exception $e )
            {
                logActivity("Failed to rehash admin password: " . $e->getMessage());
            }
        }
        return $result;
    }
    public function getAdminTemplate()
    {
        return $this->admindata['template'];
    }
    public function getAdminLanguage()
    {
        return $this->admindata['language'];
    }
    public function getAdmin2FAModule()
    {
        return $this->admindata['authmodule'];
    }
    private function getAdminUserAgent()
    {
        return array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }
    /**
     * Returns if an admin user is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->admindata['disabled'] != 1;
    }
    public function generateAdminSessionHash($whmcsclass = false)
    {
        global $whmcs;
        if( $whmcsclass )
        {
            $haship = $whmcsclass->get_config('DisableSessionIPCheck') ? '' : WHMCS_Utility_Environment_CurrentUser::getip();
            $cchash = $whmcsclass->get_hash();
        }
        else
        {
            $haship = $whmcs->get_config('DisableSessionIPCheck') ? '' : WHMCS_Utility_Environment_CurrentUser::getip();
            $cchash = $whmcs->get_hash();
        }
        $hash = sha1($this->getAdminID() . $this->getAdminUserAgent() . $this->getAdminPWHash() . $haship . substr(sha1($cchash), 20));
        return $hash;
    }
    public function setSessionVars($whmcsclass = false)
    {
        $_SESSION['adminid'] = $this->getAdminID();
        $_SESSION['adminpw'] = $this->generateAdminSessionHash($whmcsclass);
        conditionally_set_token(genRandomVal());
    }
    /**
     * Performs required actions post login
     *
     * To be called upon a successful login. Creates a log entry,
     * resets admin login attempts and runs AdminLogin hook.
     *
     * @param bool $createAdminLogEntry
     */
    public function processLogin($createAdminLogEntry = true)
    {
        $whmcs = WHMCS_Application::getinstance();
        if( $createAdminLogEntry )
        {
            update_query('tbladminlog', array( 'logouttime' => "now()" ), array( 'adminusername' => $this->getAdminUsername(), 'logouttime' => '00000000000000' ));
            insert_query('tbladminlog', array( 'adminusername' => $this->getAdminUsername(), 'logintime' => "now()", 'lastvisit' => "now()", 'ipaddress' => WHMCS_Utility_Environment_CurrentUser::getip(), 'sessionid' => session_id() ));
        }
        update_query('tbladmins', array( 'loginattempts' => '0' ), array( 'username' => $this->getAdminUsername() ));
        $resetTokenId = get_query_val('tbltransientdata', 'id', array( 'data' => json_encode(array( 'id' => $this->getAdminID(), 'email' => $this->getAdminEmail() )) ));
        if( $resetTokenId )
        {
            delete_query('tbltransientdata', array( 'id' => $resetTokenId ));
        }
        run_hook('AdminLogin', array( 'adminid' => $this->getAdminID(), 'username' => $this->getAdminUsername() ));
    }
    public function getRememberMeCookie()
    {
        $remcookie = WHMCS_Cookie::get('AU');
        if( !$remcookie )
        {
            $remcookie = WHMCS_Cookie::get('AUser');
        }
        return $remcookie;
    }
    public function isValidRememberMeCookie($whmcsclass = false)
    {
        global $whmcs;
        $cookiedata = $this->getRememberMeCookie();
        if( $cookiedata )
        {
            $cookiedata = explode(":", $cookiedata);
            $resource = $whmcsclass !== false ? $whmcsclass->getDatabaseObj()->retrieveDatabaseConnection() : $whmcs->getDatabaseObj()->retrieveDatabaseConnection();
            if( $this->getInfobyID($cookiedata[0], $resource) )
            {
                if( $whmcsclass )
                {
                    $hash = $whmcsclass->get_hash();
                }
                else
                {
                    $hash = $whmcs->get_hash();
                }
                $cookiehashcompare = sha1($this->generateAdminSessionHash($whmcsclass) . $hash);
                if( $cookiedata[1] == $cookiehashcompare && $this->isAdminPWHashSet() )
                {
                    return true;
                }
            }
        }
        return false;
    }
    public function setRememberMeCookie()
    {
        global $whmcs;
        WHMCS_Cookie::set('AU', $this->getAdminID() . ":" . sha1($_SESSION['adminpw'] . $whmcs->get_hash()), '12m');
    }
    public function unsetRememberMeCookie()
    {
        WHMCS_Cookie::delete('AU');
    }
    private function getWhiteListedIPs()
    {
        global $whmcs;
        $ips = array(  );
        $whitelistedips = unserialize($whmcs->get_config('WhitelistedIPs'));
        foreach( $whitelistedips as $whitelisted )
        {
            $ips[] = $whitelisted['ip'];
        }
        return $ips;
    }
    private function isWhitelistedIP($ip)
    {
        $whitelistedips = $this->getWhiteListedIPs();
        if( in_array($ip, $whitelistedips) )
        {
            return true;
        }
        $ipparts = explode(".", $ip);
        if( 3 <= count($ipparts) )
        {
            $ip = $ipparts[0] . "." . $ipparts[1] . "." . $ipparts[2] . ".*";
            if( in_array($ip, $whitelistedips) )
            {
                return true;
            }
        }
        if( 2 <= count($ipparts) )
        {
            $ip = $ipparts[0] . "." . $ipparts[1] . ".*.*";
            if( in_array($ip, $whitelistedips) )
            {
                return true;
            }
        }
        return false;
    }
    private function isBanEnabled()
    {
        global $whmcs;
        return 0 < $whmcs->get_config('InvalidLoginBanLength') ? true : false;
    }
    private function getLoginBanDate()
    {
        global $whmcs;
        return date("Y-m-d H:i:s", mktime(date('H'), date('i') + $whmcs->get_config('InvalidLoginBanLength'), date('s'), date('m'), date('d'), date('Y')));
    }
    /**
     * Get admin setting for whether to send a failed login notice on a whitelisted IP.
     *
     * To be called upon after an unsuccessful login.  Checks whether the admin has set
     * a notice to be sent when a failed attempt comes from a whitelisted IP address.
     *
     * @return bool
     */
    protected function sendWhitelistedIPNotice()
    {
        return (string) WHMCS_Application::getinstance()->get_config('sendFailedLoginWhitelist');
    }
    public function failedLogin()
    {
        global $whmcs;
        if( !$this->isBanEnabled() )
        {
            return false;
        }
        $remote_ip = WHMCS_Utility_Environment_CurrentUser::getip();
        if( $this->isWhitelistedIP($remote_ip) )
        {
            if( $this->sendWhitelistedIPNotice() )
            {
                if( isset($this->admindata['username']) )
                {
                    $username = $this->admindata['username'];
                    sendAdminNotification('system', "WHMCS Admin Failed Login Attempt", "<p>A recent login attempt failed.  Details of the attempt are below.</p>" . "<p>Date/Time: " . date("d/m/Y H:i:s") . "<br>" . "Username: " . $username . "<br>" . "IP Address: " . $remote_ip . "<br>" . "Hostname: " . gethostbyaddr($remote_ip) . "</p>");
                }
                else
                {
                    sendAdminNotification('system', "WHMCS Admin Failed Login Attempt", "<p>A recent login attempt failed.  Details of the attempt are below.</p>" . "<p>Date/Time: " . date("d/m/Y H:i:s") . "<br>" . "Username: " . $this->inputusername . "<br>" . "IP Address: " . $remote_ip . "<br>" . "Hostname: " . gethostbyaddr($remote_ip) . "</p>");
                }
            }
            return false;
        }
        $loginfailures = unserialize($whmcs->get_config('LoginFailures'));
        if( !is_array($loginfailures[$remote_ip]) )
        {
            $loginfailures[$remote_ip] = array(  );
        }
        if( $loginfailures[$remote_ip]['expires'] < time() )
        {
            $loginfailures[$remote_ip]['count'] = 0;
        }
        $loginfailures[$remote_ip]['count']++;
        $loginfailures[$remote_ip]['expires'] = time() + 30 * 60;
        if( 3 <= $loginfailures[$remote_ip]['count'] )
        {
            unset($loginfailures[$remote_ip]);
            insert_query('tblbannedips', array( 'ip' => $remote_ip, 'reason' => "3 Invalid Login Attempts", 'expires' => $this->getLoginBanDate() ));
        }
        $whmcs->set_config('LoginFailures', serialize($loginfailures));
        if( isset($this->admindata['username']) )
        {
            $username = $this->admindata['username'];
            sendAdminNotification('system', "WHMCS Admin Failed Login Attempt", "<p>A recent login attempt failed.  Details of the attempt are below.</p><p>Date/Time: " . date("d/m/Y H:i:s") . "<br>Username: " . $username . "<br>IP Address: " . $remote_ip . "<br>Hostname: " . gethostbyaddr($remote_ip) . "</p>");
            logActivity("Failed Admin Login Attempt - Username: " . $username);
        }
        else
        {
            sendAdminNotification('system', "WHMCS Admin Failed Login Attempt", "<p>A recent login attempt failed.  Details of the attempt are below.</p><p>Date/Time: " . date("d/m/Y H:i:s") . "<br>Username: " . $this->inputusername . "<br>IP Address: " . $remote_ip . "<br>Hostname: " . gethostbyaddr($remote_ip) . "</p>");
            logActivity("Failed Admin Login Attempt - IP: " . $remote_ip);
        }
    }
    public static function getID()
    {
        return WHMCS_Auth::isloggedin() ? (int) $_SESSION['adminid'] : 0;
    }
    public static function isLoggedIn()
    {
        return isset($_SESSION['adminid']);
    }
    public function logout()
    {
        if( $this->isLoggedIn() )
        {
            update_query('tbladminlog', array( 'logouttime' => "now()" ), array( 'sessionid' => session_id() ));
            $adminid = $_SESSION['adminid'];
            session_unset();
            session_destroy();
            $this->unsetRememberMeCookie();
            run_hook('AdminLogout', array( 'adminid' => $adminid ));
            return true;
        }
        return false;
    }
    public function isSessionPWHashValid($whmcsclass = false)
    {
        if( isset($_SESSION['adminpw']) && $this->isAdminPWHashSet() && $_SESSION['adminpw'] == $this->generateAdminSessionHash($whmcsclass) )
        {
            return true;
        }
        return false;
    }
    public function updateAdminLog()
    {
        global $whmcs;
        if( !$this->isLoggedIn() )
        {
            return false;
        }
        $result = select_query('tbladminlog', 'id', "lastvisit>='" . date("Y-m-d H:i:s", mktime(date('H'), date('i') - 15, date('s'), date('m'), date('d'), date('Y'))) . "' AND sessionid='" . db_escape_string(session_id()) . "' AND logouttime='00000000000000'");
        $data = mysql_fetch_array($result);
        $adminlogid = $data['id'];
        if( $adminlogid )
        {
            update_query('tbladminlog', array( 'lastvisit' => "now()" ), array( 'id' => $adminlogid ));
        }
        else
        {
            full_query("UPDATE tbladminlog SET logouttime=lastvisit WHERE adminusername='" . mysql_real_escape_string($this->getAdminUsername()) . "' AND logouttime='00000000000000'");
            insert_query('tbladminlog', array( 'adminusername' => $this->getAdminUsername(), 'logintime' => "now()", 'lastvisit' => "now()", 'ipaddress' => WHMCS_Utility_Environment_CurrentUser::getip(), 'sessionid' => session_id() ));
        }
        return true;
    }
    public function destroySession()
    {
        session_unset();
        session_destroy();
        return true;
    }
}