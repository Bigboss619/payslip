// ✅ FIXED: Correct path + Error handling
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    
    if (!id) {
        showError('No payslip ID in URL');
        return;
    }
    
    // ✅ CORRECT PATH (adjust based on your folder structure)
    const apiUrl = `../includes/get-payslip-detail.php?id=${id}`; // Same folder as payslip-view.php
    
    console.log('🔍 Fetching:', apiUrl); // DEBUG
    
    fetch(apiUrl)
        .then(response => {
            console.log('📡 Response status:', response.status); // DEBUG
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(result => {
            console.log('✅ Data received:', result); // DEBUG
            if (result.success) {
                loadPayslipData(result.data);
            } else {
                showError(result.error || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('❌ Fetch error:', error);
            showError(`Failed to load: ${error.message}`);
        });
});

function showError(message) {
    const loadingEl = document.getElementById('loading');
    loadingEl.innerHTML = `
        <div class="text-center py-12">
            <div class="w-16 h-16 border-4 border-red-200 border-t-red-500 rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-red-500 font-medium">${message}</p>
            <button onclick="window.history.back()" class="mt-4 px-6 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200 transition-all">
                ← Go Back
            </button>
        </div>
    `;
}

function loadPayslipData(data) {
    console.log('🎨 Loading data:', data); // DEBUG
    
    document.getElementById('loading').style.display = 'none';
    document.getElementById('detail-content').classList.remove('hidden');
    
    const formatCurrency = (amount) => `₦${parseFloat(amount || 0).toLocaleString()}`;
    
    // Header
    document.getElementById('payslip-period').textContent = `${data.month} ${data.year}`;
    document.getElementById('company-period').textContent = `${data.month} ${data.year}`;
    document.getElementById('pay-period').textContent = `${data.month} ${data.year}`;
    document.getElementById('generated-date').textContent = data.generatedDate || 'N/A';
    
    // Employee info
    document.getElementById('employee-name').textContent = data.employeeName || 'N/A';
    document.getElementById('employee-id').textContent = data.employeeId || 'N/A';
    document.getElementById('department').textContent = data.department || 'N/A';
    document.getElementById('position').textContent = data.position || 'N/A';
    
    // Summary
    document.getElementById('gross-salary-display').textContent = formatCurrency(data.grossSalary);
    document.getElementById('net-salary-display').textContent = formatCurrency(data.netSalary);
    
    // Status
    const statusBadge = document.getElementById('status-badge');
    statusBadge.textContent = data.status || 'Unknown';
    const statusColors = {
        'Paid': 'bg-green-100 text-green-800',
        'Pending': 'bg-yellow-100 text-yellow-800',
        'Failed': 'bg-red-100 text-red-800'
    };
    statusBadge.className = `font-semibold px-2 py-1 rounded-full text-xs ${statusColors[data.status] || 'bg-gray-100 text-gray-800'}`;
    
    // Earnings (use actual data if available)
    document.getElementById('basic-salary').textContent = formatCurrency(data.basic_salary || 0);
    document.getElementById('housing').textContent = formatCurrency(data.housing || 0);
    document.getElementById('transport').textContent = formatCurrency(data.transport || 0);
    document.getElementById('medical').textContent = formatCurrency(data.medical || 0);
    document.getElementById('total-earnings').textContent = formatCurrency(data.grossSalary);
    
    // Deductions
    document.getElementById('tax').textContent = formatCurrency(data.paye || 0);
    document.getElementById('pension').textContent = formatCurrency(data.pension || 0);
    document.getElementById('payroll-deductions').textContent = formatCurrency((data.deductions || 0) - (data.paye || 0) - (data.pension || 0));
    document.getElementById('total-deductions').textContent = formatCurrency(data.deductions || 0);
}
// Add this function to your payslip-view.js
function printPayslip() {
    // ✅ Hide non-essential elements before print
    const nonEssential = document.querySelectorAll('nav, header, footer, button:not(#printBtn)');
    nonEssential.forEach(el => el.style.display = 'none');
    
    // ✅ Force print styles
    window.print();
    
    // ✅ Restore after print dialog closes
    setTimeout(() => {
        nonEssential.forEach(el => el.style.display = '');
    }, 100);
}

// Add click handler
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('printBtn')?.addEventListener('click', printPayslip);
});
// ✅ FIXED PDF Download
function downloadPDF() {
    const staffId = document.getElementById('employee-id').textContent;
    const month = document.getElementById('pay-period').textContent.split(' ')[0];
    const year = document.getElementById('pay-period').textContent.split(' ')[1];
    
    // ✅ CORRECT PDF URL PATH
    const pdfUrl = `../includes/payslip-template.php?id=${encodeURIComponent(staffId)}`;
    window.open(pdfUrl, '_blank');
}