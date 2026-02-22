<?php

namespace frontend\models\working\hrdoc;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\working\hrdoc\HrEmployeeDocuments;
use common\models\myTools\MyFormatter;

/**
 * HrEmployeeDocumentSearch represents the model behind the search form of `frontend\models\working\hrdoc\HrEmployeeDocuments`.
 */
class HrEmployeeDocumentSearch extends HrEmployeeDocuments {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'hr_doctype', 'active_sts', 'is_read'], 'integer'],
            [['filename', 'read_at', 'created_at', 'employee_id', 'created_by'], 'safe'],
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
        $query = HrEmployeeDocuments::find();

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

        $query->join("LEFT JOIN", 'user as staff', 'hr_employee_documents.employee_id = staff.id');
        $query->join('LEFT JOIN', 'user as createdBy', 'hr_employee_documents.created_by = createdBy.id');

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'hr_doctype' => $this->hr_doctype,
            'active_sts' => $this->active_sts,
            'is_read' => $this->is_read,
        ]);

        $query->andFilterWhere(['like', 'filename', $this->filename])
                ->andFilterWhere(['like', 'concat(staff.staff_id," ",staff.fullname)', $this->employee_id])
                ->andFilterWhere(['like', 'read_at', $this->read_at == "" ? "" : MyFormatter::changeDateFormat_readToDB($this->read_at)])
                ->andFilterWhere(['like', 'hr_employee_documents.created_at', $this->created_at == "" ? "" : MyFormatter::changeDateFormat_readToDB($this->created_at)])
                ->andFilterWhere(['like', 'createdBy.fullname', $this->created_by]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['id' => SORT_DESC]);
        }

        return $dataProvider;
    }

}
