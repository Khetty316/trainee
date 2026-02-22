<?php

namespace frontend\models\office\preReqForm;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\office\preReqForm\PrereqFormMaster;
use frontend\models\RefGeneralStatus;

/**
 * PrereqFormMasterSearch represents the model behind the search form of `frontend\models\office\preReqForm\PrereqFormMaster`.
 */
class PrereqFormMasterSearch extends PrereqFormMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'status', 'is_deleted', 'created_by', 'updated_by'], 'integer'],
            [['superior_id', 'prf_no', 'date_of_material_required', 'filename', 'created_at', 'updated_at'], 'safe'],
            [['total_amount'], 'number'],
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
    public function search($params, $context = null) {
        $query = PrereqFormMaster::find();

        switch ($context) {
            case "personalPending":
                $query->where(['prereq_form_master.created_by' => Yii::$app->user->identity->id])
                        ->andWhere(['prereq_form_master.is_deleted' => 0])
                        ->andWhere(['prereq_form_master.status' => RefGeneralStatus::STATUS_GetSuperiorApproval]);
                break;

            case "personalAll":
                $query->where(['prereq_form_master.created_by' => Yii::$app->user->identity->id]);
                break;

            case "superiorPending":
                $query->where(['prereq_form_master.superior_id' => Yii::$app->user->identity->id])
                        ->andWhere(['prereq_form_master.is_deleted' => 0])
                        ->andWhere(['prereq_form_master.status' => RefGeneralStatus::STATUS_GetSuperiorApproval]);
                break;

            case "superiorAll":
                $query->where(['prereq_form_master.superior_id' => Yii::$app->user->identity->id]);
                break;

            case "superuserPending":
                $query->where(['prereq_form_master.is_deleted' => 0])
                        ->andWhere(['prereq_form_master.status' => RefGeneralStatus::STATUS_GetSuperiorApproval]);
                break;

            case "pendingInventory":
                $query->where(['prereq_form_master.source_module' => 2])
                        ->andWhere(['prereq_form_master.is_deleted' => 0])
                        ->andWhere(['prereq_form_master.status' => RefGeneralStatus::STATUS_GetSuperiorApproval]);
                break;

            case "allInventory":
                $query->where(['prereq_form_master.source_module' => 2]);
                break;

            case "pendingProcurementInventoryProjcoor":
                $query->where(['prereq_form_master.source_module' => 2])
                        ->andWhere(['prereq_form_master.is_deleted' => 0])
                        ->andWhere(['prereq_form_master.status' => RefGeneralStatus::STATUS_Approved])
                        ->andWhere(['prereq_form_master.inventory_flag' => null])
                        ->andWhere(['prereq_form_master.created_by' => Yii::$app->user->identity->id]);
                break;

            case "pendingApprovalInventoryProjcoor":
                $query->where(['prereq_form_master.source_module' => 2])
                        ->andWhere(['prereq_form_master.is_deleted' => 0])
                        ->andWhere(['prereq_form_master.status' => RefGeneralStatus::STATUS_GetSuperiorApproval])
                        ->andWhere(['prereq_form_master.inventory_flag' => null])
                        ->andWhere(['prereq_form_master.created_by' => Yii::$app->user->identity->id]);
                break;

            case "allInventoryProjcoor":
                $query->where(['prereq_form_master.source_module' => 2])
                    ->andWhere(['prereq_form_master.created_by' => Yii::$app->user->identity->id]);
                break;
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "user a", "prereq_form_master.superior_id = a.id");

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//            'date_of_material_required' => $this->date_of_material_required,
            'total_amount' => $this->total_amount,
//            'superior_id' => $this->superior_id,
            'status' => $this->status,
            'is_deleted' => $this->is_deleted,
//            'created_by' => $this->created_by,
//            'created_at' => $this->created_at,
//            'updated_by' => $this->updated_by,
//            'updated_at' => $this->updated_at,
        ]);

        if (!empty($this->date_of_material_required)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->date_of_material_required);
            if ($date) {
                $query->andFilterWhere([
                    'prereq_form_master.date_of_material_required' => $date->format('Y-m-d')
                ]);
            }
        }

        if (!empty($this->created_at)) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->created_at);
            if ($date) {
                $query->andFilterWhere(['between', 'prereq_form_master.created_at',
                    $date->format('Y-m-d 00:00:00'),
                    $date->format('Y-m-d 23:59:59')
                ]);
            }
        }

        $query->andFilterWhere(['like', 'prereq_form_master.prf_no', $this->prf_no])
//                ->andFilterWhere(['like', 'prereq_form_master.filename', $this->filename])
                ->andFilterWhere(['like', 'a.fullname', $this->superior_id]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
        ]);

        return $dataProvider;
    }
}
