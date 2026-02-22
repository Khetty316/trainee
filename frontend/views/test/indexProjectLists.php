<?php

use frontend\models\test\TestMain;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\test\TestMainSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
?>
<style>
    .no-wrap {
        white-space: nowrap;
        overflow: visible;
    }
</style>
<div id="app">

    <div class="test-main-index">
        <?= $this->render('_PanelTestNavBar', ['pageKey' => '1']) ?>
        <h5><?php //= Html::encode($this->title) ?></h5>
 
        <table class="table table-sm table-bordered table-striped table-hover m-0 mt-2 col-12 rounded" id="maintable">
            <thead>
                <tr>
                    <th @click="sortTable('project_production_code')" class="search-hover text-primary col-3">Project Code</th>
                    <th @click="sortTable('name')" class="search-hover text-primary">Project Name</th>
                </tr>
                <tr>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.project_production_code"></th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.name"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="model in paginatedModels" :key="model.id">
                    <td class="p-1"><a :href="'/test/testing/index-project?id=' + model.id" title="View panels in project">{{ model.project_production_code }}</a></td>
                    <td class="p-1">{{ model.name }}</td>
<!--                    <td class="p-1 text-center no-wrap">
                        <a :href="'/test/testing/index-project?id=' + model.id" class="btn btn-sm btn-primary" title="View panels in project">View Project</a>
                    </td>-->
                </tr>
            </tbody>
        </table>

        <div class="pagination my-3 flex justify-content-end">
            <button class="btn btn-sm btn-primary" @click="prevPage" :disabled="currentPage === 1">Previous</button>
            <span class="pt-1">&nbsp;{{ currentPage }} / {{ totalPages }}&nbsp;</span>
            <button class="btn btn-sm btn-primary" @click="nextPage" :disabled="currentPage === totalPages">Next</button>
        </div>

    </div>
</div>

<script>
    window.models = <?= $projects ?>;
    window.numPerPage = 15;
</script>
<script src="\js\vueTable.js"></script>