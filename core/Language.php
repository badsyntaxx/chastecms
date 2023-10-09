<?php 

/**
 * Language Core Class
 * 
 * This language library will be loaded in the application controller. So you can 
 * always use the language library in your controller classes.
 * @example $this->language->get('filename/key');
 */
class Language
{
    /**
     * Get Language Setting
     * 
     * Get the language setting from the settings table and return it.
     *
     * @return string 
     */
    public function getLanguageSetting()
    {
        $query = Database::getInstance()->mysqli->query('SELECT `value` FROM `settings` WHERE `name` = "language"');
        $lang = $query->fetch_object();  
        return $lang->value;
    }

    /**
     * Return language keys
     * 
     * The language library includes the language files and returns keys from them. 
     * The parameter passed to the get method should be a string with the filename 
     * and key seperated by a forward slash.
     * @example $this->language->get('home/title');
     * 
     * @param string - $path - The path to the language file and key. file/key = home/title
     * @return string
     */
    public function get($path)
    {
        $language = $this->getLanguageSetting();
        $key_path = explode('/', $path);
        $file = isset($key_path[0]) ? $key_path[0] : 'home';
        $key = isset($key_path[1]) ? $key_path[1] : '';

        if (is_file(LANGUAGE_DIR . '/' . $language . '/' . $file . '.php')) {
            include LANGUAGE_DIR . '/' . $language . '/' . $file . '.php';
        } else {
            include LANGUAGE_DIR . '/' . $language . '/dashboard.php';
        }

        if (isset($_[$key])) {
            return $_[$key];
        } else {
            return 'Error: Language key (' . $key . ') not found in (' . $file . ').';
        }
    }
}