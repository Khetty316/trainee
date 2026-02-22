<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\profile\UserDocuments */

$this->title = 'Hr Documents'; // . " - " . $model->fullname;
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-documents-view">

    <h3> <?= Html::encode(Yii::$app->user->identity->fullname) ?> </h3>

    <?= $this->render('__ProfileNavBar', ['module' => 'account_claims', 'pageKey' => '3']); ?>
    <div class="col-lg-12">
        <?php
        $list = [];
        foreach ($model as $key => $documents) {
            $list[$documents['hr_doctype']][] = $documents;
        }

        foreach ($list as $key => $documents) {
            $docType = frontend\models\common\RefHrDoctypes::findOne($key);
            ?>
            <div class="list-group col-xs-12 col-md-8 newDocType" data-doctype_id='<?= $key ?>' id='doctype_group_<?= $key ?>'>
                <a class="collapsed list-group-item list-group-item-primary list-group-item-action btn-header-link" data-toggle="collapse" href="#collapseListGroup<?= $key ?>" aria-expanded="false" aria-controls="collapseListGroup<?= $key ?>">
                    <b><?= $docType->doc_type_name ?></b>
                    <span class="badge badge-pill badge-warning" id='totalUnread_<?= $key ?>'></span>
                </a>
                <div id="collapseListGroup<?= $key ?>" class="panel-collapse collapse border" role="tabpanel" aria-labelledby="collapseListGroupHeading<?= $key ?>">
                    <ul class="list-group p-2">


                        <?php
                        if ($docType->doc_type_name == "Payslip") {
                            echo "<span class='small text-danger'>Password to unlock: Your Staff id + Last 4 digits of your IC.<br/>For example: Staff ID: <b>N123</b>,"
                            . " IC No: XXXXXX-XX-<b>7890</b>, then password is: <b>N1237890</b></span>";
                        }
                        foreach ($documents as $document) {
                            echo Html::a($document->filename . ($document->is_read ? '' : '<span class="badge badge-pill text-danger isNew">New</span>'),
                                    ['/profile/get-doc', 'docId' => $document->id],
                                    ['class' => 'p-2 list-group-item list-group-item-action mainItem', 'data-doctype_id' => $key, 'data-doc_id' => $document->id, 'target' => "_blank"]);
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <?php
        }
        ?>
    </div> 
</div>
<script>
    $(function () {
        $(".mainItem").click(function () {
            var newItem = $(this).find($(".isNew"));
            if (newItem.length === 1) {
                if (setToRead($(this).data('doc_id'))) {
                    $(this).find($(".isNew")).remove();
                    calculateUnread($(this).data('doctype_id'));
                }
            }
        });

        // Loop through all doctype and calculate unread letters
        $(document).find('.newDocType').each(function (index, value) {
            var doctypeId = $(this).data('doctype_id');
            calculateUnread(doctypeId);
        });

    });



    function calculateUnread(doctypeId) {
        var total = $('#doctype_group_' + doctypeId).find($(".isNew")).length;
        if (total > 0) {
            $("#totalUnread_" + doctypeId).html(total);
        } else {
            $("#totalUnread_" + doctypeId).html('');
        }
    }


    function setToRead(id) {
        var url = "/profile/set-hr-doc-read";
        var result = false
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            async: false,
            data: {
                doc_id: id
            }
        }).done(function (response) {
            result = response.data.success;
        }).fail(function (xhr, textStatus, errorThrown) {
            alert("ERROR! Kindly contact IT Department.");
            return false;
        });

        return result;
    }
</script>
