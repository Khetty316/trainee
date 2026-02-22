<?php

namespace frontend\models\sysadmin\user;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form of `common\models\User`.
 */
class UserSearch extends User {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'postcode', 'area_id'], 'integer'],
            [['username', 'auth_key', 'password', 'password_reset_token', 'email', 'verification_token', 'fullname', 'contact_no', 'address', 'emergency_contact_no', 'emergency_contact_person', 'staff_id'], 'safe'],
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
    public function search($params, $type = '') {
        $query = User::find();

        // add conditions that should always apply here
        switch ($type) {
            case 'payrollList':
                $query->where("status in (9,10) AND (staff_id <> '' AND staff_id IS NOT NULL)");
                break;
        }

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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'postcode' => $this->postcode,
            'area_id' => $this->area_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
                ->andFilterWhere(['like', 'auth_key', $this->auth_key])
                ->andFilterWhere(['like', 'staff_id', $this->staff_id])
                ->andFilterWhere(['like', 'password', $this->password])
                ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
                ->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'verification_token', $this->verification_token])
                ->andFilterWhere(['like', 'fullname', $this->fullname])
                ->andFilterWhere(['like', 'contact_no', $this->contact_no])
                ->andFilterWhere(['like', 'address', $this->address])
                ->andFilterWhere(['like', 'emergency_contact_no', $this->emergency_contact_no])
                ->andFilterWhere(['like', 'emergency_contact_person', $this->emergency_contact_person]);

        switch ($type) {
            case 'payrollList':
                if (!array_key_exists('sort', $params)) {
                    $query->orderBy(['staff_id' => SORT_ASC]);
                }
                break;
        }

        return $dataProvider;
    }

}
