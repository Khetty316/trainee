<?php

namespace frontend\models\common;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\common\AuditTrailPageVisit;

/**
 * AuditTrailPageVisitSearch represents the model behind the search form of `frontend\models\common\AuditTrailPageVisit`.
 */
class AuditTrailPageVisitSearch extends AuditTrailPageVisit {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['page', 'user_id', 'created_at'], 'safe'],
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
        $query = AuditTrailPageVisit::find();
        $query->join("INNER JOIN", "user", "user.id=user_id");
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
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'page', $this->page])
                ->andFilterWhere(['or', ['like', 'user_id', $this->user_id], ['like', 'user.fullname', $this->user_id]]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['id' => SORT_DESC]);
        }
        return $dataProvider;
    }

}
