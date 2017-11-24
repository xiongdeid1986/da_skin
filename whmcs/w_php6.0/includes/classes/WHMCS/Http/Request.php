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
 * Request represents an HTTP request.
 *
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @contributor WHMCS
 */
class WHMCS_Http_Request
{
    protected static $trustedProxies = array(  );
    protected static $trustedHostPatterns = array(  );
    protected static $trustedHosts = array(  );
    protected static $trustedHeaders = array( self::HEADER_CLIENT_IP => 'X_FORWARDED_FOR', self::HEADER_CLIENT_HOST => 'X_FORWARDED_HOST', self::HEADER_CLIENT_PROTO => 'X_FORWARDED_PROTO', self::HEADER_CLIENT_PORT => 'X_FORWARDED_PORT' );
    protected $headers = array(  );
    protected $server = array(  );
    const HEADER_CLIENT_IP = 'client_ip';
    const HEADER_CLIENT_HOST = 'client_host';
    const HEADER_CLIENT_PROTO = 'client_proto';
    const HEADER_CLIENT_PORT = 'client_port';
    /**
     * Constructor
     *
     * @param array $server $_SERVER array to gather header related information
     */
    public function __construct($server = array(  ))
    {
        if( !isset($server['REMOTE_ADDR']) )
        {
            $server['REMOTE_ADDR'] = '';
        }
        foreach( $server as $key => $value )
        {
            if( strpos($key, 'HTTP') === 0 )
            {
                $key = substr($key, 5);
                $this->headers[$key] = $value;
            }
            $this->server[$key] = $value;
        }
    }
    /**
     * Sets a list of trusted proxies.
     *
     * You should only list the reverse proxies that you manage directly.
     *
     * @param array $proxies A list of trusted proxies
     */
    public static function setTrustedProxies($proxies)
    {
        self::$trustedProxies = $proxies;
    }
    /**
     * Gets the list of trusted proxies.
     *
     * @return array An array of trusted proxies.
     */
    public static function getTrustedProxies()
    {
        return self::$trustedProxies;
    }
    /**
     * Sets the name for trusted headers.
     *
     * The following header keys are supported:
     *
     *  * Request::HEADER_CLIENT_IP:    defaults to X-Forwarded-For   (see getClientIp())
     *  * Request::HEADER_CLIENT_HOST:  defaults to X-Forwarded-Host  (see getClientHost())
     *  * Request::HEADER_CLIENT_PORT:  defaults to X-Forwarded-Port  (see getClientPort())
     *  * Request::HEADER_CLIENT_PROTO: defaults to X-Forwarded-Proto (see getScheme() and isSecure())
     *
     * Setting an empty value allows to disable the trusted header for the given key.
     *
     * @param string $key   The header key
     * @param string $value The header name
     *
     * @throws \InvalidArgumentException
     */
    public static function setTrustedHeaderName($key, $value)
    {
        if( !array_key_exists($key, self::$trustedHeaders) )
        {
            throw new InvalidArgumentException(sprintf("Unable to set the trusted header name for key \"%s\".", $key));
        }
        self::$trustedHeaders[$key] = $value;
    }
    /**
     * Gets the trusted proxy header name.
     *
     * @param string $key The header key
     *
     * @return string The header name
     *
     * @throws \InvalidArgumentException
     */
    public static function getTrustedHeaderName($key)
    {
        if( !array_key_exists($key, self::$trustedHeaders) )
        {
            throw new InvalidArgumentException(sprintf("Unable to get the trusted header name for key \"%s\".", $key));
        }
        return self::$trustedHeaders[$key];
    }
    /**
     * Returns the client IP addresses.
     *
     * In the returned array the most trusted IP address is first, and the
     * least trusted one last. The 'real' client IP address is the last one,
     * but this is also the least trusted one. Trusted proxies are stripped.
     *
     * Use this method carefully; you should use getClientIp() instead.
     *
     * @return array The client IP addresses
     *
     * @see getClientIp()
     */
    public function getClientIps()
    {
        $ip = $this->server['REMOTE_ADDR'];
        if( !self::$trustedProxies )
        {
            return array( $ip );
        }
        if( !isset(self::$trustedHeaders[self::HEADER_CLIENT_IP]) || empty($this->headers[self::$trustedHeaders[self::HEADER_CLIENT_IP]]) )
        {
            return array( $ip );
        }
        $clientIps = array_map('trim', explode(',', $this->headers[self::$trustedHeaders[self::HEADER_CLIENT_IP]]));
        $clientIps[] = $ip;
        $ip = $clientIps[0];
        foreach( $clientIps as $key => $clientIp )
        {
            if( WHMCS_Http_IpUtils::checkip($clientIp, self::$trustedProxies) )
            {
                unset($clientIps[$key]);
            }
        }
        return $clientIps ? array_reverse($clientIps) : array( $ip );
    }
    /**
     * Returns the client IP address.
     *
     * This method can read the client IP address from the 'X-Forwarded-For' header
     * when trusted proxies were set via "setTrustedProxies()". The 'X-Forwarded-For'
     * header value is a comma+space separated list of IP addresses, the left-most
     * being the original client, and each successive proxy that passed the request
     * adding the IP address where it received the request from.
     *
     * If your reverse proxy uses a different header name than 'X-Forwarded-For',
     * ('Client-Ip' for instance), configure it via "setTrustedHeaderName()" with
     * the 'client-ip' key.
     *
     * @return string The client IP address
     *
     * @see defineProxyTrustFromApplication()
     * @see getClientIps()
     * @see http://en.wikipedia.org/wiki/X-Forwarded-For
     */
    public function getClientIp()
    {
        $ipAddresses = $this->getClientIps();
        return $ipAddresses[0];
    }
    /**
     * Set static internals for Admin defined proxies
     *
     * This methods should be invoked within the application prior to fetching
     * the IP {@see getClientIp()}.  Otherwise, the IP reported by this class
     * is simply $_SERVER['REMOTE_ADDR'] (which may not be the actual value
     * the admin wants, since that value may be a proxy on the edge of his
     * network)
     *
     * @param WHMCS_Application $whmcs
     */
    public static function defineProxyTrustFromApplication($whmcs)
    {
        $trustedIps = array(  );
        $proxyHeader = $whmcs->get_config('proxyHeader');
        $trustedHeader = $proxyHeader ? $proxyHeader : 'X_FORWARDED_FOR';
        self::settrustedheadername(WHMCS_Http_Request::HEADER_CLIENT_IP, $trustedHeader);
        $adminDefinedProxies = $whmcs->get_config('trustedProxyIps');
        $adminDefinedProxies = json_decode($adminDefinedProxies, true);
        if( !is_array($adminDefinedProxies) )
        {
            $adminDefinedProxies = array(  );
        }
        foreach( $adminDefinedProxies as $proxyDefinition )
        {
            $trustedIps[] = $proxyDefinition['ip'];
        }
        self::settrustedproxies($trustedIps);
    }
}