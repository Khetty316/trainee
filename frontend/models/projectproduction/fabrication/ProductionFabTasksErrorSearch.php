<?php

namespace frontend\models\projectproduction\fabrication;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\projectproduction\fabrication\ProductionFabTasksError;

/**
 * ProductionFabTasksErrorSearch represents the model behind the search form of `frontend\models\projectproduction\fabrication\ProductionFabTasksError`.
 */
class ProductionFabTasksErrorSearch extends ProductionFabTasksError {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'production_fab_task_id', 'error_code', 'created_by', 'updated_by'], 'integer'],
            [['remark', 'created_at', 'updated_at'], 'safe'],
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
    public function search($params, $params2 = "", $extraParams = []) {
        $query = ProductionFabTasksError::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        switch ($params2) {
            case "singlePanel":
                $query->where(['production_fab_task_id' => $extraParams['taskId']]);
                break;
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'production_fab_task_id' => $this->production_fab_task_id,
            'error_code' => $this->error_code,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }

}
