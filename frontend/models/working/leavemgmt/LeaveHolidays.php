<?php

namespace frontend\models\working\leavemgmt;

use common\models\myTools\MyFormatter;
use Yii;
use common\models\User;

/**
 * This is the model class for table "leave_holidays".
 *
 * @property int $id
 * @property string $holiday_date
 * @property string $holiday_name
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property User $createdBy
 */
class LeaveHolidays extends \yii\db\ActiveRecord {

    const COL_HOLIDAY_DATE = 0;
    const COL_HOLIDAY_NAME = 1;
    const COL_YEAR = 1;
    const ROW_YEAR = 1;
    const START_ROW = 4;

    public $year;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'leave_holidays';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['holiday_date', 'holiday_name'], 'required'],
            [['holiday_date', 'created_at'], 'safe'],
            [['created_by'], 'integer'],
            [['holiday_name'], 'string', 'max' => 200],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'holiday_date' => 'Holiday Date',
            'holiday_name' => 'Holiday Name',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
//            $this->updated_at = new \yii\db\Expression('NOW()');
//            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public static function getByDateRange_array($dateFrom, $dateTo) {
        return \yii\helpers\ArrayHelper::map(LeaveHolidays::find()
                                ->where('holiday_date BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"')
                                ->all(), "holiday_date", "holiday_name");
    }

    public function getHolidaysForCalendar_Json() {
        $holidayList = LeaveHolidays::find()->all();
        $returnList = [];
        foreach ($holidayList as $holiday) {
            $tempArr = array(
                "title" => "$holiday->holiday_name",
                "allDay" => "true", // always set to all day
                "start" => "$holiday->holiday_date",
                "display" => 'background'
            );

            array_push($returnList, $tempArr);
        }
        return json_encode($returnList);
    }

    /**
     * Return an Array
     */
    public static function getMinMaxYear() {
        $year = (new \yii\db\Query())
                ->select(['min(year(holiday_date)) as minYear,max(YEAR(holiday_date)) as maxYear'])
                ->from('leave_holidays')
                ->one();
        $minYear = $year['minYear'] ? $year['minYear'] : date("Y");
        $maxYear = $year['maxYear'] ? $year['maxYear'] : date("Y");
        return array($minYear, $maxYear);
    }

    public function processExcel() {
        require_once Yii::getAlias('@webroot') . "/library/PHPExcel-1.8.1/Classes/PHPExcel.php";
        $inputFileType = 'CSV';
        $inputFileName = \yii\web\UploadedFile::getInstanceByName('excelFile')->tempName;
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objExcel = $objReader->load($inputFileName);

        $returnArr = [];
        foreach ($objExcel->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $this->year = $worksheet->getCellByColumnAndRow(self::COL_YEAR, self::ROW_YEAR);
            for ($row = self::START_ROW; $row <= $highestRow; $row++) {
                $excelBean = new LeaveHolidays();
                $excelBean->holiday_date = MyFormatter::fromDateTimeExcelMDY_toDateSql($worksheet->getCellByColumnAndRow(self::COL_HOLIDAY_DATE, $row)->getValue());
//                $excelBean->holiday_date = ($worksheet->getCellByColumnAndRow(self::COL_HOLIDAY_DATE, $row)->getValue());
                $excelBean->holiday_name = $worksheet->getCellByColumnAndRow(self::COL_HOLIDAY_NAME, $row)->getValue();
                if ($excelBean->holiday_date) {
                    array_push($returnArr, $excelBean);
                }
            }
        }

        return $returnArr;
    }

    public function processUpdates() {
        $post = Yii::$app->request->post();
        $this->year = $post['year'];
        $this::deleteAll('year(holiday_date)=' . $this->year);

        foreach ($post['holidayDate'] as $key => $holiday) {
            $holiday = new LeaveHolidays();
            $holiday->holiday_date = $post['holidayDate'][$key];
            $holiday->holiday_name = $post['holidayName'][$key];
            $holiday->created_by = Yii::$app->user->id;
            $holiday->save();
        }
        return true;
    }

}
