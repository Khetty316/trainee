<?php

namespace frontend\models\client;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\client\ClientReminderLetterEmailAttachment;

/**
 * ClientReminderLetterEmailAttachmentSearch represents the model behind the search form of `frontend\models\client\ClientReminderLetterEmailAttachment`.
 */
class ClientReminderLetterEmailAttachmentSearch extends ClientReminderLetterEmailAttachment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'email_id'], 'integer'],
            [['file_name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = ClientReminderLetterEmailAttachment::find();

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
            'email_id' => $this->email_id,
        ]);

        $query->andFilterWhere(['like', 'file_name', $this->file_name]);

        return $dataProvider;
    }
}
