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
 * Varilogix_Call_Result is the api you should be using to fetch the actual
 * result of a call.  While the call status is 'pending' you should be using
 * this to get the pass/fail/error response
 *
 * @package vlx-sdk
 * @author Eric Coleman <eric@varilogix.com>
 **/
class Varilogix_Call_Result
{
    public $_api = "https://v3.varilogix.com/api/%s/pickup/";
    public $_apiName = null;
    public $_code = NULL;
    public $_message = NULL;
    public $_rawResponse = NULL;
    /**
     * Constructor
     *
     * @return Varilogix_Call_Result
     * @author Eric Coleman <eric@varilogix.com>
     **/
    public function Varilogix_Call_Result($apiName)
    {
        $this->_api = sprintf($this->_api, $apiName);
        $this->_apiName = $apiName;
    }
    /**
     * Fetch the result of the call
     *
     * @see getCode
     * @see getMessage
     * @param string $call_id
     * @return string either pass/fail/calling
     */
    public function fetch($call_id)
    {
        $call = new Varilogix_Request($this->_api);
        $call->setParams(array( 'call_id' => $call_id ));
        $this->_rawResponse = $call->execute();
        $result = explode(',', $this->_rawResponse);
        $this->_code = trim($result[1]);
        $this->_message = trim($result[2]);
        return trim($result[0]);
    }
    /**
     * Returns the error code
     *
     * @return int
     * @author Eric Coleman <eric@varilogix.com>
     **/
    public function getCode()
    {
        return $this->_code;
    }
    /**
     * Returns the error message
     *
     * @return string
     * @author Eric Coleman <eric@varilogix.com>
     **/
    public function getMessage()
    {
        return $this->_message;
    }
    public function getRawResponse()
    {
        return $this->_rawResponse;
    }
    /**
     * WARNING: DO NOT USE THIS METHOD.  IT WILL MESS UP YOUR RESPONSES
     *
     * @private
     * @hidden
     */
    public function isCompat()
    {
        $this->_api .= "?compat=true";
    }
}