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
 * Wraps a series of static functions for pushing updates from our
 * english language files to all the rest.
 *
 * @copyright Copyright (c) WHMCS Limited 2005-2014
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */
class WHMCS_Utility_LanguageUpdate
{
    /**
     * Enforce the line format required for our translation files.
     *
     * We need to make sure that the key section uses single quotes, that the value section
     * is wrapped in double quotes, that we escape double quotes in the string section
     * accordingly. See the unit tests for examples of expected behavior.
     * @param  string $lineContent - line to clean.
     * @return string the correctly formatted line.
     */
    public static function correctLineFormat($lineContent)
    {
        $parts = WHMCS_Utility_LanguageUpdate::splitline($lineContent);
        $key = str_replace("\"", "'", $parts[0]);
        $value = preg_replace(array( "/^'/", "/';\$/" ), array( "\"", "\";" ), trim($parts[1]));
        $value = preg_replace("/\\\"/", "\"", $value);
        $value = substr($value, 1);
        $value = substr($value, 0, 0 - 2);
        $value = preg_replace("/\"/", "\\\"", $value);
        return $key . "= \"" . $value . "\";";
    }
    /**
     * explode a key / value line into an array. See unit tests for expected behavior.
     * @param  string $lineToSplit
     * @return array  two items, key and value
     */
    public static function splitLine($lineToSplit)
    {
        return explode("=", $lineToSplit, 2);
    }
    /**
     * return a list of all language files to update given a directory to check
     * @param  string $dirpath  the directory to check
     * @return array            a list of languages to update inside that directory
     */
    public static function getLanguages($dirpath)
    {
        $langs = array(  );
        $dh = opendir($dirpath);
        while( false !== ($file = readdir($dh)) )
        {
            if( !is_dir($dirpath . $file) )
            {
                $path_parts = pathinfo($dirpath . $file);
                if( $path_parts['extension'] == 'php' && $path_parts['filename'] != 'english' && $path_parts['filename'] != 'langupdate' )
                {
                    $langs[] = $path_parts['filename'];
                }
            }
        }
        closedir($dh);
        return $langs;
    }
    /**
     * This function updates a language translation file, based on the previously loaded
     * lines in the cannonical english translation. The goal is to keep the order and formatting
     * of the cannonical file, while making sure that the translation includes all of the translated strings,
     * and has english fallbacks for all strings that have not yet been translated.
     * @param  string $lang    the language name you want us to push new strings to.
     * @param  string $dirpath path to load the translated file we are updating.
     * @param  array  $lines   the full text of the cannonical english translation to consider.
     * @return string the full updated language file to write out
     */
    public static function updateTranslation($lang, $dirpath, $lines)
    {
        $_LANG = array(  );
        require($dirpath . $lang . ".php");
        $translatedLines[$lang] = file($dirpath . $lang . ".php");
        $data = '';
        foreach( $lines as $lineNumber => $lineContent )
        {
            if( substr($lineContent, 0, 2) == " *" )
            {
                if( substr($translatedLines[$lang][$lineNumber], 0, 2) == " *" )
                {
                    $data .= $translatedLines[$lang][$lineNumber];
                }
                else
                {
                    $data .= $lineContent;
                }
            }
            else
            {
                if( substr($lineContent, 0, 1) == "\$" )
                {
                    $cleanLine = WHMCS_Utility_LanguageUpdate::correctlineformat($lineContent);
                    $parts = WHMCS_Utility_LanguageUpdate::splitline($cleanLine);
                    $langkey = $parts[0];
                    $old_reporting = error_reporting(0);
                    $trans = eval("return " . $langkey . ';');
                    error_reporting($old_reporting);
                    if( !$trans )
                    {
                        $data .= $cleanLine . "\n";
                    }
                    else
                    {
                        $trans = preg_replace("/\"/", "\\\"", $trans);
                        $data .= $langkey . "= \"" . $trans . "\";" . "\n";
                    }
                }
                else
                {
                    $data .= $lineContent;
                }
            }
        }
        return $data;
    }
}