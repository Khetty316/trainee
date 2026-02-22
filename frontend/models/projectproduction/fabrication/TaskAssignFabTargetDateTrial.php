<?php

namespace frontend\models\projectproduction\fabrication;

use Yii;
use common\models\User;
use common\models\myTools\MyFormatter;
/**
 * This is the model class for table "task_assign_fab_target_date_trial".
 *
 * @property int $id
 * @property int|null $task_assign_fab_id
 * @property string|null $target_date
 * @property string|null $remark
 * @property int|null $created_by
 * @property string|null $created_at
 *
 * @property User $createdBy
 */
class TaskAssignFabTargetDateTrial extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'task_assign_fab_target_date_trial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['task_assign_fab_id', 'created_by'], 'integer'],
            [['target_date', 'created_at'], 'safe'],
            [['remark'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'task_assign_fab_id' => 'Task Assign Fab ID',
            'target_date' => 'Target Date',
            'remark' => 'Remark',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
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

    public function beforeSave($insert) {
        $this->created_at = new \yii\db\Expression('NOW()');
        $this->created_by = Yii::$app->user->identity->id;
        if (!empty($this->target_date)) {
            $this->target_date = MyFormatter::fromDateRead_toDateSQL($this->target_date);
        }
        return parent::beforeSave($insert);
    }
}
