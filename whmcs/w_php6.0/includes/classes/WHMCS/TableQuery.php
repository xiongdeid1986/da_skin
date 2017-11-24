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
class WHMCS_TableQuery
{
    protected $recordOffset = 0;
    protected $recordLimit = 25;
    protected $data = array(  );
    public function getData()
    {
        return $this->data;
    }
    public function getOne()
    {
        return isset($this->data[0]) ? $this->data[0] : null;
    }
    public function setRecordLimit($limit)
    {
        $this->recordLimit = $limit;
        return $this;
    }
    public function getRecordLimit()
    {
        return $this->recordLimit;
    }
    public function getRecordOffset()
    {
        $page = $this->getPageObj()->getPage();
        $offset = ($page - 1) * $this->getRecordLimit();
        return $offset;
    }
    public function getQueryLimit()
    {
        return $this->getRecordOffset() . ',' . $this->getRecordLimit();
    }
    public function setData($data = array(  ))
    {
        if( !is_array($data) )
        {
            throw new InvalidArgumentException("Dataset must be an array");
        }
        $this->data = $data;
        return $this;
    }
}