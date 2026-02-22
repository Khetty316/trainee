<?php
$toConfirm = $toConfirm ?? false;
?>
<div id="app" class="col-12">
    <fieldset class="form-group border p-3" v-for="(form, index) in forms" :key="form.form_id">
        <legend class="w-auto px-2 m-0"> {{ form.form_name }} </legend>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-bordered table-striped table-hover m-0 mt-2 col-12 rounded">
                    <thead>
                        <tr>
                            <th class="col-2 p-1">Factor</th>
                            <th class="col-6 p-1">Description</th>
                            <th class="text-center">Rating</th>
                            <?php if (!$toConfirm) { ?>
                                <th class="text-center">Review</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="factor in getFactorsForForm(form.id)" :key="factor.id">
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
                            <td class="p-1 text-center">{{ factor.rating }}</td>
                            <?php if (!$toConfirm) { ?>
                                <td class="p-1 text-center">{{ factor.review }}</td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td colspan="2" class="p-1 bold text-right">Subtotal</td>
                            <td class="p-1 text-center">{{ form.subtotal_rating }}</td>
                            <?php if (!$toConfirm) { ?>
                                <td class="p-1 text-center">{{ form.subtotal_review }}</td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td colspan="2" class="p-1 bold text-right">Calculated Subtotal</td>
                            <td class="p-1 text-center">{{ form.final_subtotal_rating }}</td>
                            <?php if (!$toConfirm) { ?>
                                <td class="p-1 text-center">{{ form.final_subtotal_review }}</td>
                            <?php } ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>
</div>
<script>
    const app = Vue.createApp({
        data() {
            return {
                model: <?= $model ?>,
                forms: <?= $forms ?>,
                factors: <?= $factors ?>
            }
            ;
        },
        methods: {
            // Method to get factors by form_id
            getFactorsForForm(formId) {
                return this.factors.find((factors) => factors[0].appraisal_master_form_id === formId) || [];
            },
        }
    });

    app.mount('#app');

</script>