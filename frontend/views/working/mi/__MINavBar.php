<?php

switch ($module) {
    case 'mi_requestor':
        $linkList = array(
            '1' => array(
                'name' => 'Review',
                'link' => '/working/mi/requestorreview'),
            '2' => array(
                'name' => 'Acknowledge',
                'link' => '/working/mi/requestor-acknowledge'),
            '3' => array(
                'name' => 'Review History',
                'link' => '/working/mi/requestorreview-history'),
        );
        break;

    case 'mi_account':
        $linkList = array(
            '1' => array(
                'name' => 'Payment',
                'link' => '/working/mi/accountpay'),
            '2' => array(
                'name' => 'Receiving',
                'link' => '/working/mi/accountreceivedoc'),
            '3' => array(
                'name' => 'All Incoming Document',
                'link' => '/working/mi/accountalldoc'),
        );
        break;

    case 'mi_admin':
        $linkList = array(
            '1' => array(
                'name' => 'Active Records',
                'link' => '/working/mi/adminactiverecord'),
            '2' => array(
                'name' => 'Keep Document',
                'link' => '/working/mi/adminkeepdoc'),
            '3' => array(
                'name' => 'Send To Account',
                'link' => '/working/mi/adminsenddocacc'),
            '4' => array(
                'name' => 'Send To Procurement',
                'link' => '/working/mi/adminsenddocproc'),
        );
        break;

    case 'mi_director':
        $linkList = array(
            '1' => array(
                'name' => 'Review',
                'link' => '/working/mi/directorreview'),
            '2' => array(
                'name' => 'Acknowledge',
                'link' => '/working/mi/director-acknowledge'),
        );
        break;

    case 'mi_procurement':
        $linkList = array(
            '1' => array(
                'name' => 'Provide GRN',
                'link' => '/working/mi/procurementgrn'),
            '2' => array(
                'name' => 'Receiving',
                'link' => '/working/mi/procurementreceivedoc'),
            '3' => array(
                'name' => 'Edit GRN',
                'link' => '/working/mi/procurement-edit-grn'),
        );
        break;

    case 'mi_super':
        $linkList = array(
            '1' => array(
                'name' => 'All',
                'link' => '/working/mi/super-mi-all'),
            '2' => array(
                'name' => 'Edit Invoices',
                'link' => '/working/mi/super-mi-invoice'),
        );
        $this->params['breadcrumbs'][] = ['label' => 'Super User (Document Incoming)'];

        break;
}

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
?> 

<?= $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]) ?>