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
 * Handles encoding of incoming user input, decoding, and making
 * safe for output
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Input_Sanitize
{
    /**
     * Makes user input safe for output in terms of preventing execution of HTML
     *
     * Supported Input Types: bool, numeric, string or array
     *
     * We run a decode twice in case data has been double entity encoded.
     * For example encoded pre DB storage and again on retrieval.
     *
     * @param mixed $val The value to be made safe
     * @return mixed
     */
    public static function makeSafeForOutput($val)
    {
        $input = new WHMCS_Input_Sanitize();
        $val = $input->decode($val);
        return $input->encode($val);
    }
    public static function convertToCompatHtml($val)
    {
        $input = new WHMCS_Input_Sanitize();
        $val = $input->decode($val);
        $val = $input->decode($val);
        return $input->encodeToCompatHTML($val);
    }
    /**
     * Encodes an object using HTML Special Chars
     *
     * Supported Input Types: bool, numeric, string or array
     * For any other input type returns blank
     *
     * @param mixed $val The value to be encoded
     *
     * @return mixed
     */
    public static function encode($val)
    {
        $input = new WHMCS_Input_Sanitize();
        if( is_bool($val) )
        {
            return $val;
        }
        if( is_numeric($val) )
        {
            return $val;
        }
        if( is_string($val) )
        {
            return $input->encodeString($val);
        }
        if( is_array($val) )
        {
            return $input->encodeArray($val);
        }
        if( is_object($val) )
        {
            return $val;
        }
        return '';
    }
    /**
     * Encodes an object using HTML Special Chars - no ENT_QUOTES flag
     *
     * Supported Input Types: bool, numeric, string or array
     * For any other input type returns blank
     *
     * @param mixed $val The value to be encoded
     *
     * @return mixed
     */
    public static function encodeToCompatHTML($val)
    {
        $input = new WHMCS_Input_Sanitize();
        if( is_bool($val) )
        {
            return $val;
        }
        if( is_numeric($val) )
        {
            return $val;
        }
        if( is_string($val) )
        {
            return $input->encodeStringToCompatHTML($val);
        }
        if( is_array($val) )
        {
            return $input->encodeArrayToCompatHTML($val);
        }
        if( is_object($val) )
        {
            return $val;
        }
        return '';
    }
    /**
     * Decodes an object using HTML Entity Decode
     *
     * Supported Input Types: bool, numeric, string or array
     * For any other input type returns blank
     *
     * @param mixed $val The value to be decoded
     *
     * @return mixed
     */
    public static function decode($val)
    {
        $input = new WHMCS_Input_Sanitize();
        if( is_bool($val) )
        {
            return $val;
        }
        if( is_numeric($val) )
        {
            return $val;
        }
        if( is_string($val) )
        {
            return $input->decodeString($val);
        }
        if( is_array($val) )
        {
            return $input->decodeArray($val);
        }
        if( is_object($val) )
        {
            return $val;
        }
        return '';
    }
    /**
     * Loops through an array performing the encode action
     *
     * @param array $array The array to be encoded
     *
     * @return array
     */
    protected function encodeArray($array)
    {
        foreach( $array as $k => $v )
        {
            $array[$k] = $this->encode($v);
        }
        return $array;
    }
    /**
     * Loops through an array performing the encode action
     *
     * @param array $array The array to be encoded
     *
     * @return array
     */
    protected function encodeArrayToCompatHTML($array)
    {
        foreach( $array as $k => $v )
        {
            $array[$k] = $this->encodeToCompatHTML($v);
        }
        return $array;
    }
    /**
     * Loops through an array performing the decode action
     *
     * @param array $array The array to be decoded
     *
     * @return array
     */
    protected function decodeArray($array)
    {
        foreach( $array as $k => $v )
        {
            $array[$k] = $this->decode($v);
        }
        return $array;
    }
    /**
     * Perform HTML Special Chars on a string
     *
     * @param string $val The string to be entity encoded
     *
     * @return string
     */
    protected function encodeString($val)
    {
        return htmlspecialchars($val, ENT_QUOTES);
    }
    /**
     * Perform HTML Special Chars on a string
     *
     * @param string $val The string to be entity encoded
     *
     * @return string
     */
    protected function encodeStringToCompatHTML($val)
    {
        static $mask;
        if( !isset($mask) )
        {
            $mask = $this->getCompatBitmask();
        }
        return htmlspecialchars($val, $mask);
    }
    public function getCompatBitmask()
    {
        $mask = ENT_COMPAT;
        if( defined('ENT_HTML401') )
        {
            $mask = $mask | ENT_HTML401;
        }
        return $mask;
    }
    /**
     * Perform an HTML Entity Decode on a String
     *
     * @param string $val The string to be entity decoded
     *
     * @return string
     */
    protected function decodeString($val)
    {
        $val = str_replace("&nbsp;", " ", $val);
        return html_entity_decode($val, ENT_QUOTES);
    }
}