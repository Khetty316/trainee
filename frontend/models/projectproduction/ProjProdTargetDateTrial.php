<?php

namespace frontend\models\ProjectProduction;

use Yii;
use common\models\User;

/**
 * This is the model class for table "proj_prod_target_date_trial".
 *
 * @property int $id
 * @property int|null $proj_prod_master_id
 * @property string|null $target_date
 * @property string|null $remark
 * @property int|null $created_by
 * @property string|null $created_at
 *
 * @property User $createdBy
 * @property ProjectProductionMaster $projProdMaster
 */
class ProjProdTargetDateTrial extends \yii\db\ActiveRecord {

    const REMARK_INITIAL_TARGET_DATE = 'Initial target completion date';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'proj_prod_target_date_trial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_prod_master_id', 'created_by'], 'integer'],
            [['target_date', 'created_at'], 'safe'],
            [['remark'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['proj_prod_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionMaster::className(), 'targetAttribute' => ['proj_prod_master_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proj_prod_master_id' => 'Proj Prod Master ID',
            'target_date' => 'Target Date',
            'remark' => 'Remark',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
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

    /**
     * Gets query for [[ProjProdMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjProdMaster() {
        return $this->hasOne(ProjectProductionMaster::className(), ['id' => 'proj_prod_master_id']);
    }

    public function beforeSave($insert) {
        $this->created_at = new \yii\db\Expression('NOW()');
        $this->created_by = Yii::$app->user->identity->id;
        return parent::beforeSave($insert);
    }
}
