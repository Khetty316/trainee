<?php

namespace frontend\models\common;

use Yii;
use common\models\User;

/**
 * This is the model class for table "audit_trail".
 *
 * @property int $id
 * @property string|null $table
 * @property string|null $idx_no
 * @property string|null $module
 * @property string|null $detail
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property User $createdBy
 */
class AuditTrail extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'audit_trail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['detail'], 'string'],
            [['created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['table', 'idx_no'], 'string', 'max' => 100],
            [['module'], 'string', 'max' => 50],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'table' => 'Table',
            'idx_no' => 'Idx No',
            'module' => 'Module',
            'detail' => 'Detail',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
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

    public static function createNew($table, $idxNo, $module, $detail) {
        $audit = new AuditTrail();
        $audit->created_by = Yii::$app->user->identity->id;
        $audit->table = $table;
        $audit->idx_no = $idxNo;
        $audit->module = $module;
        $audit->detail = $detail;
        $audit->save();
    }


}
