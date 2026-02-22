<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;
use frontend\models\office\preReqForm\PrereqFormMaster;
use frontend\models\RefInventoryStatus;

/**
 * This is the model class for table "inventory_reorder_master".
 *
 * @property int $id
 * @property int|null $prereq_form_master_id
 * @property string|null $department_code
 * @property string|null $requested_at
 * @property int|null $requested_by
 * @property int|null $approved_by
 * @property string|null $created_at
 * @property int|null $created_by
 * @property int|null $status
 *
 * @property InventoryReorder[] $inventoryReorders
 * @property InventoryReorderItem[] $inventoryReorderItems
 * @property PrereqFormMaster $prereqFormMaster
 * @property User $requestedBy
 * @property User $approvedBy
 * @property RefInventoryStatus $status0
 * @property User $createdBy
 */
class InventoryReorderMaster extends \yii\db\ActiveRecord {

    public $prf_no;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_reorder_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['prereq_form_master_id', 'requested_by', 'approved_by', 'created_by', 'status'], 'integer'],
            [['requested_at', 'created_at', 'prf_no'], 'safe'],
            [['department_code'], 'string', 'max' => 50],
            [['prereq_form_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrereqFormMaster::className(), 'targetAttribute' => ['prereq_form_master_id' => 'id']],
            [['requested_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['requested_by' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approved_by' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefInventoryStatus::className(), 'targetAttribute' => ['status' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'prereq_form_master_id' => 'Prereq Form Master ID',
            'department_code' => 'Department Code',
            'requested_at' => 'Requested At',
            'requested_by' => 'Requested By',
            'approved_by' => 'Approved By',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[InventoryReorders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorders() {
        return $this->hasMany(InventoryReorder::className(), ['inventory_reorder_master_id' => 'id']);
    }

    /**
     * Gets query for [[InventoryReorderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderItems() {
        return $this->hasMany(InventoryReorderItem::className(), ['inventory_reorder_master_id' => 'id']);
    }

    /**
     * Gets query for [[PrereqFormMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrereqFormMaster() {
        return $this->hasOne(PrereqFormMaster::className(), ['id' => 'prereq_form_master_id']);
    }

    /**
     * Gets query for [[RequestedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestedBy() {
        return $this->hasOne(User::className(), ['id' => 'requested_by']);
    }

    /**
     * Gets query for [[ApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy() {
        return $this->hasOne(User::className(), ['id' => 'approved_by']);
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0() {
        return $this->hasOne(RefInventoryStatus::className(), ['id' => 'status']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
    
    public function getDepartment()
    {
        return $this->hasOne(\frontend\models\common\RefUserDepartments::class, [
            'code' => 'department_code'
        ]);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }
}
