<?php

namespace frontend\models\inventory;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\inventory\InventorySupplier;

/**
 * InventorySupplierSearch represents the model behind the search form of `frontend\models\inventory\InventorySupplier`.
 */
class InventorySupplierSearch extends InventorySupplier
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'active_sts'], 'integer'],
            [['created_by', 'updated_by', 'code', 'name', 'address1', 'address2', 'address3', 'address4', 'contact_name', 'contact_number', 'contact_email', 'contact_fax', 'agent_terms', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params) {
        $query = InventorySupplier::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user", "inventory_supplier.created_by = user.id");

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
                ->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'address1', $this->address1])
                ->andFilterWhere(['like', 'address2', $this->address2])
                ->andFilterWhere(['like', 'address3', $this->address3])
                ->andFilterWhere(['like', 'address4', $this->address4])
                ->andFilterWhere(['like', 'contact_name', $this->contact_name])
                ->andFilterWhere(['like', 'contact_number', $this->contact_number])
                ->andFilterWhere(['like', 'contact_email', $this->contact_email])
                ->andFilterWhere(['like', 'contact_fax', $this->contact_fax])
                ->andFilterWhere(['like', 'agent_terms', $this->agent_terms])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
                ->andFilterWhere(['like', 'user.fullname', $this->updated_by]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
        ]);
        
        return $dataProvider;
    }
}
