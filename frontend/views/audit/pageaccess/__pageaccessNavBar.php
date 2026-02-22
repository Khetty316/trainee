<?php

switch ($module) {
    case 'admin':
        $linkList = array(
            '1' => array(
                'name' => 'Summarize By Hour',
                'link' => '/audit/pageaccess/index-sum-by-hour'
            ),
            '2' => array(
                'name' => 'Detail',
                'link' => '/audit/pageaccess/index'
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
        $this->params['breadcrumbs'][] = 'Page Access Activity';

        break;

}
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);


