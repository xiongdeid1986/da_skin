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
class WHMCS_Clients extends WHMCS_TableModel
{
    private $groups = null;
    private $customfieldsfilter = false;
    public function _execute($criteria = null)
    {
        return $this->getClients($criteria);
    }
    public function getClients($criteria = array(  ))
    {
        global $disable_clients_list_services_summary;
        $clientgroups = $this->getGroups();
        $filters = $this->buildCriteria($criteria);
        $where = count($filters) ? " WHERE " . implode(" AND ", $filters) : '';
        $customfieldjoin = $this->customfieldsfilter ? " INNER JOIN tblcustomfieldsvalues ON tblcustomfieldsvalues.relid=tblclients.id" : '';
        $result = full_query("SELECT COUNT(*) FROM tblclients" . $customfieldjoin . $where);
        $data = mysql_fetch_array($result);
        $this->getPageObj()->setNumResults($data[0]);
        $clients = array(  );
        $query = "SELECT id,firstname,lastname,companyname,email,datecreated,groupid,status FROM tblclients" . $customfieldjoin . $where . " ORDER BY " . $this->getPageObj()->getOrderBy() . " " . $this->getPageObj()->getSortDirection() . " LIMIT " . $this->getQueryLimit();
        $result = full_query($query);
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $firstname = $data['firstname'];
            $lastname = $data['lastname'];
            $companyname = $data['companyname'];
            $email = $data['email'];
            $datecreated = $data['datecreated'];
            $groupid = $data['groupid'];
            $status = $data['status'];
            $datecreated = fromMySQLDate($datecreated);
            $groupcolor = isset($clientgroups[$groupid]['colour']) ? $clientgroups[$groupid]['colour'] . "\"" : '';
            $services = $totalservices = '-';
            if( !$disable_clients_list_services_summary )
            {
                $result2 = full_query("SELECT (SELECT COUNT(*) FROM tblhosting WHERE userid=tblclients.id AND domainstatus IN ('Active','Suspended'))+(SELECT COUNT(*) FROM tblhostingaddons WHERE hostingid IN (SELECT id FROM tblhosting WHERE userid=tblclients.id) AND status IN ('Active','Suspended'))+(SELECT COUNT(*) FROM tbldomains WHERE userid=tblclients.id AND status IN ('Active')) AS services,(SELECT COUNT(*) FROM tblhosting WHERE userid=tblclients.id)+(SELECT COUNT(*) FROM tblhostingaddons WHERE hostingid IN (SELECT id FROM tblhosting WHERE userid=tblclients.id))+(SELECT COUNT(*) FROM tbldomains WHERE userid=tblclients.id) AS totalservices FROM tblclients WHERE tblclients.id=" . (int) $id . " LIMIT 1");
                $data = mysql_fetch_array($result2);
                $services = $data['services'];
                $totalservices = $data['totalservices'];
            }
            $clients[] = array( 'id' => $id, 'firstname' => $firstname, 'lastname' => $lastname, 'companyname' => $companyname, 'groupid' => $groupid, 'groupcolor' => $groupcolor, 'email' => $email, 'services' => $services, 'totalservices' => $totalservices, 'datecreated' => $datecreated, 'status' => $status );
        }
        return $clients;
    }
    private function buildCriteria($criteria)
    {
        $filters = array(  );
        if( $criteria['userid'] )
        {
            $filters[] = "id=" . (int) $criteria['userid'];
        }
        if( $criteria['clientname'] )
        {
            $filters[] = "concat(firstname,' ',lastname) LIKE '%" . db_escape_string($criteria['clientname']) . "%'";
        }
        if( $criteria['address'] )
        {
            $filters[] = "concat(address1,' ',address2,' ',city,' ',state,' ',postcode) LIKE '%" . db_escape_string($criteria['address']) . "%'";
        }
        if( $criteria['state'] )
        {
            $filters[] = "state LIKE '%" . db_escape_string($criteria['state']) . "%'";
        }
        if( $criteria['country'] )
        {
            $filters[] = "country='" . db_escape_string($criteria['country']) . "'";
        }
        if( $criteria['companyname'] )
        {
            $filters[] = "companyname LIKE '%" . db_escape_string($criteria['companyname']) . "%'";
        }
        if( $criteria['email'] )
        {
            $filters[] = "email LIKE '%" . db_escape_string($criteria['email']) . "%'";
        }
        if( $criteria['phonenumber'] )
        {
            $filters[] = "phonenumber LIKE '%" . db_escape_string($criteria['phonenumber']) . "%'";
        }
        if( $criteria['status'] )
        {
            $filters[] = "status='" . db_escape_string($criteria['status']) . "'";
        }
        if( $criteria['clientgroup'] )
        {
            $filters[] = "groupid='" . db_escape_string($criteria['clientgroup']) . "'";
        }
        if( $criteria['cardlastfour'] )
        {
            $filters[] = "cardlastfour='" . db_escape_string($criteria['cardlastfour']) . "'";
        }
        if( $criteria['currency'] )
        {
            $filters[] = "currency='" . db_escape_string($criteria['currency']) . "'";
        }
        $cfquery = array(  );
        if( is_array($criteria['customfields']) )
        {
            foreach( $criteria['customfields'] as $fieldid => $fieldvalue )
            {
                $fieldvalue = trim($fieldvalue);
                if( $fieldvalue )
                {
                    $cfquery[] = "(tblcustomfieldsvalues.fieldid='" . db_escape_string($fieldid) . "' AND tblcustomfieldsvalues.value LIKE '%" . db_escape_string($fieldvalue) . "%')";
                    $this->customfieldsfilter = true;
                }
            }
        }
        if( count($cfquery) )
        {
            $filters[] = implode(" OR ", $cfquery);
        }
        return $filters;
    }
    public function getGroups()
    {
        if( is_array($this->groups) )
        {
            return $this->groups;
        }
        $this->groups = array(  );
        $result = select_query('tblclientgroups', '', '');
        while( $data = mysql_fetch_array($result) )
        {
            $this->groups[$data['id']] = array( 'name' => $data['groupname'], 'colour' => $data['groupcolour'], 'discountpercent' => $data['discountpercent'], 'susptermexempt' => $data['susptermexempt'], 'separateinvoices' => $data['separateinvoices'] );
        }
        return $this->groups;
    }
}