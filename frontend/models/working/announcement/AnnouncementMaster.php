<?php

namespace frontend\models\working\announcement;

use Yii;

/**
 * This is the model class for table "announcement_master".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $content
 * @property string|null $announce_date
 * @property string|null $announce_by
 * @property string|null $effective_date_from
 * @property string|null $effective_date_to
 * @property int $active
 * @property string $created_at
 * @property int|null $created_by
 */
class AnnouncementMaster extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'announcement_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['content'], 'string'],
            [['announce_date', 'effective_date_from', 'effective_date_to', 'created_at'], 'safe'],
            [['active', 'created_by'], 'integer'],
            [['title', 'announce_by'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'announce_date' => 'Announce Date',
            'announce_by' => 'Announce By',
            'effective_date_from' => 'Effective Date From',
            'effective_date_to' => 'Effective Date To',
            'active' => 'Active',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

}
