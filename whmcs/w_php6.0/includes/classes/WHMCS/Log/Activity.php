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
class WHMCS_Log_Activity
{
    protected $criteria = array(  );
    protected $outputFormatting = true;
    /**
     * Set format for output (defaults to true)
     *
     * @param boolean $enable Set true to enable
     */
    public function setOutputFormatting($enable)
    {
        $this->outputFormatting = $enable ? true : false;
    }
    /**
     * Get format for output status
     *
     * @return boolean
     */
    public function getOutputFormatting()
    {
        return $this->outputFormatting;
    }
    public function prune()
    {
        $whmcs = WHMCS_Application::getinstance();
        $activitylimit = (int) $whmcs->get_config('ActivityLimit');
        $result = select_query('tblactivitylog', '', "userid=0", 'id', 'DESC', $activitylimit . ',9999');
        while( $data = mysql_fetch_array($result) )
        {
            delete_query('tblactivitylog', array( 'id' => $data['id'] ));
        }
        return true;
    }
    public function setCriteria($where)
    {
        if( is_array($where) )
        {
            $this->criteria = $where;
            return true;
        }
        return false;
    }
    public function getCriteria($key)
    {
        return array_key_exists($key, $this->criteria) ? $this->criteria[$key] : '';
    }
    protected function buildCriteria()
    {
        $userid = $this->getCriteria('userid');
        $date = $this->getCriteria('date');
        $description = $this->getCriteria('description');
        $username = $this->getCriteria('username');
        $ipaddress = $this->getCriteria('ipaddress');
        $where = array(  );
        if( $userid )
        {
            $where[] = "userid='" . (int) $userid . "'";
        }
        if( $date )
        {
            $where[] = "date>'" . toMySQLDate($date) . "' AND date<='" . toMySQLDate($date) . "235959'";
        }
        if( $description )
        {
            $where[] = "description LIKE '%" . db_escape_string($description) . "%'";
        }
        if( $username )
        {
            $where[] = "user='" . db_escape_string($username) . "'";
        }
        if( $ipaddress )
        {
            $where[] = " ipaddr='" . db_escape_string($ipaddress) . "'";
        }
        return implode(" AND ", $where);
    }
    public function getTotalCount()
    {
        $result = select_query('tblactivitylog', "COUNT(id)", $this->buildCriteria());
        $data = mysql_fetch_array($result);
        return (int) $data[0];
    }
    public function getLogEntries($page = 0, $limit = 0)
    {
        $page = (int) $page;
        $limit = (int) $limit;
        if( !$limit )
        {
            $whmcs = WHMCS_Application::getinstance();
            $limit = (int) $whmcs->get_config('NumRecordstoDisplay');
        }
        $logs = array(  );
        $result = select_query('tblactivitylog', '', $this->buildCriteria(), 'id', 'DESC', $page * $limit . ',' . $limit);
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $userid = $data['userid'];
            $date = $data['date'];
            $description = $data['description'];
            $username = $data['user'];
            $ipaddress = $data['ipaddr'];
            if( $this->getOutputFormatting() )
            {
                $date = fromMySQLDate($date, true);
                $description = WHMCS_Input_Sanitize::makesafeforoutput($description);
                $username = WHMCS_Input_Sanitize::makesafeforoutput($username);
                $ipaddress = WHMCS_Input_Sanitize::makesafeforoutput($ipaddress);
                $description = $this->autoLink($description);
            }
            $logs[] = array( 'id' => (int) $id, 'userid' => (int) $userid, 'date' => $date, 'description' => $description, 'username' => $username, 'ipaddress' => $ipaddress );
        }
        return $logs;
    }
    protected function autoLink($description)
    {
        $patterns = $replacements = array(  );
        $patterns[] = "/User ID: (.*?) /";
        $patterns[] = "/Service ID: (.*?) /";
        $patterns[] = "/Domain ID: (.*?) /";
        $patterns[] = "/Invoice ID: (.*?) /";
        $patterns[] = "/Quote ID: (.*?) /";
        $patterns[] = "/Order ID: (.*?) /";
        $patterns[] = "/Transaction ID: (.*?) /";
        $replacements[] = "<a href=\"clientssummary.php?userid=\$1\">User ID: \$1</a> ";
        $replacements[] = "<a href=\"clientsservices.php?id=\$1\">Service ID: \$1</a> ";
        $replacements[] = "<a href=\"clientsdomains.php?id=\$1\">Domain ID: \$1</a> ";
        $replacements[] = "<a href=\"invoices.php?action=edit&id=\$1\">Invoice ID: \$1</a> ";
        $replacements[] = "<a href=\"quotes.php?action=manage&id=\$1\">Quote ID: \$1</a> ";
        $replacements[] = "<a href=\"orders.php?action=view&id=\$1\">Order ID: \$1</a> ";
        $replacements[] = "<a href=\"transactions.php?action=edit&id=\$1\">Transaction ID: \$1</a> ";
        $description = preg_replace($patterns, $replacements, $description . " ");
        return trim($description);
    }
}