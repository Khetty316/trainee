<?php

namespace frontend\models\client;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\client\Clients;

/**
 * ClientSearch represents the model behind the search form of `frontend\models\client\Clients`.
 */
class ClientSearch extends Clients {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'created_by', 'updated_by'], 'integer'],
            [['company_registration_no', 'company_tin', 'payment_term', 'client_code', 'area', 'state', 'company_name', 'contact_person', 'contact_position', 'contact_number', 'email', 'address_1', 'address_2', 'postcode', 'country', 'created_at', 'updated_at'], 'safe'],
            [['current_outstanding_balance'], 'number'],
            [['tk_balance', 'tke_balance' ,'tkm_balance'], 'integer'],
            
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
        $query = Clients::find()
                ->joinWith(['clientContacts']) // join relation
                ->join("LEFT JOIN", 'ref_area', 'ref_area.area_id=area')
                ->join("LEFT JOIN", 'ref_state', 'ref_state.state_id=state')
                ->join("LEFT JOIN", 'ref_countries', 'ref_countries.country_code=country')
                ->distinct(); // to avoid duplicate clients if they have multiple contacts

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // main table filters
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        // apply filter conditions
        $query->andFilterWhere(['like', 'company_name', $this->company_name])
                ->andFilterWhere(['like', 'client_code', $this->client_code])
                ->andFilterWhere(['like', 'company_registration_no', $this->company_registration_no])
                ->andFilterWhere(['like', 'company_tin', $this->company_tin])
                ->andFilterWhere(['like', 'payment_term', $this->payment_term])
                
                ->andFilterWhere([
                        'tk_balance' => $this->tk_balance,
                        'tke_balance'=> $this->tke_balance,
                        'tkm_balance'=> $this->tkm_balance,
                        'current_outstanding_balance' => $this->current_outstanding_balance,
                    ])
                
                ->andFilterWhere(['like', 'ref_area.area_name', $this->area])
                ->andFilterWhere(['like', 'ref_state.state_name', $this->state])
                ->andFilterWhere(['like', 'ref_countries.country_name', $this->country])
                // 🔽 fix: use correct client_contact table column names
                ->andFilterWhere(['like', 'client_contact.name', $this->contact_person])
                ->andFilterWhere(['like', 'client_contact.position', $this->contact_position])
                ->andFilterWhere(['like', 'client_contact.contact_number', $this->contact_number])
                ->andFilterWhere(['like', 'client_contact.email_address', $this->email])
                // address filters (still from clients table)
                ->andFilterWhere(['like', 'address_1', $this->address_1])
                ->andFilterWhere(['like', 'address_2', $this->address_2])
                ->andFilterWhere(['like', 'postcode', $this->postcode]);

        // sorting fixes (optional)
        if (array_key_exists('sort', $params)) {
            switch ($params['sort']) {
                case "area":
                    $query->orderBy(['ref_area.area_name' => SORT_ASC]);
                    break;
                case "-area":
                    $query->orderBy(['ref_area.area_name' => SORT_DESC]);
                    break;
                case "state":
                    $query->orderBy(['ref_state.state_name' => SORT_ASC]);
                    break;
                case "-state":
                    $query->orderBy(['ref_state.state_name' => SORT_DESC]);
                    break;
                case "country":
                    $query->orderBy(['ref_countries.country_name' => SORT_ASC]);
                    break;
                case "-country":
                    $query->orderBy(['ref_countries.country_name' => SORT_DESC]);
                    break;
            }
        }

        return $dataProvider;
    }
}
