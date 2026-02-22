<?php

namespace frontend\models\test;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\test\TestDetailFunctionality;

/**
 * TestDetailFunctionalitySearch represents the model behind the search form of `frontend\models\test\TestDetailFunctionality`.
 */
class TestDetailFunctionalitySearch extends TestDetailFunctionality {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'form_functionality_id', 'success', 'created_by', 'updated_by'], 'integer'],
            [['no', 'feeder_tag', 'created_at', 'updated_at'], 'safe'],
            [['power_terminal_voltage'], 'number'],
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
    public function search($params, $param1, $param2) {
        $query = TestDetailFunctionality::find();

        // add conditions that should always apply here
        switch ($param1) {
            case 'singleFunctionality':
                $query->where(['form_functionality_id' => $param2]);
                break;
            default:
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
            'form_functionality_id' => $this->form_functionality_id,
            'power_terminal_voltage' => $this->power_terminal_voltage,
            'success' => $this->success,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'no', $this->no])
                ->andFilterWhere(['like', 'feeder_tag', $this->feeder_tag]);

        return $dataProvider;
    }

}
