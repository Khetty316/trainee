<?php

namespace frontend\models\quotation;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\quotation\QuotationMasters;
use Yii;

/**
 * QuotationMasterSearch represents the model behind the search form of `frontend\models\quotation\QuotationMasters`.
 */
class QuotationMasterSearch extends QuotationMasters {

    public $overallStatus;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'requestor_id', 'proc_approval', 'proc_approve_by', 'requestor_approval', 'requestor_approve_by', 'manager_approval', 'manager_approve_by', 'created_by', 'updated_by'], 'integer'],
            [['project_code', 'description', 'proc_remark', 'requestor_remark', 'manager_remark', 'created_at', 'updated_at', 'overallStatus','file_reference'], 'safe'],
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
    public function search($params, $type = "") {
        $query = QuotationMasters::find();

        switch ($type) {
            case "staff_personal_pending": // 1         director_approval           Waiting for directors' approval
                $query->where(["requestor_id" => Yii::$app->user->id, 'request_is_complete' => 0]);
                break;
            case "staff_personal_all": // 1         director_approval           Waiting for directors' approval
                $query->where(["requestor_id" => Yii::$app->user->id]);
                break;
            case "staff_personal_awaiting": // 1         director_approval           Waiting for directors' approval
                $query->where(["requestor_id" => Yii::$app->user->id, 'request_is_complete' => 0, 'proc_approval' => 1, 'requestor_approval' => 0]);
                break;
            case "procurement_pending": // 1         director_approval           Waiting for directors' approval
                $query->where(["proc_approval" => 0]);
                break;
            case "procurement_po": // 1         director_approval           Waiting for directors' approval
                $query->where(["manager_approval" => 1, 'request_is_complete' => 0]);
                break;
            case "procurement_all": // 1         director_approval           Waiting for directors' approval
                break;
            case "manager_pending": // 1         director_approval           Waiting for directors' approval
                $query->where("proc_approval = 1 AND requestor_approval = 1 AND manager_approval = 0")
                        ->andWhere(['request_is_complete' => 0]);
                break;
        }


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
//            'id' => $this->id,
            'requestor_id' => $this->requestor_id,
            'proc_approval' => $this->proc_approval,
            'proc_approve_by' => $this->proc_approve_by,
            'requestor_approval' => $this->requestor_approval,
            'requestor_approve_by' => $this->requestor_approve_by,
            'manager_approval' => $this->manager_approval,
            'manager_approve_by' => $this->manager_approve_by,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'project_code', $this->project_code])
                ->andFilterWhere(['like', 'id', $this->id])
                ->andFilterWhere(['like', 'file_reference', $this->file_reference])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'proc_remark', $this->proc_remark])
                ->andFilterWhere(['like', 'requestor_remark', $this->requestor_remark])
                ->andFilterWhere(['like', 'manager_remark', $this->manager_remark]);

        if ($this->overallStatus) {
            switch ($this->overallStatus) {
                case self::STS_PROC:
                    $query->andFilterWhere(['proc_approval' => 0]);
                    break;
                case self::STS_PROC_REJ:
                    $query->andFilterWhere(['proc_approval' => 2]);
                    break;
                case self::STS_REQ:
                    $query->andFilterWhere(['proc_approval' => 1, 'requestor_approval' => 0]);
                    break;
                case self::STS_REQ_REJ:
                    $query->andFilterWhere(['requestor_approval' => 2]);
                    break;
                case self::STS_MGR:
                    $query->andFilterWhere(['requestor_approval' => 1, 'manager_approval' => 0]);
                    break;

                case self::STS_MGR_REJ:
                    $query->andFilterWhere(['manager_approval' => 2]);
                    break;
                case self::STS_WAIT_PO:
                    $query->andFilterWhere(['manager_approval' => 1, 'request_is_complete' => 0]);
                    break;
                case self::STS_PO_DONE:
                    $query->andFilterWhere(['manager_approval' => 1, 'request_is_complete' => 1]);
                    break;
            }
//                
        }


        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['id' => SORT_DESC]);
        }

        return $dataProvider;
    }

}
