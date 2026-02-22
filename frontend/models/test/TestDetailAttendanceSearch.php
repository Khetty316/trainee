<?php

namespace frontend\models\test;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\test\TestDetailAttendance;

/**
 * TestDetailAttendanceSearch represents the model behind the search form of `frontend\models\test\TestDetailAttendance`.
 */
class TestDetailAttendanceSearch extends TestDetailAttendance {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'form_attendance_id', 'created_by', 'updated_by'], 'integer'],
            [['name', 'org', 'designation', 'role', 'signature', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params, $param1, $param2) {
        $query = TestDetailAttendance::find();

        switch ($param1) {
            case 'singleAttendance':
                $query->where(['form_attendance_id' => $param2]);
                break;

            default:
                break;
        }

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
            'form_attendance_id' => $this->form_attendance_id,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'org', $this->org])
                ->andFilterWhere(['like', 'designation', $this->designation])
                ->andFilterWhere(['like', 'role', $this->role])
                ->andFilterWhere(['like', 'signature', $this->signature]);

        return $dataProvider;
    }

}
