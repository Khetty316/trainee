<?php

namespace frontend\controllers;

use frontend\models\working\project\MasterProjects;
use common\models\User;
use yii\helpers;
use Yii;

class ListController extends \yii\web\Controller {

    /**
     * GET FOR AUTOCOMPLETE
     * @param type $term
     * @return type JSON
     */
    public function actionGetprojectlist($term = '') {
        $data = MasterProjects::find()
                ->select(['project_code as value', 'project_code as id', 'CONCAT(project_code," - ",project_name) as label, person_in_charge as pic, fullname as pic_fullname'])
                ->leftJoin('user', 'user.id = master_projects.person_in_charge')
                ->where('project_code like "%' . addslashes($term) . '%" OR project_name like "%' . addslashes($term) . '%" ')
                ->asArray()
                ->all();
        return helpers\Json::encode($data);
    }

    public function actionGetRefAreaList($term = '') {
        $data = \frontend\models\common\RefArea::find()
                ->select(['area_name as label', 'area_name as value', 'area_id as id', 'ref_area.state_id as stateId', 'ref_state.state_name as stateName'])
                ->leftJoin('ref_state', 'ref_state.state_id = ref_area.state_id')
                ->where('area_name like "%' . addslashes($term) . '%"')
                ->orderBy(['area_name' => SORT_ASC])
                ->asArray()
                ->all();

        return helpers\Json::encode($data);
    }

    public function actionGetRefStateList($term = '') {
        $data = \frontend\models\common\RefState::find()
                ->select(['state_name as label', 'state_name as value', 'state_id as id'])
                ->where('state_name like "%' . addslashes($term) . '%"')
                ->orderBy(['state_name' => SORT_ASC])
                ->asArray()
                ->all();

        return helpers\Json::encode($data);
    }

    public function actionGetRefCountryList($term = '') {
        $data = \frontend\models\common\RefCountries::find()
                ->select(['country_name as label', 'country_name as value', 'country_code as id'])
                ->where('country_name like "%' . addslashes($term) . '%"')
                ->orderBy(['country_name' => SORT_ASC])
                ->asArray()
                ->all();

        return helpers\Json::encode($data);
    }

    public function actionGetUserList($term = '') {
        $data = User::find()
                ->select(['fullname as value', 'id as id', 'CONCAT(fullname) as label'])
                ->where('fullname like "%' . addslashes($term) . '%" ')
                ->asArray()
                ->all();
        return helpers\Json::encode($data);
    }

    public function actionGetClaimDetailList($term = '') {
        $data = \frontend\models\working\claim\ClaimsDetail::find()
                ->select(['claims_detail_sub.detail', 'claims_detail_sub.detail as id', 'claims_detail_sub.detail as label'])
                ->join("INNER JOIN", "claims_detail_sub", "claims_detail_sub.claims_detail_id=claims_detail.claims_detail_id")
                ->where("claimant_id = " . Yii::$app->user->identity->id . " AND claims_detail_sub.detail LIKE '%" . addslashes($term) . "%'")
                ->distinct()
                ->orderBy(['detail' => SORT_ASC])
                ->asArray()
                ->all();
        return helpers\Json::encode($data);
    }

    public function actionGetRefAreaDetailById($term = '') {
        $data = \frontend\models\common\RefArea::find()
                ->where('area_id=' . $term)
                ->asArray()
                ->all();
        return helpers\Json::encode($data);
    }

}
