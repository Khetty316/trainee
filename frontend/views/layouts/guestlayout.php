<?php
/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\bootstrap4\Breadcrumbs;
use app\assets\AppAsset;

$GLOBALS['status_waitapprove'] = 1;
$GLOBALS['status_approved'] = 2;
$GLOBALS['status_completed'] = 3;
$GLOBALS['status_paid'] = 4;
//$GLOBALS['project_file_path'] = "uploads/projects/";
//$GLOBALS['invoice_file_path'] = "invoices/";


AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<?= Html::csrfMetaTags() ?>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="manifest" href="../manifest.json">
        <script src="../index.js" ></script>

        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
<!--        <script>
            function showSpinner() {
                document.getElementById("loader").style.display = "";
                document.getElementById("container").style.display = "";
            }

            function hideSpinner() {
                document.getElementById("loader").style.display = "none";
                $("body").find("*").attr("disabled", "");      
                document.getElementById("container").style.display = "block";
            }
        </script>-->
    </head>
    <body>
        <div id="loader" style="display:none"  ></div>
        <section id="container" class="animate-bottom">
        </section>
        <?php $this->beginBody() ?>

        <div class="wrap">

            <nav class="navbar navbar-expand-lg navbar-inverse bg-dark">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03" aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">About</a>
                        </li>
                        <li class="nav-item dropdown dmenu">
                            <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                                Our Service
                            </a>
                            <div class="dropdown-menu sm-menu">
                                <a class="dropdown-item" href="#">service2</a>
                                <a class="dropdown-item" href="#">service 2</a>
                                <a class="dropdown-item" href="#">service 3</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contact Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Call</a>
                        </li>
                        <!-- <li class="nav-item dropdown dmenu">
                         <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                           Dropdown link
                         </a>
                         <div class="dropdown-menu sm-menu">
                           <a class="dropdown-item" href="#">Link 1</a>
                           <a class="dropdown-item" href="#">Link 2</a>
                           <a class="dropdown-item" href="#">Link 3</a>
                           <a class="dropdown-item" href="#">Link 4</a>
                           <a class="dropdown-item" href="#">Link 5</a>
                           <a class="dropdown-item" href="#">Link 6</a>
                         </div>
                       </li> -->
                    </ul>
                    <div class="social-part">
                        <!--
                                                <i class="fa fa-facebook" aria-hidden="true"></i>
                        <i class="fa fa-twitter" aria-hidden="true"></i>
                        <i class="fa fa-instagram" aria-hidden="true"></i>
                        -->



                        <?php
                        echo Yii::$app->user->isGuest ? (
                                Html::a("Login", "/site/login")
                                ) : (
                                '<li>'
                                . Html::beginForm(['/site/logout'], 'post')
                                . Html::submitButton(
                                        'Logout (' . Yii::$app->user->identity->username . ')', ['class' => 'btn btn-link logout']
                                )
                                . Html::endForm()
                                . '</li>'
                                )
                        ?>
                    </div>
                </div>
            </nav>


            <div class="container">
                <?=
                Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ])
                ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="pull-left">&copy; NOSA United Sdn. Bhd. <?= date('Y') ?></p>
                <script>
                    if (!navigator.serviceWorker.controller) {
                        navigator.serviceWorker.register("/sw.js").then(function (reg) {
                            console.log("Service worker has been registered for scope: " + reg.scope);
                        });
                    }
                </script>
                                <!--<p class="pull-right"><?= Yii::powered() ?></p>-->
            </div>
        </footer>

        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>


