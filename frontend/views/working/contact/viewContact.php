<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\contact\ContactMaster */

$this->title = 'View Contact: ' . $model->company_name;
$this->params['breadcrumbs'][] = ['label' => 'Contact Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="contact-master-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a('Update <i class="far fa-edit"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?=
        Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            [
                'attribute' => 'contact_type',
                'value' => function($model) {
                    return $model['contactType']['contact_type_name'];
                }
            ],
            'company_name',
            'contact_person',
            'contact_position',
            'contact_number',
            'email:email',
            [
                'attribute' => 'address',
                'value' => function($model) {
                    return $model['address'];
                }
            ],
            'postcode',
            [
                'attribute' => 'area',
                'value' => function($model) {
                    return $model['area0']['area_name'];
                }
            ],
            [
                'attribute' => 'state',
                'value' => function($model) {
                    return $model['state0']['state_name'];
                }
            ],
            [
                'attribute' => 'country',
                'value' => function($model) {
                    return $model['country0']['country_name'];
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return common\models\myTools\MyFormatter::asDateTime_Read($model->created_at);
                }
            ],
            [
                'attribute' => 'created_by',
                'value' => function($model) {
                    return $model['createdBy']['fullname'];
                }
            ],
        ],
    ])
    ?>

</div>
