<?php
$totalInventoryCount = $newItemApprovedForPurchase + $newItemReadyForPo + $reorderItemReadyForPo;
?>

<div class="summary-card">
    <div class="summary-title">
        <span>
            <i class="fas fa-warehouse summary-icon text-info"></i> 
            Inventory
        </span>
        <span class="summary-count total-pending"><?= $totalInventoryCount ?></span>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <div class="summary-subtext">Awaiting for your action</div>
        <i class="fas fa-chevron-down accordion-arrow mr-1"></i>
    </div>
</div>

<div class="collapse mb-3" id="inventoryAccordion">
    <div class="accordion-content p-2" style="border-radius: 8px;">
        <div class="section-header">
            <span>Procurement</span>
        </div> 
        <div class="sub-accordion-wrapper mb-2">
            <div class="role-summary-item shadow-sm inventory-sub-trigger" 
                 data-target="#subNewItem" 
                 style="background: #f8fafc; cursor: pointer; padding: 8px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center;">
                <div class="role-info">
                    <i class="fas fa-plus-circle role-icon text-primary"></i>
                    <span class="role-name">New Item</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="role-count badge bg-info">
                        <?= ($newItemApprovedForPurchase + $newItemReadyForPo) ?>
                    </span>
                    <i class="fas fa-chevron-down transition-icon ml-3" style="font-size: 0.8rem;"></i>
                </div>
            </div>

            <div class="collapse mt-1" id="subNewItem">
                <div class="list-group list-group-flush pl-3 bg-white" style="border-radius: 5px; border: 1px solid #ddd;">
                    <a href="/inventory/inventory/executive-pre-requisition-pending-approval"
                       class="list-group-item list-group-item-action d-flex align-items-center py-2">

                        <small>Approved for Purchase</small>

                        <div class="ml-auto d-flex align-items-center">
                            <span class="role-count badge bg-info mr-2">
                                <?= $newItemApprovedForPurchase ?>
                            </span>
                            <i class="fas fa-chevron-right transition-icon" style="font-size: 0.8rem;"></i>
                        </div>
                    </a>

                    <a href="/inventory/inventory/executive-new-item-ready-for-po-list"
                       class="list-group-item list-group-item-action d-flex align-items-center py-2">

                        <small>Ready for PO</small>

                        <div class="ml-auto d-flex align-items-center">
                            <span class="role-count badge bg-info mr-2">
                                <?= $newItemReadyForPo ?>
                            </span>
                            <i class="fas fa-chevron-right transition-icon" style="font-size: 0.8rem;"></i>
                        </div>
                    </a>

                </div>
            </div>
        </div>

        <div class="sub-accordion-wrapper">
            <div class="role-summary-item shadow-sm inventory-sub-trigger" 
                 data-target="#subReorderItem" 
                 style="background: #f8fafc; cursor: pointer; padding: 8px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center;">
                <div class="role-info">
                    <i class="fas fa-sync-alt role-icon text-success"></i>
                    <span class="role-name">Reorder Item</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="role-count badge bg-info">
                        <?= ($reorderItemReadyForPo) ?>
                    </span>
                    <i class="fas fa-chevron-down transition-icon ml-3" style="font-size: 0.8rem;"></i>
                </div>
            </div>

            <div class="collapse mt-1" id="subReorderItem">
                <div class="list-group list-group-flush pl-3 bg-white" style="border-radius: 5px; border: 1px solid #ddd;">
                    <a href="/inventory/inventory/executive-reorder-item-ready-for-po-list"
                       class="list-group-item list-group-item-action d-flex align-items-center py-2">
                        <small>Ready for PO</small>
                        <div class="ml-auto d-flex align-items-center">
                            <span class="role-count badge bg-info mr-2">
                                <?= $reorderItemReadyForPo ?>
                            </span>
                            <i class="fas fa-chevron-right transition-icon" style="font-size: 0.8rem;"></i>
                        </div>
                    </a>

                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* Prevent the parent from hiding when child is active */
    #inventoryAccordion.collapse.show {
        display: block !important;
        height: auto !important;
    }

    .inventory-sub-trigger[aria-expanded="true"] .transition-icon {
        transform: rotate(180deg);
    }

    .transition-icon {
        transition: transform 0.2s ease;
    }
</style>

<script>
    $(document).ready(function () {
        // Custom trigger for nested items
        $('.inventory-sub-trigger').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation(); // Stops the click from closing the parent accordion

            var targetId = $(this).attr('data-target');
            $(targetId).collapse('toggle');

            // Toggle rotation class manually
            var isExpanded = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', !isExpanded);
        });
    });
</script>