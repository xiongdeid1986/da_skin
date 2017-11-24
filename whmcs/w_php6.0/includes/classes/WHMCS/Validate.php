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
 * Validation Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2014
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Validate
{
    protected $optionalFields = array(  );
    protected $validated = array(  );
    protected $errors = array(  );
    protected $errorMessages = array(  );
    /**
     * Specify optional fields to override required checks
     *
     * @param string|string[] $optionalFields Accepts either an array or comma separated list of optional fields
     *
     * @return WHMCS_Validate
     */
    public function setOptionalFields($optionalFields)
    {
        if( !is_array($optionalFields) )
        {
            $optionalFields = explode(',', $optionalFields);
        }
        $this->optionalFields = array_merge($this->optionalFields, $optionalFields);
        return $this;
    }
    /**
     * Add a validation rule for a given field
     *
     * @param string $rule One of the defined validation rules
     * @param string $field The field name to run the rule against
     * @param string $languageKey The language var name to use for error on failure
     * @param string|array $field2 The second field needed by some rules (or an array for certain rules)
     * @param string $value The value of field that was passed in
     *
     * @return boolean True or false depending on pass or fail of rule
     */
    public function validate($rule, $field, $languageKey, $field2 = '', $value = null)
    {
        if( in_array($field, $this->optionalFields) )
        {
            return false;
        }
        if( $this->runRule($rule, $field, $field2, $value) )
        {
            $this->validated[] = $field;
            return true;
        }
        $this->errors[] = $field;
        $this->addError($languageKey);
        return false;
    }
    /**
     * This function will load custom fields and perform validation rules as per custom field config
     *
     * @param string $type Type of custom field to validate
     * @param int $relid Optional ID the type relates to - product ID or support department ID
     * @param boolean $order Set true if in the order process to validate fields that only show on sign-up
     * @param array $customFields Custom fields passed through an API call.
     *
     * @return true
     */
    public function validateCustomFields($type, $relid, $order = false, $customFields = array(  ))
    {
        $whmcs = WHMCS_Application::getinstance();
        $where = array( 'type' => $type, 'adminonly' => '' );
        if( $relid )
        {
            $where['relid'] = (int) $relid;
        }
        if( $order )
        {
            $where['showorder'] = 'on';
        }
        $result = select_query('tblcustomfields', 'id,fieldname,fieldtype,fieldoptions,required,regexpr', $where, "sortorder` ASC,`id", 'ASC');
        while( $data = mysql_fetch_array($result) )
        {
            $fieldId = $data['id'];
            $fieldName = $data['fieldname'];
            $fieldOptions = $data['fieldoptions'];
            $required = $data['required'];
            $regularExpression = $data['regexpr'];
            if( strpos($fieldName, "|") )
            {
                $fieldName = explode("|", $fieldName);
                $fieldName = trim($fieldName[1]);
            }
            $value = isset($customFields[$fieldName]) ? $customFields[$fieldName] : null;
            if( is_null($value) )
            {
                $value = isset($customFields[$fieldId]) ? $customFields[$fieldId] : null;
            }
            switch( $fieldName )
            {
                case 'link':
                    $this->validate('url', "customfield[" . $fieldId . "]", $fieldName . " is an Invalid URL", '', $value);
                    break;
                case 'dropdown':
                    $this->validate('inarray', "customfield[" . $fieldId . "]", $fieldName . " Invalid Select Option", explode(',', $fieldOptions), $value);
                    break;
                case 'tickbox':
                    $this->validate('inarray', "customfield[" . $fieldId . "]", $fieldName . " Invalid Value", array( 'on', '1', '' ), $value);
            }
            if( $required )
            {
                $this->validate('required', "customfield[" . $fieldId . "]", $fieldName . " " . $whmcs->get_lang('clientareaerrorisrequired'), '', $value);
            }
            if( $regularExpression && trim($whmcs->get_req_var('customfield', $fieldId)) )
            {
                $this->validate('matchpattern', "customfield[" . $fieldId . "]", $fieldName . " " . $whmcs->get_lang('customfieldvalidationerror'), array( $regularExpression ), $value);
            }
            break;
        }
        return true;
    }
    /**
     * This function actually performs the requested validation rule
     *
     * @param string $rule The rule name to execute
     * @param string $field The field name to run the rule against
     * @param string|array $field2 The optional second field required by some rules (or an array for certain rules)
     * @param string $val The value of field that was passed in
     *
     * @return boolean True or false depending upon the result of the rule
     */
    protected function runRule($rule, $field, $field2, $val = null)
    {
        $whmcs = WHMCS_Application::getinstance();
        if( is_null($val) )
        {
            if( strpos($field, "[") )
            {
                $k1 = explode("[", $field);
                $k2 = explode("]", $k1[1]);
                $val = $whmcs->get_req_var($k1[0], $k2[0]);
            }
            else
            {
                $val = $whmcs->get_req_var($field);
            }
        }
        $val2 = is_array($field2) ? null : $whmcs->get_req_var($field2);
        if( in_array($field, $this->optionalFields) )
        {
            return true;
        }
        switch( $rule )
        {
            case 'required':
                return !trim($val) ? false : true;
                break;
            case 'numeric':
                return is_numeric($val);
                break;
            case 'match_value':
                if( is_array($field2) )
                {
                    return $field2[0] === $field2[1];
                }
                return $val === $val2;
                break;
            case 'matchpattern':
                return preg_match($field2[0], $val);
                break;
            case 'email':
                if( function_exists('filter_var') )
                {
                    return filter_var($val, FILTER_VALIDATE_EMAIL);
                }
                return preg_match("/^([a-zA-Z0-9&'.])+([\\.a-zA-Z0-9+_-])*@([a-zA-Z0-9_-])+(\\.[a-zA-Z0-9_-]+)*\\.([a-zA-Z]{2,6})\$/", $val);
                break;
            case 'postcode':
                return !preg_replace("/[a-zA-Z0-9 \\-]/", '', $val);
                break;
            case 'phone':
                return !preg_replace("/[0-9 .\\-()]/", '', $val);
                break;
            case 'country':
                if( preg_replace("/[A-Z]/", '', $val) )
                {
                    return false;
                }
                if( strlen($val) != 2 )
                {
                    return false;
                }
                return true;
                break;
            case 'url':
                return preg_match("|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?\$|i", $val);
                break;
            case 'inarray':
                return in_array($val, $field2);
                break;
            case 'banneddomain':
                if( strpos($val, "@") )
                {
                    $val = explode("@", $val, 2);
                    $val = $val[1];
                }
                return get_query_val('tblbannedemails', "COUNT(id)", array( 'domain' => $val )) ? false : true;
                break;
            case 'uniqueemail':
                $where = array( 'email' => $val );
                if( is_array($field2) && 0 < $field2[0] )
                {
                    $where['id'] = array( 'sqltype' => 'NEQ', 'value' => $field2[0] );
                }
                $clientExists = get_query_val('tblclients', "COUNT(id)", $where);
                if( $clientExists )
                {
                    return false;
                }
                $where = array( 'subaccount' => '1', 'email' => $val );
                if( is_array($field2) && 0 < $field2[1] )
                {
                    $where['id'] = array( 'sqltype' => 'NEQ', 'value' => $field2[1] );
                }
                $subAccountExists = get_query_val('tblcontacts', "COUNT(id)", $where);
                if( $subAccountExists )
                {
                    return false;
                }
                return true;
                break;
            case 'pwstrength':
                $requiredPasswordStrength = $whmcs->get_config('RequiredPWStrength');
                if( !$requiredPasswordStrength )
                {
                    return true;
                }
                $passwordStrength = $this->calcPasswordStrength($val);
                if( $passwordStrength <= $requiredPasswordStrength )
                {
                    return false;
                }
                return true;
                break;
            case 'captcha':
                $captcha = $whmcs->get_config('CaptchaSetting');
                if( !$captcha )
                {
                    return true;
                }
                if( $captcha == 'offloggedin' && isset($_SESSION['uid']) )
                {
                    return true;
                }
                return $this->checkCaptchaInput($val);
                break;
            case 'fileuploads':
                return $this->checkUploadExtensions($field);
                break;
        }
        return false;
    }
    /**
     * Checks the extensions of uploaded files against the allowed file types
     *
     * @param string $field The file upload field name to be checked
     *
     * @return boolean False if any file extension is not on the allow list
     */
    protected function checkUploadExtensions($field)
    {
        $whmcs = WHMCS_Application::getinstance();
        if( $_FILES[$field]['name'][0] == '' )
        {
            return true;
        }
        $ext_array = $whmcs->get_config('TicketAllowedFileTypes');
        $ext_array = explode(',', trim($ext_array));
        if( !count($ext_array) )
        {
            return false;
        }
        foreach( $_FILES[$field]['name'] as $filename )
        {
            $filename = trim($filename);
            if( $filename )
            {
                $filename = preg_replace("/[^a-zA-Z0-9-_. ]/", '', $filename);
                $parts = explode(".", $filename);
                $extension = "." . strtolower(end($parts));
                foreach( $ext_array as $value )
                {
                    if( trim($value) == $extension )
                    {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    /**
     * Validates captcha field input
     *
     * @param string $val The captcha code input
     *
     * @return boolean False if the captcha check fails verification
     */
    protected function checkCaptchaInput($val)
    {
        $whmcs = WHMCS_Application::getinstance();
        $captchaType = $whmcs->get_config('CaptchaType');
        if( $captchaType == 'recaptcha' )
        {
            if( !function_exists('recaptcha_check_answer') )
            {
                require(ROOTDIR . "/includes/recaptchalib.php");
            }
            $resp = recaptcha_check_answer($whmcs->get_config('ReCAPTCHAPrivateKey'), WHMCS_Utility_Environment_CurrentUser::getip(), $whmcs->get_req_var('recaptcha_challenge_field'), $whmcs->get_req_var('recaptcha_response_field'));
            if( !is_object($resp) )
            {
                return false;
            }
            if( !$resp->is_valid )
            {
                return false;
            }
        }
        else
        {
            if( $_SESSION['captchaValue'] != md5(strtoupper($val)) )
            {
                generateNewCaptchaCode();
                return false;
            }
        }
        generateNewCaptchaCode();
        return true;
    }
    /**
     * Calculates password strength
     *
     * @param string $password The user input password
     *
     * @return int Password strength
     */
    protected function calcPasswordStrength($password)
    {
        $length = strlen($password);
        $calculatedLength = $length;
        if( 5 < $length )
        {
            $calculatedLength = 5;
        }
        $numbers = preg_replace("/[^0-9]/", '', $password);
        $numericCount = strlen($numbers);
        if( 3 < $numericCount )
        {
            $numericCount = 3;
        }
        $symbols = preg_replace("/[^A-Za-z0-9]/", '', $password);
        $symbolCount = $length - strlen($symbols);
        if( $symbolCount < 0 )
        {
            $symbolCount = 0;
        }
        if( 3 < $symbolCount )
        {
            $symbolCount = 3;
        }
        $uppercase = preg_replace("/[^A-Z]/", '', $password);
        $uppercaseCount = $length - strlen($uppercase);
        if( $uppercaseCount < 0 )
        {
            $uppercaseCount = 0;
        }
        if( 3 < $uppercaseCount )
        {
            $uppercaseCount = 3;
        }
        $strength = $calculatedLength * 10 - 20 + $numericCount * 10 + $symbolCount * 15 + $uppercaseCount * 10;
        return $strength;
    }
    /**
     * Adds an error to the error messages array
     *
     * @param string|array $var Either a client area language file string, or an admin area language file array reference
     *
     * @return true
     */
    public function addError($var)
    {
        global $_LANG;
        global $aInt;
        if( defined('ADMINAREA') )
        {
            if( is_array($var) )
            {
                $this->errorMessages[] = $aInt->lang($var[0], $var[1]);
            }
            else
            {
                $this->errorMessages[] = $var;
            }
        }
        else
        {
            $this->errorMessages[] = array_key_exists($var, $_LANG) ? $_LANG[$var] : $var;
        }
        return true;
    }
    /**
     * Adds an array of errors to the error messages array
     *
     * @param array $errors An array of error messages
     *
     * @return boolean true
     */
    public function addErrors($errors = array(  ))
    {
        foreach( $errors as $error )
        {
            $this->addError($error);
        }
        return true;
    }
    public function validated($field)
    {
        if( $field )
        {
            return in_array($field, $this->validated);
        }
        return $this->validated;
    }
    public function error($field)
    {
        if( $field )
        {
            return in_array($field, $this->errors);
        }
        return $this->errors;
    }
    /**
     * Returns an array of error messages currently in memory
     *
     * @return array Error Messages
     */
    public function getErrors()
    {
        return $this->errorMessages;
    }
    /**
     * Returns the number of error messages currently in memory
     *
     * @return int Number of Errors
     */
    public function hasErrors()
    {
        return count($this->getErrors());
    }
    /**
     * Returns an HTML formatted list of error messages currently in memory
     *
     * @return string HTML Formatted Error Message Output
     */
    public function getHTMLErrorOutput()
    {
        $code = '';
        foreach( $this->getErrors() as $errorMessage )
        {
            $code .= "<li>" . $errorMessage . "</li>";
        }
        return $code;
    }
}