<?php

namespace frontend\models\working\appraisal;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\appraisal\ShortAppraisalMaster;

/**
 * ShortAppraisalMasterSearch represents the model behind the search form of `frontend\models\working\appraisal\ShortAppraisalMaster`.
 */
class ShortAppraisalMasterSearch extends ShortAppraisalMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['date', 'content', 'created_at', 'user_id', 'created_by'], 'safe'],
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
        $query = ShortAppraisalMaster::find();

        // add conditions that should always apply here
        switch ($type) {
            case 'personal':
                $query->where(['user_id' => Yii::$app->user->id]);
                break;
        }


        $query->join("INNER JOIN", 'user', 'user.id=short_appraisal_master.user_id');

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
//            'user_id' => $this->user_id,
//            'date' => $this->date,
//            'created_at' => $this->created_at,
//            'created_by' => $this->created_by,
        ]);


        $query->andFilterWhere(['like', 'content', $this->content])
                ->andFilterWhere(['like', 'user.fullname', $this->user_id])
                ->andFilterWhere(['like', 'DATE_FORMAT(date, "%d/%m/%Y")', $this->date])
                ->andFilterWhere(['like', 'DATE_FORMAT(short_appraisal_master.created_at,"%d/%m/%Y %H:%i")', $this->created_at]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['date' => SORT_DESC]);
        }

        return $dataProvider;
    }

}
