<?php

namespace frontend\models\test;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\test\TestDetailPunchlist;

/**
 * TestDetailPunchlistSearch represents the model behind the search form of `frontend\models\test\TestDetailPunchlist`.
 */
class TestDetailPunchlistSearch extends TestDetailPunchlist {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'form_punchlist_id', 'created_by', 'updated_by'], 'integer'],
            [['test_form_code', 'error_id', 'remark', 'rectify_date', 'verify_by', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params, $param1 = null, $param2 = null) {
        $query = TestDetailPunchlist::find();

        switch ($param1) {
            case 'singleForm':
                $query->where(['form_punchlist_id' => $param2]);
                break;

            default:
                break;
        }

        $query->join('LEFT JOIN', 'ref_test_form_list as reftest', 'reftest.code = test_detail_punchlist.test_form_code')
                ->join('LEFT JOIN', 'ref_proj_prod_task_errors as referror', 'referror.id = test_detail_punchlist.error_id');

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
            'form_punchlist_id' => $this->form_punchlist_id,
            'rectify_date' => \common\models\myTools\MyFormatter::fromDateRead_toDateSQL($this->rectify_date),
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'reftest.code', $this->test_form_code])
                ->andFilterWhere(['like', 'referror.description', $this->error_id])
                ->andFilterWhere(['like', 'remark', $this->remark])
                ->andFilterWhere(['like', 'verify_by', $this->verify_by]);

        return $dataProvider;
    }

}
