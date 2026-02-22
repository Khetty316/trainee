<?php

namespace frontend\models\working\project;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\project\ProjectMaster;

/**
 * ProjectMasterSearch represents the model behind the search form of `frontend\models\working\project\ProjectMaster`.
 */
class ProjectMasterSearch extends ProjectMaster
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'location', 'client_id', 'proj_director', 'proj_manager', 'site_manager', 'proj_coordinator', 'project_engineer', 'site_engineer', 'site_supervisor', 'project_qs', 'created_by', 'updated_by'], 'integer'],
            [['proj_code', 'title_short', 'title_long', 'project_status', 'service', 'client_pic_name', 'client_pic_contact', 'award_date', 'commencement_date', 'eot_date', 'handover_date', 'dlp_expiry_date', 'created_at', 'updated_at'], 'safe'],
            [['contract_sum'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = ProjectMaster::find();

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
            'location' => $this->location,
            'client_id' => $this->client_id,
            'contract_sum' => $this->contract_sum,
            'award_date' => $this->award_date,
            'commencement_date' => $this->commencement_date,
            'eot_date' => $this->eot_date,
            'handover_date' => $this->handover_date,
            'dlp_expiry_date' => $this->dlp_expiry_date,
            'proj_director' => $this->proj_director,
            'proj_manager' => $this->proj_manager,
            'site_manager' => $this->site_manager,
            'proj_coordinator' => $this->proj_coordinator,
            'project_engineer' => $this->project_engineer,
            'site_engineer' => $this->site_engineer,
            'site_supervisor' => $this->site_supervisor,
            'project_qs' => $this->project_qs,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'proj_code', $this->proj_code])
            ->andFilterWhere(['like', 'title_short', $this->title_short])
            ->andFilterWhere(['like', 'title_long', $this->title_long])
            ->andFilterWhere(['like', 'project_status', $this->project_status])
            ->andFilterWhere(['like', 'service', $this->service])
            ->andFilterWhere(['like', 'client_pic_name', $this->client_pic_name])
            ->andFilterWhere(['like', 'client_pic_contact', $this->client_pic_contact]);

        return $dataProvider;
    }
}
