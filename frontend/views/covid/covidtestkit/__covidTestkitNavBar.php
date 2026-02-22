<?php

switch ($module) {
    case 'admin':
        $linkList = array(
            '1' => array(
                'name' => 'Test-Kit Summary',
                'link' => '/covidtestkit/index'
            ),
            '2' => array(
                'name' => 'Movement Detail',
                'link' => '/covidtestkit/index-detail'
            ),
            '3' => array(
                'name' => 'Transferred Test-Kit',
                'link' => '/covidtestkit/index-covid-testkit-transferred-detail'
            ),
        );
        $this->params['breadcrumbs'][] = 'Covid-19 Test-Kit (Admin)';

        break;
}
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);


