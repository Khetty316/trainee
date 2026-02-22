<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'System Admin - Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">


    <div class="row">
        <div class='col-lg-1'></div>
        <div class="col-lg-10">
            <h3><?php
                if ($model->status == 0) {
                    $status = "<span class='text-danger'>(Deleted)<span>";
                } else if ($model->status == 9) {
                    $status = "<span class='text-warning'>(Inactive)<span>";
                } else if ($model->status == 10) {
                    $status = "<span class='text-success'>(Active)<span>";
                }

                echo Html::encode($this->title) . " " . $status;
                ?></h3>

            <p>
                <?= Html::a('Update <i class="fas fa-edit"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
                <?php //    = Html::a('Update <i class="fas fa-edit"></i>', ['/profile/update', 'id' => $model->id], ['class' => 'btn btn-success'])  ?>

            </p>
            <div class="pb-3">
                <div class="justify-content-center d-flex pb-3">
                    <img style="height: 250px" src="<?= yii\helpers\Url::to("/profile/get-file?filename=" . urlencode($model->profile_pic) . "&id=" . $model->id) ?>" class="img-thumbnail rounded"
                         onError="this.onerror=null;this.src='<?= Yii::$app->request->getBaseUrl() ?>/images/blank-profile-picture.png';">    
                </div>
            </div>
            <?=
            DetailView::widget([
                'model' => $model,
                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                'template' => "<tr><th style='width: 30%;'>{label}</th><td>{value}</td></tr>",
                'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
                'attributes' => [
                    [
                        'attribute' => 'id',
                        'label' => "ID (E-office)"
                    ],
                    'username',
                    'staff_id',
                    'fullname',
                    [
                        'attribute' => 'sex',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model['sex0']['sex_name'];
                        }
                    ],
                    [
                        'attribute' => 'ethnic_id',
                        'label' => 'Ethnic',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model['ethnic']['ethnic_name'];
                        }
                    ],
                    [
                        'attribute' => 'religion_id',
                        'label' => 'Religion',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model['religion']['religion_name'];
                        }
                    ],
                    [
                        'attribute' => 'designation',
                        'value' => function($data) {
                            return $data['designation0']['design_name'];
                        }
                    ],
                    [
                        'attribute' => 'date_of_join',
                        'value' => function($data) {
                            return MyFormatter::asDate_Read($data->date_of_join);
                        }
                    ],
                    'ic_no',
                    'email:email',
                    'contact_no',
                    [
                        'attribute' => 'address',
                        'format' => 'raw',
                        'value' => function($data) {
                            $address = $data->address;
                            if ($data->address_line_2) {
                                $address .= "<br/>" . $data->address_line_2;
                            }
                            return $address . "<br/>" . $data->postcode . ", " . (is_null($data->area) ? "" : $data->area->area_name);
                        }
                    ],
                    'emergency_contact_no',
                    'emergency_contact_person',
                    [
                        'attribute' => 'status',
                        'value' => function($data) {
                            if ($data->status == 0) {
                                return "Deleted";
                            } else if ($data->status == 9) {
                                return "Inactive";
                            } else if ($data->status == 10) {
                                return "Active";
                            }
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function($data) {
                            return MyFormatter::asDateTime_ReaddmYHi($data->updated_at);
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function($data) {
                            return MyFormatter::asDateTime_ReaddmYHi($data->created_at);
                        }
                    ],
                ],
            ])
            ?>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class='col-lg-1'></div>
        <div class="col-lg-10">
            <h3>System Authorisation / Roles</h3>
            <p>               
                <?= Html::a('Update Auth <i class="fas fa-edit"></i>', ['update-user-auth', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            </p>
            <table class="table table-striped table-bordered table-sm">
                <tr>
                    <th>Auth Code</th><th>Auth Name</th><th>Assign Time</th>
                </tr>
                <?php
                foreach ($authAssign as $key => $auth) {
                    $authItem = $auth->itemName;
                    echo "<tr><td>" . $authItem->name . "</td><td>"
                    . $authItem->auth_fullname . "</td><td>"
                    . MyFormatter::asDateTime_ReaddmYHi($auth->created_at) . "</tr></td>";
                }
                ?>
            </table>
        </div>
    </div>
</div>
