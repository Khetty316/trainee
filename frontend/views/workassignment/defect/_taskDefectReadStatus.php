<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
?>
<div class="work-assignment-master-form">
    <div class="row">
        <div class="col-12">
            <fieldset class="border rounded p-3">
                <legend class="w-auto px-2 m-0">Staff Read Status:</legend>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered text-center">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 40%;">Assigned Staffs</th>
                                <th style="width: 15%;">Read</th>
                                <th style="width: 40%;">Read At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($details as $index => $detail) : ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td class="text-start"><?= Html::encode($detail->staff->fullname) ?></td>
                                    <td>
                                        <?php if ($detail->is_read == 2) : ?>
                                            <i class="far fa-check-circle text-success" title="Read"></i>
                                        <?php else : ?>
                                            <i class="far fa-times-circle text-danger" title="Unread"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?=
                                        $detail->is_read == 2 ? MyFormatter::asDateTime_ReaddmYHi($detail->read_at) : '-'
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="form-group">
        <?=
        Html::button("Close", [
            'class' => 'btn btn-danger float-right m-2',
            'data-dismiss' => 'modal'
        ])
        ?>

    </div>
</div>
