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
 * PDF factory that wraps the TCPDF library
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_PDF extends TCPDF
{
    public function __construct()
    {
        $whmcs = WHMCS_Application::getinstance();
        $l = array(  );
        $l['a_meta_charset'] = $whmcs->get_config('Charset');
        $l['a_meta_dir'] = 'ltr';
        $l['a_meta_language'] = 'en';
        $l['w_page'] = 'page';
        $paperSize = $whmcs->get_config('PDFPaperSize');
        if( !$paperSize )
        {
            $paperSize = 'A4';
        }
        $unicode = strtolower(substr($whmcs->get_config('Charset'), 0, 3)) == 'iso' ? false : true;
        parent::__construct('P', 'mm', $paperSize, $unicode, $whmcs->get_config('Charset'), false);
        $this->SetCreator('WHMCS');
        $this->SetAuthor($whmcs->get_config('CompanyName'));
        $this->SetMargins(15, 25, 15);
        $this->SetFooterMargin(15);
        $this->SetAutoPageBreak(TRUE, 25);
        $this->setLanguageArray($l);
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
    }
    /**
     * Sets the font used to print character strings
     *
     * Overrides the family with admin configured PDF font setting
     * If unavailable, uses requested font family
     * If neither are found, reverts to TCPDF's default font
     *
     * @todo Remove the enforced override of the font family
     *
     * @param string $family
     * @param string $style
     * @param string $size
     * @param string $fontfile
     * @param string $subset
     * @param boolean $out
     */
    public function SetFont($family, $style = '', $size = null, $fontfile = '', $subset = 'default', $out = true)
    {
        $adminFontSetting = WHMCS_Application::getinstance()->get_config('TCPDFFont');
        if( in_array($adminFontSetting, $this->fontlist) )
        {
            $familyOverride = $adminFontSetting;
        }
        else
        {
            if( in_array($family, $this->fontList) )
            {
                $familyOverride = $family;
            }
            else
            {
                $familyOverride = PDF_FONT_NAME_MAIN;
            }
        }
        parent::setfont($familyOverride, $style, $size, $fontfile, $subset, $out);
    }
}