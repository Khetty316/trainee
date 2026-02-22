<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "menu_sub".
 *
 * @property int $id
 * @property int $main_menu_id
 * @property string|null $sub_menu_name
 * @property string|null $sub_menu_auth
 * @property string|null $sub_menu_special_auth
 * @property string|null $sub_menu_link
 *
 * @property MenuMain $mainMenu
 */
class MenuSub extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'menu_sub';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['main_menu_id'], 'required'],
            [['main_menu_id'], 'integer'],
            [['sub_menu_name', 'sub_menu_auth', 'sub_menu_special_auth', 'sub_menu_link'], 'string', 'max' => 255],
            [['main_menu_id'], 'exist', 'skipOnError' => true, 'targetClass' => MenuMain::className(), 'targetAttribute' => ['main_menu_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'main_menu_id' => 'Main Menu ID',
            'sub_menu_name' => 'Sub Menu Name',
            'sub_menu_auth' => 'Sub Menu Auth',
            'sub_menu_special_auth' => 'Sub Menu Special Auth',
            'sub_menu_link' => 'Sub Menu Link',
        ];
    }

    /**
     * Gets query for [[MainMenu]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMainMenu() {
        return $this->hasOne(MenuMain::className(), ['id' => 'main_menu_id']);
    }

}
