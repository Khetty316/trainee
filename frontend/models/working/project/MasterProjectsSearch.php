<?php

namespace frontend\models\working\project;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\project\MasterProjects;

/**
 * MasterProjectsSearch represents the model behind the search form of `frontend\models\working\project\MasterProjects`.
 */
class MasterProjectsSearch extends MasterProjects {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['project_code', 'project_name', 'project_description', 'project_image', 'created_at', 'updated_at', 'person_in_charge', 'created_by'], 'safe'],
            [['updated_by'], 'integer'],
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
        $query = MasterProjects::find();

        // add conditions that should always apply here

        $query->join("LEFT JOIN", 'user as createBy', 'master_projects.created_by = createBy.id');
        $query->join('LEFT JOIN', 'user as PIC', 'master_projects.person_in_charge = PIC.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['created_by'] = [
            'asc' => ['createBy.fullname' => SORT_ASC],
            'desc' => ['createBy.fullname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['person_in_charge'] = [
            'asc' => ['PIC.fullname' => SORT_ASC],
            'desc' => ['PIC.fullname' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'project_code', $this->project_code])
                ->andFilterWhere(['like', 'project_name', $this->project_name])
                ->andFilterWhere(['like', 'project_description', $this->project_description])
                ->andFilterWhere(['like', 'createBy.fullname', $this->created_by])
                ->andFilterWhere(['like', 'PIC.fullname', $this->person_in_charge])
                ->andFilterWhere(['like', 'project_image', $this->project_image]);

        return $dataProvider;
    }

}
