<?php

namespace common\models\myTools;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "customer".
 *
 * @property int $customer_id
 * @property string|null $customer_name
 * @property string|null $customer_ic
 * @property string|null $phone_no
 * @property string|null $email
 * @property string|null $created_by
 * @property string $created_date
 */
class ImageHandler extends Model {

    function resize_image($file, $w, $h, $crop = FALSE) {

        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width - ($width * abs($r - $w / $h)));
            } else {
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w / $h > $r) {
                $newwidth = $h * $r;
                $newheight = $h;
            } else {
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefromjpeg($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return $dst;
    }

    public static function resize_image_w1200($file, $extension = "") {
        $newwidth = 1200;
        $Orientation = 0;

        $src = "";
        if ($extension == "png") {
            $src = imagecreatefrompng($file);
        } else {
            $src = imagecreatefromjpeg($file);
            $exif = exif_read_data($file);
            if (array_key_exists("Orientation", $exif)) {
                $Orientation = $exif["Orientation"];
            }
        }



        list($width, $height) = getimagesize($file);


// REFER TO: https://www.php.net/manual/en/function.exif-read-data.php#76964
        if ($Orientation == 6) {
            $src = imagerotate($src, -90, 0);
            $temp = $width;
            $width = $height;
            $height = $temp;
        }



        $r = $width / $height;
        $newheight = $newwidth / $r;



        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
        imagejpeg($dst, $file);
        return $dst;
    }

}
