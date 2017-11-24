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
 * Internal class to handle the connections to the API.
 *
 * @package vlx-sdk
 * @author Eric Coleman <eric@varilogix.com>
 **/
class Varilogix_Request
{
    public $_api = NULL;
    public $_params = NULL;
    /**
     * constructor
     *
     * @param string $api
     * @return Varilogix_Request
     */
    public function Varilogix_Request($api)
    {
        $this->_api = $api;
    }
    /**
     * Set params to be sent with the request.
     *
     * @param mixed $params either a string or an array
     */
    public function setParams($params)
    {
        if( is_array($params) )
        {
            foreach( $params as $name => $value )
            {
                $this->_params[$name] = urlencode($value);
            }
        }
        else
        {
            $this->_params = $params;
        }
    }
    /**
     * Actually make the connection to our server and return
     * the result
     *
     * @return mixed
     */
    public function execute()
    {
        $query_string = http_build_query($this->_params);
        $link = curl_init();
        curl_setopt($link, CURLOPT_URL, $this->_api);
        curl_setopt($link, CURLOPT_VERBOSE, 0);
        curl_setopt($link, CURLOPT_POST, 1);
        curl_setopt($link, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($link, CURLOPT_POSTFIELDS, $query_string);
        curl_setopt($link, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($link, CURLOPT_TIMEOUT, 360);
        $res = curl_exec($link);
        curl_close($link);
        return $res;
    }
}