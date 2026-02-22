<?php

namespace frontend\models\working\po;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\po\VPurchaseOrderMaster;
use common\models\myTools\MyFormatter;

/**
 * PurchaseOrderMasterSearch represents the model behind the search form of `frontend\models\working\po\PurchaseOrderMaster`.
 */
class PurchaseOrderMasterSearch extends VPurchaseOrderMaster {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['po_id', 'po_pic', 'po_address', 'po_receive_status', 'created_by', 'updated_by','quotation_master_id'], 'integer'],
            [['po_number', 'po_date', 'project_code', 'po_material_desc', 'po_lead_time', 'po_etd', 'po_transporter',
            'po_upload_file', 'remarks', 'created_at', 'update_at', 'address_name', 'address_description',
            'city_id', 'city_name', 'created_by_fullname', 'po_pic_fullname', 'project_name', 'project_description', 'onsite_receive_by'
                ], 'safe'],
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
        $query = VPurchaseOrderMaster::find();
        // add conditions that should always apply here

        switch ($type) {
            case "pending":
                $query->where("po_receive_status=0");
                break;
            case "procTrackingListIndividual":
                $query->where("po_receive_status=0 AND po_pic=" . Yii::$app->user->id);
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
            'po_id' => $this->po_id,
            'po_address' => $this->address_name,
            'po_receive_status' => $this->po_receive_status,
        ]);

        $query->andFilterWhere(['like', 'po_number', $this->po_number])
                ->andFilterWhere(['=', 'po_date', $this->po_date == "" ? "" : MyFormatter::changeDateFormat_readToDB($this->po_date)])
                ->andFilterWhere(['like', 'project_code', $this->project_code])
                ->andFilterWhere(['like', 'po_pic_fullname', $this->po_pic_fullname])
                ->andFilterWhere(['like', 'po_material_desc', $this->po_material_desc])
                ->andFilterWhere(['like', 'po_lead_time', $this->po_lead_time])
                ->andFilterWhere(['like', 'po_etd', $this->po_etd])
                ->andFilterWhere(['like', 'po_transporter', $this->po_transporter])
                ->andFilterWhere(['like', 'po_upload_file', $this->po_upload_file])
                ->andFilterWhere(['like', 'onsite_receive_by', $this->onsite_receive_by])
                ->andFilterWhere(['like', 'quotation_master_id', $this->quotation_master_id])
                ->andFilterWhere(['like', 'remarks', $this->remarks]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['created_at' => SORT_DESC]);
        }

        return $dataProvider;
    }

}
