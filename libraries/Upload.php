<?php 

/**
 * Upload Library Class
 *
 * This class is will be used to validate and upload files to the server.
 */
class Upload
{
    /**
     * Name of the file to upload.
     * @var string
     */
    public $filename;

    /**
     * Temporary name of the file to upload.
     * @var string
     */
    public $filetmp;

    /**
     * Type of the file to upload.
     * @var string
     */
    public $filetype;

    /**
     * Size of the file to upload.
     * @var int
     */
    public $filesize;

    /**
     * Error if the selected file is invalid.
     * @var string
     */
    public $file_error;

    /**
     * Array of the filename to upload.
     * @var array
     */
    public $tmp;

    /**
     * Extension of the file to upload.
     * @var string
     */
    public $extention;

    /**
     * Alert if the file to upload has an invalid extension.
     * @var boolean
     */
    public $file_invalid = false;

    /**
     * Alert if the file to upload is too large.
     * @var boolean
     */
    public $file_big = false;

    /**
     * Alert if the upload is successful or not.
     * @var boolean
     */
    public $upload_success = false;

    public function uploadImage($source, $directory, $size = 1048576)
    {
        $extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $max_size = $size;

        $this->process($source);
        $this->validate($extensions, $max_size);

        if ($this->move($directory)) {
            $this->upload_success = true;
        }
    }

    public function uploadDoc($source, $directory, $size = 1048576)
    {
        $extensions = ['txt', 'pdf', 'doc', 'docx', 'xls', 'xml'];
        $max_size = $size;

        $this->process($source);
        $this->validate($extensions, $max_size);
        
        if ($this->move($directory)) {
            $this->upload_success = true;
        }
    }

    public function validate($extensions, $max_size) 
    {
        if (in_array($this->extention, $extensions) === false) {
            $this->file_invalid = true;
        } 

        $size = $this->formatBytes($this->filesize);
        $max = $this->formatBytes($max_size);

        if ($this->filesize > $max_size) {
            $this->file_big = ['size' => $size, 'max' => $max];
        }
    }

    public function formatBytes($bytes, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
    
        // Uncomment one of the following alternatives
        // $bytes /= pow(1024, $pow);
        $bytes /= (1 << (10 * $pow)); 
    
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    } 

    public function process($source)
    {
        $this->filename = trim(strtolower(str_replace(' ', '_', $source['name'])));          
        $this->filename = mt_rand(100000, 999999) . '_' . $this->filename;
        $this->filename = preg_replace('#[^a-z0-9._]#i', '', $this->filename);
        $this->filetmp = $source['tmp_name'];
        $this->filetype = $source['type'];
        $this->filesize = $source['size'];
        $this->file_error = $source['error'];
        $this->tmp = explode('.', $this->filename);
        $this->extention = strtolower(end($this->tmp));
    }

    public function move($directory)
    {
        if (!$this->file_invalid && !$this->file_big) {
            if (move_uploaded_file($this->filetmp, ROOT_DIR . $directory . $this->filename)) {
                return true;
            } else {
                return false;
            }
        }
    }
}