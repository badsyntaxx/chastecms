<?php 

/**
 * Admin Footer Controller Class
 */
class AdminFooterController extends Controller
{
    public function index()
    {     
        return $this->load->view('common/footer', null);
    }
}