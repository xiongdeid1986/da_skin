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
class WHMCS_File_Directory
{
    protected $path = '';
    /**
     * Initialise object with a given path to a directory
     *
     * @throws WHMCS_Exception Upon failure to set path
     *
     * @param string $path The directory path relative to ROOTDIR
     */
    public function __construct($path)
    {
        $this->setPath($path);
    }
    /**
     * Set the directory path
     *
     * @throws WHMCS_Exception If directory does not exist
     *
     * @param string $path The directory path relative to ROOTDIR
     */
    protected function setPath($path)
    {
        $full_path = ROOTDIR . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR;
        if( !is_dir($full_path) )
        {
            throw new WHMCS_Exception("Not a valid directory");
        }
        $this->path = $full_path;
    }
    /**
     * Return the directory path
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->path;
    }
    /**
     * Retrieves a list of subdirectories within the current directory
     *
     * @return string[]
     */
    public function getSubdirectories()
    {
        $folders = array(  );
        $dh = opendir($this->getPath());
        while( false !== ($folder = readdir($dh)) )
        {
            if( $folder != "." && $folder != ".." && is_dir($this->getPath() . $folder) )
            {
                $folders[] = $folder;
            }
        }
        closedir($dh);
        sort($folders);
        return $folders;
    }
    /**
     * Retrieves a list of files within the current directory
     *
     * @return string[]
     */
    public function listFiles()
    {
        $files = array(  );
        $dh = opendir($this->getPath());
        while( false !== ($file = readdir($dh)) )
        {
            if( is_file($this->getPath() . $file) )
            {
                $files[] = $file;
            }
        }
        closedir($dh);
        sort($files);
        return $files;
    }
}