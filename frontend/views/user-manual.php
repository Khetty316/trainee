<?php
$this->title = 'User Manual';
?>
<style>
#pdfViewer {
    width: 100%;
    height: 90vh;
    border: 1px solid #ccc;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px 0;
    background-color: #525659;
}

#pdfViewer canvas {
    display: block;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
</style>

<div id="pdfViewer"></div>

<script type="module">
import { getDocument, GlobalWorkerOptions } from 'https://cdn.jsdelivr.net/npm/pdfjs-dist@5.4.149/build/pdf.min.mjs';

GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@5.4.149/build/pdf.worker.min.mjs';

document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('pdfViewer');
    const url = '<?= $fileUrl ?>';
    let pdfDoc = null;

    getDocument(url).promise.then(pdf => {
        pdfDoc = pdf;
        for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
            renderPage(pageNum);
        }
    });

    function renderPage(num) {
        pdfDoc.getPage(num).then(page => {
            const maxWidth = container.clientWidth - 40; // Account for padding
            const viewport = page.getViewport({ scale: 1 });
            const scale = Math.min(maxWidth / viewport.width, 1.5); // Max scale of 1.5
            const scaledViewport = page.getViewport({ scale });

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.height = scaledViewport.height;
            canvas.width = scaledViewport.width;

            container.appendChild(canvas);

            page.render({
                canvasContext: ctx,
                viewport: scaledViewport
            });
        });
    }

    window.addEventListener('resize', () => {
        if (!pdfDoc) return;
        container.innerHTML = '';
        for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
            renderPage(pageNum);
        }
    });
});
</script>