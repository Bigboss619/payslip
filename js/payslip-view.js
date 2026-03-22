
        // Get payslip data from URL params
        const urlParams = new URLSearchParams(window.location.search);
        const payslipDataParam = urlParams.get('payslipData');
        
        if (payslipDataParam) {
            try {
                const payslipData = JSON.parse(decodeURIComponent(payslipDataParam));
                loadPayslipData(payslipData);
            } catch (e) {
                console.error('Error parsing payslip data:', e);
                document.getElementById('loading').innerHTML = '<p class="text-red-500">Error loading payslip data</p>';
            }
        } else {
            document.getElementById('loading').innerHTML = '<p class="text-gray-500">No payslip data found</p>';
        }

        function loadPayslipData(data) {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('detail-content').style.display = 'block';
            
            const formatCurrency = (amount) => `₦${amount.toLocaleString()}`;
            const formatMonthYear = (month, year) => `${month} ${year}`;
            
            // Update header
            document.getElementById('payslip-period').textContent = formatMonthYear(data.month, data.year);
            document.getElementById('company-period').textContent = formatMonthYear(data.month, data.year);
            document.getElementById('pay-period').textContent = formatMonthYear(data.month, data.year);
            document.getElementById('generated-date').textContent = data.date;
            
            // Employee details from data
            if (data.employeeName) document.getElementById('employee-name').textContent = data.employeeName;
            if (data.employeeId) document.getElementById('employee-id').textContent = data.employeeId;
            if (data.department) document.getElementById('department').textContent = data.department;
            if (data.position) document.getElementById('position').textContent = data.position;
            
            // Summary
            document.getElementById('gross-salary-display').textContent = formatCurrency(data.grossSalary);
            document.getElementById('net-salary-display').textContent = formatCurrency(data.netSalary);
            
            // Status with colors
            const statusBadge = document.getElementById('status-badge');
            const statusDisplay = document.getElementById('status-display');
            statusBadge.textContent = data.status;
            statusDisplay.textContent = `${data.status} ✓`;
            const statusColors = {
              'Paid': 'bg-green-100 text-green-800',
              'Pending': 'bg-yellow-100 text-yellow-800',
              'Failed': 'bg-red-100 text-red-800'
            };
            const statusClass = statusColors[data.status] || 'bg-gray-100 text-gray-800';
            statusBadge.className = `font-semibold px-2 py-1 rounded-full text-xs ${statusClass}`;
            
            // Earnings breakdown
            const basic = Math.round(data.grossSalary * 0.75);
            const housing = Math.round(data.grossSalary * 0.15);
            const transport = Math.round(data.grossSalary * 0.07);
            const medical = Math.round(data.grossSalary * 0.03);
            
            document.getElementById('basic-salary').textContent = formatCurrency(basic);
            document.getElementById('housing').textContent = formatCurrency(housing);
            document.getElementById('transport').textContent = formatCurrency(transport);
            document.getElementById('medical').textContent = formatCurrency(medical);
            document.getElementById('total-earnings').textContent = formatCurrency(data.grossSalary);
            
            // Deduction breakdown (simplified to match total)
            const tax = Math.round(data.deductions * 0.5);
            const pension = Math.round(data.grossSalary * 0.08);
            const payrollDeductions = data.deductions - tax - pension;
            
            document.getElementById('tax').textContent = formatCurrency(tax);
            document.getElementById('pension').textContent = formatCurrency(pension);
            document.getElementById('payroll-deductions').textContent = formatCurrency(payrollDeductions);
            document.getElementById('total-deductions').textContent = formatCurrency(data.deductions);
        }

        function downloadPDF() {
            // Enhanced PDF - save as HTML for printing
            const content = document.getElementById('detail-content').innerHTML;
            const blob = new Blob([content], { type: 'text/html' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `payslip-${new Date().toISOString().slice(0,10)}.php`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            window.print(); // Also open print dialog
        }
