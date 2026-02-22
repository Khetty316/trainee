<?php

namespace frontend\models\ProjectProduction;

use Yii;
use yii\base\Model;

class ProjectProductionPanelDesignForm extends Model {

    public $scannedFile;
    public $selectedPanelIds;
    public $remarks;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['remarks'], 'string'],
            ['scannedFile', 'file', 'maxFiles' => 0, 'skipOnEmpty' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'remarks' => 'Remarks',
            'scannedFile' => 'Attachments',
        ];
    }

}
