<?php

namespace frontend\models\working\leavemgmt;

use yii\base\Model;
use Yii;

require Yii::getAlias('@webroot') . "/library/PHPExcel-1.8.1/Classes/PHPExcel.php";
require_once Yii::getAlias('@webroot') . "/library/PHPExcel-1.8.1/Classes/PHPExcel/IOFactory.php";

class LeaveEntitlementExcelModel extends Model {

//
//    public $scannedFile;
//    public $ann_bring_forward = 0.0;
//    public $ann_entitlement = 0.0;
//    public $ann_available = 0.0;
//    public $ann_taken = 0.0;
//    public $ann_balance = 0.0;
//    public $sick_entitlement = 0.0;
//    public $sick_taken = 0.0;
//    public $sick_balance = 0.0;

    const COL_STAFF_ID = 1;
    const COL_STAFF_FULLNAME = 2;
    const COL_ENT_ID = 3;
    const COL_USER = 4;
    const COL_BROUGHT = 5;
    const COL_ANN = 6;
    const COL_SICK = 7;
    const COL_YEAR = 2;
    const ROW_YEAR = 1;
    const START_ROW = 4;

    public $year;
    public $userId;
    public $staffId;
    public $userFullname;
    public $leaveEntitleId;
    public $daysBroughtForward;
    public $daysAnnual;
    public $daysSick;
    public $userBean;
    public $leaveEntitlementBean;
    public $processMethod;  // Insert / Edit / No Changes

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
//            [['ann_bring_forward'],'number']
//            ['start_date', 'date', 'timestampAttribute' => 'start_date'],
//            ['end_date', 'date', 'timestampAttribute' => 'end_date'],
//            ['start_date', 'compare', 'compareAttribute' => 'end_date', 'operator' => '<','enableClientValidation' => false],
        ];
    }
    

    public function processExcel() {

        $inputFileType = 'CSV';
        $inputFileName = \yii\web\UploadedFile::getInstanceByName('excelFile')->tempName;
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objExcel = $objReader->load($inputFileName);

        $returnArr = [];
//        $file = \yii\web\UploadedFile::getInstanceByName('excelFile');
//        $objExcel = \PHPExcel_IOFactory::load($file->tempName);
        foreach ($objExcel->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $year = $worksheet->getCellByColumnAndRow(self::COL_YEAR, self::ROW_YEAR);
            for ($row = self::START_ROW; $row <= $highestRow; $row++) {
                $excelBean = new LeaveEntitlementExcelModel();
                $excelBean->year = $year;
                $excelBean->leaveEntitleId = $worksheet->getCellByColumnAndRow(self::COL_ENT_ID, $row)->getValue();
                $excelBean->userId = $worksheet->getCellByColumnAndRow(self::COL_USER, $row)->getValue();
                $excelBean->daysBroughtForward = $worksheet->getCellByColumnAndRow(self::COL_BROUGHT, $row)->getValue();
                $excelBean->daysAnnual = $worksheet->getCellByColumnAndRow(self::COL_ANN, $row)->getValue();
                $excelBean->daysSick = $worksheet->getCellByColumnAndRow(self::COL_SICK, $row)->getValue();

                $excelBean->leaveEntitlementBean = LeaveEntitlement::find()->where(["user_id" => $excelBean->userId, "year" => $year])->asArray()->one(); //->where(["id"=>$entitleId,"year"=>$year])->one();
                if(!$excelBean->leaveEntitlementBean){
                    $excelBean->leaveEntitlementBean= new LeaveEntitlement();
                }
                $excelBean->userBean = \common\models\User::findOne($worksheet->getCellByColumnAndRow(self::COL_USER, $row)->getValue());
                if ($excelBean['leaveEntitlementBean']['id']) {
                    $excelBean->processMethod = "edit";
                } else {
                    $excelBean->processMethod = "add";
                }
                array_push($returnArr, $excelBean);
            }
        }

        return $returnArr;
    }

    public function processUpdates() {
        $post = Yii::$app->request->post();
        foreach ($post['action'] as $key => $action) {
            $leaveEntId = $post['leaveEntitleId'][$key];
            $brought = $post['newDaysBroughtForward'][$key];
            $annual = $post['newDaysAnnual'][$key];
            $sick = $post['newDaysSick'][$key];
            $userId = $post['userId'][$key];
            $year = $post['year'][$key];
            if ($action == "edit") {
                $leaveEntitlement = LeaveEntitlement::findOne($leaveEntId);
                $leaveEntitlement->updateRecord($brought, $annual, $sick, Yii::$app->user->id);
            } else if ($action == "add") {
                $leaveEntitlement = new LeaveEntitlement();
                $leaveEntitlement->newRecord($userId, $year, $brought, $annual, $sick, Yii::$app->user->id);
            }
            $this->year = $year;
        }
        return true;
    }

}
