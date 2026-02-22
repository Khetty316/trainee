<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap4\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap4\Modal;

AppAsset::register($this);
$session = Yii::$app->session;

$acc = 'ACC1';
$proc = 'PROC1';
$admin = 'ADMIN1';
$hr = 'HR1';
$it = 'IT1';
$sysadmin = 'SYSADMIN1';
$isStaff = 'STAFF';
$isProbation = 'PROB';
$directors = 'ENGR11';

$thisUser = Yii::$app->user;

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="manifest" href="/manifest.json">
        <script src="/index.js" ></script>

        <?php $this->registerCsrfMetaTags() ?>
        <title>NPL E-Office</title>
        <?php $this->head() ?>

    </head>
    <body>
        <?php $this->beginBody() ?>

        <div class="wrap">

            <nav id="w1-navbar" class="navbar navbar-dark bg-dark navbar-expand-lg fixed-top mainmenu p-0 m-0" >
                <button class="navbar-toggler ml-2" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="/" style="align-self: center"><b>NPL E-Office</b></a>
                <?php if (!$thisUser->isGuest) { ?>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
    <!--                            <li class="active"><a href="#">Home <span class="sr-only">(current)</span></a></li>
                            <li><a href="#">Link</a></li>-->
                            <?php
                            //   if (Yii::$app->user->can($isStaff)) {
                            ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class='far fa-smile'></i>&nbsp;&nbsp;&nbsp;Personal</a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <?php
                                    echo Html::tag('li',
                                            Html::a('<i class=\'far fa-calendar\'></i>&nbsp;&nbsp;&nbsp; Leave Application', '/office/leave/personal-leave', ['class' => 'pl-4'])
                                            , ['class' => 'pl-0']);
                                    echo Html::tag('li',
                                            Html::a('<i class="fas fa-money-check-alt"></i>&nbsp;&nbsp;&nbsp; Claim Application', '/working/claim/personal-claim', ['class' => 'pl-4'])
                                            , ['class' => 'pl-0']);
                                    ?>
                                </ul>
                            </li>
                            <?php //}  ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class='far fa-building'></i>&nbsp;&nbsp;&nbsp;E-Working
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li class="dropdown dropright pl-0">
                                        <a class="dropdown-toggle pl-4" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width:250px">
                                            <i class='far fa-folder-open'></i>&nbsp;&nbsp;&nbsp;Document Incoming 
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <?php
                                            if ($session->get($proc) || $session->get($acc) || $session->get($admin) || $session->get($hr)) {
                                                echo Html::tag('li',
                                                        Html::a('<i class="far fa-plus-square"></i> New Incoming Registration', '/working/mi/index'));
                                            }
//                                            if ($thisUser->can($isStaff)) {
                                            if ($session->get($isStaff)) {
                                                echo Html::tag('li',
                                                        Html::a('Requestor Review <i class="fas fa-mobile-alt fa-pull-right pt-1"></i>&nbsp;&nbsp;&nbsp;', '/working/mi/requestorreview'));
                                            }
                                            if ($session->get($admin)) {
                                                echo Html::tag('li',
                                                        Html::a('Admin Dept.', '/working/mi/adminactiverecord'));
                                            }

                                            if ($session->get($proc)) {
                                                echo Html::tag('li',
                                                        Html::a('Procurement Dept.', '/working/mi/procurementgrn'));
                                            }

                                            if ($session->get($acc)) {

                                                echo Html::tag('li',
                                                        Html::a('Account Dept.', '/working/mi/accountpay'));
                                            }

                                            if ($session->get($directors)) {
                                                echo Html::tag('li',
                                                        Html::a('Director <i class="fas fa-mobile-alt fa-pull-right pt-1"></i>&nbsp;&nbsp;&nbsp;', '/working/mi/directorreview'));
                                            }
                                            ?>
                                        </ul>
                                    </li>
                                    <?php
                                    if ($thisUser->identity->is_leave_superior == 1 || $session->get($directors) || $session->get($hr)) {
                                        ?>
                                        <li class="dropdown dropright pl-0">
                                            <a class="dropdown-toggle pl-4" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  style="min-width:250px">
                                                <i class='far fa-calendar'></i>&nbsp;&nbsp;&nbsp;Leave Application
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                                <?php
                                                if ($thisUser->identity->is_leave_superior == 1 || $session->get($directors)) {
                                                    echo Html::tag('li',
                                                            Html::a('Superior Approval', '/working/leavemgmt/superior-leave-approval'));
                                                }
                                                if ($session->get($hr)) {
                                                    echo Html::tag('li',
                                                            Html::a('HR', '/working/leavemgmt/hr-leave-approval'));
                                                }
                                                if ($session->get($directors)) {
                                                    echo Html::tag('li',
                                                            Html::a('Directors\' Approval ', '/working/leavemgmt/director-leave-approval'));
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                        <?php
                                    }

                                    if ($session->get($proc)) {
                                        echo Html::tag('li',
                                                Html::a('<i class="fas fa-cash-register"></i>&nbsp;&nbsp;&nbsp;Purchase Order', '/working/po/index', ['class' => 'pl-4'])
                                                , ['class' => 'pl-0']);
                                    }

                                    echo Html::tag('li',
                                            Html::a('<i class="fas fa-hard-hat"></i>&nbsp;&nbsp;&nbsp;Projects', '/working/projects/index', ['class' => 'pl-4'])
                                            , ['class' => 'pl-0']);
                                    ?>
                                    <li class="dropdown dropright pl-0">
                                        <a class="dropdown-toggle pl-4" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  style="min-width:250px">
                                            <i class="fas fa-money-check-alt"></i>&nbsp;&nbsp;&nbsp;Claims
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <?php
                                            echo Html::tag('li',
                                                    Html::a('Authorize Claim <i class="fas fa-mobile-alt fa-pull-right pt-1"></i>&nbsp;&nbsp;&nbsp;', '/working/claim/authorize-claim'));
//                                            if ($session->get($hr) || $session->get($acc)) {
//                                                echo Html::tag('li',
//                                                        Html::a('Master Claim List', '/working/claim/hr-submitted-claim'));
//                                            }
                                            if ($session->get($hr)) {
                                                echo Html::tag('li',
                                                        Html::a('HR Dept. - Travel Claim', '/working/claim/hr-travel-claim'));
                                            }

                                            if ($session->get($proc)) {
                                                echo Html::tag('li',
                                                        Html::a('Procurement Dept. - Claim GRN', '/working/claim/proc-claim-grn'));
                                            }

                                            if ($session->get($acc)) {
                                                echo Html::tag('li',
                                                        Html::a('Account Dept. - Claim', '/working/claim/account-claim-pending'));
                                            }

                                            if ($session->get($directors)) {
                                                echo Html::tag('li',
                                                        Html::a('Director - Special Approval <i class="fas fa-mobile-alt fa-pull-right pt-1"></i>&nbsp;&nbsp;&nbsp;', '/working/claim/director-special-approval'));
                                            }
                                            ?>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="navbar-nav mr-1">
                            <?php
                            if ($session->get($sysadmin)) {
                                ?>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-users-cog"></i>&nbsp;&nbsp;&nbsp;System Administrator
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <li class="dropdown dropleft pl-0">
                                            <a class="dropdown-toggle pl-4" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-users-cog"></i>&nbsp;&nbsp;&nbsp;Users
                                            </a>
                                            <ul class="dropdown-menu ">
                                                <?php
                                                echo Html::tag('li',
                                                        Html::a('<i class="fas fa-users"></i>&nbsp;&nbsp;&nbsp;Manage Users', '/sysadmin/user/index', ['class' => 'pl-4'])
                                                        , ['class' => 'pl-0']);
                                                echo Html::tag('li',
                                                        Html::a('<i class="fas fa-user-secret"></i>&nbsp;&nbsp;&nbsp;RBAC Config', '/auth/rbac', ['class' => 'pl-4'])
                                                        , ['class' => 'pl-0']);
                                                ?>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="far fa-user-circle"></i>&nbsp;&nbsp;&nbsp;<?= $thisUser->identity->username ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <?php
                                    echo Html::tag('li',
                                            Html::a('<i class="far fa-id-card"></i>&nbsp;&nbsp;&nbsp;My Space', '/profile/view-profile', ['class' => 'pl-4'])
                                            , ['class' => 'pl-0']);
                                    ?>
                                    <li><div class="dropdown-divider p-0 m-0"></div></li>
                                    <li>
                                        <?=
                                        Html::beginForm(['/site/logout'], 'post')
                                        . Html::submitButton(
                                                '<i class="fas fa-sign-out-alt"></i>&nbsp;&nbsp;&nbsp;Logout ',
//                                                 '<i class="fas fa-running"></i>&nbsp;&nbsp;&nbsp;"Good Bye" ',
                                                ['class' => 'btn btn-block logout pl-4 text-left']
                                        )
                                        . Html::endForm()
                                        ?>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                <?php } else { ?>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto nav"></ul>
                        <ul class="navbar-nav nav">
                            <!--                            <li class="nav-item">
                                                            <a class="nav-link" href="/site/login">Login <i class="fas fa-sign-in-alt"></i></a>
                                                        </li>-->
                            <?php
                            echo Html::tag('li',
                                    Html::a('Login <i class="fas fa-sign-in-alt"></i>', '/site/login', ['class' => 'pl-4'])
                                    , ['class' => 'pl-0']);
                            ?>
                        </ul>
                    </div>
                    <?php
                }
//                $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
                ?>
            </nav>
            <div class="mainContainer p-0" style='margin-top: 70px'>
                <?php
                Modal::begin([
                    'id' => 'alertModal',
                    'title' => 'Alert..',
//                    'size' => 'modal-xl',
                    'centerVertical' => true
                ]);

                echo "<div id='alertModalContent'></div>";
                Modal::end();
                ?>




                <?php
                Modal::begin([
                    'id' => 'myModal',
                    'size' => 'modal-xl',
                    'centerVertical' => true,
                    'options' => ['style' => 'padding:0px;margin:0px;']
                ]);

                echo "<div id='myModalContent' style='padding:0px;margin:0px;'></div>";
                Modal::end();
                ?>


                <div class="modal fade" tabindex="-1" role="dialog" id="spinnerModal">
                    <div class="modal-dialog modal-dialog-centered text-center" role="document">
                        <span class="fa fa-spinner fa-spin fa-3x w-100"></span>
                    </div>
                </div>


                <?php
                echo Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    'options' => [],
                ])
                ?>

                <?= Alert::widget() ?>


                <div class="col-12">


                    <?= $content ?>

                </div>
            </div>
        </div>
        <br/>



        <footer class="footer">
            <div class="col-12">

                <span class="">&copy; <?= Html::encode(Yii::$app->name) . " " . Yii::$app->params['version'] ?> </span>
                <!--<span class="float-right"><?= Yii::$app->getDb()->dsn ?></span>-->
<!--<p class="pull-right"><?= Yii::powered() ?></p>-->
            </div>
        </footer>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
