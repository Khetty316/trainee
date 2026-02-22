<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\resume\ResumeAcademicQualificationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Resume Academic Qualifications';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resume-academic-qualifications-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Resume Academic Qualifications', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'academic_level',
            'academic_institution',
            'academic_course',
            //'academic_period',
            //'academic_honour',
            //'active_sts',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
