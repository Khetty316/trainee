<?php

namespace frontend\models\projectquotation;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\projectquotation\ProjectQPanels;

/**
 * ProjectQPanelSearch represents the model behind the search form of `frontend\models\projectquotation\ProjectQPanels`.
 */
class ProjectQPanelSearch extends ProjectQPanels
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'revision_id', 'sort', 'created_by', 'updated_by'], 'integer'],
            [['panel_description', 'remark', 'created_at', 'updated_at'], 'safe'],
            [['amount'], 'number'],
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
        $query = ProjectQPanels::find();

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
            'revision_id' => $this->revision_id,
            'amount' => $this->amount,
            'sort' => $this->sort,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'panel_description', $this->panel_description])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
