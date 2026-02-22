<?php

namespace frontend\models\common;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\common\AuditTrailPageAccess;

/**
 * AuditTrailPageAccessSearch represents the model behind the search form of `frontend\models\common\AuditTrailPageAccess`.
 */
class AuditTrailPageAccessSearch extends AuditTrailPageAccess {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'user_id'], 'integer'],
            [['page', 'created_at'], 'safe'],
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
        $query = AuditTrailPageAccess::find();

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
        ]);

        $query->andFilterWhere(['like', 'page', $this->page]);
        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['created_at' => SORT_DESC]);
        }
        return $dataProvider;
    }

}
