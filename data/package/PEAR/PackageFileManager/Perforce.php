<?php
/**
 * The Perforce plugin generator for both PEAR_PackageFileManager,
 * and PEAR_PackageFileManager2 classes.
 *
 * PHP versions 4 and 5
 *
 * @category  PEAR
 * @package   PEAR_PackageFileManager_Plugins
 * @author    Jon Parise <jon@php.net>
 * @copyright 2005-2009 The PEAR Group
 * @license   New BSD, Revised
 * @version    @package_version@
 * @link      http://pear.php.net/package/PEAR_PackageFileManager_Plugins
 * @since     File available since Release 1.0.0alpha1
 */

require_once 'PEAR/PackageFileManager/File.php';

/**
 * Generate a file list from a Perforce checkout.
 *
 * This requires the 'p4' command line client,
 * a properly-configured Perforce environment, and a
 * connection to the Perforce server.  Specifically, the 'p4 have' command
 * is used to determine which local files are under Perforce's control.
 *
 * @category  PEAR
 * @package   PEAR_PackageFileManager_Plugins
 * @author    Jon Parise <jon@php.net>
 * @copyright 2005-2009 The PEAR Group
 * @license   New BSD, Revised
 * @version    @package_version@
 * @link      http://pear.php.net/package/PEAR_PackageFileManager_Plugins
 * @since     Class available since Release 1.0.0alpha1
 */
class PEAR_PackageFileManager_Perforce extends PEAR_PackageFileManager_File
{
    /**
     * Build a list of files based on the output of the 'p4 have' command.
     *
     * @param string $directory The directory in which to list the files.
     *
     * @return mixed An array of full filenames or a PEAR_Error value if
     *               $directory does not exist.
     */
    function dirList($directory)
    {
        /* Return an error if the directory does not exist. */
        if (@is_dir($directory) === false) {
            return parent::raiseError(PEAR_PACKAGEFILEMANAGER_PLUGINS_DIR_DOESNT_EXIST, $directory);
        }

        /* List the files below $directory that are under Perforce control. */
        exec("p4 have $directory/...", $output);

        /* Strip off everything except the filename from each line of output. */
        $files = preg_replace('/^.* \- /', '', $output);

        /* If we have a list of files to include, remove all other entries. */
        if ($this->ignore[0]) {
            $files = array_filter($files, array($this, '_includeFilter'));
        }

        /* If we have a list of files to ignore, remove them from the array. */
        if ($this->ignore[1]) {
            $files = array_filter($files, array($this, '_ignoreFilter'));
        }

        return $files;
    }

    /**
     * Determine whether a given file should be excluded from the file list.
     *
     * @param string $file The full pathname of file to check.
     *
     * @return bool  True if the specified file should be included.
     * @access private
     */
    function _includeFilter($file)
    {
        return ($this->_checkIgnore(basename($file), $file, 0) === 0);
    }

    /**
     * Determine whether a given file should be included (i.e., not ignored)
     * from the file list.
     *
     * @param string $file The full pathname of file to check.
     *
     * @return bool  True if the specified file should be included.
     * @access private
     */
    function _ignoreFilter($file)
    {
        return ($this->_checkIgnore(basename($file), $file, 1) !== 1);
    }
}