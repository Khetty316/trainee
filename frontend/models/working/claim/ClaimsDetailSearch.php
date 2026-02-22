<?php

namespace frontend\models\working\claim;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\claim\ClaimsDetail;
use Yii;

/**
 * ClaimsDetailSearch represents the model behind the search form of `frontend\models\working\claim\ClaimsDetail`.
 */
class ClaimsDetailSearch extends ClaimsDetail {
    public $claims_id;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['claims_detail_id', 'claimant_id', 'claim_master_id', 'receipt_lost', 'is_submitted', 'is_deleted', 'update_by'], 'integer'],
            [['claim_type', 'date1', 'date2', 'company_name', 'receipt_no', 'detail', 'project_account', 'filename', 'created_at', 'update_at', 'claims_id'], 'safe'],
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
    public function search($params, $type = "", $claimType = "") {
        $query = ClaimsDetail::find();
        switch ($type) {
            case "personalClaim":
                if ($claimType != "") {
                    $query->join('INNER JOIN', 'ref_claim_type', ' ref_claim_type.code = claims_detail.claim_type AND ref_claim_type.claim_family ="' . $claimType . '"');
                }
                $query->where("claimant_id = " . Yii::$app->user->identity->id)->andWhere('is_deleted = 0 AND is_submitted=0');
                $query->andWhere("(((YEAR(NOW()) - YEAR(date1))*12 + (MONTH(NOW()) - MONTH(date1)))<2 OR special_approved=2)");
                break;

            case "outdated":
                $query->where("claimant_id = " . Yii::$app->user->identity->id)->andWhere('is_deleted = 0 AND is_submitted=0');
                $query->andWhere("((YEAR(NOW()) - YEAR(date1))*12 + (MONTH(NOW()) - MONTH(date1)))>=2 AND special_approved IN (0,1)");
                break;

            case "rejected":
                $query->where("claimant_id = " . Yii::$app->user->identity->id)->andWhere('is_deleted = 0 AND is_submitted=0');
                $query->andWhere("((YEAR(NOW()) - YEAR(date1))*12 + (MONTH(NOW()) - MONTH(date1)))>=2 AND special_approved IN (3)");
                break;
            case "AuthorizeClaim":
                $query->where("is_submitted=1 AND authorize_status=1 AND is_deleted=0 AND authorized_by=" . Yii::$app->user->identity->id)
                        ->andWhere("claim_master_id=" . $claimType);
                break;
            case "hrPayTravelClaim":
                $query->where("claim_master_id=" . $claimType);
                break;

            case "procClaimAssignGRN":
                $query->where('claim_master_id=' . $claimType . ' AND claim_type IN ("mat","pet")'); // Material & Petty Case, betwewen authorized & doc receive
                break;

            case "directorSpecialApproval":
                $query->where('special_approved=1 && is_deleted=0'); // Material & Petty Case, betwewen authorized & doc receive
                break;

            case "AuthorizeClaimCount":
                $query->where("is_submitted=1 AND authorize_status=1 AND is_deleted=0 AND authorized_by=" . Yii::$app->user->identity->id);
                break;

            case "pendingPersonalClaimCount":
                $query->where("claimant_id = " . Yii::$app->user->identity->id)->andWhere('is_deleted = 0 AND is_submitted=0');
                break;

            case "accountClaimMedical":
                $query->join('INNER JOIN', 'claims_master', 'claims_detail.claim_master_id = claims_master.claims_master_id')
                        ->where("claims_detail.claim_type = 'med'")
                        ->andWhere('claims_master.claims_status in (2,3,4,5)');
                break;

            case "general":
                break;
        }

        $query->orderBy(['claims_detail_id' => SORT_DESC]);
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
            'claims_detail_id' => $this->claims_detail_id,
            'claimant_id' => $this->claimant_id,
            'claim_master_id' => $this->claim_master_id,
            'date1' => $this->date1,
            'date2' => $this->date2,
            'amount' => $this->amount,
            'receipt_lost' => $this->receipt_lost,
            'is_submitted' => $this->is_submitted,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'update_at' => $this->update_at,
            'update_by' => $this->update_by,
        ]);

        $query->andFilterWhere(['like', 'claim_type', $this->claim_type])
                ->andFilterWhere(['like', 'company_name', $this->company_name])
                ->andFilterWhere(['like', 'receipt_no', $this->receipt_no])
                ->andFilterWhere(['like', 'detail', $this->detail])
                ->andFilterWhere(['like', 'project_account', $this->project_account])
                ->andFilterWhere(['like', 'filename', $this->filename]);

        return $dataProvider;
    }

}
