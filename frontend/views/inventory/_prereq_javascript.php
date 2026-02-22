<?php
use yii\helpers\Url;

/* @var $master mixed */
/* @var $items array */
/* @var $isView boolean */
/* @var $isUpdate boolean */
/* @var $moduleIndex string */
/* @var $hasSuperiorUpdate boolean */
/* @var $supplierList array */
/* @var $brandList array */
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
var PrereqForm = (function() {
    'use strict';
    
    // Configuration
    const config = {
        masterId: '<?= $master->id ?? '' ?>',
        moduleIndex: '<?= $moduleIndex ?>',
        isView: <?= $isView ? 'true' : 'false' ?>,
        isUpdate: <?= $isUpdate ? 'true' : 'false' ?>,
        hasSuperiorUpdate: <?= $hasSuperiorUpdate ? 'true' : 'false' ?>,
        duplicateCheckUrl: '<?= Url::to(['inventory-check-duplicate']) ?>',
        addRowUrl: '<?= Url::to(['ajax-add-form-item']) ?>',
        csrfToken: $('meta[name="csrf-token"]').attr('content')
    };
    
    let currentKey = $('tr[id^="tr_"]').length;
    let duplicateCheckTimeout;
    const supplierList = <?= json_encode($supplierList) ?>;
    const brandList = <?= json_encode($brandList) ?>;
    
    // Public API
    const publicAPI = {
        init: function() {
            this.initCSRF();
            this.initTotals();
            this.initDropdowns(); // ✅ Added here
            
            if (!config.isView) {
                this.initDuplicateCheck();
                this.initEventListeners();
                this.initSubmitHandler();
                this.checkAllDuplicates();
                this.buildCurrencyTotals();
            }
            
            if (config.hasSuperiorUpdate && config.isView) {
                this.initSuperiorUpdateView();
            }
        },
        
        initCSRF: function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': config.csrfToken
                }
            });
        },
        
        initTotals: function() {
            $("#listTBody tr").each(function() {
                const input = $(this).find('input[name*="quantity"], input[name*="unit_price"]').first();
                if (input.length) {
                    publicAPI.updateTotal(input[0]);
                }
            });
        },
        
        // ✅ NEW METHOD - Initialize dropdowns
        initDropdowns: function() {
            // Initialize dropdowns with selected values
            $('.department-select, .supplier-select, .brand-select, .currency-select').each(function() {
                const $select = $(this);
                const currentValue = $select.val();
                
                if (currentValue && currentValue !== '') {
                    // Check if value exists in options
                    const optionExists = $select.find('option[value="' + currentValue + '"]').length > 0;
                    
                    if (optionExists) {
                        // Set the value and trigger change
                        $select.val(currentValue).trigger('change');
                    } else {
                        // If value doesn't exist in options but we need to show it
                        console.warn('Value "' + currentValue + '" not found in dropdown options for', $select.attr('name'));
                        
                        // Add the missing option
                        $select.append($('<option>', {
                            value: currentValue,
                            text: currentValue,
                            selected: true
                        }));
                    }
                }
            });
        },
        
        initDuplicateCheck: function() {
            $('#listTBody').off('input change', '.supplier-select, .supplier-field, .brand-select, .brand-field, .model-field, .department-select');
            
            $('#listTBody').on('input change', '.supplier-select, .supplier-field, .brand-select, .brand-field, .model-field, .department-select', function() {
                const $row = $(this).closest('tr');
                
                clearTimeout(duplicateCheckTimeout);
                duplicateCheckTimeout = setTimeout(function() {
                    publicAPI.checkFormDuplicates();
                    
                    if (config.moduleIndex === 'inventory') {
                        publicAPI.checkRowInventoryDuplicate($row);
                    }
                }, 300);
            });
        },
        
        initEventListeners: function() {
            $('#listTBody').on('input', '.quantity-input, .unit-price-input', function() {
                publicAPI.updateTotal(this);
            });
            
            $('#listTBody').on('change', '.currency-select', function() {
                publicAPI.buildCurrencyTotals();
            });
        },
        
        initSubmitHandler: function() {
            $('form').on('submit', function(e) {
                const duplicateCount = $('.duplicate-row').length;
                if (duplicateCount > 0) {
                    e.preventDefault();
                    alert('Cannot submit form: Please resolve ' + duplicateCount + ' duplicate item(s) first.');
                    return false;
                }
            });
        },
        
        // ================= TOTAL AMOUNT FUNCTIONALITY =================
        buildCurrencyTotals: function() {
            const currencies = new Set();
            
            // Collect unique currencies
            $('.currency-select').each(function() {
                const currency = $(this).val();
                if (currency) currencies.add(currency);
            });
            
            // Remove existing total rows
            $('tr[id^="total_amount"]').remove();
            
            let tfoot = $("#item_table tfoot");
            
            // Create total rows for each currency
            currencies.forEach(currency => {
                if (!currency) return;
                
                const tr = document.createElement("tr");
                tr.id = "total_amount";
                
                const tdLabel = document.createElement("td");
                tdLabel.colSpan = 9;
                tdLabel.style.textAlign = "right";
                tdLabel.textContent = "Total Amount: (" + currency + ")";
                tr.appendChild(tdLabel);
                
                const tdValue = document.createElement("td");
                tdValue.colSpan = 9;
                
                if (!config.isView) {
                    const input = document.createElement("input");
                    input.type = "number";
                    input.id = "totalAmount-" + currency;
                    input.readOnly = true;
                    input.className = "form-control";
                    tdValue.appendChild(input);
                } else {
                    const span = document.createElement("span");
                    span.id = "totalAmount-" + currency;
                    tdValue.appendChild(span);
                }
                
                tr.appendChild(tdValue);
                tfoot.prepend(tr);
                
                // Calculate total for this currency
                publicAPI.updateTotalAmount(currency);
            });
        },
        
        updateTotalAmount: function(currency) {
            let total = 0;
            const table = document.getElementById('item_table');
            const totalAmountCell = document.getElementById('totalAmount-' + currency);
            
            if (!totalAmountCell) return;
            
            // Reset to 0 first
            if (config.isView) {
                totalAmountCell.textContent = "0.00";
            } else {
                totalAmountCell.value = "0.00";
            }
            
            // Calculate total for this currency
            table.querySelectorAll("#listTBody tr").forEach(row => {
                let currencyCell = '';
                if (config.isView) {
                    // In view mode, get from column 8 (index 7) - adjust if needed
                    currencyCell = (row.cells[7]?.textContent || "").trim();
                } else {
                    // In edit mode, get from dropdown
                    const dropdown = row.querySelector('.currency-select');
                    currencyCell = dropdown ? dropdown.value.trim() : "";
                }
                
                if (currencyCell === currency.trim()) {
                    let val = 0;
                    if (config.isView) {
                        // In view mode, get from column 10 (index 9) - total price column
                        const totalText = row.cells[9]?.textContent || "0";
                        val = parseFloat(totalText.replace(/,/g, '')) || 0;
                    } else {
                        // In edit mode, get from total price input
                        const input = row.querySelector('.total-price-input');
                        val = parseFloat(input?.value || 0);
                    }
                    if (!isNaN(val)) total += val;
                }
            });
            
            // Update display
            if (config.isView) {
                totalAmountCell.textContent = total.toLocaleString('en-US', {
                    minimumFractionDigits: 2, 
                    maximumFractionDigits: 2
                });
            } else {
                totalAmountCell.value = total.toFixed(2);
            }
        },
        
        updateCurrencyTotals: function() {
            // Rebuild all currency totals
            this.buildCurrencyTotals();
        },
        
        // ================= DUPLICATE CHECKING =================
        checkAllDuplicates: function() {
            this.checkFormDuplicates();
            
            if (config.moduleIndex === 'inventory') {
                $('#listTBody tr').each(function(index) {
                    const $row = $(this);
                    setTimeout(function() {
                        publicAPI.checkRowInventoryDuplicate($row);
                    }, index * 50);
                });
            }
        },
        
        checkFormDuplicates: function() {
            const rowData = [];
            const duplicateKeys = new Set();

            $('#listTBody tr').each(function(index) {
                const $row = $(this);
                const rowKey = publicAPI.getRowKey($row);

                if (rowKey) {
                    rowData.push({
                        row: $row,
                        key: rowKey,
                        index: index
                    });
                } else {
                    if (!$row.hasClass('inventory-duplicate')) {
                        $row.removeClass('duplicate-row form-duplicate');
                        $row.find('.duplicate-warning').hide();
                    }
                }
            });

            for (let i = 0; i < rowData.length; i++) {
                for (let j = i + 1; j < rowData.length; j++) {
                    if (rowData[i].key === rowData[j].key) {
                        duplicateKeys.add(rowData[i].key);
                    }
                }
            }

            rowData.forEach(item => {
                if (duplicateKeys.has(item.key)) {
                    item.row.addClass('duplicate-row form-duplicate');
                    if (!item.row.hasClass('inventory-duplicate')) {
                        item.row.find('.duplicate-warning').text('⚠ Duplicate item in this form').show();
                    }
                } else {
                    item.row.removeClass('form-duplicate');
                    if (!item.row.hasClass('inventory-duplicate')) {
                        item.row.removeClass('duplicate-row');
                        item.row.find('.duplicate-warning').hide();
                    }
                }
            });

            this.toggleSaveButton();
        },
        
        checkRowInventoryDuplicate: function($row) {
    const department = $row.find('.department-select').val();
    const supplier = $row.find('.supplier-select').val();
    const brand = $row.find('.brand-select').val();
    const model = $row.find('.model-field').val();

    console.log('Checking duplicate:', {
        department: department,
        supplier: supplier,
        brand: brand,
        model: model
    });

    if (!department || !supplier || !brand || !model) {
        console.log('Missing required fields, skipping check');
        $row.removeClass('inventory-duplicate');
        if (!$row.hasClass('form-duplicate')) {
            $row.removeClass('duplicate-row');
            $row.find('.duplicate-warning').hide();
        }
        this.toggleSaveButton();
        return;
    }

    const deptVal = String(department).trim();
    const suppVal = String(supplier).trim();
    const brandVal = String(brand).trim();
    const modelVal = String(model).trim();

    console.log('Trimmed values:', {
        deptVal: deptVal,
        suppVal: suppVal,
        brandVal: brandVal,
        modelVal: modelVal
    });

    $.ajax({
        url: config.duplicateCheckUrl,
        type: 'GET',
        dataType: 'json',
        data: {
            department: deptVal,
            supplier: suppVal,
            brand: brandVal,
            model: modelVal
        },
        success: (response) => {
            console.log('Duplicate check response:', response);
            
            if (response.exists === true) {
                $row.addClass('duplicate-row inventory-duplicate');
                $row.find('.duplicate-warning').text('⚠ This item already exists in inventory').show();
            } else {
                $row.removeClass('inventory-duplicate');
                if (!$row.hasClass('form-duplicate')) {
                    $row.removeClass('duplicate-row');
                    $row.find('.duplicate-warning').hide();
                }
            }
            this.toggleSaveButton();
        },
        error: (xhr, status, error) => {
            console.error('AJAX Error:', {
                status: status,
                error: error,
                response: xhr.responseText
            });
        }
    });
},
        
        getRowKey: function($row) {
            const department = $row.find('.department-select').val();
            let supplier = $row.find('.supplier-select').val();
            if (!supplier) {
                supplier = $row.find('.supplier-field').val();
            }
            let brand = $row.find('.brand-select').val();
            if (!brand) {
                brand = $row.find('.brand-field').val();
            }
            const model = $row.find('.model-field').val();

            const deptVal = (department || '').trim();
            const suppVal = (supplier || '').trim();
            const brandVal = (brand || '').trim();
            const modelVal = (model || '').trim();

            if (!deptVal || !suppVal || !brandVal || !modelVal) {
                return null;
            }

            return `${deptVal}|${suppVal}|${brandVal}|${modelVal}`.toLowerCase();
        },
        
        toggleSaveButton: function() {
            const duplicateCount = $('.duplicate-row').length;
            const $saveBtn = $('#save-btn');

            if (duplicateCount > 0) {
                $saveBtn.prop('disabled', true)
                        .removeClass('btn-success')
                        .addClass('btn-secondary')
                        .attr('title', 'Cannot save: Duplicate items detected');
            } else {
                $saveBtn.prop('disabled', false)
                        .removeClass('btn-secondary')
                        .addClass('btn-success')
                        .removeAttr('title');
            }
        },
        
        // ================= ROW MANAGEMENT =================
        addRow: function() {
            $.ajax({
                url: config.addRowUrl,
                dataType: 'html',
                data: {
                    masterId: config.masterId,
                    key: currentKey++,
                    moduleIndex: config.moduleIndex,
                    hasSuperiorUpdate: config.hasSuperiorUpdate,
                },
                success: (response) => {
                    const $newRow = $(response);
                    $("#listTBody").append($newRow);
                    
                    const input = $newRow.find('input[name*="quantity"], input[name*="unit_price"]').first();
                    if (input.length) {
                        publicAPI.updateTotal(input[0]);
                    }
                    
                    this.updateRowIndices();
                    this.buildCurrencyTotals();
                },
                error: (xhr, status, error) => {
                    alert('Failed to add row. Please try again.');
                }
            });
        },
        
        removeRow: function(rowNum) {
            if (!confirm("Remove this row?")) return;
            
            $("#tr_" + rowNum).remove();
            this.updateRowIndices();
            this.buildCurrencyTotals();
            this.checkAllDuplicates();
        },
        
        updateRowIndices: function() {
            $('#listTBody tr:visible').each(function(i) {
                $(this).find('td:first').text(i + 1);
            });
        },
        
        // ================= CORE FUNCTIONALITY =================
        updateTotal: function(input) {
            const row = $(input).closest('tr');
            const qty = parseFloat(row.find('.quantity-input').val()) || 0;
            const unit = parseFloat(row.find('.unit-price-input').val()) || 0;
            const total = (qty * unit).toFixed(2);

            row.find('.total-price-input').val(total);
            
            // Update the total amount for this row's currency
            const currency = row.find('.currency-select').val();
            if (currency) {
                this.updateTotalAmount(currency);
            }
        },
        
        // ================= SUPERIOR UPDATE VIEW =================
        initSuperiorUpdateView: function() {
            const STATUS_APPROVED = "<?= \frontend\models\RefGeneralStatus::STATUS_Approved ?>";
            const STATUS_REJECTED = "<?= \frontend\models\RefGeneralStatus::STATUS_SuperiorRejected ?>";
            
            function updateCurrencyTotal(currency) {
                let submittedTotal = 0;
                let approvedTotal = 0;

                document.querySelectorAll('#listTBody tr').forEach(row => {
                    const submittedCurrency = (row.cells[7]?.textContent || "").trim();
                    if (submittedCurrency === currency) {
                        const submittedPriceText = row.cells[9]?.textContent || "0";
                        const submittedPrice = parseFloat(submittedPriceText.replace(/,/g, '')) || 0;
                        submittedTotal += submittedPrice;
                    }

                    const approvedCurrency = (row.cells[12]?.textContent || "").trim();
                    if (approvedCurrency === currency) {
                        const statusText = row.cells[15]?.textContent || "";
                        if (statusText.includes('Approved')) {
                            const approvedPriceText = row.cells[14]?.textContent || "0";
                            const approvedPrice = parseFloat(approvedPriceText.replace(/,/g, '')) || 0;
                            approvedTotal += approvedPrice;
                        }
                    }
                });

                const submittedEl = document.getElementById('totalAmountSubmitted-' + currency);
                const approvedEl = document.getElementById('totalAmountApproved-' + currency);
                
                if (submittedEl) {
                    submittedEl.textContent = submittedTotal.toLocaleString('en-US', {
                        minimumFractionDigits: 2, 
                        maximumFractionDigits: 2
                    });
                }
                
                if (approvedEl) {
                    approvedEl.textContent = approvedTotal.toLocaleString('en-US', {
                        minimumFractionDigits: 2, 
                        maximumFractionDigits: 2
                    });
                }
            }
            
            function buildCurrencyTotals() {
                const currencies = new Set();
                document.querySelectorAll("#listTBody tr").forEach(row => {
                    const submittedCurrency = (row.cells[7]?.textContent || "").trim();
                    if (submittedCurrency) currencies.add(submittedCurrency);
                    
                    const approvedCurrency = (row.cells[12]?.textContent || "").trim();
                    if (approvedCurrency) currencies.add(approvedCurrency);
                });

                document.querySelectorAll('tr[id^="total_amount_"]').forEach(row => row.remove());

                let tfoot = document.querySelector("#item_table tfoot");
                if (!tfoot) {
                    tfoot = document.createElement("tfoot");
                    document.querySelector("#item_table").appendChild(tfoot);
                }

                currencies.forEach(currency => {
                    const tr = document.createElement("tr");
                    tr.id = "total_amount_" + currency;
                    tr.className = "table-sm font-weight-bold";

                    const tdLabel = document.createElement("td");
                    tdLabel.colSpan = 9;
                    tdLabel.className = "text-right";
                    tdLabel.style.padding = "10px";
                    tdLabel.textContent = `Total Amount (${currency}):`;
                    tr.appendChild(tdLabel);

                    const tdSubmitted = document.createElement("td");
                    tdSubmitted.style.padding = "10px";
                    tdSubmitted.colSpan = 2;
                    tdSubmitted.innerHTML = `<span class="text-muted">Requested:</span> <span id="totalAmountSubmitted-${currency}" class="fw-bold">0.00</span>`;
                    tr.appendChild(tdSubmitted);

                    const tdApproved = document.createElement("td");
                    tdApproved.style.padding = "10px";
                    tdApproved.colSpan = 5;
                    tdApproved.innerHTML = `<span class="text-muted">Approved:</span> <span id="totalAmountApproved-${currency}" class="fw-bold text-success">0.00</span>`;
                    tr.appendChild(tdApproved);

                    const filler = document.createElement("td");
                    filler.colSpan = 3;
                    tr.appendChild(filler);

                    tfoot.prepend(tr);
                    updateCurrencyTotal(currency);
                });
            }
            
            buildCurrencyTotals();
        }
    };
    
    return publicAPI;
})();

$(document).ready(function() {
    PrereqForm.init();
});
</script>