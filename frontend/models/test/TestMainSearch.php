<?php

namespace frontend\models\test;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\test\TestMain;

/**
 * TestMainSearch represents the model behind the search form of `frontend\models\test\TestMain`.
 */
class TestMainSearch extends TestMain
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'panel_id', 'test_type', 'doc_ref', 'rev_no', 'status', 'created_by', 'updated_by'], 'integer'],
            [['client', 'elec_consultant', 'elec_contractor', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params)
    {
        $query = TestMain::find();

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
            'id' => $this->id,
            'panel_id' => $this->panel_id,
            'test_type' => $this->test_type,
            'doc_ref' => $this->doc_ref,
            'rev_no' => $this->rev_no,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'client', $this->client])
            ->andFilterWhere(['like', 'elec_consultant', $this->elec_consultant])
            ->andFilterWhere(['like', 'elec_contractor', $this->elec_contractor]);

        return $dataProvider;
    }
}
