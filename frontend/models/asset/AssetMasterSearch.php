<?php

namespace frontend\models\asset;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\asset\AssetMaster;
use frontend\models\common\RefAssetApprovalStatus;
/**
 * AssetMasterSearch represents the model behind the search form of `frontend\models\asset\AssetMaster`.
 */
class AssetMasterSearch extends VAssetMaster {

    public $current_user_fullname;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'asset_category', 'asset_sub_category', 'purchased_by', 'idle_sts', 'active_sts', 'created_by', 'cur_user', 'pend_user']
                , 'integer'],
            [['rental_fee', 'cost'], 'number'],
            [['asset_idx_no', 'warranty_due_date', 'created_at', 'specification', 'remarks', 'file_image',
            'file_invoice_image', 'description', 'cur_user_fullname', 'pend_user_fullname', 'own_type', 'brand',
            'model', 'condition', 'condition_desc', 'own_type_desc',
            'asset_category_name', 'asset_sub_category_name', 'purchase_by_name', 'created_by_name'],
                'safe'],
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
    public function search($params, $type = '') {
        $query = VAssetMaster::find();

        switch ($type) {

            case "assetOnHand":
                $query->where(['cur_user' => Yii::$app->user->id, 'approval_status' => RefAssetApprovalStatus::STATUS_APPROVE]);
                break;
            case "assetPendingReceive":
                $query->where(['pend_user' => Yii::$app->user->id]);
                break;
            case 'assetPendingRegister' :
                $query->where(['approval_status' => RefAssetApprovalStatus::STATUS_PENDING, 'cur_user' => Yii::$app->user->id]);
                break;
            case 'indexAssetSuper' :
                $query->where(['approval_status' => RefAssetApprovalStatus::STATUS_APPROVE]);
                break;
            case 'assetPendingRegisterSuper' :
                $query->where(['approval_status' => RefAssetApprovalStatus::STATUS_PENDING]);
                break;
            case 'assetRejectRegisterSuper' :
                $query->where(['approval_status' => RefAssetApprovalStatus::STATUS_REJECT])->orWhere(['approval_status' => RefAssetApprovalStatus::STATUS_CANCEL]);
                break;
            case 'assetAll' :
                $query->where(['active_sts' => '1']);
                break;
        }

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
            'asset_category' => $this->asset_category_name,
            'asset_sub_category' => $this->asset_sub_category_name,
            'purchased_by' => $this->purchased_by,
            'rental_fee' => $this->rental_fee,
            'idle_sts' => $this->idle_sts,
            'cost' => $this->cost,
            'warranty_due_date' => $this->warranty_due_date,
            'active_sts' => $this->active_sts,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'own_type' => $this->own_type_desc
        ]);

        $query->andFilterWhere(['like', 'asset_idx_no', $this->asset_idx_no])
                ->andFilterWhere(['like', 'file_image', $this->file_image])
                ->andFilterWhere(['like', 'file_invoice_image', $this->file_invoice_image])
                ->andFilterWhere(['like', 'purchase_by_name', $this->purchase_by_name])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'brand', $this->brand])
                ->andFilterWhere(['like', 'model', $this->model])
                ->andFilterWhere(['like', 'specification', $this->specification])
                ->andFilterWhere(['like', 'remarks', $this->remarks])
                ->andFilterWhere(['like', 'pend_user_fullname', $this->pend_user_fullname])
                ->andFilterWhere(['like', 'cur_user_fullname', $this->cur_user_fullname])
                ->andFilterWhere(['like', 'condition', $this->condition]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['id' => SORT_DESC]);
        }


        return $dataProvider;
    }

}
