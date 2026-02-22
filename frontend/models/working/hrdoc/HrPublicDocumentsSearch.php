<?php

namespace frontend\models\working\hrdoc;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\hrdoc\HrPublicDocuments;
use common\models\myTools\MyFormatter;

/**
 * HrPublicDocumentsSearch represents the model behind the search form of `frontend\models\working\hrdoc\HrPublicDocuments`.
 */
class HrPublicDocumentsSearch extends HrPublicDocuments {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'active_sts', 'created_by', 'updated_by', 'show_alert'], 'integer'],
            [['category', 'description', 'filename', 'file_date', 'remark', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $type = '', $status = '') {
        $query = HrPublicDocuments::find();

        // add conditions that should always apply here

        switch ($type) {
            case "remind":
                $query->where("remind_date<='" . date("Y-m-d") . "' OR expiry_date<='" . date("Y-m-d") . "'");
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
            'id' => $this->id,
            'active_sts' => $this->active_sts,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'show_alert' => $this->show_alert,
        ]);

        $query->andFilterWhere(['like', 'category', $this->category])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'SUBSTRING(filename,16)', $this->filename])
                ->andFilterWhere(['=', 'file_date', $this->file_date == "" ? "" : MyFormatter::changeDateFormat_readToDB($this->file_date)])
                ->andFilterWhere(['like', 'remark', $this->remark]);

//        if ($status === 'new') {
//            $query->andWhere(['show_alert' => 1]);
//        }

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['created_at' => SORT_DESC]);
        }
        return $dataProvider;
    }
}
