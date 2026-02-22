<?php

namespace frontend\models\working\project;

use Yii;
use frontend\models\working\project\ProspectMaster;
use common\models\myTools\MyCommonFunction;

/**
 * This is the model class for table "prospect_master_scope".
 *
 * @property int $id
 * @property int $master_prospect
 * @property string|null $scope
 * @property float|null $amount
 * @property string|null $attachment
 * @property string $created_at
 * @property int|null $created_by
 * @property string $updated_at
 * @property int|null $updated_by
 *
 * @property ProspectDetailRevisionScope[] $prospectDetailRevisionScopes
 * @property ProspectMaster $masterProspect
 */
class ProspectMasterScope extends \yii\db\ActiveRecord {

    public $scannedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'prospect_master_scope';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['master_prospect', 'scope', 'amount'], 'required'],
            [['master_prospect', 'created_by', 'updated_by'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['scope', 'attachment'], 'string', 'max' => 255],
            [['master_prospect'], 'exist', 'skipOnError' => true, 'targetClass' => ProspectMaster::className(), 'targetAttribute' => ['master_prospect' => 'id']],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
            ['scannedFile', 'file', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'master_prospect' => 'Master Prospect',
            'scope' => 'Scope',
            'amount' => 'Amount',
            'attachment' => 'Attachment',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProspectDetailRevisionScopes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProspectDetailRevisionScopes() {
        return $this->hasMany(ProspectDetailRevisionScope::className(), ['prospect_master_scope_id' => 'id']);
    }

    /**
     * Gets query for [[MasterProspect]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMasterProspect() {
        return $this->hasOne(ProspectMaster::className(), ['id' => 'master_prospect']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public function processAndSave() {
        $this->save();

        if ($this->validate() && $this->scannedFile) {
            $filePath = Yii::$app->params['project_file_path'] . 'NPL' . $this->master_prospect . '/scopeMaster';
            MyCommonFunction::mkDirIfNull($filePath);

            $this->attachment = $this->id . "_" . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;

            $filePath .= '/' . $this->attachment;
            $this->scannedFile->saveAs($filePath);
            $this->update(false);
        }
        return true;
    }

    public static function getDistinctScope() {
        return ProspectMasterScope::find()
                        ->select(['scope as value', 'scope as id', 'scope as label'])
                        ->orderBy(["scope" => SORT_ASC])
                        ->distinct(true)
                        ->asArray()
                        ->all();
    }

}
