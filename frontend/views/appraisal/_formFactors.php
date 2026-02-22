<?php

use yii\bootstrap4\Html;
use frontend\models\common\RefAppraisalFactor;

$dmodel = json_decode($model, true);
?>
<style>
    .custom-radio {
        width: 50px; /* Adjust the width as needed */
        height: 50px; /* Adjust the height as needed */
    }
</style>

<div id="app" class="col-9 mx-auto">
    <div class="mb-2">
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2 m-0">Information</legend>
            <p>1 = Unsatisfactory / Tidak Memuaskan</p>
            <p>2 = Need Improvement / Perlu Peningkatan</p>
            <p>3 = Average / Sederhana</p>
            <p>4 = Satisfactory / Memuaskan</p>
            <p>5 = Excellent / Cemerlang</p>
        </fieldset>

        <form @submit="validateAndSubmit">
            <fieldset v-for="(form, index) in forms" :key="form.form_id" class="form-group border p-3">
                <legend class="w-auto px-2 m-0">{{ form.form_name }}</legend>
                <div class="row m-2">
                    <table class="table table-bordered table-striped table-hover m-0 p-3 col-12 rounded">
                        <tr>
                            <th class="col-2 p-1" style="vertical-align: middle">Factor</th>
                            <th class="col-6 p-1" style="vertical-align: middle">Description</th>
                            <?php if (!$staff) { ?>
                                <th class="p-1 text-center" style="vertical-align: middle">Rating</th>
                            <?php } ?>
                            <th class="text-center">1</th>
                            <th class="text-center">2</th>
                            <th class="text-center">3</th>
                            <th class="text-center">4</th>
                            <th class="text-center">5</th>
                        </tr>
                        <tr class="col-md-6" v-for="factor in getFactorsForForm(form.id)">
                            <td class="p-1">
                                <p>
                                    {{ factor.factor_name }}
                                </p>
                                <i>{{ factor.factor_name_my }}</i>
                            </td>
                            <td class="p-1">
                                <p>
                                    {{ factor.factor_desc }}
                                </p>
                                <i>{{ factor.factor_desc_my }}</i>
                            </td>
                            <?php if (!$staff) { ?>
                                <td class="p-1 text-center bold">{{ factor.rating }}</td>
                            <?php } ?>
                            <td v-for="value in [1, 2, 3, 4, 5]" class="p-1 text-center" style="vertical-align: middle">
                                <input
                                    type="radio"
                                    :name="'<?= $type ?>' + factor.id"
                                    :value="value"
                                    v-model="factor.<?= $type ?>"
                                    required
                                    class="custom-radio"
                                    @change="saveSelectedData(factor.id, value)"
                                    :disabled="disableInput(factor.factor_id)"
                                    />
                            </td>
                        </tr>
                    </table>
                </div>
            </fieldset>
            <div>
                <?php
                if ($staff) {
                    echo Html::label("Write a short statement below supporting your $type", 'staffRemark');
                    echo Html::textarea('staffRemark', null, ['id' => 'staffRemark', 'class' => 'form-control', 'v-model' => 'staffRemark', 'required' => false]);
                }
                ?>
            </div>
            <div class="form-group d-flex justify-content-end">
                <button type="button" class="btn btn-warning my-2 mx-2" @click="saveDraft" title="Save draft and continue later">Save Draft</button>
                <button type="submit" class="btn btn-success my-2" title="Submit all mark">Submit</button>
            </div>
        </form>
    </div>
</div>

<script>
    const app = Vue.createApp({
        data() {
            return {
                model: <?= $model ?>,
                forms: <?= $forms ?>,
                factors: <?= $factors ?>,
                type: '<?= $type ?>',
                staffRemark: `<?= str_replace(["\r", "\n"], ['\\r', '\\n'], json_decode($model, true)['staff_remark']) ?>`,
                csrfToken: '<?= Yii::$app->request->getCsrfToken() ?>',
                selectedData: [],
                draft: false
            };
        },
        methods: {
            getFactorsForForm(formId) {
                return this.factors.find((factors) => factors[0].appraisal_master_form_id === formId) || [];
            },
            disableInput(factorId) {
                return factorId == <?= RefAppraisalFactor::FACTOR_ATTENDANCE ?> || factorId == <?= RefAppraisalFactor::FACTOR_PUNCTUALITY ?> || factorId == <?= RefAppraisalFactor::FACTOR_OVERTIME ?>;
            },
            saveSelectedData(factorId, value) {
                const index = this.selectedData.findIndex(data => data.factorId === factorId);

                if (index !== -1) {
                    this.selectedData[index][this.type] = value;
                } else {
                    const newData = {factorId};
                    newData[this.type] = value;
                    this.selectedData.push(newData);
                }
            },
            validateAndSubmit(event) {
                event.preventDefault();
                const hasMissingSelection = this.forms.some(form => {
                    const factors = this.getFactorsForForm(form.id);
                    return factors.some(factor => {
                        if (factor.factor_id == <?= RefAppraisalFactor::FACTOR_ATTENDANCE ?> || factor.factor_id == <?= RefAppraisalFactor::FACTOR_PUNCTUALITY ?> || factor.factor_id == <?= RefAppraisalFactor::FACTOR_OVERTIME ?>) {
                            return false;
                        }
                        return factor[this.type] === null;
                    });
                });
                if (hasMissingSelection) {
                    alert('Please select a value for each row before submitting.');
                    console.log('Please select a value for each row before submitting.');
                } else {
                    this.sendData();
                }
            },
            saveDraft() {
                this.draft = true;
                this.sendData();
            },
            sendData() {
                const data = new FormData();
                data.append('master', this.model.id);
                data.append('factors', JSON.stringify(this.selectedData));
                data.append('staffRemark', this.staffRemark);
                data.append('type', this.type);
                data.append('saveDraft', this.draft);
                if (this.type == "<?= frontend\models\appraisal\AppraisalMaster::TYPE_RATING ?>") {
                    var url = `/appraisalgnrl/confirm-appraisal?id=<?= json_decode($model, true)['id'] ?>`;
                } else {
                    var url = `/appraisalgnrl/index-${this.type}?id=<?= $main->id ?>`;
                }
                fetch('/appraisalgnrl/process-factor-mark', {
                    method: 'POST',
                    body: data,
                    headers: {
                        'X-CSRF-Token': this.csrfToken
                    }
                })
                        .then((response) => {
                            if (response.ok) {
                                const redirectUrl = url;
                                window.location.href = redirectUrl;
                            } else {
                                const redirectUrl = url;
                                window.location.href = redirectUrl;
                            }
                        })
                        .catch((err) => {
                            console.log(err);
                        });
            }
        }
    });

    app.mount('#app');
</script>