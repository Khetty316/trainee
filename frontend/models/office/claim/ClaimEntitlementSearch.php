<?php

namespace frontend\models\office\claim;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\office\claim\ClaimEntitlement;

/**
 * ClaimEntitlementSearch represents the model behind the search form of `frontend\models\office\claim\ClaimEntitlement`.
 */
class ClaimEntitlementSearch extends ClaimEntitlement {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'year', 'status'], 'integer'],
            [['created_at', 'updated_at', 'user_id', 'superior_id', 'created_by', 'updated_by', 'is_active'], 'safe'],
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
        $query = ClaimEntitlement::find();

        // add conditions that should always apply here
        switch ($type) {
            case "pendingHr":
                $query->where("claim_entitlement.status =" . \frontend\models\RefGeneralStatus::STATUS_GetSuperiorApproval)
                        ->andWhere("claim_entitlement.is_active = 1");
                break;
            case "pendingSuperior":
                $query->where("claim_entitlement.status =" . \frontend\models\RefGeneralStatus::STATUS_GetSuperiorApproval)
                        ->andWhere("claim_entitlement.superior_id = " . \Yii::$app->user->id)
                        ->andWhere("claim_entitlement.is_active = 1");
                break;
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user a", "claim_entitlement.user_id = a.id");
        $query->join("LEFT JOIN", "user b", "claim_entitlement.created_by = b.id");
        $query->join("LEFT JOIN", "user c", "claim_entitlement.updated_by = c.id");
        $query->join("LEFT JOIN", "user d", "claim_entitlement.superior_id = d.id");

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//            'user_id' => $this->user_id,
            'year' => $this->year,
//            'superior_id' => $this->superior_id,
            'status' => $this->status,
            'is_active' => $this->is_active,
//            'created_by' => $this->created_by,
//            'claim_entitlement.created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'claim_entitlement.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'a.fullname', $this->user_id])
                ->andFilterWhere(['like', 'b.fullname', $this->created_by])
                ->andFilterWhere(['like', 'c.fullname', $this->updated_by])
                ->andFilterWhere(['like', 'd.fullname', $this->superior_id])
                ->andFilterWhere(['like', 'claim_entitlement.created_at', $this->created_at])
                ->andFilterWhere(['like', 'claim_entitlement.updated_at', $this->updated_at])
        ;

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
        ]);

        return $dataProvider;
    }
}
