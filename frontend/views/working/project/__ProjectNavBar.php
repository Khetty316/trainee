<?php

use common\models\myTools\MyFormatter;

$linkList = array(
    '1' => array(
        'name' => 'Contract',
        'link' => '/working/project/view-contract?id=' . $id
    ),
    '2' => array(
        'name' => 'Letters',
        'link' => '/working/project/view-letter?id=' . $id
    ),
    '3' => array(
        'name' => 'Progress Claim (Main Con)',
        'link' => '/working/project/view-progress-claim-main?id=' . $id
    ),
    '4' => array(
        'name' => 'Progress Claim (Sub Con)',
        'link' => '/working/project/view-progress-claim-sub?id=' . $id
    ),
    '5' => array(
        'name' => 'Costing',
        'link' => '/working/project/view-costing?id=' . $id
    ),
    '6' => array(
        'name' => 'Closing',
        'link' => '/working/project/view-closing?id=' . $id
    ),
//    '7' => array(
//        'name' => 'Summary',
//        'link' => '/working/project/view-user-hr-documents'
//    ),
    '8' => array(
        'name' => 'Detail',
        'link' => '/working/project/view?id=' . $id
    ),
);


if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = ['label' => 'Master Project', 'url' => ['index']];
$this->params['breadcrumbs'][] = $projectCode . ' >> ' . $this->title;

echo $this->renderFile(Yii::getAlias('@app') . '/views/working/project/viewProjectMain.php', ['model' => $model]);

$certifiedTotal = $model->getTotalCertifiedClaim();
$contractSum = $model->contract_sum + $model->getVoTotal();
?>
<style>
    #myProgress {
        width: 100%;
        background-color: #ddd;
        border-radius: 25px;

    }

    #myBar {
        border-radius: 25px;
        width: 0%;
        height: 25px;
        background-color: #0275d8;
        padding: 0px;
        margin: 0px;
    }
</style>
Financial Progress:

<div id = "myProgress" class = 'mb-2'>
    <div id = "myBar" class = 'text-center'></div>
</div>
<hr style="border: 1px solid black"/>

<?php
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
?>
<script>
    $(function () {
        move();
    });
    var i = 0;
    var percent = <?= MyFormatter::asDecimal2NoSeparator($certifiedTotal / ($contractSum > 0 ? $contractSum : 1) * 100) ?>;
    if (percent >= 100) {
        maxWidth = 100;
    } else {
        maxWidth = percent;
    }
    function move() {
        if (i === 0) {
            i = 1;
            var elem = document.getElementById("myBar");
            var width = 1;
            var id = setInterval(frame, 2);
            function frame() {
                if (width >= maxWidth) {
                    clearInterval(id);
                    i = 0;
                } else {
                    width++;
                    elem.style.width = width + "%";
                }
            }
        }
        $("#myBar").html("<b class='text-white'>" + percent + " %</b>");
    }


</script>