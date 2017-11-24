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
abstract class WHMCS_Version_AbstractVersion
{
    protected $major = '';
    protected $minor = '';
    protected $patch = '';
    protected $preReleaseIdentifier = '';
    protected $preReleaseRevision = '';
    protected $buildTag = '';
    protected $casualNames = array( 'release' => '', 'rc' => 'RC', 'beta' => 'Beta' );
    protected $version = "0.0.0";
    protected $data = array(  );
    const DEFAULT_PRERELEASE_IDENTIFIER = 'release';
    const DEFAULT_PRERELEASE_REVISION = '1';
    /**
     * @var array
     */
    public function __construct($version)
    {
        $this->setVersion($version);
    }
    /**
     * Is version number provided valid
     *
     * @param string $version String to validate
     *
     * @return boolean
     */
    abstract public function isValid($version);
    abstract public function parse($version);
    /**
     * Retrieve the version number
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
    /**
     * Set the version number
     *
     * @param $version
     *
     * @return $this
     * @throws WHMCS_Exception_Version_BadVersionNumber if string is not valid
     */
    public function setVersion($version)
    {
        if( !$this->isValid($version) )
        {
            throw new WHMCS_Exception_Version_BadVersionNumber(sprintf("'%s' is not a valid version number.", $version));
        }
        $this->version = $version;
        $this->parse($version);
        return $this;
    }
    /**
     * Retrieve a normalized version string, which would include at minimum
     * the Major, Minor, and Patch segments.
     *
     * Different concrete implementations of this abstract class may, by their
     * own specification, provide something beyond that.
     *
     * Example:
     *  "1.2.3"
     *
     * @return string
     */
    public function getCanonical()
    {
        $version = sprintf("%d.%d.%d", $this->getMajor(), $this->getMinor(), $this->getPatch());
        return $version;
    }
    /**
     * Retrieve a version string that most people would understand or look
     * more like what is envisioned when using the real version number in
     * conversations
     *
     * Example:
     *   "1.2.3 Beta", "1.2.3 RC1", "1.2.3 RC2", "1.2.3"
     *      vs the canonical
     *   "1.2.3-beta.1", "1.2.3-rc.1", "1.2.3-rc.2", "1.2.3-release.1"
     *
     * @return string
     */
    public function getCasual()
    {
        $version = sprintf("%d.%d.%d", $this->getMajor(), $this->getMinor(), $this->getPatch());
        $label = $this->getPreReleaseIdentifier();
        if( !empty($this->casualNames[$label]) )
        {
            $version .= " " . $this->casualNames[$label];
            if( 0 < $this->getPreReleaseRevision() )
            {
                $version .= $this->getPreReleaseRevision();
            }
        }
        return $version;
    }
    /**
     * Retrieve the major version info
     *
     * @return string
     */
    public function getMajor()
    {
        return $this->major;
    }
    /**
     * Retrieve the minor version info
     *
     * @return string
     */
    public function getMinor()
    {
        return $this->minor;
    }
    /**
     * Retrieve the patch version info
     *
     * @return string
     */
    public function getPatch()
    {
        return $this->patch;
    }
    /**
     * Retrieve the pre-release (label) version info
     *
     * @return string
     */
    public function getPreReleaseIdentifier()
    {
        return $this->preReleaseIdentifier;
    }
    /**
     * Retrieve the pre-release revision version info
     *
     * @return string
     */
    public function getPreReleaseRevision()
    {
        return $this->preReleaseRevision;
    }
    /**
     * Retrieve the pre-release revision version info
     *
     * @return string
     */
    public function getBuildTag()
    {
        return $this->buildTag;
    }
    /**
     * Set Major segment of version string
     *
     * @param string $data
     *
     * @return $this
     */
    public function setMajor($data)
    {
        $this->major = $data;
        return $this;
    }
    /**
     * Set Minor segment of version string
     *
     * @param string $data
     *
     * @return $this
     */
    public function setMinor($data)
    {
        $this->minor = $data;
        return $this;
    }
    /**
     * Set Patch segment of version string
     *
     * @param string $data
     *
     * @return $this
     */
    public function setPatch($data)
    {
        $this->patch = $data;
        return $this;
    }
    /**
     * Set Pre-Release label segment of version string
     *
     * @param string $data
     *
     * @return $this
     */
    public function setPreReleaseIdentifier($data)
    {
        $this->preReleaseIdentifier = $data;
        return $this;
    }
    /**
     * Set Pre-Release Revision segment of version string
     *
     * @param string $data
     *
     * @return $this
     */
    public function setPreReleaseRevision($data)
    {
        $this->preReleaseRevision = $data;
        return $this;
    }
    /**
     * Set Build Tag segment of version string
     *
     * @param string $data
     *
     * @return $this
     */
    public function setBuildTag($data)
    {
        $this->buildTag = $data;
        return $this;
    }
}