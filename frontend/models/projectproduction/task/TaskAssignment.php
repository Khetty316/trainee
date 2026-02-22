<?php

namespace frontend\models\projectproduction\task;

use yii\base\Model;

class TaskAssignment extends Model {

    CONST taskTypeFabrication = "fab";
    CONST taskTypeElectrical = "elec";

    public $projectId, $panelId, $startDate, $endDate, $comments, $staffIdString;
    public $taskCode = [], $staffIds = []; // array    
    public $taskAssignmentList = [];
    public $panelIds;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['projectId', 'panelId'], 'number'],
            [['startDate', 'endDate', 'comments', 'taskCode', 'staffIdString', 'panelIds'], 'string'],
            ['taskCode', 'each', 'rule' => ['string']],
            ['staffIds', 'each', 'rule' => ['int']],
//            ['start_date', 'date', 'timestampAttribute' => 'start_date'],
//            ['end_date', 'date', 'timestampAttribute' => 'end_date'],
//            ['start_date', 'compare', 'compareAttribute' => 'end_date', 'operator' => '<','enableClientValidation' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'projectId' => 'Project ID',
            'panelId' => 'Panel ID'
        ];
    }

}
