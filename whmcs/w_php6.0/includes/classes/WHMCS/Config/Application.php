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
class WHMCS_Config_Application extends WHMCS_Config_AbstractConfig implements WHMCS_Config_DatabaseInterface
{
    const WHMCS_DEFAULT_CONFIG_FILE = "configuration.php";
    const DEFAULT_ATTACHMENTS_FOLDER = 'attachments';
    const DEFAULT_DOWNLOADS_FOLDER = 'downloads';
    const DEFAULT_COMPILED_TEMPLATES_FOLDER = 'templates_c';
    /**
     * The default Smarty compiled templates folder, relative to ROOTDIR.
     *
     * @type string
     */
    public static function factory()
    {
        $config = new self();
        $file = self::WHMCS_DEFAULT_CONFIG_FILE;
        if( !$config->configFileExists($file) )
        {
            $msg = "<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;padding:10px;color:#cc0000;\"><strong>Welcome to WHMCS!</strong><br>Before you can begin using WHMCS you need to perform the installation procedure. <a href=\"" . (file_exists("install/install.php") ? '' : "../") . "install/install.php\" style=\"color:#000;\">Click here to begin...</a></div>";
            throw new WHMCS_Exception_Fatal(sprintf($msg, ''));
        }
        if( !$config->loadConfigFile($file) )
        {
            $msg = "<div style=\"border: 1px dashed #cc0000;font-family:Tahoma;background-color:#FBEEEB;padding:10px;color:#cc0000;\"><strong>Critical Error</strong><br>Unable to load configuration file. Please check permissions of the configuration.php file.</div>";
            throw new WHMCS_Exception_Fatal(sprintf($msg, ''));
        }
        return $config;
    }
    public function validConfigVariables()
    {
        return array( 'api_access_key', 'api_enable_logging', 'attachments_dir', 'autoauthkey', 'customadminpath', 'cc_encryption_hash', 'disable_iconv', 'disable_admin_ticket_page_counts', 'disable_auto_ticket_refresh', 'disable_clients_list_services_summary', 'display_errors', 'db_host', 'db_name', 'db_username', 'db_password', 'downloads_dir', 'license', 'license_debug', 'mysql_charset', 'overidephptimelimit', 'pleskpacketversion', 'plesk8packetversion', 'plesk10packetversion', 'smtp_debug', 'templates_compiledir', 'use_legacy_client_ip_logic' );
    }
    public function loadConfigFile($file)
    {
        $file = $this->getAbsolutePath($file);
        if( $this->configFileExists($file) )
        {
            ob_start();
            $loaded = include($file);
            ob_end_clean();
            if( $loaded === false )
            {
                return false;
            }
            $validVars = $this->validConfigVariables();
            $data = array(  );
            foreach( $validVars as $var )
            {
                $data[$var] = isset(${$var}) ? ${$var} : null;
            }
            $this->setData($data);
            return $this;
        }
        return false;
    }
    public function configFileExists($file)
    {
        $file = $this->getAbsolutePath($file);
        return file_exists($file) ? true : false;
    }
    protected function getAbsolutePath($file)
    {
        if( strpos($file, ROOTDIR) !== 0 )
        {
            $file = ROOTDIR . DIRECTORY_SEPARATOR . $file;
        }
        return $file;
    }
    public function getDatabaseName()
    {
        return $this->OffsetGet('db_name');
    }
    public function getDatabaseUserName()
    {
        return $this->OffsetGet('db_username');
    }
    public function getDatabasePassword()
    {
        return $this->OffsetGet('db_password');
    }
    public function getDatabaseHost()
    {
        return $this->OffsetGet('db_host');
    }
    public function getDatabaseCharset()
    {
        return $this->OffsetGet('mysql_charset');
    }
    public function setDatabaseCharset($charset)
    {
        $this->OffsetSet('mysql_charset', $charset);
        return $this;
    }
    public function setDatabaseName($name)
    {
        $this->OffsetSet('db_name', $name);
        return $this;
    }
    public function setDatabaseUsername($username)
    {
        $this->OffsetSet('db_username', $username);
        return $this;
    }
    public function setDatabaseHost($host)
    {
        $this->OffsetSet('db_host', $host);
        return $this;
    }
    public function setDatabasePassword($password)
    {
        $this->OffsetSet('db_password', $password);
        return $this;
    }
    /**
     * Determine if custom writeable directory paths are defined
     *
     * In order to be customised, the attachments, downloads and
     * templates_c path settings must be defined in the configuration
     * file and different from their default values.
     *
     * If not customised, we can be certain the writeable directories
     * are within the public doc root and therefore their contents is
     * at risk of public access.
     *
     * @todo We should check to ensure this isn't broken when merging
     * to 6.0 since the config file handling has changed considerably.
     *
     * @return bool
     */
    public function hasCustomWritableDirectories()
    {
        if( $this->OffsetGet('attachments_dir') != ROOTDIR . DIRECTORY_SEPARATOR . self::DEFAULT_ATTACHMENTS_FOLDER . DIRECTORY_SEPARATOR && $this->OffsetGet('downloads_dir') != ROOTDIR . DIRECTORY_SEPARATOR . self::DEFAULT_DOWNLOADS_FOLDER . DIRECTORY_SEPARATOR && $this->OffsetGet('templates_compiledir') != ROOTDIR . DIRECTORY_SEPARATOR . self::DEFAULT_COMPILED_TEMPLATES_FOLDER . DIRECTORY_SEPARATOR )
        {
            return true;
        }
        return false;
    }
}