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
class Plesk_Loader
{
    public static function init($params)
    {
        spl_autoload_register(array( 'Plesk_Loader', 'autoload' ));
        $port = $params['serveraccesshash'] ? $params['serveraccesshash'] : ($params['serversecure'] ? 8443 : 8880);
        list(, $caller) = debug_backtrace(false);
        Plesk_Registry::getinstance()->actionName = $caller['function'];
        Plesk_Registry::getinstance()->translator = new Plesk_Translate();
        Plesk_Registry::getinstance()->api = new Plesk_Api($params['serverusername'], $params['serverpassword'], $params['serverhostname'], $port, $params['serversecure']);
        $manager = new Plesk_Manager_V1000();
        foreach( $manager->getSupportedApiVersions() as $version )
        {
            $managerClassName = 'Plesk_Manager_V' . str_replace(".", '', $version);
            if( class_exists($managerClassName) )
            {
                Plesk_Registry::getinstance()->manager = new $managerClassName();
                break;
            }
        }
        if( !isset(Plesk_Registry::getinstance()->manager) )
        {
            throw new Exception(Plesk_Registry::getinstance()->translator->translate('ERROR_NO_APPROPRIATE_MANAGER'));
        }
    }
    public static function autoload($className)
    {
        $filePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $className) . ".php";
        if( file_exists($filePath) )
        {
            require_once($filePath);
        }
    }
}