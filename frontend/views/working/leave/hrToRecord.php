<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

//use yii\grid\GridView;
//use common\models\myTools\MyFormatter;
//$this->title = 'To Be Recorded';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$this->params['breadcrumbs'][] = $this->title;
?>

<div class="leave-master-index">
    <?= $this->render('__hrLeaveNavBar', ['module' => 'hr', 'pageKey' => '3']) ?>
    <p class="font-weight-lighter text-success">Showing leave details that have not been recorded.</p>

    <?php
    $form = ActiveForm::begin([
                'action' => \yii\helpers\Url::to(['hr-leave-to-record']),
                'method' => 'post',
                'id' => 'recordForm'
    ]);
    ?>
    <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>

    <button type="submit" class="btn btn-success" title="Record Row" data-confirm="Are you sure to record?">Record Selected Rows <i class="far fa-check-circle"></i></button>

    <div class="mt-3">
        <?php
        echo $this->render('__gridviewToRecord', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider]); // Allow HR to recall leave
        ?>
    </div>


    <?php ActiveForm::end(); ?>
</div>

<script>
    $('#recordForm').submit(onSubmit);

    function onSubmit()
    {
        var fields = $("input[name='selection[]']").serializeArray();
        if (fields.length === 0)
        {
            alert('No row selected!');
            return false;
        } else
        {
            $('#recordForm').submit();
        }
    }

</script>