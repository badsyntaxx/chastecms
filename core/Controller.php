<?php 

/**
 * Application Controller
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
        $this->gusto = Gusto::getInstance();
        foreach (get_object_vars($this->gusto) as $key => $value) {
            $this->$key = $value;
        }
    }
}