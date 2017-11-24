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
 * Table Building Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Table
{
    private $fields = array(  );
    private $labelwidth = '20';
    /**
     * Constructor of class
     *
     * @param int $width The width to apply to the field label columns (defaults to 20%)
     *
     * @return WHMCS_Table
     **/
    public function __construct($width = '20')
    {
        $this->labelwidth = $width;
        return $this;
    }
    /**
     * Adds a field to the table
     *
     * @param string $name Field label/name
     * @param string $field Table cell content
     * @param boolean $fullwidth Set true for full width field (ie. single column)
     *
     * @return string Valid HTML Form Element
     **/
    public function add($name, $field, $fullwidth = false)
    {
        if( $fullwidth )
        {
            $fullwidth = true;
        }
        $this->fields[] = array( 'name' => $name, 'field' => $field, 'fullwidth' => $fullwidth );
        return $this;
    }
    /**
     * Builds and returns table output
     *
     * @return string Valid HTML Table Element
     **/
    public function output()
    {
        $code = "<table class=\"form\" width=\"100%\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\"><tr>";
        $i = 0;
        foreach( $this->fields as $k => $v )
        {
            $colspan = '';
            if( $v['fullwidth'] )
            {
                $colspan = '3';
                if( $colspan && $i != 0 )
                {
                    $code .= "</tr><tr>";
                    $i = 0;
                }
                $i++;
            }
            $code .= "<td class=\"fieldlabel\" width=\"" . $this->labelwidth . "%\">" . $v['name'] . "</td><td class=\"fieldarea\"" . ($colspan ? " colspan=\"" . $colspan . "\"" : '') . ">" . $v['field'] . "</td>";
            $i++;
            if( $i == 2 )
            {
                $code .= "</tr><tr>";
                $i = 0;
            }
        }
        $code .= "</tr></table>";
        return $code;
    }
}