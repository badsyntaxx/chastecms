<?php 

/**
 * Language Core Class
 * 
 * This language class will be loaded in the Gusto object. So you can 
 * always use the language class in your controller classes.
 * @example $this->language->get('filename/key');
 */
class Language
{
    /**
     * Get the language settings from the settings table in the database.
     * 
     * @return string - The language setting.
     */
    public function getLanguageSetting()
    {
        $query = Database::getInstance()->mysqli->query('SELECT `value` FROM `settings` WHERE `name` = "language"');
        $lang = $query->fetch_object();  

        return $lang->value;
    }

    /**
     * The language class includes the language files and returns keys from them. 
     * The parameter passed to the get method should be a string with the filename 
     * and key seperated by a forward slash.
     * @example $this->language->get('file/key');
     * 
     * @param string - $path - String including the file and key. @example home/title
     * @return string - Text from the language file.
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
            include LANGUAGE_DIR . '/' . $language . '/home.php';
        }

        if (isset($_[$key])) {
            return $_[$key];
        } else {
            return 'Error: Language key (' . $key . ') not found in (' . $file . ').';
        }
    }
}