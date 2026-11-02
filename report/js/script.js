// report/js/script.js
let currentPage = 1;
const totalPages = 8;

function loadPage(pageNumber) {
    const container = document.getElementById('printable-area');
    if (!container) return;
    
    fetch(`pages/page${pageNumber}.html`)
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
            currentPage = pageNumber;
            updateButtons();
            window.scrollTo(0, 0);
        })
        .catch(error => {
            console.error('Error loading page:', error);
            container.innerHTML = '<div style="padding: 50px; text-align: center;">Error loading page</div>';
        });
}

function updateButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const pageIndicator = document.getElementById('pageIndicator');
    
    if (prevBtn) {
        prevBtn.disabled = (currentPage === 1);
        prevBtn.style.opacity = (currentPage === 1) ? '0.5' : '1';
    }
    if (nextBtn) {
        nextBtn.disabled = (currentPage === totalPages);
        nextBtn.style.opacity = (currentPage === totalPages) ? '0.5' : '1';
    }
    if (pageIndicator) {
        pageIndicator.textContent = `Page ${currentPage} of ${totalPages}`;
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        loadPage(currentPage + 1);
    }
}

function prevPage() {
    if (currentPage > 1) {
        loadPage(currentPage - 1);
    }
}

function downloadPDF(btn) {
    const element = document.getElementById('printable-area');
    const bar = document.getElementById('btn-bar');
    
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    }
    if (bar) bar.style.display = 'none';
    
    const opt = {
        margin: 0,
        filename: 'VinUnified_Vehicle_History_Report.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF: { unit: 'px', format: [794, 1123], orientation: 'portrait' }
    };
    
    html2pdf()
        .set(opt)
        .from(element)
        .save()
        .then(() => {
            if (bar) bar.style.display = 'flex';
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-download"></i> Download PDF';
            }
        });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadPage(1);
    
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const pdfBtn = document.getElementById('pdfBtn');
    
    if (prevBtn) prevBtn.addEventListener('click', prevPage);
    if (nextBtn) nextBtn.addEventListener('click', nextPage);
    if (pdfBtn) pdfBtn.addEventListener('click', () => downloadPDF(pdfBtn));
});