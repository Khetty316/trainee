<?php

namespace frontend\models\covid\testkit;

use yii\base\Model;
use yii\data\ActiveDataProvider;
//use frontend\models\covid\testkit\CovidTestkitInventory;
use frontend\models\covid\testkit\CovidTestkitRecord;
use Yii;

/**
 * CovidTestkitInventorySearch represents the model behind the search form of `frontend\models\covid\testkit\CovidTestkitInventory`.
 */
class CovidTestkitRecordSearch extends CovidTestkitRecord {

    public $record_date;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['inventory_id'], 'integer'],
            [['created_at', 'inventory_id', 'brand', 'result_attachment', 'record_date', 'complete_status', 'remark', 'user_id'], 'safe'],
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
        $query = CovidTestkitRecord::find();

        // add conditions that should always apply here
        switch ($type) {
            case 'personalReceiveTestkit':
                $query->where("user_id=" . Yii::$app->user->id);
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

        $query->join('INNER JOIN', 'covid_testkit_inventory', 'covid_testkit_inventory.id=covid_testkit_record.inventory_id');
        $query->join('INNER JOIN', 'user', 'user.id=covid_testkit_record.user_id');

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'inventory_id' => $this->inventory_id,
//            'user_id' => $this->user_id,
//            'brand' => $this->brand,
//            'complete_status' => $this->complete_status,
            'result_attachment' => $this->result_attachment,
//            'created_at' => $this->created_at,
        ]);


        $query->andFilterWhere(['like', 'covid_testkit_record.brand', $this->brand])
                ->andFilterWhere(['like', 'DATE_FORMAT(covid_testkit_inventory.record_date,\'%d/%m/%Y\')', $this->record_date])
                ->andFilterWhere(['like', 'DATE_FORMAT(covid_testkit_record.created_at,\'%d/%m/%Y\')', $this->created_at])
                ->andFilterWhere(['like', 'IF(complete_status=1,"Yes","NO")', $this->complete_status])
                ->andFilterWhere(['like', 'user.fullname', $this->user_id])
                ->andFilterWhere(['like', 'covid_testkit_record.remark', $this->remark]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['id' => SORT_DESC]);
        }

        return $dataProvider;
    }

}
