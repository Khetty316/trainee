<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use \yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\Modal;

/* @var $this yii\web\View */
/* @var $searchModel app\models\working\MasterIncomingsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Send To Procurement';
$this->params['breadcrumbs'][] = ['label' => 'Admin - Document Incoming'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-incomings-index">
    <?= $this->render('__MINavBar', ['title' => $this->title, 'module' => 'mi_admin']) ?>
    <p>
        <?php // = Html::a('Send Doc <i class="far fa-paper-plane"></i>', '?', ['class' => 'btn btn-success']) ?>
        <?=
        Html::a(
                'Send Doc <i class="far fa-paper-plane"></i>',
                "#",
                [
                    'class' => 'btn btn-success',
                    'onclick' => 'checkCheckBoxes()'
                ]
        )
        ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);      ?>


    <div class="tab-content p-1">


        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class],
            'headerRowOptions' => ['class' => 'my-thead'],
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model, $key, $index, $column) {
                        return ['value' => $model->id, 'step' => $model->current_step];
                    }
                ],
                [
                    'attribute' => 'index_no',
                    'label' => "Index Number",
                    'format' => 'raw',
                    'value' => function($model) {
                        $title = "Uploaded By: " . $model->uploader_fullname . "\n" . "At time: " . MyFormatter::asDateTime_Read($model->created_at);
                        $title .= "\nRemarks: " . $model->remarks;
                        return Html::a($model->index_no . ' <i class="fas fa-info-circle" aria-hidden="true"></i>',
                                        "#",
                                        [
                                            "title" => $title,
                                            "value" => \yii\helpers\Url::to('viewonly?id=' . $model->id),
                                            "class" => "modalButton"])
                                . Html::a("<i class='far fa-file-alt fa-lg' ></i>", "#",
                                        [
                                            'title' => "Click to view me",
                                            "value" => ("/working/mi/get-file?filename=" . urlencode($model->filename)),
                                            "class" => "modalButtonPdf m-2"]);
                    }
                ],
                [
                    'attribute' => 'doc_type_name',
                    'label' => 'Document Type',
                    'filter' => $docTypeList,
                ],
                [
                    'attribute' => 'doc_due_date',
                    'value' => function($model) {
                        return $model->doc_due_date == "" ? "" : Yii::$app->formatter->asDatetime($model->doc_due_date, 'php:d/m/Y');
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'doc_due_date',
                        'language' => 'en',
                        'dateFormat' => 'dd-MM-yyyy',
//                    'dateFormat'=>'php:d/m/Y',
                        'options' => ['class' => 'form-control'],
                    ]),
                    'format' => 'html',
                ],
                'reference_no',
                'particular',
                ['attribute' => "grn_no"],
                ['attribute' => "po_number"],
                [
                    'attribute' => 'isPerforma',
                    'value' => function ($model) {
                        return $model->isPerforma ? 'Yes' : '';
                    },
                    'label' => 'Pro Forma?'
                ],
                [
                    'attribute' => 'file_type_name',
                    'label' => 'File Type',
                    'filter' => $fileTypeList,
                ],
                'received_from',
                [
                    'attribute' => "amount",
                    'format' => 'raw',
                    'value' => function($data) {
                        $projects = \frontend\models\working\mi\MiProjects::find()->where('mi_id=' . $data->id)->all();
                        $str = '';
                        foreach ($projects as $key => $project) {
//                            if($project->amount>0)
                            $amt = $project->amount > 0 ? $project->currency->currency_sign . " " . MyFormatter::asDecimal2($project->amount) : ' - ';
                            $str .= ($str == '' ? '' : ',<br/>') . $amt;
                        }
                        return '<p class="text-right m-0">' . $str . '</p>';
                    }
                ],
                [
                    'attribute' => 'project_code',
                    'format' => 'raw',
                    'value' => function($data) {
                        $projects = \frontend\models\working\mi\MiProjects::find()->where('mi_id=' . $data->id)->all();
                        $str = '';
                        foreach ($projects as $key => $project) {
                            $str .= ($str == '' ? '' : ',<br/>') . $project->project_code;
                        }
                        return $str;
                    }
                ],
                [
                    'attribute' => 'requestor_fullname',
                    'format' => 'raw',
                    'label' => 'Requestor',
                    'value' => function($data) {
                        $projects = \frontend\models\working\mi\MiProjects::find()->where('mi_id=' . $data->id)->all();
                        $str = '';
                        foreach ($projects as $key => $project) {
                            $str .= ($str == '' ? '' : ',<br/>') . $project->requestor0->fullname;
                        }
                        return $str;
                    }
//                'attribute' => 'requestor_fullname',
                ],
            ],
        ]);
        ?>
    </div>
</div>
<?php echo $this->render('_checkboxModal', ['action' => '/working/mi/adminsendingdocproc', 'title' => 'Send to Procurement?']); ?>
<script>



</script>