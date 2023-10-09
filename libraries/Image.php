<?php 

/**
 * Image Library Class
 *
 * This library is used to process images that have been uploaded.
 */
class Image
{   
    /**
     * This method targets an image in a given directory and makes adjustments to it.
     * Good for if you need to change the width, height or crop an image on the server.
     * 
     * @param string - $source - Source image.
     * @param integer - $width - Desired width for new image.
     * @param integer - $height - Desired height for new image.
     * @param string - $filename - Desired filename for new image.
     * @param string - $directory -Directory for new image.
     * 
     * @return boolean - Return true or false.
     */
    public function cropImage($source = '', $width = 60, $height = 60, $filename = '', $directory = '')
    {
        $type = mime_content_type($source); // Determine the file type of the source image.
        
        if ($type == 'image/jpg' || $type == 'image/jpeg') { // Create a new image from the source file based on its file type.
            $image = imagecreatefromjpeg($source);
        }
        if ($type == 'image/png') {
            $image = imagecreatefrompng($source);
        }
        if ($type == 'image/gif') {
            $image = imagecreatefromgif($source);
        }

        list($source_width, $source_height) = getimagesize($source); // Get the width and height from the source image.

        if ($source_width > $source_height) { // Do some math I don't understand. Thanks stack overflow. Pretty sure it crops the image proportionately.
            $new_height = $height;
            $new_width = floor($source_width * ($new_height / $source_height));
            $crop_x = ceil(($source_width - $source_height) / 2);
            $crop_y = 0;
        } else {
            $new_width = $width;
            $new_height = floor($source_height * ($new_width / $source_width));
            $crop_x = 0;
            $crop_y = ceil(($source_height - $source_width) / 2);
        }

        $tmp = imagecreatetruecolor($width, $height); // Create a temporary image

        if ($type == 'image/gif' || $type == 'image/png') {
            imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
        }
        
        imagecopyresampled($tmp, $image, 0, 0, $crop_x, $crop_y, $new_width, $new_height, $source_width, $source_height); // Copy and resize part of the new image with resampling.

        if ($type == 'image/gif' || $type == 'image/png') {
            header('Content-Type: image/png');
            imagepng($tmp, ROOT_DIR . $directory . '/' . $filename, 0); 
        } else {
            header('Content-Type: image/jpeg');
            imagejpeg($tmp, ROOT_DIR . $directory . '/' . $filename, 100);  
        }

        imagedestroy($image);
        imagedestroy($tmp);
        return true; 
    }
}