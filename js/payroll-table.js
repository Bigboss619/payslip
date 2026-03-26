// payroll-table.js - COMPLETE WORKING VERSION
let currentExcelData = [];
let currentPayslipData = [];
let filteredExcelData = [];
let filteredPayslipData = [];
let currentPage = 1;
let currentView = 'excel'; // 'excel' or 'payslip'
const pageSize = 10;

const formatCurrency = (amount) => `₦${parseFloat(amount || 0).toLocaleString()}`;

function initPayrollTable() {
  setupToggleButtons();
  loadPayslipRecords();
  renderCurrentView();
}

function setupToggleButtons() {
  document.getElementById('excelToggle')?.addEventListener('click', () => switchView('excel'));
  document.getElementById('payslipToggle')?.addEventListener('click', () => switchView('payslip'));
}

function switchView(view) {
  currentView = view;
  currentPage = 1;
  
  // Update buttons
  document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.remove('active', 'bg-blue-600', 'text-white', 'bg-gray-200', 'text-gray-700'));
  document.getElementById(view + 'Toggle')?.classList.add('active', 'bg-blue-600', 'text-white');
  
  // Update containers
  document.getElementById('excelTableContainer')?.classList.toggle('active', view === 'excel');
  document.getElementById('excelTableContainer')?.classList.toggle('hidden', view !== 'excel');
  document.getElementById('payslipTableContainer')?.classList.toggle('active', view === 'payslip');
  document.getElementById('payslipTableContainer')?.classList.toggle('hidden', view !== 'payslip');
  
  renderCurrentView();
}

function renderCurrentView() {
  if (currentView === 'excel') {
    renderExcelTable();
  } else {
    renderPayslipTable();
  }
}

function renderExcelTable() {
  const data = filteredExcelData.length > 0 ? filteredExcelData : currentExcelData;
  const start = (currentPage - 1) * pageSize;
  const end = start + pageSize;
  const paginated = data.slice(start, end);
  
  const tbody = document.getElementById('excelTableBody');
  if (!tbody) return;
  
  if (paginated.length === 0) {
    tbody.innerHTML = `
      <tr><td colspan="6" class="p-12 text-center text-gray-500 bg-gray-50">
        <div class="text-3xl mb-4">📄 No Excel Data</div>
        <div class="text-lg mb-4 text-gray-600">Upload Excel file first</div>
      </td></tr>
    `;
  } else {
    tbody.innerHTML = paginated.map((item, index) => `
      <tr class="hover:bg-gray-50/50 border-b transition-colors">
        <td class="p-3 text-xs font-mono border-r">${(start + index + 1)}</td>
        <td class="p-3 font-medium border-r">${item.staff_id || ''}</td>
        <td class="p-3 border-r">${item.name || ''}</td>
        <td class="p-3 border-r">${item.department || ''}</td>
        <td class="p-3 text-right font-semibold border-r">${formatCurrency(item.gross_salary)}</td>
        <td class="p-3 text-right font-bold text-green-600 border-r">${formatCurrency(item.net_salary)}</td>
      </tr>
    `).join('');
  }
  
  renderPagination(data.length);
}

// async function loadPayslipRecords() {
//   const tbody = document.getElementById('payslipTableBody');
//   if (!tbody) return;
  
//   tbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto mb-2"></div><p>Loading payslip records...</p></td></tr>';
  
//   try {
//     // ✅ Replace with your actual payslip API endpoint
//     const response = await fetch('../includes/get-payslips.php');
//     const result = await response.json();
    
//     if (result.success && result.payslips) {
//       currentPayslipData = result.payslips;
//       filteredPayslipData = [...currentPayslipData];
//       renderPayslipTable();
//     } else {
//       // Mock payslip data for testing
//       currentPayslipData = [
//         {staff_id: 'EMP001', name: 'John Doe', month: 'January 2024', gross_salary: 500000, net_salary: 450000, status: 'Paid'},
//         {staff_id: 'EMP002', name: 'Jane Smith', month: 'January 2024', gross_salary: 450000, net_salary: 400000, status: 'Pending'}
//       ];
//       filteredPayslipData = [...currentPayslipData];
//       renderPayslipTable();
//     }
//   } catch (err) {
//     console.error('Payslip load error:', err);
//     // Use mock data
//     currentPayslipData = [
//       {staff_id: 'EMP001', name: 'John Doe', month: 'January 2024', gross_salary: 500000, net_salary: 450000, status: 'Paid'},
//       {staff_id: 'EMP002', name: 'Jane Smith', month: 'January 2024', gross_salary: 450000, net_salary: 400000, status: 'Pending'}
//     ];
//     filteredPayslipData = [...currentPayslipData];
//     renderPayslipTable();
//   }
// }

// async function loadPayslipRecords() {
//   // ✅ TEMPORARILY DISABLE - Focus on Excel first
//   console.log('⏳ Payslip loading disabled - using mock data');
  
//   currentPayslipData = [
//     {staff_id: 'PAY001', name: 'John Doe', month: 'January 2024', gross_salary: 500000, net_salary: 450000, status: 'Paid'},
//     {staff_id: 'PAY002', name: 'Jane Smith', month: 'January 2024', gross_salary: 450000, net_salary: 400000, status: 'Pending'},
//     {staff_id: 'PAY003', name: 'Bob Wilson', month: 'February 2024', gross_salary: 550000, net_salary: 495000, status: 'Paid'}
//   ];
//   filteredPayslipData = [...currentPayslipData];
//   renderPayslipTable();
  
//   return; // Skip real API call
// }

async function loadPayslipRecords(month = null, year = null) {
  const tbody = document.getElementById('payslipTableBody');
  if (tbody) {
    tbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto mb-2"></div><p>Loading payslip records...</p></td></tr>';
  }
  
  try {
    const params = new URLSearchParams({
      month: month || '',
      year: year || '',
      limit: 1000,  // Get all for table display
      offset: 0
    });
    
    console.log('📡 Fetching payslips:', `../includes/get-payroll.php?${params}`);
    
    const response = await fetch(`../includes/get-payroll.php?${params}`);
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }
    
    const result = await response.json();
    console.log('✅ Payslip result:', result);
    
    if (result.success && result.data) {
      // ✅ Transform your API data to match table
      currentPayslipData = result.data.map(item => ({
        staff_id: item.employeeId,
        name: item.employeeName,
        month: `${item.month} ${item.year}`,
        gross_salary: item.grossSalary,
        net_salary: item.netSalary,
        status: item.status || 'Pending'
      }));
      
      filteredPayslipData = [...currentPayslipData];
      console.log('✅ Loaded', result.data.length, 'real payslip records');
      
      // Update summary cards
      const totalEl = document.getElementById('total-employees');
      const grossEl = document.getElementById('total-gross');
      const netEl = document.getElementById('total-net');
      
      if (totalEl) totalEl.textContent = result.summary?.total_employees || result.data.length;
      if (grossEl) grossEl.textContent = formatCurrency(result.summary?.total_gross || 0);
      if (netEl) netEl.textContent = formatCurrency(result.summary?.total_net || 0);
      
      renderPayslipTable();
      
    } else {
      console.log('❌ No payslips:', result.error);
      currentPayslipData = [];
      filteredPayslipData = [];
      
      if (tbody) {
        tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-yellow-500">
          <div class="text-xl mb-2">📋 No Payslip Records</div>
          <div class="text-sm">${result.error || 'No records found'}</div>
        </td></tr>`;
      }
    }
  } catch (err) {
    console.error('❌ Payslip error:', err);
    if (tbody) {
      tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-red-500">
        <div class="text-xl mb-2">❌ Error</div>
        <div class="text-sm">${err.message}</div>
      </td></tr>`;
    }
  }
}

function renderPayslipTable() {
  const data = filteredPayslipData.length > 0 ? filteredPayslipData : currentPayslipData;
  const start = (currentPage - 1) * pageSize;
  const end = start + pageSize;
  const paginated = data.slice(start, end);
  
  const tbody = document.getElementById('payslipTableBody');
  if (!tbody) return;
  
  if (paginated.length === 0) {
    tbody.innerHTML = `
      <tr><td colspan="6" class="p-12 text-center text-gray-500 bg-gray-50">
        <div class="text-3xl mb-4">💰 No Payslip Records</div>
      </td></tr>
    `;
  } else {
    tbody.innerHTML = paginated.map((item, index) => `
      <tr class="hover:bg-green-50/50 border-b transition-colors">
        <td class="p-3 font-medium border-r">${item.staff_id}</td>
        <td class="p-3 border-r">${item.name}</td>
        <td class="p-3 text-right border-r">${item.month}</td>
        <td class="p-3 text-right font-semibold border-r">${formatCurrency(item.gross_salary)}</td>
        <td class="p-3 text-right font-bold text-green-600 border-r">${formatCurrency(item.net_salary)}</td>
        <td class="p-3 text-center border-r">
          <span class="px-2 py-1 rounded-full text-xs font-medium ${
            item.status === 'Paid' ? 'bg-green-100 text-green-800' : 
            item.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
            'bg-gray-100 text-gray-800'
          }">${item.status}</span>
        </td>
      </tr>
    `).join('');
  }
  
  renderPagination(data.length);
}

function renderPagination(total) {
  const totalPages = Math.ceil(total / pageSize);
  const pagination = document.getElementById('payrollPagination');
  if (!pagination) return;
  
  pagination.innerHTML = `
    <div class="flex items-center justify-between mt-6 px-4 py-4 bg-gray-50 rounded-xl border">
      <div class="text-sm text-gray-700">
        Showing ${Math.min((currentPage-1)*pageSize +1, total)}-${Math.min(currentPage*pageSize, total)} of ${total} results
      </div>
      <div class="flex items-center space-x-2">
        <button onclick="changePage('prev')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 ${currentPage===1 ? 'opacity-50 cursor-not-allowed' : ''}" ${currentPage===1 ? 'disabled' : ''}>
          Previous
        </button>
        <span class="px-4 py-2 text-sm font-semibold bg-white border rounded-lg">${currentPage} / ${totalPages}</span>
        <button onclick="changePage('next')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 ${currentPage===totalPages ? 'opacity-50 cursor-not-allowed' : ''}" ${currentPage===totalPages ? 'disabled' : ''}>
          Next
        </button>
      </div>
    </div>
  `;
}

function changePage(dir) {
  const totalPages = Math.ceil(
    (currentView === 'excel' ? (filteredExcelData.length || currentExcelData.length) : 
     (filteredPayslipData.length || currentPayslipData.length)) / pageSize
  );
  
  if (dir === 'prev' && currentPage > 1) currentPage--;
  if (dir === 'next' && currentPage < totalPages) currentPage++;
  renderCurrentView();
}

function toggleFilters() {
  const filterSection = document.getElementById('filterSection');
  if (!filterSection) return;
  
  filterSection.classList.toggle('hidden');
}

// ✅ EXPORTS
window.formatCurrency = formatCurrency;
// ✅ Global functions for onclick handlers
window.refreshPayslips = function() {
  const month = document.getElementById('payslipMonth')?.value;
  const year = document.getElementById('payslipYear')?.value;
  window.payrollTable.loadPayslipRecords(month, year);
};

// ✅ Export everything
window.payrollTable = {
  init: initPayrollTable,
  loadPayslipRecords,
  renderExcelTable,
  renderPayslipTable,
  toggleFilters
};