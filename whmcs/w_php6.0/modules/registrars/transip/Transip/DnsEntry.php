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
 * Models A DnsEntry
 *
 * @package Transip
 * @class DnsEntry
 * @author TransIP (support@transip.nl)
 * @version 20121211 12:04
 */
class Transip_DnsEntry
{
    public $name = NULL;
    public $expire = NULL;
    public $type = NULL;
    public $content = NULL;
    const TYPE_A = 'A';
    const TYPE_AAAA = 'AAAA';
    const TYPE_CNAME = 'CNAME';
    const TYPE_MX = 'MX';
    const TYPE_NS = 'NS';
    const TYPE_TXT = 'TXT';
    const TYPE_SRV = 'SRV';
    /**
     * Constructs a new DnsEntry of the form
     * www  IN  86400   A       127.0.0.1
     * mail IN  86400   CNAME   @
     *
     * Note that the IN class is always mandatory for this Entry and this is implied.
     *
     * @param string $name the name of this DnsEntry, e.g. www, mail or @
     * @param int $expire the expiration period of the dns entry, in seconds. For example 86400 for a day
     * @param string $type the type of this entry, one of the TYPE_ constants in this class
     * @param string $content content of of the dns entry, for example '10 mail', '127.0.0.1' or 'www'
     */
    public function __construct($name, $expire, $type, $content)
    {
        $this->name = $name;
        $this->expire = $expire;
        $this->type = $type;
        $this->content = $content;
    }
}