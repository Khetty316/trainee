<?php

namespace frontend\models\quotation;

use Yii;
use common\models\User;
/**
 * This is the model class for table "quotation_details".
 *
 * @property int $id
 * @property int $quotation_master_id
 * @property string|null $filename
 * @property string|null $supplier_name
 * @property int $is_selected
 * @property string|null $remark
 * @property int $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property QuotationMasters $quotationMaster
 */
class QuotationDetails extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'quotation_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['quotation_master_id', 'created_by'], 'required'],
            [['quotation_master_id', 'is_selected', 'created_by', 'updated_by'], 'integer'],
            [['remark'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['filename', 'supplier_name'], 'string', 'max' => 255],
            [['quotation_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuotationMasters::className(), 'targetAttribute' => ['quotation_master_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'quotation_master_id' => 'Quotation Master ID',
            'filename' => 'Filename',
            'supplier_name' => 'Supplier Name',
            'is_selected' => 'Is Selected',
            'remark' => 'Remark',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }
        /**
     * Gets query for [[ManagerApproveBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return User::findOne($this->created_by);
    }

    /**
     * Gets query for [[QuotationMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotationMaster() {
        return $this->hasOne(QuotationMasters::className(), ['id' => 'quotation_master_id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

    public function insertNew($quotationMasterId, $filename) {
        $this->filename = $filename;
        $this->quotation_master_id = $quotationMasterId;
        $this->created_by = Yii::$app->user->id;
        $this->save();
    }

}
