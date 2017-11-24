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
class WHMCS_File_Upload extends WHMCS_File
{
    protected $uploadFilename = NULL;
    protected $uploadTmpName = NULL;
    public function __construct($name, $key = null)
    {
        if( !isset($_FILES[$name]) )
        {
            throw new WHMCS_Exception_File_NotUploaded("Check name and key parameters.");
        }
        if( is_numeric($key) )
        {
            $this->uploadFilename = $_FILES[$name]['name'][$key];
            $this->uploadTmpName = $_FILES[$name]['tmp_name'][$key];
        }
        else
        {
            $this->uploadFilename = $_FILES[$name]['name'];
            $this->uploadTmpName = $_FILES[$name]['tmp_name'];
        }
        if( !$this->isUploaded() )
        {
            throw new WHMCS_Exception_File_NotUploaded("No file uploaded.");
        }
        if( !$this->isFileNameSafe($this->getCleanName()) )
        {
            throw new WHMCS_Exception("Invalid upload filename. Valid filenames contain only alpha-numeric, dot, hyphen and underscore characters.");
        }
    }
    public function getFileName()
    {
        return $this->uploadFilename;
    }
    public function getFileTmpName()
    {
        return $this->uploadTmpName;
    }
    /**
     * Sanitize the incoming filename to remove unwanted characters
     *
     * @return string
     */
    public function getCleanName()
    {
        return preg_replace("/[^a-zA-Z0-9-_. ]/", '', $this->getFileName());
    }
    /**
     * Has file been uploaded
     *
     * @return bool
     */
    public function isUploaded()
    {
        return is_uploaded_file($this->getFileTmpName());
    }
    /**
     * Move uploaded file to permenant storage
     *
     * @param string $dest_dir The folder to move to
     * @param string $prefix (Optional) Prefix to apply to file
     *
     * @return string The filename stored
     */
    public function move($dest_dir = '', $prefix = '')
    {
        if( !is_writeable($dest_dir) )
        {
            throw new WHMCS_Exception("Could not save uploaded file. Please check permissions.");
        }
        $destinationPath = $this->generateUniqueDestinationPath($dest_dir, $prefix);
        if( !move_uploaded_file($this->getFileTmpName(), $destinationPath) )
        {
            throw new WHMCS_Exception("Could not save uploaded file. Please check available disk space.");
        }
        return basename($destinationPath);
    }
    /**
     * Generate a unique destination save path
     *
     * When prefix allows for a random salt, generates up to 30 random
     * file upload prefixes until a filename which does not already
     * exist is found to store the uploaded file to.
     *
     * If prefix does not include a random attribute and filename
     * already exists, upload is aborted immediately.
     *
     * @throws WHMCS_Exception If unable to find a non-existing filename for upload
     *
     * @param string $dest_dir
     * @param string $prefix
     *
     * @return string
     */
    protected function generateUniqueDestinationPath($dest_dir, $prefix)
    {
        mt_srand($this->makeRandomSeed());
        $i = 1;
        while( $i <= 30 )
        {
            $rand = mt_rand(100000, 999999);
            $destinationPath = $dest_dir . str_replace("{RAND}", $rand, $prefix) . $this->getCleanName();
            $file = new WHMCS_File($destinationPath);
            if( $file->exists() )
            {
                if( strpos($prefix, "{RAND}") === false )
                {
                    throw new WHMCS_Exception("Could not save uploaded file. File already exists.");
                }
                $i++;
            }
            else
            {
                return $destinationPath;
            }
        }
        throw new WHMCS_Exception("Could not save uploaded file. Unable to find a unique filename.");
    }
    /**
     * Generate a random seed for the prefix calculation
     *
     * Optimally seed the random generator (courtesy of the PHP manual)
     *
     * @link http://php.net/mt_srand
     *
     * @return float
     */
    protected function makeRandomSeed()
    {
        list($usec, $sec) = explode(" ", microtime());
        return (double) $sec + (double) $usec * 100000;
    }
    /**
     * Check the filename being uploaded is in the allowed file types for upload.
     *
     * @return bool
     */
    public function checkExtension()
    {
        $whmcs = WHMCS_Application::getinstance();
        $extensionArray = explode(',', $whmcs->get_config('TicketAllowedFileTypes'));
        $fileNameParts = explode(".", $this->getFileName());
        $fileExtension = "." . strtolower(end($fileNameParts));
        $alwaysBannedExtensions = array( ".php", ".cgi", ".pl", ".htaccess" );
        foreach( $alwaysBannedExtensions as $bannedExtension )
        {
            $pos = strpos($this->getFileName(), $bannedExtension);
            if( $pos !== false )
            {
                return false;
            }
        }
        foreach( $extensionArray as $extension )
        {
            if( trim($extension) == $fileExtension )
            {
                return true;
            }
        }
        return false;
    }
}