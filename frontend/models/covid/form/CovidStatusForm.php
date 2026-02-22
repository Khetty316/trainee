<?php

namespace frontend\models\covid\form;

use Yii;
use common\models\User;
use common\models\myTools\MyFormatter;
use frontend\models\covid\form\RefCovidPlaces;
use frontend\models\covid\form\RefCovidSymptoms;
use frontend\models\covid\testkit\CovidTestkitRecord;

/**
 * This is the model class for table "covid_status_form".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $created_at
 * @property float $body_temperature
 * @property float $spo2
 * @property int $self_vaccine_dose
 * @property string|null $self_symptom_list multiple options seperated by,
 * @property string|null $self_symptom_other
 * @property string|null $self_place_list
 * @property string|null $self_place_other
 * @property int $self_test_is
 * @property string|null $self_test_date
 * @property string|null $self_test_reason
 * @property int|null $self_test_kit_type Self Purchased / From Company
 * @property int|null $self_covid_kit_id
 * @property string|null $self_test_result
 * @property string|null $self_test_result_attachment
 * @property int|null $other_how_many
 * @property int|null $other_vaccine_two_dose
 * @property string|null $other_symptom_list
 * @property string|null $other_symptom_other
 * @property string|null $other_place_list
 * @property string|null $other_place_other
 * @property int $other_test_is
 * @property string|null $other_test_reason
 * @property string|null $other_test_result
 * @property int|null $to_take_action
 *
 * @property User $user
 * @property RefCovidReact $toTakeAction
 * @property RefCovidTestkitType $selfTestKitType
 * @property CovidTestkitRecord $selfCovidKit
 */
class CovidStatusForm extends \yii\db\ActiveRecord {

    const PIC_EMAIL = "natalie@npl.com.my";
    const NOTIFY_PIC_STATUS = 2;
    const HIGH_FEVER = 37.6;
    const RESULT_NEG = 'negative', RESULT_POS = 'positive', RESULT_WAIT = 'awaiting';

    public $scannedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'covid_status_form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'self_vaccine_dose', 'self_test_is', 'self_test_kit_type', 'self_covid_kit_id', 'other_how_many', 'other_vaccine_two_dose', 'other_test_is', 'to_take_action'], 'integer'],
            [['created_at', 'self_test_date'], 'safe'],
            [['body_temperature', 'spo2', 'self_vaccine_dose'], 'required'],
            [['body_temperature', 'spo2'], 'number'],
            [['self_place_list', 'other_place_list'], 'string'],
            [['self_symptom_list', 'other_symptom_list'], 'string', 'max' => 100],
            [['self_symptom_other', 'self_place_other', 'self_test_reason', 'self_test_result_attachment', 'other_symptom_other', 'other_place_other', 'other_test_reason'], 'string', 'max' => 255],
            [['self_test_result', 'other_test_result'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['to_take_action'], 'exist', 'skipOnError' => true, 'targetClass' => RefCovidReact::className(), 'targetAttribute' => ['to_take_action' => 'id']],
            [['self_test_kit_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefCovidTestkitType::className(), 'targetAttribute' => ['self_test_kit_type' => 'id']],
            [['self_covid_kit_id'], 'exist', 'skipOnError' => true, 'targetClass' => CovidTestkitRecord::className(), 'targetAttribute' => ['self_covid_kit_id' => 'id']],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
            ['scannedFile', 'file', 'extensions' => 'png, jpg, jpeg, pdf', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'Staff Name',
            'created_at' => 'Record Time',
            'body_temperature' => 'Body Temperature',
            'spo2' => 'Spo2',
            'self_vaccine_dose' => 'Vaccine Dose',
            'self_symptom_list' => 'Sick List',
            'self_symptom_other' => 'Sick (Other)',
            'self_place_list' => 'Places List',
            'self_place_other' => 'Places (Other)',
            'self_test_is' => 'Took Covid-19 Test?',
            'self_test_date' => 'Test Date',
            'self_test_reason' => 'Test Reason',
            'self_test_kit_type' => 'Test Kit Type',
            'self_covid_kit_id' => 'Self Covid Kit ID',
            'self_test_result' => 'Self Test Result',
            'self_test_result_attachment' => 'Self Test Result Attachment',
            'other_how_many' => 'Housemates',
            'other_vaccine_two_dose' => 'Housemate Complete Vaccine',
            'other_symptom_list' => 'Sick List',
            'other_symptom_other' => 'Sick (Other)',
            'other_place_list' => 'Places List',
            'other_place_other' => 'Places (Other)',
            'other_test_is' => 'Housemate Took Covid-19 Test?',
            'other_test_reason' => 'Test Reason',
            'other_test_result' => 'Other Test Result',
            'to_take_action' => 'To Take Action',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[ToTakeAction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getToTakeAction() {
        return $this->hasOne(RefCovidReact::className(), ['id' => 'to_take_action']);
    }

    /**
     * Gets query for [[SelfTestKitType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSelfTestKitType() {
        return $this->hasOne(RefCovidTestkitType::className(), ['id' => 'self_test_kit_type']);
    }

    /**
     * Gets query for [[SelfCovidKit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSelfCovidKit() {
        return $this->hasOne(CovidTestkitRecord::className(), ['id' => 'self_covid_kit_id']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
        }

        return parent::beforeSave($insert);
    }

    public function processAndSave() {

        $selfSickList = Yii::$app->request->post('selfSymptoms');
        $selfPlaces = Yii::$app->request->post('selfPlaces');
        $othersSickList = Yii::$app->request->post('othersSymptoms');
        $othersPlaces = Yii::$app->request->post('othersPlaces');
        $this->user_id = Yii::$app->user->id;
        $hasPreviousVaccineRecord = Yii::$app->request->post('hasPreviousVaccineRecord');

        $this->to_take_action = 0;
        $highestActionLevel = RefCovidReact::highestAlertLevel;

        if ($this->body_temperature > $this::HIGH_FEVER) {
            $this->to_take_action = $highestActionLevel;
        }
        if ($this->spo2 >= 96) {
            
        } else if ($this->spo2 >= 95) {
            $this->to_take_action = 3;
        } else if ($this->spo2 >= 93) {
            $this->to_take_action = 6;
        } else if ($this->spo2 <= 92) {
            $this->to_take_action = 6;
        }

        if ($this->other_test_is || $this->self_test_is) {
            if ($this->other_test_result == self::RESULT_POS || $this->self_test_result == self::RESULT_POS) {
                $this->to_take_action = RefCovidReact::placeHaveReason;
            } else if ($this->other_test_result == self::RESULT_WAIT || $this->self_test_result == self::RESULT_WAIT) {
                $this->to_take_action = RefCovidReact::waitForResult;
            }
        }

        // If have any special places / symptoms, take alert
        if (trim($this->self_symptom_other . $this->self_place_other . $this->other_symptom_other . $this->other_place_other) != '') {
            $this->to_take_action = RefCovidReact::alertLevel;
        }

        if ($this->self_symptom_other != '' || $this->other_symptom_other != '') {
            $this->to_take_action = RefCovidReact::placeHaveReason;
        }

        if ($selfSickList) {
            if ($this->to_take_action < $highestActionLevel) {
                foreach ($selfSickList as $sick) {
                    $symptoms = RefCovidSymptoms::findOne($sick);
                    if ($this->to_take_action < $symptoms->react_id) {
                        $this->to_take_action = $symptoms->react_id;
                    }
                }
            }
            $this->self_symptom_list = implode(",", $selfSickList);
        }

        if ($selfPlaces) {
            foreach ($selfPlaces as $place) {
                if ($this->to_take_action < $highestActionLevel) {
                    $places = RefCovidPlaces::findOne($place);
                    if ($this->to_take_action < $places->react_id) {
                        $this->to_take_action = $places->react_id;
                    }
                }
                $reason = Yii::$app->request->post('selfPlaces_' . $place . '_reason');
                $this->self_place_list .= $place . ' | ' . ($reason ? $reason : '') . "\r\n";
            }
        }

        if ($othersSickList) {
            if ($this->to_take_action < $highestActionLevel) {
                foreach ($othersSickList as $sick) {
                    $symptoms = RefCovidSymptoms::findOne($sick);
                    if ($this->to_take_action < $symptoms->react_id) {
                        $this->to_take_action = $symptoms->react_id;
                    }
                }
            }
            $this->other_symptom_list = implode(",", $othersSickList);
        }

        if ($othersPlaces) {
            foreach ($othersPlaces as $place) {
                if ($this->to_take_action < $highestActionLevel) {
                    $places = RefCovidPlaces::findOne($place);
                    if ($this->to_take_action < $places->react_id) {
                        $this->to_take_action = $places->react_id;
                    }
                }
                $reason = Yii::$app->request->post('othersPlaces_' . $place . '_reason');
                $this->other_place_list .= $place . ' | ' . ($reason ? $reason : '') . "\r\n";
            }
        }

        if ($this->self_test_date) {
            $this->self_test_date = MyFormatter::fromDateRead_toDateSQL($this->self_test_date);
        }

        if ($hasPreviousVaccineRecord == 1) {
            $this->self_test_result_attachment = Yii::$app->request->post('previousResult');
        }

        $this->save(false);

        if ($this->validate() && $this->scannedFile) {
            $filePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['covid_result_file_path'];
            $this->self_test_result_attachment = $this->id . '.' . $this->scannedFile->extension;
            \common\models\myTools\MyCommonFunction::mkDirIfNull($filePath);
            $this->scannedFile->saveAs($filePath . $this->self_test_result_attachment);

            $this->update(false);
        }
        // Update test kit result
        if ($hasPreviousVaccineRecord == 0) {
            if ($this->self_test_kit_type == RefCovidTestkitType::COMPANY_KIT && $this->self_covid_kit_id) {
                $testKitRecord = CovidTestkitRecord::findOne($this->self_covid_kit_id);
                $testKitRecord->updateResult($this->self_test_result_attachment);
            }
        }

        if ($this->to_take_action >= $this::NOTIFY_PIC_STATUS) {
            $this->generateAlertEmail();
        }

        return true;
    }

    public function generateSickList($input) {
        $sickList = explode(",", $input);
        $returnStr = '';
        foreach ($sickList as $sick) {
            $symptom = RefCovidSymptoms::findOne($sick);
            $classColor = '';
            if ($symptom->react_id == $symptom->react::alertLevel) {
                $classColor = 'text-warning';
            } else if ($symptom->react_id > $symptom->react::alertLevel) {
                $classColor = 'text-danger';
            }
            $returnStr .= '<span class="' . $classColor . '">➼ ' . $symptom['description'] . "</span><br/>";
        }
        return $returnStr;
    }

    public function generatePlaceList($input) {
        $placeList = explode("\r\n", $input);
        foreach ($placeList as $key => &$v) {
            if ($v != '') {
                $v = explode(' | ', $v);
            } else {
                unset($placeList[$key]);
            }
        }
        $returnStr = '';
        foreach ($placeList as $place) {
            $placeWent = RefCovidPlaces::findOne($place[0]);

            $classColor = '';
            if ($placeWent->react_id == $placeWent->react::alertLevel) {
                $classColor = 'text-warning';
            } else if ($placeWent->react_id > $placeWent->react::alertLevel) {
                $classColor = 'text-danger';
            }

            $returnStr .= '<span class="' . $classColor . '">➼ ' . $placeWent['description'] . (($place[1]) ? " &nbsp;&nbsp;Reason: " . $place[1] : '') . "</span><br/>";
        }
        return $returnStr;
    }

    public function generatePlaceListOthers($input) {
        $placeList = explode("\r\n", $input);
        foreach ($placeList as $key => &$v) {
            if ($v != '') {
                $v = explode(' | ', $v);
            } else {
                unset($placeList[$key]);
            }
        }
        $returnStr = '';
        foreach ($placeList as $place) {
            $placeWent = RefCovidPlacesOther::findOne($place[0]);

            $classColor = '';
            if ($placeWent->react_id == $placeWent->react::alertLevel) {
                $classColor = 'text-warning';
            } else if ($placeWent->react_id > $placeWent->react::alertLevel) {
                $classColor = 'text-danger';
            }

            $returnStr .= '<span class="' . $classColor . '">➼ ' . $placeWent['description'] . (($place[1]) ? " &nbsp;&nbsp;Reason: " . $place[1] : '') . "</span><br/>";
        }
        return $returnStr;
    }

    private function generateAlertEmail() {
        try {
            $targetEmail = $this::PIC_EMAIL;
            $viewFormDetailUrl = $_SERVER["REQUEST_SCHEME"] . '://' . Yii::$app->request->hostName . '/covidform/view?id=' . $this->id;

            $subject = "Health Declaration Form Alert!: " . $this->user->fullname;
            $textBody = "<h3>A staff's health declaration form needs your review/verification immediately!</h3>"
                    . "<table style='border: 1px solid black; border-collapse: collapse;' ><tr><td style='border: 1px solid black'>Name:</td>"
                    . "<td style='border: 1px solid black'>" . $this->user->fullname . "</td></tr>"
//                    . "<tr><td style='border: 1px solid black'>Check in time: </td><td style='border: 1px solid black'>"
//                    . MyFormatter::asDateTime_ReaddmYHi($this->created_at) . "</td></tr>"
                    . "<tr><td style='border: 1px solid black'>Action: </td><td style='border: 1px solid black'>"
                    . $this->toTakeAction->description . "</td></tr><tr><td colspan='2' style='text-align:center'>"
                    . "<a href='" . $viewFormDetailUrl . "' target='_blank'><button type='button'>View Now</button></a>"
                    . "</td></tr></table>";

            Yii::$app->mailer->compose()
                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' Robot'])
                    ->setTo($targetEmail)
                    ->setSubject($subject)
                    ->setHtmlBody($textBody)
                    ->send();
        } catch (Exception $e) {
            $err = 'Caught exception: ' . $e->getMessage() . "\n";
            \common\models\myTools\FlashHandler::err("$err Error, kindly contact the IT Department");
        }


        return true;
    }

}
