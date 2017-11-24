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
class WHMCS_Input_Validation
{
    /**
    * A PHP implementation of escapeshellcmd().
    *
    * Some hosts add the escapeshellcmd() function to the disabled_functions
    * php.ini directive. Perform escapeshellcmd() manually in those cases.
    *
    * @see http://www.php.net/manual/en/function.escapeshellcmd.php
    * @param string $string
    * @return string
    */
    public function escapeshellcmd($string)
    {
        if( function_exists('escapeshellcmd') && WHMCS_Environment_Php::functionenabled('escapeshellcmd') )
        {
            return escapeshellcmd($string);
        }
        $shellCharacters = array( "#", "&", ';', "`", "|", "*", "?", "~", "<", ">", "^", "(", ")", "[", "]", "{", "}", "\$", chr(10), chr(255) );
        if( WHMCS_Environment_Os::iswindows() )
        {
            $shellCharacters[] = "%";
            $shellCharacters[] = "\\";
            $string = str_replace($shellCharacters, " ", $string);
            $quotePosition = $this->mismatchedQuotePosition($string);
            if( $quotePosition !== false )
            {
                $string = substr_replace($string, " ", $quotePosition, 1);
            }
            $quotePosition = $this->mismatchedQuotePosition($string, "'");
            if( $quotePosition !== false )
            {
                $string = substr_replace($string, " ", $quotePosition, 1);
            }
        }
        else
        {
            $string = str_replace("\\", "\\\\", $string);
            foreach( $shellCharacters as $shellCharacter )
            {
                $string = str_replace($shellCharacter, "\\" . $shellCharacter, $string);
            }
            $quotePosition = $this->mismatchedQuotePosition($string);
            if( $quotePosition !== false )
            {
                $string = substr_replace($string, "\\\"", $quotePosition, 1);
            }
            $quotePosition = $this->mismatchedQuotePosition($string, "'");
            if( $quotePosition !== false )
            {
                $string = substr_replace($string, "\\'", $quotePosition, 1);
            }
        }
        return $string;
    }
    /**
    * Get the location of the final mismatched quote in a string.
    *
    * A string has mismatched quotation marks in it if it has an odd number of
    * quotation marks in it. If a string has mismatched quotes then return the
    * position of the mismatched quote in the string. Otherwise, the string has
    * matching quotes in it, so return false.
    *
    * @param $string
    * @param string $quoteCharacter
    * @return int|false
    */
    public function mismatchedQuotePosition($string, $quoteCharacter = "\"")
    {
        return substr_count($string, $quoteCharacter) % 2 == 0 ? false : strrpos($string, $quoteCharacter);
    }
}