<?php

namespace frontend\models\cmms;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\cmms\CmmsAssetList;

/**
 * CmmsAssetListSearch represents the model behind the search form of `frontend\models\cmms\CmmsAssetList`.
 */
class CmmsAssetListSearch extends CmmsAssetList
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'part_id', 'active_sts', 'is_deleted', 'updated_by'], 'integer'],
            [['area', 'section', 'name', 'manufacturer', 'serial_no', 'date_of_purchase', 'date_of_installation', 'asset_id'], 'safe'],
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
        $query = CmmsAssetList::find()
//                ->where(['is_deleted' => 0])
                ->where(['active_sts' => 1]);

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
            'part_id' => $this->part_id,
            'date_of_purchase' => $this->date_of_purchase,
            'date_of_installation' => $this->date_of_installation,
            'active_sts' => $this->active_sts,
            'is_deleted' => $this->is_deleted,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'section', $this->section])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'manufacturer', $this->manufacturer])
            ->andFilterWhere(['like', 'serial_no', $this->serial_no])
            ->andFilterWhere(['like', 'asset_id', $this->asset_id]);

        return $dataProvider;
    }
}
