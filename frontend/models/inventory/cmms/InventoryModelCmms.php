<?php

namespace frontend\models\inventory\cmms;

use Yii;
use common\models\User;

/**
 * This is the model class for table "inventory_model_cmms".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $description
 * @property string|null $unit_type
 * @property string|null $image
 * @property int|null $active_sts
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class InventoryModelCmms extends \yii\db\ActiveRecord {

    const Prefix_ModelCode = "MOD";
    const runningNoLength = 5;

    public $scannedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_model_cmms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['active_sts', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'description', 'image'], 'string', 'max' => 255],
            [['unit_type'], 'string', 'max' => 100],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
            ['scannedFile', 'file', 'extensions' => "png, jpg, jpeg, pdf", 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg'], 'checkExtensionByMimeType' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'description' => 'Description',
            'unit_type' => 'Unit Type',
            'image' => 'Image',
            'active_sts' => 'Active Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'scannedFile' => 'Image',
        ];
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
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

    public function generateModelCode() {
        $currentYear = date("Y");
        $currentMonth = date("m");
        $currentYearShort = date("y");

        $initialCode = self::Prefix_ModelCode;
        $query = self::find()->where(['YEAR(created_at)' => $currentYear]);

        $runningNo = $query->count() + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $code = $initialCode . $runningNo . "-" . $currentMonth . $currentYearShort;

        return $code;
    }
    
    public function getAllDropDownModelList(){
        return \yii\helpers\ArrayHelper::map(self::find()->where(['active_sts' => 1])->all(), "code", "description");
    }
}
