<?php

namespace frontend\models\office\prodOtMealRecord;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\office\prodOtMealRecord\ProdOtMealRecordDetail;

/**
 * ProdOtMealRecordDetailSearch represents the model behind the search form of `frontend\models\office\prodOtMealRecord\ProdOtMealRecordDetail`.
 */
class ProdOtMealRecordDetailSearch extends ProdOtMealRecordDetail {

    public $staff;
    public $receipt_date_display;
    public $created_at_display;
    public $updated_at_display;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'prod_ot_meal_record_master_id', 'total_staff', 'deleted_by', 'created_by', 'updated_by'], 'integer'],
            [['receipt_date', 'created_at', 'updated_at', 'staff', 'receipt_date_display', 'created_at_display', 'updated_at_display'], 'safe'],
            [['receipt_total_amount'], 'number'],
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
    public function search($params, $masterId) {
        $subQuery = (new \yii\db\Query())
                ->select(['prod_ot_meal_record_detail_id', 'GROUP_CONCAT(u.fullname SEPARATOR "; ") AS staff'])
                ->from('prod_ot_meal_record_item i')
                ->leftJoin('user u', 'u.id = i.user_id')
                ->groupBy('prod_ot_meal_record_detail_id');

        $query = ProdOtMealRecordDetail::find()
                ->alias('d')
                ->where(['d.prod_ot_meal_record_master_id' => $masterId])
                ->andWhere(['d.deleted_by' => null])
                ->leftJoin(['s' => $subQuery], 's.prod_ot_meal_record_detail_id = d.id')
                ->select(['d.*', 's.staff AS staff']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Filtering - make sure to use table aliases
        $query->andFilterWhere([
            'd.id' => $this->id,
            'd.receipt_date' => $this->receipt_date,
//            'd.receipt_total_amount' => $this->receipt_total_amount,
            'd.total_staff' => $this->total_staff,
//            'd.created_at' => $this->created_at,
//            'd.updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 's.staff', $this->staff]);
        $query->andFilterWhere(['like', 'd.created_at', $this->created_at]);
        $query->andFilterWhere(['like', 'd.updated_at', $this->updated_at]);
        $query->andFilterWhere(['like', 'd.receipt_total_amount', $this->receipt_total_amount]);

        $dataProvider->setSort([
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
        ]);

        return $dataProvider;
    }
}
