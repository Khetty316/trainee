<?php

use yii\helpers\Html;
?>

<div class="card">
    <div class="card-header bg-light">
        <h6 class="mb-0">Production Overtime Meal Record Details</h6>
    </div>

    <div class="card-body p-2">

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Reference Code</th>
                        <th>Month</th>
                        <th>Year</th>
                        <th>Selected Period</th>
                        <th>Total Amount</th>
                        <th>Daily Record List</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td class="text-center"><?= $record->ref_code ?></td>
                        <td class="text-center"><?= DateTime::createFromFormat('!m', $record->month)->format('F') ?></td>
                        <td class="text-center"><?= $record->year ?></td>
                        <td class="text-center">
                            <?= Yii::$app->formatter->asDate($record->dateFrom, 'php:d/m/Y') ?>
                            to
                            <?= Yii::$app->formatter->asDate($record->dateTo, 'php:d/m/Y') ?>
                        </td>
                        <td class="text-right">
                            <span><?= \common\models\myTools\MyFormatter::asDecimal2($record->total_amount) ?></span>
                        </td>
                        <td class="text-center">
                            <?=
                            Html::a(
                                    "View",
                                    "javascript:",
                                    [
                                        "onclick" => "event.preventDefault();",
                                        "value" => \yii\helpers\Url::to([
                                            '../office/prod-ot-meal-record-master/ajax-view-daily-record',
                                            'id' => $record->id
                                        ]),
                                        "class" => "modalButtonProd btn btn-success",
                                    ]
                            )
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php
\yii\bootstrap4\Modal::begin([
    'title' => '<h5 class="modal-title font-weight-bold mb-0">
                    <i class="fas fa-clipboard-list mr-2"></i>Production Overtime Meal Record
                </h5>',
    'id' => 'modalMedium',
    'options' => ['tabindex' => false, 'class' => 'fade'],
    'dialogOptions' => ['class' => 'modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable'],
]);

echo "<h4 class='text-center font-weight-bold'>Daily Record List</h4>
      <div id='modalContentMedium' class='pt-0 pl-3 pr-3 pb-3'></div>";

\yii\bootstrap4\Modal::end();

$css = <<<CSS
/* Fullscreen-like modal for Bootstrap 4 */
#modalMedium .modal-dialog {
    max-width: 95% !important;
    width: 95%;
}

#modalMedium .modal-content {
    height: 95vh !important;
}

/* Backdrop fix */
.modal-backdrop.show {
    opacity: 0.6 !important;
}

/* Prevent background scrolling */
body.modal-open {
    overflow: hidden !important;
}

/* Responsive table fallback */
.table-responsive {
    overflow-x: auto;
    width: 100%;
}

.table {
    min-width: 900px; /* forces horizontal scroll if needed */
}
CSS;

$this->registerCss($css);

$js = <<<JS
$(document).on('click', '.modalButtonProd', function (e) {
    e.preventDefault();
    const url = $(this).attr('value');

    const modal = $('#modalMedium');
    const modalBody = modal.find('#modalContentMedium');

    modalBody.html(
        '<div class="text-center py-5 text-muted">' +
        '<i class="fas fa-spinner fa-spin fa-2x"></i>' +
        '<p class="mt-2">Loading...</p></div>'
    );

    modal.modal('show')
        .find('#modalContentMedium')
        .load(url, function (response, status, xhr) {
            if (status === 'error') {
                modalBody.html(
                    '<div class="alert alert-danger">Failed to load content. Please try again later.</div>'
                );
                console.error('Failed:', xhr.statusText);
            }
        });
});
JS;

$this->registerJs($js);
?>
