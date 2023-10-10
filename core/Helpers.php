<?php 

/**
* Helpers
*
* Random functions that can be helpful at different times.
*/

/**
 * Gets the url from the site and explodes it into pieces. Good for when you want to use 
 * the url like an array. website/page/param becomes array('website', 'page', 'param').
 *
 * @param string - $url - Should be the current URL.
 * @return array - The current URL in an array.
 */
function splitUrl($url)
{
    $url = explode('/', filter_var(trim($url, '/'), FILTER_SANITIZE_URL));
    return $url;
}

/**
 * If STRONG_PASSWORDS is set to "true" in the settings file then the checkPasswordStrength 
 * function is run. If the password check is set to run, then users who signup or change 
 * their passwords will be forced to create strong passwords. Strong password requirements 
 * are at least 1 upper case, 1 lower case, 1 number and 1 special character.
 *
 * @param string - $password - A password string.
 */
function isPasswordStrong($password)
{
    if (strlen($password) > 8 && preg_match('/[0-9]/', $password) == true && preg_match('/[A-Z]/', $password) == true && preg_match('/[!@#$%]/', $password) == true) {
        return true;
    }
}

/**
 * Get a template file to use.
 * 
 * @param string - $template - Sub dir and filename. Example "email/activate"
 * @return string - The text in the file.
 */
function getTemplate($template)
{
    $file = ROOT_DIR . '/storage/templates/' . $template . '.txt';
    ob_start();
    require ($file);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}

/**
 * This function gets a list of files in a given dir.
 * 
 * @param string - $dir - Directory to scan.
 * @return array - List of files found in dir.
 */
function getFileList($dir)
{
    $dir .= '/';
    $found = array_diff(scandir($dir), ['.', '..', 'index.htm']);

    foreach ($found as $f) {
        if (is_file($dir . $f)) {
            $files[] = $f;
        }
    }

    natcasesort($files);

    return $files;
}

/**
 * Expects the "red_herring" input from forms as a parameter. If the "red_herring" input 
 * is not empty then exit immediately. The assumption being that only bots would fill 
 * out these hidden fields.
 */
function botTest($input) 
{
    if (!empty($input)) {
        exit('Ah ah ah, you didn\'t say the magig word!');
    }
}

/**
 * Get the ammount of days its been since a specific date.
 * 
 * @param string - $date - The date to check.
 * @return int - Number of days ago.
 */
function getDaysAgo($date)
{
    if (isset($date)) {
        $today = date('c');
        $diff = strtotime($today) - strtotime($date);
        $days_ago = (int) $diff / (60 * 60 * 24);
        return (int) round($days_ago);
    } else {
        return 0;
    }
}
