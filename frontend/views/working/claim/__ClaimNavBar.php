<?php

switch ($module) {
    case 'personal_claims':
        $linkList = array(
            '1' => array(
                'name' => 'Personal Claims - Pending',
                'link' => '/working/claim/personal-claim'),
            '2' => array(
                'name' => 'Personal Claims - Submitted',
                'link' => '/working/claim/personal-submitted-claim'),
        );
        break;

    case 'hr_claims':
        $linkList = array(
            '1' => array(
                'name' => 'Travel Claim (Pending)',
                'link' => '/working/claim/hr-travel-claim'),
            '2' => array(
                'name' => 'Travel Claim (All)',
                'link' => '/working/claim/hr-travel-claim-all'),
        );
        break;

    case 'account_claims':
        $linkList = array(
            '1' => array(
                'name' => 'Claim (Pending)',
                'link' => '/working/claim/account-claim-pending'),
            '2' => array(
                'name' => 'Claim (All)',
                'link' => '/working/claim/account-claim-all')
        );

        break;
    case 'super_claims':
        $linkList = array(
            '1' => array(
                'name' => 'All Claims',
                'link' => '/working/claim/super-claim-all'),
            '2' => array(
                'name' => 'Modify Claim (Item)',
                'link' => '/working/claim/super-claim-modify'),
            '3' => array(
                'name' => 'Medical Claim (Summary)',
                'link' => '/working/claim/super-claim-medical'),
            '4' => array(
                'name' => 'Entertainment Claim (Summary)',
                'link' => '/working/claim/super-claim-entertainment'),
        );
        $this->params['breadcrumbs'][] = ['label' => 'Super User (Claim)'];

        break;
}
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
