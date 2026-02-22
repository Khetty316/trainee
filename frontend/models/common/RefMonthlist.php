<?php

namespace frontend\models\common;

use Yii;
use yii\base\Model;

class RefMonthlist extends Model {


    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefState::find()->orderBy(['state_name' => SORT_ASC])->all(), "state_id", "state_name");
    }

}
