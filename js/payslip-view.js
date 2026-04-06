document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    
    if (!id) {
        showError('No payslip ID in URL');
        return;
    }
    
    const apiUrl = `../includes/get-payslip-detail.php?id=${id}`; 
    
    console.log('🔍 Fetching:', apiUrl);
    
    fetch(apiUrl)
        .then(response => {
            console.log('📡 Response status:', response.status);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(result => {
            console.log('✅ Data received:', result);
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
            <button onclick="window.history.back()" class="mt-4 px-6 py-2 bg-red-100 text-red-700 rounded-xl hover:bg-red-200">← Go Back</button>
        </div>
    `;
}

// function loadPayslipData(data) {
//     console.log('🎨 Raw data:', data); // DEBUG
    
//     document.getElementById('loading').style.display = 'none';
//     document.getElementById('detail-content').classList.remove('hidden');
    
//     // ✅ FORMAT CURRENCY FUNCTION (MOVED INSIDE)
//     const formatCurrency = (amount) => {
//         return `₦${parseFloat(amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 0, maximumFractionDigits: 2})}`;
//     };
    
//     // ✅ BASIC INFO (Working)
//     document.getElementById('pdf-period').textContent = `${data.month} ${data.year}`;
//     document.getElementById('pdf-employee-name').textContent = data.employeeName || data.name || 'Unknown';
//     document.getElementById('pdf-department').textContent = data.department || 'Unknown';
//     document.getElementById('pdf-payer-id').textContent = data.taxId || 'N/A';
//     document.getElementById('pdf-days-worked').textContent = data.days_worked || 22;
//     document.getElementById('pdf-position').textContent = data.accountNumber;
//     document.getElementById('pdf-bank').textContent = data.bankName;

    
//     // ✅ EARNINGS (MATCH YOUR DB EXACTLY)
//     document.getElementById('pdf-basic').textContent = formatCurrency(data.basic_salary);
//     document.getElementById('pdf-housing').textContent = formatCurrency(data.housing);
//     document.getElementById('pdf-transport').textContent = formatCurrency(data.transport);
//     document.getElementById('pdf-medical').textContent = formatCurrency(data.medical);
//     document.getElementById('pdf-utility').textContent = formatCurrency(data.utility);
    
//     // ✅ DEDUCTIONS (MATCH YOUR DB EXACTLY)
//     document.getElementById('pdf-pension').textContent = formatCurrency(data.pension);
//     document.getElementById('pdf-paye').textContent = formatCurrency(data.paye);
//     document.getElementById('pdf-deductions').textContent = formatCurrency(data.deductions);
    
//     // ✅ TOTALS (Always have these)
//     document.getElementById('pdf-gross-total').textContent = formatCurrency(data.gross_salary || data.grossSalary);
//     document.getElementById('pdf-total-deduction').textContent = formatCurrency(data.deductions);
//     document.getElementById('pdf-net-pay').textContent = formatCurrency(data.net_salary || data.netSalary);
    
//     console.log('✅ All fields updated!'); // DEBUG
// }

function loadPayslipData(data) {
    console.log('🔍 HR Type:', data.hr_type);
    
    document.getElementById('loading').style.display = 'none';
    document.getElementById('detail-content').classList.remove('hidden');
    
    const formatCurrency = (amount) => `₦${parseFloat(amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 0, maximumFractionDigits: 2})}`;
    
    // Common fields
    document.getElementById('pdf-period').textContent = `${data.month} ${data.year}`;
    document.getElementById('pdf-employee-name').textContent = data.employeeName;
    document.getElementById('pdf-department').textContent = data.department;
    document.getElementById('pdf-payer-id').textContent = data.taxId;
    document.getElementById('pdf-position').textContent = data.accountNumber;
    document.getElementById('pdf-bank').textContent = data.bankName;
    
    if (data.hr_type === 'MAIN' || !data.hr_type) {
        // 🔥 MAIN HR (your existing code)
        document.getElementById('pdf-days-worked').textContent = data.days_worked || 22;
        document.getElementById('pdf-basic').textContent = formatCurrency(data.basic_salary);
        document.getElementById('pdf-housing').textContent = formatCurrency(data.housing);
        document.getElementById('pdf-transport').textContent = formatCurrency(data.transport);
        document.getElementById('pdf-medical').textContent = formatCurrency(data.medical);
        document.getElementById('pdf-utility').textContent = formatCurrency(data.utility);
        document.getElementById('pdf-pension').textContent = formatCurrency(data.pension);
        document.getElementById('pdf-paye').textContent = formatCurrency(data.paye);
        document.getElementById('pdf-deductions').textContent = formatCurrency(data.deductions);
        document.getElementById('pdf-gross-total').textContent = formatCurrency(data.grossSalary);
        document.getElementById('pdf-total-deduction').textContent = formatCurrency(data.deductions);
        document.getElementById('pdf-net-pay').textContent = formatCurrency(data.netSalary);
        
    } else {
        // 🔥 RETAIL HR - Use JSON-extracted fields
        document.getElementById('salary-table').style.display = 'none';
        
        const retailSummary = document.createElement('div');
        retailSummary.style.cssText = `
            margin-top: 30px; padding: 30px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; border-radius: 20px; text-align: center;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        `;
        retailSummary.innerHTML = `
            <h2 style="font-size: 28px; margin: 0 0 20px 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">Retail HR Payslip</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 25px; font-size: 16px;">
                <div style="background: rgba(255,255,255,0.2); padding: 20px; border-radius: 12px; backdrop-filter: blur(10px);">
                    <strong>Station:</strong><br><span style="font-size: 18px;">${data.station || 'N/A'}</span>
                </div>
                <div style="background: rgba(255,255,255,0.2); padding: 20px; border-radius: 12px; backdrop-filter: blur(10px);">
                    <strong>Monthly Gross:</strong><br><span style="font-size: 22px; font-weight: bold;">${formatCurrency(data.grossSalary)}</span>
                </div>
                <div style="background: rgba(255,255,255,0.2); padding: 20px; border-radius: 12px; backdrop-filter: blur(10px);">
                    <strong>Annual Gross:</strong><br><span style="font-size: 18px;">${data.annual_gross || 'N/A'}</span>
                </div>
                <div style="background: rgba(255,255,255,0.2); padding: 20px; border-radius: 12px; backdrop-filter: blur(10px);">
                    <strong>Monthly Net:</strong><br><span style="font-size: 24px; color: #10b981; font-weight: bold;">${formatCurrency(data.netSalary)}</span>
                </div>
                <div style="background: rgba(255,255,255,0.2); padding: 20px; border-radius: 12px; backdrop-filter: blur(10px);">
                    <strong>Tax:</strong><br>${formatCurrency(data.monthly_tax || 0)}
                </div>
            </div>
        `;
        
        document.querySelector('#info-table').after(retailSummary);
    }
}
function printPayslip() {
    const nonEssential = document.querySelectorAll('nav, header, footer, button');
    nonEssential.forEach(el => el.style.display = 'none');
    window.print();
    setTimeout(() => {
        nonEssential.forEach(el => el.style.display = '');
    }, 500);
}

// PDF Download (Fixed ID from URL)
function downloadPDF() {
    const urlParams = new URLSearchParams(window.location.search);
    const payslipId = urlParams.get('id');
    window.open(`../includes/payslip-template.php?id=${payslipId}`, '_blank');
}