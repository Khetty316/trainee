<?php

namespace frontend\models\working\project;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\project\ProspectMaster;

/**
 * ProspectMasterSearch represents the model behind the search form of `frontend\models\working\project\ProspectMaster`.
 */
class ProspectMasterSearch extends ProspectMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['proj_code', 'title_short', 'title_long', 'due_date', 'other_pic', 'project_type', 'created_at', 'area', 'staff_pic', 'created_by'], 'safe'],
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
        $query = ProspectMaster::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['area'] = [
            'asc' => ['ref_area.area_name' => SORT_ASC],
            'desc' => ['ref_area.area_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['staff_pic'] = [
            'asc' => ['user.fullname' => SORT_ASC],
            'desc' => ['user.fullname' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->join("LEFT JOIN", 'user', 'prospect_master.staff_pic = user.id');
        $query->join('LEFT JOIN', 'ref_area', 'prospect_master.area = ref_area.area_id');

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'due_date' => $this->due_date,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'proj_code', $this->proj_code])
                ->andFilterWhere(['like', 'title_short', $this->title_short])
                ->andFilterWhere(['like', 'title_long', $this->title_long])
                ->andFilterWhere(['like', 'other_pic', $this->other_pic])
                ->andFilterWhere(['like', 'project_type', $this->project_type])
                ->andFilterWhere(['like', 'user.fullname', $this->staff_pic])
                ->andFilterWhere(['like', 'ref_area.area_name', $this->area])
        ;
        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['id' => SORT_DESC]);
        }

        return $dataProvider;
    }

}
