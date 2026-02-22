<?php

namespace frontend\models\appraisal;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\appraisal\AppraisalMaster;

/**
 * AppraisalMasterSearch represents the model behind the search form of `frontend\models\appraisal\AppraisalMaster`.
 */
class AppraisalMasterSearch extends AppraisalMaster
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'year', 'total_rating', 'total_review', 'status', 'appraise_by', 'review_by', 'created_by', 'updated_by'], 'integer'],
            [['appraise_date', 'review_date', 'created_at', 'updated_at'], 'safe'],
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
        $query = AppraisalMaster::find();

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
            'year' => $this->year,
            'total_rating' => $this->total_rating,
            'total_review' => $this->total_review,
            'status' => $this->status,
            'appraise_by' => $this->appraise_by,
            'appraise_date' => $this->appraise_date,
            'review_by' => $this->review_by,
            'review_date' => $this->review_date,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        return $dataProvider;
    }
}
