<?php
/* @var $isView boolean */
/* @var $hasSuperiorUpdate boolean */
/* @var $moduleIndex string */
?>

<?php if ($isView && $hasSuperiorUpdate): ?>
    <!-- View mode with superior approval columns -->
    <tr>
        <th rowspan="2" class="text-center" width="4%">No.</th>
        <th rowspan="2" width="8%">Department</th>
        <th rowspan="2" width="8%">Supplier</th>
        <th rowspan="2" width="8%">Brand</th>
        <th rowspan="2" width="8%">Model Type</th>
        <th rowspan="2" width="8%">Model Group</th>
        <th rowspan="2" width="18%">Item Description</th>
        <th rowspan="2" width="6%">Quantity</th>
        <th rowspan="2" width="6%">Unit Type</th>
        <th rowspan="2" class="text-center" width="3%">Currency</th>
        <th rowspan="2" class="text-right" width="9%">Unit Price</th>
        <th rowspan="2" class="text-right" width="9%">Total Price</th>
        <th rowspan="2" width="16%">Purpose</th>
        <th colspan="5" class="text-center" width="35%">Superior's Response</th>
    </tr>
    <tr>
        <th width="5%">Quantity</th>
        <th class="text-center" width="6%">Currency</th>
        <th class="text-right" width="8%">Unit Price</th>
        <th class="text-right" width="8%">Total Price</th>
        <th class="text-left" width="8%">Remark</th>
    </tr>

<?php elseif ($isView): ?>
    <!-- Simple view mode -->
    <tr>
        <th class="text-center" style="min-width: 50px;">No.</th>
        <th style="min-width: 120px;">Department</th>
        <th style="min-width: 120px;">Supplier</th>
        <th style="min-width: 120px;">Brand</th>
        <th style="min-width: 130px;">Model Type</th>
        <th style="min-width: 130px;">Model Group</th>
        <th style="min-width: 180px;">Item Description</th>
        <th style="min-width: 80px;">Quantity</th>
        <th rowspan="2" width="6%">Unit Type</th>
        <th class="text-center" style="min-width: 100px;">Currency</th>
        <th class="text-right" style="min-width: 110px;">Unit Price</th>
        <th class="text-right" style="min-width: 110px;">Total Price</th>
        <th style="min-width: 150px;">Purpose</th>
        <th class="text-left" style="min-width: 100px;">Remark</th>
        <th style="min-width: 80px;" class="text-center">Action</th>
    </tr>

<?php else: ?>
    <!-- Edit/Create mode -->
    <tr>
        <th class="text-center" style="min-width: 50px;">No.</th>
        <th style="min-width: 120px;">Department</th>
        <th style="min-width: 120px;">Supplier</th>
        <th style="min-width: 120px;">Brand</th>
        <th style="min-width: 130px;">Model Type</th>
        <th style="min-width: 130px;">Model Group</th>
        <th style="min-width: 180px;">Item Description</th>
        <th style="min-width: 80px;">Quantity</th>
        <th rowspan="2" width="6%">Unit Type</th>
        <th class="text-center" style="min-width: 100px;">Currency</th>
        <th class="text-right" style="min-width: 110px;">Unit Price</th>
        <th class="text-right" style="min-width: 110px;">Total Price</th>
        <th style="min-width: 150px;">Purpose</th>
        <th class="text-left" style="min-width: 100px;">Remark</th>
        <th style="min-width: 80px;" class="text-center">Action</th>
    </tr>
<?php endif; ?>