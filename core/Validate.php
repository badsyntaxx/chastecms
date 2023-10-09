<?php 

/**
 * Validate
 */
class Validate
{   
    public function email($email)
    {
        $email = empty($email) ? 'empty' : filter_var($email, FILTER_VALIDATE_EMAIL);

        return $email;
    }

    public function phone($phone)
    {
        if (empty($phone)) return 'empty';
        
        if (preg_match('/[^0-9\- ]/', $phone)) return false;

        if (strlen($phone) > 10) return 'long';
            
        return $phone;
    }

    public function int($int)
    {
        return filter_var($int, FILTER_VALIDATE_INT);
    }

    public function url($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public function string($string)
    {
        if ($string != strip_tags($string)) {
            return false;
        }

        return $string;
    }

    public function specChars($specChars)
    {
        if (preg_match('/[^a-zA-Z0-9]/', trim($specChars))) {
            return false;
        } else {
            return $specChars;
        }
    }
}