<?php

use frontend\models\common\RefGeneralReferences;

$company = $revision->projectQType->project->companyGroupCode;
?>
<div  style="font-family: Arial;margin-bottom: 0cm;">
    <table>
        <tr class="vbtm">
            <td>
                <img width="10cm" style="max-height:100%; max-width:100%" src="<?= Yii::$app->request->getBaseUrl() ?>/images/<?= $company->logo_name ?>"/>   
            </td>
        </tr>
    </table>
</div>
