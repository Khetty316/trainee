<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;

/**
 * This is the model class for table "inventory_brand".
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
 * @property InventoryDetail[] $inventoryDetails
 */
class InventoryBrand extends \yii\db\ActiveRecord {

    const PREFIX = 'BRD';
    const RUNNING_NO_LENGTH = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_brand';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['active_sts', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'name'], 'string', 'max' => 255],
            [['code'], 'unique'],
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
            'active_sts' => 'Active',
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

    /**
     * Gets query for [[InventoryDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryDetails() {
        return $this->hasMany(InventoryDetail::className(), ['brand_id' => 'id']);
    }

    public static function getBomDropdownlist() {
        return \yii\helpers\ArrayHelper::map(self::find()->where(['active_sts' => 2])->orderBy(['name' => SORT_ASC])->all(), "id", "name");
    }
    
    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->code = $this->generateBrandCode();
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }

        return parent::beforeSave($insert);
    }

    public function generateBrandCode() {
        $brandPrefix = $this->getBrandPrefix($this->name);
        $baseCode = self::PREFIX . '-' . $brandPrefix;
        $lastCode = self::find()
                ->select('code')
                ->where(['like', 'code', $baseCode . '%', false])
                ->orderBy(['id' => SORT_DESC])
                ->scalar();

        $runningNo = 1;

        if ($lastCode) {
            preg_match('/(\d+)$/', $lastCode, $matches);
            $runningNo = ((int) $matches[1]) + 1;
        }

        $runningNo = str_pad($runningNo, self::RUNNING_NO_LENGTH, '0', STR_PAD_LEFT);
        return $baseCode . '-' . $runningNo;
    }

    private function getBrandPrefix($name, $length = 3) {
        $clean1 = preg_replace('/[^A-Za-z]/', '', $name); // remove symbols
        $clean2 = strtoupper($clean1);

        return substr($clean2, 0, $length);
    }

    public function getAllDropDownBrandList() {
        return \yii\helpers\ArrayHelper::map(self::find()->where(['active_sts' => 2])->orderBy(['name' => SORT_ASC])->all(), "id", "name");
    }
    
    /* ==============================
     * NORMALIZE NAME
     * ============================== */

    public static function normalizeName($name) {
        $name = strtoupper($name);
        $name = preg_replace('/[^A-Z0-9 ]/', '', $name);
        return trim(preg_replace('/\s+/', ' ', $name));
    }

    /* ==============================
     * CHECK SIMILAR BRAND
     * ============================== */

    public static function findSimilarBrand($name, $threshold = 80) {
        $normalizedInput = self::normalizeName($name);
        $brands = self::find()->all();

        foreach ($brands as $brand) {
            $normalizedDb = self::normalizeName($brand->name);
            similar_text($normalizedInput, $normalizedDb, $percent);

            if ($percent >= $threshold) {
                return [
                    'match' => true,
                    'percent' => round($percent, 2),
                    'existing_name' => $brand->name,
                ];
            }
        }

        return ['match' => false];
    }
}
