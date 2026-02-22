<?php

namespace frontend\models\projectquotation;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\projectquotation\QuotationPdfMasters;

/**
 * QuotationPdfMastersSearch represents the model behind the search form of `frontend\models\projectquotation\QuotationPdfMasters`.
 */
class QuotationPdfMastersSearch extends QuotationPdfMasters {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'revision_id', 'proj_q_rev_id', 'with_sst', 'currency_id', 'show_breakdown', 'show_breakdown_price', 'discount_type', 'show_panel_description', 'file_size', 'prepared_by', 'approved_by', 'created_by', 'updated_by', 'md_approval_status'], 'integer'],
            [['project_q_client_id', 'quotation_no', 'to_company', 'to_pic', 'to_tel_no', 'to_fax_no', 'q_from', 'q_your_ref', 'q_date', 'proj_title', 'q_material_offered', 'q_switchboard_standard', 'q_quotation', 'q_delivery_ship_mode', 'q_delivery_destination', 'q_delivery', 'q_validity', 'q_payment', 'q_remark', 'filename', 'file_type', 'file_blob', 'created_at', 'updated_at', 'prepared_by_sign', 'approved_by_sign'], 'safe'],
            [['discount_amt'], 'number'],
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
        $query = QuotationPdfMasters::find();

        // add conditions that should always apply here
        switch ($type) {
            case "pending":
                $query->where(['md_approval_status' => \frontend\models\projectquotation\QuotationPdfMasters::QUOTATION_GET_DIRECTOR_APPROVAL]);

                break;

            case "all":
                $query->where(['md_approval_status' => \frontend\models\projectquotation\QuotationPdfMasters::QUOTATION_GET_DIRECTOR_APPROVAL])
                        ->orWhere(['md_approval_status' => \frontend\models\projectquotation\QuotationPdfMasters::QUOTATION_DIRECTOR_APPROVED]);

            default:
                // Handle unknown type or no filter
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->join("LEFT JOIN", "project_q_clients a", "quotation_pdf_masters.project_q_client_id = a.id");
        $query->join("LEFT JOIN", "clients b", "a.client_id = b.id");

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
//            'id' => $this->id,
//            'project_q_client_id' => $this->project_q_client_id,
//            'revision_id' => $this->revision_id,
//            'q_date' => $this->q_date,
//            'proj_q_rev_id' => $this->proj_q_rev_id,
//            'with_sst' => $this->with_sst,
//            'currency_id' => $this->currency_id,
//            'show_breakdown' => $this->show_breakdown,
//            'show_breakdown_price' => $this->show_breakdown_price,
//            'discount_amt' => $this->discount_amt,
//            'discount_type' => $this->discount_type,
//            'show_panel_description' => $this->show_panel_description,
//            'file_size' => $this->file_size,
//            'prepared_by' => $this->prepared_by,
//            'approved_by' => $this->approved_by,
//            'created_at' => $this->created_at,
//            'created_by' => $this->created_by,
//            'updated_at' => $this->updated_at,
//            'updated_by' => $this->updated_by,
//            'md_approval_status' => $this->md_approval_status,
        ]);

        $query->andFilterWhere(['like', 'quotation_no', $this->quotation_no])
                ->andFilterWhere(['like', 'b.company_name', $this->project_q_client_id])
//            ->andFilterWhere(['like', 'to_pic', $this->to_pic])
//            ->andFilterWhere(['like', 'to_tel_no', $this->to_tel_no])
//            ->andFilterWhere(['like', 'to_fax_no', $this->to_fax_no])
//            ->andFilterWhere(['like', 'q_from', $this->q_from])
//            ->andFilterWhere(['like', 'q_your_ref', $this->q_your_ref])
                ->andFilterWhere(['like', 'quotation_pdf_masters.created_at', $this->created_at])
                ->andFilterWhere(['like', 'proj_title', $this->proj_title]);
//            ->andFilterWhere(['like', 'q_material_offered', $this->q_material_offered])
//            ->andFilterWhere(['like', 'q_switchboard_standard', $this->q_switchboard_standard])
//            ->andFilterWhere(['like', 'q_quotation', $this->q_quotation])
//            ->andFilterWhere(['like', 'q_delivery_ship_mode', $this->q_delivery_ship_mode])
//            ->andFilterWhere(['like', 'q_delivery_destination', $this->q_delivery_destination])
//            ->andFilterWhere(['like', 'q_delivery', $this->q_delivery])
//            ->andFilterWhere(['like', 'q_validity', $this->q_validity])
//            ->andFilterWhere(['like', 'q_payment', $this->q_payment])
//            ->andFilterWhere(['like', 'q_remark', $this->q_remark])
//            ->andFilterWhere(['like', 'filename', $this->filename])
//            ->andFilterWhere(['like', 'file_type', $this->file_type])
//            ->andFilterWhere(['like', 'file_blob', $this->file_blob])
//            ->andFilterWhere(['like', 'prepared_by_sign', $this->prepared_by_sign])
//            ->andFilterWhere(['like', 'approved_by_sign', $this->approved_by_sign]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
        ]);

        return $dataProvider;
    }
}
