<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\MasterProjects */

$this->title = $model->project_code;
$this->params['breadcrumbs'][] = ['label' => 'Master Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="master-projects-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
<?= Html::a('Update <i class="far fa-edit"></i>', ['update', 'id' => $model->project_code], ['class' => 'btn btn-success']) ?>

    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'project_code',
            'project_name',
            'project_description',
            'project_image',
            [
                'attribute' => 'person_in_charge',
                'value' => function($model) {
                    return $model['personInCharge']['fullname'];
                }
            ],
            [
                'attribute' => 'created_by',
                'value' => function($model) {
                    return $model['createdBy']['fullname'];
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return MyFormatter::asDateTime_ReaddmYHi($model['created_at']);
                }
            ],
            [
                'attribute' => 'updated_by',
                'value' => function($model) {
                    return $model['updatedBy']['fullname'];
                }
            ],
            [
                'attribute' => 'updated_at',
                'value' => function($model) {
                    return MyFormatter::asDateTime_ReaddmYHi($model['updated_at']);
                }
            ],
        ],
    ])
    ?>

</div>
