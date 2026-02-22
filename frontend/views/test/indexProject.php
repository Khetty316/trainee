<?php

use frontend\models\test\TestMain;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\test\TestMainSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = "Panel's Test List";
$this->params['breadcrumbs'][] = ['label' => 'Test Project List', 'url' => ['/test/testing/index-project-lists']];
$this->params['breadcrumbs'][] = $project->project_production_code;
?>
<style>
    .no-wrap {
        white-space: nowrap;
        overflow: visible;
    }
</style>

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2 m-0"><h5 class="m-0">Project Details:</h5></legend>
            <?php
            echo $this->render("../projectproduction/main/_detailviewProjectProduction", [
                'model' => $project
            ]);
            ?>
        </fieldset>
    </div>
</div>
<div id='app'>
    <?= $this->render('_indexProjectNavBar', ['project' => $project, 'pageKey' => '1']) ?>
    <div class="test-main-index col-lg-12 col-md-12 col-sm-12" style="overflow: auto">
        <table class="table table-sm table-bordered table-striped table-hover m-0 mt-2 col-12 rounded" id="maintable">
            <thead>
                <tr>
                    <!--<th @click="sortTable('name')" class="search-hover text-primary">Project Name</th>-->
                    <th @click="sortTable('panel_description')" class="search-hover text-primary">Panel Description</th>
                    <th @click="sortTable('project_production_panel_code')" class="search-hover text-primary">Panel Code</th>
                    <th class="text-center">Testing Stage</th>
                </tr>
                <tr>
                    <!--<th class="p-1"><input class="form-control" v-model="searchCriteria.name"></th>-->
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.panel_description"></th>
                    <th class="p-1"><input class="form-control" v-model="searchCriteria.project_production_panel_code"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="model in paginatedModels" :key="model.id">
                    <!--<td class="p-1">{{ model.name }}</td>-->
                    <td class="p-1">{{ model.panel_description }}</td>
                    <td class="p-1 text-center no-wrap">
                        {{ model.project_production_panel_code }}
                    </td>
                    <td class="p-1 text-center no-wrap">
                        <a :href="'/test/testing/index-panel?id=' + model.id" class="btn btn-sm btn-primary px-2" title="View tests in panel">Test Details</a>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="pagination my-3 flex justify-content-end" v-if="totalPages > 1">
            <button class="btn btn-sm btn-primary" @click="prevPage" :disabled="currentPage === 1">Previous</button>
            <span class="pt-1">&nbsp;{{ currentPage }} / {{ totalPages }}&nbsp;</span>
            <button class="btn btn-sm btn-primary" @click="nextPage" :disabled="currentPage === totalPages">Next</button>
        </div>

    </div>
</div>

<script>
    window.models = <?= $panels ?>;
    window.numPerPage = 10;
</script>
<script src="\js\vueTable.js"></script>