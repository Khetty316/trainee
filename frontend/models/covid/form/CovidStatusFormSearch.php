<?php

namespace frontend\models\covid\form;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\covid\form\CovidStatusForm;

/**
 * CovidStatusFormSearch represents the model behind the search form of `frontend\models\covid\form\CovidStatusForm`.
 */
class CovidStatusFormSearch extends CovidStatusForm {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'user_id', 'self_vaccine_dose', 'self_test_is', 'to_take_action', 'self_covid_kit_id', 'other_how_many', 'other_vaccine_two_dose', 'other_test_is'], 'integer'],
            [['created_at', 'self_symptom_list', 'self_symptom_other', 'self_place_list', 'self_place_other', 'self_test_date', 'self_test_reason', 'self_test_kit_type', 'other_symptom_list', 'other_symptom_other', 'other_place_list', 'other_place_other', 'other_test_reason'], 'safe'],
            [['body_temperature'], 'number'],
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
        $query = CovidStatusForm::find();

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
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'body_temperature' => $this->body_temperature,
            'self_vaccine_dose' => $this->self_vaccine_dose,
            'self_test_is' => $this->self_test_is,
            'self_test_date' => $this->self_test_date,
            'to_take_action' => $this->to_take_action,
            'self_covid_kit_id' => $this->self_covid_kit_id,
            'other_how_many' => $this->other_how_many,
            'other_vaccine_two_dose' => $this->other_vaccine_two_dose,
            'other_test_is' => $this->other_test_is,
        ]);

        $query->andFilterWhere(['like', 'self_symptom_list', $this->self_symptom_list])
                ->andFilterWhere(['like', 'self_symptom_other', $this->self_symptom_other])
                ->andFilterWhere(['like', 'self_place_list', $this->self_place_list])
                ->andFilterWhere(['like', 'self_place_other', $this->self_place_other])
                ->andFilterWhere(['like', 'self_test_reason', $this->self_test_reason])
                ->andFilterWhere(['like', 'self_test_kit_type', $this->self_test_kit_type])
                ->andFilterWhere(['like', 'other_symptom_list', $this->other_symptom_list])
                ->andFilterWhere(['like', 'other_symptom_other', $this->other_symptom_other])
                ->andFilterWhere(['like', 'other_place_list', $this->other_place_list])
                ->andFilterWhere(['like', 'other_place_other', $this->other_place_other])
                ->andFilterWhere(['like', 'other_test_reason', $this->other_test_reason]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['created_at' => SORT_DESC]);
        }
        return $dataProvider;
    }

}
