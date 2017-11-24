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
 * WHMCS Google Charts Class
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2005-2014
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Chart
{
    public $chartcount = 0;
    public function __construct()
    {
    }
    public function drawChart($type, $data, $args = array(  ), $height = '300px', $width = "100%")
    {
        global $aInt;
        $datafunc = !is_array($data) ? $data : '';
        if( $datafunc && !function_exists('json_encode') )
        {
            return "JSON appears to be missing from your PHP build and is required for graphs to function. Please recompile PHP with JSON included and then try again.";
        }
        if( $datafunc && isset($_POST['chartdata']) && $_POST['chartdata'] == $datafunc )
        {
            if( function_exists('chartdata_' . $datafunc) )
            {
                $chartdata = call_user_func('chartdata_' . $datafunc);
                foreach( $chartdata['cols'] as $k => $col )
                {
                    if( isset($chartdata['cols'][$k]['label']) )
                    {
                        $chartdata['cols'][$k]['label'] = strval($chartdata['cols'][$k]['label']);
                    }
                }
                echo json_encode($chartdata);
                exit();
            }
            exit( "Function Not Found" );
        }
        if( $this->chartcount == 0 )
        {
            if( is_string($aInt->headOutput) )
            {
                $aInt->headOutput .= "";
            }
            else
            {
                if( is_array($aInt->headOutput) )
                {
                    $aInt->addHeadOutput("");
                }
            }
        }
        $this->chartcount++;
        $options = array(  );
        if( !isset($args['legendpos']) )
        {
            $args['legendpos'] = 'top';
        }
        $options[] = "legend: {position: \"" . $args['legendpos'] . "\"}";
        if( isset($args['title']) )
        {
            $options[] = "title: '" . $args['title'] . "'";
        }
        if( isset($args['xlabel']) )
        {
            $options[] = "hAxis: {title: \"" . $args['xlabel'] . "\"}";
        }
        $vaxis = array(  );
        if( isset($args['ylabel']) )
        {
            $vaxis[] = "title: \"" . $args['ylabel'] . "\"";
        }
        if( isset($args['minyvalue']) )
        {
            $vaxis[] = "minValue: \"" . $args['minyvalue'] . "\"";
        }
        if( isset($args['maxyvalue']) )
        {
            $vaxis[] = "maxValue: \"" . $args['maxyvalue'] . "\"";
        }
        if( isset($args['gridlinescount']) )
        {
            $vaxis[] = "gridlines: {count:" . $args['gridlinescount'] . "}";
        }
        if( isset($args['minorgridlinescount']) )
        {
            $vaxis[] = "minorGridlines: {color:\"#efefef\",count:" . $args['minorgridlinescount'] . "}";
        }
        if( count($vaxis) )
        {
            $options[] = "vAxis: {" . implode(',', $vaxis) . "}";
        }
        if( $args['colors'] )
        {
            $colors = $args['colors'];
            $colors = explode(',', $colors);
            foreach( $colors as $i => $color )
            {
                $colors[$i] = "\"" . $color . "\"";
            }
            $options[] = "colors: [" . implode(',', $colors) . "]";
        }
        if( $args['chartarea'] )
        {
            $chartarea = explode(',', $args['chartarea']);
            $options[] = "chartArea: {left:" . $chartarea[0] . ",top:" . $chartarea[1] . ",width:\"" . $chartarea[2] . "\",height:\"" . $chartarea[3] . "\"}";
        }
        if( isset($args['stacked']) && $args['stacked'] )
        {
            $options[] = "isStacked: true";
        }
        $output = "\n            <script type=\"text/javascript\">\n            function drawChart" . $this->chartcount . "() {";
        if( $datafunc )
        {
            $output .= "\n            var jsonData = \$.ajax({\n                url: \"" . $_SERVER['PHP_SELF'] . "\",\n                type: \"POST\",\n                data: \"chartdata=" . $datafunc . "\",\n                dataType:\"json\",\n                async: false\n            }).responseText;\n            ";
        }
        else
        {
            foreach( $data['cols'] as $k => $col )
            {
                if( isset($data['cols'][$k]['label']) )
                {
                    $data['cols'][$k]['label'] = strval($data['cols'][$k]['label']);
                }
            }
            foreach( $data['rows'] as $k => $row )
            {
                if( isset($data['rows'][$k]['c']) )
                {
                    $data['rows'][$k]['c'][0]['v'] = strval($data['rows'][$k]['c'][0]['v']);
                    $data['rows'][$k]['c'][1]['v'] = floatval($data['rows'][$k]['c'][1]['v']);
                    if( !empty($data['rows'][$k]['c'][1]['f']) )
                    {
                        $data['rows'][$k]['c'][1]['f'] = strval($data['rows'][$k]['c'][1]['f']);
                    }
                    else
                    {
                        unset($data['rows'][$k]['c'][1]['f']);
                    }
                }
            }
            if( version_compare(PHP_VERSION, "5.3.0", ">=") )
            {
                $sanitizedData = json_encode($data, JSON_HEX_APOS);
            }
            else
            {
                $sanitizedData = str_replace("'", "\\u0027", json_encode($data));
            }
            $output .= "\n                var jsonData = '" . $sanitizedData . "';\n            ";
        }
        $output .= "\n                        }\n        </script>\n        <div id=\"chartcont" . $this->chartcount . "\" style=\"width:" . $width . ";height:" . $height . ";\"><div style=\"padding-top:" . round($height / 2 - 10, 0) . "px;text-align:center;\"><img src=\"images/loading.gif\" /> Loading...</div></div>\n        ";
        $aInt->chartFunctions[] = 'drawChart' . $this->chartcount;
        return $output;
    }
}