<?php

namespace frontend\models\client;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\client\ClientReminderLetterTemplate;

/**
 * ClientReminderLetterTemplateSearch represents the model behind the search form of `frontend\models\client\ClientReminderLetterTemplate`.
 */
class ClientReminderLetterTemplateSearch extends ClientReminderLetterTemplate {

    public $creator_name;
    public $updater_name;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['creator_name', 'updater_name'], 'safe'],
            [['id', 'active_sts',], 'integer'],
            [['letter_name', 'content', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'safe'],
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
        $query = ClientReminderLetterTemplate::find();
        $query->joinWith(['creator' => function ($q) {$q->alias('c');}]);
        $query->joinWith(['updater' => function ($q) {$q->alias('u');}]);

        $dataProvider = new ActiveDataProvider(['query' => $query,]);

        $dataProvider->sort->attributes['creator_name'] = [
            'asc' => ['c.username' => SORT_ASC],
            'desc' => ['c.username' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['updater_name'] = [
            'asc' => ['u.username' => SORT_ASC],
            'desc' => ['u.username' => SORT_DESC],
        ];

        $this->load($params);

        if (!empty($this->updated_at)) {
            $date = \DateTime::createFromFormat('M j, Y', $this->updated_at);

            $start = $date->format('Y-m-d') . ' 00:00:00';
            $end = $date->format('Y-m-d') . ' 23:59:59';

            $query->andWhere(['between', 'client_reminder_letter_template.updated_at', $start, $end]);
        }

        if (!empty($this->created_at)) {
            $date = \DateTime::createFromFormat('M j, Y', $this->created_at);

            if ($date) {
                $start = $date->format('Y-m-d') . ' 00:00:00';
                $end = $date->format('Y-m-d') . ' 23:59:59';

                $query->andWhere(['between', 'client_reminder_letter_template.created_at', $start, $end]);
            }
        }

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id, 'active_sts' => $this->active_sts, 'updated_by' => $this->updated_by,]);

        $query->andFilterWhere(['like', 'c.username', $this->creator_name])
                ->andFilterWhere(['like', 'u.username', $this->updater_name]);

        if (!empty($this->letter_name)) {

            $query->andFilterWhere(['like', 'LOWER(letter_name)', strtolower(trim($this->letter_name))]);
        }

        if (!empty($this->content)) {

            if (strlen(trim($this->content)) >= 2) {

                $query->andFilterWhere(['like', 'content', $this->content]);
            } else {

                $query->andWhere('0=1');
            }
        }
        return $dataProvider;
    }
}
