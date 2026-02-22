<?php

namespace frontend\models\projectproduction;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\projectproduction\VProductionTasksError;

/**
 * VProductionTasksErrorSearch represents the model behind the search form of `frontend\models\projectproduction\VProductionTasksError`.
 */
class VProductionTasksErrorSearch extends VProductionTasksError {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['task_type', 'id', 'production_task_id', 'error_code', 'panel_code', 'task_name', 'description', 'remark', 'created_by', 'is_read'], 'safe'],
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
        $query = VProductionTasksError::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        switch ($params2) {
            case "singlePanel":
                $query->where(['task_type' => $extraParams['taskType'], 'production_task_id' => $extraParams['taskId']]);
                break;
            case "allErrors":
//                $query->where(['production_task_id' => $extraParams['taskId']]);
                break;
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'task_type' => $this->task_type,
            'task_name' => $this->task_name,
            'id' => $this->id,
            'production_task_id' => $this->production_task_id,
            'error_code' => $this->error_code,
            'is_read' => $this->is_read,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark])
                ->andFilterWhere(['like', 'created_by', $this->created_by])
                ->andFilterWhere(['like', 'panel_code', $this->panel_code])
                ->andFilterWhere(['like', 'description', $this->description])
        ;

        $dataProvider->setSort([
            'defaultOrder' => ['created_at' => SORT_DESC],
        ]);
        
        return $dataProvider;
    }

}
