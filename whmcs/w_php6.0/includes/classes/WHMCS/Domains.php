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
 * WHMCS Domains Management Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Domains
{
    private $id = '';
    private $data = array(  );
    private $moduleresults = array(  );
    const ACTIVE_STATUS = 'Active';
    public function __construct()
    {
    }
    public function splitAndCleanDomainInput($domain)
    {
        $domain = trim($domain);
        if( substr($domain, 0 - 1, 1) == '/' )
        {
            $domain = substr($domain, 0, 0 - 1);
        }
        if( substr($domain, 0, 8) == "https://" )
        {
            $domain = substr($domain, 8);
        }
        if( substr($domain, 0, 7) == "http://" )
        {
            $domain = substr($domain, 7);
        }
        if( strpos($domain, ".") !== false )
        {
            $domain = $this->stripOutSubdomains($domain);
            $domainparts = explode(".", $domain, 2);
            $sld = $domainparts[0];
            $tld = isset($domainparts[1]) ? "." . $domainparts[1] : '';
        }
        else
        {
            $sld = $domain;
            $tld = '';
        }
        $sld = $this->clean($sld);
        $tld = $this->clean($tld);
        return array( 'sld' => $sld, 'tld' => $tld );
    }
    /**
     * @param string $domain
     * @return string
     *
     * Strips out "www." from a domain name. -Ted 2013-08-21
     * See Case 3107: Domain Checker parse error w/long compound domain names
     */
    protected function stripOutSubdomains($domain)
    {
        $domain = preg_replace("/^www\\./", '', $domain);
        return $domain;
    }
    public function clean($val)
    {
        global $whmcs;
        $val = trim($val);
        if( !$whmcs->get_config('AllowIDNDomains') )
        {
            $val = strtolower($val);
        }
        else
        {
            if( function_exists('mb_strtolower') )
            {
                $val = mb_strtolower($val);
            }
        }
        return $val;
    }
    public function checkDomainisValid($parts)
    {
        global $CONFIG;
        $sld = $parts['sld'];
        $tld = $parts['tld'];
        if( $sld[0] == '-' || $sld[strlen($sld) - 1] == '-' )
        {
            return 0;
        }
        $isIdn = $isIdnTld = $skipAllowIDNDomains = false;
        if( $CONFIG['AllowIDNDomains'] )
        {
            WHMCS_Application::getinstance()->load_function('whois');
            $idnConvert = new WHMCS_Domains_Idna();
            $idnConvert->encode($sld);
            if( $idnConvert->get_last_error() && $idnConvert->get_last_error() != "The given string does not contain encodable chars" )
            {
                return 0;
            }
            if( $idnConvert->get_last_error() && $idnConvert->get_last_error() == "The given string does not contain encodable chars" )
            {
                $skipAllowIDNDomains = true;
            }
            else
            {
                $isIdn = true;
            }
        }
        if( $isIdn === false )
        {
            if( preg_replace("/[^.%\$^'#~@&*(),_£?!+=:{}[]()|\\/ \\\\ ]/", '', $sld) )
            {
                return 0;
            }
            if( (!$CONFIG['AllowIDNDomains'] || $skipAllowIDNDomains === true) && preg_replace("/[^a-z0-9-.]/i", '', $sld . $tld) != $sld . $tld )
            {
                return 0;
            }
            if( preg_replace("/[^a-z0-9-.]/", '', $tld) != $tld )
            {
                return 0;
            }
            $validMask = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-';
            if( strspn($sld, $validMask) != strlen($sld) )
            {
                return 0;
            }
        }
        run_hook('DomainValidation', array( 'sld' => $sld, 'tld' => $tld ));
        if( $sld === false && $sld !== 0 || !$tld )
        {
            return 0;
        }
        $coreTLDs = array( ".com", ".net", ".org", ".info", 'biz', ".mobi", ".name", ".asia", ".tel", ".in", ".mn", ".bz", ".cc", ".tv", ".us", ".me", ".co.uk", ".me.uk", ".org.uk", ".net.uk", ".ch", ".li", ".de", ".jp" );
        $DomainMinLengthRestrictions = $DomainMaxLengthRestrictions = array(  );
        require(ROOTDIR . "/configuration.php");
        foreach( $coreTLDs as $cTLD )
        {
            if( !array_key_exists($cTLD, $DomainMinLengthRestrictions) )
            {
                $DomainMinLengthRestrictions[$cTLD] = 3;
            }
            if( !array_key_exists($cTLD, $DomainMaxLengthRestrictions) )
            {
                $DomainMaxLengthRestrictions[$cTLD] = 63;
            }
        }
        if( array_key_exists($tld, $DomainMinLengthRestrictions) && strlen($sld) < $DomainMinLengthRestrictions[$tld] )
        {
            return 0;
        }
        if( array_key_exists($tld, $DomainMaxLengthRestrictions) && $DomainMaxLengthRestrictions[$tld] < strlen($sld) )
        {
            return 0;
        }
        return 1;
    }
    public function getDomainsDatabyID($domainid)
    {
        $where = array( 'id' => $domainid );
        if( defined('CLIENTAREA') )
        {
            if( !isset($_SESSION['uid']) )
            {
                return false;
            }
            $where['userid'] = $_SESSION['uid'];
        }
        return $this->getDomainsData($where);
    }
    private function getDomainsData($where = '')
    {
        $result = select_query('tbldomains', '', $where);
        $data = mysql_fetch_array($result);
        if( $data['id'] )
        {
            $this->id = $data['id'];
            $this->data = $data;
            return $data;
        }
        return false;
    }
    public function isActive()
    {
        if( is_array($this->data) && $this->data['status'] == self::ACTIVE_STATUS )
        {
            return true;
        }
        return false;
    }
    public function getData($var)
    {
        return isset($this->data[$var]) ? $this->data[$var] : '';
    }
    public function getModule()
    {
        global $whmcs;
        return $whmcs->sanitize('0-9a-z_-', $this->getData('registrar'));
    }
    public function hasFunction($function)
    {
        $mod = new WHMCS_Module_Registrar();
        $mod->load($this->getModule());
        return $mod->functionExists($function);
    }
    public function moduleCall($function, $additionalVars = '')
    {
        $mod = new WHMCS_Module_Registrar();
        $module = $this->getModule();
        if( !$module )
        {
            $this->moduleresults = array( 'error' => "Domain not assigned to a registrar module" );
            return false;
        }
        $loaded = $mod->load($module);
        if( !$loaded )
        {
            $this->moduleresults = array( 'error' => "Registrar module not found" );
            return false;
        }
        $mod->setDomainID($this->getData('id'));
        $results = $mod->call($function, $additionalVars);
        if( $results === WHMCS_Module::FUNCTIONDOESNTEXIST )
        {
            $this->moduleresults = array( 'error' => "Function not found" );
            return false;
        }
        $this->moduleresults = $results;
        return is_array($results) && array_key_exists('error', $results) && $results['error'] ? false : true;
    }
    public function getModuleReturn($var = '')
    {
        if( !$var )
        {
            return $this->moduleresults;
        }
        return isset($this->moduleresults[$var]) ? $this->moduleresults[$var] : '';
    }
    public function getLastError()
    {
        return $this->getModuleReturn('error');
    }
    public function getDefaultNameservers()
    {
        global $whmcs;
        $vars = array(  );
        $serverid = get_query_val('tblhosting', 'server', array( 'domain' => $this->getData('domain') ));
        if( $serverid )
        {
            $result = select_query('tblservers', 'nameserver1,nameserver2,nameserver3,nameserver4,nameserver5', array( 'id' => $serverid ));
            $data = mysql_fetch_array($result);
            for( $i = 1; $i <= 5; $i++ )
            {
                $vars['ns' . $i] = trim($data['nameserver' . $i]);
            }
        }
        else
        {
            for( $i = 1; $i <= 5; $i++ )
            {
                $vars['ns' . $i] = trim($whmcs->get_config('DefaultNameserver' . $i));
            }
        }
        return $vars;
    }
    public function getSLD()
    {
        $domain = $this->getData('domain');
        $domainparts = explode(".", $this->getData('domain'), 2);
        return $domainparts[0];
    }
    public function getTLD()
    {
        $domain = $this->getData('domain');
        $domainparts = explode(".", $this->getData('domain'), 2);
        return $domainparts[1];
    }
    public function buildWHOISSaveArray($data)
    {
        $arr = array( "First Name" => 'firstname', "Last Name" => 'lastname', "Full Name" => 'fullname', "Contact Name" => 'fullname', 'Email' => 'email', "Email Address" => 'email', "Job Title" => '', "Company Name" => 'companyname', "Organisation Name" => 'companyname', 'Address' => 'address1', "Address 1" => 'address1', 'Street' => 'address1', "Address 2" => 'address2', 'City' => 'city', 'State' => 'state', 'County' => 'state', 'Region' => 'state', 'Postcode' => 'postcode', "ZIP Code" => 'postcode', 'ZIP' => 'postcode', 'Country' => 'country', 'Phone' => 'phonenumberformatted', "Phone Number" => 'phonenumberformatted' );
        $retarr = array(  );
        foreach( $arr as $k => $v )
        {
            $retarr[$k] = $data[$v];
        }
        return $retarr;
    }
    /**
     * Obtain an array of the last email reminder of each type configured in WHMCS
     * for the current domain.
     *
     * @return array
     */
    public function obtainEmailReminders()
    {
        $reminderData = array(  );
        $reminders = select_query('tbldomainreminders', '', array( 'domain_id' => $this->id ), 'id', 'DESC');
        while( $data = mysql_fetch_assoc($reminders) )
        {
            $reminderData[] = $data;
        }
        return $reminderData;
    }
}