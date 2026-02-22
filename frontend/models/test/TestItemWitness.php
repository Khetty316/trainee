<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "test_item_witness".
 *
 * @property int $id
 * @property string|null $form_type
 * @property int|null $test_master_id
 * @property string|null $name
 * @property string|null $org
 * @property string|null $designation
 * @property string|null $role
 * @property resource|null $signature
 * @property string $created_at
 * @property int|null $created_by
 * @property string $updated_at
 * @property int|null $updated_by
 */
class TestItemWitness extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_item_witness';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['test_master_id', 'created_by', 'updated_by'], 'integer'],
            [['signature'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['form_type', 'name', 'org', 'designation', 'role'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'form_type' => 'Form Type',
            'test_master_id' => 'Test Master ID',
            'name' => 'Name',
            'org' => 'Org',
            'designation' => 'Designation',
            'role' => 'Role',
            'signature' => 'Signature',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public static function getTestItemWitness($id, $formType) {
        return self::find()->where(['test_master_id' => $id, 'form_type' => $formType])->all();
    }

}
