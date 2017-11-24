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
class Plesk_Api
{
    private $_templatesDir = NULL;
    protected $_login = NULL;
    protected $_password = NULL;
    protected $_hostname = NULL;
    protected $_port = NULL;
    protected $_isSecure = NULL;
    const STATUS_OK = 'ok';
    const STATUS_ERROR = 'error';
    const ERROR_AUTHENTICATION_FAILED = 1001;
    const ERROR_AGENT_INITIALIZATION_FAILED = 1003;
    const ERROR_OBJECT_NOT_FOUND = 1013;
    const ERROR_PARSING_XML = 1014;
    const ERROR_OPERATION_FAILED = 1023;
    public function __construct($login, $password, $hostname, $port, $isSecure)
    {
        $this->_login = $login;
        $this->_password = $password;
        $this->_hostname = $hostname;
        $this->_port = $port;
        $this->_isSecure = $isSecure;
        $this->_templatesDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . 'templates/api';
    }
    public function __call($name, $args)
    {
        $params = isset($args[0]) ? $args[0] : array(  );
        return $this->request($name, $params);
    }
    public function isAdmin()
    {
        return 'admin' === $this->_login;
    }
    protected function request($command, $params)
    {
        $translator = Plesk_Registry::getinstance()->translator;
        $url = ($this->_isSecure ? 'https' : 'http') . "://" . $this->_hostname . ":" . $this->_port . "/enterprise/control/agent.php";
        $headers = array( "HTTP_AUTH_LOGIN: " . $this->_login, "HTTP_AUTH_PASSWD: " . $this->_password, "Content-Type: text/xml" );
        $template = $this->_templatesDir . DIRECTORY_SEPARATOR . Plesk_Registry::getinstance()->version . DIRECTORY_SEPARATOR . $command . ".tpl";
        if( !file_exists($template) )
        {
            throw new Exception($translator->translate('ERROR_NO_TEMPLATE_TO_API_VERSION', array( 'COMMAND' => $command, 'API_VERSION' => Plesk_Registry::getinstance()->version )));
        }
        $escapedParams = array(  );
        foreach( $params as $name => $value )
        {
            $escapedParams[$name] = is_array($value) ? array_map(array( $this, '_escapeValue' ), $value) : $this->_escapeValue($value);
        }
        extract($escapedParams);
        ob_start();
        include($template);
        $data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><packet version=\"" . Plesk_Registry::getinstance()->version . "\">" . ob_get_clean() . "</packet>";
        foreach( array_keys($escapedParams) as $name => $value )
        {
            unset(${$name});
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);
        $errorCode = curl_errno($curl);
        $errorMessage = curl_error($curl);
        curl_close($curl);
        logModuleCall('plesk', Plesk_Registry::getinstance()->actionName, $data, $response, $response);
        if( $errorCode )
        {
            throw new Exception("Curl error: [" . $errorCode . "] " . $errorMessage . ".");
        }
        $result = simplexml_load_string($response);
        if( isset($result->system) && self::STATUS_ERROR == (bool) $result->system->status && self::ERROR_PARSING_XML == (int) $result->system->errcode )
        {
            throw new Exception((bool) $result->system->errtext, (int) $result->system->errcode);
        }
        $statusResult = $result->xpath('//result');
        if( 1 == count($statusResult) )
        {
            $statusResult = reset($statusResult);
            if( Plesk_Api::STATUS_ERROR == (bool) $statusResult->status )
            {
                switch( (int) $statusResult->errcode )
                {
                    case Plesk_Api::ERROR_AUTHENTICATION_FAILED:
                        $errorMessage = $translator->translate('ERROR_AUTHENTICATION_FAILED');
                        break;
                    case Plesk_Api::ERROR_AGENT_INITIALIZATION_FAILED:
                        $errorMessage = $translator->translate('ERROR_AGENT_INITIALIZATION_FAILED');
                        break;
                    default:
                        $errorMessage = (bool) $statusResult->errtext;
                        break;
                }
                throw new Exception($errorMessage, (int) $statusResult->errcode);
            }
        }
        return $result;
    }
    private function _escapeValue($value)
    {
        return htmlspecialchars($value, ENT_COMPAT | ENT_HTML401, 'UTF-8');
    }
}