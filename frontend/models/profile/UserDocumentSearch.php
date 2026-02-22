<?php

namespace frontend\models\profile;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\profile\UserDocuments;

/**
 * UserDocumentSearch represents the model behind the search form of `frontend\models\profile\UserDocuments`.
 */
class UserDocumentSearch extends UserDocuments {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'user_id', 'created_by', 'udpated_by'], 'integer'],
            [['doctype_code', 'doc_file_link', 'doc_date', 'doc_expiry_date', 'created_at', 'updated_at'], 'safe'],
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
        $query = UserDocuments::find();

        // add conditions that should always apply here
        switch ($type) {
            case "viewUserDocuments": 
                $query->where("user_id = ". \Yii::$app->user->identity->id);
                break;
            case "viewUserHrDocuments": 
                $query->where("user_id = ". \Yii::$app->user->identity->id);
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
            'user_id' => $this->user_id,
            'doc_date' => $this->doc_date,
            'doc_expiry_date' => $this->doc_expiry_date,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'udpated_by' => $this->udpated_by,
        ]);

        $query->andFilterWhere(['like', 'doctype_code', $this->doctype_code])
                ->andFilterWhere(['like', 'doc_file_link', $this->doc_file_link]);

        return $dataProvider;
    }

}
