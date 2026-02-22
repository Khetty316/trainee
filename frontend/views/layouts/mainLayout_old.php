<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\bootstrap4\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap4\Modal;

AppAsset::register($this);


$acc = 'ACC1';
$proc = 'PROC1';
$admin = 'ADMIN1';
$hr = 'HR1';
$it = 'IT1';
$sysadmin = 'SYSADMIN1';
$isStaff = 'STAFF';
$isProbation = 'PROB';
$directors = 'ENGR11';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>

        <div class="wrap">
            <nav id="w1-navbar" class="fixed-top navbar-expand-lg navbar-dark bg-dark navbar">
                <button type="button" class="navbar-toggler p-0 m-0" data-toggle="collapse" data-target="#w1-collapse" aria-controls="w1-collapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="/" style="align-self: center"><b>NPL E-Office</b></a>

                <?php if (!Yii::$app->user->isGuest) { ?>
                    <div id="w1-collapse" class="collapse navbar-collapse">
                        <ul class="navbar-nav mr-auto nav">
                            <li class="nav-item"><a class="nav-link"></a></li>

                            <?php
                            if (Yii::$app->user->can($isStaff)) { 
                                ?>
                                <li class="nav-item dropdown multi-level-dropdown">
                                    <a class="dropdown-toggle nav-link" href="/working/mi/#" data-toggle="dropdown"><i class='far fa-smile'></i> Personal </a>
                                    <ul class="dropdown-menu bg-dark">
                                        <?php
                                        echo yii\bootstrap4\Html::tag('li',
                                                Html::a('<i class=\'far fa-calendar\'></i> Leave Application', '/office/leave/personal-leave', ['class' => 'nav-link'])
                                                , ['class' => 'dropdown-item  pl-4']);
                                        ?>

                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="nav-item"><a class="nav-link"></a></li>
                            <li class="nav-item dropdown multi-level-dropdown">
                                <a href="#" id="menu" data-toggle="dropdown" class="nav-link dropdown-toggle w-100"><i class='far fa-building'></i> E-Working </a>
                                <ul class="dropdown-menu bg-dark">
                                    <li class="dropdown-item dropdown-submenu">
                                        <a href="#" data-toggle="dropdown" class="nav-link"><i class='far fa-folder-open'></i> Document Incoming </a>
                                        <ul class="dropdown-menu bg-dark">
                                            <?php
                                            echo yii\bootstrap4\Html::tag('li',
                                                    Html::a('<i class="far fa-plus-square"></i> New Incoming Registration', '/working/mi/index', ['class' => 'nav-link'])
                                                    , ['class' => 'dropdown-item  pl-4']);

                                            if (Yii::$app->user->can($isStaff)) {
                                                echo yii\bootstrap4\Html::tag('li',
                                                        Html::a('Requestor Review', '/working/mi/requestorreview', ['class' => 'nav-link'])
                                                        , ['class' => 'dropdown-item pl-4']);
                                            }
                                            if (Yii::$app->user->can($admin)) {
                                                echo yii\bootstrap4\Html::tag('li',
                                                        Html::a('Admin Dept.', '/working/mi/adminactiverecord', ['class' => 'nav-link'])
                                                        , ['class' => 'dropdown-item  pl-4']);
                                            }

                                            if (Yii::$app->user->can($proc)) {
                                                echo yii\bootstrap4\Html::tag('li',
                                                        Html::a('Procurement Dept.', '/working/mi/procurementgrn', ['class' => 'nav-link'])
                                                        , ['class' => 'dropdown-item  pl-4']);
                                            }

                                            if (Yii::$app->user->can($acc)) {

                                                echo yii\bootstrap4\Html::tag('li',
                                                        Html::a('Account Dept.', '/working/mi/accountpay', ['class' => 'nav-link'])
                                                        , ['class' => 'dropdown-item  pl-4']);
                                            }

                                            if (Yii::$app->user->can($directors)) {
                                                echo yii\bootstrap4\Html::tag('li',
                                                        Html::a('Director', '/working/mi/directorreview', ['class' => 'nav-link'])
                                                        , ['class' => 'dropdown-item  pl-4']);
                                            }
                                            ?>
                                        </ul>
                                    </li>

                                    <li class="dropdown-item dropdown-submenu">
                                        <a href="#" data-toggle="dropdown" class="nav-link"> <i class='far fa-calendar'></i> Leave Application </a>
                                        <ul class="dropdown-menu bg-dark">
                                            <?php
                                            echo yii\bootstrap4\Html::tag('li',
                                                    Html::a('Superior Approval', '/working/leavemgmt/superior-leave-approval', ['class' => 'nav-link'])
                                                    , ['class' => 'dropdown-item pl-4']);

                                            if (Yii::$app->user->can($hr)) {
                                                echo yii\bootstrap4\Html::tag('li',
                                                        Html::a('HR', '/working/leavemgmt/hr-leave-approval', ['class' => 'nav-link'])
                                                        , ['class' => 'dropdown-item  pl-4']);
//                                                echo yii\bootstrap4\Html::tag('li',
//                                                        Html::a('HR Leave Summary', '/working/leavemgmt/hr-leave-summary', ['class' => 'nav-link'])
//                                                        , ['class' => 'dropdown-item  pl-4']);
                                            }
                                            if (Yii::$app->user->can($directors)) {
                                                echo yii\bootstrap4\Html::tag('li',
                                                        Html::a('Directors\' Approval', '/working/leavemgmt/director-leave-approval', ['class' => 'nav-link'])
                                                        , ['class' => 'dropdown-item  pl-4']);
                                            }
                                            ?>
                                        </ul>
                                    </li>
                                    <?php
                                    if (false) { // Temporarily Disable first 
                                        if (Yii::$app->user->can($proc)) {
                                            echo yii\bootstrap4\Html::tag('li',
                                                    Html::a('Purchase Order', '/working/po/index', ['class' => 'nav-link'])
                                                    , ['class' => 'dropdown-item pl-4']);
                                        }
                                        ?>
                                        <?php
//                                         if (Yii::$app->user->can($proc)) {
                                        echo yii\bootstrap4\Html::tag('li',
                                                Html::a('Projects', '/working/projects/index', ['class' => 'nav-link'])
                                                , ['class' => 'dropdown-item pl-4']);
//                                            }
                                    }
                                    ?>
                                    <!--                                    <li class="dropdown-item ">
                                                                            <a class="nav-link" href="/working/po/index">Purchase Order</a>
                                                                        </li>-->
                                </ul>
                            </li>
                        </ul>

                        <ul class="navbar-nav">
                            <?php
                            if (Yii::$app->user->can($sysadmin)) {
                                ?>
                                <li class="nav-item dropdown multi-level-dropdown">
                                    <a href="#" id="menu" data-toggle="dropdown" class="nav-link dropdown-toggle w-100"><i class="fas fa-users-cog"></i> System Administrator</a>
                                    <ul class="dropdown-menu bg-dark">
                                        <li class="dropdown-item dropdown-submenu">
                                            <a href="#" data-toggle="dropdown" class="nav-link">User</a>
                                            <ul class="dropdown-menu bg-dark">
                                                <?php
                                                echo yii\bootstrap4\Html::tag('li',
                                                        Html::a('<i class="fas fa-users"></i> Users', '/sysadmin/user/index', ['class' => 'nav-link'])
                                                        , ['class' => 'dropdown-item  pl-4']);
//                                            echo yii\bootstrap4\Html::tag('li',
//                                                    Html::a('RBAC Config', '/auth/rbac', ['class' => 'nav-link'])
//                                                    , ['class' => 'dropdown-item  pl-2']);
                                                ?>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <!--<li>-->
                            <li class="nav-item dropdown multi-level-dropdown">
                                <a class="dropdown-toggle nav-link" href="/working/mi/#" data-toggle="dropdown"><i class="far fa-user-circle"></i> <?= Yii::$app->user->identity->username ?> </a>
                                <ul class="dropdown-menu bg-dark dropdown-menu-right">
                                    <?php
                                    echo yii\bootstrap4\Html::tag('li',
                                            Html::a('User Profile', '/profile/view-profile?id=' . Yii::$app->user->identity->id, ['class' => 'nav-link'])
                                            , ['class' => 'dropdown-item  pl-4']);
                                    ?>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li>
                                        <?=
                                        Html::beginForm(['/site/logout'], 'post')
                                        . Html::submitButton(
                                                ' Logout <i class="fas fa-sign-out-alt"></i>',
                                                ['class' => 'btn btn-link logout']
                                        )
                                        . Html::endForm()
                                        ?>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                    </div>
                <?php } else { ?>
                    <div id="w1-collapse" class="collapse navbar-collapse">
                        <ul class="navbar-nav mr-auto nav"></ul>
                        <ul class="navbar-nav nav">
                            <li class="nav-item">
                                <a class="nav-link" href="/site/login">Login <i class="fas fa-sign-in-alt"></i></a>
                            </li>
                        </ul>
                    </div>
                    <?php
                }
//                $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
                ?>
            </nav>

            <div class="mainContainer">
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
                    'centerVertical' => true
//               'options'=>['class'=>'modal-dialog-centered'] 
                ]);

                echo "<div id='myModalContent'></div>";
                Modal::end();
                ?>

                <br/>
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




        <footer class="footer">
            <div class="col-12">
                <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> v0.1.01</p>
     <!--<p class="pull-right"><?= Yii::powered() ?></p>-->
            </div>
        </footer>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
