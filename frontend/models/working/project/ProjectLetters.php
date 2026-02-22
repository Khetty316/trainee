<?php

namespace frontend\models\working\project;

use Yii;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;

/**
 * This is the model class for table "project_letters".
 *
 * @property int $id
 * @property int $project_id
 * @property string $letter_type incoming/outgoing
 * @property string|null $description
 * @property string|null $file
 * @property string|null $date
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property ProjectMaster $project
 */
class ProjectLetters extends \yii\db\ActiveRecord {

    public $scannedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_letters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['project_id', 'letter_type'], 'required'],
            [['project_id', 'created_by'], 'integer'],
            [['file'], 'string'],
            [['date', 'created_at'], 'safe'],
            [['letter_type'], 'string', 'max' => 20],
            [['description'], 'string', 'max' => 255],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectMaster::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'letter_type' => 'Letter Type',
            'description' => 'Description',
            'file' => 'File',
            'date' => 'Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject() {
        return $this->hasOne(ProjectMaster::className(), ['id' => 'project_id']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public function processAndSave() {
        if ($this->date) {
            $this->date = MyFormatter::changeDateFormat_readToDB($this->date);
        }

        $projectCode = $this->project->proj_code;

        if ($this->validate() && $this->scannedFile) {
            $filePath = Yii::$app->params['project_file_path'] . $projectCode . '/';
            if ($this->letter_type == "incoming") {
                $filePath .= Yii::$app->params['proj_letter_incoming_folder'] . '/';
            } else {
                $filePath .= Yii::$app->params['proj_letter_outgoing_folder'] . '/';
            }

            if ($this->file && file_exists($filePath . $this->file)) {
                unlink($filePath . $this->file);
            }
            $this->file = date('Ymdhis', time()) . '-' . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
            MyCommonFunction::saveFile($this->scannedFile, $filePath, $this->file);
        }

        // Update vs Save
        if ($this->id) {
            $status = $this->update(false);
            \common\models\myTools\FlashHandler::success("Letter updated!");
        } else {
            $status = $this->save();
            \common\models\myTools\FlashHandler::success("Letter added!");
        }

        return true;
    }

}
