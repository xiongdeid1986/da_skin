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
 * Module Handling Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Module
{
    protected $type = '';
    protected $loadedmodule = '';
    protected $metaData = array(  );
    protected $moduleParams = array(  );
    const FUNCTIONDOESNTEXIST = "!Function not found in module!";
    public function __construct($type = '')
    {
        if( $type )
        {
            $this->setType($type);
        }
    }
    public function setType($type)
    {
        global $whmcs;
        $type = $whmcs->sanitize('a-z', $type);
        $this->type = $type;
    }
    protected function getType()
    {
        global $whmcs;
        $type = $whmcs->sanitize('a-z', $this->type);
        return $type;
    }
    protected function setLoadedModule($module)
    {
        $this->loadedmodule = $module;
    }
    public function getLoadedModule()
    {
        return $this->loadedmodule;
    }
    public function getList($type = '')
    {
        if( $type )
        {
            $this->setType($type);
        }
        $base_dir = $this->getBaseModuleDir();
        if( is_dir($base_dir) )
        {
            $modules = array(  );
            $dh = opendir($base_dir);
            while( false !== ($module = readdir($dh)) )
            {
                if( is_file($this->getModulePath($module)) )
                {
                    $modules[] = $module;
                }
            }
            sort($modules);
            return $modules;
        }
        return false;
    }
    public function getBaseModuleDir()
    {
        return ROOTDIR . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $this->getType();
    }
    public function getModulePath($module)
    {
        $base_dir = $this->getBaseModuleDir();
        switch( $this->getType() )
        {
            case 'gateways':
                return $base_dir . DIRECTORY_SEPARATOR . $module . ".php";
                break;
            default:
                return $base_dir . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $module . ".php";
                break;
        }
    }
    public function load($module)
    {
        global $whmcs;
        global $licensing;
        $module = $whmcs->sanitize('0-9a-z_-', $module);
        $modpath = $this->getModulePath($module);
        if( file_exists($modpath) )
        {
            include_once($modpath);
            $this->setLoadedModule($module);
            $this->setMetaData($this->getMetaData());
            return true;
        }
        return false;
    }
    public function call($function, $params = array(  ))
    {
        global $whmcs;
        global $licensing;
        if( $this->functionExists($function) )
        {
            $params = $this->prepareParams($params);
            $params = array_merge($this->getParams(), $params);
            return call_user_func($this->getLoadedModule() . '_' . $function, $params);
        }
        return self::FUNCTIONDOESNTEXIST;
    }
    public function functionExists($name)
    {
        return function_exists($this->getLoadedModule() . '_' . $name);
    }
    /**
     * Retrieves Meta Data from the Loaded Module
     *
     * @return mixed
     */
    protected function getMetaData()
    {
        $moduleName = $this->getLoadedModule();
        if( $this->functionExists('MetaData') )
        {
            return $this->call('MetaData');
        }
    }
    /**
     * Stores Meta Data to class store
     *
     * @param array $metaData
     *
     * @return bool
     */
    protected function setMetaData($metaData)
    {
        if( is_array($metaData) )
        {
            $this->metaData = $metaData;
            return true;
        }
        $this->metaData = array(  );
        return false;
    }
    /**
     * Retrieves a value from the Meta Data
     *
     * @param string $keyName The value to fetch
     *
     * @return string
     */
    public function getMetaDataValue($keyName)
    {
        return array_key_exists($keyName, $this->metaData) ? $this->metaData[$keyName] : '';
    }
    /**
     * Retrieves the Display Name for the loaded module
     *
     * @return string
     */
    public function getDisplayName()
    {
        $DisplayName = $this->getMetaDataValue('DisplayName');
        if( !$DisplayName )
        {
            $DisplayName = ucfirst($this->getLoadedModule());
        }
        return $DisplayName;
    }
    /**
     * Retrieves the API Version for the loaded module
     *
     * @return string
     */
    public function getAPIVersion()
    {
        $APIVersion = $this->getMetaDataValue('APIVersion');
        if( !$APIVersion )
        {
            $APIVersion = $this->getDefaultAPIVersion();
        }
        return $APIVersion;
    }
    /**
     * Get Default API Version dependant upon module type
     *
     * For gateways, which more often than not output data in HTML
     * forms, we want the default data passed to them to be entity
     * encoded as it historically has been unless explicity stated
     * otherwise via the meta data.
     *
     * But for other module types, where data is typically used in
     * direct API communication, decoded should be the default and
     * it should be up to the modules to make safe for output,
     * should they even be doing any.
     *
     * @return string
     */
    protected function getDefaultAPIVersion()
    {
        $moduleType = $this->getType();
        switch( $moduleType )
        {
            case 'gateways':
                $version = "1.0";
                break;
            default:
                $version = "1.1";
                break;
        }
        return $version;
    }
    /**
     * Pre-process parameters before passing into module
     *
     * Performs entity decoding or compat-encoding based on
     * API Version of the module in use
     *
     * @param array $params
     *
     * @return array
     */
    public function prepareParams($params)
    {
        if( version_compare($this->getAPIVersion(), "1.1", "<") )
        {
            $params = WHMCS_Input_Sanitize::converttocompathtml($params);
        }
        else
        {
            if( version_compare($this->getAPIVersion(), "1.1", ">=") )
            {
                $params = WHMCS_Input_Sanitize::decode($params);
            }
        }
        return $params;
    }
    /**
     * Add parameter for this module instance
     *
     * @param string $key The key to set
     * @param mixed $value The value to set
     *
     * @return $this
     */
    protected function addParam($key, $value)
    {
        $this->moduleParams[$key] = $value;
        return $this;
    }
    /**
     * Get parameters
     *
     * Params are formatted according to module API Version
     *
     * @return array
     */
    public function getParams()
    {
        $moduleParams = $this->moduleParams;
        return $this->prepareParams($moduleParams);
    }
    /**
     * Get individual parameter value
     *
     * @param string $key The key to get
     *
     * @return mixed
     */
    public function getParam($key)
    {
        $moduleParams = $this->getParams();
        return isset($moduleParams[$key]) ? $moduleParams[$key] : '';
    }
}