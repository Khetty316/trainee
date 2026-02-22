<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\documentreminder\DocumentReminderMaster */

$this->title = $model->description;
$this->params['breadcrumbs'][] = ['label' => 'Document Reminder', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$warningLevel = $model->getDocumentWarningLevel();
$icon = "";

if ($warningLevel == 1) {
    $icon = ' <i class="fas fa-exclamation-triangle text-warning" title="Within Warning Date!"></i> ';
} else if ($warningLevel == 2) {
    $icon = ' <i class="fas fa-exclamation-triangle text-danger" title="EXPIRED!"></i> ';
}
?>
<div class="document-reminder-master-view">

    <h3><?= $icon . Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a('Update <i class="fas fa-pencil-alt"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php
        if (Yii::$app->user->can("ADMIN2")) {
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
    DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            'category',
//            'active_sts',
            'description',
            [
                'attribute' => 'filename',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a(substr($model->filename, 15), ["/working/documentreminder/get-file", 'id' => $model->id],
                                    ['target' => "_blank"]);
                }
            ],
            [
                'attribute' => 'remind_date',
                'format' => 'raw',
                'value' => function($model) {
                    $warningLevel = $model->getDocumentPassReminderDate();
                    $icon = "";

                    if ($warningLevel == 1) {
                        $icon = ' <i class="fas fa-exclamation-triangle text-warning" title="Within Warning Date!"></i> ';
                    }
                    return $icon . MyFormatter::asDate_Read($model->remind_date) . (($model->remind_period && $model->remind_period_unit) ? ' (' . $model->remind_period . ' ' . $model->remind_period_unit . ') ' : "");
                }
            ],
            [
                'attribute' => 'expiry_date',
                'format' => 'raw',
                'value' => function($model) {
                    $warningLevel = $model->getDocumentPassExpiryDate();
                    $icon = "";
                    if ($warningLevel == 2) {
                        $icon = ' <i class="fas fa-exclamation-triangle text-danger" title="EXPIRED!"></i> ';
                    }
                    return $icon . MyFormatter::asDate_Read($model->expiry_date);
                }
            ],
            'remark:ntext',
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => function($model) {
                    return User::findOne($model->created_by)->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                }
            ],
            [
                'attribute' => 'updated_by',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->updated_by) {
                        return User::findOne($model->updated_by)->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model->updated_at);
                    } else {
                        return null;
                    }
                }
            ],
        ],
    ])
    ?>

</div>
