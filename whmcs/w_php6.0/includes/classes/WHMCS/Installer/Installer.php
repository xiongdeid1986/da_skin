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
class WHMCS_Installer_Installer {
	protected $installed = false;
	protected $version = NULL;
	protected $latestversion = NULL;
	protected $db = NULL;
	private $versions = array("3.2.0", "3.2.1", "3.2.2", "3.2.3", "3.3.0", "3.4.0", "3.4.1", "3.5.0", "3.5.1", "3.6.0", "3.6.1", "3.6.2", "3.7.0", "3.7.1", "3.7.2", "3.8.0", "3.8.1", "3.8.2", "4.0.0", "4.0.1", "4.1.0", "4.1.1", "4.1.2", "4.2.0", "4.2.1", "4.3.0", "4.3.1", "4.4.0", "4.4.1", "4.4.2", "4.5.0", "4.5.1", "4.5.2", "5.0.0", "5.0.1", "5.0.2", "5.0.3", "5.1.0", "5.1.1", "5.1.2", "5.2.0", "5.2.1", "5.2.2", "5.2.3", "5.2.4", "5.2.5", "5.3.0", "5.3.1", "5.3.2", "5.3.3-rc.1", "5.3.3-rc.2", "5.3.3-release.1", "5.3.4-release.1", "5.3.5-release.1", "5.3.6-release.1", "5.3.8-release.1", "5.3.9-release.1", "5.3.10-release.1", "5.3.11-release.1", "5.3.12-release.1", "5.3.13-release.1", "5.3.14-release.1");
	protected $customadminpath = 'admin';
	protected $installerDirectory = '';
	const DEFAULT_VERSION = "0.0.0";
	/**
	 * @var MySQL Resource
	 */
	public function __construct($installedVersion, $latestVersionAvailable) {
		$this->setVersion($installedVersion);
		$this->setLatestVersion($latestVersionAvailable);
		$this->checkIfInstalled();
	}
	public function setInstallerDirectory($dir) {
		if(!is_dir($dir)) {
			throw new WHMCS_Exception_Installer(sprintf("\"%s\" is not a valid installer directory", $dir));
		}
		$this->installerDirectory = $dir;
	}
	public function getInstallerDirectory() {
		return $this->installerDirectory;
	}
	public function isInstalled() {
		return $this->installed;
	}
	/**
	 * Get the target version for this copy of product
	 * Format: Major.Minor  (ie, "5.2")
	 *
	 * @return string
	 */
	public function getLatestMajorMinorVersion() {
		$latest = $this->getLatestVersion();
		return sprintf("%s.%s", $latest->getMajor(), $latest->getMinor());
	}
	/**
	 * Get the current installed version of product
	 * Format: Major.Minor.Patch  (ie, "5.2.1")
	 *
	 * @return string
	 */
	public function getInstalledVersion() {
		return $this->getVersion()->getRelease();
	}
	/**
	 * @TODO investigate the use of this...refactor likely broke it
	 * @return string
	 */
	public function getInstalledVersionNumeric() {
		$previous = $this->getVersion();
		return sprintf("%s%s%s", $previous->getMajor(), $previous->getMinor(), $previous->getPatch());
	}
	/**
	 * Compare a target version and the current install version
	 *
	 * @param WHMCS_Version_SemanticVersion $versionOfInterest
	 *
	 * @return bool
	 */
	protected function shouldRunUpgrade($versionOfInterest) {
		$previousInstalledVersion = $this->getVersionFromDatabase();
		return WHMCS_Version_SemanticVersion::compare($versionOfInterest, $previousInstalledVersion, ">");
	}
	/**
	 * Is the version of this product the same as the one installed?
	 *
	 * @return bool
	 */
	public function isUpToDate() {
		return !$this->shouldRunUpgrade($this->latestversion);
	}
	public function checkIfInstalled() {
		if(file_exists(ROOTDIR . "/configuration.php")) {
			$db_host = $db_username = $db_password = $db_name = $mysql_charset = $customadminpath = '';
			include(ROOTDIR . "/configuration.php");
			if($customadminpath) {
				$this->customadminpath = $customadminpath;
			}
			if(function_exists('mysql_connect') && $db_username && $db_name) {
				try {
					$this->setDatabase($this->factoryDatabase($db_host, $db_username, $db_password, $db_name, $mysql_charset));
				}
				catch(Exception $e) {
				}
				$db = $this->getDatabase();
				if($db) {
					$previousVersion = $this->getVersionFromDatabase();
					if($previousVersion instanceof WHMCS_Version_SemanticVersion) {
						$this->setVersion($previousVersion);
					}
					if(!WHMCS_Version_SemanticVersion::compare(new WHMCS_Version_SemanticVersion(self::DEFAULT_VERSION), $previousVersion, "==")) {
						$this->installed = true;
					}
				}
			}
		}
		return $this;
	}
	/**
	 * Build a MySQL resource
	 *
	 * @param string $db_host
	 * @param string $db_username
	 * @param string $db_password
	 * @param string $db_name
	 * @param string $mysql_charset
	 *
	 * @return resource
	 * @throws Exception
	 */
	public function factoryDatabase($db_host = "127.0.0.1", $db_username = '', $db_password = '', $db_name = '', $mysql_charset = '') {
		if(!($link = mysql_connect($db_host, $db_username, $db_password))) {
			throw new Exception(sprintf("Could not connect to MySQL at \"%s\" with user \"%s\"", $db_host, $db_username));
		}
		if(!mysql_select_db($db_name)) {
			throw new Exception(sprintf("Could not connect to MySQL database \"%s\"", $db_name));
		}
		if($mysql_charset) {
			$query = sprintf("SET NAMES \"%s\"", mysql_real_escape_string($mysql_charset));
			mysql_query($query, $link);
		}
		return $link;
	}
	/**
	 * Get a version object that will represent the state of this deployment
	 *
	 * Look in database for a version string, if a valid on cannot be found,
	 * use the default (0.0.0) to represent a fresh install
	 *
	 * @return WHMCS_Version_SemanticVersion
	 */
	protected function getVersionFromDatabase() {
		$versionToReturn = new WHMCS_Version_SemanticVersion(self::DEFAULT_VERSION);
		try {
			$storedVersion   = $this->fetchDatabaseConfigurationValue('Version');
			$previousVersion = $storedVersion ? $storedVersion : self::DEFAULT_VERSION;
			if($storedVersion == "5.3.3") {
				$storedVersion .= "-rc.1";
			}
			$versionToReturn = new WHMCS_Version_SemanticVersion($previousVersion);
		}
		catch(Exception $e) {
		}
		return $versionToReturn;
	}
	/**
	 * Get the MySQL resource
	 *
	 * @return mysql resource
	 */
	public function getDatabase() {
		return $this->database;
	}
	/**
	 * Set the MySQL resource
	 *
	 * @param $db mysql resource
	 *
	 * @return $this
	 */
	public function setDatabase($db) {
		$this->database = $db;
		return $this;
	}
	/**
	 * Fetch a setting from the tblconfiguration table
	 *
	 * Unfound settings will provide a default value as specified by MySQL/PHP
	 *
	 * @param string $key
	 *
	 * @return string
	 * @throws InvalidArgumentException
	 * @throws Exception
	 */
	protected function fetchDatabaseConfigurationValue($key = 'Version') {
		if(!is_string($key)) {
			throw new InvalidArgumentException("Configuration setting to retrieve must be a string");
		}
		$query = sprintf("SELECT value FROM tblconfiguration WHERE setting=\"%s\"", $key);
		$db    = $this->getDatabase();
		if($result = mysql_query($query, $db)) {
			$data = mysql_fetch_array($result);
			if(isset($data['value'])) {
				return trim($data['value']);
			}
			throw new Exception(sprintf("Could not retrieve configuration value for \"%s\" . Invalid database schema", $key));
		}
		throw new Exception("Could not query database");
	}
	/**
	 * Store a value in tblconfiguration
	 *
	 * @param $value
	 * @param string $key
	 *
	 * @return $this
	 * @throws InvalidArgumentException
	 */
	protected function storeDatabaseConfigurationValue($value, $key = 'Version') {
		if(!is_string($value)) {
			throw new InvalidArgumentException("Configuration setting value to store must be a string");
		}
		if(!is_string($key)) {
			throw new InvalidArgumentException("Configuration setting name to store must be a string");
		}
		$db    = $this->getDatabase();
		$query = sprintf("UPDATE tblconfiguration SET value=\"%s\" WHERE setting=\"%s\"", $value, $key);
		mysql_query($query, $db);
		return $this;
	}
	/**
	 * Iterate throw entire list of version that require data or schema
	 * mutation for valid core file use
	 *
	 * @throws WHMCS_Exception_Installer
	 */
	public function runUpgrades() {
		$installerSqlDir = $this->getInstallerDirectory() . '/sql/';
		foreach($this->versions as $ver) {
			try {
				$versionOfInterest = new WHMCS_Version_SemanticVersion($ver);
			}
			catch(Exception $e) {
				throw new WHMCS_Exception_Installer("Unable to interpret \"%s\" as a valid, version to install", $ver);
			}
			$targetVersion = $versionOfInterest->getRelease();
			$vernum        = str_replace(".", '', $targetVersion);
			if($this->shouldRunUpgrade($versionOfInterest)) {
				if(function_exists('v' . $vernum . 'Upgrade')) {
					call_user_func('v' . $vernum . 'Upgrade');
				} else {
					$filename = sprintf("upgrade%s.sql", $vernum);
					if(file_exists($installerSqlDir . $filename)) {
						mysql_import_file($filename, $installerSqlDir);
					}
				}
				if(version_compare($targetVersion, "5.3.3", "<")) {
					$versionToStore = $targetVersion;
				} else {
					$versionToStore = $versionOfInterest->getCanonical();
				}
				$this->storeDatabaseConfigurationValue($versionToStore, 'Version');
				$this->setVersion($versionOfInterest);
			}
		}
	}
	/**
	 * Relative admin path from configuration file?
	 *
	 * @return string
	 */
	public function getAdminPath() {
		return $this->customadminpath;
	}
	/**
	 * Get a version object representing the current known state of deployment
	 * @return WHMCS_Version_SemanticVersion
	 */
	public function getVersion() {
		return $this->version;
	}
	/**
	 *
	 * @param WHMCS_Version_SemanticVersion $version
	 * @return $this
	 */
	public function setVersion($version) {
		$this->version = $version;
		return $this;
	}
	/**
	 *
	 * @return WHMCS_Version_SemanticVersion
	 */
	public function getLatestVersion() {
		return $this->latestversion;
	}
	/**
	 *
	 * @param WHMCS_Version_SemanticVersion $latest
	 */
	public function setLatestVersion($latest) {
		$this->latestversion = $latest;
	}
}