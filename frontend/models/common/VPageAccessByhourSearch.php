<?php

namespace frontend\models\common;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\common\VPageAccessByhour;

/**
 * AuditTrailPageAccessSearch represents the model behind the search form of `frontend\models\common\AuditTrailPageAccess`.
 */
class VPageAccessByhourSearch extends VPageAccessByhour {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['times'], 'number'],
            [['theDate'], 'safe'],
            [['theTime'], 'string', 'max' => 13],
            [['fullname'], 'string', 'max' => 255],
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
        $query = VPageAccessByhour::find();

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
//            'id' => $this->id,
        ]);


        $query->andFilterWhere(['like', 'fullname', $this->fullname])
                ->andFilterWhere(['like', 'theDate', $this->theDate])
                ->andFilterWhere(['like', 'theTime', $this->theTime]);

        if (!array_key_exists('sort', $params)) {
            $query->orderBy(['fullname' => SORT_ASC,'theDate'=>SORT_DESC,'theTime'=>SORT_ASC]);
        }
        return $dataProvider;
    }

}
