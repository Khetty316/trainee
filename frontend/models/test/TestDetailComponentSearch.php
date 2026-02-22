<?php

namespace frontend\models\test;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\test\TestDetailComponent;

/**
 * TestDetailComponentSearch represents the model behind the search form of `frontend\models\test\TestDetailComponent`.
 */
class TestDetailComponentSearch extends TestDetailComponent {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'form_component_id', 'created_by', 'updated_by'], 'integer'],
            [['non_conform', 'remark', 'created_at', 'updated_at'], 'safe'],
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
        $query = TestDetailComponent::find();

        // add conditions that should always apply here
        switch ($params) {
            case 'singleComponent':
                $query->where(['form_component_id' => $param1]);
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
            'form_component_id' => $this->form_component_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'non_conform', $this->non_conform])
                ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }

}
