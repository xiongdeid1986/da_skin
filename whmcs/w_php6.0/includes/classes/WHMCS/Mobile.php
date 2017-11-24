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
class WHMCS_Mobile extends WHMCS_Admin
{
    public function getTemplatePath()
    {
        if( !defined('MOBILEDIR') )
        {
            exit( "No Mobile Directory Defined" );
        }
        return MOBILEDIR . '/templates/';
    }
    protected function factoryAdminSmarty()
    {
        $smarty = parent::factoryadminsmarty();
        $smarty->template_dir = $this->getTemplatePath();
        return $smarty;
    }
    public function output()
    {
        $this->smarty->display("header.tpl");
        $content = $this->smarty->fetch($this->template . ".tpl");
        $content = preg_replace("/(<form\\W[^>]*\\bmethod=('|\"|)POST('|\"|)\\b[^>]*>)/i", "\\1" . "\n" . generate_token(), $content);
        if( $this->exitmsg )
        {
            $content = $this->exitmsg;
        }
        echo $content;
        $this->smarty->display("footer.tpl");
    }
    public function setPageTitle($title)
    {
        $this->title = $title;
        return true;
    }
    public function setHeaderLeftBtn($url, $label = '', $icon = '')
    {
        if( $url == 'back' )
        {
            $url = "\" data-rel=\"back";
            $label = 'Back';
            $icon = 'back';
        }
        if( $url == 'home' )
        {
            $url = "index.php";
            $label = 'Home';
            $icon = 'home';
        }
        $this->assign('headleftbtnurl', $url);
        $this->assign('headleftbtnlabel', $label);
        $this->assign('headleftbtnicon', $icon);
    }
    public function setHeaderRightBtn($url, $label, $icon = '')
    {
        $this->assign('headrightbtnurl', $url);
        $this->assign('headrightbtnlabel', $label);
        $this->assign('headrightbtnicon', $icon);
    }
}