<?php

namespace frontend\models\inventory\cmms;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\cmms\InventoryModelCmms;

/**
 * InventoryModelCmmsSearch represents the model behind the search form of `frontend\models\inventory\cmms\InventoryModelCmms`.
 */
class InventoryModelCmmsSearch extends InventoryModelCmms {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'active_sts'], 'integer'],
            [['code', 'description', 'unit_type', 'image', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
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
        $query = InventoryModelCmms::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user", "inventory_model_cmms.created_by = user.id");

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
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'unit_type', $this->unit_type])
                ->andFilterWhere(['like', 'image', $this->image])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
                ->andFilterWhere(['like', 'user.fullname', $this->updated_by]);

        return $dataProvider;
    }
}
