<?php

use frontend\models\test\RefTestStatus;
use yii\helpers\Html;
use frontend\models\test\TestFormAts;
use frontend\models\test\TestDetailAts;
?>
<style>
    .cbvcheader{
        padding:0.25rem;
    }
</style>
<table class="table table-sm table-bordered" id="dynamicAtsTable">
    <thead>
        <tr id="headerRow1">
            <th class="vmiddle text-center" rowspan="3">SCENARIO<br>NO.</th>
            <th class="vmiddle text-center" rowspan="3">CONDITION</th>

            <th class="vmiddle text-center" colspan="2" rowspan="2" data-column-group="incoming-breaker">
                <a href="#" class="remove-group-btn" data-target-group="incoming-breaker" title="Remove Incoming Breaker Group"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>INCOMING BREAKER<br>STATUS
                <a href="#" class="add-sub-column-btn" data-parent-group="incoming-breaker" title="Add NO.X Column to group"><i class='fa fa-plus-circle text-success'></i></a>
            </th>

            <th class="vmiddle text-center" colspan="7" data-column-group="auto-transfer-switch">
                <a href="#" class="remove-group-btn" data-target-group="auto-transfer-switch" title="Remove ATS Group"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>AUTO TRANSFER SWITCH STATUS
                <a href="#" class="add-sub-group-btn" data-parent-group="auto-transfer-switch" title="Add Kx Group"><i class='fa fa-plus-circle text-success'></i></a>
            </th>

            <th class="vmiddle text-center" colspan="2" data-column-group="dc-supply" rowspan="2">
                <a href="#" class="remove-group-btn" data-target-group="dc-supply" title="Remove DC Supply Group"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>DC SUPPLY
                <a href="#" class="add-sub-column-btn" data-parent-group="dc-supply" title="Add another DC Result Column to group"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
            <th class="vmiddle text-center" rowspan="3" data-column-id="result">
                <a href="#" class="remove-column-btn" data-target-col-id="result" title="Remove Result Column"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>RESULT
            </th>
        </tr>
        <tr id="headerRow2">
            <th class="vmiddle text-center" colspan="2" data-column-type="K1" data-parent-group="auto-transfer-switch">
                <a href="#" class="remove-group-type-btn" data-target-type="K1" title="Remove K1 Group"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>K1
                <a href="#" class="add-sub-column-btn" data-parent-type="K1" title="Add K1 Sub-column to group"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
            <th class="vmiddle text-center" colspan="2" data-column-type="K2" data-parent-group="auto-transfer-switch">
                <a href="#" class="remove-group-type-btn" data-target-type="K2" title="Remove K2 Group"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>K2
                <a href="#" class="add-sub-column-btn" data-parent-type="K2" title="Add K2 Sub-column to group"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
            <th class="vmiddle text-center" colspan="3" data-column-type="K3" data-parent-group="auto-transfer-switch">
                <a href="#" class="remove-group-type-btn" data-target-type="K3" title="Remove K3 Group"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>K3
                <a href="#" class="add-sub-column-btn" data-parent-type="K3" title="Add K3 Sub-column to group"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
        </tr>
        <tr id="headerRow3">
            <th class="vmiddle text-center" data-column-id="no.1" data-parent-group="incoming-breaker">
                <a href="#" class="remove-column-btn" data-target-col-id="no.1" title="Remove NO.1"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>NO.1
                <a href="#" class="add-column-btn" data-target-col-id="no.1" data-parent-group="incoming-breaker" title="Add another NO.X Column"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
            <th class="vmiddle text-center" data-column-id="no.2" data-parent-group="incoming-breaker">
                <a href="#" class="remove-column-btn" data-target-col-id="no.2" title="Remove NO.2"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>NO.2
                <a href="#" class="add-column-btn" data-target-col-id="no.2" data-parent-group="incoming-breaker" title="Add another NO.X Column"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
            <th class="vmiddle text-center" data-column-id="k1-a" data-parent-type="K1">
                <a href="#" class="remove-column-btn" data-target-col-id="k1-a" title="Remove K1-A"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>K1-A
                <a href="#" class="add-column-btn" data-target-col-id="k1-a" data-parent-type="K1" title="Add another K1-X Column"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
            <th class="vmiddle text-center" data-column-id="k1-b" data-parent-type="K1">
                <a href="#" class="remove-column-btn" data-target-col-id="k1-b" title="Remove K1-B"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>K1-B
                <a href="#" class="add-column-btn" data-target-col-id="k1-b" data-parent-type="K1" title="Add another K1-X Column"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
            <th class="vmiddle text-center" data-column-id="k2-a" data-parent-type="K2">
                <a href="#" class="remove-column-btn" data-target-col-id="k2-a" title="Remove K2-A"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>K2-A
                <a href="#" class="add-column-btn" data-target-col-id="k2-a" data-parent-type="K2" title="Add another K2-X Column"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
            <th class="vmiddle text-center" data-column-id="k2-b" data-parent-type="K2">
                <a href="#" class="remove-column-btn" data-target-col-id="k2-b" title="Remove K2-B"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>K2-B
                <a href="#" class="add-column-btn" data-target-col-id="k2-b" data-parent-type="K2" title="Add another K2-X Column"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
            <th class="vmiddle text-center" data-column-id="k3-a" data-parent-type="K3">
                <a href="#" class="remove-column-btn" data-target-col-id="k3-a" title="Remove K3-A"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>K3-A
                <a href="#" class="add-column-btn" data-target-col-id="k3-a" data-parent-type="K3" title="Add another K3-X Column"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
            <th class="vmiddle text-center" data-column-id="k3-b" data-parent-type="K3">
                <a href="#" class="remove-column-btn" data-target-col-id="k3-b" title="Remove K3-B"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>K3-B
                <a href="#" class="add-column-btn" data-target-col-id="k3-b" data-parent-type="K3" title="Add another K3-X Column"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
            <th class="vmiddle text-center" data-column-id="on-result" data-parent-group="dc-supply">
                <a href="#" class="remove-column-btn" data-target-col-id="on-result" title="Remove ON Result"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>ON<br>RESULT
                <a href="#" class="add-column-btn" data-target-col-id="on-result" data-parent-group="dc-supply" title="Add another ON Result Column"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
            <th class="vmiddle text-center" data-column-id="off-result" data-parent-group="dc-supply">
                <a href="#" class="remove-column-btn" data-target-col-id="off-result" title="Remove OFF Result"><i class='fa fa-minus-circle text-danger'></i></a>
                <br>OFF<br>RESULT
                <a href="#" class="add-column-btn" data-target-col-id="off-result" data-parent-group="dc-supply" title="Add another OFF Result Column"><i class='fa fa-plus-circle text-success'></i></a>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr class="vmiddle text-center">
            <td>1</td>
            <td>NORMAL</td>
            <td>CLOSE</td>
            <td>CLOSE</td>
            <td>CLOSE</td>
            <td></td>
            <td>CLOSE</td>
            <td></td>
            <td>OPEN</td>
            <td>CLOSE</td>
            <td>(PASS/FAIL)</td>
            <td>(PASS/FAIL)</td>
            <td>(PASS/FAIL)</td>
        </tr>
        <tr class="vmiddle text-center">
            <td>2</td>
            <td>MAIN INCOMING 1 POWER FAIL<br>K1 'OPEN' & K3 'CLOSE'<br>AUTOMATICALLY AFTER PRESET<br>TIME DELAY</td>
            <td>CLOSE</td>
            <td>CLOSE</td>
            <td>OPEN</td>
            <td></td>
            <td>CLOSE</td>
            <td></td>
            <td>CLOSE</td>
            <td></td>
            <td>(PASS/FAIL)</td>
            <td>(PASS/FAIL)</td>
            <td>(PASS/FAIL)</td>
        </tr>
    </tbody>
</table>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('dynamicAtsTable');
    if (!table) {
        console.error("Table with ID 'dynamicAtsTable' not found.");
        return;
    }

    const headerRows = [
        document.getElementById('headerRow1'),
        document.getElementById('headerRow2'),
        document.getElementById('headerRow3')
    ];
    const tbody = table.querySelector('tbody');

    // Helper: Get the effective column index of a <th> in its specific row
    // This accounts for colspans and gives the "visual" start index for that row.
    function getThEffectiveIndex(thElement) {
        const row = thElement.parentNode;
        let index = 0;
        for (let i = 0; i < row.children.length; i++) {
            if (row.children[i] === thElement) {
                return index;
            }
            index += row.children[i].colSpan || 1;
        }
        return -1;
    }

    // Helper: Get the actual <td> index in the tbody for a given header column ID.
    // This is crucial because header rows have different structures due to colspans/rowspans.
    function getTbodyColumnIndex(columnId) {
        let colIndex = -1;
        const targetTh = headerRows[2].querySelector(`th[data-column-id="${columnId}"]`);
        
        if (targetTh) {
            // If it's a regular leaf column in headerRow3
            colIndex = getThEffectiveIndex(targetTh);
        } else {
            // Check if it's a column with rowspan=3 (like 'result') or rowspan=2 (like 'on-result', 'off-result')
            // which are actually declared in headerRow1 but conceptually are leaf columns in terms of data.
            const targetTh1 = headerRows[0].querySelector(`th[data-column-id="${columnId}"]`);
            if (targetTh1) {
                colIndex = getThEffectiveIndex(targetTh1);
            }
        }
        return colIndex;
    }

    // --- Core Function: Remove a specific leaf column (Tier 3 or rowspan=2/3) ---
    function removeColumn(columnId) {
        const confirmRemoval = confirm(`Are you sure you want to remove the column '${columnId}'?`);
        if (!confirmRemoval) return;

        let targetTh = null;
        let parentTh = null; // Parent in headerRow1 or headerRow2
        let grandParentTh = null; // Grandparent in headerRow1

        // 1. Find the target <th> and its parent(s)
        const th3 = headerRows[2].querySelector(`th[data-column-id="${columnId}"]`);
        if (th3) {
            targetTh = th3;
            const parentType = targetTh.dataset.parentType; // K1, K2, K3
            const parentGroup = targetTh.dataset.parentGroup; // incoming-breaker

            if (parentType) { // For K1-A, K1-B, etc.
                parentTh = headerRows[1].querySelector(`th[data-column-type="${parentType}"]`);
                if (parentTh && parentTh.dataset.parentGroup) {
                    grandParentTh = headerRows[0].querySelector(`th[data-column-group="${parentTh.dataset.parentGroup}"]`);
                }
            } else if (parentGroup) { // For NO.1, NO.2
                parentTh = headerRows[0].querySelector(`th[data-column-group="${parentGroup}"]`);
            }
        } else {
            // Check for columns defined in headerRow1 with rowspan=3 or rowspan=2
            const th1 = headerRows[0].querySelector(`th[data-column-id="${columnId}"]`);
            if (th1 && (th1.getAttribute('rowspan') === '3' || th1.getAttribute('rowspan') === '2')) {
                targetTh = th1;
                // For 'on-result'/'off-result', their parent is 'dc-supply' in headerRow1
                if (targetTh.dataset.parentGroup === 'dc-supply') {
                    parentTh = headerRows[0].querySelector(`th[data-column-group="dc-supply"]`);
                }
            }
        }

        if (!targetTh) {
            console.warn(`Column with ID '${columnId}' not found.`);
            return;
        }

        const tbodyColIndex = getTbodyColumnIndex(columnId);
        if (tbodyColIndex === -1) {
            console.error(`Could not determine tbody column index for '${columnId}'.`);
            return;
        }

        // 2. Remove the <th> element
        targetTh.remove();

        // 3. Adjust colspans of parent(s)
        if (parentTh) {
            let currentParentColspan = parseInt(parentTh.getAttribute('colspan') || 1);
            parentTh.setAttribute('colspan', currentParentColspan - 1);
            if (currentParentColspan - 1 <= 0) { // If parent becomes empty
                parentTh.remove();
            }
        }
        if (grandParentTh) {
            let currentGrandParentColspan = parseInt(grandParentTh.getAttribute('colspan') || 1);
            grandParentTh.setAttribute('colspan', currentGrandParentColspan - 1);
            if (currentGrandParentColspan - 1 <= 0) { // If grandparent becomes empty
                grandParentTh.remove();
            }
        }

        // 4. Remove corresponding <td> cells from all body rows
        Array.from(tbody.rows).forEach(row => {
            if (row.cells[tbodyColIndex]) {
                row.deleteCell(tbodyColIndex);
            }
        });

        console.log(`Column '${columnId}' removed.`);
    }

    // --- Core Function: Add a new sibling column (Tier 3) next to an existing one ---
    function addColumn(targetColId, parentType, parentGroup) {
        let newColId, newColTitle;
        let insertionIndexInTbody = -1;
        const targetLeafTh = headerRows[2].querySelector(`th[data-column-id="${targetColId}"]`) ||
                             headerRows[0].querySelector(`th[data-column-id="${targetColId}"]`); // For rowspan=2/3 columns

        if (!targetLeafTh) {
            console.error(`Target column for addition not found: ${targetColId}`);
            return;
        }

        let parentTh = null;
        let grandParentTh = null;
        let newTh = document.createElement('th');
        newTh.className = 'vmiddle text-center';

        // Determine parent(s) and generate new ID/Title
        if (parentType) { // K1, K2, K3 sub-columns (e.g., K1-A, K1-B -> adding K1-C)
            parentTh = headerRows[1].querySelector(`th[data-column-type="${parentType}"]`);
            if (parentTh && parentTh.dataset.parentGroup) {
                grandParentTh = headerRows[0].querySelector(`th[data-column-group="${parentTh.dataset.parentGroup}"]`);
            }

            const existingLeafs = Array.from(headerRows[2].querySelectorAll(`th[data-parent-type="${parentType}"]`));
            const lastLeafId = existingLeafs.length > 0 ? existingLeafs[existingLeafs.length - 1].dataset.columnId : `${parentType.toLowerCase()}-a`;
            const lastChar = lastLeafId.slice(-1).toUpperCase();
            const nextLetterCode = String.fromCharCode(lastChar.charCodeAt(0) + 1);
            
            newColId = `${parentType.toLowerCase()}-${nextLetterCode.toLowerCase()}`;
            newColTitle = `${parentType}-${nextLetterCode}`;

            newTh.dataset.columnId = newColId;
            newTh.dataset.parentType = parentType;
            newTh.innerHTML = `<a href="#" class="remove-column-btn" data-target-col-id="${newColId}" title="Remove ${newColTitle}"><i class='fa fa-minus-circle text-danger'></i></a>
                                <br>${newColTitle}
                                <a href="#" class="add-column-btn" data-target-col-id="${newColId}" data-parent-type="${parentType}" title="Add another ${newColTitle} Column"><i class='fa fa-plus-circle text-success'></i></a>`;
            
            targetLeafTh.after(newTh); // Insert new column after the clicked one
            insertionIndexInTbody = getTbodyColumnIndex(targetColId) + 1;

        } else if (parentGroup === 'incoming-breaker') { // NO.1, NO.2 -> adding NO.3
            parentTh = headerRows[0].querySelector(`th[data-column-group="incoming-breaker"]`);
            
            const existingLeafs = Array.from(headerRows[2].querySelectorAll(`th[data-parent-group="incoming-breaker"]`));
            const lastLeafId = existingLeafs.length > 0 ? existingLeafs[existingLeafs.length - 1].dataset.columnId : `no.0`; // Sentinel for first case
            const match = lastLeafId.match(/no\.(\d+)/);
            const nextNumber = match ? parseInt(match[1]) + 1 : 1;
            
            newColId = `no.${nextNumber}`;
            newColTitle = `NO.<br>${nextNumber}`;

            newTh.dataset.columnId = newColId;
            newTh.dataset.parentGroup = parentGroup;
            newTh.innerHTML = `<a href="#" class="remove-column-btn" data-target-col-id="${newColId}" title="Remove ${newColTitle}"><i class='fa fa-minus-circle text-danger'></i></a>
                                <br>${newColTitle}
                                <a href="#" class="add-column-btn" data-target-col-id="${newColId}" data-parent-group="${parentGroup}" title="Add another ${newColTitle} Column"><i class='fa fa-plus-circle text-success'></i></a>`;
            
            targetLeafTh.after(newTh);
            insertionIndexInTbody = getTbodyColumnIndex(targetColId) + 1;
        } else if (parentGroup === 'dc-supply') { // ON/OFF Result -> adding another DC Result
            parentTh = headerRows[0].querySelector(`th[data-column-group="dc-supply"]`);

            newColId = `new-dc-result-${Date.now()}`; // Unique ID for new generic DC result
            newColTitle = `NEW<br>DC<br>RESULT`;

            newTh.dataset.columnId = newColId;
            newTh.dataset.parentGroup = parentGroup;
            newTh.setAttribute('rowspan', '2'); // DC Supply results span 2 rows
            newTh.innerHTML = `<a href="#" class="remove-column-btn" data-target-col-id="${newColId}" title="Remove ${newColTitle}"><i class='fa fa-minus-circle text-danger'></i></a>
                                <br>${newColTitle}
                                <a href="#" class="add-column-btn" data-target-col-id="${newColId}" data-parent-group="${parentGroup}" title="Add another ${newColTitle} Column"><i class='fa fa-plus-circle text-success'></i></a>`;
            
            targetLeafTh.after(newTh); // Insert into headerRow1
            insertionIndexInTbody = getTbodyColumnIndex(targetColId) + 1;
        } else {
            console.warn('Unhandled add column scenario for targetColId:', targetColId);
            return;
        }

        // Adjust colspans for parent(s)
        if (parentTh) {
            parentTh.colSpan = parseInt(parentTh.colSpan) + 1;
        }
        if (grandParentTh) {
            grandParentTh.colSpan = parseInt(grandParentTh.colSpan) + 1;
        }

        // Add empty <td> cells to all body rows at the determined insertion index
        Array.from(tbody.rows).forEach(row => {
            const newTd = document.createElement('td');
            newTd.textContent = ''; 

            if (insertionIndexInTbody !== -1 && row.cells[insertionIndexInTbody - 1]) {
                 row.cells[insertionIndexInTbody - 1].after(newTd);
            } else if (insertionIndexInTbody === 0 && row.cells.length > 0) {
                 row.cells[0].before(newTd); // Insert at the very beginning if it's the first column conceptually
            }
            else {
                 row.appendChild(newTd); // Fallback: append if insertion point is unclear
            }
        });

        console.log(`New column '${newColId}' added.`);
    }

    // --- Core Function: Add a new sub-column (Tier 3) or a new group of sub-columns from group-level buttons ---
    function addMainSubColumn(contextType, parentIdentifier) {
        let newColId, newColTitle, newTh;
        let insertionIndexInTbody = -1;
        let parentTh = null;
        let grandParentTh = null;

        if (contextType === 'parent-type') { // Adding to K1, K2, K3 groups (from row2 button)
            const parentTh2 = headerRows[1].querySelector(`th[data-column-type="${parentIdentifier}"]`);
            if (!parentTh2) { console.error('Parent type not found:', parentIdentifier); return; }
            parentTh = parentTh2;

            const existingLeafs = Array.from(headerRows[2].querySelectorAll(`th[data-parent-type="${parentIdentifier}"]`));
            const lastLeaf = existingLeafs.length > 0 ? existingLeafs[existingLeafs.length - 1] : null;

            let nextLetterCode = 'A';
            if (lastLeaf) {
                const lastId = lastLeaf.dataset.columnId; // e.g., k1-b
                const lastChar = lastId.slice(-1).toUpperCase(); // 'B'
                nextLetterCode = String.fromCharCode(lastChar.charCodeAt(0) + 1);
            }

            newColId = `${parentIdentifier.toLowerCase()}-${nextLetterCode.toLowerCase()}`;
            newColTitle = `${parentIdentifier}-${nextLetterCode}`;

            newTh = document.createElement('th');
            newTh.className = 'vmiddle text-center';
            newTh.dataset.columnId = newColId;
            newTh.dataset.parentType = parentIdentifier;
            newTh.innerHTML = `<a href="#" class="remove-column-btn" data-target-col-id="${newColId}" title="Remove ${newColTitle}"><i class='fa fa-minus-circle text-danger'></i></a>
                                <br>${newColTitle}
                                <a href="#" class="add-column-btn" data-target-col-id="${newColId}" data-parent-type="${parentIdentifier}" title="Add another ${newColTitle} Column"><i class='fa fa-plus-circle text-success'></i></a>`;

            if (lastLeaf) {
                lastLeaf.after(newTh);
                insertionIndexInTbody = getTbodyColumnIndex(lastLeaf.dataset.columnId) + 1;
            } else {
                // If this is the first child for this Kx group, find its parent's starting index
                // This is very complex and would require a more robust index mapping.
                // For simplicity, we'll append to the relevant section or alert user.
                let previousSiblingTh2 = parentTh2.previousElementSibling;
                let anchorLeaf = null;
                if (previousSiblingTh2) {
                    // Find the last leaf under the previous sibling (e.g., last K2-B for K3)
                    anchorLeaf = headerRows[2].querySelector(`th[data-parent-type="${previousSiblingTh2.dataset.columnType}"]:last-child`);
                }
                if (anchorLeaf) {
                    anchorLeaf.after(newTh);
                    insertionIndexInTbody = getTbodyColumnIndex(anchorLeaf.dataset.columnId) + 1;
                } else {
                    // If no prior Kx group's leaf exists (e.g. adding first K1-A), find relevant insertion point
                    const firstAtsLeaf = headerRows[2].querySelector(`th[data-parent-group="auto-transfer-switch"]:first-child`);
                    if(firstAtsLeaf) {
                        firstAtsLeaf.before(newTh);
                        insertionIndexInTbody = getTbodyColumnIndex(firstAtsLeaf.dataset.columnId);
                    } else {
                        headerRows[2].appendChild(newTh);
                        insertionIndexInTbody = getTbodyColumnIndex(newColId);
                    }
                }
            }

            // Adjust colspans
            parentTh.colSpan = parseInt(parentTh.colSpan) + 1;
            grandParentTh = headerRows[0].querySelector(`th[data-column-group="${parentTh.dataset.parentGroup}"]`);
            if (grandParentTh) {
                grandParentTh.colSpan = parseInt(grandParentTh.colSpan) + 1;
            }

        } else if (contextType === 'parent-group') { // Adding to 'incoming-breaker' or 'dc-supply' groups (from row1 button)
            const parentTh1 = headerRows[0].querySelector(`th[data-column-group="${parentIdentifier}"]`);
            if (!parentTh1) { console.error('Parent group not found:', parentIdentifier); return; }
            parentTh = parentTh1;

            if (parentIdentifier === 'incoming-breaker') {
                const existingLeafs = Array.from(headerRows[2].querySelectorAll(`th[data-parent-group="incoming-breaker"]`));
                const lastLeaf = existingLeafs.length > 0 ? existingLeafs[existingLeafs.length - 1] : null;

                let nextNumber = 1;
                if (lastLeaf) {
                    const lastId = lastLeaf.dataset.columnId; // e.g., no.2
                    const lastNum = parseInt(lastId.replace('no.', ''));
                    nextNumber = lastNum + 1;
                }
                newColId = `no.${nextNumber}`;
                newColTitle = `NO.<br>${nextNumber}`;

                newTh = document.createElement('th');
                newTh.className = 'vmiddle text-center';
                newTh.dataset.columnId = newColId;
                newTh.dataset.parentGroup = parentIdentifier;
                newTh.innerHTML = `<a href="#" class="remove-column-btn" data-target-col-id="${newColId}" title="Remove ${newColTitle}"><i class='fa fa-minus-circle text-danger'></i></a>
                                <br>${newColTitle}
                                <a href="#" class="add-column-btn" data-target-col-id="${newColId}" data-parent-group="${parentIdentifier}" title="Add another ${newColTitle} Column"><i class='fa fa-plus-circle text-success'></i></a>`;

                if (lastLeaf) {
                    lastLeaf.after(newTh);
                    insertionIndexInTbody = getTbodyColumnIndex(lastLeaf.dataset.columnId) + 1;
                } else {
                    const firstAtsLeaf = headerRows[2].querySelector(`th[data-parent-group="auto-transfer-switch"]:first-child`);
                    if(firstAtsLeaf) {
                        firstAtsLeaf.before(newTh);
                        insertionIndexInTbody = getTbodyColumnIndex(firstAtsLeaf.dataset.columnId);
                    } else {
                        headerRows[2].appendChild(newTh);
                        insertionIndexInTbody = getTbodyColumnIndex(newColId);
                    }
                }
                parentTh.colSpan = parseInt(parentTh.colSpan) + 1; // Update parent in headerRow1
                parentTh.rowSpan = 2; // Ensure parent still spans 2 rows
            }
            else if (parentIdentifier === 'dc-supply') {
                const existingLeafs = Array.from(headerRows[0].querySelectorAll(`th[data-parent-group="dc-supply"][rowspan]`));
                const lastLeaf = existingLeafs.length > 0 ? existingLeafs[existingLeafs.length - 1] : null;

                newColId = `new-dc-result-${Date.now()}`;
                newColTitle = `NEW<br>DC<br>RESULT`;

                newTh = document.createElement('th');
                newTh.className = 'vmiddle text-center';
                newTh.dataset.columnId = newColId;
                newTh.dataset.parentGroup = parentIdentifier;
                newTh.setAttribute('rowspan', '2'); // DC Supply results span 2 rows (headerRow2 and headerRow3 visually)
                newTh.innerHTML = `<a href="#" class="remove-column-btn" data-target-col-id="${newColId}" title="Remove ${newColTitle}"><i class='fa fa-minus-circle text-danger'></i></a>
                                    <br>${newColTitle}
                                    <a href="#" class="add-column-btn" data-target-col-id="${newColId}" data-parent-group="${parentIdentifier}" title="Add another ${newColTitle} Column"><i class='fa fa-plus-circle text-success'></i></a>`;

                if (lastLeaf) {
                    lastLeaf.after(newTh);
                    insertionIndexInTbody = getTbodyColumnIndex(lastLeaf.dataset.columnId) + 1;
                } else {
                    const resultTh = headerRows[0].querySelector(`th[data-column-id="result"]`);
                    if(resultTh) {
                        resultTh.before(newTh);
                        insertionIndexInTbody = getTbodyColumnIndex(newColId);
                    } else {
                         headerRows[0].appendChild(newTh);
                         insertionIndexInTbody = getTbodyColumnIndex(newColId);
                    }
                }
                parentTh.colSpan = parseInt(parentTh.colSpan) + 1; // Update parent in headerRow1
            }

        } else {
            console.warn('Unknown context type for adding main sub-column:', contextType, parentIdentifier);
            return;
        }

        // Add empty <td> cells to all body rows at the determined insertion index
        Array.from(tbody.rows).forEach(row => {
            const newTd = document.createElement('td');
            newTd.textContent = ''; 

            if (insertionIndexInTbody !== -1 && row.cells[insertionIndexInTbody - 1]) {
                 row.cells[insertionIndexInTbody - 1].after(newTd);
            } else if (insertionIndexInTbody === 0 && row.cells.length > 0) {
                 row.cells[0].before(newTd);
            }
            else {
                 row.appendChild(newTd);
            }
        });
        console.log(`New sub-column '${newColId}' added via group button.`);
    }


    // --- Core Function: Add a new group (Tier 2 or Tier 1) ---
    function addGroup(targetIdentifier, level) {
        if (level === 'group') { // Adding a new ATS group (e.g., K4) or a new major group
            if (targetIdentifier === 'auto-transfer-switch') {
                // Determine a unique ID for the new K group (e.g., K4)
                let nextKNumber = 1;
                const existingKGroups = Array.from(headerRows[1].querySelectorAll(`th[data-column-type^="K"]`));
                if (existingKGroups.length > 0) {
                    const lastKId = existingKGroups[existingKGroups.length - 1].dataset.columnType; // e.g., 'K3'
                    nextKNumber = parseInt(lastKId.replace('K', '')) + 1;
                }
                const newKType = `K${nextKNumber}`;

                // Create the new Kx group header (Tier 2)
                const newKTh2 = document.createElement('th');
                newKTh2.className = 'vmiddle text-center';
                newKTh2.setAttribute('colspan', '2'); // Start with 2 sub-columns
                newKTh2.dataset.columnType = newKType;
                newKTh2.dataset.parentGroup = 'auto-transfer-switch';
                newKTh2.innerHTML = `<a href="#" class="remove-group-type-btn" data-target-type="${newKType}" title="Remove ${newKType} Group"><i class='fa fa-minus-circle text-danger'></i></a>
                                      <br>${newKType}
                                      <a href="#" class="add-sub-column-btn" data-parent-type="${newKType}" title="Add ${newKType} Sub-column to group"><i class='fa fa-plus-circle text-success'></i></a>`;

                const lastKGroupTh2 = headerRows[1].querySelector(`th[data-column-type="K3"]`);
                if (lastKGroupTh2) {
                    lastKGroupTh2.after(newKTh2);
                } else {
                    // This implies no Kx groups exist, find the previous non-Kx group
                    const incomingBreakerTh1 = headerRows[0].querySelector('th[data-column-group="incoming-breaker"]');
                    const firstAtsTh2 = headerRows[1].querySelector(`th[data-parent-group="auto-transfer-switch"]:first-child`);
                    if(firstAtsTh2){
                        firstAtsTh2.before(newKTh2);
                    } else if (incomingBreakerTh1) {
                         // Tricky insertion: need to insert in headerRow2 after potentially colspanned elements
                         // For simplicity, find the first non-rowspan group in headerRow2 after 'Condition'
                         let insertAfterTh2 = headerRows[1].querySelector('th[rowspan="2"]'); // This would be the 'Incoming Breaker Status' area in Row 1's context.
                         // Need to find the last element in headerRow2 before ATS starts, or append if ATS is first.
                         headerRows[1].appendChild(newKTh2); // Fallback: append
                         console.warn("Complex placement for adding first ATS Kx group. Appending for now.");
                    } else {
                        headerRows[1].appendChild(newKTh2);
                    }
                }

                // Create initial leaf columns (e.g., K4-A, K4-B)
                const newKXATh3 = document.createElement('th');
                newKXATh3.className = 'vmiddle text-center';
                newKXATh3.dataset.columnId = `${newKType.toLowerCase()}-a`;
                newKXATh3.dataset.parentType = newKType;
                newKXATh3.innerHTML = `<a href="#" class="remove-column-btn" data-target-col-id="${newKType.toLowerCase()}-a" title="Remove ${newKType}-A"><i class='fa fa-minus-circle text-danger'></i></a>
                                        <br>${newKType}-A
                                        <a href="#" class="add-column-btn" data-target-col-id="${newKType.toLowerCase()}-a" data-parent-type="${newKType}" title="Add another ${newKType}-X Column"><i class='fa fa-plus-circle text-success'></i></a>`;

                const newKXBTh3 = document.createElement('th');
                newKXBTh3.className = 'vmiddle text-center';
                newKXBTh3.dataset.columnId = `${newKType.toLowerCase()}-b`;
                newKXBTh3.dataset.parentType = newKType;
                newKXBTh3.innerHTML = `<a href="#" class="remove-column-btn" data-target-col-id="${newKType.toLowerCase()}-b" title="Remove ${newKType}-B"><i class='fa fa-minus-circle text-danger'></i></a>
                                        <br>${newKType}-B
                                        <a href="#" class="add-column-btn" data-target-col-id="${newKType.toLowerCase()}-b" data-parent-type="${newKType}" title="Add another ${newKType}-X Column"><i class='fa fa-plus-circle text-success'></i></a>`;

                const lastLeafTh3 = headerRows[2].querySelector(`th[data-column-id="k3-b"]`); // Or last existing ATS leaf
                let insertionIndexInTbody = -1;
                if (lastLeafTh3) {
                    lastLeafTh3.after(newKXBTh3);
                    newKXBTh3.before(newKXATh3);
                    insertionIndexInTbody = getTbodyColumnIndex(lastLeafTh3.dataset.columnId) + 1;
                } else {
                    // This implies no ATS leaf columns exist. Find the right place (e.g., after last incoming breaker).
                    const lastIncomingBreakerLeaf = headerRows[2].querySelector(`th[data-parent-group="incoming-breaker"]:last-child`);
                    if(lastIncomingBreakerLeaf) {
                        lastIncomingBreakerLeaf.after(newKXBTh3);
                        newKXBTh3.before(newKXATh3);
                        insertionIndexInTbody = getTbodyColumnIndex(lastIncomingBreakerLeaf.dataset.columnId) + 1;
                    } else {
                        // Fallback: append
                        headerRows[2].appendChild(newKXATh3);
                        headerRows[2].appendChild(newKXBTh3);
                        insertionIndexInTbody = getTbodyColumnIndex(newKXATh3.dataset.columnId);
                    }
                }

                // Update colspan of top-level ATS header
                const topAtsTh1 = headerRows[0].querySelector(`th[data-column-group="auto-transfer-switch"]`);
                if (topAtsTh1) {
                    topAtsTh1.colSpan = parseInt(topAtsTh1.colSpan) + 2; // Added 2 sub-columns
                }

                // Add empty <td> cells to all body rows
                Array.from(tbody.rows).forEach(row => {
                    const newTdA = document.createElement('td');
                    const newTdB = document.createElement('td');
                    if (insertionIndexInTbody !== -1 && row.cells[insertionIndexInTbody - 1]) {
                        row.cells[insertionIndexInTbody - 1].after(newTdB);
                        newTdB.before(newTdA);
                    } else {
                        row.appendChild(newTdA);
                        row.appendChild(newTdB);
                    }
                });

            } else {
                alert(`Adding a new top-level group like '${targetIdentifier}' is highly complex and requires specific logic.`);
                return;
            }
        }
        console.log(`New group '${targetIdentifier}' added.`);
    }

    // --- Core Function: Remove a group of columns (Tier 2 or Tier 1) ---
    function removeGroup(targetIdentifier, level) {
        const confirmRemoval = confirm(`Are you sure you want to remove the entire group '${targetIdentifier}'? This will remove all its sub-columns.`);
        if (!confirmRemoval) return;

        let targetTh = null;
        let relatedLeafColumnIds = [];

        if (level === 'group') { // Tier 1 group removal (e.g., 'incoming-breaker', 'auto-transfer-switch', 'dc-supply')
            targetTh = headerRows[0].querySelector(`th[data-column-group="${targetIdentifier}"]`);
            if (!targetTh) { console.error('Group not found:', targetIdentifier); return; }

            // Collect all leaf column IDs under this group
            if (targetIdentifier === 'incoming-breaker') {
                relatedLeafColumnIds = Array.from(headerRows[2].querySelectorAll(`th[data-parent-group="${targetIdentifier}"]`)).map(th => th.dataset.columnId);
            } else if (targetIdentifier === 'dc-supply') {
                relatedLeafColumnIds = Array.from(headerRows[0].querySelectorAll(`th[data-parent-group="${targetIdentifier}"][rowspan]`)).map(th => th.dataset.columnId);
            } else if (targetIdentifier === 'auto-transfer-switch') {
                const kxGroups = headerRows[1].querySelectorAll(`th[data-parent-group="auto-transfer-switch"]`);
                kxGroups.forEach(kTh => {
                    relatedLeafColumnIds = relatedLeafColumnIds.concat(
                        Array.from(headerRows[2].querySelectorAll(`th[data-parent-type="${kTh.dataset.columnType}"]`)).map(th => th.dataset.columnId)
                    );
                });
            }
        } else if (level === 'type') { // Tier 2 group removal (e.g., 'K1', 'K2', 'K3')
            targetTh = headerRows[1].querySelector(`th[data-column-type="${targetIdentifier}"]`);
            if (!targetTh) { console.error('Type group not found:', targetIdentifier); return; }

            relatedLeafColumnIds = Array.from(headerRows[2].querySelectorAll(`th[data-parent-type="${targetIdentifier}"]`)).map(th => th.dataset.columnId);
        }
        
        // Sort indexes in descending order to avoid issues when removing from a live NodeList
        const colIndexesToRemove = relatedLeafColumnIds.map(id => getTbodyColumnIndex(id)).sort((a, b) => b - a);

        colIndexesToRemove.forEach(index => {
            Array.from(tbody.rows).forEach(row => {
                if (row.cells[index]) {
                    row.deleteCell(index);
                }
            });
        });

        // Finally, remove the group's header <th>s themselves
        if (level === 'group') {
            if (targetIdentifier === 'auto-transfer-switch') {
                headerRows[1].querySelectorAll(`th[data-parent-group="auto-transfer-switch"]`).forEach(th => th.remove());
            } else if (targetIdentifier === 'incoming-breaker') {
                headerRows[2].querySelectorAll(`th[data-parent-group="incoming-breaker"]`).forEach(th => th.remove());
            } else if (targetIdentifier === 'dc-supply') {
                 headerRows[0].querySelectorAll(`th[data-parent-group="dc-supply"]`).forEach(th => th.remove());
            }
            targetTh.remove(); // Remove the main group header from headerRow1
        } else if (level === 'type') {
            targetTh.remove(); // Remove the Kx header from headerRow2
             // Also need to decrease colspan of its grandparent (ATS)
            const grandParentTh = headerRows[0].querySelector(`th[data-column-group="auto-transfer-switch"]`);
            if (grandParentTh) {
                grandParentTh.colSpan = parseInt(grandParentTh.colSpan) - (targetTh.colSpan || 1);
                if (parseInt(grandParentTh.colSpan) <= 0) grandParentTh.remove();
            }
        }
        console.log(`Group '${targetIdentifier}' removed.`);
    }


    // --- Event Handling ---
    function attachEventListeners() {
        // Remove all previous listeners to prevent duplicates (less efficient but safer for dynamic tables)
        const allButtons = table.querySelectorAll('a[class$="-btn"]');
        allButtons.forEach(btn => {
            btn.removeEventListener('click', handleButtonClick);
        });

        // Add new listeners
        table.querySelectorAll('a[class$="-btn"]').forEach(btn => {
            btn.addEventListener('click', handleButtonClick);
        });
    }

    function handleButtonClick(event) {
        event.preventDefault(); // Prevent default link behavior

        const button = event.currentTarget;

        if (button.classList.contains('remove-column-btn')) {
            removeColumn(button.dataset.targetColId);
        } else if (button.classList.contains('add-column-btn')) { // NEW: Handle adding a sibling column
            addColumn(button.dataset.targetColId, button.dataset.parentType, button.dataset.parentGroup);
        } else if (button.classList.contains('add-sub-column-btn')) { // Existing: Handle adding a new main sub-column for a group
            if (button.dataset.parentType) {
                addMainSubColumn('parent-type', button.dataset.parentType);
            } else if (button.dataset.parentGroup) {
                addMainSubColumn('parent-group', button.dataset.parentGroup);
            }
        } else if (button.classList.contains('remove-group-type-btn')) { // Remove K1, K2, K3 groups
            removeGroup(button.dataset.targetType, 'type');
        } else if (button.classList.contains('add-sub-group-btn')) { // Add new Kx group under ATS
            if (button.dataset.parentGroup === 'auto-transfer-switch') {
                addGroup('auto-transfer-switch', 'group');
            } else {
                alert("Adding new sub-groups only supported for 'Auto Transfer Switch' for now.");
            }
        } else if (button.classList.contains('remove-group-btn')) { // Remove top-level groups
            removeGroup(button.dataset.targetGroup, 'group');
        }
        // After any DOM manipulation, re-attach event listeners
        attachEventListeners();
    }

    // Initial attachment of event listeners
    attachEventListeners();
});
</script>