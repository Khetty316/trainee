<?php

namespace frontend\models\client;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\client\ClientReminderLetterEmails;
use yii\jui\DatePicker;

/**
 * ClientReminderLetterEmailsSearch represents the model behind the search form of `frontend\models\client\ClientReminderLetterEmails`.
 */
class ClientReminderLetterEmailsSearch extends ClientReminderLetterEmails {

    public $sent_by_name;
    public $client_code;
    public $company_name;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['client_code', 'company_name'], 'safe'],
            [['id', 'template_id', 'client_id', 'sent_by', 'status'], 'integer'],
            [['sender', 'recipient', 'Cc', 'Bcc', 'subject', 'content', 'sent_at', 'sent_by_name', 'created_at'], 'safe'],
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
        $query = ClientReminderLetterEmails::find()
                ->joinWith(['senderUser sentUser']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $dataProvider->sort->attributes['sent_by_name'] = [
            'asc' => ['sentUser.fullname' => SORT_ASC],
            'desc' => ['sentUser.fullname' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['client_code'] = [
            'asc' => ['clients.client_code' => SORT_ASC],
            'desc' => ['clients.client_code' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['company_name'] = [
            'asc' => ['clients.company_name' => SORT_ASC],
            'desc' => ['clients.company_name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->joinWith(['client']);

        $query->andFilterWhere([
            'id' => $this->id,
            'template_id' => $this->template_id,
            'client_id' => $this->client_id,
            'sent_by' => $this->sent_by,
            'client_reminder_letter_emails.status' => $this->status,
        ]);

        $query->andFilterWhere([
            'like',
            'clients.client_code',
            $this->client_code
        ]);

        if (!empty($this->company_name)) {

            $query->andWhere([
                'like',
                'clients.company_name',
                $this->company_name . '%',
                false
            ]);
        }

        if (!empty($this->sent_at)) {

            $date = date(
                    'Y-m-d',
                    strtotime(str_replace('/', '-', $this->sent_at))
            );

            $query->andWhere([
                'between',
                'client_reminder_letter_emails.sent_at',
                $date . ' 00:00:00',
                $date . ' 23:59:59'
            ]);
        }

        if (!empty($this->created_at)) {

            $date = date(
                    'Y-m-d',
                    strtotime(str_replace('/', '-', $this->created_at))
            );

            $query->andWhere([
                'between',
                'client_reminder_letter_emails.created_at',
                $date . ' 00:00:00',
                $date . ' 23:59:59'
            ]);
        }

        $query->andFilterWhere(['like', 'sender', $this->sender])
                ->andFilterWhere(['like', 'recipient', $this->recipient])
                ->andFilterWhere(['like', 'Cc', $this->Cc])
                ->andFilterWhere(['like', 'Bcc', $this->Bcc])
                ->andFilterWhere(['like', 'content', $this->content]);

        if (!empty($this->subject)) {

            if (strlen(trim($this->subject)) >= 2) {

                $query->andFilterWhere([
                    'like',
                    'subject',
                    $this->subject
                ]);
            } else {
                $query->andWhere('0=1');
            }
        }
        if (!empty($this->sent_by_name)) {

            $query->andWhere([
                'like',
                'sentUser.fullname',
                $this->sent_by_name . '%',
                false
            ]);
        }
        return $dataProvider;
    }
}
