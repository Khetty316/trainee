<?php

namespace frontend\models\test;

use Yii;
use common\models\User;
use frontend\models\test\TestMaster;

/**
 * This is the model class for table "test_custom_content".
 *
 * @property int $id
 * @property int|null $test_form_id
 * @property string|null $content
 * @property int|null $content_order
 * @property int|null $is_deleted 0 = no, 1 = yes
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property User $createdBy
 */
class TestCustomContent extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_custom_content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['test_form_id', 'content_order', 'is_deleted', 'created_by'], 'integer'],
            [['content'], 'string'],
            [['created_at'], 'safe'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'test_form_id' => 'Test Form ID',
            'content' => 'Content',
            'content_order' => 'Content Order',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function beforeSave($insert) {
        $this->created_at = new \yii\db\Expression('NOW()');
        $this->created_by = Yii::$app->user->identity->id;
        return parent::beforeSave($insert);
    }

    /**
     * Get custom contents for a specific form
     */
    public function getCustomContents($formName, $formId) {
        $contents = self::find()
                ->where(['test_form_id' => $formId])
                ->andWhere(['test_form_name' => $formName])
                ->orderBy('content_order ASC')
                ->all();
        
        return $contents;
    }

    /**
     * Save custom contents with transaction support
     */
    public function saveCustomContents($formId, $formName, $processedContent) {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            // First, delete existing records
            self::deleteAll(['test_form_id' => $formId]);

            // Save new content
            foreach ($processedContent as $contentData) {
                $customContent = new self();
                $customContent->test_form_id = $formId;
                $customContent->test_form_name = $formName;
                $customContent->content = $contentData['content'];
                $customContent->content_order = $contentData['content_order'];

                if (!$customContent->save()) {
                    throw new \Exception('Failed to save content: ' . json_encode($customContent->errors));
                }
            }

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw new \Exception('Database error: ' . $e->getMessage());
        }
    }

    /**
     * Get content as array for form display
     */
    public static function getContentArray($formId) {
        $contents = self::find()
                ->where(['test_form_id' => $formId])
                ->andWhere(['is_active' => 1])
                ->orderBy('content_order ASC')
                ->all();

        $contentArray = [];
        foreach ($contents as $content) {
            $contentArray[] = $content->content;
        }

        return empty($contentArray) ? [''] : $contentArray;
    }
}
