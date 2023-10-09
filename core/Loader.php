<?php 

/**
 * Loader Core Class
 *
 * The loader class is used to load the various components of the MVC application. 
 * Pass the file name to the method to load the class. The loading methods will 
 * handle including the necessary files and instantiating the class for that file.
 * The loading methods will also report an error if it cannot include the file or 
 * instantiate the class.
 */
class Loader
{   
    /**
     * Controller Load
     * 
     * Require and instantiate a controller class based on the parameter passed to the method.
     * The parameter should be the controllers parent dir and the controller file.
     * Expected param: account/signup
     * 
     * @param string $controller
     * @return object
     */
    public function controller($controller)
    {   
        $delimiters = ['/', '\\', '-', '_', '.'];

        foreach ($delimiters as $d) {
            if (strpos($controller, $d)) {
                $keys = explode($d, $controller);
                foreach ($keys as $key) {
                    $array[] = ucfirst($key);
                }
                $controller = implode($array);
            }
        }

        // Set the first character of the class name to an uppercase letter.
        $class = ucfirst($controller . 'Controller');

        // Instantiate the class.
        if (class_exists($class)) {
            $controller = new $class();
        }

        if (get_class($controller)) {
            return $controller;
        } else {
            exit('The Loader was unable to load the controller ' . $class . '.');
        }
    }

    /**
     * Model loader
     * 
     * Require and instantiate the model class based on the parameter 
     * passed to the method.
     * 
     * @param string $model
     * @return object
     */
    public function model($model)
    {
        $model = ucfirst($model . 'Model');

        if (class_exists($model)) {
            return new $model(); 
        } 

        exit('The "' . $model . '" class does not exist.');
    }

    /**
     * View loader
     * 
     * Process the view data for display in the view and require the view file.
     * Exit with notification if file cannot be found or opened.
     * 
     * @param string $view 
     * @param array $data 
     * @param array $raw_data
     */
    public function view($view, $data = [])
    {
        $theme_dir = $this->model('settings')->getSetting('theme');
        $file = VIEWS_DIR . '/front/themes/' . $theme_dir . '/htm/' . $view . '.htm';

        if (isAdmin()) {
            $file = VIEWS_DIR . '/admin/htm/' . $view . '.htm';
        }
        
        if (is_array($data)) {
            extract($data);
        }
        
        if (is_file($file)) {
            ob_start();
            require_once $file;
            $content = ob_get_clean();
            return $content;
        }

        exit('The view file ( ' . $file . ' ) cannot be found.');
    }

    /**
     * Library loader
     * 
     * Require and instantiate the library class based on the parameter passed to the method.
     * 
     * @param string $library
     * @return object
     */
    public function library($library)
    {
        $library = ucfirst($library);

        if (class_exists($library)) {
            return new $library();
        } 

        exit('The "' . $library . '" library class does not exist.');
    }

    /**
     * Route
     *
     * Redirects user to new route.
     * 
     * @param string $route
     */
    public function route($route)
    {
        exit(header('Location:' . $route));
    }
}