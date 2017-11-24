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
$query_count = 0;
function select_query($table, $fields, $where, $orderby = '', $orderbyorder = '', $limit = '', $innerjoin = '', $handle = null)
{
    global $CONFIG;
    global $query_count;
    global $mysql_errors;
    if( !is_resource($handle) )
    {
        $handle = WHMCS_Application::getinstance()->getDatabaseObj()->retrieveDatabaseConnection();
    }
    if( !$fields )
    {
        $fields = "*";
    }
    $query = "SELECT " . $fields . " FROM " . db_make_safe_field($table);
    if( $innerjoin )
    {
        $query .= " INNER JOIN " . db_escape_string($innerjoin) . '';
    }
    if( $where )
    {
        if( is_array($where) )
        {
            $criteria = array(  );
            foreach( $where as $key => $value )
            {
                if( is_array($value) )
                {
                    $key = db_build_quoted_field($key);
                    if( $value['sqltype'] == 'LIKE' )
                    {
                        $criteria[] = $key . " LIKE '%" . db_escape_string($value['value']) . "%'";
                    }
                    else
                    {
                        if( $value['sqltype'] == 'NEQ' )
                        {
                            $criteria[] = $key . "!='" . db_escape_string($value['value']) . "'";
                        }
                        else
                        {
                            if( $value['sqltype'] == ">" && db_is_valid_amount($value['value']) )
                            {
                                $criteria[] = $key . ">" . db_escape_string($value['value']) . '';
                            }
                            else
                            {
                                if( $value['sqltype'] == "<" && db_is_valid_amount($value['value']) )
                                {
                                    $criteria[] = $key . "<" . db_escape_string($value['value']) . '';
                                }
                                else
                                {
                                    if( $value['sqltype'] == "<=" && db_is_valid_amount($value['value']) )
                                    {
                                        $criteria[] = $key . "<=" . db_escape_string($value['value']) . '';
                                    }
                                    else
                                    {
                                        if( $value['sqltype'] == ">=" && db_is_valid_amount($value['value']) )
                                        {
                                            $criteria[] = $key . ">=" . db_escape_string($value['value']) . '';
                                        }
                                        else
                                        {
                                            if( $value['sqltype'] == 'TABLEJOIN' )
                                            {
                                                $criteria[] = $key . "=" . db_escape_string($value['value']) . '';
                                            }
                                            else
                                            {
                                                if( $value['sqltype'] == 'IN' )
                                                {
                                                    $criteria[] = $key . " IN (" . db_build_in_array($value['values']) . ")";
                                                }
                                                else
                                                {
                                                    throw new WHMCS_Exception_Fatal("Invalid input condition");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    if( substr($key, 0, 3) == 'MD5' )
                    {
                        $key = explode("(", $key, 2);
                        $key = explode(")", $key[1], 2);
                        $key = db_make_safe_field($key[0]);
                        $key = "MD5(" . $key . ")";
                    }
                    else
                    {
                        $key = db_build_quoted_field($key);
                    }
                    $criteria[] = $key . "='" . db_escape_string($value) . "'";
                }
            }
            $query .= " WHERE " . implode(" AND ", $criteria);
        }
        else
        {
            $query .= " WHERE " . $where;
        }
    }
    if( $orderby )
    {
        $orderbysql = tokenizeOrderby($orderby, $orderbyorder);
        $query .= " ORDER BY " . implode(',', $orderbysql);
    }
    if( $limit )
    {
        if( strpos($limit, ',') )
        {
            $limit = explode(',', $limit);
            $limit = (int) $limit[0] . ',' . (int) $limit[1];
        }
        else
        {
            $limit = (int) $limit;
        }
        $query .= " LIMIT " . $limit;
    }
    $result = full_query($query, $handle);
    if( !$result && ($CONFIG['SQLErrorReporting'] || $mysql_errors) )
    {
        logActivity("SQL Error: " . mysql_error($handle) . " - Full Query: " . $query);
    }
    $query_count++;
    return $result;
}
function tokenizeOrderby($input, $default_ordering = 'ASC')
{
    $field_separator = ',';
    $field_begin = "`";
    $field_end = "`";
    $seg_qualifier = ".";
    $qualifier = $field_end . $seg_qualifier . $field_begin;
    $order_up_rev = "CSA ";
    $order_down_rev = "CSED ";
    if( $default_ordering )
    {
        $default_ordering = trim($default_ordering);
    }
    else
    {
        $default_ordering = 'ASC';
    }
    $default_ordering_rev = strrev(" " . $default_ordering);
    if( $default_ordering_rev != $order_up_rev && $default_ordering_rev != $order_down_rev )
    {
        $default_ordering_rev = $order_up_rev;
    }
    $tokenizedFields = array(  );
    $i = 0;
    for( $field = strtok($input, $field_separator); $i < 30 && $field !== false; $i++ )
    {
        $field = trim($field);
        if( !$field )
        {
            continue;
        }
        while( strpos($field, $field_begin) === 0 )
        {
            $field = substr($field, 1);
        }
        $rev_field = strrev($field);
        $ordering_field_rev = '';
        if( strpos($rev_field, $order_up_rev) === 0 )
        {
            $ordering_field_rev .= $order_up_rev;
            $rev_field = substr($rev_field, strlen($order_up_rev));
        }
        else
        {
            if( strpos($rev_field, $order_down_rev) === 0 )
            {
                $ordering_field_rev .= $order_down_rev;
                $rev_field = substr($rev_field, strlen($order_down_rev));
            }
            else
            {
                $ordering_field_rev .= $default_ordering_rev;
            }
        }
        while( strpos($rev_field, $field_end) === 0 )
        {
            $rev_field = substr($rev_field, 1);
        }
        $field = strrev($rev_field);
        $field_parts = explode($qualifier, $field, 2);
        $safe_field_parts = array(  );
        foreach( $field_parts as $key => $part )
        {
            $tmp_part = db_make_safe_field($part);
            if( $tmp_part === trim($part) )
            {
                $safe_field_parts[] = $tmp_part;
            }
        }
        if( 1 < count($safe_field_parts) )
        {
            $field = implode($qualifier, $safe_field_parts);
        }
        else
        {
            $field = array_shift($safe_field_parts);
        }
        if( $field )
        {
            $tokenizedFields[] = $field_begin . $field . $field_end . strrev($ordering_field_rev);
        }
        $field = strtok($field_separator);
    }
    return $tokenizedFields;
}
function update_query($table, $array, $where, $resource = null)
{
    global $CONFIG;
    global $query_count;
    global $mysql_errors;
    if( !is_resource($resource) )
    {
        $resource = WHMCS_Application::getinstance()->getDatabaseObj()->retrieveDatabaseConnection();
    }
    $query = "UPDATE " . db_make_safe_field($table) . " SET ";
    foreach( $array as $key => $value )
    {
        $query .= db_build_quoted_field($key) . "=";
        $key = db_make_safe_field($key);
        if( is_array($value) )
        {
            if( isset($value['type']) && $value['type'] == 'AES_ENCRYPT' )
            {
                $query .= sprintf("AES_ENCRYPT('%s','%s'),", db_escape_string($value['text']), db_escape_string($value['hashkey']));
            }
        }
        else
        {
            if( $value === "now()" )
            {
                $query .= "'" . date('YmdHis') . "',";
            }
            else
            {
                if( $value === "+1" )
                {
                    $query .= "`" . $key . "`+1,";
                }
                else
                {
                    if( $value === 'NULL' )
                    {
                        $query .= 'NULL,';
                    }
                    else
                    {
                        if( substr($value, 0, 2) === "+=" && db_is_valid_amount(substr($value, 2)) )
                        {
                            $query .= "`" . $key . "`+" . db_escape_string(substr($value, 2)) . ',';
                        }
                        else
                        {
                            if( substr($value, 0, 2) === "-=" && db_is_valid_amount(substr($value, 2)) )
                            {
                                $query .= "`" . $key . "`-" . db_escape_string(substr($value, 2)) . ',';
                            }
                            else
                            {
                                $query .= "'" . db_escape_string($value) . "',";
                            }
                        }
                    }
                }
            }
        }
    }
    $query = substr($query, 0, 0 - 1);
    if( is_array($where) )
    {
        $query .= " WHERE";
        foreach( $where as $key => $value )
        {
            if( substr($key, 0, 4) == "MD5(" )
            {
                $key = "MD5(" . db_make_safe_field(substr($key, 4, 0 - 1)) . ")";
            }
            else
            {
                $key = db_make_safe_field($key);
                if( $key == 'order' )
                {
                    $key = "`order`";
                }
            }
            $query .= " " . $key . "='" . db_escape_string($value) . "' AND";
        }
        $query = substr($query, 0, 0 - 4);
    }
    else
    {
        if( $where )
        {
            $query .= " WHERE " . $where;
        }
    }
    $result = false;
    if( is_array($array) && 0 < count($array) )
    {
        $result = full_query($query, $resource);
        if( !$result && ($CONFIG['SQLErrorReporting'] || $mysql_errors) )
        {
            logActivity("SQL Error: " . mysql_error($resource) . " - Full Query: " . $query);
        }
        $query_count++;
    }
    else
    {
        if( $CONFIG['SQLErrorReporting'] )
        {
            logActivity("SQL Error: No values to SET" . " - Full Query: " . $query);
        }
    }
    return $result === false ? false : true;
}
function insert_query($table, $array, $resource = null)
{
    global $CONFIG;
    global $query_count;
    global $mysql_errors;
    if( !is_resource($resource) )
    {
        $resource = WHMCS_Application::getinstance()->getDatabaseObj()->retrieveDatabaseConnection();
    }
    $fieldnamelist = $fieldvaluelist = '';
    $query = "INSERT INTO " . db_make_safe_field($table) . " ";
    foreach( $array as $key => $value )
    {
        $fieldnamelist .= db_build_quoted_field($key) . ',';
        if( $value === "now()" )
        {
            $fieldvaluelist .= "'" . date('YmdHis') . "',";
        }
        else
        {
            if( $value === 'NULL' )
            {
                $fieldvaluelist .= 'NULL,';
            }
            else
            {
                $fieldvaluelist .= "'" . db_escape_string($value) . "',";
            }
        }
    }
    $fieldnamelist = substr($fieldnamelist, 0, 0 - 1);
    $fieldvaluelist = substr($fieldvaluelist, 0, 0 - 1);
    $query .= "(" . $fieldnamelist . ") VALUES (" . $fieldvaluelist . ")";
    $result = full_query($query, $resource);
    if( !$result && $table != 'tblactivitylog' && ($CONFIG['SQLErrorReporting'] || $mysql_errors) )
    {
        logActivity("SQL Error: " . mysql_error($resource) . " - Full Query: " . $query);
    }
    $query_count++;
    $id = mysql_insert_id($resource);
    return $id;
}
function delete_query($table, $where, $resource = null)
{
    global $CONFIG;
    global $query_count;
    global $mysql_errors;
    if( !is_resource($resource) )
    {
        $resource = WHMCS_Application::getinstance()->getDatabaseObj()->retrieveDatabaseConnection();
    }
    $query = "DELETE FROM " . db_make_safe_field($table) . " WHERE ";
    if( is_array($where) )
    {
        foreach( $where as $key => $value )
        {
            $query .= db_build_quoted_field($key) . "='" . db_escape_string($value) . "' AND ";
        }
        $query = substr($query, 0, 0 - 5);
    }
    else
    {
        $query .= $where;
    }
    $result = full_query($query, $resource);
    if( !$result && ($CONFIG['SQLErrorReporting'] || $mysql_errors) )
    {
        logActivity("SQL Error: " . mysql_error($resource) . " - Full Query: " . $query);
    }
    $query_count++;
}
function db_build_quoted_field($key)
{
    $field_quote = "`";
    $parts = explode(".", $key, 3);
    foreach( $parts as $k => $name )
    {
        $clean_name = db_make_safe_field($name);
        if( $clean_name !== $name && $field_quote . $clean_name . $field_quote !== $name )
        {
            exit( "Unexpected input field parameter in database query." );
        }
        $parts[$k] = $field_quote . $clean_name . $field_quote;
    }
    return implode(".", $parts);
}
function full_query($query, $userHandle = null)
{
    global $CONFIG;
    global $query_count;
    global $mysql_errors;
    if( is_resource($userHandle) )
    {
        $handle = $userHandle;
    }
    else
    {
        $handle = WHMCS_Application::getinstance()->getDatabaseObj()->retrieveDatabaseConnection();
    }
    $result = mysql_query($query, $handle);
    if( !$result && ($CONFIG['SQLErrorReporting'] || $mysql_errors) )
    {
        logActivity("SQL Error: " . mysql_error($handle) . " - Full Query: " . $query);
    }
    $query_count++;
    return $result;
}
function get_query_val($table, $field, $where, $orderby = '', $orderbyorder = '', $limit = '', $innerjoin = '', $resource = null)
{
    $result = select_query($table, $field, $where, $orderby, $orderbyorder, $limit, $innerjoin, $resource);
    if( !is_resource($result) )
    {
        return null;
    }
    $data = mysql_fetch_array($result);
    return $data[0];
}
function get_query_vals($table, $field, $where, $orderby = '', $orderbyorder = '', $limit = '', $innerjoin = '', $resource = null)
{
    $result = select_query($table, $field, $where, $orderby, $orderbyorder, $limit, $innerjoin, $resource);
    if( !is_resource($result) )
    {
        return null;
    }
    $data = mysql_fetch_array($result);
    return $data;
}
function db_escape_string($string)
{
    $string = mysql_real_escape_string($string);
    return $string;
}
function db_escape_array($array)
{
    $array = array_map('db_escape_string', $array);
    return $array;
}
function db_escape_numarray($array)
{
    $array = array_map('intval', $array);
    return $array;
}
function db_build_in_array($array, $allow_empty = false)
{
    if( !is_array($array) )
    {
        $array = array(  );
    }
    foreach( $array as $k => $v )
    {
        if( !trim($v) && !$allow_empty )
        {
            unset($array[$k]);
        }
        else
        {
            if( is_numeric($v) )
            {
                $v;
            }
            else
            {
                $array[$k] = "'" . db_escape_string($v) . "'";
            }
        }
    }
    return implode(',', $array);
}
function db_make_safe_field($field)
{
    return db_escape_string(preg_replace("/[^a-z0-9_.,]/i", '', $field));
}
function db_build_update_array($fields, $arrayhandler = 'serialize')
{
    $whmcs = WHMCS_Application::getinstance();
    $array = array(  );
    foreach( $fields as $key )
    {
        $array[$key] = $whmcs->get_req_var($key);
        if( is_array($array[$key]) )
        {
            if( $arrayhandler == 'serialize' )
            {
                $array[$key] = serialize($array[$key]);
            }
            else
            {
                if( $arrayhandler == 'implode' )
                {
                    $array[$key] = implode(',', $array[$key]);
                }
            }
        }
    }
    return $array;
}
function db_make_safe_date($date)
{
    $dateparts = explode('-', $date);
    $date = (int) $dateparts[0] . '-' . str_pad((int) $dateparts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad((int) $dateparts[2], 2, '0', STR_PAD_LEFT);
    return db_escape_string($date);
}
function db_make_safe_human_date($date)
{
    $date = toMySQLDate($date);
    return db_make_safe_date($date);
}
function db_is_valid_amount($amount)
{
    return preg_match("/^-?[0-9\\.]+\$/", $amount) === 1 ? true : false;
}