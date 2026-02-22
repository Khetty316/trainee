<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\documentreminder\DocumentReminderMaster */

$this->title = $model->description;
$this->params['breadcrumbs'][] = ['label' => 'HR Public Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$icon = "";
?>
<div class="document-reminder-master-view">

    <h3><?= $icon . Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a('Update <i class="fas fa-pencil-alt"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php
        if (common\models\myTools\MyCommonFunction::checkRoles([\common\modules\auth\models\AuthItem::ROLE_HR_Senior])) {
            echo Html::a('Delete <i class="far fa-trash-alt"></i>', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]);
        }
        ?>
    </p>

    <?=
    DetailView::widget(array_merge(Yii::$app->params['detailViewOption28'], [
        'model' => $model,
        'attributes' => [
//            'id',
            'category',
//            'active_sts',
            'description',
            [
                'attribute' => 'filename',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(substr($model->filename, 15), ["/working/hr-public-document/get-file", 'id' => $model->id],
                            ['target' => "_blank"]);
                }
            ],
            [
                'attribute' => 'file_date',
                'format' => 'html',
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->file_date);
                },
            ],
            'remark:ntext',
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => function ($model) {
                    return User::findOne($model->created_by)->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                }
            ],
            [
                'attribute' => 'updated_by',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->updated_by) {
                        return User::findOne($model->updated_by)->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model->updated_at);
                    } else {
                        return null;
                    }
                }
            ],
            [
                'attribute' => 'show_alert',
                'value' => function ($model) {
                    return $model->show_alert == 0 ? 'No' : 'Yes';
                },
            ]
        ],
    ]))
    ?>

</div>
