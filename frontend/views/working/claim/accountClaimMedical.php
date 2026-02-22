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
    echo $this->render('__ClaimNavBar', ['module' => 'account_claims', 'pageKey' => '3']);
    $this->params['breadcrumbs'][] = ['label' => 'Account - Medical Claims'];
    $this->params['breadcrumbs'][] = $this->title;
    ?>

    <?php
    $i = 0;

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
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
                    $url = '/working/claim/viewonly?claimsMasterId=' . $data->claims_master_id;
                    return Html::a($data->claims_id, "#", ["value" => \yii\helpers\Url::to($url), "class" => "modalButton"]);
                }
            ],
            [
                'attribute' => 'detail',
                'value' => function($data) {
                    return '(Medical) - '.$data->detail;
                }
            ],
//            [
//                'attribute' => 'created_at',
//                'label' => 'Submission Date',
//                'format' => 'raw',
//                'value' => function($data) {
//                    return MyFormatter::asDate_Read($data->created_at);
//                }
//            ],
            [
                'attribute' => 'claims_status_name',
                'label' => 'Status',
            ],
            [
                'attribute' => 'amount',
                'label' => 'Amount',
                'format' => 'raw',
                'value' => function($data) {
                    return '<p class="p-0 m-0 text-right">' . MyFormatter::asCurrency($data->amount) . '</p>';
                }
            ],
            [
                'attribute' => 'invoice_date',
                'label' => 'Invoice Date',
                'format' => 'raw',
                'value' => function($data) {
                    return '<p class="p-0 m-0 text-right">' . MyFormatter::asDate_Read($data->invoice_date) . '</p>';
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