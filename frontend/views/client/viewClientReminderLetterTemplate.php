<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\client\ClientReminderLetterTemplate */

$this->title = $model->letter_name;
$this->params['breadcrumbs'][] = ['label' => 'Debt Reminder Letter Templates', 'url' => ['index-debt-reminder-letter-template']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-reminder-letter-template-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update-client-reminder-letter-template', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php
//        = Html::a('Delete', ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]) 
        ?>
    </p>

    <?=
    DetailView::widget([
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'template' => "<tr><th style='width: 10%;'>{label}</th><td>{value}</td></tr>",
        'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
        'model' => $model,
        'attributes' => [
//            'id',
            'letter_name',
            [
                'attribute' => 'content',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<div class="content-preview">' . $model->content . '</div>';
                },
            ],
            [
                'attribute' => 'active_sts',
                'value' => function ($model) {
                    return $model->active_sts == 0 ? 'Yes' : 'No';
                },
            ],
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => function ($model) {
                    $user = User::findOne($model->created_by);
                    if ($user) {
                        return $user->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                    }
                }
            ],
//            'created_at',
            [
                'attribute' => 'updated_by',
                'format' => 'raw',
                'value' => function ($model) {
                    $user = User::findOne($model->updated_by);
                    if ($user) {
                        return $user->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at);
                    }
                }
            ],
//            'updated_at',
        ],
    ])
    ?>

    <?php $this->registerCss(".content-preview ul {list-style-type: none !important;padding-left: 0 !important;}.content-preview li::marker {content: '' !important;}"); ?>
    <?php $this->registerCss(".content-preview,.content-preview * {font-size: 12px !important;line-height: 1.2 !important;margin-bottom: 2px !important;}.content-preview p {margin: 0 0 6px 0 !important;}"); ?>

</div>
