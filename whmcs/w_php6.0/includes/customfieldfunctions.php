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
function getCustomFields($type, $relid, $relid2, $admin = '', $order = '', $ordervalues = '', $hidepw = '')
{
    $customfields = $where = array(  );
    $where['type'] = $type;
    if( $relid )
    {
        $where['relid'] = $relid;
    }
    if( !$admin )
    {
        $where['adminonly'] = '';
    }
    if( $order )
    {
        $where['showorder'] = 'on';
    }
    $result = select_query('tblcustomfields', '', $where, "sortorder` ASC,`id", 'ASC');
    while( $data = mysql_fetch_array($result) )
    {
        $id = $data['id'];
        $fieldname = $data['fieldname'];
        if( strpos($fieldname, "|") )
        {
            $fieldname = explode("|", $fieldname);
            $fieldname = trim($fieldname[1]);
        }
        $fieldtype = $data['fieldtype'];
        $description = $data['description'];
        $fieldoptions = $data['fieldoptions'];
        $required = $data['required'];
        $adminonly = $data['adminonly'];
        $customfieldval = is_array($ordervalues) ? $ordervalues[$id] : '';
        if( $relid2 )
        {
            $customfieldval = get_query_val('tblcustomfieldsvalues', 'value', array( 'fieldid' => $id, 'relid' => $relid2 ));
            $fieldloadhooks = run_hook('CustomFieldLoad', array( 'fieldid' => $id, 'relid' => $relid2, 'value' => $customfieldval ));
            if( 0 < count($fieldloadhooks) )
            {
                $fieldloadhookslast = array_pop($fieldloadhooks);
                if( array_key_exists('value', $fieldloadhookslast) )
                {
                    $customfieldval = $fieldloadhookslast['value'];
                }
            }
        }
        $rawvalue = $customfieldval;
        $customfieldval = WHMCS_Input_Sanitize::makesafeforoutput($customfieldval);
        if( $required == 'on' )
        {
            $required = "*";
        }
        if( $fieldtype == 'text' || $fieldtype == 'password' && $admin )
        {
            $input = "<input type=\"text\" name=\"customfield[" . $id . "]" . "\" id=\"customfield" . $id . "\" value=\"" . $customfieldval . "\" size=\"30\" />";
        }
        else
        {
            if( $fieldtype == 'link' )
            {
                $webaddr = trim($customfieldval);
                if( substr($webaddr, 0, 4) == "www." )
                {
                    $webaddr = "http://" . $webaddr;
                }
                $input = "<input type=\"text\" name=\"customfield[" . $id . "]" . "\" id=\"customfield" . $id . "\" value=\"" . $customfieldval . "\" size=\"40\" /> " . ($customfieldval ? "<a href=\"" . $webaddr . "\" target=\"_blank\">www</a>" : '');
                $customfieldval = "<a href=\"" . $webaddr . "\" target=\"_blank\">" . $customfieldval . "</a>";
            }
            else
            {
                if( $fieldtype == 'password' )
                {
                    $input = "<input type=\"password\" name=\"customfield[" . $id . "]" . "\" id=\"customfield" . $id . "\" value=\"" . $customfieldval . "\" size=\"30\" />";
                    if( $hidepw )
                    {
                        $pwlen = strlen($customfieldval);
                        $customfieldval = '';
                        for( $i = 1; $i <= $pwlen; $i++ )
                        {
                            $customfieldval .= "*";
                        }
                    }
                }
                else
                {
                    if( $fieldtype == 'textarea' )
                    {
                        $input = "<textarea name=\"customfield[" . $id . "]" . "\" id=\"customfield" . $id . "\" rows=\"3\" style=\"width:90%;\">" . $customfieldval . "</textarea>";
                    }
                    else
                    {
                        if( $fieldtype == 'dropdown' )
                        {
                            $input = "<select name=\"customfield[" . $id . "]" . "\" id=\"customfield" . $id . "\">";
                            $fieldoptions = explode(',', $fieldoptions);
                            foreach( $fieldoptions as $optionvalue )
                            {
                                $input .= "<option value=\"" . $optionvalue . "\"";
                                if( $customfieldval == $optionvalue )
                                {
                                    $input .= " selected";
                                }
                                if( strpos($optionvalue, "|") )
                                {
                                    $optionvalue = explode("|", $optionvalue);
                                    $optionvalue = trim($optionvalue[1]);
                                }
                                $input .= ">" . $optionvalue . "</option>";
                            }
                            $input .= "</select>";
                        }
                        else
                        {
                            if( $fieldtype == 'tickbox' )
                            {
                                $input = "<input type=\"checkbox\" name=\"customfield[" . $id . "]" . "\" id=\"customfield" . $id . "\"";
                                if( $customfieldval == 'on' )
                                {
                                    $input .= " checked";
                                }
                                $input .= " />";
                            }
                        }
                    }
                }
            }
        }
        if( $fieldtype != 'link' && strpos($customfieldval, "|") )
        {
            $customfieldval = explode("|", $customfieldval);
            $customfieldval = trim($customfieldval[1]);
        }
        $customfields[] = array( 'id' => $id, 'name' => $fieldname, 'description' => $description, 'type' => $fieldtype, 'input' => $input, 'value' => $customfieldval, 'rawvalue' => $rawvalue, 'required' => $required, 'adminonly' => $adminonly );
    }
    return $customfields;
}
function saveCustomFields($relid, $customfields, $type = '')
{
    if( is_array($customfields) )
    {
        foreach( $customfields as $id => $value )
        {
            if( !is_int($id) && !empty($id) )
            {
                $where = array( 'fieldname' => $id );
                $result = select_query('tblcustomfields', 'id', $where);
                $data = mysql_fetch_array($result);
                $num_rows = mysql_num_rows($result);
                if( empty($data['id']) || 1 < $num_rows )
                {
                    continue;
                }
                $id = $data['id'];
            }
            if( $type )
            {
                $where = array( 'id' => $id, 'type' => $type );
                $result = select_query('tblcustomfields', '', $where);
                $data = mysql_fetch_array($result);
                if( !$data['id'] )
                {
                    continue;
                }
            }
            $fieldsavehooks = run_hook('CustomFieldSave', array( 'fieldid' => $id, 'relid' => $relid, 'value' => $value ));
            if( 0 < count($fieldsavehooks) )
            {
                $fieldsavehookslast = array_pop($fieldsavehooks);
                if( array_key_exists('value', $fieldsavehookslast) )
                {
                    $value = $fieldsavehookslast['value'];
                }
            }
            $result = select_query('tblcustomfieldsvalues', '', array( 'fieldid' => $id, 'relid' => $relid ));
            $num_rows = mysql_num_rows($result);
            if( $num_rows == '0' )
            {
                insert_query('tblcustomfieldsvalues', array( 'fieldid' => $id, 'relid' => $relid, 'value' => $value ));
            }
            else
            {
                update_query('tblcustomfieldsvalues', array( 'value' => $value ), array( 'fieldid' => $id, 'relid' => $relid ));
            }
        }
    }
}
function migrateCustomFields($itemType, $itemID, $newRelID)
{
    if( $itemType == 'product' )
    {
        $existingRelID = get_query_val('tblhosting', 'packageid', array( 'id' => $itemID ));
    }
    else
    {
        if( $itemType == 'support' )
        {
            $existingRelID = get_query_val('tbltickets', 'did', array( 'id' => $itemID ));
        }
        else
        {
            $existingRelID = 0;
        }
    }
    if( !$existingRelID || $existingRelID == $newRelID )
    {
        return false;
    }
    $customfields = getcustomfields($itemType, $existingRelID, $itemID, true);
    $dataArr = array(  );
    foreach( $customfields as $v )
    {
        $cfid = $v['id'];
        $cfname = $v['name'];
        $cfval = $v['rawvalue'];
        $dataArr[$cfname] = $cfval;
        delete_query('tblcustomfieldsvalues', array( 'fieldid' => $cfid, 'relid' => $itemID ));
    }
    $customfields = getcustomfields($itemType, $newRelID, '', true);
    foreach( $customfields as $v )
    {
        $cfid = $v['id'];
        $cfname = $v['name'];
        if( isset($dataArr[$cfname]) )
        {
            insert_query('tblcustomfieldsvalues', array( 'fieldid' => $cfid, 'relid' => $itemID, 'value' => $dataArr[$cfname] ));
        }
    }
}
function migrateCustomFieldsBetweenProducts($serviceid, $newpid, $save = false)
{
    if( $save )
    {
        $existingpid = get_query_val('tblhosting', 'packageid', array( 'id' => $serviceid ));
        $customfields = getcustomfields('product', $existingpid, $serviceid, true);
        foreach( $customfields as $v )
        {
            $k = $v['id'];
            $customfieldsarray[$k] = $_POST['customfield'][$k];
        }
        savecustomfields($serviceid, $customfieldsarray);
    }
    migratecustomfields('product', $serviceid, $newpid);
}