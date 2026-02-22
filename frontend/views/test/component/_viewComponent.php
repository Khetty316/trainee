<?php

use frontend\models\test\RefTestCompType;
use frontend\models\test\TestDetailComponent;
?>
<style>
    /*Masonry Column*/
    .masonry {
        display: flex;
        flex-flow: row wrap;
    }

    .masonry-brick {
        flex: auto;
        min-width: 150px;
        margin: 0 8px 8px 0; /* Some gutter */
    }

</style>
<div class="view-component">
    <?php foreach ($details as $key => $detail) { ?>
        <div class="mb-4">
            <h5>Details of <?= $detail['form']->comp_type == RefTestCompType::TYPE_OTHER ? $detail['form']->comp_name . " " . $detail['form']->pou0->name . '-' . $detail['form']->pou_val : $detail['form']->compType->name . " " . $detail['form']->pou0->name . '-' . $detail['form']->pou_val ?> : </h5>
            <div class="px-2 masonry border">
                <?php foreach ($detail['attributetorender'] as $attribute) { ?>
                    <?php
                    if ($attribute == TestDetailComponent::ATTRIBUTE_COMPNAME):
                        continue;
                        ?>
                    <?php elseif ($attribute == TestDetailComponent::ATTRIBUTE_ACCESSORY): ?>
                        <div class="masonry-brick">
                            <div><?= $detail['form']->attributeLabels()["$attribute"] ?> : 
                                <span><?php
                                    foreach ($detail['form'][$attribute] as $key => $acsrycode) {
                                        echo $accesList[$acsrycode] ?? null;
                                        if (!empty($detail['form'][$attribute][$key + 1])) {
                                            echo ", ";
                                        }
                                    }
                                    ?></span>
                            </div>
                        </div>
                    <?php elseif ($attribute == TestDetailComponent::ATTRIBUTE_FUNCTIONTYPE): ?>
                        <div class="masonry-brick">
                            <div class="w-100"><?= $detail['form']->attributeLabels()["$attribute"] ?> : <span><?= $funcList[$detail['form'][$attribute]] ?></span></div>

                        </div>
                    <?php elseif ($attribute != TestDetailComponent::ATTRIBUTE_POU && $attribute != TestDetailComponent::ATTRIBUTE_POUVAL): ?>
                        <div class="masonry-brick">
                            <div><?= $detail['form']->attributeLabels()["$attribute"] ?> : <span><?= $detail['form'][$attribute] ?></span></div>

                        </div>
                    <?php endif; ?>
                <?php } ?>
                <?php
                if (!empty($detail['otheritem'])) {
                    foreach ($detail['otheritem'] as $item) {
                        ?>
                        <div class="masonry-brick">
                            <div><?= $item->attribute ?> : <span><?= $item->value ?></span></div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    <?php } ?>

    <div>
        <h5>Components Conformity</h5>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>No.</th>
                    <th class="col-5">Non-conform Component</th>
                    <th class="col-5">Remark</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conformities as $key => $conformity): ?>
                    <tr class="p-0 m-0">
                        <td>
                            <?= $key + 1 ?>
                        </td>
                        <td>
                            <?= $conformity->non_conform ?>
                        </td>
                        <td> 
                            <?= $conformity->remark ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>