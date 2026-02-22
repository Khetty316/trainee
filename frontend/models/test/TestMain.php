<?php

namespace frontend\models\test;

use Yii;
use common\models\User;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use frontend\models\test\TestMaster;
use frontend\models\test\TestTemplate;

/**
 * This is the model class for table "test_main".
 *
 * @property int $id
 * @property int|null $panel_id
 * @property string|null $test_type
 * @property string|null $client
 * @property string|null $elec_consultant
 * @property string|null $elec_contractor
 * @property int|null $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectProductionPanels $panel
 * @property User $createdBy
 * @property User $updatedBy
 * @property TestMaster[] $testMasters
 */
class TestMain extends \yii\db\ActiveRecord {

    const TEST_FAT_TITLE = '(FAT) Factory Acceptance Testing';
    const TEST_ITP_TITLE = '(ITP) Internal Test Plan';
    const TEST_LIST = [self::TEST_ITP_TITLE => self::TEST_ITP_TITLE, self::TEST_FAT_TITLE => self::TEST_FAT_TITLE];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'test_main';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['panel_id', 'test_type', 'client', 'elec_consultant', 'elec_contractor', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'default', 'value' => null],
            [['panel_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['test_type', 'client', 'elec_consultant', 'elec_contractor'], 'string', 'max' => 255],
            [['panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanels::className(), 'targetAttribute' => ['panel_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'panel_id' => 'Panel ID',
            'test_type' => 'Test Type',
            'client' => 'Client',
            'elec_consultant' => 'Elec Consultant',
            'elec_contractor' => 'Elec Contractor',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Panel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPanel() {
        return $this->hasOne(ProjectProductionPanels::className(), ['id' => 'panel_id']);
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[TestMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTestMasters() {
        return $this->hasMany(TestMaster::className(), ['test_main_id' => 'id']);
    }

    public function getClient() {
        return $this->hasOne(\frontend\models\client\Clients::class, ['id' => 'client']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
            $this->status = 1;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    public static function getDropDownListType($id) {
        $main1 = TestMain::findOne($id);
        $main2 = null;
        if ($main1->test_type == self::TEST_ITP_TITLE) {
            $main2 = TestMain::findOne($id + 1);
        } else {
            $main2 = TestMain::findOne($id - 1);
        }
        $mains = [$main1, $main2];
        return \yii\helpers\ArrayHelper::map($mains, "id", "test_type");
    }

}
