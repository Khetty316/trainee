<?php

namespace frontend\models\working\mi;

use common\models\User;
use Yii;

/**
 * This is the model class for table "mi_worklist".
 *
 * @property int $mi_worklist_id
 * @property int $mi_id
 * @property int|null $step
 * @property int|null $task_id
 * @property int|null $responsed_by
 * @property int $approved_flag
 * @property string|null $remarks
 * @property string $created_at
 *
 * @property MasterIncomings $mi
 * @property User $responsedBy
 * @property RefMiTasks $task
 */
class MiWorklist extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'mi_worklist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['mi_id'], 'required'],
            [['mi_id', 'step', 'task_id', 'responsed_by', 'approved_flag'], 'integer'],
            [['remarks'], 'string'],
            [['created_at'], 'safe'],
            [['mi_id'], 'exist', 'skipOnError' => true, 'targetClass' => MasterIncomings::className(), 'targetAttribute' => ['mi_id' => 'id']],
            [['responsed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['responsed_by' => 'id']],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefMiTasks::className(), 'targetAttribute' => ['task_id' => 'task_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'mi_worklist_id' => 'Mi Worklist ID',
            'mi_id' => 'Mi ID',
            'step' => 'Step',
            'task_id' => 'Task ID',
            'responsed_by' => 'Responsed By',
            'approved_flag' => 'Approved Flag',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Mi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMi() {
        return $this->hasOne(MasterIncomings::className(), ['id' => 'mi_id']);
    }

    /**
     * Gets query for [[ResponsedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsedBy() {
        return $this->hasOne(User::className(), ['id' => 'responsed_by']);
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask() {
        return $this->hasOne(RefMiTasks::className(), ['task_id' => 'task_id']);
    }

    //    mi_id,
//step,
//task_id,
//responsed_by,
//approved_flag,
//remarks

    public function createNewWorklist($MI, $approval, $remarks) {
//        $MI = new MasterIncomings();
        $this->mi_id = $MI->id;
        $this->step = $MI->current_step;
        $this->task_id = $MI->current_step_task_id;
        $this->responsed_by = \yii::$app->user->id;
        $this->approved_flag = $approval;
        $this->remarks = $remarks;

        return ($this->save());
    }
    
    
    public static function getWorklistWithRemark($miId){
        $workList = MiWorklist::find()->where(['mi_id'=>$miId])->andWhere('remarks IS NOT NULL AND remarks !="" ')->all();
        return $workList;
    }

}
