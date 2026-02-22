<?php

namespace frontend\models\projectproduction;

use Yii;

/**
 * This is the model class for table "ref_proj_prod_task_errors".
 *
 * @property int $id
 * @property string|null $proj_prod_task_code
 * @property string|null $description
 *
 * @property ProductionElecTasksError[] $productionElecTasksErrors
 * @property ProductionFabTasksError[] $productionFabTasksErrors
 */
class RefProjProdTaskErrors extends \yii\db\ActiveRecord {

    const STS_NAMES = ['assemble' => 'Assemble', 'bend' => 'Bending', 'busbar' => 'Busbar', 'cutnpunch' => 'Cut and Punching', 'mount' => 'Mounting', 'powcoat' => 'Powder Coating', 'test' => 'Testing', 'weldngrind' => 'Welding and Grinding', 'wire' => 'Wiring'];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_proj_prod_task_errors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_prod_task_code', 'description'], 'default', 'value' => null],
            [['proj_prod_task_code'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proj_prod_task_code' => 'Proj Prod Task Code',
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[ProductionElecTasksErrors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionElecTasksErrors() {
        return $this->hasMany(ProductionElecTasksError::class, ['error_code' => 'id']);
    }

    /**
     * Gets query for [[ProductionFabTasksErrors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductionFabTasksErrors() {
        return $this->hasMany(ProductionFabTasksError::class, ['error_code' => 'id']);
    }

    public static function getDropDownList($taskCode) {
        return \yii\helpers\ArrayHelper::map(self::find()->where(['proj_prod_task_code' => $taskCode])->orderBy(['id' => SORT_ASC])->all(), "id", "description");
    }

    public static function getDropDownListAll() {
        $groupedData = self::find()
                ->orderBy(['proj_prod_task_code' => SORT_ASC, 'id' => SORT_ASC])
                ->all();
        $groupedDropDownList = [];
        foreach ($groupedData as $data) {
            $groupCode = $data->proj_prod_task_code;
            $description = $data->description;
            if (isset(self::STS_NAMES[$groupCode])) {
                $groupName = self::STS_NAMES[$groupCode];
            } else {
                $groupName = $groupCode;
            }
            if (!isset($groupedDropDownList[$groupName])) {
                $groupedDropDownList[$groupName] = [];
            }
            $groupedDropDownList[$groupName][$data->id] = $description;
        }
        return $groupedDropDownList;
    }

}
