<?php

namespace common\models\myTools;

use Yii;
use yii\base\Model;

class email extends Model {

    private $directorsEmail = "director_system@npl.com.my";
    private $email_sender = "paul.ling@npl.com.my";
    private $email_account = "ann.lau@npl.com.my";
    private $email_procurement = "";
    private $email_admin = "";

//    private $receiver = "paulling1987@gmail.com";

    public function rules() {
        
    }

    public function attributeLabels() {
        
    }

    public function sendEmail_Invoice($invoice, $mpdf) {
        $subject = "NOSA United System - New Invoice: " . $invoice->invoice_no;
        $htmlBody = "<p>Project Code: " . $invoice->project->project_code . "</p>"
                . "<p>Project Title: " . $invoice->project->title . "</p>"
                . "<p>Project Amount: " . MyFormatter::asCurrency($invoice->project->price) . "</p>"
                . "<p>Invoice No.: " . $invoice->invoice_no . "</p>"
                . "<p>Invoice Amount: " . MyFormatter::asCurrency($invoice->invoice_amount) . "</p>";

        $attachment = array('fileName' => $invoice->invoice_no . ".pdf", 'contentType' => 'application/pdf');

        $this->sendEmail($subject, "", $htmlBody, $mpdf, $attachment);
    }

    public function sendEmail_Receipt($payment, $mpdf) {
        $subject = "NOSA United System - New Receipt Issued: " . $payment->receipt_no;
        $htmlBody = "<p>Project Code: " . $payment->project->project_code . "</p>"
                . "<p>Project Title: " . $payment->project->title . "</p>"
                . "<p>Project Amount: " . MyFormatter::asCurrency($payment->project->price) . "</p>"
                . "<p>Invoice No.: " . $payment->invoice->invoice_no . "</p>"
                . "<p>Invoice Amount: " . MyFormatter::asCurrency($payment->invoice->invoice_amount) . "</p>"
                . "<p>Receipt No.: " . $payment->receipt_no . "</p>"
                . "<p>Paid Amount: " . MyFormatter::asCurrency($payment->pay_amount) . "</p>"
                . "<p>Paid by: " . $payment->payMethod->pay_method_desc . "</p>"
                . "<p>Reference: " . $payment->pay_reference . "</p>";

        $attachment = array('fileName' => $payment->receipt_no . ".pdf", 'contentType' => 'application/pdf');

        $this->sendEmail($subject, "", $htmlBody, $mpdf, $attachment);
    }

    private function sendEmail($sender, $receiver, $cc, $subject, $textBody, $htmlBody, $mpdf, $attachment) {

        $mail = Yii::$app->mailer->compose()
                ->setFrom($sender)
                ->setTo($receiver)
                ->setSubject($subject)
                ->setTextBody($textBody)
                ->setHtmlBody($htmlBody);
        if ($cc) {
            $mail->setCc($cc);
        }
        if ($mpdf && $attachment) {
            $mail->attachContent($mpdf, $attachment);
        }
        return $mail->send();
    }

    public function sendEmail_MI_toDirector($subject, $htmlBody) {
        return $this->sendEmail($this->email_sender, $this->directorsEmail, null, $subject, "", $htmlBody, NULL, null);
    }

    public function sendEmail_MI_requestor($subject, $htmlBody, $requestorEmail) {
        return $this->sendEmail($this->email_sender, $requestorEmail, null, $subject, "", $htmlBody, NULL, null);
    }

}
