<?php

namespace frontend\models\working\mi;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\mi\VMasterIncomings;
use \common\models\myTools\MyFormatter;

/**
 * MasterIncomingsSearch represents the model behind the search form of `app\models\working\MasterIncomings`.
 */
class MasterIncomingsSearch extends VMasterIncomings {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'doc_type_id', 'sub_doc_type_id', 'isUrgent', 'isPerforma', 'file_type_id', 'requestor_id', 'current_step', 'current_step_task_id', 'mi_status'], 'integer'],
            [['index_no', 'doc_due_date', 'grn_no', 'po_id', 'po_number', 'reference_no', 'particular', 'received_from', 'remarks', 'filename', 'project_code', 'created_at', 'updated_at', 'uploader_id',
            'uploader_fullname', 'uploader_username', 'doc_type_name', 'sub_doc_type_name', 'file_type_name', 'project_name', 'project_description', 'requestor_username', 'requestor_fullname', 'task_name', 'status', 'task_description'], 'safe'],
            [['amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $type) {
        /* query result
          task_id	task_name                   task_description
          1         director_approval           Waiting for directors' approval
          2         requestor_approval          Waiting for requestor's approval

          6         account_receivedoc          Waiting for account to receive doc
          3         account_payment             Waiting for account's payment

          4         procurement_approval        Waiting for procurements approval
          10        procurement_receivedoc      Waiting for procurement to receive doc

          5         admin_senddoc_acc           Waiting for admin to send doc to account
          8         admin_approval              Waiting for admin's approval  // Temporarily not yet apply
          9         admin_senddoc_proc          Waiting for admin to send doc to procurement
          11        admin_keepdoc               Admin to keep the doc

         *           12        force_close                 Force Closed by admin
          7         done                        Done

         * 
         */

        $directorReview_TaskId = array("1");
        $requestorReview_TaskId = array("2");

        $procGRN_TaskId = array("4");
        $procRecDoc_TaskId = array("10");

        $accountPay_TaskId = array("3");
        $accountReceiveDoc_TaskId = array("6");

        $adminSendDocAcc_TaskId = array("5");
        $adminSendDocProc_TaskId = array("9");
        $adminKeepDoc_TaskId = array("11");

        $query = VMasterIncomings::find();


        switch ($type) {
            case "directorReview": // 1         director_approval           Waiting for directors' approval
                $query->where(" current_step_task_id IN ('" . implode(",", $directorReview_TaskId) . "')");
                break;
            case "requestorReview": // 2         requestor_approval          Waiting for requestor's approval
                $query->innerJoin('mi_projects', 'mi_projects.mi_id=v_master_incomings.id')
                        ->where(" current_step_task_id IN ('" . implode(",", $requestorReview_TaskId) . "')")
                        ->andWhere(" mi_projects.requestor= " . Yii::$app->user->identity->id)
                        ->andWhere(" mi_projects.requestor_approval IS NULL");
                break;
            case "requestorReviewHistory": // 2         requestor_approval          Waiting for requestor's approval
                $query->innerJoin('mi_projects', 'mi_projects.mi_id=v_master_incomings.id')
                        ->where(" mi_projects.requestor= " . Yii::$app->user->identity->id);
                break;
            case "general":
                break;

            case "procurementGrn": // 4         procurement_approval        Waiting for procurements approval
                $query->where(" current_step_task_id IN ('" . implode(",", $procGRN_TaskId) . "')");
                break;
            case "procurementReceiveDoc": // 10        procurement_receivedoc      Waiting for procurement to receive doc
                $query->where(" current_step_task_id IN ('" . implode(",", $procRecDoc_TaskId) . "')");
                break;
            case "procurementGrnEdit": // 10        procurement_receivedoc      Waiting for procurement to receive doc
                $query->where("grn_no IS NOT NULL");
                break;
            case "accountPay": // 3         account_payment             Waiting for account's payment
                $query->where(" current_step_task_id IN ('" . implode(",", $accountPay_TaskId) . "')");
                break;
            case "accountReceiveDoc": // 6         account_receivedoc          Waiting for account to receive doc
                $query->where(" current_step_task_id IN ('" . implode(",", $accountReceiveDoc_TaskId) . "')");
                break;

            case "adminSendDocAcc": // 5         admin_senddoc_acc           Waiting for admin to send doc to account
                $query->where(" current_step_task_id IN ('" . implode(",", $adminSendDocAcc_TaskId) . "')");
                break;
            case "adminSendDocProc": // 9         admin_senddoc_proc          Waiting for admin to send doc to procurement
                $query->where(" current_step_task_id IN ('" . implode(",", $adminSendDocProc_TaskId) . "')");
                break;
            case "adminKeepDoc": // 11        admin_keepdoc               Admin to keep the doc
                $query->where(" current_step_task_id IN ('" . implode(",", $adminKeepDoc_TaskId) . "')");
                break;

            case "adminActiveRecord":
                $query->where(" mi_status = 1");
                break;
            case "directorAcknowledge":
                $query->where(" acknowledge_sts = 1");
                break;
            case "requestorAcknowledge":
                $query->join('join', 'mi_projects', 'v_master_incomings.id = mi_projects.mi_id')
                        ->where(" acknowledge_req_sts = 1 AND mi_projects.requestor = " . Yii::$app->user->identity->id . ' AND (IFNULL(requestor_approval, 0)!=1)');
                break;
            case "superMiInvoice":
                $query->where(" doc_type_id IN (2,3,4) ");
                break;
            case "superMiAll":
//                $query->where(" doc_type_id IN (2,3,4) ");
                break;
        }



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if ($this->project_code != "") {
            $query->join('INNER JOIN', 'mi_projects as mp3', 'mp3.mi_id = v_master_incomings.id AND mp3.project_code like "%' . $this->project_code . '%"');
        }
        if ($this->amount != "") {
            $query->join('INNER JOIN', 'mi_projects as mp1', 'mp1.mi_id = v_master_incomings.id AND mp1.amount like "%' . $this->amount . '%"');
        }
        if ($this->requestor_fullname != "") {
            $query->join('INNER JOIN', 'mi_projects as mp2', 'mp2.mi_id = v_master_incomings.id ')
                    ->join('INNER JOIN', 'user', 'user.id = mp2.requestor  AND user.fullname like "%' . $this->requestor_fullname . '%"');
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//            'amount' => $this->amount,
            'isUrgent' => $this->isUrgent,
            'isPerforma' => $this->isPerforma,
            'current_step' => $this->current_step,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
            'mi_status' => $this->mi_status
        ]);

        $query->andFilterWhere(['like', 'index_no', $this->index_no])
                ->andFilterWhere(['like', 'uploader_fullname', $this->uploader_fullname])
                ->andFilterWhere(['=', 'doc_type_id', $this->doc_type_name])
                ->andFilterWhere(['=', 'sub_doc_type_id', $this->sub_doc_type_name])
                ->andFilterWhere(['=', 'file_type_id', $this->file_type_name])
                ->andFilterWhere(['=', 'current_step_task_id', $this->task_description])
                ->andFilterWhere(['=', 'doc_due_date', $this->doc_due_date == "" ? "" : MyFormatter::changeDateFormat_readToDB($this->doc_due_date)])
                ->andFilterWhere(['like', 'project_name', $this->project_name])
//                ->andFilterWhere(['like', 'requestor_fullname', $this->requestor_fullname])
                ->andFilterWhere(['like', 'task_name', $this->task_name])
                ->andFilterWhere(['like', 'reference_no', $this->reference_no])
                ->andFilterWhere(['like', 'particular', $this->particular])
                ->andFilterWhere(['like', 'received_from', $this->received_from])
                ->andFilterWhere(['like', 'remarks', $this->remarks])
                ->andFilterWhere(['like', 'DATE_FORMAT(v_master_incomings.created_at, "%d/%m/%Y %H:%i")', $this->created_at])
                ->andFilterWhere(['or',
                    ['like', 'requestor.full_name', $this->uploader_id]]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['id' => SORT_DESC]);
        }

        /*

          ->andFilterWhere(['or',
          ['like', 'customer.customer_name', $this->customer_id],
          ['like', 'project_code', $this->customer_id],
          ['like', 'DATE_FORMAT(project.created_date,"%d/%m/%Y")', $this->customer_id],
          ['like', 'title', $this->customer_id]]);

         */

        return $dataProvider;
    }

}
