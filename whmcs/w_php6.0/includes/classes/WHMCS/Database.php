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
class WHMCS_Database implements WHMCS_Config_DatabaseInterface
{
    private $dbname = '';
    private $host = '';
    private $password = '';
    private $username = '';
    private $charset = '';
    private $db = NULL;
    public function __construct()
    {
    }
    public function loadConfig($config)
    {
        $this->setDatabaseName($config->getDatabaseName());
        $this->setDatabasePassword($config->getDatabasePassword());
        $this->setDatabaseUsername($config->getDatabaseUsername());
        $this->setDatabaseHost($config->getDatabaseHost());
        $charset = $config->getDatabaseCharset();
        $charset = $charset ? $charset : $this->getDefaultCharset();
        $this->setDatabaseCharset($charset);
        return $this;
    }
    public function connect()
    {
        $obj = $this->legacyCreateDatabaseConnection();
        return $this->storeDatabaseConnection($obj);
    }
    public function storeDatabaseConnection($databaseConnection)
    {
        $this->db = $databaseConnection;
    }
    public function retrieveDatabaseConnection()
    {
        return $this->db;
    }
    public function mysqliCreateDatabaseConnection()
    {
        $host = $this->getDatabaseHost();
        $password = $this->getDatabasePassword();
        $username = $this->getDatabaseUsername();
        $dbname = $this->getDatabaseName();
        $charset = $this->getDatabaseCharset();
        if( !defined('MYSQL_CONN_ERROR') )
        {
            define('MYSQL_CONN_ERROR', "Unable to connect to database.");
        }
        $postConnectQueries = array(  );
        mysqli_report(MYSQLI_REPORT_STRICT ^ MYSQLI_REPORT_INDEX);
        $mysqli = mysqli_init();
        if( !$mysqli )
        {
            throw new mysqli_sql_exception("Unable to initialize database connection");
        }
        if( defined('MYSQLI_OPT_CONNECT_TIMEOUT') )
        {
            if( !$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 600) )
            {
                mysqli_sql_exception("Setting MYSQLI_OPT_CONNECT_TIMEOUT failed");
            }
        }
        else
        {
            $postConnectQueries[] = "SET SESSION wait_timeout=600";
        }
        if( !$mysqli->real_connect($host, $username, $password, $dbname) )
        {
            $msg = mysqli_connect_error();
            $msg = $msg ? $msg : MYSQL_CONN_ERROR;
            throw new mysqli_sql_exception($msg);
        }
        foreach( $postConnectQueries as $query )
        {
            if( !$mysqli->real_query($query) )
            {
                throw new mysqli_sql_exception(sprintf("Could not perform setup query: %s", $query));
            }
        }
        return $mysqli;
    }
    public function legacyCreateDatabaseConnection()
    {
        $host = $this->getDatabaseHost();
        $password = $this->getDatabasePassword();
        $username = $this->getDatabaseUsername();
        $dbname = $this->getDatabaseName();
        $charset = $this->getDatabaseCharset();
        global $whmcsmysql;
        $whmcsmysql = @mysql_connect($host, $username, $password);
        $selected_db = @mysql_select_db($dbname);
        if( !$selected_db )
        {
            throw new RuntimeException("Could not connect to configured database");
        }
        full_query("SET SESSION wait_timeout=600", $whmcsmysql);
        if( $charset )
        {
            full_query("SET NAMES '" . db_escape_string($charset) . "'", $whmcsmysql);
        }
        return $whmcsmysql;
    }
    public function setDatabaseName($name)
    {
        $this->dbname = $name;
        return $this;
    }
    public function getDatabaseName()
    {
        return $this->dbname;
    }
    public function setDatabasePassword($password)
    {
        $this->password = $password;
        return $this;
    }
    public function getDatabasePassword()
    {
        return $this->password;
    }
    public function setDatabaseUsername($username)
    {
        $this->username = $username;
        return $this;
    }
    public function getDatabaseUsername()
    {
        return $this->username;
    }
    public function setDatabaseHost($host)
    {
        $this->host = $host;
        return $this;
    }
    public function getDatabaseHost()
    {
        return $this->host;
    }
    public function getDatabaseCharset()
    {
        return $this->charset;
    }
    public function setDatabaseCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }
    public function getDefaultCharset()
    {
        return '';
    }
    public function getTblConfigurationData()
    {
        $db = $this->retrieveDatabaseConnection();
        if( $db instanceof mysqli )
        {
            throw new RuntimeException("dbfunction.php does not use mysqli");
        }
        $result = select_query('tblconfiguration', '', '', '', '', '', '', $db);
        $settings = array(  );
        while( $data = @mysql_fetch_array($result) )
        {
            $settings[$data['setting']] = $data['value'];
        }
        return $settings;
    }
}