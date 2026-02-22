<?php

namespace frontend\models\working\hrpayslip;

use yii\base\Model;

class HrPayslipSummary extends Model {

    public $advances = [];
    public $allowances = [];
    public $overtimes = [];
    public $gifts = [];
    public $staffId;
    public $payYear;
    public $payMonth;
    public $basicSalary;
    public $bonus;
    public $commission;
    public $directorFee;
    public $epf;
    public $socso;
    public $eisSip;
    public $incomeTax;
    public $unpaidLeave;
    public $annualLeavePay;
    public $netSalary;
    public $employerEpf;
    public $employerSocso;
    public $employerEisSip;

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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function loadMonthlySummary($month, $year) {
        $rows = (new \yii\db\Query())
                ->select(['pay_year,  pay_month ,  SUM(basic_salary) basic_salary,  SUM(bonus) bonus,  SUM(commission) commission, '
                    . ' SUM(director_fee) director_fee,  SUM(epf) epf,  SUM(socso) socso,  SUM(eis_sip) eis_sip,  SUM(income_tax) income_tax,  SUM(unpaid_leave) unpaid_leave,  '
                    . 'SUM(annual_leave_pay) annual_leave_pay,  SUM(net_salary) net_salary,  SUM(employer_epf) employer_epf,  SUM(employer_socso) employer_socso,  '
                    . 'SUM(employer_eis_sip) AS employer_eis_sip '])
                ->from('hr_payslip')
                ->where('pay_year = ' . $year . ' AND pay_month = ' . $month)
                ->all();
        $row = $rows[0];
        $this->payYear = $row['pay_year'];
        $this->payMonth = $row['pay_month'];
        $this->basicSalary = $row['basic_salary'];

        $this->bonus = $row['bonus'];
        $this->commission = $row['commission'];
        $this->directorFee = $row['director_fee'];
        $this->epf = $row['epf'];
        $this->socso = $row['socso'];
        $this->eisSip = $row['eis_sip'];
        $this->incomeTax = $row['income_tax'];
        $this->unpaidLeave = $row['unpaid_leave'];

        $this->annualLeavePay = $row['annual_leave_pay'];
        $this->netSalary = $row['net_salary'];

        $this->employerEpf = $row['employer_epf'];
        $this->employerSocso = $row['employer_socso'];
        $this->employerEisSip = $row['employer_eis_sip'];

        $this->getAdvances($month, $year);
        $this->getAllowances($month, $year);
        $this->getOvertimes($month, $year);
        $this->getGifts($month, $year);
        return true;
    }

    private function getAdvances($month, $year) {
        $rows = (new \yii\db\Query())
                ->select(['b.description,sum(b.amount) as advance'])
                ->from('hr_payslip as a')
                ->join('INNER JOIN', 'hr_payslip_advance as b', 'a.id = b.payslip_id')
                ->where('pay_year = ' . $year . ' AND pay_month = ' . $month)
                ->groupBy(['b.description'])
                ->all();
        foreach ($rows as $key => $row) {
            $this->advances[] = $row;
        }
    }

    private function getAllowances($month, $year) {
        $rows = (new \yii\db\Query())
                ->select(['c.allowance_name,SUM(b.amount) AS allowances'])
                ->from('hr_payslip as a')
                ->join('INNER JOIN', 'hr_payslip_allowance as b', 'a.id = b.payslip_id')
                ->join('INNER JOIN', 'ref_pay_allowance as c', 'c.code=b.allowance_code')
                ->where('pay_year = ' . $year . ' AND pay_month = ' . $month)
                ->groupBy(['b.allowance_code'])
                ->all();
        foreach ($rows as $key => $row) {
            $this->allowances[] = $row;
        }
    }

    private function getOvertimes($month, $year) {
        $rows = (new \yii\db\Query())
                ->select(['c.overtime_name,sum(b.amount) as overtimes'])
                ->from('hr_payslip as a')
                ->join('INNER JOIN', 'hr_payslip_overtime as b', 'a.id = b.payslip_id')
                ->join('INNER JOIN', 'ref_pay_overtime as c', 'c.code=b.overtime_code')
                ->where('pay_year = ' . $year . ' AND pay_month = ' . $month)
                ->groupBy(['b.overtime_code'])
                ->all();
        foreach ($rows as $key => $row) {
            $this->overtimes[] = $row;
        }
    }

    private function getGifts($month, $year) {
        $rows = (new \yii\db\Query())
                ->select(['concat(c.gift_name," - ",b.description) as gift_name,sum(b.amount) as gifts'])
                ->from('hr_payslip as a')
                ->join('INNER JOIN', 'hr_payslip_gift as b', 'a.id = b.payslip_id')
                ->join('INNER JOIN', 'ref_pay_gift as c', 'c.code=b.gift_code')
                ->where('pay_year = ' . $year . ' AND pay_month = ' . $month)
                ->groupBy(['b.gift_code', 'b.description'])
                ->all();
        foreach ($rows as $key => $row) {
            $this->gifts[] = $row;
        }
    }

}
