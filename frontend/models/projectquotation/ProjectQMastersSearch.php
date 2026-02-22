<?php

namespace frontend\models\projectquotation;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProjectQMastersSearch represents the model behind the search form of `frontend\models\projectquotation\ProjectQMasters`.
 */
class ProjectQMastersSearch extends VProjectQuotationMaster {

    public $total_amount;
    public $currency_sign;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'project_coordinator', 'active', 'created_by', 'updated_by'], 'integer'],
            [['amount', 'is_finalized'], 'number'],
            [['remark', 'clients'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['quotation_no', 'quotation_display_no', 'project_code', 'project_name', 'project_coordinator_fullname'], 'string', 'max' => 255],
            [['company_group_code', 'status'], 'string', 'max' => 10],
            [['total_amount'], 'number'],
            [['currency_sign'], 'string', 'max' => 5],
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
        $query = VProjectQuotationMaster::find()
                ->alias('p')
                ->select([
                    'p.*',
                    'total_amount' => 'COALESCE(SUM(r.amount), 0)',
                    'currency_sign' => 'MAX(c.currency_sign)'
                ])
                ->leftJoin('project_q_types qt', 'qt.project_id = p.id')
                ->leftJoin(
                        'project_q_revisions r',
                        'r.id = qt.active_revision_id AND r.is_active = 0'
                )
                ->leftJoin('ref_currencies c', 'c.currency_id = r.currency_id')
                ->groupBy('p.id');

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
//            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'quotation_no', $this->quotation_no])
                ->andFilterWhere(['like', 'quotation_display_no', $this->quotation_display_no])
                ->andFilterWhere(['like', 'project_code', $this->project_code])
                ->andFilterWhere(['like', 'project_coordinator_fullname', $this->project_coordinator_fullname])
                ->andFilterWhere(['like', 'project_name', $this->project_name])
                ->andFilterWhere(['like', 'clients', $this->clients])
                ->andFilterWhere(['like', 'status', $this->status])
                ->andFilterWhere(['like', 'remark', $this->remark]);

        $query->andFilterHaving(['=', 'total_amount', $this->total_amount]);

        $query->andWhere(['p.active' => true]);

        $dataProvider->sort->attributes['total_amount'] = [
            'asc' => ['total_amount' => SORT_ASC],
            'desc' => ['total_amount' => SORT_DESC],
        ];

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['id' => SORT_DESC]);
        }
        return $dataProvider;
    }
}
