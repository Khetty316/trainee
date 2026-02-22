<?php

namespace frontend\models\working\project;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\project\ProjectProgressClaim;
use common\models\myTools\MyFormatter;
/**
 * ProjectProgressClaimSearch represents the model behind the search form of `frontend\models\working\project\ProjectProgressClaim`.
 */
class ProjectProgressClaimSearch extends ProjectProgressClaim {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'project_id', 'created_by'], 'integer'],
            [['submit_file', 'submit_date', 'certified_file', 'certified_date', 'created_at','submit_reference', 'certified_reference', 'invoice_file'], 'safe'],
            [['submit_amount', 'certified_amount'], 'number'],

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
    public function search($params, $type = '', $myParams = []) {
        $query = ProjectProgressClaim::find();

        switch ($type) {
            case "viewProgressClaimMain": // 1         director_approval           Waiting for directors' approval
                $query->where("project_id = " . $myParams[0]);
                break;
            case "indexAccountIssueInvoice": // 1         director_approval           Waiting for directors' approval
                $query->where(['IS NOT', 'certified_amount', null])->andWhere(['invoice_file' => null]);
                break;
        }



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
        
        if($this->certified_date){
            $this->certified_date = MyFormatter::fromDateRead_toDateSQL($this->certified_date);
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'project_id' => $this->project_id,
            'submit_date' => $this->submit_date,
            'submit_amount' => $this->submit_amount,
            'certified_date' => $this->certified_date,
            'certified_amount' => $this->certified_amount,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);
        $query->andFilterWhere(['like', 'submit_file', $this->submit_file])
                ->andFilterWhere(['like', 'certified_file', $this->certified_file]);


        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['id' => SORT_DESC]);
        }

        return $dataProvider;
    }

}
