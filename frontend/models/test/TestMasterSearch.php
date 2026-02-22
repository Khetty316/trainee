<?php

namespace frontend\models\test;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\test\TestMaster;

/**
 * TestMasterSearch represents the model behind the search form of `frontend\models\test\TestMaster`.
 */
class TestMasterSearch extends TestMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'test_main_id', 'test_num', 'panel_qty', 'tested_by', 'certified_by', 'created_by', 'updated_by'], 'integer'],
            [['tc_ref', 'date', 'venue', 'detail', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params) {
        $query = TestMaster::find();

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
            'test_main_id' => $this->test_main_id,
            'test_num' => $this->test_num,
            'panel_qty' => $this->panel_qty,
            'date' => $this->date,
            'tested_by' => $this->tested_by,
            'certified_by' => $this->certified_by,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'tc_ref', $this->tc_ref])
                ->andFilterWhere(['like', 'venue', $this->venue])
                ->andFilterWhere(['like', 'detail', $this->detail]);

        return $dataProvider;
    }

}
