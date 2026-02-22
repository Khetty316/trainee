
<div class="test-progress-view" id="app">
    <table class="table table-sm table-striped table-bordered col-12">
        <thead class="thead-light">
            <tr>
                <th class="text-center col-1">Test Certificates</th>
                <th class="text-center col-1">Panel Description</th>
                <th class="text-center col-1">Panel Code</th>
                <th class="text-center col-1">Test Type</th>
                <th class="text-center col-1">Status</th>
            </tr>
        </thead>
        <tbody >
            <?php
            
            $panelIds = [];
            $panelData = [];
           
            foreach ($tests as $test) {
                $panelId = $test->panel_id;
                $tcRef = $test->tc_ref;

                if (!in_array($panelId, $panelIds)) {
                    $panelIds[] = $panelId;
                    $panelData[$panelId] = [
                        'panel_desc' => $test->panel_desc,
                        'prod_panel_code' => $test->prod_panel_code,
                        'test_type' => $test->test_type,
                        'status' => $test->status,
                        'tc_refs' => []
                    ];
                }

                $panelData[$panelId]['tc_refs'][] = $tcRef;

            }

            usort($panelIds, function ($a, $b) use ($panelData) {
                return strcmp($panelData[$a]['prod_panel_code'], $panelData[$b]['prod_panel_code']);
            });

            foreach ($panelIds as $panelId) {
                ?>
                <tr>
                    <td>
                        <ul>
                            <?php foreach ($panelData[$panelId]['tc_refs'] as $tcRef) { ?>
                                <li><?php echo $tcRef; ?></li>
                            <?php } ?>
                        </ul>
                    </td>
                    <td class="text-center"><?php echo $panelData[$panelId]['panel_desc']; ?></td>
                    <td class="text-center"><?php echo $panelData[$panelId]['prod_panel_code']; ?></td>
                    <td class="text-center"><?php echo $panelData[$panelId]['test_type']; ?></td>
                    <td class="text-center"><?php echo $panelData[$panelId]['status']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<div class="pagination my-3 flex justify-content-end" v-if="totalPages > 1">
                <button class="btn btn-sm btn-primary" @click="prevPage" :disabled="currentPage === 1">Previous</button>
                <span class="pt-1">&nbsp;{{ currentPage }} / {{ totalPages }}&nbsp;</span>
                <button class="btn btn-sm btn-primary" @click="nextPage" :disabled="currentPage === totalPages">Next</button>
            </div>
</div>
<script>
    window.models = <?= json_encode($tests) ?>;
    window.numPerPage = 4;
</script>
<script src="\js\vueTable.js"></script>