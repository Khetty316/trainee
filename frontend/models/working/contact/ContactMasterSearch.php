<?php

namespace frontend\models\working\contact;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\contact\ContactMaster;

/**
 * ContactMasterSearch represents the model behind the search form of `frontend\models\working\contact\ContactMaster`.
 */
class ContactMasterSearch extends ContactMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'created_by'], 'integer'],
            [['contact_type', 'company_name', 'contact_person', 'contact_position', 'contact_number', 'email', 'address', 'postcode', 'country', 'created_at', 'area', 'state'], 'safe'],
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
    public function search($params, $type = "") {
        $query = ContactMaster::find();

        // add conditions that should always apply here
        switch ($type) {
            case 'client':
            case '':
                $query->where("contact_type='client'");
                break;
            case 'vendor':
                $query->where("contact_type='vendor'");
                break;
        }

        $query->leftJoin('ref_area', 'ref_area.area_id = contact_master.area')
                ->leftJoin('ref_state', 'ref_state.state_id = contact_master.state')
                ->leftJoin('ref_countries', 'ref_countries.country_code = contact_master.country');



        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->attributes['area'] = [
            'asc' => ['ref_area.area_name' => SORT_ASC],
            'desc' => ['ref_area.area_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['state'] = [
            'asc' => ['ref_state.state_name' => SORT_ASC],
            'desc' => ['ref_state.state_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['country'] = [
            'asc' => ['ref_countries.country_name' => SORT_ASC],
            'desc' => ['ref_countries.country_name' => SORT_DESC],
        ];



        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'contact_type', $this->contact_type])
                ->andFilterWhere(['like', 'company_name', $this->company_name])
                ->andFilterWhere(['like', 'contact_person', $this->contact_person])
                ->andFilterWhere(['like', 'contact_position', $this->contact_position])
                ->andFilterWhere(['like', 'contact_number', $this->contact_number])
                ->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'address', $this->address])
                ->andFilterWhere(['like', 'postcode', $this->postcode])
                ->andFilterWhere(['like', 'ref_area.area_name', $this->area])
                ->andFilterWhere(['like', 'ref_state.state_name', $this->state])
                ->andFilterWhere(['like', 'ref_countries.country_name', $this->country]);


        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['company_name' => SORT_ASC]);
        }


        return $dataProvider;
    }

}
