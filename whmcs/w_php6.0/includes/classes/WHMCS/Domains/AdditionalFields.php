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
 * WHMCS Domains Additional Fields Handling Class
 *
 * Additional fields as referred to here are the TLD specific fields
 * required by individual domain registries to register certain TLDs.
 * They are defined on a per TLD basis, and can be customised by end
 * users via the definitions file: includes/additionaldomainfields.php
 *
 * This class handles the reading of the field definitions, interpreting
 * of them, HTML output generation, validation during the order process
 * and saving/retrieving of values both on the order form and within the
 * admin area.
 *
 * The class is designed to be instantiated on a per domain basis.  The
 * active TLD property defines which fields are being worked with in the
 * instance.
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2014
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Domains_AdditionalFields
{
    protected $fieldsData = array(  );
    protected $activeTLD = '';
    protected $activeTLDData = array(  );
    protected $activeTLDValues = array(  );
    /**
     * Load Additional Field Data
     */
    protected function loadFieldsData()
    {
        global $_LANG;
        $additionaldomainfields = null;
        require(ROOTDIR . "/includes/additionaldomainfields.php");
        if( is_array($additionaldomainfields) )
        {
            $this->fieldsData = $additionaldomainfields;
        }
    }
    /**
     * Set TLD based on a full domain name eg. domain.com
     *
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $domainparts = explode(".", $domain, 2);
        $this->setTLD($domainparts[1]);
    }
    /**
     * Set TLD passing in just a TLD
     *
     * @param string $tld
     */
    public function setTLD($tld)
    {
        if( substr($tld, 0, 1) != "." )
        {
            $tld = "." . $tld;
        }
        $this->activeTLD = $tld;
    }
    /**
     * Get TLD
     *
     * @return string
     */
    public function getTLD()
    {
        return $this->activeTLD;
    }
    /**
     * Get fields for the currently set TLD
     *
     * @return array
     */
    public function getFields()
    {
        $this->loadFieldsData();
        if( array_key_exists($this->getTLD(), $this->fieldsData) )
        {
            $fieldData = $this->fieldsData[$this->getTLD()];
            $this->activeTLDData = $fieldData;
            return $fieldData;
        }
        return array(  );
    }
    /**
     * Get Field Configuration Value if set
     *
     * @param int $fieldKey The array key of the field to fetch config for
     * @param string $name The name of the config value to fetch
     *
     * @return string
     */
    protected function getConfigValue($fieldKey, $name)
    {
        return array_key_exists($name, $this->activeTLDData[$fieldKey]) ? $this->activeTLDData[$fieldKey][$name] : '';
    }
    /**
     * Get the field name using language var if available, otherwise display name, otherwise key name
     *
     * @param int $fieldKey The array key of the field to get the name of
     *
     * @return string
     */
    protected function getFieldName($fieldKey)
    {
        global $_LANG;
        $langvar = $this->getConfigValue($fieldKey, 'LangVar');
        $displayname = $this->getConfigValue($fieldKey, 'DisplayName');
        if( $langvar && isset($_LANG[$langvar]) )
        {
            return $_LANG[$langvar];
        }
        if( $displayname )
        {
            return $displayname;
        }
        return $this->getConfigValue($fieldKey, 'Name');
    }
    /**
     * Set Field Values by passing in an array of key=>name pairs
     *
     * @param array $values
     */
    public function setFieldValues($values)
    {
        if( is_array($values) )
        {
            $this->activeTLDValues = $values;
        }
    }
    /**
     * Get Field Value based on array key first, or a field name
     *
     * @param mixed $fieldKey Either the array key of the field or the name
     *
     * @return string
     */
    protected function getFieldValue($fieldKey)
    {
        $val = array_key_exists($fieldKey, $this->activeTLDValues) ? $this->activeTLDValues[$fieldKey] : '';
        if( $val === '' )
        {
            $name = $this->getConfigValue($fieldKey, 'Name');
            $val = array_key_exists($name, $this->activeTLDValues) ? $this->activeTLDValues[$name] : '';
        }
        return $val;
    }
    /**
     * Process Fields Ready For Output
     *
     * Reads the configuration values for the fields and builds the HTML output needed to render them for display
     *
     * @param integer $domainKey The array key to use if rendering multiple fields on same page (optional)
     *
     * @return array An array of field names and HTML field output
     */
    public function getFieldsForOutput($domainKey = '')
    {
        global $_LANG;
        $domainKey = is_numeric($domainKey) ? "[" . $domainKey . "]" : '';
        $domainfields = array(  );
        foreach( $this->getFields() as $fieldKey => $values )
        {
            $type = $this->getConfigValue($fieldKey, 'Type');
            $size = $this->getConfigValue($fieldKey, 'Size');
            $options = $this->getConfigValue($fieldKey, 'Options');
            $required = $this->getConfigValue($fieldKey, 'Required');
            $defaultval = $this->getConfigValue($fieldKey, 'Default');
            if( $this->getFieldValue($fieldKey) !== '' )
            {
                $defaultval = $this->getFieldValue($fieldKey);
            }
            $input = $this->genFieldHTML('domainfield' . $domainKey . "[" . $fieldKey . "]", $type, $size, $options, $defaultval, $required);
            $desc = $this->getConfigValue($fieldKey, 'Description');
            if( $desc )
            {
                $input .= " " . $desc;
            }
            $domainfields[$this->getFieldName($fieldKey)] = $input;
        }
        return $domainfields;
    }
    /**
     * Generate Individual Field HTML
     *
     * @param string $name
     * @param string $type
     * @param int $size
     * @param array $options
     * @param string $defaultval
     * @param bool $required
     *
     * @return string The HTML for the input field
     */
    protected function genFieldHTML($name, $type, $size, $options, $defaultval, $required)
    {
        if( $type == 'dropdown' || $type == 'radio' )
        {
            $fieldoptions = array(  );
            $tmpoptions = explode(',', $options);
            foreach( $tmpoptions as $optionvalue )
            {
                $opkey = $opvalue = $optionvalue;
                if( strpos($opkey, "|") )
                {
                    $opkey = explode("|", $opkey, 2);
                    $opvalue = trim($opkey[1]);
                    $opkey = trim($opkey[0]);
                    if( !$opvalue )
                    {
                        $opvalue = $opkey;
                    }
                }
                $fieldoptions[$opkey] = $opvalue;
            }
        }
        $frm = new WHMCS_Form();
        if( $type == 'text' )
        {
            $input = $frm->text($name, $defaultval, $size);
            if( $required )
            {
                $input .= " *";
            }
        }
        else
        {
            if( $type == 'dropdown' )
            {
                $input = $frm->dropdown($name, $fieldoptions, $defaultval);
            }
            else
            {
                if( $type == 'tickbox' )
                {
                    $input = $frm->checkbox($name, '', $defaultval, 'on');
                }
                else
                {
                    if( $type == 'radio' )
                    {
                        $input = $frm->radio($name, $fieldoptions, $defaultval);
                    }
                    else
                    {
                        if( $type == 'display' )
                        {
                            $input = "<p>" . $defaultval . "</p>";
                        }
                    }
                }
            }
        }
        return $input;
    }
    /**
     * Get Missing Required Fields
     *
     * @return array An array of field names that are missing
     */
    public function getMissingRequiredFields()
    {
        $missingfields = array(  );
        foreach( $this->getFields() as $fieldKey => $values )
        {
            if( $this->getConfigValue($fieldKey, 'Required') && !$this->getFieldValue($fieldKey) )
            {
                $missingfields[] = $this->getFieldName($fieldKey);
            }
        }
        return $missingfields;
    }
    /**
     * Determine if any required fields are missing
     *
     * @return bool
     */
    public function isMissingRequiredFields()
    {
        return count($this->getMissingRequiredFields()) ? true : false;
    }
    /**
     * Load & return field values for a given domain from the database
     *
     * @param int $domainID The domain ID to retrieve
     *
     * @return array
     */
    public function getFieldValuesFromDatabase($domainID)
    {
        $values = array(  );
        $result = select_query('tbldomainsadditionalfields', 'name,value', array( 'domainid' => $domainID ));
        while( $data = mysql_fetch_array($result) )
        {
            $values[$data['name']] = $data['value'];
        }
        $this->setFieldValues($values);
        return $values;
    }
    /**
     * Save the currently set field values to the database
     *
     * @param int $domainID The domain ID to update
     */
    public function saveToDatabase($domainID)
    {
        foreach( $this->getFields() as $fieldKey => $values )
        {
            $name = $this->getConfigValue($fieldKey, 'Name');
            $value = $this->getFieldValue($fieldKey);
            $table = 'tbldomainsadditionalfields';
            $where = array( 'domainid' => (int) $domainID, 'name' => $name );
            $exists = get_query_val($table, "COUNT(*)", $where);
            if( $exists )
            {
                update_query($table, array( 'value' => $value ), $where);
            }
            else
            {
                insert_query($table, array( 'domainid' => (int) $domainID, 'name' => $name, 'value' => $value ));
            }
        }
    }
}