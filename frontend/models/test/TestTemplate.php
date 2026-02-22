<?php

namespace frontend\models\test;

use Yii;
use common\models\User;
use yii\web\Response;
use yii\web\UploadedFile;
use frontend\models\test\RefTestFormList;

/**
 * This is the model class for table "test_template".
 *
 * @property int $id
 * @property string|null $doc_ref
 * @property string|null $rev_no
 * @property string|null $formcode
 * @property string|null $proctest1
 * @property string|null $proctest2
 * @property string|null $proctest3
 * @property int|null $active_sts
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class TestTemplate extends \yii\db\ActiveRecord {

    public $files;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_template';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proctest1', 'proctest2', 'proctest3'], 'string'],
            [['active_sts', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['doc_ref', 'rev_no'], 'string', 'max' => 255],
            [['formcode'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'doc_ref' => 'Document Reference',
            'rev_no' => 'Rev. No',
            'formcode' => 'Form',
            'proctest1' => 'Procedure Test 1',
            'proctest2' => 'Procedure Test 2',
            'proctest3' => 'Procedure Test 3',
            'active_sts' => 'Active',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getUpdatedBy() {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public function uploadImage() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $uploadedFile = UploadedFile::getInstanceByName('file');

        if ($uploadedFile) {
            $fileContents = file_get_contents($uploadedFile->tempName);

            $base64Image = 'data:' . $uploadedFile->type . ';base64,' . base64_encode($fileContents);

            return ['imagePath' => $base64Image];
        } else {

            return ['error' => 'No image uploaded.'];
        }
    }
    
//    public function uploadImage() {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $uploadedFile = UploadedFile::getInstanceByName('file');
//
//        if ($uploadedFile) {
//            $maxSize = 80 * 1024; // 80KB in bytes
//            // Get original image info
//            $imageInfo = getimagesize($uploadedFile->tempName);
//            $originalWidth = $imageInfo[0];
//            $originalHeight = $imageInfo[1];
//            $mimeType = $imageInfo['mime'];
//
//            // Create image resource based on type
//            switch ($mimeType) {
//                case 'image/jpeg':
//                    $image = imagecreatefromjpeg($uploadedFile->tempName);
//                    break;
//                case 'image/png':
//                    $image = imagecreatefrompng($uploadedFile->tempName);
//                    break;
//                case 'image/gif':
//                    $image = imagecreatefromgif($uploadedFile->tempName);
//                    break;
//                default:
//                    return ['error' => 'Unsupported image format.'];
//            }
//
//            // Start with original dimensions
//            $newWidth = $originalWidth;
//            $newHeight = $originalHeight;
//            $quality = 90; // Start with high quality
//
//            do {
//                // Create new image with current dimensions
//                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
//
//                // Preserve transparency for PNG and GIF
//                if ($mimeType == 'image/png' || $mimeType == 'image/gif') {
//                    imagealphablending($resizedImage, false);
//                    imagesavealpha($resizedImage, true);
//                    $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
//                    imagefill($resizedImage, 0, 0, $transparent);
//                }
//
//                // Resize image
//                imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
//
//                // Capture output
//                ob_start();
//                switch ($mimeType) {
//                    case 'image/jpeg':
//                        imagejpeg($resizedImage, null, $quality);
//                        break;
//                    case 'image/png':
//                        imagepng($resizedImage, null, 9 - round(($quality / 100) * 9));
//                        break;
//                    case 'image/gif':
//                        imagegif($resizedImage, null);
//                        break;
//                }
//                $imageData = ob_get_contents();
//                ob_end_clean();
//
//                $currentSize = strlen($imageData);
//
//                // If size is acceptable, break
//                if ($currentSize <= $maxSize) {
//                    break;
//                }
//
//                // Reduce quality first (for JPEG and PNG)
//                if ($quality > 10 && ($mimeType == 'image/jpeg' || $mimeType == 'image/png')) {
//                    $quality -= 10;
//                } else {
//                    // If quality is already low, reduce dimensions
//                    $newWidth = intval($newWidth * 0.9);
//                    $newHeight = intval($newHeight * 0.9);
//                    $quality = 90; // Reset quality for new dimensions
//                    // Prevent infinite loop - minimum size
//                    if ($newWidth < 50 || $newHeight < 50) {
//                        break;
//                    }
//                }
//
//                imagedestroy($resizedImage);
//            } while ($currentSize > $maxSize);
//
//            // Clean up
//            imagedestroy($image);
//            if (isset($resizedImage)) {
//                imagedestroy($resizedImage);
//            }
//
//            // Convert to base64
//            $base64Image = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
//
//            return [
//                'imagePath' => $base64Image,
//                'originalSize' => $uploadedFile->size,
//                'compressedSize' => strlen($imageData),
//                'dimensions' => $newWidth . 'x' . $newHeight
//            ];
//        } else {
//            return ['error' => 'No image uploaded.'];
//        }
//    }

    function cleanHtmlContent($htmlContent) {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $elements = $xpath->query('//*');

        foreach ($elements as $element) {
            $attributesToRemove = [];
            foreach ($element->attributes as $attr) {
                if (preg_match('/\s*\w+-?\w*:?\s*=\s*""/', $attr->nodeValue) ||
                        preg_match('/^\w+-?\w*:\s*$/', $attr->nodeName) ||
                        preg_match('/;""/', $attr->nodeValue)) {
                    $attributesToRemove[] = $attr->nodeName;
                }
            }
            foreach ($attributesToRemove as $attrName) {
                $element->removeAttribute($attrName);
            }

            if ($element->tagName === 'a') {
                $element->setAttribute('style', 'background-color: rgb(255, 255, 255);');
            }
        }

        $cleanedHtml = $dom->saveHTML();
        return $cleanedHtml;
    }
}
