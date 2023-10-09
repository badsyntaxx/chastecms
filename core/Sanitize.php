<?php 

/**
 * Sanitize
 */
class Sanitize
{   
    /**
     * Escape strings
     * 
     * Sanitize strings passed to this method and return them.
     * 
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        return htmlentities(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
    }

    public function clean($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                // If an item in the view data array is a sub array, iterate through the sub array 
                // and sanitize the values then push the data to the data array. If the item is 
                // not a sub array sanitize the data and push it to the data array.
                if (is_array($value)) {
                    foreach ($value as $v) {
                        if (is_array($v)) {
                            array_walk($v, [$this, 'escape']);
                        }
                    }
                    $data[$key] = $v;
                } else {
                    $data[$key] = htmlentities(trim(strip_tags($value)), ENT_QUOTES, 'UTF-8');
                }
            }
        } else {
            $data = $this->escape($data);
        }

        return $data;
    }

    public function email($email)
    {
        return filter_var(strtolower($email), FILTER_SANITIZE_EMAIL);  
    }

    public function int($int)
    {
        return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
    }

    public function url($url)
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    public function string($string)
    {
        return filter_var($string, FILTER_SANITIZE_STRING);
    }

    public function specChars($specChars)
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $specChars);
    }
}