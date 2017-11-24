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
 * Class WHMCS_Utility_Binary
 *
 * Collection of binary-safe functions
 *
 * Some of this have been gathered from MIT licenced code; attribute precedes
 * normal docblock
 *
 * @link https://github.com/ircmaxell/password_compat Compatability library
 * for PHP >= 5.3.7 which provides PHP 5.5 password_* functions
 *
 * @copyright Copyright (c) WHMCS Limited 2005-2014
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */
class WHMCS_Utility_Binary
{
    /**
     * Count the number of bytes in a string
     *
     * We cannot simply use strlen() for this, because it might be overwritten by the mbstring extension.
     * In this case, strlen() will count the number of *characters* based on the internal encoding. A
     * sequence of bytes might be regarded as a single multibyte character.
     *
     * @param string $binary_string The input string
     *
     * @internal
     * @return int The number of bytes
     */
    public static function strlen($binary_string)
    {
        if( function_exists('mb_strlen') )
        {
            return mb_strlen($binary_string, '8bit');
        }
        return strlen($binary_string);
    }
    /**
     * Get a substring based on byte limits
     *
     * @see _strlen()
     *
     * @param string $binary_string The input string
     * @param int    $start
     * @param int    $length
     *
     * @internal
     * @return string The substring
     */
    public static function substr($binary_string, $start, $length)
    {
        if( function_exists('mb_substr') )
        {
            return mb_substr($binary_string, $start, $length, '8bit');
        }
        return substr($binary_string, $start, $length);
    }
}