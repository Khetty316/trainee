<?php

namespace frontend\models\office\pettyCash;

use Yii;
use common\models\User;

/**
 * This is the model class for table "petty_cash_request_post_attachment".
 *
 * @property int $id
 * @property int|null $pc_request_post_id
 * @property string|null $file_name
 * @property int|null $uploaded_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 *
 * @property PettyCashRequestPost $pcRequestPost
 * @property User $uploadedBy
 * @property User $deletedBy
 */
class PettyCashRequestPostAttachment extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'petty_cash_request_post_attachment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['pc_request_post_id', 'uploaded_by', 'deleted_by'], 'integer'],
            [['deleted_at'], 'safe'],
            [['file_name'], 'string', 'max' => 255],
            [['pc_request_post_id'], 'exist', 'skipOnError' => true, 'targetClass' => PettyCashRequestPost::className(), 'targetAttribute' => ['pc_request_post_id' => 'id']],
            [['uploaded_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uploaded_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'pc_request_post_id' => 'Pc Request Post ID',
            'file_name' => 'File Name',
            'uploaded_by' => 'Uploaded By',
            'deleted_at' => 'Deleted At',
            'deleted_by' => 'Deleted By',
        ];
    }

    /**
     * Gets query for [[PcRequestPost]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPcRequestPost() {
        return $this->hasOne(PettyCashRequestPost::className(), ['id' => 'pc_request_post_id']);
    }

    /**
     * Gets query for [[UploadedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUploadedBy() {
        return $this->hasOne(User::className(), ['id' => 'uploaded_by']);
    }

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy() {
        return $this->hasOne(User::className(), ['id' => 'deleted_by']);
    }
}
