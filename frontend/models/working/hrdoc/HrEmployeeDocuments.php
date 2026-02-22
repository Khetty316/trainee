<?php

namespace frontend\models\working\hrdoc;

use Yii;
use common\models\User;
use frontend\models\common\RefHrDoctypes;
use common\models\myTools\MyCommonFunction;

/**
 * This is the model class for table "hr_employee_documents".
 *
 * @property int $id
 * @property int $hr_doctype
 * @property int $employee_id
 * @property int $active_sts
 * @property int $is_read
 * @property string|null $read_at
 * @property string $filename
 * @property string $created_at
 * @property int $created_by
 *
 * @property RefHrDoctypes $hrDoctype
 * @property User $employee
 * @property User $createdBy
 */
class HrEmployeeDocuments extends \yii\db\ActiveRecord {

    public $scannedFile;

    const PAYSLIP_REF_DOC_ID = 8;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'hr_employee_documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['hr_doctype', 'employee_id', 'filename', 'created_by'], 'required'],
            [['hr_doctype', 'employee_id', 'active_sts', 'is_read', 'created_by'], 'integer'],
            [['read_at', 'created_at'], 'safe'],
            [['filename'], 'string', 'max' => 255],
            [['hr_doctype'], 'exist', 'skipOnError' => true, 'targetClass' => RefHrDoctypes::className(), 'targetAttribute' => ['hr_doctype' => 'doc_type_id']],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['employee_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            ['scannedFile', 'file', 'maxFiles' => 0, 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg'], 'skipOnEmpty' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'hr_doctype' => 'Document Type',
            'employee_id' => 'Employee',
            'active_sts' => 'Active?',
            'is_read' => 'Read?',
            'read_at' => 'Read At',
            'filename' => 'Document',
            'created_at' => 'Created / Uploaded At',
            'created_by' => 'Created / Uploaded By',
        ];
    }

    /**
     * Gets query for [[HrDoctype]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHrDoctype() {
        return $this->hasOne(RefHrDoctypes::className(), ['doc_type_id' => 'hr_doctype']);
    }

    /**
     * Gets query for [[Employee]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee() {
        return $this->hasOne(User::className(), ['id' => 'employee_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

//    public function beforeSave($insert) {
//        if ($this->isNewRecord) {
//            $this->created_at = new CDbExpression('NOW()');
//        }
//        if (!$this->isNewRecord) {
//            $this->updated_at = new Expression('NOW()');
//        }
//        return parent::beforeSave($insert);
//    }

    /**
     * Save Function
     */
    public function processAndSave() {

        if ($this->scannedFile) {
            $errorMsg = "";
            foreach ($this->scannedFile as $file) {

                $employeeId = $this->getStaffIdFromFilename($file->name);
                if ($employeeId == null) {
                    $errorMsg .= "Could not found staff id from filename given: " . $file->name . "<br/>";
                    continue;
                }

                //save file
                $filePath = Yii::$app->params['personaldocument_file_path'] . "$employeeId/";
                MyCommonFunction::mkDirIfNull($filePath);
                $file->saveAs($filePath . $file->name);

                $newDoc = new HrEmployeeDocuments();
                $newDoc->hr_doctype = $this->hr_doctype;
                $newDoc->employee_id = $employeeId;
                $newDoc->filename = $file->name;
                $newDoc->created_by = Yii::$app->user->id;
                $newDoc->save();
            }
        }

        if ($errorMsg != '') {
            \common\models\myTools\FlashHandler::err($errorMsg);
            return false;
        }
        return true;
    }

    /**
     * Save Function for payslip module
     */
    public function processAndSaveSingle($employeeId, $dir, $filename) {
        $this->hr_doctype = $this::PAYSLIP_REF_DOC_ID;
        $this->employee_id = $employeeId;
        $this->filename = $filename;
        $this->file_type = filetype($dir . $filename);
        $this->file_size = filesize($dir . $filename);
        $this->file_blob = file_get_contents($dir . $filename);
        $this->created_by = Yii::$app->user->id;
        return $this->save();
    }

    public function generateRecord($hrDoctypeId, $employeeId, $filename) {
        $this->hr_doctype = $hrDoctypeId;
        $this->employee_id = $employeeId;
        $this->filename = $filename;
        $this->active_sts = 1;
        $this->is_read = 0;
        $this->created_by = Yii::$app->user->identity->id;
        return $this->save();
    }

    public static function setToRead($id) {
        $model = HrEmployeeDocuments::findOne($id);
        $model->is_read = 1;
        $model->read_at = new \yii\db\Expression('NOW()');
        if ($model->update(false)) {
            return true;
        } else {
            \common\models\myTools\Mydebug::dumpFileW($model->getErrors());
            return false;
        }
//        return $model->update(false);
    }

    private function getStaffIdFromFilename($filename) {
        $staffId = substr($filename, 0, strpos($filename, '-'));
        $staff = User::find()->where('staff_id="' . $staffId . '"')->one();
        return $staff ? $staff->id : null;
    }

}
