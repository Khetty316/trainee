<?php

namespace frontend\models\working\documentreminder;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\documentreminder\DocumentReminderMaster;

/**
 * DocumentReminderMasterSearch represents the model behind the search form of `frontend\models\working\documentreminder\DocumentReminderMaster`.
 */
class DocumentReminderMasterSearch extends DocumentReminderMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'active_sts', 'created_by', 'updated_by'], 'integer'],
            [['category', 'description', 'filename', 'expiry_date', 'remind_date', 'remark', 'created_at', 'updated_at'], 'safe'],
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
        $query = DocumentReminderMaster::find();

        // add conditions that should always apply here

        switch ($type) {
            case "remind":
                $query->where("remind_date<='" . date("Y-m-d") . "' OR expiry_date<='" . date("Y-m-d") . "'");
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
            'active_sts' => $this->active_sts,
//            'expiry_date' => $this->expiry_date,
//            'remind_date' => $this->remind_date,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'category', $this->category])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'SUBSTRING(filename,16)', $this->filename])
                ->andFilterWhere(['like', 'DATE_FORMAT(expiry_date,\'%d/%m/%Y\')', $this->expiry_date])
                ->andFilterWhere(['like', 'DATE_FORMAT(remind_date,\'%d/%m/%Y\')', $this->remind_date])
                ->andFilterWhere(['like', 'remark', $this->remark]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['created_at' => SORT_DESC]);
        }
        return $dataProvider;
    }

}
