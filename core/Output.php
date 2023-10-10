<?php 

/**
 * Output Library Class
 */
class Output
{
    public function json($json, $exit = false)
    {
        if ($exit == 'exit') {
            exit(json_encode($json));
        } else {
            echo json_encode($json);
        }
    }

    public function html($html)
    {
        echo $html;
    }

    public function text($text)
    {
        echo $text;
    }
}