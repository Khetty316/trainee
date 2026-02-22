<?php

namespace frontend\models\working\hrdoc;

class HrEmployeeDocumentForm extends \yii\base\Model {

    public $hrDocType;
    public $hrScannedFiles;

    public function rules() {
        return [
        [['hr_doctype', 'employee_id', 'filename', 'created_by'], 'required'],
        [['hr_doctype', 'employee_id', 'active_sts', 'is_read', 'created_by'], 'integer'],
        [['read_at', 'created_at'], 'safe'],
        [['filename'], 'string', 'max' => 255],
        [['hr_doctype'], 'exist', 'skipOnError' => true, 'targetClass' => RefHrDoctypes::className(), 'targetAttribute' => ['hr_doctype' => 'doc_type_id']],
        [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['employee_id' => 'id']],
        [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];

        function attributeLabels() {
            return [
                'id' => 'ID',
                'hr_doctype' => 'Doc Type',
                'employee_id' => 'Staff',
                'filename' => 'Filename',
                'active_sts' => 'Active?',
                'is_read' => 'Read?',
                'read_at' => 'Read At',
                'created_at' => 'Created / Uploaded At',
                'created_by' => 'Created / Uploaded By',
            ];
        }

    }

}
