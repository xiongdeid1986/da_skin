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
class Plesk_Translate
{
    private $_keys = array(  );
    public function __construct()
    {
        $dir = realpath(dirname(__FILE__) . "/../../lang");
        $englishFile = $dir . "/english.php";
        $currentFile = $dir . '/' . $this->_getLanguage() . ".php";
        if( file_exists($englishFile) )
        {
            require_once($englishFile);
            $this->_keys = $keys;
        }
        if( file_exists($currentFile) )
        {
            require_once($currentFile);
            $this->_keys = array_merge($this->_keys, $keys);
        }
    }
    public function translate($msg, $placeholders = array(  ))
    {
        if( isset($this->_keys[$msg]) )
        {
            $msg = $this->_keys[$msg];
            foreach( $placeholders as $key => $val )
            {
                $msg = str_replace("@" . $key . "@", $val, $msg);
            }
        }
        return $msg;
    }
    private function _getLanguage()
    {
        $language = 'english';
        if( isset($GLOBALS['CONFIG']['Language']) )
        {
            $language = $GLOBALS['CONFIG']['Language'];
        }
        if( isset($_SESSION['adminid']) )
        {
            $language = $this->_getUserLanguage('tbladmins', 'adminid');
        }
        else
        {
            if( $_SESSION['uid'] )
            {
                $language = $this->_getUserLanguage('tblclients', 'uid');
            }
        }
        return strtolower($language);
    }
    private function _getUserLanguage($table, $field)
    {
        $sqlresult = select_query($table, 'language', array( 'id' => mysql_real_escape_string($_SESSION[$field]) ));
        if( $data = mysql_fetch_row($sqlresult) )
        {
            return reset($data);
        }
        return '';
    }
}