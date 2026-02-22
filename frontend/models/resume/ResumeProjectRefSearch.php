<?php

namespace frontend\models\resume;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\resume\ResumeProjectRef;

/**
 * ResumeProjectRefSearch represents the model behind the search form of `frontend\models\resume\ResumeProjectRef`.
 */
class ResumeProjectRefSearch extends ResumeProjectRef {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'user_id', 'sort', 'active_sts', 'created_by', 'updated_by'], 'integer'],
            [['project_detail', 'created_at', 'updated_at'], 'safe'],
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
        $query = ResumeProjectRef::find();

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
            'sort' => $this->sort,
            'active_sts' => $this->active_sts,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'project_detail', $this->project_detail]);

        return $dataProvider;
    }

}
