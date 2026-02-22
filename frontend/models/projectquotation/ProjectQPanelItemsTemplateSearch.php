<?php

namespace frontend\models\projectquotation;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\projectquotation\ProjectQPanelItemsTemplate;

/**
 * ProjectQPanelItemsTemplateSearch represents the model behind the search form of `frontend\models\projectquotation\ProjectQPanelItemsTemplate`.
 */
class ProjectQPanelItemsTemplateSearch extends ProjectQPanelItemsTemplate
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'panel_template_id', 'product_id', 'sort', 'created_by', 'updated_by'], 'integer'],
            [['item_description', 'created_at', 'updated_at'], 'safe'],
            [['cost', 'markup', 'amount'], 'number'],
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
        $query = ProjectQPanelItemsTemplate::find();

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
            'panel_template_id' => $this->panel_template_id,
            'cost' => $this->cost,
            'markup' => $this->markup,
            'amount' => $this->amount,
            'product_id' => $this->product_id,
            'sort' => $this->sort,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'item_description', $this->item_description]);

        return $dataProvider;
    }
}
