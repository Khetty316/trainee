<?php

namespace frontend\models\profile;

use Yii;
use common\models\User;
use common\models\myTools\MyFormatter;
use common\models\myTools\FlashHandler;

/**
 * This is the model class for table "user_documents".
 *
 * @property int $id
 * @property int $user_id
 * @property string $doctype_code
 * @property string|null $doc_file_link
 * @property string|null $doc_date
 * @property string|null $doc_expiry_date
 * @property string|null $description
 * @property string $created_at
 * @property int|null $created_by
 * @property string $updated_at
 * @property int|null $udpated_by
 *
 * @property RefUserDoctypes $doctypeCode
 * @property User $createdBy
 * @property User $udpatedBy
 * @property User $user
 */
class UserDocuments extends \yii\db\ActiveRecord {

    public $scannedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'user_documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'doctype_code'], 'required'],
            [['user_id', 'created_by', 'udpated_by'], 'integer'],
            [['doc_date', 'doc_expiry_date', 'created_at', 'updated_at'], 'safe'],
            [['doctype_code'], 'string', 'max' => 20],
            [['doc_file_link', 'description'], 'string', 'max' => 255],
            [['doctype_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefUserDoctypes::className(), 'targetAttribute' => ['doctype_code' => 'code']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['udpated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['udpated_by' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
            ['scannedFile', 'file', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'doctype_code' => 'Document Type',
            'doc_file_link' => 'Link',
            'doc_date' => 'Document Date',
            'doc_expiry_date' => 'Document Expiry Date',
            'description' => 'Description',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'udpated_by' => 'Udpated By',
        ];
    }

    /**
     * Gets query for [[DoctypeCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDoctypeCode() {
        return $this->hasOne(RefUserDoctypes::className(), ['code' => 'doctype_code']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UdpatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUdpatedBy() {
        return $this->hasOne(User::className(), ['id' => 'udpated_by']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * **************************************** MAIN FUNCTION
     * @return boolean
     */
    public function processAndSave() {
        $this->doc_date = MyFormatter::fromDateRead_toDateSQL($this->doc_date);
        $this->doc_expiry_date = MyFormatter::fromDateRead_toDateSQL($this->doc_expiry_date);
        $this->user_id = Yii::$app->user->identity->id;
        if ($this->save(false)) {
            if ($this->validate() && $this->scannedFile) {

                $filePath = Yii::$app->params['user_profile_file_path'] . Yii::$app->user->identity->id;
                if (!is_dir($filePath)) {
                    mkdir($filePath);
                }

                $filePath .= "/doc/";
                $this->doc_file_link = 'doc_' . Yii::$app->user->identity->id . "_" . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
                if (!is_dir($filePath)) {
                    mkdir($filePath);
                }
                $this->scannedFile->saveAs($filePath . $this->doc_file_link);
                $this->update(false);
            }
            FlashHandler::success("User Document updated success!");
            return true;
        } else {
            FlashHandler::err("User Document updated FAIL!");
            return false;
        }
    }

}
