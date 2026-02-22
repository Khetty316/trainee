<?php

namespace frontend\models\common;

use Yii;

/**
 * This is the model class for table "v_page_access_byhour".
 *
 * @property float|null $times
 * @property string|null $theDate
 * @property string|null $theTime
 * @property string|null $fullname
 */
class VPageAccessByhour extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_page_access_byhour';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['times'], 'number'],
            [['theDate'], 'safe'],
            [['theTime'], 'string', 'max' => 13],
            [['fullname'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'times' => 'Times',
            'theDate' => 'The Date',
            'theTime' => 'The Time',
            'fullname' => 'Fullname',
        ];
    }

}
