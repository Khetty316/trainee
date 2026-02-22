<?php

namespace frontend\models\office\prodOtMealRecord;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster;

/**
 * ProdOtMealRecordMasterSearch represents the model behind the search form of `frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster`.
 */
class ProdOtMealRecordMasterSearch extends ProdOtMealRecordMaster
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'month', 'year', 'status', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['ref_code', 'dateFrom', 'dateTo', 'created_at', 'updated_at', 'deleted_at', 'total_amount'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
    public function search($params, $type = "")
    {
        $query = ProdOtMealRecordMaster::find();

        // add conditions that should always apply here
        switch ($type) {
            case "personal":
                $query->where(['created_by' => \Yii::$app->user->identity->id]);
                break;
            
            default:
                // Handle unknown type or no filter
//                $query->andWhere(['claim_master.is_deleted' => 0]);
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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'month' => $this->month,
            'year' => $this->year,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'status' => $this->status,
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
            'deleted_by' => $this->deleted_by,
            'deleted_at' => $this->deleted_at,
        ]);

        $query->andFilterWhere(['like', 'ref_code', $this->ref_code]);
        $query->andFilterWhere(['like', 'created_at', $this->created_at]);
        $query->andFilterWhere(['like', 'updated_at', $this->updated_at]);
        $query->andFilterWhere(['like', 'total_amount', $this->total_amount]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
        ]);
        
        return $dataProvider;
    }
}
