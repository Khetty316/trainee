<?php

use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\appraisal\AppraisalMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "Appraisal - $main->index";
$this->params['breadcrumbs'][] = "HR Appraisal";
$this->params['breadcrumbs'][] = ['label' => "Appraisal List", 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="app">
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0"> Appraisal Info - <?= $main->index ?></legend>
        <?=
        $this->render('_viewAppraisalMain', [
            'model' => $main,
        ]);
        ?>
    </fieldset>
    <div v-if="models && models.length > 0">
        <div class="row m-0 p-0">
            <div class="col pl-0">
                <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', "?id=$main->id", ['class' => 'btn btn-primary mr-1']) ?>    
            </div>
            <div class="col text-right pr-0">
                <?php
                if ($main->status != \frontend\models\common\RefAppraisalStatus::STS_COMPLETE) {
                    echo Html::a('Add Staff to Appraisal <i class="fas fa-plus"></i>', "appraisal-add-staff?mainId=$main->id", ['class' => 'btn btn-success mr-1']);
                    echo Html::a('Delete Staff from Appraisal <i class="fas fa-minus"></i>', "appraisal-delete-staff?mainId=$main->id", ['class' => 'btn btn-danger']);
                }
                ?>
                <?php
                if ($main->status == \frontend\models\common\RefAppraisalStatus::STS_COMPLETE) {
                    echo Html::a(
                            'Export to CSV <i class="fas fa-file-csv fa-lg"></i>',
                            ['export-to-csv', 'mainId' => $main->id],
                            [
                                'target' => '_blank',
                                'class' => 'btn btn-primary'
                            ]
                    );
                }
                ?>
            </div>
        </div>
        <div>
            <table class="table table-bordered table-striped table-hover mt-2 col-12 rounded">
                <thead>
                    <tr class="text-primary">
                        <th @click="sortTable('staff_id')" class="search-hover col-1">Staff ID</th>
                        <th @click="sortTable('fullname')" class="search-hover col-2">Fullname</th>
                        <th @click="sortTable('staff_type')" class="search-hover col-1">Staff Type</th>
                        <th @click="sortTable('appraisal_sts_name')" class="search-hover">Appraisal Status</th>
                        <th @click="sortTable('overall_rating')" class="search-hover">Overall Rating</th>
                        <th @click="sortTable('overall_review')" class="search-hover">Overall Review</th>
                        <th @click="sortTable('appraise_date')" class="search-hover">Appraise Date</th>
                        <th @click="sortTable('review_by_name')" class="search-hover">Review By</th>
                        <th @click="sortTable('review_date')" class="search-hover">Review Date</th>
                    </tr>
                    <tr>
                        <th class="p-1"><input class="form-control" v-model="searchCriteria.staff_id" type="number"></th>
                        <th class="p-1"><input class="form-control" v-model="searchCriteria.fullname"></th>
                        <th class="p-1">
                            <select class="form-control" v-model="searchCriteria.staff_type">
                                <option value="">Select Type</option>
                                <?php foreach ($staffTypeList as $key => $statusName): ?>
                                    <option value="<?= $key ?>"><?= $statusName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </th>
                        <th class="p-1">
                            <select class="form-control" v-model="searchCriteria.appraisal_sts_name">
                                <option value="">Select Status</option>
                                <?php foreach ($statusList as $key => $statusName): ?>
                                    <option value="<?= $key ?>"><?= $statusName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </th>
                        <th class="p-1"><input class="form-control" v-model="searchCriteria.overall_rating"></th>
                        <th class="p-1"><input class="form-control" v-model="searchCriteria.overall_review"></th>
                        <th class="p-1">
                            <input type="text" id="appraise_date-datepicker" class="form-control" v-model="searchCriteria.appraise_date" @click="showDatePicker('appraise_date')" placeholder="Select Date">
                        </th>
                        <th class="p-1"><input class="form-control" v-model="searchCriteria.review_by_name"></th>
                        <th class="p-1">
                            <input type="text" id="review_date-datepicker" class="form-control" v-model="searchCriteria.review_date" @click="showDatePicker('review_date')" placeholder="Select Date">
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="model in filteredModels" :key="model.id">
                        <td class="p-1">{{ model.staff_id }}</td>
                        <td class="p-1"><a :href="'/appraisal/view-appraisal?id=' + model.id" class="btn-link">{{ model.fullname }}</a></td>
                        <td class="p-1">{{ model.staff_type }}</td>
                        <td class="p-1">{{ model.appraisal_sts_name }}</td>
                        <td class="p-1">{{ model.overall_rating }}</td>
                        <td class="p-1">{{ model.overall_review }}</td>
                        <td class="p-1">{{ formatDateDMY(model.appraise_date) }}</td>
                        <td class="p-1">{{ model.review_by_name }}</td>
                        <td class="p-1">{{ formatDateDMY(model.review_date) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div v-else>
        <?= Html::a("Initiate Staff Appraisal - $main->index <i class='fas fa-external-link-square-alt'></i>", ['/appraisal/index-initiate', 'mainId' => $main->id], ['class' => 'btn btn-success']) ?>
    </div>
</div>

<script>
    window.models = <?= $users ?>;

</script>
<script src="\js\vueTable.js"></script>

