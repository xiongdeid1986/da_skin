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
 * Transient Data Class
 *
 * @category  WHMCS
 * @package   WHMCS
 * @author    WHMCS Limited <development@whmcs.com>
 * @copyright 2005-2015 Copyright (c) WHMCS Limited
 * @license   http://www.whmcs.com/license/ WHMCS Eula
 * @version   $Id
 * @link      http://www.whmcs.com/
 */
class WHMCS_TransientData
{
    const DB_TABLE = 'tbltransientdata';
    /**
     * Stores the given data into transient storage
     *
     * @param string $name The key name for the data, must always be unique
     * @param string $data The data to be stored, must be a string
     * @param int    $life The time in seconds the data should be retained for
     *
     * @return boolean True on success
     */
    public function store($name, $data, $life = 300)
    {
        if( !is_string($data) )
        {
            return false;
        }
        $expires = time() + (int) $life;
        if( $this->ifNameExists($name) )
        {
            $this->sqlUpdate($name, $data, $expires);
        }
        else
        {
            $this->sqlInsert($name, $data, $expires);
        }
        return true;
    }
    /**
     * Retrieve a value from the transient data store
     *
     * @param string $name The key name to lookup
     *
     * @return string The data from the store
     */
    public function retrieve($name)
    {
        return $this->sqlSelect($name, true);
    }
    /**
     * Checks if a key name exists in the transient data store
     *
     * @param string $name The key name to look for
     *
     * @return boolean Returns true if found
     */
    public function ifNameExists($name)
    {
        $data = $this->sqlSelect($name);
        return $data === null ? false : true;
    }
    /**
     * Deletes the specified key name data from the transient data store
     *
     * @param string $name The key name to be removed
     *
     * @return boolean
     */
    public function delete($name)
    {
        $this->sqlDelete($name);
        return true;
    }
    /**
     * Deletes expired data from the transient data store
     *
     * Supports passing in a delay parameter to avoid interfering with any
     * processes currently using the transient data store
     *
     * @param int $delaySeconds Number of seconds, defaults to 120 if not passed
     *
     * @return boolean
     */
    public function purgeExpired($delaySeconds = 120)
    {
        $now = time() - (int) $delaySeconds;
        delete_query('tbltransientdata', "expires<" . db_escape_string($now));
        return true;
    }
    /**
     * Performs SQL Select from Transient Data Store
     *
     * @param string  $name            The key name to select
     * @param boolean $exclude_expired Set true to only retrieve non-expired data
     *
     * @return string|NULL Returns data or null upon no match
     */
    protected function sqlSelect($name, $exclude_expired = false)
    {
        $where = array( 'name' => $name );
        if( $exclude_expired )
        {
            $where['expires'] = array( 'sqltype' => ">", 'value' => time() );
        }
        $data = get_query_val(self::DB_TABLE, 'data', $where);
        return $data;
    }
    /**
     * Performs SQL Insert to Transient Data Store
     *
     * @param string $name    The key name to create
     * @param string $data    The data to store
     * @param int    $expires The expiry timestamp
     *
     * @return int The ID of the created record
     */
    protected function sqlInsert($name, $data, $expires)
    {
        $arrdata = array( 'name' => $name, 'data' => $data, 'expires' => $expires );
        return insert_query(self::DB_TABLE, $arrdata);
    }
    /**
     * Performs SQL Update in Transient Data Store
     *
     * @param string $name    The key name to update
     * @param string $data    The data to store
     * @param int    $expires The new expiry timestamp
     *
     * @return boolean
     */
    protected function sqlUpdate($name, $data, $expires)
    {
        $updatearr = array( 'data' => $data, 'expires' => $expires );
        $where = array( 'name' => $name );
        update_query(self::DB_TABLE, $updatearr, $where);
        return true;
    }
    /**
     * Performs SQL Delete from Transient Data Store
     *
     * @param string $name The key name to delete
     *
     * @return boolean
     */
    public function sqlDelete($name)
    {
        $where = array( 'name' => $name );
        delete_query(self::DB_TABLE, $where);
        return true;
    }
}