<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>

<div class="stock-dispatch mb-5 pb-2">
    <?php
    $form = ActiveForm::begin([
                'id' => 'myForm',
                'method' => 'post',
    ]);
    ?>
    <h4><?= $this->title ?></h4>
    <?php if(isset($receiver)) : ?>
    <div class="row mt-3">
        <div class="col-lg-4 col-md-12 col-sm-12 d-flex align-items-center">
            <h5 for="receiver" class="mb-0 pr-3 text-nowrap">Received By: </h5>
            <select class="form-control form-control-sm" disabled="true">
                <option><?= $receiver['fullname'] ?></option>
                <input type="hidden" name="receiver[id]" class="form-control" value="<?= $receiver['user_id'] ?>"/>
            </select>
        </div>
    </div>
    <?php endif ; ?>
    <?php foreach ((isset($postData['dispatch']) ? $postData['dispatch'] : $postData) as $key => $data): ?>
        <div class="table-responsive">
            <div class="card mt-2 bg-light">
                <div class="p-1 pl-2 pr-2 m-0 card-header hoverItem border-dark btn-header-link" 
                     id="heading_<?= $key ?>" 
                     data-toggle="collapse" 
                     data-target="#collapse_<?= $key ?>" 
                     aria-expanded="true" 
                     aria-controls="collapse_<?= $key ?>">
                    <span class="p-0 m-0 accordionHeader">
                        #<?= $key + 1 ?>
                    </span>
                </div>

                <div id="collapse_<?= $key ?>" 
                     class="collapse show" 
                     aria-labelledby="heading_<?= $key ?>">
                    <div class="card-body p-1" style="background-color:white">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Model Type</th>
                                    <th>Brand</th>
                                    <th>Description</th>
                                    <th><?= $type ?> Quantity</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $row = 1;
                                foreach ($data as $detailId => $detail):
                                    ?>
                                    <?php if (isset($detail['dispatch_qty']) && isset($detail['remark'])): ?>
                                        <tr>
                                            <td class="text-center"><?= $row ?></td>
                                            <td><?= $detail['model_type'] ?></td>
                                            <td><?= $detail['brand'] ?></td>
                                            <td><?= $detail['descriptions'] ?></td>
                                            <td><?= $detail['dispatch_qty'] ?></td>
                                            <td><?= $detail['remark'] ?></td>
                                        </tr>
                                        <?php
                                        $row++;
                                    endif;
                                    ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (!empty($postData)) { ?>
        <h2>
            <?= Html::submitButton('Save', ['class' => 'btn btn-success px-3 mt-3 float-right']) ?>
        </h2>
    <?php } ?>
    <?php ActiveForm::end(); ?>
</div> 

