<?php 

/**
 * Output Core Class
 * 
 * Output different types of data to the view.
 */
class Output
{
    /**
     * Output JSON
     *
     * Take an array and convert to json then tepending on
     * the action exit, return or echo the json.
     * 
     * @param array $array
     * @param boolean $action
     * @return void
     */
    public function json($array, $action = false)
    {
        switch ($action) {
            case 'exit':
                exit(json_encode($array));
                break;
            case 'return':
                return json_encode($array);
                break;
            default:
                echo json_encode($array);
                break;
        }
    }

    /**
     * Output Text
     * 
     * Return plain text.
     *
     * @param string $text
     * @return void
     */
    public function text($content)
    {
        $content = preg_replace('@<script[^>]*?>.*?</script>@si', '', $content);
        $content = preg_replace('@<style[^>]*?>.*?</style>@si', '', $content);
        $content = strip_tags($content);
        $content = trim($content);
        return $content;
    }
}