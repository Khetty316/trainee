<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "menu_main".
 *
 * @property int $id
 * @property string|null $main_menu_name
 * @property string|null $main_menu_auth
 * @property string|null $main_menu_special_auth
 *
 * @property MenuSub[] $menuSubs
 */
class MenuMain extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'menu_main';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['main_menu_name', 'main_menu_auth', 'main_menu_special_auth'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'main_menu_name' => 'Main Menu Name',
            'main_menu_auth' => 'Main Menu Auth',
            'main_menu_special_auth' => 'Main Menu Special Auth',
        ];
    }

    /**
     * Gets query for [[MenuSubs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMenuSubs() {
        return $this->hasMany(MenuSub::className(), ['main_menu_id' => 'id']);
    }

}
