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
 * WHMCS Filters Management Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Filter
{
    private $name = '';
    private $data = array(  );
    private $allowedvars = array(  );
    public function __construct()
    {
        $filtername = $this->getFilename();
        $this->name = $filtername;
        $this->data = WHMCS_Cookie::get('FD', true);
    }
    private function getFilename()
    {
        $whmcs = WHMCS_Application::getinstance();
        return $whmcs->getCurrentFilename();
    }
    public function isActive()
    {
        if( !array_key_exists($this->name, $this->data) )
        {
            return false;
        }
        foreach( $this->data[$this->name] as $v )
        {
            if( $v )
            {
                return true;
            }
        }
        return false;
    }
    public function setAllowedVars($allowedvars)
    {
        $this->allowedvars = $allowedvars;
        return true;
    }
    public function addAllowedVar($var)
    {
        $this->allowedvars[] = $var;
        return true;
    }
    public function getFromReq($var)
    {
        global $whmcs;
        return $whmcs->get_req_var($var);
    }
    public function getFromSession($var)
    {
        return isset($this->data[$this->name][$var]) ? $this->data[$this->name][$var] : '';
    }
    public function get($var)
    {
        $this->addAllowedVar($var);
        if( $this->getFromReq('filter') )
        {
            return $this->getFromSession($var);
        }
        return $this->getFromReq($var);
    }
    public function store()
    {
        if( $this->getFromReq('filter') )
        {
            return false;
        }
        $arr = array(  );
        foreach( $this->allowedvars as $op )
        {
            $arr[$op] = $this->getFromReq($op);
        }
        $this->data[$this->name] = $arr;
        WHMCS_Cookie::set('FD', $this->data);
        return true;
    }
    public function redir($vars = '')
    {
        if( is_array($this->data[$this->name]) )
        {
            if( $vars )
            {
                $vars .= "&filter=1";
            }
            else
            {
                $vars = "filter=1";
            }
        }
        redir($vars);
    }
}