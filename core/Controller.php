<?php

/**
 * Controller Core Class
 *
 * The controller class is the main controller of the application system.
 * All controller classes will be extensions of this class.
 */
class Controller
{
    /**
     * Controller construct
     * 
     * Controller classes are extended from this class so everytime you load a
     * controller class this construct will be called.
     */
    public function __construct()
    {   
        foreach (Framework::getCores() as $name => $object) {
            $this->$name = $object;
        }
    }

    /**
     * Log messages by inserting them into the logs table in the database.
     * 
     * @param string - $message - The message to be inserted.
     */
    public function log($message)
    {
        $this->load->model('log')->insertLog($message);
    }
}
