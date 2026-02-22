<?php

namespace frontend\models\working\project;

use Yii;
use common\models\User;

/**
 * This is the model class for table "master_projects".
 *
 * @property string $project_code
 * @property string|null $project_name
 * @property string|null $project_description
 * @property string|null $project_image
 * @property int|null $person_in_charge
 * @property int|null $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property MasterIncomings[] $masterIncomings
 * @property User $createdBy
 * @property User $personInCharge
 * @property User $updatedBy
 * @property MiProjects[] $miProjects
 * @property PurchaseOrderMaster[] $purchaseOrderMasters
 */
class MasterProjects extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'master_projects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['project_code'], 'required'],
            [['person_in_charge', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['project_code'], 'string', 'max' => 20],
            [['project_name', 'project_description', 'project_image'], 'string', 'max' => 255],
            [['project_code'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['person_in_charge'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['person_in_charge' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'project_code' => 'Project Code',
            'project_name' => 'Project Name',
            'project_description' => 'Project Description',
            'project_image' => 'Project Image',
            'person_in_charge' => 'Person In Charge',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[MasterIncomings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMasterIncomings() {
        return $this->hasMany(MasterIncomings::className(), ['project_code' => 'project_code']);
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
     * Gets query for [[PersonInCharge]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonInCharge() {
        return $this->hasOne(User::className(), ['id' => 'person_in_charge']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[MiProjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMiProjects() {
        return $this->hasMany(MiProjects::className(), ['project_code' => 'project_code']);
    }

    /**
     * Gets query for [[PurchaseOrderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseOrderMasters() {
        return $this->hasMany(PurchaseOrderMaster::className(), ['project_code' => 'project_code']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        } else {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public static function getActiveDropDownList() {
        return \yii\helpers\ArrayHelper::map(MasterProjects::find()->all(), "project_code", function($data) {
                    return '' . $data['project_code'] . ' - ' . $data['project_name'];
                });
    }

    public static function copyFromProjectMaster($project) {

        $MProj = MasterProjects::findOne($project->proj_code);
        if ($MProj) {
            
        } else {
            $MProj = new MasterProjects();
            $MProj->project_code = $project->proj_code;
            $MProj->project_name = $project->title_short;
            $MProj->project_description = $project->service;
            $MProj->created_by = Yii::$app->user->id;
            $MProj->save();
        }

        return \yii\helpers\ArrayHelper::map(MasterProjects::find()->all(), "project_code", function($data) {
                    return '' . $data['project_code'] . ' - ' . $data['project_name'];
                });
    }

    public static function getAutoCompleteList() {
        $list = MasterProjects::find()
                ->select(['project_code as value', 'project_code as id', 'CONCAT(project_code," - ",project_name) as label'])
                ->asArray()
                ->all();
        return $list;
    }

}
