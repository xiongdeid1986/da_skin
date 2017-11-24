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
class WHMCS_File
{
    protected $filepath = NULL;
    /**
     * Takes a file path to perform operations on
     *
     * @throws WHMCS_Exception If no file path is supplied
     * @throws WHMCS_Exception If the file's directory is invalid
     * @throws WHMCS_Exception If the file name is invalid
     *
     * @param string $filepath
     */
    public function __construct($filepath)
    {
        if( !trim($filepath) )
        {
            throw new WHMCS_Exception("No file path supplied.");
        }
        if( !WHMCS_Environment_Os::iswindows() && realpath(dirname($filepath)) != dirname($filepath) )
        {
            throw new WHMCS_Exception("File path invalid.");
        }
        if( !$this->isFileNameSafe(basename($filepath)) )
        {
            throw new WHMCS_Exception("Filename invalid.");
        }
        $this->filepath = $filepath;
    }
    /**
     * Checks if the given file path exists
     *
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->filepath);
    }
    /**
     * Creates a file and writes the given contents to it
     *
     * If filename does not exist, the file is created. Otherwise, the
     * existing file is overwritten.
     *
     * @throws WHMCS_Exception_File_NotCreated If file cannot be written
     *
     * @return $this
     */
    public function create($contents)
    {
        if( !file_put_contents($this->filepath, $contents) )
        {
            throw new WHMCS_Exception_File_NotCreated($this->filepath);
        }
        return $this;
    }
    /**
     * Safely delete a file from the file system
     *
     * Note: If the file requested to be deleted does not exist, this function
     * assumes it has been deleted from the file system by other means and
     * returns successful
     *
     * @throws WHMCS_Exception_File_NotDeleted If file cannot be deleted
     *
     * @return $this
     */
    public function delete()
    {
        if( file_exists($this->filepath) )
        {
            if( unlink($this->filepath) )
            {
                return $this;
            }
            throw new WHMCS_Exception_File_NotDeleted($this->filepath);
        }
        throw new WHMCS_Exception_File_NotFound($this->filepath);
    }
    /**
     * Recursively loops through a directory and deletes all files/folders, finishes by deleting the parent directory also
     *
     * @param string $dir (Optional) Defaults to path set on instantiation
     */
    public function deleteDirectoryAndAllFiles($dir = '')
    {
        if( !$dir )
        {
            $dir = $this->filepath;
        }
        if( is_dir($dir) )
        {
            $objects = scandir($dir);
            foreach( $objects as $object )
            {
                if( $object != "." && $object != ".." )
                {
                    $path = $dir . DIRECTORY_SEPARATOR . $object;
                    if( filetype($path) == 'dir' )
                    {
                        $this->deleteDirectoryAndAllFiles($path);
                    }
                    else
                    {
                        try
                        {
                            $file = new WHMCS_File($path);
                            $file->delete();
                        }
                        catch( Exception $e )
                        {
                        }
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
    public function isFileNameSafe($filename)
    {
        if( empty($filename) )
        {
            return false;
        }
        if( strpos($filename, '') !== false )
        {
            return false;
        }
        if( strpos($filename, DIRECTORY_SEPARATOR) !== false || strpos($filename, PATH_SEPARATOR) !== false )
        {
            return false;
        }
        if( strpos($filename, chr(8)) !== false )
        {
            return false;
        }
        if( substr($filename, 0, 1) === "." )
        {
            return false;
        }
        $inputValidation = new WHMCS_Input_Validation();
        if( $inputValidation->escapeshellcmd($filename) != $filename )
        {
            return false;
        }
        return true;
    }
}