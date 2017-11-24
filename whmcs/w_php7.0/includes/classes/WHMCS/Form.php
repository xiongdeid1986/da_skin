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
 * Form Building Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Form
{
    private $frmname = '';
    /**
     * Constructor of class
     *
     * @param string $name Form name
     *
     * @return WHMCS_Form
     **/
    public function __construct($name = 'frm1')
    {
        $this->name($name);
        return $this;
    }
    /**
     * Function to set form name used to handle multiple forms on the same page
     *
     * @param string $name Form name
     *
     * @return WHMCS_Form
     **/
    public function name($name)
    {
        $this->frmname = $name;
        return $this;
    }
    public function getname()
    {
        return $this->frmname;
    }
    /**
     * Opens new form for adding elements to, requires closing later
     *
     * @param string $url URL to submit to (defaults to PHP_SELF)
     * @param boolean $files Set true if this form requires file submissions
     * @param string $target Target for the submission (excluding _ prefix)
     * @param string $method Form post method eg. post, get (defaults to post)
     * @param boolean $nosubmitvar Set true to not include hidden input used in form submission check
     *
     * @return string Valid HTML Form Element
     **/
    public function form($url = '', $files = false, $target = '', $method = 'post', $nosubmitvar = false)
    {
        if( !$url )
        {
            $url = $_SERVER['PHP_SELF'];
        }
        $code = "<form method=\"" . $method . "\" action=\"" . $url . "\" name=\"" . $this->frmname . "\" id=\"" . $this->frmname . "\"";
        if( $files )
        {
            $code .= " enctype=\"multipart/form-data\"";
        }
        if( $target )
        {
            $code .= " target=\"_" . $target . "\"";
        }
        $code .= ">";
        if( !$nosubmitvar )
        {
            $code .= $this->hidden('__fp' . $this->frmname, '1');
        }
        return $code;
    }
    /**
     * Validates form submission by checking for hidden input field and validating token
     *
     * @param boolean $skiptoken Set true to skip token checking for this form submission
     *
     * @return boolean form submit true/false
     **/
    public function issubmitted($skiptoken = false)
    {
        if( isset($_POST['__fp' . $this->frmname]) )
        {
            if( !$skiptoken )
            {
                check_token();
            }
            return true;
        }
        return false;
    }
    /**
     * Generates a text input field
     *
     * @param string $name Field name
     * @param string $value Optional default field value
     * @param int $size Width of the field (defaults to 30)
     * @param boolean $disabled Set true to disable
     * @param string $class Optional class name to apply
     * @param string $type Optional field type attribute
     *
     * @return string Valid HTML Form Element
     **/
    public function text($name, $value = '', $size = '30', $disabled = false, $class = '', $type = 'text')
    {
        $code = "<input type=\"" . $type . "\" name=\"" . $name . "\" value=\"" . $value . "\" size=\"" . $size . "\"";
        if( $disabled )
        {
            $code .= " disabled=\"disabled\"";
        }
        if( $class )
        {
            $code .= " class=\"" . $class . "\"";
        }
        $code .= " />";
        return $code;
    }
    /**
     * Generates a password input field
     *
     * @param string $name Field name
     * @param string $value Optional default field value
     * @param int $size Width of the field (defaults to 30)
     * @param boolean $disabled Set true to disable
     *
     * @return string Valid HTML Form Element
     **/
    public function password($name, $value = '', $size = '30', $disabled = false)
    {
        return $this->text($name, $value, $size, $disabled, '', 'password');
    }
    /**
     * Generates a date text input field
     *
     * @param string $name Field name
     * @param string $value Optional default field value
     * @param int $size Width of the field (defaults to 12)
     * @param boolean $disabled Set true to disable
     *
     * @return string Valid HTML Form Element
     **/
    public function date($name, $value = '', $size = '12', $disabled = false)
    {
        return $this->text($name, $value, $size, $disabled, 'datepick');
    }
    /**
     * Generates a textarea input field
     *
     * @param string $name Field name
     * @param string $value Optional default field value
     * @param int $rows Number of rows (defaults to 3)
     * @param int $cols Number of columns (defaults to 50) supports fixed or percentage
     *
     * @return string Valid HTML Form Element
     **/
    public function textarea($name, $value, $rows = '3', $cols = '50')
    {
        $code = "<textarea name=\"" . $name . "\" rows=\"" . $rows . "\"";
        if( substr($cols, 0 - 1, 1) == "%" )
        {
            $code .= " style=\"width:" . $cols . "\"";
        }
        else
        {
            $code .= " cols=\"" . $cols . "\"";
        }
        $code .= ">" . $value . "</textarea>";
        return $code;
    }
    /**
     * Generates a checkbox input field with optional label
     *
     * @param string $name Field name
     * @param string $label Optional label to follow checkbox
     * @param boolean $checked Set true to check by default
     * @param string $value Field value when checked (defaults to 1)
     *
     * @return string Valid HTML Form Element
     **/
    public function checkbox($name, $label = '', $checked = false, $value = '1', $class = '')
    {
        $code = '';
        if( $label )
        {
            $code .= "<label>";
        }
        $code .= "<input type=\"checkbox\" name=\"" . $name . "\" value=\"" . $value . "\"" . ($checked ? " checked=\"checked\"" : '') . ($class ? " class=\"" . $class . "\"" : '') . " />";
        if( $label )
        {
            $code .= " " . $label . "</label>";
        }
        return $code;
    }
    /**
     * Generates a select dropdown input field
     *
     * @param string $name Field name
     * @param array $values An array of dropdown options
     * @param string $selected Optionally the default selected value
     * @param string $onchange Optional onchange js action
     * @param boolean $anyopt Set true to display any as first option
     * @param boolean $noneopt Set true to display none as first option
     * @param int $size Optional size of select field (defaults to 1)
     *
     * @return string Valid HTML Form Element
     **/
    public function dropdown($name, $values = array(  ), $selected = '', $onchange = '', $anyopt = '', $noneopt = '', $size = '1')
    {
        global $aInt;
        $code = "<select name=\"" . $name . "\" size=\"" . $size . "\"";
        if( $onchange )
        {
            $code .= " onchange=\"" . $onchange . "\"";
        }
        $code .= ">";
        if( $anyopt )
        {
            $code .= "<option value=\"0\">" . $aInt->lang('global', 'any') . "</option>";
        }
        if( $noneopt )
        {
            $code .= "<option value=\"0\">" . $aInt->lang('global', 'none') . "</option>";
        }
        if( is_array($values) )
        {
            foreach( $values as $k => $v )
            {
                $color = '';
                if( is_array($v) )
                {
                    $color = $v[0];
                    $v = $v[1];
                }
                $code .= "<option value=\"" . $k . "\"" . ($k == $selected ? " selected=\"selected\"" : '') . ($color ? " style=\"background-color:" . $color . "\"" : '') . ">" . $v . "</option>";
            }
        }
        else
        {
            $code .= $values;
        }
        $code .= "</select>";
        return $code;
    }
    /**
     * Generates a group of radio input fields
     *
     * @param string $name Field name
     * @param array $values An array of radio button options
     * @param string $selected The option to select by default
     * @param string $spacer Option spacer (defaults to line break)
     *
     * @return string Valid HTML Form Element
     **/
    public function radio($name, $values = array(  ), $selected = '', $spacer = "<br />")
    {
        $code = '';
        foreach( $values as $k => $v )
        {
            $code .= "<label><input type=\"radio\" name=\"" . $name . "\" value=\"" . $k . "\"" . ($k == $selected ? " checked=\"checked\"" : '') . " /> " . $v . "</label>" . $spacer;
        }
        return $code;
    }
    /**
     * Generates a hidden input field
     *
     * @param string $name Field name
     * @param string $value Field value
     *
     * @return string Valid HTML Form Element
     **/
    public function hidden($name, $value)
    {
        $code = "<input type=\"hidden\" name=\"" . $name . "\" value=\"" . $value . "\" />";
        return $code;
    }
    /**
     * Generates a submit button
     *
     * @param string $text Button display text
     * @param string $class Button class (defaults to btn)
     *
     * @return string Valid HTML Form Element
     **/
    public function submit($text, $class = 'btn')
    {
        $code = "<input type=\"submit\" value=\"" . $text . "\" class=\"" . $class . "\" />";
        return $code;
    }
    /**
     * Generates a regular button
     *
     * @param string $text Button display text
     * @param string $onclick Optional javascript onclick action
     * @param string $class Button class (defaults to btn)
     *
     * @return string Valid HTML Form Element
     **/
    public function button($text, $onclick = '', $class = 'btn')
    {
        $code = "<input type=\"button\" value=\"" . $text . "\" class=\"" . $class . "\"" . ($onclick ? " onclick=\"" . $onclick . "\"" : '') . " />";
        return $code;
    }
    /**
     * Generates a reset button
     *
     * @param string $text Button display text
     * @param string $class Button class (defaults to btn)
     *
     * @return string Valid HTML Form Element
     **/
    public function reset($text, $class = 'btn')
    {
        $code = "<input type=\"reset\" value=\"" . $text . "\" class=\"" . $class . "\" />";
        return $code;
    }
    public function savereset()
    {
        global $aInt;
        $code = "<p align=\"center\">" . $this->submit($aInt->lang('global', 'savechanges'), "btn btn-primary") . " " . $this->reset($aInt->lang('global', 'cancelchanges')) . "</p>";
        return $code;
    }
    /**
     * Closes form
     *
     * @return string Valid HTML Form Element
     **/
    public function close()
    {
        $code = "</form>";
        return $code;
    }
}