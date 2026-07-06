<?php
/* @var $company frontend\models\common\RefCompanyGroupList */
?>
<div style="font-family: Arial;margin-bottom: 0cm;">
    <table>
        <tr class="vbtm">
            <td>
                <img width="10cm"
                     style="max-height:100%; max-width:100%"
                     src="<?= Yii::getAlias('@frontend/web/images/' . $company->code . '.png') ?>"/>
            </td>
            <td style="width:37%">
                <table class="vtop table table-sm table-borderless">
                    <tr><td style="font-size:8pt;"><?= $company->company_addr_1 ?? null ?></td></tr>
                    <tr><td style="font-size:8pt;"><?= $company->company_addr_2 ?? null ?></td></tr>
                    <tr><td style="font-size:8pt;"><?= $company->company_addr_3 ?? null ?></td></tr>
                    <tr><td style="font-size:8pt;"><?= $company->company_addr_4 ?? null ?></td></tr>
                    <tr><td style="margin:0;padding:0">
                            <table style="border-collapse:collapse">
                                <tr><td class="vtop" style="font-size:8pt;">TEL:</td>
                                    <td style="font-size:8pt;"><?= $company->tel ?? null ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td style="font-size:8pt;">E-mail: <?= $company->email ?? null ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <br>
</div>
