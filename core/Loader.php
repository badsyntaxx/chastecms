<?php 

/**
 * Loader Class
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
     * Require the core file if it's a file and exists. Exit with an error if not.
     * 
     * @return array - Core file names. 
     */
    public function cores()
    {
        $cores = [];
        $files = scandir(CORES_DIR);
        foreach ($files as $file) {
            $file = pathinfo($file);
            if ($file['extension'] == 'php') {
                if ($file['filename'] != 'Controller' && $file['filename'] != 'Model' && $file['filename'] != 'Gusto' && $file['filename'] != 'Database') {
                    array_push($cores, $file['filename']);
                }
            }
        }
        return $cores;
    }

    /**
     * Require and instantiate a controller class based on the parameter passed to the method.
     * The parameter can be a plain lower case string becuase this method will adjust the 
     * name accordingly.
     * @example "home" becomes "HomeController".
     * 
     * @param string - $controller - The controller name.
     * @return object - The controller object.
     */
    public function controller($controller)
    {   
        if (strpos($controller, '/')) {
            $keys = explode('/', $controller);
            foreach ($keys as $key) {
                $array[] = ucfirst($key);
            }
            $controller = implode($array);
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
     * Require and instantiate the model class based on the parameter passed to the method.
     * The parameter can be a plain lower case string becuase this method will adjust the 
     * name accordingly.
     * @example "users" becomes UsersModel.
     * 
     * @param string - $model - The model name.
     * @return object - The model object.
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
     * Process the view data for display in the view and require the view file.
     * Exit with notification if file cannot be found or opened.
     * 
     * @param string - $view - The view file name to get. 
     * @param array - $data - Data created in a controller to be shown in the view
     * @return string - The view from the view file.
     */
    public function view($view, $data = [])
    {
        $theme_dir = $this->model('settings')->getSetting('theme');

        if (isAdmin()) {
            $theme_dir = 'admin';
        }

        if (is_array($data)) {
            extract($data);
        }

        $file = VIEWS_DIR . '/themes/' . $theme_dir . '/htm/' . $view . '.htm';

        if (is_file($file)) {
            ob_start();
            require_once $file;
            $content = ob_get_clean();
            return $content;
        }

        exit('The view file ( ' . VIEWS_DIR .  '/themes/' . $theme_dir . '/htm/' . $view . '.htm ) cannot be found.');
    }

    /**
     * Require and instantiate a library class based on the parameter passed to the method.
     * The parameter can be a plain lower case string becuase this method will adjust the 
     * name accordingly.
     * 
     * @param string - $library - The library name.
     * @return object - The library object.
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
     * Redirects user to a given route.
     * 
     * @param string - $route - The route to send the user to.
     */
    public function route($route)
    {
        exit(header('Location:' . $route));
    }
}