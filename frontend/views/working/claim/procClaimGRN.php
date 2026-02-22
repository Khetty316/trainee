<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\claim\ClaimsDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="claims-detail-index">


    <?php
//    $this->title = "HR Claim List";
//    $this->params['breadcrumbs'][] = $this->title;
    ?>

    <?php
    $this->title = "Pre-GRN";
    $this->params['breadcrumbs'][] = ['label' => 'Procurement - Claim'];
    $this->params['breadcrumbs'][] = $this->title;
    ?>
    <h3><?= Html::encode($this->title) ?></h3>

    <?php
    $i = 0;

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'options' => ['class' => 'table-sm'],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
        'columns' => [
            [
                'attribute' => 'claims_id',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::a($data->claims_id, '/working/claim/proc-claim-assign-grn?claimsMasterId=' . $data->claims_master_id);
                }
            ],
            [
                'attribute' => 'claimant_id',
                'label' => 'Claimant',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->claimant->fullname;
                }
            ],
            [
                'attribute' => 'claim_type',
                'label' => 'Claimant',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->claimType->claim_name;
                }
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Submission Date',
                'format' => 'raw',
                'value' => function($data) {
                    return MyFormatter::asDate_Read($data->created_at);
                }
            ],
            [
                'attribute' => 'claims_status',
                'label' => 'Status',
                'format' => 'raw',
                'value' => function($data) {
                    return $data->claimsStatus->status_name;
                }
            ],
            [
                'label' => 'Pending GRN',
                'format' => 'raw',
                'value' => function($data) use ($recordCount) {
                    return array_key_exists($data->claims_master_id, $recordCount) ? ('<b class="text-danger">' . $recordCount[$data->claims_master_id] . '</b>') : 0;
                }
            ],
        ],
    ]);
    ?>

</div>


<div class="hidden">
    <?php
    $form = \yii\bootstrap4\ActiveForm::begin([
                'id' => 'myForm',
                'action' => '/working/claim/submit-claim',
                'method' => 'post'
    ]);
    echo '<input type="text" name="claimIds" id="claimIds"/> ';
    echo '<input type="text" name="claimFamily" id="claimFamily"/>';
    \yii\bootstrap4\ActiveForm::end();
    ?>
</div>
<script>

    function submitClaim(claimName, recordIds, claimFamily) {

        var Ids = $("#" + recordIds).val();

        var answer = confirm("Submit your " + claimName + "?");
        if (answer) {
            $("#claimIds").val(Ids);
            $("#claimFamily").val(claimFamily);
            $("#myForm").submit();
        }
    }

</script>