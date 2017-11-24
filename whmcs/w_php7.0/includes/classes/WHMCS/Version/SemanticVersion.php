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
 * Class WHMCS_Version_SemanticVersion
 *
 * NOTE: this is not exact to SemVer 2.0.0, but a subset, namely:
 * It will not truthfully honor all permutations of a build identifier (denote with
 * a leading +) in a given version number
 *
 * The ideal uses should one of the following examples:
 *  - major.minor.patch-preReleaseLabel.preReleaseRevision+buildTag
 *    "1.0.0-beta.1+ciadijw323ksdfljLikelyGitSha"
 *  - major.minor.patch-preReleaseLabel.preReleaseRevision
 *    "1.0.0-rc.2"
 *  - major.minor.patch-preReleaseLabel
 *    "1.0.0-beta" (equivalent to "1.0.0-beta.1"
 *  - major.minor.patch
 *    "1.0.0" (takes precedence over (ie, ">") any other 1.0.0 that has a
 *    preReleaseLabel, like "1.0.0-rc.4")
 *
 * Please do NOT place a build identifer
 *
 * @link http://semver.org/spec/v2.0.0.html Semantic Versioning 2.0.0
 */
class WHMCS_Version_SemanticVersion extends WHMCS_Version_AbstractVersion
{
    /**
     * Get regex pattern for v2.0.0 Semantic Version spec
     *
     * NOTE: there is no official regex from semver.org.
     * Therefore, I've brazenly taken from https://github.com/mojombo/semver/issues/110#issuecomment-19433829
     * and modified it to be A) PHP and B) conforming to semver 2.0.0 with
     * grouping correlative to spec (mostly, see unit test for an outlier)
     *
     * @return string
     */
    protected function getSemVerPattern()
    {
        $pattern = "^" . "(0|[1-9])" . "\\.(0|[1-9]\\d*)" . "\\.(0|[1-9]\\d*)" . "(?:-" . "(" . "0|" . "[1-9]\\d*|" . "\\d*[a-zA-Z-][a-zA-Z0-9-]*" . ")" . "(?:\\." . "(" . "(?:(?:0|[1-9]\\d*|[a-zA-Z-][a-zA-Z]*)\\.?)*" . ").*" . ")?" . ")?" . "\$";
        return $pattern;
    }
    /**
     * Validate if a version string is Semantic Version compatible
     *
     * @param $version
     *
     * @return bool
     */
    public static function isSemantic($version)
    {
        try
        {
            new self($version);
            return true;
        }
        catch( Exception $e )
        {
            return false;
        }
    }
    /**
     * Validate if a version string is Semantic Version compatible
     *
     * @param $version
     *
     * @return bool
     */
    public function isValid($version)
    {
        if( !is_string($version) )
        {
            return false;
        }
        try
        {
            $versionParts = $this->separateBuildTag($version);
        }
        catch( WHMCS_Exception_Version_Parse $e )
        {
            return false;
        }
        $pattern = $this->getSemVerPattern();
        return preg_match('/' . $pattern . '/', $versionParts[0]) ? true : false;
    }
    /**
     * Separate out a build tag within a semantic string
     *
     * NOTE: this is a rudimentary parse operation based on a split at the
     * '+' delimiter.
     *
     * CAVEAT EMPTOR: Semantic Versioning 2.0.0 does not explicitly limit
     * the build tag info to be at the end of the string, IMO it is certainly
     * the intention of the specification that it always should be.  And for
     * that reason, please don't do it; this routine will fail to appropriately
     * deal with that condition
     *
     * Example of behavior:
     * '1.2.3+abc" --> ['1.2.3', 'abc']
     * '1.2.3-alpha.1+abc" --> ['1.2.3-alpha.1', 'abc']
     *
     * Example caveat:
     * '1.2.3+abc-beta'  --> ['1.2.3', 'abc-beta']
     *   ^^ technically allowed by spec but should never be used ^^
     *
     * @param $version
     *
     * @return array
     * @throws WHMCS_Exception_Version_BadVersionNumber
     */
    protected function separateBuildTag($version)
    {
        $versionParts = explode("+", $version, 2);
        if( empty($versionParts[0]) )
        {
            throw new WHMCS_Exception_Version_BadVersionNumber(sprintf("Missing primary version info in \"%s\"", $version));
        }
        if( count($versionParts) == 2 && empty($versionParts[1]) )
        {
            throw new WHMCS_Exception_Version_BadVersionNumber(sprintf("Missing build tag info in \"%s\"", $version));
        }
        return $versionParts;
    }
    /**
     * Parse a string into the various bits of a Semantic Version string
     *
     * @param string $version
     *
     * @return array
     * @throws WHMCS_Exception_Version_BadVersionNumber
     */
    public function parse($version)
    {
        $pattern = $this->getSemVerPattern();
        $versionParts = $this->separateBuildTag($version);
        $matches = array(  );
        if( preg_match('/' . $pattern . '/', $versionParts[0], $matches) === false )
        {
            throw new WHMCS_Exception_Version_BadVersionNumber(sprintf("\"%s\" is not a semantic version string!", $version));
        }
        if( count($matches) < 4 )
        {
            throw new WHMCS_Exception_Version_BadVersionNumber(sprintf("\"%s\" is not a semantic version string! Too few version segments.", $version));
        }
        array_shift($matches);
        if( isset($matches[0]) )
        {
            $this->setMajor(strtolower($matches[0]));
        }
        else
        {
            $this->setMajor(null);
        }
        if( isset($matches[1]) )
        {
            $this->setMinor(strtolower($matches[1]));
        }
        else
        {
            $this->setMinor(null);
        }
        if( isset($matches[2]) )
        {
            $this->setPatch(strtolower($matches[2]));
        }
        else
        {
            $this->setPatch(null);
        }
        if( isset($matches[3]) )
        {
            $this->setPreReleaseIdentifier(strtolower($matches[3]));
        }
        else
        {
            $this->setPreReleaseIdentifier(null);
        }
        if( isset($matches[4]) )
        {
            $this->setPreReleaseRevision(strtolower($matches[4]));
        }
        else
        {
            $this->setPreReleaseRevision(null);
        }
        if( empty($versionParts[1]) )
        {
            $versionParts[1] = null;
        }
        $this->setBuildTag($versionParts[1]);
        return $this;
    }
    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getCanonical()
    {
        $version = parent::getcanonical();
        $label = $this->getPreReleaseIdentifier();
        $preRelRevision = $this->getPreReleaseRevision();
        if( !$label )
        {
            $label = self::DEFAULT_PRERELEASE_IDENTIFIER;
        }
        if( !$preRelRevision )
        {
            $preRelRevision = self::DEFAULT_PRERELEASE_REVISION;
        }
        $version = sprintf("%s-%s.%d", $version, $label, $preRelRevision);
        return $version;
    }
    /**
     * Retrieve friendly "Major.Minor.Revision" string
     *
     * @return string
     */
    public function getRelease()
    {
        return parent::getcanonical();
    }
    /**
     * Compare two version objects
     *
     * This is the equivalent of PHP's version_compare() but modified to
     * work with objects and adhere to the semantic versioning scheme.
     *
     * Note that the not equal to (!=) operator is not currently supported.
     * Instead you must evaluate as equal to (==) and invert the result.
     *
     * @param WHMCS_Version_SemanticVersion $a
     * @param WHMCS_Version_SemanticVersion $b
     * @param string $operator Supported operators are <, >, lt, gt or ==
     *
     * @return bool
     */
    public static function compare($a, $b, $operator)
    {
        $primaryA = $a->getRelease();
        $primaryB = $b->getRelease();
        $status = version_compare($primaryA, $primaryB);
        if( $status !== 0 )
        {
            return self::getboolforoperatorcompare($operator, $status);
        }
        $map = self::getlabelhierarchymap();
        $labelA = $map[$a->getPreReleaseIdentifier()];
        $labelB = $map[$b->getPreReleaseIdentifier()];
        $status = version_compare($labelA, $labelB);
        if( $status !== 0 )
        {
            return self::getboolforoperatorcompare($operator, $status);
        }
        $preRevisionA = $a->getPreReleaseRevision();
        $preRevisionA = $preRevisionA ? $preRevisionA : 1;
        $preRevisionB = $b->getPreReleaseRevision();
        $preRevisionB = $preRevisionB ? $preRevisionB : 1;
        $status = version_compare($preRevisionA, $preRevisionB);
        if( $status !== 0 )
        {
            return self::getboolforoperatorcompare($operator, $status);
        }
        if( $status !== 0 )
        {
            return true;
        }
        if( $operator == "=" || $operator == "==" )
        {
            return true;
        }
        return false;
    }
    protected static function getBoolForOperatorCompare($operator, $status)
    {
        if( $status == 0 - 1 )
        {
            if( $operator == "<" || $operator == 'lt' )
            {
                return true;
            }
            return false;
        }
        if( $status == 1 )
        {
            if( $operator == ">" || $operator == 'gt' )
            {
                return true;
            }
            return false;
        }
    }
    protected static function getLabelHierarchyMap()
    {
        return array( 'alpha' => 1, 'beta' => 2, 'rc' => 3, '' => 4, 'release' => 4, 'stable' => 5 );
    }
}