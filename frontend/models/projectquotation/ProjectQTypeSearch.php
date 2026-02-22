<?php

namespace frontend\models\projectquotation;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\projectquotation\ProjectQTypes;

/**
 * ProjectQTypeSearch represents the model behind the search form of `frontend\models\projectquotation\ProjectQTypes`.
 */
class ProjectQTypeSearch extends ProjectQTypes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'is_finalized', 'created_by'], 'integer'],
            [['type', 'remark', 'created_at'], 'safe'],
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
        $query = ProjectQTypes::find();

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
            'project_id' => $this->project_id,
            'is_finalized' => $this->is_finalized,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
