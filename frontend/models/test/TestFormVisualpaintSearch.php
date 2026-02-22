<?php

namespace frontend\models\test;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\test\TestFormVisualpaint;

/**
 * TestFormVisualpaintSearch represents the model behind the search form of `frontend\models\test\TestFormVisualpaint`.
 */
class TestFormVisualpaintSearch extends TestFormVisualpaint {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'test_master_id', 'status', 'a_scratch', 'a_rust', 'b_scratch', 'b_rust', 'c_scratch', 'c_rust', 'd_scratch', 'd_rust', 'e_scratch', 'e_rust', 'f_scratch', 'f_rust', 'a_measure1', 'a_measure2', 'a_measure3', 'a_average', 'a_result', 'b_measure1', 'b_measure2', 'b_measure3', 'b_average', 'b_result', 'c_measure1', 'c_measure2', 'c_measure3', 'c_average', 'c_result', 'd_measure1', 'd_measure2', 'd_measure3', 'd_average', 'd_result', 'e_measure1', 'e_measure2', 'e_measure3', 'e_average', 'e_result', 'f_measure1', 'f_measure2', 'f_measure3', 'f_average', 'f_result', 'created_by', 'updated_by'], 'integer'],
            [['template', 'a_color', 'a_finishing', 'a_remark', 'b_color', 'b_finishing', 'b_remark', 'c_color', 'c_finishing', 'c_remark', 'd_color', 'd_finishing', 'd_remark', 'e_color', 'e_finishing', 'e_remark', 'f_color', 'f_finishing', 'f_remark', 'created_at', 'updated_at'], 'safe'],
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
        $query = TestFormVisualpaint::find();

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
            'test_master_id' => $this->test_master_id,
            'status' => $this->status,
            'a_scratch' => $this->a_scratch,
            'a_rust' => $this->a_rust,
            'b_scratch' => $this->b_scratch,
            'b_rust' => $this->b_rust,
            'c_scratch' => $this->c_scratch,
            'c_rust' => $this->c_rust,
            'd_scratch' => $this->d_scratch,
            'd_rust' => $this->d_rust,
            'e_scratch' => $this->e_scratch,
            'e_rust' => $this->e_rust,
            'f_scratch' => $this->f_scratch,
            'f_rust' => $this->f_rust,
            'a_measure1' => $this->a_measure1,
            'a_measure2' => $this->a_measure2,
            'a_measure3' => $this->a_measure3,
            'a_average' => $this->a_average,
            'a_result' => $this->a_result,
            'b_measure1' => $this->b_measure1,
            'b_measure2' => $this->b_measure2,
            'b_measure3' => $this->b_measure3,
            'b_average' => $this->b_average,
            'b_result' => $this->b_result,
            'c_measure1' => $this->c_measure1,
            'c_measure2' => $this->c_measure2,
            'c_measure3' => $this->c_measure3,
            'c_average' => $this->c_average,
            'c_result' => $this->c_result,
            'd_measure1' => $this->d_measure1,
            'd_measure2' => $this->d_measure2,
            'd_measure3' => $this->d_measure3,
            'd_average' => $this->d_average,
            'd_result' => $this->d_result,
            'e_measure1' => $this->e_measure1,
            'e_measure2' => $this->e_measure2,
            'e_measure3' => $this->e_measure3,
            'e_average' => $this->e_average,
            'e_result' => $this->e_result,
            'f_measure1' => $this->f_measure1,
            'f_measure2' => $this->f_measure2,
            'f_measure3' => $this->f_measure3,
            'f_average' => $this->f_average,
            'f_result' => $this->f_result,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'template', $this->template])
                ->andFilterWhere(['like', 'a_color', $this->a_color])
                ->andFilterWhere(['like', 'a_finishing', $this->a_finishing])
                ->andFilterWhere(['like', 'a_remark', $this->a_remark])
                ->andFilterWhere(['like', 'b_color', $this->b_color])
                ->andFilterWhere(['like', 'b_finishing', $this->b_finishing])
                ->andFilterWhere(['like', 'b_remark', $this->b_remark])
                ->andFilterWhere(['like', 'c_color', $this->c_color])
                ->andFilterWhere(['like', 'c_finishing', $this->c_finishing])
                ->andFilterWhere(['like', 'c_remark', $this->c_remark])
                ->andFilterWhere(['like', 'd_color', $this->d_color])
                ->andFilterWhere(['like', 'd_finishing', $this->d_finishing])
                ->andFilterWhere(['like', 'd_remark', $this->d_remark])
                ->andFilterWhere(['like', 'e_color', $this->e_color])
                ->andFilterWhere(['like', 'e_finishing', $this->e_finishing])
                ->andFilterWhere(['like', 'e_remark', $this->e_remark])
                ->andFilterWhere(['like', 'f_color', $this->f_color])
                ->andFilterWhere(['like', 'f_finishing', $this->f_finishing])
                ->andFilterWhere(['like', 'f_remark', $this->f_remark]);

        return $dataProvider;
    }

}
