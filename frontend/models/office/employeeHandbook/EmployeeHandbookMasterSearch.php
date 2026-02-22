<?php

namespace frontend\models\office\employeeHandbook;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\office\employeeHandbook\EmployeeHandbookMaster;

/**
 * EmployeeHandbookMasterSearch represents the model behind the search form of `frontend\models\office\employeeHandbook\EmployeeHandbookMaster`.
 */
class EmployeeHandbookMasterSearch extends EmployeeHandbookMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'edition_no', 'is_active'], 'integer'],
            [['name', 'edition_date', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
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
        $query = EmployeeHandbookMaster::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user", "employee_handbook_master.created_by = user.id");

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'edition_no' => $this->edition_no,
            'edition_date' => $this->edition_date,
            'is_active' => $this->is_active,
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'employee_handbook_master.created_at', $this->created_at])
                ->andFilterWhere(['like', 'employee_handbook_master.updated_at', $this->updated_at])
                ->andFilterWhere(['like', 'user.fullname', $this->created_by])
        ;

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
        ]);
        
        return $dataProvider;
    }
}
