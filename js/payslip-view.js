document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    
    if (!id) {
        showError('No payslip ID in URL');
        return;
    }
    
    const apiUrl = `../api/services/get-payslip-detail.php?id=${id}`; 
    
    // console.log('🔍 Fetching:', apiUrl);
    
    fetch(apiUrl)
        .then(response => {
            // console.log('📡 Response status:', response.status);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.json();
        })
        .then(result => {
            // console.log('✅ Data received:', result);
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

function loadPayslipData(data) {
    // console.log('🔍 HR Type:', data.hr_type);
    
    document.getElementById('loading').style.display = 'none';
    document.getElementById('detail-content').classList.remove('hidden');
    
    const formatCurrency = (amount) => `₦${parseFloat(amount || 0).toLocaleString('en-NG', {minimumFractionDigits: 0, maximumFractionDigits: 2})}`;
    
    // Common fields - skip for RETAIL to avoid duplication
    // Common fields - always populate for consistency, RETAIL has own table
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
        document.getElementById('pdf-total-deduction').textContent = formatCurrency(parseFloat(data.deductions) + parseFloat(data.paye) + parseFloat(data.pension));
        document.getElementById('pdf-net-pay').textContent = formatCurrency(data.netSalary);
        
    } else {
        // 🔥 RETAIL HR - Perfect template population
    document.getElementById('pdf-period').textContent = `${data.month} ${data.year}`;
    document.getElementById('pdf-employee-name').textContent = data.employeeName;
    document.getElementById('pdf-department').textContent = data.department;
    document.getElementById('pdf-payer-id').textContent = data.taxId || 'N/A';
    document.getElementById('pdf-position').textContent = data.accountNumber || 'N/A';
    document.getElementById('pdf-bank').textContent = data.bankName || 'N/A';

    // 🔥 RETAIL-SPECIFIC FIELDS from JSON
    const annualGross = parseFloat(data.annual_gross) || 0;
    const taxableIncome = parseFloat(data.taxable_income) || 0;
    const annualTax = parseFloat(data.annual_tax) || 0;
    const monthlyTax = parseFloat(data.monthly_tax) || 0;
    const monthlyNet = parseFloat(data.monthly_net) || 0;
    const monthlyGross = parseFloat(data.grossSalary) || 0;
    const station = data.station || 'N/A';
    const totalDeduction = taxableIncome + annualTax + monthlyTax; 
    const grossPay = annualGross + monthlyNet + monthlyGross; 
    const netPay = grossPay - totalDeduction;
        // 🔥 RETAIL HR - Use JSON-extracted fields
        // Create common-style tables for RETAIL matching payslip-template.php
        const retailTables = document.createElement('div');
        retailTables.id = 'retail-tables';
        retailTables.style.marginTop = '20px';
        
        // Update formatCurrency for exact PHP match
        const formatAmount = (amount) => {
            const num = parseFloat(amount || 0);
            return '₦' + num.toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        };
        
        const tax = data.monthly_tax || 0;
        const totalDed = parseFloat(tax);
        
        retailTables.innerHTML = `
            <!-- Info Table - Exact match from template -->
            <table class="info-table" style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-family: Arial, sans-serif; font-size: 12px;">
                <tr>
                    <td style="padding: 6px; border: 1px solid #000; font-weight: bold; background: #f3f4f6;">Pay Type</td>
                    <td style="padding: 6px; border: 1px solid #000;">Monthly</td>
                    <td style="padding: 6px; border: 1px solid #000; font-weight: bold; background: #f3f4f6;">Period</td>
                    <td style="padding: 6px; border: 1px solid #000;">${data.month} ${data.year}</td>
                </tr>
                <tr>
                    <td style="padding: 6px; border: 1px solid #000; font-weight: bold; background: #f3f4f6;">Station</td>
                    <td style="padding: 6px; border: 1px solid #000;">${data.station || 'N/A'}</td>
                    <td style="padding: 6px; border: 1px solid #000; font-weight: bold; background: #f3f4f6;">Payer ID</td>
                    <td style="padding: 6px; border: 1px solid #000;">${data.taxId}</td>
                </tr>
                <tr>
                    <td style="padding: 6px; border: 1px solid #000; font-weight: bold; background: #f3f4f6;">Employee Name</td>
                    <td style="padding: 6px; border: 1px solid #000;">${data.employeeName}</td>
                    <td style="padding: 6px; border: 1px solid #000; font-weight: bold; background: #f3f4f6;">Account Number</td>
                    <td style="padding: 6px; border: 1px solid #000;">${data.accountNumber}</td>
                </tr>
                <tr>
                    <td style="padding: 6px; border: 1px solid #000; font-weight: bold; background: #f3f4f6;">Department</td>
                    <td style="padding: 6px; border: 1px solid #000;">${data.department}</td>
                    <td style="padding: 6px; border: 1px solid #000; font-weight: bold; background: #f3f4f6;">Bank</td>
                    <td style="padding: 6px; border: 1px solid #000;">${data.bankName}</td>
                </tr>
            </table>

            <!-- Salary Table - Exact side-by-side match -->
            <table class="salary-table" style="width: 100%; border-collapse: collapse; margin-top: 10px; font-family: Arial, sans-serif; font-size: 12px;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #000; padding: 8px; background: #e5e7eb; text-align: center; font-weight: bold;">Earnings</th>
                        <th style="border: 1px solid #000; padding: 8px; background: #e5e7eb; text-align: center; font-weight: bold;">Amount (₦)</th>
                        <th style="border: 1px solid #000; padding: 8px; background: #e5e7eb; text-align: center; font-weight: bold;">Deductions</th>
                        <th style="border: 1px solid #000; padding: 8px; background: #e5e7eb; text-align: center; font-weight: bold;">Amount (₦)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px;">Monthly Gross</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: right;">${formatAmount(data.grossSalary)}</td>
                        <td style="border: 1px solid #000; padding: 8px;">Monthly Tax</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: right;">${formatAmount(monthlyTax)}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px;">Annual Gross</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: right;">${formatAmount(annualGross)}</td>
                        <td style="border: 1px solid #000; padding: 8px;">Taxable Income</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: right;">${formatAmount(taxableIncome)}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px;">Monthly Net</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: right;">${formatAmount(monthlyNet)}</td>
                        <td style="border: 1px solid #000; padding: 8px;">Annual Tax</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: right;">${formatAmount(annualTax)}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px;"></td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: right;"></td></td>
                        <td style="border: 1px solid #000; padding: 8px;">Deductions</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: right;">${formatAmount(data.deductions)}</td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td style="border: 1px solid #000; padding: 8px;">Gross Pay</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: right;">${formatAmount(grossPay)}</td>
                        <td style="border: 1px solid #000; padding: 8px;">Total Deduction</td>
                        <td style="border: 1px solid #000; padding: 8px; text-align: right;">${formatAmount(totalDeduction)}</td>
                    </tr>
                    
                    <tr style="background: #d1fae5; font-weight: bold; font-size: 14px;">
                        <td colspan="2" style="border: 1px solid #000; padding: 8px;">Net Pay</td>
                        <td colspan="2" style="border: 1px solid #000; padding: 8px; text-align: right;">${formatAmount(netPay)}</td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 25px; font-size: 10px; text-align: center; font-family: Arial, sans-serif;">
                This is a system-generated payslip | Generated on ${new Date().toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'})}
            </div>
        `;
        
        // Hide existing tables, insert only our 2 tables
        const existingInfoTable = document.querySelector('#info-table');
        const existingSalaryTable = document.querySelector('#salary-table');
        if (existingInfoTable) existingInfoTable.style.display = 'none';
        if (existingSalaryTable) existingSalaryTable.style.display = 'none';
        
        // Move buttons after tables for RETAIL
        document.getElementById('detail-content').appendChild(retailTables);
        
        // Ensure tables visible - override any previous hides
        const newInfoTable = retailTables.querySelector('.info-table');
        const newSalaryTable = retailTables.querySelector('.salary-table');
        if (newInfoTable) newInfoTable.style.display = 'table';
        if (newSalaryTable) newSalaryTable.style.display = 'table';
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
    window.open(`../api/services/payslip-template.php?id=${payslipId}`, '_blank');
}