<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
?>
<?php $pendingCount = $totalPendingApprovalQuotaion; ?>
<div class="summary-card clickable-card">
    <a href="/projectquotation/director-pending-approval" style="text-decoration: none; color: inherit;">
        <div class="summary-title">
            <span> 
                <i class="fas fa-clipboard-list me-2 summary-icon text-info"></i>
                Quotation 
            </span>
            <span class="summary-count total-pending"><?= $pendingCount ?></span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="summary-subtext">Awaiting your approval</div>
        </div>
    </a>
</div> 
