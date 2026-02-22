
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height: 80vh; padding: 0; position: relative;">
                <div id="pdfLoading" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10;">
                    <i class="fa fa-spinner fa-spin fa-3x"></i>
                    <p>Loading Document...</p>
                </div>
                <iframe id="pdfViewer" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    $(document).on('click', '.docModal', function(e) {
        e.preventDefault();
        
        var url = $(this).attr('value');
        var title = $(this).attr('title') || 'Supporting Document';
        
        // Set modal title
        $('#pdfModalLabel').text(title);
        
        // Show loading indicator
        $('#pdfLoading').show();
        
        // Set iframe source
        var iframe = $('#pdfViewer');
        iframe.attr('src', url);
        
        // Hide loading when iframe loads
        iframe.on('load', function() {
            $('#pdfLoading').fadeOut();
        });
                
        // Show modal
        $('#pdfModal').modal('show');
    });
    
    // Clear iframe when modal is closed
    $('#pdfModal').on('hidden.bs.modal', function () {
        $('#pdfViewer').attr('src', '');
        $('#pdfLoading').show();
    });
", \yii\web\View::POS_READY);
?>