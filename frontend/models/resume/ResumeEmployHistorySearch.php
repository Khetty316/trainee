<?php

namespace frontend\models\resume;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\resume\ResumeEmployHistory;

/**
 * ResumeEmployHistorySearch represents the model behind the search form of `frontend\models\resume\ResumeEmployHistory`.
 */
class ResumeEmployHistorySearch extends ResumeEmployHistory {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'user_id', 'active_sts', 'created_by', 'updated_by', 'sort'], 'integer'],
            [['employ_role', 'employ_company', 'employ_period', 'employ_detail', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params, $type = '') {


        $query = ResumeEmployHistory::find();

        // add conditions that should always apply here
        switch ($type) {
            case 'personal':
                $query->where('user_id=' . Yii::$app->user->id);
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
            'user_id' => $this->user_id,
            'active_sts' => $this->active_sts,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'employ_role', $this->employ_role])
                ->andFilterWhere(['like', 'employ_company', $this->employ_company])
                ->andFilterWhere(['like', 'employ_period', $this->employ_period])
                ->andFilterWhere(['like', 'employ_detail', $this->employ_detail]);
        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['sort' => SORT_ASC]);
        }

        return $dataProvider;
    }

}
