<?php

namespace frontend\models\asset;

use Yii;
use common\models\myTools\MyFormatter;
/**
 * This is the model class for table "asset_service".
 *
 * @property int $id
 * @property int $asset_id
 * @property string|null $service_date
 * @property string|null $service_remark
 * @property string|null $next_service_date
 * @property int $active_status
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property AssetMaster $asset
 */
class AssetService extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'asset_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['asset_id','service_date'], 'required'],
            [['asset_id', 'active_status', 'created_by'], 'integer'],
            [['service_date', 'next_service_date', 'created_at'], 'safe'],
            [['service_remark'], 'string'],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => AssetMaster::className(), 'targetAttribute' => ['asset_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'asset_id' => 'Asset ID',
            'service_date' => 'Service Date',
            'service_remark' => 'Service Remark',
            'next_service_date' => 'Next Service Date',
            'active_status' => 'Active Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[Asset]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsset() {
        return $this->hasOne(AssetMaster::className(), ['id' => 'asset_id']);
    }

    public function processAndSave() {
        if ($this->service_date != "") {
            $this->service_date = MyFormatter::fromDateRead_toDateSQL($this->service_date);
        }
        if ($this->next_service_date != "") {
            $this->next_service_date = MyFormatter::fromDateRead_toDateSQL($this->next_service_date);
        }
        $this->created_by = Yii::$app->user->id;

        return $this->save();
    }

}
