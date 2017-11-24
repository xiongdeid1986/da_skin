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
class WHMCS_Pagination extends WHMCS_TableQuery
{
    protected $page = 1;
    protected $defaultsort = 'ASC';
    protected $defaultorderby = 'id';
    protected $name = 'default';
    protected $sort = '';
    protected $orderby = '';
    protected $numResults = 0;
    protected $pagination = true;
    public function __construct($name = '', $defaultorderby = '', $defaultsort = '')
    {
        if( $name )
        {
            $this->name = $name;
        }
        else
        {
            $name = $this->name;
        }
        if( $defaultorderby )
        {
            $this->setDefaultOrderBy($defaultorderby);
        }
        if( $defaultsort )
        {
            $this->setDefaultSortDirection($defaultsort);
        }
        return $this;
    }
    /**
     * This function reads and interprets the sorting data (SD) stored
     * in cookies for the current page.
     *
     * NB: It must be called prior to any filter class store function
     * in order to correctly preserve applied filters.
     *
     * @return void
     */
    public function digestCookieData()
    {
        global $whmcs;
        $sortdata = WHMCS_Cookie::get('SD', true);
        $name = $this->name;
        if( array_key_exists($name, $sortdata) )
        {
            $orderby = $sortdata[$name]['orderby'];
            if( $orderby )
            {
                $this->setOrderBy($orderby);
            }
            $orderbysort = $sortdata[$name]['sort'];
            if( $orderbysort )
            {
                $this->setSortDirection($orderbysort);
            }
        }
        if( $orderby = $whmcs->get_req_var('orderby') )
        {
            $this->setOrderBy($orderby);
            $sortdata[$name] = array( 'orderby' => $this->orderby, 'sort' => $this->sort );
            WHMCS_Cookie::set('SD', $sortdata);
            redir("filter=1");
        }
        if( $page = $whmcs->get_req_var('page') )
        {
            $this->setPage($page);
        }
        $this->setRecordLimit($whmcs->get_config('NumRecordstoDisplay'));
    }
    public function setPage($page)
    {
        $this->page = (int) $page;
        return true;
    }
    public function getPage()
    {
        $page = (int) $this->page;
        $totalpages = $this->getTotalPages();
        if( $page < 1 )
        {
            $page = 1;
        }
        if( $totalpages < $page )
        {
            $page = $totalpages;
        }
        return $page;
    }
    public function setNumResults($num)
    {
        $this->numResults = $num;
    }
    public function getNumResults()
    {
        return (int) $this->numResults;
    }
    public function getTotalPages()
    {
        $pages = ceil($this->getNumResults() / $this->getRecordLimit());
        if( $pages < 1 )
        {
            $pages = 1;
        }
        return $pages;
    }
    public function getPrevPage()
    {
        $page = $this->getPage();
        $pages = $this->getTotalPages();
        if( $page <= 1 || $pages <= 1 )
        {
            return '';
        }
        return $page - 1;
    }
    public function getNextPage()
    {
        $page = $this->getPage();
        $pages = $this->getTotalPages();
        if( $pages <= $page )
        {
            return '';
        }
        return $page + 1;
    }
    public function setDefaultOrderBy($field)
    {
        global $whmcs;
        $this->defaultorderby = $whmcs->sanitize('a-z', $field);
    }
    public function setDefaultSortDirection($sort)
    {
        $this->defaultsort = strtoupper($sort) == 'DESC' ? 'DESC' : 'ASC';
    }
    public function setOrderBy($field)
    {
        if( $this->orderby == $field )
        {
            $this->reverseSortDirection();
        }
        else
        {
            $this->orderby = $field;
        }
        return true;
    }
    public function setValidOrderByValues($array)
    {
        if( !is_array($array) )
        {
            return false;
        }
        $this->validorderbyvalues = $array;
        return true;
    }
    public function getValidOrderByValues()
    {
        return $this->validorderbyvalues;
    }
    public function isValidOrderBy($field)
    {
        return in_array($field, $this->getValidOrderByValues());
    }
    public function getOrderBy()
    {
        if( $this->isValidOrderBy($this->orderby) )
        {
            return $this->orderby;
        }
        $this->setSortDirection('');
        return $this->defaultorderby;
    }
    public function setSortDirection($sort)
    {
        $this->sort = $sort;
        return true;
    }
    public function reverseSortDirection()
    {
        if( $this->sort == 'ASC' )
        {
            $this->sort = 'DESC';
        }
        else
        {
            $this->sort = 'ASC';
        }
        return true;
    }
    public function getSortDirection()
    {
        if( in_array($this->sort, array( 'ASC', 'DESC' )) )
        {
            return $this->sort;
        }
        return $this->defaultsort;
    }
    public function setPagination($boolean)
    {
        $this->pagination = $boolean;
    }
    public function isPaginated()
    {
        return $this->pagination ? true : false;
    }
}