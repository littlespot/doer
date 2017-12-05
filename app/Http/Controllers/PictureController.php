<?php

namespace Zoomov\Http\Controllers;
use Auth;
use Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Zoomov\Image;

class PictureController extends Controller
{
   public function upload(Request $request){
       $base64_image = str_replace(' ', '+', $request['base64']);

       if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)){
           $extension = $result[2] == 'jpeg' ? '.jpg':'.'.$result[2];

           $parent = $request->input('parent_id', Auth::id());
           $directory  = '/uploads/'.$request['picture_dst'].'/'.$parent;

           if(!is_dir(public_path($directory))){
               Storage::disk('images')->makeDirectory($directory);
           }

           $image = new Image($directory, time(), $extension);
           $src_img = $image->getDestination('',$extension);

           if (file_put_contents(public_path($src_img), base64_decode(str_replace($result[1], '', $base64_image))) && $request->input('image_width', '')){
               list($src_w,$src_h)=getimagesize(public_path($src_img));
               $src_scale = $src_h/$src_w;
               $dst_w = $request->input('image_width');
               $dst_h = $src_scale * $dst_w;
               $target = imagecreatetruecolor($dst_w, $dst_h);
               $source= $this->getSource(public_path($src_img), $image);
             /*  $croped=imagecreatetruecolor($w, $h);
               imagecopy($croped, $source, 0, 0, $x, $y, $src_w, $src_h);

               $scale = $dst_w / $w;
               $target = imagecreatetruecolor($dst_w, $dst_h);
               $final_w = intval($w * $scale);
               $final_h = intval($h * $scale);*/
               imagecopyresampled($target, $source, 0, 0, 0, 0, $dst_w,$dst_h, $src_w, $src_h);

               $target_img = $directory.'/'.time().'.jpg';

               imagejpeg($target, public_path($target_img));
               imagedestroy($target);

               return $target_img;
           }

           return $src_img;
       }else{
           return Response('Please upload image file', 400);
       }
   }

    public function crop(Request $request) {
        $file = $_FILES['picture_file'];
        $image = new Image($request['picture_dst'], $request['picture_name']);
        return $this->setFile($file, json_decode(stripslashes($request['picture_data'])), $image);
    }

    private function setFile($file, $data, Image $image) {
        $errorCode = $file['error'];

        if ($errorCode === UPLOAD_ERR_OK) {
            $type = exif_imagetype($file['tmp_name']);

            if ($type) {
                $extension = image_type_to_extension($type);

                $src =  public_path($image->getDestination(config('constants.image.original'), $extension));

                if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_JPEG || $type == IMAGETYPE_PNG) {
                    if (file_exists($src)) {
                        unlink($src);
                    }


                    $result = move_uploaded_file($file['tmp_name'], $src);

                    if ($result) {
                        return $this->save($src, $data, $image);
                    } else {
                        return Response('Failed to save file', 400);
                    }
                } else {
                    return Response('Please upload image with the following types: JPG, PNG, GIF', 400);
                }
            } else {
                return Response('Please upload image file', 400);
            }
        } else {
            return Response($this->getMessage($errorCode), 400);
        }
    }

    private function getSource($src, Image $image){
        $type = exif_imagetype($src);

        if ($type) {
            $image->extension = image_type_to_extension($type);
        }

        switch ($type) {
            case IMAGETYPE_GIF:
                $src_img = imagecreatefromgif($src);
                break;

            case IMAGETYPE_JPEG:
                $src_img = imagecreatefromjpeg($src);
                break;

            case IMAGETYPE_PNG:
                $src_img = imagecreatefrompng($src);
                break;

            default:
                $src_img = imagecreatefromwbmp($src);
                break;
        }

        return $src_img;
    }

    private  function save($src, $data, Image $image){
        $src_img = $this->getSource($src, $image);
        if (!$src_img) {
            return Response("Failed to read the image file", 400);
        }

        $size = getimagesize($src);
        $size_w = $size[0]; // natural width
        $size_h = $size[1]; // natural height

        $src_img_w = $size_w;
        $src_img_h = $size_h;

        $degrees = $data -> rotate;

        // Rotate the source image
        if (is_numeric($degrees) && $degrees != 0) {
            // PHP's degrees is opposite to CSS's degrees
            $new_img = imagerotate( $src_img, -$degrees, imagecolorallocatealpha($src_img, 0, 0, 0, 127) );

            imagedestroy($src_img);
            $src_img = $new_img;

            $deg = abs($degrees) % 180;
            $arc = ($deg > 90 ? (180 - $deg) : $deg) * M_PI / 180;

            $src_img_w = $size_w * cos($arc) + $size_h * sin($arc);
            $src_img_h = $size_w * sin($arc) + $size_h * cos($arc);

            // Fix rotated image miss 1px issue when degrees < 0
            $src_img_w -= 1;
            $src_img_h -= 1;
        }

        $tmp_img_w = $data -> width;
        $tmp_img_h = $data -> height;
        $dst_img_w = $image->getWidth();
        $dst_img_h = $image->getHeight();

        $src_x = $data -> x;
        $src_y = $data -> y;

        if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
            $src_x = $src_w = $dst_x = $dst_w = 0;
        } else if ($src_x <= 0) {
            $dst_x = -$src_x;
            $src_x = 0;
            $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
        } else if ($src_x <= $src_img_w) {
            $dst_x = 0;
            $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
        }

        if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
            $src_y = $src_h = $dst_y = $dst_h = 0;
        } else if ($src_y <= 0) {
            $dst_y = -$src_y;
            $src_y = 0;
            $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
        } else if ($src_y <= $src_img_h) {
            $dst_y = 0;
            $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
        }

        // Scale to destination position and size
        $ratio = $tmp_img_w / $dst_img_w;
        $dst_x /= $ratio;
        $dst_y /= $ratio;
        $dst_w /= $ratio;
        $dst_h /= $ratio;

        $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

        // Add transparent background to destination image
        imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
        imagesavealpha($dst_img, true);

        $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

        if ($result) {
            if (!imagejpeg($dst_img, public_path($image->getDestination('','.jpg')))) {
                return Response("Failed to read the image file", 400);
            }
        } else {
            return Response("Failed to read the image file", 400);
        }

        $thumb = imagecreatetruecolor($dst_img_w/2, $dst_img_h/2);
        imagecopyresampled($thumb, $dst_img, 0, 0, 0, 0, $dst_img_w/2, $dst_img_h/2, $dst_img_w, $dst_img_h);
        imagejpeg($thumb, public_path($image->getDestination(config('constants.image.thumbnail'), '.jpg')), 90);

        imagedestroy($thumb);

        $thumb = imagecreatetruecolor($dst_img_w/5, $dst_img_h/5);
        imagecopyresampled($thumb, $dst_img, 0, 0, 0, 0, $dst_img_w/5, $dst_img_h/5, $dst_img_w, $dst_img_h);
        imagejpeg($thumb, public_path($image->getDestination(config('constants.image.small')), '.jpg'), 90);

        imagedestroy($thumb);

        imagedestroy($src_img);
        imagedestroy($dst_img);

        return Response($image->getDestination('','.jpg'), 200);
    }

    private function getMessage($code){
        $errors = array(
            UPLOAD_ERR_INI_SIZE =>'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE =>'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL =>'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE =>'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR =>'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE =>'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION =>'File upload stopped by extension',
        );

        if (array_key_exists($code, $errors)) {
            return $errors[$code];
        }

        return 'Unknown upload error';
    }
}
