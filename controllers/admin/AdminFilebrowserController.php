<?php 

/**
* Admin Filebrowser Controller Class
*/
class AdminFilebrowserController extends Controller
{
    public function browse()
    {
        $result = $this->load->library('filebrowser')->browse();
        echo json_encode($result);
    }

    public function makeFolder()
    {
        $filebrowser = $this->load->library('filebrowser');
        $result = $filebrowser->makeFolder();
        if ($result === 'folder_exists') {
            exit($this->language->get('filebrowser/folder_exists'));
        } elseif ($result === 'folder_created') {
            exit($this->language->get('filebrowser/folder_created'));
        } else {
            exit('UNABLE TO CREATE FOLDER UNKNOWN REASON!!!!!!!!!!!!!!!!!');
        }
    }

    public function delete() 
    {
        $filebrowser = $this->load->library('filebrowser');
        $result = $filebrowser->delete();
        switch ($result) {
            case 'file_deleted':
                exit($this->language->get('filebrowser/file_deleted'));
                break;
            case 'folder_deleted':
                exit($this->language->get('filebrowser/folder_deleted'));
                break;
            case 'system_file':
                exit($this->language->get('filebrowser/system_file'));
                break;
            default:
                exit('Unknown Event!');
                break;
        }
    }
}