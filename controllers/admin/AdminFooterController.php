<?php 

/**
 * Admin Footer Controller Class
 */
class AdminFooterController extends Controller
{
    public function init()
    {     
        return $this->load->view('common/footer', null);
    }
}