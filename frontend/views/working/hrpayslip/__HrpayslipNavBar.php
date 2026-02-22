<?php

switch ($module) {
    case 'hr_payslip':
        $linkList = array(
            '1' => array(
                'name' => 'Payroll List',
                'link' => '/working/hrpayslip/index'),
            '2' => array(
                'name' => 'Payslip',
                'link' => '/working/hrpayslip/index-hrpayslip'),
        );
        break;
}
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
