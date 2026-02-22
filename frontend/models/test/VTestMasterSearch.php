<?php

namespace frontend\models\test;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\test\VTestMaster;

class VTestMasterSearch extends VTestMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'test_main_id', 'proj_id', 'panel_id', 'test_num', 'panel_qty', 'certified_by'], 'integer'],
            [['date'], 'safe'],
            [['detail'], 'string'],
            [['tc_ref', 'project_name', 'panel_type', 'prod_panel_code', 'panel_desc', 'panel_type', 'test_type', 'venue', 'client', 'elec_consultant', 'elec_contractor', 'status', 'tested_by'], 'string', 'max' => 255],
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
    public function search($params, $id, $type) {
        $query = VTestMaster::find();

        switch ($type) {
            case "testList":
                $query->orderBy(['prod_panel_code' => SORT_ASC])->all();
                break;
            case "testProgress":
                $query->where(['proj_id' => $id])->orderBy(['prod_panel_code' => SORT_ASC])->all();
                break;
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'test_main_id' => $this->test_main_id,
            'proj_id' => $this->proj_id,
            'panel_id' => $this->panel_id,
            'test_num' => $this->test_num,
            'panel_qty' => $this->panel_qty,
            'status' => $this->status,
            'certified_by' => $this->certified_by,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'tc_ref', $this->tc_ref])
                ->andFilterWhere(['like', 'project_name', $this->project_name])
                ->andFilterWhere(['like', 'prod_panel_code', $this->prod_panel_code])
                ->andFilterWhere(['like', 'panel_desc', $this->panel_desc])
                ->andFilterWhere(['like', 'test_type', $this->test_type])
                ->andFilterWhere(['like', 'venue', $this->venue])
                ->andFilterWhere(['like', 'client', $this->client])
                ->andFilterWhere(['like', 'elec_consultant', $this->elec_consultant])
                ->andFilterWhere(['like', 'elec_contractor', $this->elec_contractor])
                ->andFilterWhere(['like', 'tested_by', $this->tested_by])
                ->andFilterWhere(['like', 'detail', $this->detail])
                ->andFilterWhere(['like', 'panel_type', $this->panel_type]);
//        ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }

}
