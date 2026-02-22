<?php

switch ($module) {
    case 'personal':
        $linkList = array(
            '1' => array(
                'name' => 'Asset On Hand',
                'link' => '/asset/asset-on-hand'
            ),
            '2' => array(
                'name' => 'To Receive',
                'link' => '/asset/asset-pending-receive'
            ),
            '3' => array(
                'name' => 'New Asset (Pending)',
                'link' => '/asset/asset-pending-register'
            ),
            '4' => array(
                'name' => 'All Asset List',
                'link' => '/asset/asset-all'
            ),
        );
        $this->params['breadcrumbs'][] = 'Asset Management';

        break;
    case 'super':
        $linkList = array(
            '1' => array(
                'name' => 'Asset List',
                'link' => '/asset/index-asset-super'
            ),
            '2' => array(
                'name' => 'New Asset (Pending)',
                'link' => '/asset/asset-pending-register-super'
            ),
            '3' => array(
                'name' => 'New Asset (Reject/Cancel)',
                'link' => '/asset/asset-reject-register-super'
            ),
        );
        $this->params['breadcrumbs'][] = 'Asset Management (Super User)';

        break;
}
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);


