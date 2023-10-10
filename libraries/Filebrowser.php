<?php 

/**
 * File Browser
 *
 * The file browser library will interface with the server to browse,
 * create and delete files on the server.
 */
class FileBrowser
{
    public function browse()
    {
        $dirs = [];
        $files = [];

        if (!empty($_POST['dir'])) {
            $dir = ROOT_DIR . $_POST['dir'];
        }

        $things = array_diff(scandir($dir), ['.', '..']);

        foreach ($things as $thing) {
            if (is_dir($dir . $thing)) {
                $dirs[] = $thing;
            }
            if (is_file($dir . $thing)) {
                $files[] = $thing;
            }
        }

        natcasesort($dirs);
        natcasesort($files);
        $dirs = array_values($dirs);
        $files = array_values(array_diff($files, ['index.htm']));
        $things = ['dirs' => $dirs, 'files' => $files];

        return $things;
    }

    public function makeFolder()
    {
        $foldername = str_replace(' ', '_', $_POST['foldername']);
        $dir = ROOT_DIR . $_POST['new_folder_dir'];

        if (is_dir($dir . $foldername)) {
            return 'folder_exists';
        }
        if (mkdir($dir . $foldername)) {
            $forbidden = fopen($dir . $foldername . '/' . 'index.htm', 'w') or exit('Unable to open file!');
            fwrite($forbidden, "<!DOCTYPE html>\n<html>\n<head>\n    <title>403 Forbidden</title>\n</head>\n<body>\n    <p>Directory access is forbidden.</p>\n</body>\n</html>");
            return 'folder_created';
        } else {
            return 'Unable';
        }
    }

    public function delete() 
    {
        $dir = ROOT_DIR . $_POST['dir'];
        $file = trim($_POST['file']);

        if ($file === 'default_blog.jpg' || $file === 'index.htm') {
            return 'system_file';
        }
        if (is_file($dir . $file)) {
            unlink($dir . $file);
            return 'file_deleted';
        } else {
            $dir = $dir . $file;
            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($files as $file) {
                if ($file->isDir()){
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }

            rmdir($dir);
            
            return 'folder_deleted';
        }
    }
}