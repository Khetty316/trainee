<?php

namespace frontend\models\test;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\test\TestCustomContent;

/**
 * TestCustomContentSearch represents the model behind the search form of `frontend\models\test\TestCustomContent`.
 */
class TestCustomContentSearch extends TestCustomContent
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'test_form_id', 'content_order', 'is_deleted', 'created_by'], 'integer'],
            [['content', 'created_at'], 'safe'],
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
        $query = TestCustomContent::find();

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
            'test_form_id' => $this->test_form_id,
            'content_order' => $this->content_order,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
