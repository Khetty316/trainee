<?php

namespace frontend\models\projectquotation;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\projectquotation\ProjectQRevisions;

/**
 * ProjectQRevisionSearch represents the model behind the search form of `frontend\models\projectquotation\ProjectQRevisions`.
 */
class ProjectQRevisionSearch extends ProjectQRevisions
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'project_q_type_id', 'incharged_by', 'is_active', 'is_finalized', 'finalized_by', 'created_by', 'updated_by'], 'integer'],
            [['revision_description', 'remark', 'created_at', 'updated_at'], 'safe'],
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
        $query = ProjectQRevisions::find();

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
            'project_q_type_id' => $this->project_q_type_id,
            'amount' => $this->amount,
            'incharged_by' => $this->incharged_by,
            'is_active' => $this->is_active,
            'is_finalized' => $this->is_finalized,
            'finalized_by' => $this->finalized_by,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'revision_description', $this->revision_description])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
