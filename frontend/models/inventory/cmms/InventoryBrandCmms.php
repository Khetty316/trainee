<?php

namespace frontend\models\inventory\cmms;

use Yii;
use common\models\User;

/**
 * This is the model class for table "inventory_brand_cmms".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $name
 * @property int|null $active_sts
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class InventoryBrandCmms extends \yii\db\ActiveRecord {

    const Prefix_BrandCode = "BRD";
    const runningNoLength = 3;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_brand_cmms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['active_sts', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'name'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'active_sts' => 'Active Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
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

    public function generateBrandCode() {
        $currentYear = date("Y");

        $initialCode = self::Prefix_BrandCode;
        $query = self::find()->where(['YEAR(created_at)' => $currentYear]);

        $runningNo = $query->count() + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $claimCode = $initialCode . $runningNo;
        return $claimCode;
    }
    
    public function getAllDropDownBrandList(){
        return \yii\helpers\ArrayHelper::map(self::find()->where(['active_sts' => 1])->all(), "code", "name");
    }
}
