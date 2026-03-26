// payroll-table.js - FIXED VERSION (Excel tab auto-loads uploads/excel/)
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
  
  // ✅ AUTO-LOAD EXCEL ON PAGE LOAD (since Excel tab is default)
  loadExcelDataForCurrentMonth();
  
  loadPayslipRecords();
}

function setupToggleButtons() {
  document.getElementById('excelToggle')?.addEventListener('click', () => switchView('excel'));
  document.getElementById('payslipToggle')?.addEventListener('click', () => switchView('payslip'));
}

function switchView(view) {
  currentView = view;
  currentPage = 1;
  
  // Update buttons
  document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.classList.remove('active', 'bg-blue-600', 'text-white');
    btn.classList.add('bg-gray-200', 'text-gray-700');
  });
  const activeBtn = document.getElementById(view + 'Toggle');
  if (activeBtn) {
    activeBtn.classList.add('active', 'bg-blue-600', 'text-white');
    activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
  }
  
  // Update containers
  document.getElementById('excelTableContainer')?.classList.toggle('active', view === 'excel');
  document.getElementById('excelTableContainer')?.classList.toggle('hidden', view !== 'excel');
  document.getElementById('payslipTableContainer')?.classList.toggle('active', view === 'payslip');
  document.getElementById('payslipTableContainer')?.classList.toggle('hidden', view !== 'payslip');
  
  // ✅ CRITICAL: AUTO-LOAD EXCEL WHEN SWITCHING TO EXCEL TAB
  if (view === 'excel') {
    loadExcelDataForCurrentMonth();
  } else {
    renderPayslipTable();
  }
}

function renderCurrentView() {
  if (currentView === 'excel') {
    renderExcelTable();
  } else {
    renderPayslipTable();
  }
}

// ✅ NEW: Auto-load Excel from uploads/excel/ using current Month/Year
async function loadExcelDataForCurrentMonth() {
  const monthSelect = document.getElementById('statusMonthSelect');
  const yearSelect = document.getElementById('statusYearSelect');
  
  const month = monthSelect?.value || '01'; // Default January
  const year = parseInt(yearSelect?.value) || new Date().getFullYear();
  
  console.log('📊 [Excel Tab] Auto-loading Excel for Month:', month, 'Year:', year);
  
  const tbody = document.getElementById('excelTableBody');
  if (tbody) {
    tbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>Loading Excel from uploads/excel/...</td></tr>';
  }
  
  try {
    // ✅ CALL upload.js function (already works perfectly)
    if (window.uploadManager?.loadPayrollData) {
      await window.uploadManager.loadPayrollData(month, year);
    } else {
      throw new Error('uploadManager not loaded');
    }
  } catch (error) {
    console.error('❌ Excel load failed:', error);
    if (tbody) {
      tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-red-500">
        <div class="text-xl mb-2">❌ No Excel File</div>
        <div class="text-sm text-gray-600">Upload Excel for ${getMonthName(month)} ${year} first</div>
      </td></tr>`;
    }
  }
}

function getMonthName(monthNum) {
  const months = {
    '01': 'January', '02': 'February', '03': 'March', '04': 'April',
    '05': 'May', '06': 'June', '07': 'July', '08': 'August',
    '09': 'September', '10': 'October', '11': 'November', '12': 'December'
  };
  return months[monthNum] || monthNum;
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
        <div class="text-lg mb-4 text-gray-600">
          Select Month/Year above and click "📊 Load Excel Preview"<br>
          or upload new Excel file
        </div>
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

// ✅ Keep your existing loadPayslipRecords, renderPayslipTable, etc. (unchanged)
async function loadPayslipRecords(month = null, year = null) {
  // Your existing code stays exactly the same...
  const tbody = document.getElementById('payslipTableBody');
  if (tbody) {
    tbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto mb-2"></div><p>Loading payslip records...</p></td></tr>';
  }
  
  try {
    const params = new URLSearchParams({
      month: month || '',
      year: year || '',
      limit: 1000,
      offset: 0
    });
    
    const response = await fetch(`../includes/get-payroll.php?${params}`);
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    
    const result = await response.json();
    
    if (result.success && result.data) {
      currentPayslipData = result.data.map(item => ({
        staff_id: item.employeeId,
        name: item.employeeName,
        month: `${item.month} ${item.year}`,
        gross_salary: item.grossSalary,
        net_salary: item.netSalary,
        status: item.status || 'Pending'
      }));
      
      filteredPayslipData = [...currentPayslipData];
      renderPayslipTable();
    } else {
      currentPayslipData = [];
      filteredPayslipData = [];
      if (tbody) {
        tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-yellow-500">
          <div class="text-xl mb-2">📋 No Payslip Records</div>
        </td></tr>`;
      }
    }
  } catch (err) {
    console.error('Payslip error:', err);
    if (tbody) {
      tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-red-500">❌ Error: ${err.message}</td></tr>`;
    }
  }
}

function renderPayslipTable() {
  // Your existing code stays exactly the same...
  const data = filteredPayslipData.length > 0 ? filteredPayslipData : currentPayslipData;
  const start = (currentPage - 1) * pageSize;
  const end = start + pageSize;
  const paginated = data.slice(start, end);
  
  const tbody = document.getElementById('payslipTableBody');
  if (!tbody) return;
  
  if (paginated.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-gray-500 bg-gray-50"><div class="text-3xl mb-4">💰 No Payslip Records</div></td></tr>`;
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

// Keep all your existing functions unchanged: renderPagination, changePage, toggleFilters
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
        <button onclick="changePage('prev')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 ${currentPage===1 ? 'opacity-50 cursor-not-allowed' : ''}" ${currentPage===1 ? 'disabled' : ''}>Previous</button>
        <span class="px-4 py-2 text-sm font-semibold bg-white border rounded-lg">${currentPage} / ${totalPages}</span>
        <button onclick="changePage('next')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 ${currentPage===totalPages ? 'opacity-50 cursor-not-allowed' : ''}" ${currentPage===totalPages ? 'disabled' : ''}>Next</button>
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

// ✅ Auto-sync Month/Year changes with Excel tab
document.addEventListener('DOMContentLoaded', function() {
  const monthSelect = document.getElementById('statusMonthSelect');
  const yearSelect = document.getElementById('statusYearSelect');
  
  if (monthSelect) {
    monthSelect.addEventListener('change', function() {
      if (currentView === 'excel') loadExcelDataForCurrentMonth();
    });
  }
  
  if (yearSelect) {
    yearSelect.addEventListener('change', function() {
      if (currentView === 'excel') loadExcelDataForCurrentMonth();
    });
  }
});

// ✅ EXPORTS (unchanged)
window.formatCurrency = formatCurrency;
window.refreshPayslips = function() {
  const month = document.getElementById('payslipMonth')?.value;
  const year = document.getElementById('payslipYear')?.value;
  window.payrollTable.loadPayslipRecords(month, year);
};

window.payrollTable = {
  init: initPayrollTable,
  loadPayslipRecords,
  renderExcelTable,
  renderPayslipTable,
  toggleFilters
};