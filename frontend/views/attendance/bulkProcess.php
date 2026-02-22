<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\attendance\MonthlyAttendance $model */
?>
<div id="app1" class="monthly-attendance-create">

    <?php
    $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
    echo $form->field($model, 'scannedFile')->fileInput(['accept' => '.xls'])->label("Excel file:");

    echo $form->field($model, 'year')->textInput(['v-model' => 'selectedYear', 'id' => 'numeric-input', 'maxlength' => 4,
        'options' => ['autocomplete' => false]]);

    echo $form->field($model, 'month')->dropDownList(
            \common\models\myTools\MyCommonFunction::getMonthListArray(),
            ['v-model' => 'selectedMonth']
    );
    ?>
    <div v-if="response !== null" :class="{ 'text-success': response, 'text-danger': !response }" class="mb-3">
        {{ response ? 'There is currently no data for selected month and year. Upload to save.' : '**Warning: There is already data in selected year and month, continue to overwrite.' }}
    </div>
    <?php
    echo Html::submitButton('Upload', ['class' => 'btn btn-primary']);

    $this->registerJs('
    $(document).ready(function(){
        $("#numeric-input").on("input", function() {
            $(this).val($(this).val().replace(/[^0-9]/g, ""));
        });
    });
    ');

    ActiveForm::end();
    ?>

</div>

<script>
    var app1 = Vue.createApp({
        data() {
            return {
                selectedYear: null,
                selectedMonth: null,
                response: null,
            };
        },
        watch: {
            selectedYear: 'checkBothFilled',
            selectedMonth: 'checkBothFilled',
        },
        methods: {
            checkBothFilled() {
                if (this.selectedYear !== null && this.selectedMonth !== null) {
                    this.checkData();
                } else {
                    this.response = null;
                }
            },
            checkData() {

                const data = new FormData();
                data.append('year', this.selectedYear);
                data.append('month', this.selectedMonth);
                fetch('/attendance/check-attendance-exist', {
                    method: 'POST',
                    body: data,
                    headers: {
                        'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
                    }
                })
                        .then(response => response.json())
                        .then(data => {
                            this.response = data.success;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
            },
        },
    });

    app1.mount('#app1');
</script>