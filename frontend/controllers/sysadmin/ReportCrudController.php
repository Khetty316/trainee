<?php

namespace frontend\controllers\sysadmin;

use frontend\models\common\RefProjectQTypes;
use frontend\models\common\RefProjectQTypeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReportCrudController implements the CRUD actions for RefProjectQTypes model.
 */
class ReportCrudController extends Controller {

    CONST PROJ_TYPE = "/sysadmin/reportcrud/refprojectqtype/";

    /**
     * @inheritDoc
     */
    public function behaviors() {
        return array_merge(
                parent::behaviors(),
                [
                    'verbs' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'delete' => ['POST'],
                        ],
                    ],
                ]
        );
    }

    /**
     * Lists all RefProjectQTypes models.
     *
     * @return string
     */
    public function actionIndex() {
        $searchModel = new RefProjectQTypeSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render(SELF::PROJ_TYPE . 'index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RefProjectQTypes model.
     * @param string $code Code
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($code) {
        return $this->render(SELF::PROJ_TYPE . 'view', [
                    'model' => $this->RefProjectQTypes::findOne($code),
        ]);
    }

    /**
     * Creates a new RefProjectQTypes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate() {
        $model = new RefProjectQTypes();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'code' => $model->code]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render(SELF::PROJ_TYPE . 'create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing RefProjectQTypes model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $code Code
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($code) {
        $model = $this->RefProjectQTypes::findOne($code);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'code' => $model->code]);
        }

        return $this->render(SELF::PROJ_TYPE . 'update', [
                    'model' => $model,
        ]);
    }

}
