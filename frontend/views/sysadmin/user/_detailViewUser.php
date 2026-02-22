<?php

use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;

$isUserAdminPage = $isUserAdminPage ?? false;
?>
<div class="row">
    <div class="col-12">
        <div class="pb-3 col-lg-12">
            <div class="justify-content-center d-flex pb-3">
                <img style="height: 250px" src="<?= yii\helpers\Url::to("/profile/get-file?filename=" . urlencode($model->profile_pic) . "&id=" . $model->id) ?>" class="img-thumbnail rounded"
                     onError="this.onerror=null;this.src='<?= Yii::$app->request->getBaseUrl() ?>/images/blank-profile-picture.png';">    
            </div>
        </div>
        <div class="col-12  justify-content-center d-flex">
            <div class="col-sm-12 col-md-8">
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
                        'email:email',
                        [
                            'attribute' => 'employment_type',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->employmentType->employment_type ?? null;
                            }
                        ],
                        [
                            'attribute' => 'company_name',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->companyName->company_name ?? null;
                            }
                        ],
                        [
                            'attribute' => 'superior_id',
                            'label' => 'Superior',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->superior->fullname ?? null;
                            }
                        ],
                        [
                            'attribute' => 'sex',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model['sex0']['sex_name'] ?? null;
                            }
                        ],
                        [
                            'attribute' => 'ethnic_id',
                            'label' => 'Ethnic',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model['ethnic']['ethnic_name'] ?? null;
                            }
                        ],
                        [
                            'attribute' => 'religion_id',
                            'label' => 'Religion',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model['religion']['religion_name'] ?? null;
                            }
                        ],
                        [
                            'attribute' => 'designation',
                            'value' => function ($data) {
                                return $data['designation0']['design_name'] ?? null;
                            }
                        ],
                                [
                            'attribute' => 'grade',
                            'label' => 'Grade Level',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->grade0->name ?? null;
                            }
                        ],
                        [
                            'attribute' => 'date_of_join',
                            'value' => function ($data) {
                                return MyFormatter::asDate_Read($data->date_of_join);
                            }
                        ],
                        'ic_no',
                        'contact_no',
                        [
                            'attribute' => 'address',
                            'format' => 'raw',
                            'value' => function ($data) {
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
                            'value' => function ($data) {
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
                            'value' => function ($data) {
                                return MyFormatter::asDateTime_ReaddmYHi($data->updated_at);
                            }
                        ],
                        [
                            'attribute' => 'created_at',
                            'value' => function ($data) {
                                return MyFormatter::asDateTime_ReaddmYHi($data->created_at);
                            }
                        ],
                    ],
                ])
                ?>
            </div>
        </div>
    </div>
</div>