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
class WHMCS_Invoices extends WHMCS_TableModel
{
    public function _execute($criteria = null)
    {
        return $this->getInvoices($criteria);
    }
    public function getInvoices($criteria = array(  ))
    {
        global $aInt;
        global $currency;
        $query = " FROM tblinvoices INNER JOIN tblclients ON tblclients.id=tblinvoices.userid";
        $filters = $this->buildCriteria($criteria);
        $query .= count($filters) ? " WHERE " . implode(" AND ", $filters) : '';
        $result = full_query("SELECT COUNT(*)" . $query);
        $data = mysql_fetch_array($result);
        $this->getPageObj()->setNumResults($data[0]);
        $gateways = new WHMCS_Gateways();
        $orderby = $this->getPageObj()->getOrderBy();
        if( $orderby == 'clientname' )
        {
            $orderby = "firstname " . $this->getPageObj()->getSortDirection() . ",lastname " . $this->getPageObj()->getSortDirection() . ',companyname';
        }
        if( $orderby == 'id' )
        {
            $orderby = "tblinvoices.invoicenum " . $this->getPageObj()->getSortDirection() . ",tblinvoices.id";
        }
        $invoices = array(  );
        $query = "SELECT tblinvoices.*,tblclients.firstname,tblclients.lastname,tblclients.companyname,tblclients.groupid,tblclients.currency" . $query . " ORDER BY " . $orderby . " " . $this->getPageObj()->getSortDirection() . " LIMIT " . $this->getQueryLimit();
        $result = full_query($query);
        while( $data = mysql_fetch_array($result) )
        {
            $id = $data['id'];
            $invoicenum = $data['invoicenum'];
            $userid = $data['userid'];
            $date = $data['date'];
            $duedate = $data['duedate'];
            $subtotal = $data['subtotal'];
            $credit = $data['credit'];
            $total = $data['total'];
            $gateway = $data['paymentmethod'];
            $status = $data['status'];
            $firstname = $data['firstname'];
            $lastname = $data['lastname'];
            $companyname = $data['companyname'];
            $groupid = $data['groupid'];
            $currency = $data['currency'];
            $clientname = $aInt->outputClientLink($userid, $firstname, $lastname, $companyname, $groupid);
            $paymentmethod = $gateways->getDisplayName($gateway);
            $currency = getCurrency('', $currency);
            $totalformatted = formatCurrency($credit + $total);
            $statusformatted = $this->formatStatus($status);
            $date = fromMySQLDate($date);
            $duedate = fromMySQLDate($duedate);
            if( !$invoicenum )
            {
                $invoicenum = $id;
            }
            $invoices[] = array( 'id' => $id, 'invoicenum' => $invoicenum, 'userid' => $userid, 'clientname' => $clientname, 'date' => $date, 'duedate' => $duedate, 'subtotal' => $subtotal, 'credit' => $credit, 'total' => $total, 'totalformatted' => $totalformatted, 'gateway' => $gateway, 'paymentmethod' => $paymentmethod, 'status' => $status, 'statusformatted' => $statusformatted );
        }
        return $invoices;
    }
    private function buildCriteria($criteria)
    {
        $filters = array(  );
        if( $criteria['clientid'] )
        {
            $filters[] = "userid=" . (int) $criteria['clientid'];
        }
        if( $criteria['clientname'] )
        {
            $filters[] = "concat(firstname,' ',lastname) LIKE '%" . db_escape_string($criteria['clientname']) . "%'";
        }
        if( $criteria['invoicenum'] )
        {
            $filters[] = "(tblinvoices.id='" . db_escape_string($criteria['invoicenum']) . "' OR tblinvoices.invoicenum='" . db_escape_string($criteria['invoicenum']) . "')";
        }
        if( $criteria['lineitem'] )
        {
            $filters[] = "tblinvoices.id IN (SELECT invoiceid FROM tblinvoiceitems WHERE description LIKE '%" . db_escape_string($criteria['lineitem']) . "%')";
        }
        if( $criteria['paymentmethod'] )
        {
            $filters[] = "tblinvoices.paymentmethod='" . db_escape_string($criteria['paymentmethod']) . "'";
        }
        if( $criteria['invoicedate'] )
        {
            $filters[] = "tblinvoices.date='" . toMySQLDate($criteria['invoicedate']) . "'";
        }
        if( $criteria['duedate'] )
        {
            $filters[] = "tblinvoices.duedate='" . toMySQLDate($criteria['duedate']) . "'";
        }
        if( $criteria['datepaid'] )
        {
            $filters[] = "tblinvoices.datepaid>='" . toMySQLDate($criteria['datepaid']) . "' AND tblinvoices.datepaid<='" . toMySQLDate($criteria['datepaid']) . "235959'";
        }
        if( $criteria['totalfrom'] )
        {
            $filters[] = "tblinvoices.total>='" . db_escape_string($criteria['totalfrom']) . "'";
        }
        if( $criteria['totalto'] )
        {
            $filters[] = "tblinvoices.total<='" . db_escape_string($criteria['totalto']) . "'";
        }
        if( $criteria['status'] )
        {
            if( $criteria['status'] == 'Overdue' )
            {
                $filters[] = "tblinvoices.status='Unpaid' AND tblinvoices.duedate<'" . date('Ymd') . "'";
            }
            else
            {
                $filters[] = "tblinvoices.status='" . db_escape_string($criteria['status']) . "'";
            }
        }
        return $filters;
    }
    public function formatStatus($status)
    {
        if( defined('ADMINAREA') )
        {
            global $aInt;
            if( $status == 'Unpaid' )
            {
                $status = "<span class=\"textred\">" . $aInt->lang('status', 'unpaid') . "</span>";
            }
            else
            {
                if( $status == 'Paid' )
                {
                    $status = "<span class=\"textgreen\">" . $aInt->lang('status', 'paid') . "</span>";
                }
                else
                {
                    if( $status == 'Cancelled' )
                    {
                        $status = "<span class=\"textgrey\">" . $aInt->lang('status', 'cancelled') . "</span>";
                    }
                    else
                    {
                        if( $status == 'Refunded' )
                        {
                            $status = "<span class=\"textblack\">" . $aInt->lang('status', 'refunded') . "</span>";
                        }
                        else
                        {
                            if( $status == 'Collections' )
                            {
                                $status = "<span class=\"textgold\">" . $aInt->lang('status', 'collections') . "</span>";
                            }
                            else
                            {
                                $status = 'Unrecognised';
                            }
                        }
                    }
                }
            }
        }
        else
        {
            global $_LANG;
            if( $status == 'Unpaid' )
            {
                $status = "<span class=\"textred\">" . $_LANG['invoicesunpaid'] . "</span>";
            }
            else
            {
                if( $status == 'Paid' )
                {
                    $status = "<span class=\"textgreen\">" . $_LANG['invoicespaid'] . "</span>";
                }
                else
                {
                    if( $status == 'Cancelled' )
                    {
                        $status = "<span class=\"textgrey\">" . $_LANG['invoicescancelled'] . "</span>";
                    }
                    else
                    {
                        if( $status == 'Refunded' )
                        {
                            $status = "<span class=\"textblack\">" . $_LANG['invoicesrefunded'] . "</span>";
                        }
                        else
                        {
                            if( $status == 'Collections' )
                            {
                                $status = "<span class=\"textgold\">" . $_LANG['invoicescollections'] . "</span>";
                            }
                            else
                            {
                                $status = 'Unrecognised';
                            }
                        }
                    }
                }
            }
        }
        return $status;
    }
    public function getInvoiceTotals()
    {
        global $currency;
        $invoicesummary = array(  );
        $result = full_query("SELECT currency,COUNT(tblinvoices.id),SUM(total) FROM tblinvoices INNER JOIN tblclients ON tblclients.id=tblinvoices.userid WHERE tblinvoices.status='Paid' GROUP BY tblclients.currency");
        while( $data = mysql_fetch_array($result) )
        {
            $invoicesummary[$data[0]]['paid'] = $data[2];
        }
        $result = full_query("SELECT currency,COUNT(tblinvoices.id),SUM(total)-COALESCE(SUM((SELECT SUM(amountin) FROM tblaccounts WHERE tblaccounts.invoiceid=tblinvoices.id)),0) FROM tblinvoices INNER JOIN tblclients ON tblclients.id=tblinvoices.userid WHERE tblinvoices.status='Unpaid' AND tblinvoices.duedate>='" . date('Ymd') . "' GROUP BY tblclients.currency");
        while( $data = mysql_fetch_array($result) )
        {
            $invoicesummary[$data[0]]['unpaid'] = $data[2];
        }
        $result = full_query("SELECT currency,COUNT(tblinvoices.id),SUM(total)-COALESCE(SUM((SELECT SUM(amountin) FROM tblaccounts WHERE tblaccounts.invoiceid=tblinvoices.id)),0) FROM tblinvoices INNER JOIN tblclients ON tblclients.id=tblinvoices.userid WHERE tblinvoices.status='Unpaid' AND tblinvoices.duedate<'" . date('Ymd') . "' GROUP BY tblclients.currency");
        while( $data = mysql_fetch_array($result) )
        {
            $invoicesummary[$data[0]]['overdue'] = $data[2];
        }
        $totals = array(  );
        foreach( $invoicesummary as $currency => $vals )
        {
            $currency = getCurrency('', $currency);
            if( !isset($vals['paid']) )
            {
                $vals['paid'] = 0;
            }
            if( !isset($vals['unpaid']) )
            {
                $vals['unpaid'] = 0;
            }
            if( !isset($vals['overdue']) )
            {
                $vals['overdue'] = 0;
            }
            $paid = formatCurrency($vals['paid']);
            $unpaid = formatCurrency($vals['unpaid']);
            $overdue = formatCurrency($vals['overdue']);
            $totals[] = array( 'currencycode' => $currency['code'], 'paid' => $paid, 'unpaid' => $unpaid, 'overdue' => $overdue );
        }
        return $totals;
    }
    public function duplicate($invoiceid)
    {
        $whmcs = WHMCS_Application::getinstance();
        $result = select_query('tblinvoices', 'userid,invoicenum,date,duedate,datepaid,subtotal,credit,tax,tax2,total,taxrate,taxrate2,status,paymentmethod,notes', array( 'id' => $invoiceid ));
        $data = mysql_fetch_assoc($result);
        WHMCS_Invoices::adjustincrementfornextinvoice($invoiceid);
        $userid = $data['userid'];
        $newid = insert_query('tblinvoices', $data);
        $result = select_query('tblinvoiceitems', '', array( 'invoiceid' => $invoiceid ));
        while( $data = mysql_fetch_assoc($result) )
        {
            unset($data['id']);
            $data['invoiceid'] = $newid;
            insert_query('tblinvoiceitems', $data);
        }
        logActivity("Duplicated Invoice - Existing Invoice ID: " . $invoiceid . " - New Invoice ID: " . $newid, $userid);
        return true;
    }
    /**
     * Get the status of sequential paid invoice numbering
     *
     * @return boolean
     */
    public static function isSequentialPaidInvoiceNumberingEnabled()
    {
        $whmcs = WHMCS_Application::getinstance();
        return $whmcs->get_config('SequentialInvoiceNumbering') ? true : false;
    }
    /**
     * Get the next sequential paid invoice number to assign to an invoice
     *
     * Also increments the next number value
     *
     * @return string
     */
    public static function getNextSequentialPaidInvoiceNumber()
    {
        $whmcs = WHMCS_Application::getinstance();
        $numberToAssign = $whmcs->get_config('SequentialInvoiceNumberFormat');
        $nextNumber = $whmcs->get_config('SequentialInvoiceNumberValue');
        $whmcs->set_config('SequentialInvoiceNumberValue', self::padandincrement($nextNumber));
        $numberToAssign = str_replace("{YEAR}", date('Y'), $numberToAssign);
        $numberToAssign = str_replace("{MONTH}", date('m'), $numberToAssign);
        $numberToAssign = str_replace("{DAY}", date('d'), $numberToAssign);
        $numberToAssign = str_replace("{NUMBER}", $nextNumber, $numberToAssign);
        return $numberToAssign;
    }
    /**
     * Increment a number by a given amount preserving leading zeros
     *
     * @param string $number Starting number
     * @param int $incrementAmount Increment amount (defaults to 1)
     *
     * @return string
     */
    public static function padAndIncrement($number, $incrementAmount = 1)
    {
        $newNumber = $number + $incrementAmount;
        if( substr($number, 0, 1) == '0' )
        {
            $numberLength = strlen($number);
            $newNumber = str_pad($newNumber, $numberLength, '0', STR_PAD_LEFT);
        }
        return $newNumber;
    }
    /**
     * Use the provided invoice id to calculate and set the next invoice id
     * that would be used based on the InvoiceIncrement value.
     *
     * This is a replacement to the for loop that inserted x invoices depending
     * on the InvoiceIncrement value. Anything above 1 will insert a an item into
     * tblinvoices using the passed invoice id + the Invoice Increment value -1.
     *
     * @param int $lastInvoiceId - The ID of the last invoice to be inserted
     */
    public static function adjustIncrementForNextInvoice($lastInvoiceId)
    {
        $whmcs = WHMCS_Application::getinstance();
        $incrementValue = (int) $whmcs->get_config('InvoiceIncrement');
        if( 1 < $incrementValue )
        {
            $incrementedId = $lastInvoiceId + $incrementValue - 1;
            insert_query('tblinvoices', array( 'id' => $incrementedId ));
            delete_query('tblinvoices', array( 'id' => $incrementedId ));
        }
    }
}