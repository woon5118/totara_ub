<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Definition of a class stored_file.
 *
 * @package   core_files
 * @copyright 2008 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/filestorage/file_progress.php');

/**
 * Class representing local files stored in a sha1 file pool.
 *
 * Since Moodle 2.0 file contents are stored in sha1 pool and
 * all other file information is stored in new "files" database table.
 *
 * @package   core_files
 * @category  files
 * @copyright 2008 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class stored_file {
    /** @var file_storage file storage pool instance */
    private $fs;
    /** @var stdClass record from the files table left join files_reference table */
    private $file_record;
    /** @var string location of content files */
    private $filedir;
    /** @var repository repository plugin instance */
    private $repository;

    /**
     * @var int Indicates a file handle of the type returned by fopen.
     */
    const FILE_HANDLE_FOPEN = 0;

    /**
     * @var int Indicates a file handle of the type returned by gzopen.
     */
    const FILE_HANDLE_GZOPEN = 1;


    /**
     * Constructor, this constructor should be called ONLY from the file_storage class!
     *
     * @param file_storage $fs file  storage instance
     * @param stdClass $file_record description of file
     * @param string $filedir location of file directory with sh1 named content files
     */
    public function __construct(file_storage $fs, stdClass $file_record, $filedir) {
        global $DB, $CFG;
        $this->fs          = $fs;
        $this->file_record = clone($file_record); // prevent modifications
        $this->filedir     = $filedir; // keep secret, do not expose!

        if (!empty($file_record->repositoryid)) {
            require_once("$CFG->dirroot/repository/lib.php");
            $this->repository = repository::get_repository_by_id($file_record->repositoryid, SYSCONTEXTID);
            if ($this->repository->supported_returntypes() & FILE_REFERENCE != FILE_REFERENCE) {
                // Repository cannot do file reference.
                throw new moodle_exception('error');
            }
        } else {
            $this->repository = null;
        }
        // make sure all reference fields exist in file_record even when it is not a reference
        foreach (array('referencelastsync', 'referencefileid', 'reference', 'repositoryid') as $key) {
            if (empty($this->file_record->$key)) {
                $this->file_record->$key = null;
            }
        }
    }

    /**
     * Whether or not this is a external resource
     *
     * @return bool
     */
    public function is_external_file() {
        return !empty($this->repository);
    }

    /**
     * Update some file record fields
     * NOTE: Must remain protected
     *
     * @param stdClass $dataobject
     */
    protected function update($dataobject) {
        global $DB;
        $updatereferencesneeded = false;
        $keys = array_keys((array)$this->file_record);
        $filepreupdate = clone($this->file_record);
        foreach ($dataobject as $field => $value) {
            if (in_array($field, $keys)) {
                if ($field == 'contextid' and (!is_number($value) or $value < 1)) {
                    throw new file_exception('storedfileproblem', 'Invalid contextid');
                }

                if ($field == 'component') {
                    $value = clean_param($value, PARAM_COMPONENT);
                    if (empty($value)) {
                        throw new file_exception('storedfileproblem', 'Invalid component');
                    }
                }

                if ($field == 'filearea') {
                    $value = clean_param($value, PARAM_AREA);
                    if (empty($value)) {
                        throw new file_exception('storedfileproblem', 'Invalid filearea');
                    }
                }

                if ($field == 'itemid' and (!is_number($value) or $value < 0)) {
                    throw new file_exception('storedfileproblem', 'Invalid itemid');
                }


                if ($field == 'filepath') {
                    $value = clean_param($value, PARAM_PATH);
                    if (strpos($value, '/') !== 0 or strrpos($value, '/') !== strlen($value)-1) {
                        // path must start and end with '/'
                        throw new file_exception('storedfileproblem', 'Invalid file path');
                    }
                }

                if ($field == 'filename') {
                    // folder has filename == '.', so we pass this
                    if ($value != '.') {
                        $value = clean_param($value, PARAM_FILE);
                    }
                    if ($value === '') {
                        throw new file_exception('storedfileproblem', 'Invalid file name');
                    }
                }

                if ($field === 'timecreated' or $field === 'timemodified') {
                    if (!is_number($value)) {
                        throw new file_exception('storedfileproblem', 'Invalid timestamp');
                    }
                    if ($value < 0) {
                        $value = 0;
                    }
                }

                if ($field === 'referencefileid') {
                    if (!is_null($value) and !is_number($value)) {
                        throw new file_exception('storedfileproblem', 'Invalid reference info');
                    }
                }

                if (($field == 'contenthash' || $field == 'filesize') && $this->file_record->$field != $value) {
                    $updatereferencesneeded = true;
                }

                // adding the field
                $this->file_record->$field = $value;
            } else {
                throw new coding_exception("Invalid field name, $field doesn't exist in file record");
            }
        }
        // Validate mimetype field
        // we don't use {@link stored_file::get_content_file_location()} here becaues it will try to update file_record
        $pathname = $this->get_pathname_by_contenthash();
        // try to recover the content from trash
        if (!is_readable($pathname)) {
            if (!$this->fs->try_content_recovery($this) or !is_readable($pathname)) {
                throw new file_exception('storedfilecannotread', '', $pathname);
            }
        }
        $mimetype = $this->fs->mimetype($pathname, $this->file_record->filename);
        $this->file_record->mimetype = $mimetype;

        $DB->update_record('files', $this->file_record);
        if ($updatereferencesneeded) {
            // Either filesize or contenthash of this file have changed. Update all files that reference to it.
            $this->fs->update_references_to_storedfile($this);
        }

        // Callback for file update.
        if (!$this->is_directory()) {
            if ($pluginsfunction = get_plugins_with_function('after_file_updated')) {
                foreach ($pluginsfunction as $plugintype => $plugins) {
                    foreach ($plugins as $pluginfunction) {
                        $pluginfunction($this->file_record, $filepreupdate);
                    }
                }
            }
        }
    }

    /**
     * Rename filename
     *
     * @param string $filepath file path
     * @param string $filename file name
     */
    public function rename($filepath, $filename) {
        if ($this->fs->file_exists($this->get_contextid(), $this->get_component(), $this->get_filearea(), $this->get_itemid(), $filepath, $filename)) {
            $a = new stdClass();
            $a->contextid = $this->get_contextid();
            $a->component = $this->get_component();
            $a->filearea  = $this->get_filearea();
            $a->itemid    = $this->get_itemid();
            $a->filepath  = $filepath;
            $a->filename  = $filename;
            throw new file_exception('storedfilenotcreated', $a, 'file exists, cannot rename');
        }
        $filerecord = new stdClass;
        $filerecord->filepath = $filepath;
        $filerecord->filename = $filename;
        // populate the pathname hash
        $filerecord->pathnamehash = $this->fs->get_pathname_hash($this->file_record->contextid, $this->file_record->component, $this->file_record->filearea, $this->file_record->itemid, $filepath, $filename);
        $this->update($filerecord);
    }

    /**
     * Function stored_file::replace_content_with() is deprecated. Please use stored_file::replace_file_with()
     *
     * @deprecated since Moodle 2.6 MDL-42016 - please do not use this function any more.
     * @see stored_file::replace_file_with()
     */
    public function replace_content_with(stored_file $storedfile) {
        throw new coding_exception('Function stored_file::replace_content_with() can not be used any more . ' .
            'Please use stored_file::replace_file_with()');
    }

    /**
     * Replaces the fields that might have changed when file was overriden in filepicker:
     * reference, contenthash, filesize, userid
     *
     * Note that field 'source' must be updated separately because
     * it has different format for draft and non-draft areas and
     * this function will usually be used to replace non-draft area
     * file with draft area file.
     *
     * @param stored_file $newfile
     * @throws coding_exception
     */
    public function replace_file_with(stored_file $newfile) {
        if ($newfile->get_referencefileid() &&
                $this->fs->get_references_count_by_storedfile($this)) {
            // The new file is a reference.
            // The current file has other local files referencing to it.
            // Double reference is not allowed.
            throw new moodle_exception('errordoublereference', 'repository');
        }

        $filerecord = new stdClass;
        $contenthash = $newfile->get_contenthash();
        if ($this->fs->content_exists($contenthash) || $this->fs->try_content_recovery($contenthash)) {
            $filerecord->contenthash = $contenthash;
        } else {
            throw new file_exception('storedfileproblem', 'Invalid contenthash, content must be already in filepool', $contenthash);
        }
        $filerecord->filesize = $newfile->get_filesize();
        $filerecord->referencefileid = $newfile->get_referencefileid();
        $filerecord->userid = $newfile->get_userid();
        $oldcontenthash = $this->get_contenthash();
        $this->update($filerecord);
        $this->fs->deleted_file_cleanup($oldcontenthash);
    }

    /**
     * Unlink the stored file from the referenced file
     *
     * This methods destroys the link to the record in files_reference table. This effectively
     * turns the stored file from being an alias to a plain copy. However, the caller has
     * to make sure that the actual file's content has beed synced prior to calling this method.
     */
    public function delete_reference() {
        global $DB;

        if (!$this->is_external_file()) {
            throw new coding_exception('An attempt to unlink a non-reference file.');
        }

        $transaction = $DB->start_delegated_transaction();

        // Are we the only one referring to the original file? If so, delete the
        // referenced file record. Note we do not use file_storage::search_references_count()
        // here because we want to count draft files too and we are at a bit lower access level here.
        $countlinks = $DB->count_records('files',
            array('referencefileid' => $this->file_record->referencefileid));
        if ($countlinks == 1) {
            $DB->delete_records('files_reference', array('id' => $this->file_record->referencefileid));
        }

        // Update the underlying record in the database.
        $update = new stdClass();
        $update->referencefileid = null;
        $this->update($update);

        $transaction->allow_commit();

        // Update our properties and the record in the memory.
        $this->repository = null;
        $this->file_record->repositoryid = null;
        $this->file_record->reference = null;
        $this->file_record->referencefileid = null;
        $this->file_record->referencelastsync = null;
    }

    /**
     * Is this a directory?
     *
     * Directories are only emulated, internally they are stored as empty
     * files with a "." instead of name - this means empty directory contains
     * exactly one empty file with name dot.
     *
     * @return bool true means directory, false means file
     */
    public function is_directory() {
        return ($this->file_record->filename === '.');
    }

    /**
     * Delete file from files table.
     *
     * The content of files stored in sha1 pool is reclaimed
     * later - the occupied disk space is reclaimed much later.
     *
     * @return bool always true or exception if error occurred
     */
    public function delete() {
        global $DB;

        if ($this->is_directory()) {
            // Directories can not be referenced, just delete the record.
            $DB->delete_records('files', array('id'=>$this->file_record->id));

        } else {
            $transaction = $DB->start_delegated_transaction();

            // If there are other files referring to this file, convert them to copies.
            if ($files = $this->fs->get_references_by_storedfile($this)) {
                foreach ($files as $file) {
                    $this->fs->import_external_file($file);
                }
            }

            // If this file is a reference (alias) to another file, unlink it first.
            if ($this->is_external_file()) {
                $this->delete_reference();
            }

            // Now delete the file record.
            $DB->delete_records('files', array('id'=>$this->file_record->id));

            $transaction->allow_commit();

            if (!$this->is_directory()) {
                // Callback for file deletion.
                if ($pluginsfunction = get_plugins_with_function('after_file_deleted')) {
                    foreach ($pluginsfunction as $plugintype => $plugins) {
                        foreach ($plugins as $pluginfunction) {
                            $pluginfunction($this->file_record);
                        }
                    }
                }
            }
        }

        // Move pool file to trash if content not needed any more.
        $this->fs->deleted_file_cleanup($this->file_record->contenthash);
        return true; // BC only
    }

    /**
     * Verify if content file is present in local $CFG->filedir,
     * if not attempt content recovery by default.
     *
     * @param bool $attemptrecovery if content missing then try recovery from trash directory or external store
     * @return bool
     */
    public function is_content_available(bool $attemptrecovery = true): bool {
        $contenthash = $this->get_contenthash();
        if ($this->fs->content_exists($contenthash)) {
            return true;
        }
        if (!$attemptrecovery) {
            return false;
        }
        return $this->fs->try_content_recovery($contenthash);
    }

    /**
     * Get file pathname by contenthash
     *
     * NOTE, this function is not calling sync_external_file, it assume the contenthash is current
     * Protected - developers must not gain direct access to this function.
     *
     * @return string full path to pool file with file content
     */
    protected function get_pathname_by_contenthash() {
        // Detect is local file or not.
        $contenthash = $this->file_record->contenthash;
        $l1 = $contenthash[0].$contenthash[1];
        $l2 = $contenthash[2].$contenthash[3];
        return "$this->filedir/$l1/$l2/$contenthash";
    }

    /**
     * Get file pathname by given contenthash, this method will try to sync files
     *
     * Protected - developers must not gain direct access to this function.
     *
     * NOTE: do not make this public, we must not modify or delete the pool files directly! ;-)
     *
     * @return string full path to pool file with file content
     **/
    protected function get_content_file_location() {
        $this->sync_external_file();
        return $this->get_pathname_by_contenthash();
    }

    /**
    * adds this file path to a curl request (POST only)
    *
    * @param curl $curlrequest the curl request object
    * @param string $key what key to use in the POST request
    * @return void
    */
    public function add_to_curl_request(&$curlrequest, $key) {
        if (function_exists('curl_file_create')) {
            // As of PHP 5.5, the usage of the @filename API for file uploading is deprecated.
            $value = curl_file_create($this->get_content_file_location());
        } else {
            $value = '@' . $this->get_content_file_location();
        }
        $curlrequest->_tmp_file_post_params[$key] = $value;
    }

    /**
     * Returns file handle - read only mode, no writing allowed into pool files!
     *
     * When you want to modify a file, create a new file and delete the old one.
     *
     * @param int $type Type of file handle (FILE_HANDLE_xx constant)
     * @return resource file handle
     */
    public function get_content_file_handle($type = self::FILE_HANDLE_FOPEN) {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            if (!$this->fs->try_content_recovery($this) or !is_readable($path)) {
                throw new file_exception('storedfilecannotread', '', $path);
            }
        }
        switch ($type) {
            case self::FILE_HANDLE_FOPEN:
                // Binary reading.
                return fopen($path, 'rb');
            case self::FILE_HANDLE_GZOPEN:
                // Binary reading of file in gz format.
                return gzopen($path, 'rb');
            default:
                throw new coding_exception('Unexpected file handle type');
        }
    }

    /**
     * Dumps file content to page.
     */
    public function readfile() {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            if (!$this->fs->try_content_recovery($this) or !is_readable($path)) {
                throw new file_exception('storedfilecannotread', '', $path);
            }
        }
        readfile_allow_large($path, $this->get_filesize());
    }

    /**
     * Returns file content as string.
     *
     * @return string content
     */
    public function get_content() {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            if (!$this->fs->try_content_recovery($this) or !is_readable($path)) {
                throw new file_exception('storedfilecannotread', '', $path);
            }
        }
        return file_get_contents($this->get_content_file_location());
    }

    /**
     * Copy content of file to given pathname.
     *
     * @param string $pathname real path to the new file
     * @return bool success
     */
    public function copy_content_to($pathname) {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            if (!$this->fs->try_content_recovery($this) or !is_readable($path)) {
                throw new file_exception('storedfilecannotread', '', $path);
            }
        }
        return copy($path, $pathname);
    }

    /**
     * Copy content of file to temporary folder and returns file path
     *
     * @param string $dir name of the temporary directory
     * @param string $fileprefix prefix of temporary file.
     * @return string|bool path of temporary file or false.
     */
    public function copy_content_to_temp($dir = 'files', $fileprefix = 'tempup_') {
        $tempfile = false;
        if (!$dir = make_temp_directory($dir)) {
            return false;
        }
        if (!$tempfile = tempnam($dir, $fileprefix)) {
            return false;
        }
        if (!$this->copy_content_to($tempfile)) {
            // something went wrong
            @unlink($tempfile);
            return false;
        }
        return $tempfile;
    }

    /**
     * List contents of archive.
     *
     * @param file_packer $packer file packer instance
     * @return array of file infos
     */
    public function list_files(file_packer $packer) {
        $archivefile = $this->get_content_file_location();
        return $packer->list_files($archivefile);
    }

    /**
     * Returns the total size (in bytes) of the contents of an archive.
     *
     * @param file_packer $packer file packer instance
     * @return int|null total size in bytes
     */
    public function get_total_content_size(file_packer $packer): ?int {
        // Fetch the contents of the archive.
        $files = $this->list_files($packer);

        // Early return if the value of $files is not of type array.
        // This can happen when the utility class is unable to open or read the contents of the archive.
        if (!is_array($files)) {
            return null;
        }

        return array_reduce($files, function ($contentsize, $file) {
            return $contentsize + $file->size;
        }, 0);
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param file_packer $packer file packer instance
     * @param string $pathname target directory
     * @param file_progress $progress Progress indicator callback or null if not required
     * @return array|bool list of processed files; false if error
     */
    public function extract_to_pathname(file_packer $packer, $pathname,
            file_progress $progress = null) {
        $archivefile = $this->get_content_file_location();
        return $packer->extract_to_pathname($archivefile, $pathname, null, $progress);
    }

    /**
     * Extract file to given file path (real OS filesystem), existing files are overwritten.
     *
     * @param file_packer $packer file packer instance
     * @param int $contextid context ID
     * @param string $component component
     * @param string $filearea file area
     * @param int $itemid item ID
     * @param string $pathbase path base
     * @param int $userid user ID
     * @param file_progress $progress Progress indicator callback or null if not required
     * @return array|bool list of processed files; false if error
     */
    public function extract_to_storage(file_packer $packer, $contextid,
            $component, $filearea, $itemid, $pathbase, $userid = null, file_progress $progress = null) {
        $archivefile = $this->get_content_file_location();
        return $packer->extract_to_storage($archivefile, $contextid,
                $component, $filearea, $itemid, $pathbase, $userid, $progress);
    }

    /**
     * Add file/directory into archive.
     *
     * @param file_archive $filearch file archive instance
     * @param string $archivepath pathname in archive
     * @return bool success
     */
    public function archive_file(file_archive $filearch, $archivepath) {
        if ($this->repository) {
            $this->sync_external_file();
            if ($this->get_contenthash() === sha1('')) {
                // This file is not stored locally - attempt to retrieve it from the repository.
                // This may happen if the repository deliberately does not fetch files, or if there is a failure with the sync.
                $fileinfo = $this->repository->get_file($this->get_reference());
                if (isset($fileinfo['path'])) {
                    return $filearch->add_file_from_pathname($archivepath, $fileinfo['path']);
                }
            }
        }

        if ($this->is_directory()) {
            return $filearch->add_directory($archivepath);
        } else {
            $path = $this->get_content_file_location();
            if (!is_readable($path)) {
                return false;
            }
            return $filearch->add_file_from_pathname($archivepath, $path);
        }
    }

    /**
     * Returns information about image,
     * information is determined from the file content
     *
     * @param boolean $load_orientation set true to load the orientation information tag
     * @return mixed array with width, height and mimetype; false if not an image
     */
    public function get_imageinfo($load_orientation = false) {
        $path = $this->get_content_file_location();
        if (!is_readable($path)) {
            if (!$this->fs->try_content_recovery($this) or !is_readable($path)) {
                throw new file_exception('storedfilecannotread', '', $path);
            }
        }
        $mimetype = $this->get_mimetype();
        if (!preg_match('|^image/|', $mimetype) || !filesize($path) || !($imageinfo = getimagesize($path))) {
            return false;
        }
        $image = array('width'=>$imageinfo[0], 'height'=>$imageinfo[1], 'mimetype'=>image_type_to_mime_type($imageinfo[2]));
        if (empty($image['width']) or empty($image['height']) or empty($image['mimetype'])) {
            // gd can not parse it, sorry
            return false;
        }
        if ($load_orientation && $mimetype === 'image/jpeg' && extension_loaded('exif') && function_exists('exif_read_data')) {
            // Add orientation information.
            $exif = @exif_read_data($path, 'IFD0');
            if ($exif !== false && isset($exif['Orientation'])) {
                $image['orientation'] = $exif['Orientation'];
            }
        }
        return $image;
    }

    /**
     * Verifies the file is a valid web image - gif, png and jpeg only.
     *
     * It should be ok to serve this image from server without any other security workarounds.
     *
     * @return bool true if file ok
     */
    public function is_valid_image() {
        $mimetype = $this->get_mimetype();
        if (!file_mimetype_in_typegroup($mimetype, 'web_image')) {
            return false;
        }
        if (!$info = $this->get_imageinfo()) {
            return false;
        }
        if ($info['mimetype'] !== $mimetype) {
            return false;
        }
        // ok, GD likes this image
        return true;
    }

    /**
     * Returns parent directory, creates missing parents if needed.
     *
     * @return stored_file
     */
    public function get_parent_directory() {
        if ($this->file_record->filepath === '/' and $this->file_record->filename === '.') {
            //root dir does not have parent
            return null;
        }

        if ($this->file_record->filename !== '.') {
            return $this->fs->create_directory($this->file_record->contextid, $this->file_record->component, $this->file_record->filearea, $this->file_record->itemid, $this->file_record->filepath);
        }

        $filepath = $this->file_record->filepath;
        $filepath = trim($filepath, '/');
        $dirs = explode('/', $filepath);
        array_pop($dirs);
        $filepath = implode('/', $dirs);
        $filepath = ($filepath === '') ? '/' : "/$filepath/";

        return $this->fs->create_directory($this->file_record->contextid, $this->file_record->component, $this->file_record->filearea, $this->file_record->itemid, $filepath);
    }

    /**
     * Synchronize file if it is a reference and needs synchronizing
     *
     * Updates contenthash and filesize
     */
    public function sync_external_file() {
        if (!empty($this->repository)) {
            $this->repository->sync_reference($this);
        }
    }

    /**
     * Returns context id of the file
     *
     * @return int context id
     */
    public function get_contextid() {
        return $this->file_record->contextid;
    }

    /**
     * Returns component name - this is the owner of the areas,
     * nothing else is allowed to read or modify the files directly!!
     *
     * @return string
     */
    public function get_component() {
        return $this->file_record->component;
    }

    /**
     * Returns file area name, this divides files of one component into groups with different access control.
     * All files in one area have the same access control.
     *
     * @return string
     */
    public function get_filearea() {
        return $this->file_record->filearea;
    }

    /**
     * Returns returns item id of file.
     *
     * @return int
     */
    public function get_itemid() {
        return $this->file_record->itemid;
    }

    /**
     * Returns file path - starts and ends with /, \ are not allowed.
     *
     * @return string
     */
    public function get_filepath() {
        return $this->file_record->filepath;
    }

    /**
     * Returns file name or '.' in case of directories.
     *
     * @return string
     */
    public function get_filename() {
        return $this->file_record->filename;
    }

    /**
     * Returns id of user who created the file.
     *
     * @return int
     */
    public function get_userid() {
        return $this->file_record->userid;
    }

    /**
     * Returns the size of file in bytes.
     *
     * @return int bytes
     */
    public function get_filesize() {
        $this->sync_external_file();
        return $this->file_record->filesize;
    }

     /**
     * Function stored_file::set_filesize() is deprecated. Please use stored_file::replace_file_with
     *
     * @deprecated since Moodle 2.6 MDL-42016 - please do not use this function any more.
     * @see stored_file::replace_file_with()
     */
    public function set_filesize($filesize) {
        throw new coding_exception('Function stored_file::set_filesize() can not be used any more. ' .
            'Please use stored_file::replace_file_with()');
    }

    /**
     * Returns mime type of file.
     *
     * @return string
     */
    public function get_mimetype() {
        return $this->file_record->mimetype;
    }

    /**
     * Returns unix timestamp of file creation date.
     *
     * @return int
     */
    public function get_timecreated() {
        return $this->file_record->timecreated;
    }

    /**
     * Returns unix timestamp of last file modification.
     *
     * @return int
     */
    public function get_timemodified() {
        $this->sync_external_file();
        return $this->file_record->timemodified;
    }

    /**
     * set timemodified
     *
     * @param int $timemodified
     */
    public function set_timemodified($timemodified) {
        $filerecord = new stdClass;
        $filerecord->timemodified = $timemodified;
        $this->update($filerecord);
    }

    /**
     * Returns file status flag.
     *
     * @return int 0 means file OK, anything else is a problem and file can not be used
     */
    public function get_status() {
        return $this->file_record->status;
    }

    /**
     * Returns file id.
     *
     * @return int
     */
    public function get_id() {
        return $this->file_record->id;
    }

    /**
     * Returns sha1 hash of file content.
     *
     * @return string
     */
    public function get_contenthash() {
        $this->sync_external_file();
        return $this->file_record->contenthash;
    }

    /**
     * Returns sha1 hash of all file path components sha1("contextid/component/filearea/itemid/dir/dir/filename.ext").
     *
     * @return string
     */
    public function get_pathnamehash() {
        return $this->file_record->pathnamehash;
    }

    /**
     * Returns the license type of the file, it is a short name referred from license table.
     *
     * @return string
     */
    public function get_license() {
        return $this->file_record->license;
    }

    /**
     * Set license
     *
     * @param string $license license
     */
    public function set_license($license) {
        $filerecord = new stdClass;
        $filerecord->license = $license;
        $this->update($filerecord);
    }

    /**
     * Returns the author name of the file.
     *
     * @return string
     */
    public function get_author() {
        return $this->file_record->author;
    }

    /**
     * Set author
     *
     * @param string $author
     */
    public function set_author($author) {
        $filerecord = new stdClass;
        $filerecord->author = $author;
        $this->update($filerecord);
    }

    /**
     * Returns the source of the file, usually it is a url.
     *
     * @return string
     */
    public function get_source() {
        return $this->file_record->source;
    }

    /**
     * Set license
     *
     * @param string $license license
     */
    public function set_source($source) {
        $filerecord = new stdClass;
        $filerecord->source = $source;
        $this->update($filerecord);
    }


    /**
     * Returns the sort order of file
     *
     * @return int
     */
    public function get_sortorder() {
        return $this->file_record->sortorder;
    }

    /**
     * Set file sort order
     *
     * @param int $sortorder
     * @return int
     */
    public function set_sortorder($sortorder) {
        $oldorder = $this->file_record->sortorder;
        $filerecord = new stdClass;
        $filerecord->sortorder = $sortorder;
        $this->update($filerecord);
        if (!$this->is_directory()) {
            // Callback for file sort order change.
            if ($pluginsfunction = get_plugins_with_function('after_file_sorted')) {
                foreach ($pluginsfunction as $plugintype => $plugins) {
                    foreach ($plugins as $pluginfunction) {
                        $pluginfunction($this->file_record, $oldorder, $sortorder);
                    }
                }
            }
        }
    }

    /**
     * Returns repository id
     *
     * @return int|null
     */
    public function get_repository_id() {
        if (!empty($this->repository)) {
            return $this->repository->id;
        } else {
            return null;
        }
    }

    /**
     * Returns repository type.
     *
     * @return mixed str|null the repository type or null if is not an external file
     * @since  Moodle 3.3
     */
    public function get_repository_type() {

        if (!empty($this->repository)) {
            return $this->repository->get_typename();
        } else {
            return null;
        }
    }


    /**
     * get reference file id
     * @return int
     */
    public function get_referencefileid() {
        return $this->file_record->referencefileid;
    }

    /**
     * Get reference last sync time
     * @return int
     */
    public function get_referencelastsync() {
        return $this->file_record->referencelastsync;
    }

    /**
     * Function stored_file::get_referencelifetime() is deprecated as reference
     * life time is no longer stored in DB or returned by repository. Each
     * repository should decide by itself when to synchronise the references.
     *
     * @deprecated since Moodle 2.6 MDL-42016 - please do not use this function any more.
     * @see repository::sync_reference()
     */
    public function get_referencelifetime() {
        throw new coding_exception('Function stored_file::get_referencelifetime() can not be used any more. ' .
            'See repository::sync_reference().');
    }
    /**
     * Returns file reference
     *
     * @return string
     */
    public function get_reference() {
        return $this->file_record->reference;
    }

    /**
     * Get human readable file reference information
     *
     * @return string
     */
    public function get_reference_details() {
        return $this->repository->get_reference_details($this->get_reference(), $this->get_status());
    }

    /**
     * Called after reference-file has been synchronized with the repository
     *
     * We update contenthash, filesize and status in files table if changed
     * and we always update lastsync in files_reference table
     *
     * @param null|string $contenthash if set to null contenthash is not changed
     * @param int $filesize new size of the file
     * @param int $status new status of the file (0 means OK, 666 - source missing)
     * @param int $timemodified last time modified of the source, if known
     */
    public function set_synchronized($contenthash, $filesize, $status = 0, $timemodified = null) {
        if (!$this->is_external_file()) {
            return;
        }
        $now = time();
        if ($contenthash === null) {
            $contenthash = $this->file_record->contenthash;
        }
        if ($contenthash != $this->file_record->contenthash) {
            $oldcontenthash = $this->file_record->contenthash;
        }
        // this will update all entries in {files} that have the same filereference id
        $this->fs->update_references($this->file_record->referencefileid, $now, null, $contenthash, $filesize, $status, $timemodified);
        // we don't need to call update() for this object, just set the values of changed fields
        $this->file_record->contenthash = $contenthash;
        $this->file_record->filesize = $filesize;
        $this->file_record->status = $status;
        $this->file_record->referencelastsync = $now;
        if ($timemodified) {
            $this->file_record->timemodified = $timemodified;
        }
        if (isset($oldcontenthash)) {
            $this->fs->deleted_file_cleanup($oldcontenthash);
        }
    }

    /**
     * Sets the error status for a file that could not be synchronised
     */
    public function set_missingsource() {
        $this->set_synchronized($this->file_record->contenthash, $this->file_record->filesize, 666);
    }

    /**
     * Send file references
     *
     * @param int $lifetime Number of seconds before the file should expire from caches (default 24 hours)
     * @param int $filter 0 (default)=no filtering, 1=all files, 2=html files only
     * @param bool $forcedownload If true (default false), forces download of file rather than view in browser/plugin
     * @param array $options additional options affecting the file serving
     */
    public function send_file($lifetime, $filter, $forcedownload, $options) {
        $this->repository->send_file($this, $lifetime, $filter, $forcedownload, $options);
    }

    /**
     * Imports the contents of an external file into moodle filepool.
     *
     * @throws moodle_exception if file could not be downloaded or is too big
     * @param int $maxbytes throw an exception if file size is bigger than $maxbytes (0 means no limit)
     */
    public function import_external_file_contents($maxbytes = 0) {
        if ($this->repository) {
            $this->repository->import_external_file_contents($this, $maxbytes);
        }
    }

    /**
     * Gets a file relative to this file in the repository and sends it to the browser.
     * Checks the function repository::supports_relative_file() to make sure it can be used.
     *
     * TOTARA:
     *   - Added the options array in Totara 13
     *
     * @param string $relativepath the relative path to the file we are trying to access
     * @param array $options Options for the send_file function.
     */
    public function send_relative_file($relativepath, array $options = []) {
        if ($this->repository && $this->repository->supports_relative_file()) {
            $relativepath = clean_param($relativepath, PARAM_PATH);
            $this->repository->send_relative_file($this, $relativepath, $options);
        } else {
            send_file_not_found();
        }
    }

    /**
     * Generates a thumbnail for this stored_file.
     * Note that the EXIF orientation information takes into account.
     *
     * If the GD library has at least version 2 and PNG support is available, the returned data
     * is the content of a transparent PNG file containing the thumbnail. Otherwise, the function
     * returns contents of a JPEG file with black background containing the thumbnail.
     *
     * @param   int $width the width of the requested thumbnail
     * @param   int $height the height of the requested thumbnail
     * @return  string|bool false if a problem occurs, the thumbnail image data otherwise
     */
    public function generate_image_thumbnail($width, $height) {
        global $CFG;
        require_once($CFG->libdir . '/gdlib.php');

        if (empty($width) or empty($height)) {
            return false;
        }

        // Fetch the image information for this image.
        $imageinfo = $this->get_imageinfo(true);
        if (empty($imageinfo)) {
            return false;
        }

        // Create a new image from the file.
        $original = @imagecreatefromstring($this->get_content());

        // Generate the thumbnail.
        return generate_image_thumbnail_from_image($original, $imageinfo, $width, $height);
    }

    /**
     * Generate a resized image for this stored_file.
     * Note that the EXIF orientation information takes into account.
     *
     * @param int|null $width The desired width, or null to only use the height.
     * @param int|null $height The desired height, or null to only use the width.
     * @return string|false False when a problem occurs, else the image data.
     * @since Totara 13
     */
    public function resize_image($width, $height) {
        global $CFG;
        require_once($CFG->libdir . '/gdlib.php');

        // Fetch the image information for this image.
        $imageinfo = $this->get_imageinfo(true);
        if (empty($imageinfo)) {
            return false;
        }

        // Create a new image from the file.
        $original = @imagecreatefromstring($this->get_content());

        // Generate the resized image.
        return resize_image_from_image($original, $imageinfo, $width, $height);
    }

    /**
     * Generate a crop-resized image for this stored_file.
     * Note that the EXIF orientation information takes into account.
     *
     * @param int|null $width The desired width, or null to only use the height.
     * @param int|null $height The desired height, or null to only use the width.
     * @return string|false False when a problem occurs, else the image data.
     * @since Totara 13
     */
    public function crop_image($width, $height) {
        global $CFG;
        require_once($CFG->libdir . '/gdlib.php');

        // Fetch the image information for this image.
        $imageinfo = $this->get_imageinfo(true);
        if (empty($imageinfo)) {
            return false;
        }

        // Create a new image from the file.
        $original = @imagecreatefromstring($this->get_content());

        return crop_resize_image_from_image($original, $imageinfo, $width, $height);
    }
}
