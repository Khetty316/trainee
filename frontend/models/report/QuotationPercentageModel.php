<?php

namespace frontend\models\report;

use Yii;
use yii\base\Model;
use common\models\myTools\MyFormatter;
class QuotationPercentageModel extends Model {

    public $dateFrom;
    public $dateTo;

    public function rules() {
        return [
            [['dateFrom', 'dateTo'], 'string'],
            [['dateFrom', 'dateTo'], 'required'],
            [['dateFrom'], 'validateDates'],
        ];
    }

    public function validateDates() {
        $dateForm = MyFormatter::changeDateFormat_readToDB($this->dateFrom);
        $dateTo = MyFormatter::changeDateFormat_readToDB($this->dateTo);
        if (strtotime($dateTo) < strtotime($dateForm)) {
            $this->addError('dateTo', 'End date must be later than Date From');
        }
    }

    public function attributeLabels() {
        return [
            'dateFrom' => 'Date From',
            'dateTo' => 'Date To',
        ];
    }

}
