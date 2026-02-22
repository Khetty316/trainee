<?php

namespace frontend\models\projectquotation;

use Yii;
use common\models\User;

/**
 * This is the model class for table "project_q_types_attachments".
 *
 * @property int $id
 * @property int $proj_q_type_id
 * @property string|null $filename
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 *
 * @property ProjectQTypes $projQType
 * @property User $createdBy
 * @property User $deletedBy
 */
class ProjectQTypesAttachments extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_q_types_attachments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_q_type_id'], 'required'],
            [['proj_q_type_id', 'created_by', 'deleted_by'], 'integer'],
            [['created_at', 'deleted_at'], 'safe'],
            [['filename'], 'string', 'max' => 255],
            [['proj_q_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQTypes::className(), 'targetAttribute' => ['proj_q_type_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proj_q_type_id' => 'Proj Q Type ID',
            'filename' => 'Filename',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'deleted_at' => 'Deleted At',
            'deleted_by' => 'Deleted By',
        ];
    }

    /**
     * Gets query for [[ProjQType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjQType() {
        return $this->hasOne(ProjectQTypes::className(), ['id' => 'proj_q_type_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy() {
        return $this->hasOne(User::className(), ['id' => 'deleted_by']);
    }

    public function beforeSave($insert) {
        $this->created_at = new \yii\db\Expression('NOW()');
        $this->created_by = Yii::$app->user->identity->id;

        return parent::beforeSave($insert);
    }
}
