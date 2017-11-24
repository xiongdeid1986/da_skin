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
 * Http utility functions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class WHMCS_Http_IpUtils
{
    /**
     * This class should not be instantiated
     */
    private function __construct()
    {
    }
    /**
     * Checks if an IPv4 or IPv6 address is contained in the list of given IPs or subnets
     *
     * @param string       $requestIp   IP to check
     * @param string|array $ips         List of IPs or subnets (can be a string if only a single one)
     *
     * @return bool    Whether the IP is valid
     */
    public static function checkIp($requestIp, $ips)
    {
        if( !is_array($ips) )
        {
            $ips = array( $ips );
        }
        $method = false !== strpos($requestIp, ":") ? 'checkIp6' : 'checkIp4';
        foreach( $ips as $ip )
        {
            if( self::$method($requestIp, $ip) )
            {
                return true;
            }
        }
        return false;
    }
    /**
     * Compares two IPv4 addresses.
     * In case a subnet is given, it checks if it contains the request IP.
     *
     * @param string $requestIp IPv4 address to check
     * @param string $ip        IPv4 address or subnet in CIDR notation
     *
     * @return bool    Whether the IP is valid
     */
    public static function checkIp4($requestIp, $ip)
    {
        if( false !== strpos($ip, '/') )
        {
            list($address, $netmask) = explode('/', $ip, 2);
            if( $netmask < 1 || 32 < $netmask )
            {
                return false;
            }
        }
        else
        {
            $address = $ip;
            $netmask = 32;
        }
        return 0 === substr_compare(sprintf("%032b", ip2long($requestIp)), sprintf("%032b", ip2long($address)), 0, $netmask);
    }
    /**
     * Compares two IPv6 addresses.
     * In case a subnet is given, it checks if it contains the request IP.
     *
     * @author David Soria Parra <dsp at php dot net>
     * @see https://github.com/dsp/v6tools
     *
     * @param string $requestIp IPv6 address to check
     * @param string $ip        IPv6 address or subnet in CIDR notation
     *
     * @return bool    Whether the IP is valid
     *
     * @throws \RuntimeException When IPV6 support is not enabled
     */
    public static function checkIp6($requestIp, $ip)
    {
        if( !(extension_loaded('sockets') && defined('AF_INET6') || @inet_pton("::1")) )
        {
            throw new RuntimeException("Unable to check Ipv6. Check that PHP was not compiled with option \"disable-ipv6\".");
        }
        if( false !== strpos($ip, '/') )
        {
            list($address, $netmask) = explode('/', $ip, 2);
            if( $netmask < 1 || 128 < $netmask )
            {
                return false;
            }
        }
        else
        {
            $address = $ip;
            $netmask = 128;
        }
        $bytesAddr = unpack("n*", inet_pton($address));
        $bytesTest = unpack("n*", inet_pton($requestIp));
        $i = 1;
        for( $ceil = ceil($netmask / 16); $i <= $ceil; $i++ )
        {
            $left = $netmask - 16 * ($i - 1);
            $left = $left <= 16 ? $left : 16;
            $mask = ~(65535 >> $left) & 65535;
            if( ($bytesAddr[$i] & $mask) != ($bytesTest[$i] & $mask) )
            {
                return false;
            }
        }
        return true;
    }
}