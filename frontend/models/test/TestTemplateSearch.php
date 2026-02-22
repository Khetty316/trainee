<?php

namespace frontend\models\test;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\test\TestTemplate;
use frontend\models\test\TestMain;
use frontend\models\test\TestMaster;
use frontend\models\test\RefTestFormList;

/**
 * TestTemplateSearch represents the model behind the search form of `frontend\models\test\TestTemplate`.
 */
class TestTemplateSearch extends TestTemplate {

    public $formname;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'doc_ref', 'rev_no', 'active_sts', 'created_by', 'updated_by'], 'integer'],
            [['formcode', 'proctest1', 'proctest2', 'proctest3', 'created_at', 'updated_at', 'formname'], 'safe'],
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
        $query = TestTemplate::find();
        $query->join("LEFT JOIN", "user AS created_user", "test_template.created_by = created_user.id");
        $query->join("LEFT JOIN", "user AS updated_user", "test_template.updated_by = updated_user.id");

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'formname' => [
                        'asc' => ['formcode' => SORT_ASC],
                        'desc' => ['formcode' => SORT_DESC],
                    ],
                    'doc_ref',
                    'rev_no',
                    'created_by',
                    'updated_by',
//                    'proctest3',
                ],
            ],
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
            'doc_ref' => $this->doc_ref,
            'rev_no' => $this->rev_no,
            'active_sts' => $this->active_sts,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $formClassOptions = array_keys(
                array_merge(
                        RefTestFormList::getDropDownList(),
                        [
                            TestMaster::TEMPLATE_ITP => TestMain::TEST_ITP_TITLE,
                            TestMaster::TEMPLATE_FAT => TestMain::TEST_FAT_TITLE
                        ]
                )
        );

        if ($this->formname && in_array($this->formname, $formClassOptions)) {
            // Apply your specific filter logic if formname matches one of the formClassOptions
            // This can be a custom logic to filter the main query
            $query->andFilterWhere(['formcode' => $this->formname]);
        }


        $query->andFilterWhere(['like', 'created_user.fullname', $this->created_by])
                ->andFilterWhere(['like', 'updated_user.fullname', $this->updated_by])
                ->andFilterWhere(['like', 'proctest1', $this->proctest1])
                ->andFilterWhere(['like', 'proctest2', $this->proctest2])
                ->andFilterWhere(['like', 'proctest3', $this->proctest3]);

        return $dataProvider;
    }

}
