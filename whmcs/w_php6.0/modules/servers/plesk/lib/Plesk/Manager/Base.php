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
abstract class Plesk_Manager_Base
{
    public function __call($name, $args)
    {
        $methodName = '_' . $name;
        if( !method_exists($this, $methodName) )
        {
            throw new Exception(Plesk_Registry::getinstance()->translator->translate('ERROR_NO_TEMPLATE_TO_API_VERSION', array( 'METHOD' => $methodName, 'API_VERSION' => $this->getVersion() )));
        }
        $reflection = new ReflectionClass(get_class($this));
        $declaringClassName = $reflection->getMethod($methodName)->getDeclaringClass()->name;
        $declaringClass = new $declaringClassName();
        $version = $declaringClass->getVersion();
        $currentApiVersion = isset(Plesk_Registry::getinstance()->version) ? Plesk_Registry::getinstance()->version : null;
        Plesk_Registry::getinstance()->version = $version;
        $result = call_user_func_array(array( $this, $methodName ), $args);
        Plesk_Registry::getinstance()->version = $currentApiVersion;
        return $result;
    }
    public function getVersion()
    {
        $className = get_class($this);
        return implode(".", str_split(substr($className, strrpos($className, 'V') + 1)));
    }
    public function createTableForAccountStorage()
    {
        if( !mysql_num_rows(full_query("SHOW TABLES LIKE 'mod_pleskaccounts'")) )
        {
            $query = "\r\n              CREATE TABLE IF NOT EXISTS `mod_pleskaccounts` (\r\n                `userid` int(10) unsigned NOT NULL auto_increment,\r\n                `usertype` varchar(30) NOT NULL,\r\n                `panelexternalid` varchar(255) NOT NULL,\r\n                PRIMARY KEY  (`userid`),\r\n                KEY `usertype` (`usertype`),\r\n                UNIQUE KEY `panelexternalid` (`panelexternalid`)\r\n              ) ENGINE=MyISAM\r\n            ";
            full_query($query);
        }
    }
    protected function _checkErrors($result)
    {
        if( Plesk_Api::STATUS_OK == (bool) $result->status )
        {
            return NULL;
        }
        switch( (int) $result->errcode )
        {
            case Plesk_Api::ERROR_AUTHENTICATION_FAILED:
                $errorMessage = Plesk_Registry::getinstance()->translator->translate('ERROR_AUTHENTICATION_FAILED');
                break;
            case Plesk_Api::ERROR_AGENT_INITIALIZATION_FAILED:
                $errorMessage = Plesk_Registry::getinstance()->translator->translate('ERROR_AGENT_INITIALIZATION_FAILED');
                break;
            default:
                $errorMessage = (bool) $result->errtext;
                break;
        }
        throw new Exception($errorMessage, (int) $result->errcode);
    }
}