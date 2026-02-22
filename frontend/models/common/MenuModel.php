<?php

namespace frontend\models\common;

use Yii;
use yii\base\Model;

class MenuModel extends Model {

    public $icon;
    public $title;
    public $link;
    public $children = [];

    static public function newMenuItems($icon, $title, $link) {
        $model = new MenuModel();
        $model->icon = $icon;
        $model->title = $title;
        $model->link = $link;
        return $model;
    }

}
