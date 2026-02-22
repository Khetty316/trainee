<?php

switch ($module) {
    case 'staff':
        $linkList = array(
            '1' => array(
                'name' => 'Pending',
                'link' => '/quotation/staff-view-quotation-list-pending'
            ),
            '2' => array(
                'name' => 'All',
                'link' => '/quotation/staff-view-quotation-list'
            ),
//            '3' => array(
//                'name' => 'New Asset (Pending)',
//                'link' => '/asset/asset-pending-register'
//            ),
//            '4' => array(
//                'name' => 'All Asset List',
//                'link' => '/asset/asset-all'
//            ),
        );
        $this->params['breadcrumbs'][] = 'Request For Quotation';

        break;
    case 'proc':
        $linkList = array(
            '1' => array(
                'name' => 'Pending List',
                'link' => '/quotation/proc-view-quotation-list-pending'
            ),
            '2' => array(
                'name' => 'To Issue P.O.',
                'link' => '/quotation/proc-view-quotation-list-po'
            ),
            '3' => array(
             'name' => 'All',
                'link' => '/quotation/proc-view-quotation-list-all'
            ),
        );
        $this->params['breadcrumbs'][] = 'RFQ (Procurement)';

        break;
}
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);


