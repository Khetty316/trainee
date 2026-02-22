<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "ref_test_form_list".
 *
 * @property string $code
 * @property string|null $formname
 * @property string|null $formclass
 * @property int|null $order
 * @property int|null $autocreate
 * @property int|null $active
 * @property string $created_at
 * @property int|null $creted_by
 *
 * @property TestDetailPunchlist[] $testDetailPunchlists
 */
class RefTestFormList extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_test_form_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['code'], 'required'],
            [['order', 'autocreate', 'active', 'creted_by'], 'integer'],
            [['created_at'], 'safe'],
            [['code'], 'string', 'max' => 10],
            [['formname', 'formclass'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'formname' => 'Formname',
            'formclass' => 'Formclass',
            'order' => 'Order',
            'autocreate' => 'Autocreate',
            'active' => 'Active',
            'created_at' => 'Created At',
            'creted_by' => 'Creted By',
        ];
    }

    /**
     * Gets query for [[TestDetailPunchlists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestDetailPunchlists() {
        return $this->hasMany(TestDetailPunchlist::className(), ['test_form_code' => 'code']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefTestFormList::find()->orderBy('order')->all(), "code", "formname");
    }

    public static function getDropDownClassToName() {
        return \yii\helpers\ArrayHelper::map(RefTestFormList::find()->orderBy('order')->all(), "formclass", "formname");
    }

    public static function getClassname() {
        return \yii\helpers\ArrayHelper::getColumn(RefTestFormList::find()->orderBy('order')->all(), 'formclass');
    }

    public static function getDropDownCodeNameClass() {
        $forms = RefTestFormList::find()->orderBy('order')->all();
        $result = [];
        foreach ($forms as $form) {
            $result[] = [
                'code' => $form->code,
                'formname' => $form->formname,
                'formclass' => $form->formclass,
            ];
        }
        return $result;
    }

}
