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
 * Class WHMCS_Hash_Password
 *
 * Use hashing appropriate for cryptographic purposes but also respect product
 * minimum requirements, PHP version, and available algorithms.
 *
 * As of PHP 5.3.7, it should be valid to utilize the compat lib ({@link
 * https://github.com/ircmaxell/password_compat}), so when the product requires
 * that PHP version a lot of the 'fallback' logic in this class can be replaced/
 * removed (as well as, consideration should be given for an upgrade path or
 * prevention of cryptographically deficient algorithms)
 *
 * Currently the 'fallback' is to utilize the phpseclib's
 * ({@link http://phpseclib.sourceforge.net/}) HMAC
 * ({@link http://en.wikipedia.org/wiki/Hash-based_message_authentication_code})
 * compatability layer; utilizing the SHA256 algorithm.  This is done because
 * these environments may not have crypt() compiled with all of the following:
 *    * a sufficiently strong algorithm
 *    * salt option with that _sufficiently_ strong algorithm
 *    * bug-free implementation of the algorithm (with the use of salt)
 * So, to keep from having _many_ different hashing algorithms in the wild, all
 * bound to whatever the variation of _usable_ crypt() in a give environment is,
 * we only differentiate between those that haven't got what it takes to do real
 * crypto and those that we can can do _acceptable_ HMAC hashing.
 *
 * NOTE: this is observable in the hashed value by a common idiomatic
 * expression: hash value followed by a colon followed by the key, i.e.:
 *   abcdefghijklmnopqrstuvwxyz78901234567890:somekey
 *
 *
 * For systems that first see this code executed, they stored hash values will
 * be simple MD5 messages, without HMAC (ie, no HMAC key, intermediate salt,
 * or crypto salt, just plain ol' message digest).  These simple MD5 values will
 * be stored as simple hashes, without prefixes (because prefix are not provided
 * when using md5() [or hash_hmac() for that matter])
 *
 * @link http://php.net/manual/en/function.crypt.php PHP Manual entry for crypt()
 * @link http://php.net/manual/en/function.hash-hmac.php PHP Manual entry related to HMAC
 * @link http://en.wikipedia.org/wiki/Hash-based_message_authentication_code HMAC
 * @link http://blog.ircmaxell.com/2012/12/seven-ways-to-screw-up-bcrypt.html Implementing bcrypt
 *
 * @copyright Copyright (c) WHMCS Limited 2005-2014
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */
class WHMCS_Security_Hash_Password
{
    protected $useHmac = false;
    protected $defaultHashAlgorithm = 1;
    protected $defaultHashOptions = array(  );
    protected $infoCache = array(  );
    const HASH_MD5 = 'plain-MD5';
    const HASH_HMAC_SHA256 = 'HMAC-SHA256';
    const HASH_BCRYPT = 'bcrypt';
    const HASH_UNKNOWN = 'unknown';
    const PATTERN_MD5 = "/^[a-f0-9]{32}\$/i";
    const PATTERN_HMAC_SHA256 = "/^[a-f0-9]{64}(?::(.+))\$/i";
    const PATTERN_BCRYPT = "/^(\\\$2[axy]|\\\$2)\\\$[0-9]{0,2}?\\\$([a-z0-9\\/.]{22})[a-z0-9\\/.]{31}\$/i";
    /**
     * Hashing object constructor
     *
     * * Ensure some global constants related to password hashing are defined
     * * Determines if password compat/native is appropriate for hashing
     *
     *
     * @param bool $useHmac Force the use of HMAC Digest Messaging over
     * compat/native password_* functions; default is based on PHP environment
     */
    public function __construct($useHmac = false)
    {
        if( !defined('PASSWORD_BCRYPT') )
        {
            define('PASSWORD_BCRYPT', 1);
        }
        if( !defined('PASSWORD_DEFAULT') )
        {
            define('PASSWORD_DEFAULT', PASSWORD_BCRYPT);
        }
        $this->defaultHashAlgorithm = PASSWORD_BCRYPT;
        $this->defaultHashOptions = array(  );
        if( !empty($useHmac) || version_compare(PHP_VERSION, "5.3.7", "<") )
        {
            $this->useHmac = true;
        }
    }
    /**
     * Production safe wrapper for password_verify()
     *
     * @see password_verify()
     *
     * @throws RuntimeException
     *
     * @param string $input Raw secret provided by user/caller
     * @param string $storedHash Previously hashed secret
     *
     * @return bool True if $input, when hash properly, is the same as $storedHash
     */
    public function verify($input, $storedHash)
    {
        $info = $this->getInfo($storedHash);
        switch( $info['algoName'] )
        {
            case self::HASH_MD5:
                $result = $this->verifyMd5($input, $storedHash);
                break;
            case self::HASH_HMAC_SHA256:
                $result = $this->verifyHmacSha256($input, $storedHash);
                break;
            case self::HASH_BCRYPT:
                $result = password_verify($input, $storedHash);
                break;
            default:
                throw new RuntimeException(sprintf("Calculated algorithm \"%s\" is not supported", $info['algoName']));
                break;
        }
        return $result;
    }
    /**
     * Production safe wrapper for password_hash()
     *
     * NOTE: If HMAC-only environment or if $algorithm is HMAC, it will be
     * honored.  if $algorithm is not passed or is not supported by
     * password compat/native, it will automatically be assigned the default;
     * {@see defaultHashAlgorithm()}.
     *
     * @see defaultHashAlorithm()
     * @see defaultHashOptions()
     * @see password_hash()
     *
     * @param string $input Secret to hash
     * @param string $algorithm Supported algorithm
     * @param array $options
     *
     * @return false|string Hashed $input or false on error
     */
    public function hash($input, $algorithm = '', $options = array(  ))
    {
        if( $this->useHmac || $algorithm == self::HASH_HMAC_SHA256 )
        {
            return $this->hmacHash($input);
        }
        if( !($algorithm || in_array($algorithm, array( self::HASH_BCRYPT ))) )
        {
            $algorithm = $this->defaultHashAlgorithm;
        }
        if( empty($options) )
        {
            $options = $this->defaultHashOptions;
        }
        return password_hash($input, $algorithm, $options);
    }
    /**
     * Production safe wrapper for password_get_info()
     *
     * @see password_get_info()
     *
     * @param string $hash Hashed value to evaluate
     *
     * @return array
     */
    public function getInfo($hash)
    {
        if( isset($this->infoCache[$hash]) )
        {
            return $this->infoCache[$hash];
        }
        $info = array( 'algo' => 0, 'algoName' => self::HASH_UNKNOWN, 'options' => array(  ) );
        if( strpos($hash, "\$") === 0 && $this->useHmac == false )
        {
            $info = password_get_info($hash);
        }
        else
        {
            $matches = array(  );
            if( preg_match(self::PATTERN_HMAC_SHA256, $hash, $matches) )
            {
                $info['algoName'] = self::HASH_HMAC_SHA256;
                $info['options']['salt'] = $matches[1];
            }
            else
            {
                if( preg_match(self::PATTERN_MD5, $hash) )
                {
                    $info['algoName'] = self::HASH_MD5;
                }
                else
                {
                    if( preg_match(self::PATTERN_BCRYPT, $hash, $matches) )
                    {
                        $info['algoName'] = self::HASH_BCRYPT;
                        $info['options']['salt'] = $matches[2];
                    }
                }
            }
        }
        $this->infoCache[$hash] = $info;
        return $info;
    }
    /**
     * Production safe wrapper for password_needs_rehash()
     *
     * @see defaultHashAlgorithm()
     * @see defaultHashOptions()
     * @see password_needs_rehash()
     *
     * @throws RuntimeException When $hash cannot be properly evaluated
     *
     * @param string $hash Hashed value to inspect
     * @param string $algorithm Algorithm to hunt/compare against within $hash
     * @param array $options Options to hunt/compare against within $hash
     *
     * @return bool False if $algorithm & $options were used to create $hash
     */
    public function needsRehash($hash, $algorithm = '', $options = array(  ))
    {
        $info = $this->getInfo($hash);
        switch( $info['algoName'] )
        {
            case self::HASH_MD5:
                $result = true;
                break;
            case self::HASH_HMAC_SHA256:
                if( $algorithm == self::HASH_HMAC_SHA256 )
                {
                    return false;
                }
                if( empty($algorithm) )
                {
                    $result = $this->useHmac ? false : true;
                }
                else
                {
                    $result = true;
                }
                break;
            case self::HASH_BCRYPT:
                if( !$algorithm )
                {
                    $algorithm = $this->defaultHashAlgorithm;
                }
                if( empty($options) )
                {
                    $options = $this->defaultHashOptions;
                }
                if( $this->useHmac )
                {
                    $result = true;
                }
                else
                {
                    $result = password_needs_rehash($hash, $algorithm, $options);
                }
                break;
            default:
                throw new RuntimeException(sprintf("Calculated algorithm \"%s\" is not supported", $info['algoName']));
                break;
        }
        return $result;
    }
    /**
     * Hash using a SHA256 Algorithm and random key
     *
     * Random key will be returned as part of hashed value:
     * ```somecrazylonghashvalue:generatedkey```
     *
     * NOTE: a key can be provided, but in most cases it's best to let one be
     * generated (by a sophisticated & smart pseudo generator called within this
     * function)
     *
     * @param string $input Secret to hash
     * @param string $key Optional random key to provide in HMAC digest process
     *
     * @return bool|string Hashed value or false on error
     */
    protected function hmacHash($input, $key = '')
    {
        if( !$key )
        {
            $key = bin2hex(crypt_random_string(16));
        }
        $hasher = new Crypt_Hash('sha256');
        $hasher->setKey($key);
        $hashedInput = $hasher->hash($input);
        if( empty($hashedInput) )
        {
            return false;
        }
        return bin2hex($hashedInput) . ":" . $key;
    }
    /**
     * Verify input against a known SHA256-HMAC hash value with key
     *
     * @param string $input Raw input
     * @param string $storedHash Previous SHA256-HMAC hashed value with key
     *
     * @return bool True if input, using previous key, generates the previous hash
     */
    protected function verifyHmacSha256($input, $storedHash)
    {
        list($hashSecret, $hashKey) = explode(":", $storedHash);
        $hashedInput = $this->hmacHash($input, $hashKey);
        return $this->assertBinarySameness($hashedInput, $storedHash);
    }
    /**
     * Verify input against a known MD5 hash value
     *
     * @param string $input Raw input
     * @param string $storedHash Previous plain MD5 hashed value
     *
     * @return bool True if input generates the previous hash
     */
    protected function verifyMd5($input, $storedHash)
    {
        return $this->assertBinarySameness(md5($input), $storedHash);
    }
    /**
     * Binary & timing-attack save comparison of two strings
     *
     * @param string $hashedInput
     * @param string $storedHash
     *
     * @return bool If the two string are the same
     */
    public function assertBinarySameness($hashedInput, $storedHash)
    {
        if( !is_string($hashedInput) || !is_string($storedHash) || WHMCS_Utility_Binary::strlen($hashedInput) != WHMCS_Utility_Binary::strlen($storedHash) || WHMCS_Utility_Binary::strlen($hashedInput) <= 16 )
        {
            return false;
        }
        $status = 0;
        for( $i = 0; $i < WHMCS_Utility_Binary::strlen($hashedInput); $i++ )
        {
            $status |= ord($hashedInput[$i]) ^ ord($storedHash[$i]);
        }
        return $status === 0 ? true : false;
    }
}