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
 * Mail factory that wraps the PHPMailer library
 *
 * @package    WHMCS
 * @author     WHMCS Limited <development@whmcs.com>
 * @copyright  Copyright (c) WHMCS Limited 2013
 * @license    http://www.whmcs.com/license/ WHMCS Eula
 * @version    $Id$
 * @link       http://www.whmcs.com/
 */
class WHMCS_Mail extends PHPMailer
{
    protected $decodeAltBodyOnSend = true;
    protected static $validEncodings = array( '8bit', '7bit', 'binary', 'base64', 'quoted-printable' );
    /**
     * Construct the Mail instance.
     *
     * @param string $name - The name the mail should be sent from
     * @param string $email - The email the mail should be sent from
     */
    public function __construct($name = '', $email = '')
    {
        $whmcs = WHMCS_Application::getinstance();
        $whmcsAppConfig = $whmcs->getApplicationConfig();
        parent::__construct(true);
        if( !$name )
        {
            $name = $whmcs->get_config('CompanyName');
        }
        if( !$email )
        {
            $email = $whmcs->get_config('Email');
        }
        $this->From = $email;
        $this->FromName = WHMCS_Input_Sanitize::decode($name);
        if( $whmcs->get_config('MailType') == 'mail' )
        {
            $this->Mailer = 'mail';
        }
        else
        {
            if( $whmcs->get_config('MailType') == 'smtp' )
            {
                $this->IsSMTP();
                $this->Host = $whmcs->get_config('SMTPHost');
                $this->Port = $whmcs->get_config('SMTPPort');
                $this->Hostname = $this->serverHostname();
                if( $whmcs->get_config('SMTPSSL') )
                {
                    $this->SMTPSecure = $whmcs->get_config('SMTPSSL');
                }
                if( $whmcs->get_config('SMTPUsername') )
                {
                    $this->SMTPAuth = true;
                    $this->Username = $whmcs->get_config('SMTPUsername');
                    $this->Password = decrypt($whmcs->get_config('SMTPPassword'));
                }
                $this->Sender = $this->From;
                if( $email != $whmcs->get_config('SMTPUsername') )
                {
                    $this->AddReplyTo($email, $name);
                }
                if( $whmcsAppConfig['smtp_debug'] )
                {
                    $this->SMTPDebug = true;
                }
            }
        }
        $this->XMailer = $whmcs->get_config('CompanyName');
        $this->CharSet = $whmcs->get_config('Charset');
        $this->setEncoding($whmcs->get_config('MailEncoding'));
    }
    /**
     * Get the current "server's" hostname
     *
     * First use the standard PhpMailer logic. If that returns something
     * obviously wrong, use the configured domain.
     *
     * @return string;
     */
    protected function serverHostname()
    {
        $hostname = parent::serverhostname();
        if( !$hostname || ($hostname = "localhost.localdomain") )
        {
            $hostname = parse_url(WHMCS_Application::getinstance()->get_config('Domain'), PHP_URL_HOST);
        }
        return (bool) $hostname;
    }
    /**
     * Get valid message encoding types
     *
     * @return string[]
     */
    public static function getValidEncodings()
    {
        return self::$validEncodings;
    }
    /**
     * Set message encoding
     *
     * Sets the message encoding type if valid
     *
     * If an invalid, blank, null, or no setting is passed, Encoding will
     * default to '8bit'
     *
     * @param integer $config_value Defaults to 0 (8bit) if empty
     */
    protected function setEncoding($config_value = 0)
    {
        $validEncodings = self::$validEncodings;
        if( isset($config_value) && !empty($validEncodings[$config_value]) )
        {
            $this->Encoding = $validEncodings[$config_value];
        }
        else
        {
            $this->Encoding = $validEncodings[0];
        }
    }
    /**
     * {@inheritDoc}
     */
    protected function addAnAddress($kind, $address, $name = '')
    {
        return parent::addanaddress($kind, trim($address), WHMCS_Input_Sanitize::decode($name));
    }
    /**
     * Create a message and send it.
     * Uses the sending method specified by $whmcs->get_config('MailType').
     *
     * @return bool
     */
    public function send()
    {
        $this->Subject = WHMCS_Input_Sanitize::decode($this->Subject);
        if( $this->decodeAltBodyOnSend )
        {
            $this->AltBody = WHMCS_Input_Sanitize::decode($this->AltBody);
        }
        return parent::send();
    }
    /**
     * Set email body message
     *
     * Intelligently defines the email message body based on input
     * parameters - if plain text only fully entity-decodes message
     *
     * @param string $plainText Plain-text content for email
     * @param string $HTMLMessage (Optional) HTML formatted version
     *
     * @return string The message that was set
     */
    public function setMessage($plainText, $HTMLMessage = '')
    {
        $plainText = WHMCS_Input_Sanitize::decode($plainText);
        if( $HTMLMessage )
        {
            $plainText = str_replace("<p>", '', $plainText);
            $plainText = str_replace("</p>", "\n\n", $plainText);
            $plainText = str_replace("<br>", "\n", $plainText);
            $plainText = str_replace("<br />", "\n", $plainText);
        }
        $plainText = strip_tags($plainText);
        $this->decodeAltBodyOnSend = false;
        if( $HTMLMessage )
        {
            $formattedHTMLMessage = $this->applyCSSFormatting($HTMLMessage);
            $this->Body = $formattedHTMLMessage;
            $this->AltBody = $plainText;
            return $formattedHTMLMessage;
        }
        $this->Body = $plainText;
        return nl2br($plainText);
    }
    /**
     * Prefix CSS Styling rules as defined in WHMCS General Settings
     *
     * @param string $HTMLMessage
     *
     * @return string
     */
    protected function applyCSSFormatting($HTMLMessage)
    {
        $whmcs = WHMCS_Application::getinstance();
        $emailCSSStyling = $whmcs->get_config('EmailCSS');
        if( $emailCSSStyling )
        {
            $HTMLMessage = "<style>\n" . $emailCSSStyling . "\n</style>" . $HTMLMessage;
        }
        return $HTMLMessage;
    }
}