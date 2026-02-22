<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class ModelHelper
{
    public static function createMultiple($modelClass, $multipleModels = [])
    {
        $model = new $modelClass;
        $formName = $model->formName();
        $post = Yii::$app->request->post($formName, []);
        $models = [];

        // Index existing models by ID
        if (!empty($multipleModels)) {
            $multipleModels = \yii\helpers\ArrayHelper::index($multipleModels, 'id');
        }

        if (is_array($post)) {
            foreach ($post as $key => $item) {
                if (empty(array_filter($item))) continue;

                if (!empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $modelInstance = $multipleModels[$item['id']];
                } else {
                    $modelInstance = new $modelClass;
                }

                // Assign POST attributes directly
                $modelInstance->attributes = $item;
                $models[$key] = $modelInstance;
            }
        }

        return $models;
    }
}
?>