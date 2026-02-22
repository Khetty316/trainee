<?php

namespace frontend\models\covid\testkit;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\covid\testkit\CovidTestkitInventory;
use Yii;

/**
 * CovidTestkitInventorySearch represents the model behind the search form of `frontend\models\covid\testkit\CovidTestkitInventory`.
 */
class CovidTestkitInventorySearch extends CovidTestkitInventory {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'total_movement'], 'integer'],
            [['brand', 'record_date', 'giving_to', 'created_at', 'confirm_status'], 'safe'],
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
        $query = CovidTestkitInventory::find();

        // add conditions that should always apply here
        switch ($type) {
            case 'personalReceiveTestkit':
                $query->where(["giving_to" => Yii::$app->user->id, 'confirm_status' => 0]);
                $query->join("INNER JOIN", "user", 'user.id=covid_testkit_inventory.giving_to');

                break;
            case 'summary':
                $query->select('id, brand, record_date, sum(total_movement) as total_movement, giving_to, confirm_status, created_at')->groupBy(['brand'])
                        ->where(['confirm_status' => 1]);
                break;
            case 'detail':
                $query->join("INNER JOIN", "user", 'user.id=covid_testkit_inventory.giving_to');

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
//            'record_date' => $this->record_date,
            'total_movement' => $this->total_movement,
//            'giving_to' => $this->giving_to,
//            'confirm_status' => $this->confirm_status,
            'created_at' => $this->created_at,
        ]);



        $query->andFilterWhere(['like', 'brand', $this->brand])
                ->andFilterWhere(['like', 'record_date', $this->record_date])
                ->andFilterWhere(['like', 'IF(confirm_status=0,"No","Yes")', $this->confirm_status])
                ->andFilterWhere(['like', 'user.fullname', $this->giving_to]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['id' => SORT_DESC]);
        }

        return $dataProvider;
    }

}
