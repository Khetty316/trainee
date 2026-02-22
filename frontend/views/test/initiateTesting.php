<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

$this->title = 'Initiate Panel Testing';
$this->params['breadcrumbs'][] = ['label' => 'Test List', 'url' => ['/test/index-main']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="initiate-testing">

    <div class="col-12">
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2 m-0">Starting Test Info</legend>
            <div class="col-12 row">
                <div class="col-3">
                    <table>
                        <tr>
                            <td>Test Type </td>
                            <td>: </td>
                            <td>(ITP) Inspection Test Plan</td>
                        </tr>
                        <tr>
                            <td>Document Reference</td>
                            <td>:</td>
                            <td>TK/TC-LST</td>
                        </tr>
                        <tr>
                            <td>Revision No </td>
                            <td> : </td>
                            <td>0</td>
                        </tr>
                    </table>
                </div>
                <div class="col-3">
                    <table>
                        <tr>
                            <td> </td>
                            <td>: </td>
                            <td>(ITP) Inspection Test Plan</td>
                        </tr>
                        <tr>
                            <td>Document Reference</td>
                            <td>:</td>
                            <td>TK/TC-LST</td>
                        </tr>
                        <tr>
                            <td>Revision No </td>
                            <td> : </td>
                            <td>0</td>
                        </tr>
                    </table>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="col-12">
        <div class="row">
            <div class="col-4">
                <fieldset class="form-group border p-3">
                    <legend class="w-auto px-2 m-0">Select Project:</legend>
                </fieldset>
            </div>
            <div class="col-4">
                <fieldset class="form-group border p-3">
                    <legend class="w-auto px-2 m-0">Select Panel:</legend>
                </fieldset>
            </div>
            <div class="col-4">
                <fieldset class="form-group border p-3">
                    <legend class="w-auto px-2 m-0">Fill In Information:</legend>
                </fieldset>
            </div>
        </div>
    </div>
</div>
