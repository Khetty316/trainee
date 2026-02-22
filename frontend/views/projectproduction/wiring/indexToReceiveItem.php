<?php ?>
<?= $this->render('__navBarWiringItem', ['pageKey' => '1']) ?>

<div class="row">
    <div class="col-12">
        <?php
        echo $this->render("_gridViewProcDispatchList", [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
        ?>
    </div>
</div>
