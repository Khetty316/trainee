<?php

namespace frontend\models\office\claim;

use Yii;
use common\models\User;
use frontend\models\office\claim\ClaimEntitlement;
use frontend\models\office\claim\ClaimEntitlementDetails;
use frontend\models\RefGeneralStatus;

/**
 * This is the model class for table "claim_entitlement_summary".
 *
 * @property int $id
 * @property int|null $master_id
 * @property int|null $detail_id
 * @property int|null $user_id
 * @property int|null $year
 * @property int|null $month
 * @property string|null $date_from
 * @property string|null $date_to
 * @property string|null $claim_type_code
 * @property float|null $monthly_limit
 * @property float|null $amount_claimed
 * @property float|null $balance_amt
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property ClaimEntitlement $master
 * @property ClaimEntitlementDetails $detail
 * @property User $user
 */
class ClaimEntitlementSummary extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'claim_entitlement_summary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['master_id', 'detail_id', 'user_id', 'year', 'month'], 'integer'],
            [['date_from', 'date_to', 'created_at', 'updated_at'], 'safe'],
            [['monthly_limit', 'amount_claimed', 'balance_amt'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClaimEntitlement::className(), 'targetAttribute' => ['master_id' => 'id']],
            [['detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClaimEntitlementDetails::className(), 'targetAttribute' => ['detail_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'master_id' => 'Master ID',
            'detail_id' => 'Detail ID',
            'user_id' => 'User ID',
            'year' => 'Year',
            'month' => 'Month',
            'monthly_limit' => 'Monthly Limit',
            'amount_claimed' => 'Amount Claimed',
            'balance_amt' => 'Balance Amt',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Master]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaster() {
        return $this->hasOne(ClaimEntitlement::className(), ['id' => 'master_id']);
    }

    /**
     * Gets query for [[Detail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetail() {
        return $this->hasOne(ClaimEntitlementDetails::className(), ['id' => 'detail_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function reverseClaimEntitlement($claimMaster) {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $claimantId = $claimMaster->claimant_id;
            $claimType = $claimMaster->claim_type;

            $claimDetails = $claimMaster->getClaimDetails()
                    ->where(['is_deleted' => 0])
                    ->all();

            if (empty($claimDetails)) {
                throw new \Exception("No claim details found to reverse for ClaimMaster ID {$claimMaster->id}");
            }

            // Cache entitlement info per (year, claimType)
            $entitlementCache = [];

            foreach ($claimDetails as $detail) {
                if (empty($detail->receipt_date)) {
                    throw new \Exception("Claim detail ID {$detail->id} has no receipt date");
                }

                $receiptDate = strtotime($detail->receipt_date);
                $year = (int) date('Y', $receiptDate);
                $month = (int) date('n', $receiptDate);

                $cacheKey = "{$year}-{$claimType}";

                if (!isset($entitlementCache[$cacheKey])) {
                    $entitleMaster = ClaimEntitlement::find()
                            ->where([
                                'user_id' => $claimantId,
                                'year' => $year,
                                'status' => RefGeneralStatus::STATUS_Approved,
                                'is_active' => 1,
                            ])
                            ->one();

                    if (!$entitleMaster) {
                        throw new \Exception("No entitlement master found for user {$claimantId}, year {$year}");
                    }

                    $entitleDetail = ClaimEntitlementDetails::find()
                            ->where([
                                'claim_entitle_id' => $entitleMaster->id,
                                'claim_type_code' => $claimType,
                            ])
                            ->one();

                    if (!$entitleDetail) {
                        throw new \Exception("No entitlement detail found for master {$entitleMaster->id}, claim type {$claimType}");
                    }

                    $entitlementCache[$cacheKey] = [
                        'master' => $entitleMaster,
                        'detail' => $entitleDetail,
                    ];
                }

                $entitleMaster = $entitlementCache[$cacheKey]['master'];
                $entitleDetail = $entitlementCache[$cacheKey]['detail'];

                // Retrieve existing summary
                $summary = ClaimEntitlementSummary::find()
                        ->where([
                            'master_id' => $entitleMaster->id,
                            'detail_id' => $entitleDetail->id,
                            'user_id' => $claimantId,
                            'year' => $year,
                            'month' => $month,
                            'claim_type_code' => $claimType,
                        ])
                        ->one();

                if (!$summary) {
                    throw new \Exception("No entitlement summary found for user {$claimantId}, year {$year}, month {$month}");
                }

                // 🔹 Recalculate actual claimed amount after reversal
                $dateFrom = $this->getDateFrom($year, $month);
                $dateTo = $this->getDateTo($year, $month);

                $amountClaimed = $this->getClaimedAmount($claimantId, $claimType, $dateFrom, $dateTo);
                $balanceAmt = max($summary->monthly_limit - $amountClaimed, 0);

                $summary->amount_claimed = $amountClaimed;
                $summary->balance_amt = $balanceAmt;
                $summary->updated_at = new \yii\db\Expression('NOW()');

                if (!$summary->save(false)) {
                    throw new \Exception("Failed to update entitlement summary for user {$claimantId}, month {$month}/{$year}");
                }
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::error("Reverse entitlement failed: " . $e->getMessage(), __METHOD__);
            throw $e;
        }
    }
    
//public function updateClaimEntitlementSummary($claimMaster, $claimMasterData, $receiptsData) { 
//    $transaction = Yii::$app->db->beginTransaction();
//    try {
//        $claimantId = $claimMasterData['claimant_id'] ?? null;
//        $claimType = $claimMasterData['claim_type'] ?? null;
//        $claimMasterId = $claimMaster->id ?? null;
//
//        if (!$claimantId || !$claimType || !$claimMasterId) {
//            throw new \Exception("Missing claimant_id, claim_type, or claimMasterId");
//        }
//
//        // 🔹 Get entitlement info
//        $entitlementDetail = (new \yii\db\Query())
//                ->select(['ced.*', 'ce.id AS master_id', 'ce.year'])
//                ->from(['ced' => 'claim_entitlement_details'])
//                ->innerJoin(['ce' => 'claim_entitlement'], 'ce.id = ced.claim_entitle_id')
//                ->where([
//                    'ce.user_id' => $claimantId,
//                    'ce.is_active' => 1,
//                    'ced.claim_type_code' => $claimType,
//                ])
//                ->one();
//
//        if (!$entitlementDetail) {
//            throw new \Exception("No entitlement found for user {$claimantId} and type {$claimType}");
//        }
//
//        $masterId = $entitlementDetail['master_id'];
//        $detailId = $entitlementDetail['id'];
//        $year = (int) $entitlementDetail['year'];
//        $monthStart = (int) $entitlementDetail['month_start'];
//        $monthEnd = (int) $entitlementDetail['month_end'];
//        $limit = (float) $entitlementDetail['amount'];
//        $cutoffStartDay = 23; // ✅ Get from config or entitlement detail if dynamic
//
//        // ✅ Helper function to determine period month based on cutoff
//        $getPeriodMonth = function($receiptDate) use ($cutoffStartDay) {
//            $ts = strtotime($receiptDate);
//            $day = (int) date('j', $ts);
//            $month = (int) date('n', $ts);
//            $year = (int) date('Y', $ts);
//            
//            if ($day >= $cutoffStartDay) {
//                // Belongs to next month's period
//                $periodMonth = $month + 1;
//                $periodYear = $year;
//                if ($periodMonth > 12) {
//                    $periodMonth = 1;
//                    $periodYear++;
//                }
//            } else {
//                // Belongs to current month's period
//                $periodMonth = $month;
//                $periodYear = $year;
//            }
//            
//            return ['year' => $periodYear, 'month' => $periodMonth];
//        };
//
//        // 🔹 Previous claims from DB (exclude rejected)
//        $previousClaims = (new \yii\db\Query())
//                ->select(['id', 'receipt_date', 'amount_to_be_paid', 'claim_status'])
//                ->from('claim_detail')
//                ->where([
//                    'claim_master_id' => $claimMasterId, 
//                    'is_deleted' => 0
//                ])
//                ->andWhere(['<>', 'claim_status', ClaimMaster::STATUS_REJECTED])
//                ->all();
//
//        $currentIds = array_filter(array_column($receiptsData, 'id'));
//        $deletedByPeriod = [];
//
//        // 🔹 Identify deleted receipts by PERIOD, not calendar month
//        foreach ($previousClaims as $prev) {
//            if (!in_array((int) $prev['id'], $currentIds)) {
//                $period = $getPeriodMonth($prev['receipt_date']);
//                $key = "{$period['year']}-{$period['month']}";
//                $deletedByPeriod[$key] = ($deletedByPeriod[$key] ?? 0) + (float) $prev['amount_to_be_paid'];
//            }
//        }
//
//        // 🔹 Collect all affected PERIODS (not calendar months)
//        $claimPeriods = [];
//
//        foreach ($receiptsData as $r) {
//            if (empty($r['receipt_date'])) continue;
//            
//            $period = $getPeriodMonth($r['receipt_date']);
//            $key = "{$period['year']}-{$period['month']}";
//            $claimPeriods[$key] = ['yr' => $period['year'], 'mo' => $period['month']];
//        }
//
//        foreach ($deletedByPeriod as $key => $_) {
//            [$yr, $mo] = explode('-', $key);
//            $claimPeriods[$key] = ['yr' => (int) $yr, 'mo' => (int) $mo];
//        }
//
//        // 🔹 Process each affected period
//        foreach ($claimPeriods as $m) {
//            $yr = $m['yr'];
//            $mo = $m['mo'];
//            
//            if ($mo < $monthStart || $mo > $monthEnd) continue;
//
//            $dateFrom = $this->getDateFrom($yr, $mo);
//            $dateTo = $this->getDateTo($yr, $mo);
//
//            // 🧮 Calculate claimed amount for current master
//            $currentClaimAmount = 0;
//            foreach ($receiptsData as $r) {
//                if (empty($r['receipt_date']) || empty($r['amount_to_be_paid'])) continue;
//
//                $period = $getPeriodMonth($r['receipt_date']);
//                
//                // ✅ Check if receipt belongs to this PERIOD
//                if ($period['year'] === $yr && $period['month'] === $mo) {
//                    $currentClaimAmount += (float) $r['amount_to_be_paid'];
//                }
//            }
//
//            // 🧮 Other masters (same user + claim type) - exclude rejected
//            $otherClaims = (new \yii\db\Query())
//                            ->from('claim_detail cd')
//                            ->innerJoin('claim_master cm', 'cm.id = cd.claim_master_id')
//                            ->where([
//                                'cm.claimant_id' => $claimantId,
//                                'cm.claim_type' => $claimType,
//                                'cd.is_deleted' => 0,
//                            ])
//                            ->andWhere(['<>', 'cd.claim_status', ClaimMaster::STATUS_REJECTED])
//                            ->andWhere(['between', 'cd.receipt_date', $dateFrom, $dateTo])
//                            ->andWhere(['<>', 'cm.id', $claimMasterId])
//                            ->sum('cd.amount_to_be_paid') ?? 0;
//
//            $deletedAmt = $deletedByPeriod["{$yr}-{$mo}"] ?? 0;
//
//            // 🧮 Compute totals
//            $totalClaimed = $otherClaims + $currentClaimAmount - $deletedAmt;
//            if ($totalClaimed < 0) $totalClaimed = 0;
//            $balanceAmt = max(0, $limit - $totalClaimed);
//
//            // 🔹 Update or insert summary
//            $summary = ClaimEntitlementSummary::findOne([
//                'master_id' => $masterId,
//                'detail_id' => $detailId,
//                'user_id' => $claimantId,
//                'month' => $mo,
//                'year' => $yr,
//                'claim_type_code' => $claimType,
//            ]);
//
//            if (!$summary) {
//                $summary = new ClaimEntitlementSummary([
//                    'master_id' => $masterId,
//                    'detail_id' => $detailId,
//                    'user_id' => $claimantId,
//                    'month' => $mo,
//                    'year' => $yr,
//                    'claim_type_code' => $claimType,
//                    'created_at' => new \yii\db\Expression('NOW()'),
//                ]);
//            }
//
//            $summary->date_from = $dateFrom;
//            $summary->date_to = $dateTo;
//            $summary->monthly_limit = $limit;
//            $summary->amount_claimed = $totalClaimed;
//            $summary->balance_amt = $balanceAmt;
//            $summary->updated_at = new \yii\db\Expression('NOW()');
//
//            if (!$summary->save(false)) {
//                throw new \Exception("Failed to save summary for {$yr}-{$mo}");
//            }
//        }
//
//        $transaction->commit();
//        return true;
//    } catch (\Exception $e) {
//        $transaction->rollBack();
//        Yii::error($e->getMessage(), __METHOD__);
//        throw $e;
//    }
//}
    //
public function updateClaimEntitlementSummary($claimMaster, $claimMasterData, $receiptsData)
{
    $transaction = Yii::$app->db->beginTransaction();

    try {
        $claimantId = $claimMasterData['claimant_id'] ?? null;
        $claimType  = $claimMasterData['claim_type'] ?? null;
        $claimMasterId = $claimMaster->id ?? null;

        if (!$claimantId || !$claimType || !$claimMasterId) {
            throw new \Exception("Missing claimant_id, claim_type, or claimMasterId");
        }

        // 🔹 Get entitlement info
        $entitlementDetail = (new \yii\db\Query())
            ->select(['ced.*', 'ce.id AS claim_entitle_id', 'ce.year'])
            ->from(['ced' => 'claim_entitlement_details'])
            ->innerJoin(['ce' => 'claim_entitlement'], 'ce.id = ced.claim_entitle_id')
            ->where([
                'ce.user_id' => $claimantId,
                'ce.is_active' => 1,
                'ced.claim_type_code' => $claimType,
            ])
            ->one();

        if (!$entitlementDetail) {
            throw new \Exception("No entitlement found for user {$claimantId} and type {$claimType}");
        }

        $masterId   = $entitlementDetail['claim_entitle_id']; // ✅ use same as migrate
        $detailId   = $entitlementDetail['id'];
        $year       = (int) $entitlementDetail['year'];
        $monthStart = (int) $entitlementDetail['month_start'];
        $monthEnd   = (int) $entitlementDetail['month_end'];
        $limit      = (float) $entitlementDetail['amount'];

        // 🔹 Get previous DB claims
        $previousClaims = (new \yii\db\Query())
            ->select(['id', 'receipt_date', 'amount_to_be_paid'])
            ->from('claim_detail')
            ->where(['claim_master_id' => $claimMasterId, 'is_deleted' => 0])
            ->all();

        $currentIds = array_filter(array_column($receiptsData, 'id'));
        $deletedByMonth = [];

        foreach ($previousClaims as $prev) {
            if (!in_array((int)$prev['id'], $currentIds)) {
                $ts = strtotime($prev['receipt_date']);
                $yr = (int) date('Y', $ts);
                $mo = (int) date('n', $ts);
                $key = "{$yr}-{$mo}";
                $deletedByMonth[$key] = ($deletedByMonth[$key] ?? 0) + (float)$prev['amount_to_be_paid'];
            }
        }

        // 🔹 Collect all affected months
        $claimMonths = [];
        foreach ($receiptsData as $r) {
            if (empty($r['receipt_date'])) continue;

            // ✅ handle cutoff (example: cutoff starts 23rd)
            $day = (int) date('j', strtotime($r['receipt_date']));
            $month = (int) date('n', strtotime($r['receipt_date']));
            $yearR = (int) date('Y', strtotime($r['receipt_date']));

            if ($day >= 23) {
                $month++;
                if ($month > 12) {
                    $month = 1;
                    $yearR++;
                }
            }

            $claimMonths["{$yearR}-{$month}"] = ['yr' => $yearR, 'mo' => $month];
        }

        // include deleted months
        foreach ($deletedByMonth as $key => $_) {
            [$yr, $mo] = explode('-', $key);
            $claimMonths[$key] = ['yr' => (int)$yr, 'mo' => (int)$mo];
        }

        // 🔹 Process months
        foreach ($claimMonths as $m) {
            $yr = $m['yr'];
            $mo = $m['mo'];

            if ($mo < $monthStart || $mo > $monthEnd) continue;

            $dateFrom = $this->getDateFrom($yr, $mo);
            $dateTo   = $this->getDateTo($yr, $mo);

            // 🧮 Calculate claimed amount for this master
            $currentClaimAmount = 0;
            foreach ($receiptsData as $r) {
                if (empty($r['receipt_date']) || empty($r['amount_to_be_paid'])) continue;

                $rTs = strtotime($r['receipt_date']);
                $rDay = (int) date('j', $rTs);
                $rMonth = (int) date('n', $rTs);
                $rYear = (int) date('Y', $rTs);

                if ($rDay >= 23) {
                    $rMonth++;
                    if ($rMonth > 12) {
                        $rMonth = 1;
                        $rYear++;
                    }
                }

                if ($rMonth == $mo && $rYear == $yr) {
                    $currentClaimAmount += (float)$r['amount_to_be_paid'];
                }
            }

            // 🧮 Include other claims
            $otherClaims = (new \yii\db\Query())
                ->from('claim_detail cd')
                ->innerJoin('claim_master cm', 'cm.id = cd.claim_master_id')
                ->where([
                    'cm.claimant_id' => $claimantId,
                    'cm.claim_type'  => $claimType,
                    'cd.is_deleted'  => 0,
                ])
                ->andWhere(['between', 'cd.receipt_date', $dateFrom, $dateTo])
                ->andWhere(['<>', 'cm.id', $claimMasterId])
                ->sum('cd.amount_to_be_paid') ?? 0;

            $deletedAmt = $deletedByMonth["{$yr}-{$mo}"] ?? 0;

            $totalClaimed = $otherClaims + $currentClaimAmount - $deletedAmt;
            if ($totalClaimed < 0) $totalClaimed = 0;

            $balanceAmt = max(0, $limit - $totalClaimed);

            // 🔹 Find existing summary
            $summary = ClaimEntitlementSummary::findOne([
                'master_id' => $masterId,
                'detail_id' => $detailId,
                'user_id' => $claimantId,
                'month' => $mo,
                'year' => $yr,
                'claim_type_code' => $claimType,
            ]);

            if (!$summary) {
                $summary = new ClaimEntitlementSummary([
                    'master_id' => $masterId,
                    'detail_id' => $detailId,
                    'user_id' => $claimantId,
                    'month' => $mo,
                    'year' => $yr,
                    'claim_type_code' => $claimType,
                    'created_at' => new \yii\db\Expression('NOW()'),
                ]);
            }

            $summary->date_from = $dateFrom;
            $summary->date_to = $dateTo;
            $summary->monthly_limit = $limit;
            $summary->amount_claimed = $totalClaimed;
            $summary->balance_amt = $balanceAmt;
            $summary->updated_at = new \yii\db\Expression('NOW()');

            if (!$summary->save(false)) {
                throw new \Exception("Failed to save summary for {$yr}-{$mo}");
            }
        }

        $transaction->commit();
        return true;

    } catch (\Exception $e) {
        $transaction->rollBack();
        Yii::error($e->getMessage(), __METHOD__);
        throw $e;
    }
}

    public function getClaimedAmount($userId, $claimType, $dateFrom, $dateTo) {
        $result = (new \yii\db\Query())
                ->select(['COALESCE(SUM(cd.amount_to_be_paid), 0) as amount'])
                ->from(['cm' => 'claim_master'])
                ->innerJoin(['cd' => 'claim_detail'], 'cm.id = cd.claim_master_id')
                ->where([
                    'cm.claimant_id' => $userId,
                    'cm.claim_type' => $claimType,
                    'cm.is_deleted' => 0,
                    'cd.is_deleted' => 0
                ])
                ->andWhere(['<>', 'cd.claim_status', ClaimMaster::STATUS_REJECTED])
                ->andWhere(['between', 'cd.receipt_date', $dateFrom, $dateTo])
                ->one();

        return floatval($result['amount'] ?? 0);
    }

    public function getDateFrom($year, $month) {
        if ($month == 1) {
            return date('Y-m-d', strtotime(($year - 1) . '-12-23'));
        }
        return date('Y-m-d', strtotime($year . '-' . str_pad($month - 1, 2, '0', STR_PAD_LEFT) . '-23'));
    }

    /**
     * Calculate date_to (22nd of current month)
     */
    public function getDateTo($year, $month) {
        return date('Y-m-d', strtotime($year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-22'));
    }
}
