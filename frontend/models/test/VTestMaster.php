<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "v_test_master".
 *
 * @property int $id
 * @property int|null $test_main_id
 * @property int $proj_id
 * @property int $panel_id
 * @property string|null $tc_ref
 * @property string|null $project_name
 * @property string|null $prod_panel_code
 * @property string|null $panel_desc
 * @property string $panel_type
 * @property string|null $test_type
 * @property int|null $test_num
 * @property int|null $panel_qty
 * @property string|null $date
 * @property string|null $venue
 * @property string|null $detail
 * @property string|null $client
 * @property string|null $elec_consultant
 * @property string|null $elec_contractor
 * @property string|null $status
 * @property string|null $tested_by
 * @property int|null $certified_by
 */
class VTestMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_test_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'test_main_id', 'proj_id', 'panel_id', 'test_num', 'panel_qty', 'certified_by'], 'integer'],
            [['panel_type'], 'required'],
            [['date'], 'safe'],
            [['detail'], 'string'],
            [['tc_ref', 'project_name', 'prod_panel_code', 'panel_desc', 'panel_type', 'test_type', 'venue', 'client', 'elec_consultant', 'elec_contractor', 'status', 'tested_by'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'test_main_id' => 'Test Main ID',
            'proj_id' => 'Proj ID',
            'panel_id' => 'Panel ID',
            'tc_ref' => 'Test Certificate',
            'project_name' => 'Project Name',
            'prod_panel_code' => 'Panel Code',
            'panel_desc' => 'Panel Description',
            'panel_type' => 'Panel Type',
            'test_type' => 'Test Type',
            'test_num' => 'Test Number',
            'panel_qty' => 'Panel Quantity',
            'date' => 'Date',
            'venue' => 'Venue',
            'detail' => 'Detail',
            'client' => 'Client',
            'elec_consultant' => 'Elec Consultant',
            'elec_contractor' => 'Elec Contractor',
            'status' => 'Status',
            'tested_by' => 'Tested By',
            'certified_by' => 'Certified By',
        ];
    }
}
