<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class FactoryStaffPerformanceReports extends ActiveRecord
{
    public static function tableName()
    {
        return 'factory_staff_performance_reports';
    }

    public function rules()
    {
        return [
            [['cache_key', 'period_type', 'date_from', 'date_to', 'report_data'], 'required'],
            [['date_from', 'date_to'], 'date', 'format' => 'php:Y-m-d'],
            [['report_data'], 'string'],
            [['created_at', 'updated_at', 'is_internal_project'], 'safe'],
            [['cache_key'], 'string', 'max' => 255],
            [['period_type'], 'string', 'max' => 50],
            [['cache_key'], 'unique'],
        ];
    }

    /**
     * Get decoded report data
     */
    public function getDecodedReportData()
    {
        return json_decode($this->report_data, true);
    }

    /**
     * Set report data (will be JSON encoded)
     */
    public function setReportData($data)
    {
        $this->report_data = json_encode($data);
    }

    /**
     * Find cached report by parameters
     */
    public static function findByParameters($dateFrom, $dateTo, $isInternal)
    {
        return static::find()
            ->where([
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'is_internal_project' => $isInternal
            ])
            ->one();
    }
}