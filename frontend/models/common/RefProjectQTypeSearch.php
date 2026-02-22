<?php

namespace frontend\models\common;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\common\RefProjectQTypes;

/**
 * RefProjectQTypeSearch represents the model behind the search form of `frontend\models\common\RefProjectQTypes`.
 */
class RefProjectQTypeSearch extends RefProjectQTypes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'project_type_name'], 'safe'],
            [['fab_dept_percentage', 'elec_dept_percentage'], 'number'],
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
        $query = RefProjectQTypes::find();

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
            'fab_dept_percentage' => $this->fab_dept_percentage,
            'elec_dept_percentage' => $this->elec_dept_percentage,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'project_type_name', $this->project_type_name]);

        return $dataProvider;
    }
}
