<?php

namespace frontend\models\projectquotation;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\projectquotation\ProjectQRevisionsTemplate;

/**
 * ProjectQRevisionsTemplateSearch represents the model behind the search form of `frontend\models\projectquotation\ProjectQRevisionsTemplate`.
 */
class ProjectQRevisionsTemplateSearch extends ProjectQRevisionsTemplate {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'revision_copy_master', 'currency_id', 'with_sst', 'show_breakdown', 'show_breakdown_price', 'is_active'], 'integer'],
            [['updated_by', 'created_by', 'revision_description', 'remark', 'q_material_offered', 'q_switchboard_standard', 'q_quotation', 'q_delivery_ship_mode', 'q_delivery_destination', 'q_delivery', 'q_validity', 'q_payment', 'q_remark', 'created_at', 'updated_at'], 'safe'],
            [['amount'], 'number'],
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
        $query = ProjectQRevisionsTemplate::find();

        // add conditions that should always apply here
        $query->leftJoin('user', 'user.id = project_q_revisions_template.created_by');
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
            'revision_copy_master' => $this->revision_copy_master,
            'currency_id' => $this->currency_id,
            'amount' => $this->amount,
            'with_sst' => $this->with_sst,
            'show_breakdown' => $this->show_breakdown,
            'show_breakdown_price' => $this->show_breakdown_price,
            'created_at' => $this->created_at,
//            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
//            'updated_by' => $this->updated_by,
            'is_active' => $this->is_active,
        ]);

        $query->andFilterWhere(['like', 'revision_description', $this->revision_description])
                ->andFilterWhere(['like', 'remark', $this->remark])
                ->andFilterWhere(['like', 'q_material_offered', $this->q_material_offered])
                ->andFilterWhere(['like', 'q_switchboard_standard', $this->q_switchboard_standard])
                ->andFilterWhere(['like', 'q_quotation', $this->q_quotation])
                ->andFilterWhere(['like', 'q_delivery_ship_mode', $this->q_delivery_ship_mode])
                ->andFilterWhere(['like', 'q_delivery_destination', $this->q_delivery_destination])
                ->andFilterWhere(['like', 'q_delivery', $this->q_delivery])
                ->andFilterWhere(['like', 'q_validity', $this->q_validity])
                ->andFilterWhere(['like', 'q_payment', $this->q_payment])
                ->andFilterWhere(['like', 'q_remark', $this->q_remark])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
                ->andFilterWhere(['like', 'user.fullname', $this->updated_by]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'is_active' => SORT_DESC,
                'id' => SORT_DESC,
            ],
        ]);

        return $dataProvider;
    }
}
