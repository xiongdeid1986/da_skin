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
class WHMCS_Terminus
{
    private static $instance = NULL;
    private $errorLevel = 0;
    const WHMCS_DEFAULT_EXCEPTION_HANDLER = 'whmcsExceptionHandler';
    /**
     * Build a new terminus instance.
     *
     * @param array|string $exceptionHandler
     * @param int $errorLevel
     */
    public function __construct($exceptionHandler = self::WHMCS_DEFAULT_EXCEPTION_HANDLER, $errorLevel = 0)
    {
        if( $this->setExceptionHandler($exceptionHandler) === false )
        {
            throw new WHMCS_Exception_Fatal("Invalid exception handler");
        }
        $this->setErrorReportingLevel($errorLevel);
    }
    /**
     * Set a custom exception handler.
     *
     * @param array|string $func
     * @return WHMCS_Terminus|bool
     */
    protected function setExceptionHandler($func)
    {
        if( !$func || !is_string($func) && is_array($func) )
        {
            return false;
        }
        if( $func == self::WHMCS_DEFAULT_EXCEPTION_HANDLER )
        {
            $func = array( $this, self::WHMCS_DEFAULT_EXCEPTION_HANDLER );
        }
        set_exception_handler($func);
        return $this;
    }
    /**
     * Handle uncaught exceptions.
     *
     * The default WHMCS exception handler calls self::doExit() on an uncaught
     * WHMCS_Exception_Exit, calls self::doDie() on an uncaught
     * WHMCS_Exception_Fatal, or throws the exception for all other uncaught
     * exceptions (likely leading to an uncaught exception PHP fatal error).
     *
     * @param Exception $exception
     */
    public static function whmcsExceptionHandler($exception)
    {
        $terminus = self::getinstance();
        if( $exception instanceof WHMCS_Exception_Exit )
        {
            $msg = $exception->getMessage();
            if( $msg )
            {
                echo $msg;
            }
            $terminus->doExit(1);
        }
        else
        {
            if( $exception instanceof WHMCS_Exception_Fatal )
            {
                $terminus->doDie($exception->getMessage());
            }
        }
        $class = get_class($exception);
        if( version_compare(PHP_VERSION, "5.3.6", ">=") )
        {
            restore_exception_handler();
            throw new $class($exception->getMessage(), $exception->getCode(), $exception);
        }
        if( strpos($class, 'WHMCS_Exception') === 0 )
        {
            $errorLevel = E_USER_WARNING;
        }
        else
        {
            $errorLevel = E_USER_ERROR;
        }
        trigger_error($exception->getMessage(), $errorLevel);
        $terminus->doExit(1);
    }
    /**
     * Set the WHMCS_Terminus singleton.
     *
     * @param WHMCS_Terminus $terminus
     * @return WHMCS_Terminus
     */
    protected static function setInstance($terminus)
    {
        self::$instance = $terminus;
        return $terminus;
    }
    /**
     * Remove the WHMCS_Terminus singleton.
     */
    protected static function destroyInstance()
    {
        self::$instance = null;
    }
    /**
     * Retrieve a WHMCS_Terminus object via singleton.
     *
     * @param array|string $exceptionHandler
     * @return WHMCS_Terminus
     */
    public static function getInstance($exceptionHandler = self::WHMCS_DEFAULT_EXCEPTION_HANDLER, $errorLevel = 0)
    {
        if( is_null(self::$instance) )
        {
            self::setinstance(new WHMCS_Terminus($exceptionHandler));
        }
        return self::$instance;
    }
    /**
     * Sanely call exit( dirname(__FILE__) . " | line".__LINE__ )
     *
     * @param int $status
     */
    public function doExit($status = 0)
    {
        $status = (int) $status;
        exit( $status );
    }
    /**
     * Sanely call die()
     *
     * @param string $msg
     */
    public function doDie($msg = '')
    {
        if( is_string($msg) )
        {
            exit( $msg );
        }
        exit( dirname(__FILE__) . " | line".__LINE__ );
    }
    /**
     * Set WHMCS's error_reporting() level.
     *
     * @param int $errorLevel
     * @return WHMCS_Terminus
     */
    public function setErrorReportingLevel($errorLevel = 0)
    {
        if( !is_numeric($errorLevel) )
        {
            throw new InvalidArgumentException("Error reporting level must be numeric");
        }
        $this->errorLevel = $errorLevel;
        error_reporting($errorLevel);
        return $this;
    }
    /**
     * Retrieve WHMCS's error_reporting() level.
     *
     * @return int
     */
    public function getErrorReportingLevel()
    {
        return $this->errorLevel;
    }
    /**
     * Disable PHP ini 'display_errors'
     *
     * @return WHMCS_Terminus
     */
    public function disableIniDisplayErrors()
    {
        ini_set('display_errors', false);
        return $this;
    }
    /**
     * Enable PHP ini 'display_errors'
     *
     * @param bool $setting
     * @throws InvalidArgumentException if not an appropriate TRUE value
     *
     * @return WHMCS_Terminus
     */
    public function enableIniDisplayErrors($setting = true)
    {
        if( !$setting )
        {
            $msg = "\"%s\" is not a valid value for enabling \"display_errors\". Please see PHP Manual for an appropriate value for your PHP version %s";
            throw new InvalidArgumentException(sprintf($msg, $setting, PHP_VERSION));
        }
        if( is_string($setting) )
        {
            $setting = strtolower($setting);
            if( $setting == 'on' || $setting === '1' || $setting === 'true' )
            {
                $iniValue = true;
            }
            else
            {
                if( $setting == 'stderr' || $setting == 'stdout' )
                {
                    if( version_compare(PHP_VERSION, "5.2.4", "<") )
                    {
                        $msg = "\"%s\" is not a valid value for \"display_errors\". Please see PHP Manual for an appropriate value for your PHP version %s";
                        throw new InvalidArgumentException(sprintf($msg, $setting, PHP_VERSION));
                    }
                    $iniValue = $setting;
                }
                else
                {
                    $msg = "\"%s\" is not a valid value for \"display_errors\". Please see PHP Manual for an appropriate value for your PHP version %s";
                    throw new InvalidArgumentException(sprintf($msg, $setting, PHP_VERSION));
                }
            }
            $iniValue = $setting;
        }
        else
        {
            if( !is_bool($setting) )
            {
                $msg = "\"%s\" is not a valid value for \"display_errors\". Please see PHP Manual for an appropriate value for your PHP version %s";
                throw new InvalidArgumentException(sprintf($msg, $setting, PHP_VERSION));
            }
            $iniValue = true;
        }
        ini_set('display_errors', $iniValue);
        return $this;
    }
}