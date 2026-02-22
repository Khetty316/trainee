<?php

use frontend\models\common\RefGeneralReferences;

$company = $revision->projectQType->project->companyGroupCode;
?>
<div  style="font-family: Arial;margin-bottom: 0cm;">
    <table>
        <tr class="vbtm">
            <td>
                <img width="10cm" style="max-height:100%; max-width:100%" src="<?= Yii::$app->request->getBaseUrl() ?>/images/<?= $company->code ?>.png"/>   
            </td>
            <td style='width:37%'>
                <table  class="vtop table table-sm table-borderless">
                    <tr><td style="font-size:8pt;"><?= $company->company_addr_1 ?? null ?></td></tr>
                    <tr><td style="font-size:8pt;"><?= $company->company_addr_2 ?? null ?></td></tr>
                    <tr><td style="font-size:8pt;"><?= $company->company_addr_3 ?? null ?></td></tr>
                    <tr><td style="font-size:8pt;"><?= $company->company_addr_4 ?? null ?></td></tr>
                    <tr><td style="margin: 0px;padding: 0px">
                            <table style="border-collapse: collapse">
                                <tr><td class="vtop" style="font-size:8pt;">TEL: </td><td style="font-size:8pt;"><?= $company->tel ?? null ?></td></tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td style="font-size:8pt;">E-mail: <?=  $company->email ?? null ?></td></tr>
                </table>
            </td>
        </tr>
    </table>
</div>
