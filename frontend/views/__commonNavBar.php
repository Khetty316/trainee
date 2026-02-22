<?php

use yii\bootstrap4\Html;
?>
<div class="d-none d-lg-block pb-2">
    <ul class='nav nav-tabs'>
        <?php
        foreach ($linkList as $key => $link) {

//            $active = ($title == $link['name'] ? ' active' : '');
            if ($title == $link['name']) {
                echo Html::tag('li',
                        Html::a($link['name'], $link['link'], ['class' => 'nav-link active font-weight-bold'])
                        , ['class' => 'nav-item']);
            } else {
                echo Html::tag('li',
                        Html::a($link['name'], $link['link'], ['class' => 'nav-link'])
                        , ['class' => 'nav-item']);
            }
        }
        ?>
    </ul>
</div>
<div class="d-lg-none">
    <nav>
        <div class="navbar-header">
            <button type="button" class="navbar-toggle border-0" data-toggle="collapse" data-target="#nav-sub"  aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="nav-sub">
            <div class="list-group">
                <?php
                foreach ($linkList as $key => $link) {
                    $active = ($title == $link['name'] ? ' active' : '');
                    echo Html::a($link['name'], $link['link'], ['class' => 'list-group-item list-group-item-action' . $active]);
                }
                ?>
            </div>
        </div>
    </nav>
    <h3><?= Html::encode($title) ?></h3>
</div>