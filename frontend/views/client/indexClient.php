<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\client\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="clients-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php if (Yii::$app->session->get('success_message')): ?>
        <div class="alert alert-success alert-dismissible fade show"
             style="padding:20px; font-size:18px; margin-bottom:20px;">

            <?= Yii::$app->session->get('success_message') ?>

            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>

        <?php Yii::$app->session->remove('success_message'); ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('downloadNotFoundClient')): ?>
        <script>
            setTimeout(function () {

                // remove loading spinner (optional)
                document.body.classList.remove('loading');
                document.querySelectorAll('.loading, .overlay, .spinner').forEach(el => {
                    el.style.display = 'none';
                });

                // trigger download
                var iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = '<?= \yii\helpers\Url::to(['client/export-not-found-clients']) ?>';
                document.body.appendChild(iframe);

            }, 1000);
        </script>
    <?php endif; ?>

    <p>
        <?= Html::a('Create Clients', ['create-client'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Import Outstanding Balance', ['add-by-template-clients'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
    </p>

    <div class="table-responsive">
        <?=
        GridView::widget([
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'tableOptions' => ['class' => 'table table-striped table-bordered table-sm'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'client_code',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a($model->client_code, ['view-client', 'id' => $model->id]);
                    }
                ],
                'company_name',
                'company_registration_no',
                'company_tin',
                'payment_term',
                [
                    'attribute' => 'tk_balance',
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function ($model) {
                        return number_format($model->tk_balance, 2);
                    }
                ],
                [
                    'attribute' => 'tke_balance',
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function ($model) {
                        return number_format($model->tke_balance, 2);
                    }
                ],
                [
                    'attribute' => 'tkm_balance',
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function ($model) {
                        return number_format($model->tkm_balance, 2);
                    }
                ],
                [
                    'attribute' => 'current_outstanding_balance',
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function ($model) {
                        return number_format($model->current_outstanding_balance, 2);
                    }
                ],
                [
                    'attribute' => 'contact_person',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $contacts = $model->clientContacts;
                        if (!$contacts)
                            return '<span class="text-muted">(no name)</span>';

                        $nameList = [];
                        foreach ($contacts as $contact) {
                            if (!empty($contact->name))
                                $nameList[] = htmlspecialchars($contact->name);
                            else
                                $nameList[] = '<span class="text-muted">(no name)</span>';
                        }
                        return implode('<br>', $nameList);
                    },
                ],
                [
                    'attribute' => 'contact_position',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $contacts = $model->clientContacts;
                        if (!$contacts)
                            return '<span class="text-muted">(no position)</span>';

                        $positionList = [];
                        foreach ($contacts as $contact) {
                            if (!empty($contact->position))
                                $positionList[] = htmlspecialchars($contact->position);
                            else
                                $positionList[] = '<span class="text-muted">(no position)</span>';
                        }
                        return implode('<br>', $positionList);
                    }
                ],
                [
                    'attribute' => 'contact_number',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $contacts = $model->clientContacts;
                        if (!$contacts)
                            return '<span class="text-muted">(no contact number)</span>';

                        $numList = [];
                        foreach ($contacts as $contact) {
                            if (!empty($contact->contact_number))
                                $numList[] = htmlspecialchars($contact->contact_number);
                            else
                                $numList[] = '<span class="text-muted">(no contact number)</span>';
                        }
                        return implode('<br>', $numList);
                    }
                ],
                [
                    'attribute' => 'contact_email',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $contacts = $model->clientContacts;
                        if (!$contacts)
                            return '<span class="text-muted">(no email)</span>';

                        $emailList = [];
                        foreach ($contacts as $contact) {
                            if (!empty($contact->email_address))
                                $emailList[] = htmlspecialchars($contact->email_address);
                            else
                                $emailList[] = '<span class="text-muted">(no email)</span>';
                        }
                        return implode('<br>', $emailList);
                    },
                ],
                'postcode',
                [
                    'attribute' => 'area',
                    'value' => function ($model) {
                        return $model['area0']['area_name'] ?? null;
                    }
                ],
                [
                    'attribute' => 'state',
                    'value' => function ($model) {
                        return $model['state0']['state_name'] ?? null;
                    }
                ],
                [
                    'attribute' => 'country',
                    'value' => function ($model) {
                        return $model['country0']['country_name'] ?? null;
                    }
                ],
            //'email:email',
            //'address_1',
            //'address_2',
            //'postcode',
            //'area',
            //'state',
            //'country',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            ],
        ]);
        ?>
    </div>

</div>

