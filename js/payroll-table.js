// payroll-table.js - FIXED VERSION with Month/Year Filter
let currentExcelData = [];
let currentPayslipData = [];
let filteredExcelData = [];
let filteredPayslipData = [];
let currentPage = 1;
let currentView = 'excel';
const pageSize = 10;

const formatCurrency = (amount) => `₦${parseFloat(amount || 0).toLocaleString()}`;

function initPayrollTable() {
  setupToggleButtons();
  
  // ✅ FIXED: Load LAST UPLOADED record on page load
  loadLatestPayroll();
}

function setupToggleButtons() {
  document.getElementById('excelToggle')?.addEventListener('click', () => switchView('excel'));
  document.getElementById('payslipToggle')?.addEventListener('click', () => switchView('payslip'));
}

function switchView(view) {
  currentView = view;
  currentPage = 1;
  
  document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.classList.remove('active', 'bg-blue-600', 'text-white');
    btn.classList.add('bg-gray-200', 'text-gray-700');
  });
  const activeBtn = document.getElementById(view + 'Toggle');
  if (activeBtn) {
    activeBtn.classList.add('active', 'bg-blue-600', 'text-white');
    activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
  }
  
  document.getElementById('excelTableContainer')?.classList.toggle('hidden', view !== 'excel');
  document.getElementById('payslipTableContainer')?.classList.toggle('hidden', view === 'excel');
  
  if (view === 'excel') {
    renderExcelTable();
  } else {
    renderPayslipTable();
  }
}

// ✅ FIXED: Month/Year Filter Functions (MOVED OUTSIDE DOMContentLoaded)
async function loadFilteredPayroll() {
  const month = document.getElementById('filterMonth')?.value;
  const year = document.getElementById('filterYear')?.value;
  
  if (!month || !year) {
    alert('Please select Month and Year');
    return;
  }
  
  showTableLoading();
  
  // Load Excel
  await window.uploadManager?.loadPayrollData(month, year);
  
  // Load Payslips  
  await loadPayslipRecords(month, year);
  
  console.log(`✅ Loaded ${month}/${year} for both tables`);
}

async function loadLatestPayroll() {
  showTableLoading();
  // ✅ Set default values first (prevents alert)
  const monthEl = document.getElementById('filterMonth');
  const yearEl = document.getElementById('filterYear');
  if (monthEl && !monthEl.value) monthEl.value = new Date().getMonth() + 1; // Current month
  if (yearEl && !yearEl.value) yearEl.value = new Date().getFullYear();
  
  
  try {
    const response = await fetch('../includes/get-payroll.php?get_latest=1');
    const result = await response.json();
    const month = result.success && result.latest ? result.latest.month.padStart(2, '0') : sprintf('%02d', new Date().getMonth() + 1);
    const year = result.success && result.latest ? result.latest.year : new Date().getFullYear();
    
    // Set dropdowns
    document.getElementById('filterMonth').value = month;
    document.getElementById('filterYear').value = year;
    
    // Load data
    await window.uploadManager?.loadPayrollData(month, year);
    await loadPayslipRecords(month, year);
    
    // if (result.success && result.latest) {
    //   const monthEl = document.getElementById('filterMonth');
    //   const yearEl = document.getElementById('filterYear');
    //   if (monthEl) monthEl.value = result.latest.month.padStart(2, '0');
    //   if (yearEl) yearEl.value = result.latest.year;
      
    //   await loadFilteredPayroll();
    // } else {
    //   // // Fallback: current month
    //   // await window.uploadManager?.loadPayrollData();
    //   // await loadPayslipRecords();
    // await window.uploadManager?.loadPayrollData(monthEl?.value || '', yearEl?.value || '');
    //   await loadPayslipRecords(monthEl?.value || '', yearEl?.value || '');
    // }
  } catch (error) {
    console.error('Latest load error:', error);
  //   await window.uploadManager?.loadPayrollData();
  //   await loadPayslipRecords();
  // }
  // ✅ No alert - just clear tables
    document.getElementById('excelTableBody').innerHTML = '<tr><td colspan="6" class="p-12 text-center text-gray-400">No Excel data yet. Upload first!</td></tr>';
    document.getElementById('payslipTableBody').innerHTML = '<tr><td colspan="6" class="p-12 text-center text-gray-400">No payslips yet. Save payroll first!</td></tr>';
  }
}
function toggleFilters() {
  document.getElementById('filterSection').classList.toggle('hidden');
}
function showTableLoading() {
  const excelTbody = document.getElementById('excelTableBody');
  const payslipTbody = document.getElementById('payslipTableBody');
  
  if (excelTbody) {
    excelTbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>Loading Excel...</td></tr>';
  }
  if (payslipTbody) {
    payslipTbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto mb-4"></div>Loading Payslips...</td></tr>';
  }
}

// ✅ Your existing functions (unchanged)
async function loadExcelDataForCurrentMonth() {
  const monthSelect = document.getElementById('statusMonthSelect');
  const yearSelect = document.getElementById('statusYearSelect');
  const month = monthSelect?.value || '01';
  const year = parseInt(yearSelect?.value) || new Date().getFullYear();
  
  const tbody = document.getElementById('excelTableBody');
  if (tbody) {
    tbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>Loading Excel...</td></tr>';
  }
  
  if (window.uploadManager?.loadPayrollData) {
    await window.uploadManager.loadPayrollData(month, year);
  }
}

function getMonthName(monthNum) {
  const months = {'01':'January','02':'February','03':'March','04':'April','05':'May','06':'June','07':'July','08':'August','09':'September','10':'October','11':'November','12':'December'};
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
    tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-gray-500 bg-gray-50">
      <div class="text-3xl mb-4">📄 No Excel Data</div>
    </td></tr>`;
  } else {
    tbody.innerHTML = paginated.map((item, index) => `
      <tr class="hover:bg-gray-50/50 border-b">
        <td class="p-3 text-xs font-mono border-r">${start + index + 1}</td>
        <td class="p-3 font-medium border-r">${item.staff_id || ''}</td>
        <td class="p-3 border-r">${item.name || ''}</td>
        <td class="p-3 border-r">${item.department || ''}</td>
        <td class="p-3 text-right font-semibold border-r">${formatCurrency(item.gross_salary)}</td>
        <td class="p-3 text-right font-bold text-green-600 border-r">${formatCurrency(item.net_salary || 0)}</td>
      </tr>
    `).join('');
  }
  renderPagination(data.length);
}

// ✅ ADD THIS FUNCTION (you removed it accidentally)
async function loadExcelDataForCurrentMonth() {
  const monthSelect = document.getElementById('statusMonthSelect');
  const yearSelect = document.getElementById('statusYearSelect');
  const month = monthSelect?.value || '01';
  const year = parseInt(yearSelect?.value) || new Date().getFullYear();
  
  const tbody = document.getElementById('excelTableBody');
  if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center"><div class="animate-spin h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>Loading...</td></tr>';
  
  await window.uploadManager?.loadPayrollData(month, year);
}

async function loadPayslipRecords(month = null, year = null) {
  const tbody = document.getElementById('payslipTableBody');
  if (tbody) {
    tbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto mb-2"></div>Loading payslips...</td></tr>';
  }
  
  try {
    const params = new URLSearchParams({month: month || '', year: year || '', limit: 1000, offset: 0});
    const response = await fetch(`../includes/get-payroll.php?${params}`);
    const result = await response.json();
    
    if (result.success && result.data) {
      currentPayslipData = result.data.map(item => ({
        staff_id: item.employeeId, name: item.employeeName, month: `${item.month} ${item.year}`,
        gross_salary: item.grossSalary, net_salary: item.netSalary, status: item.status || 'Pending'
      }));
      filteredPayslipData = [...currentPayslipData];
      renderPayslipTable();
    } else {
      currentPayslipData = []; filteredPayslipData = [];
      if (tbody) tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-yellow-500">📋 No Payslip Records</td></tr>`;
    }
  } catch (err) {
    console.error('Payslip error:', err);
    if (tbody) tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-red-500">❌ Error: ${err.message}</td></tr>`;
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
    tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-gray-500 bg-gray-50"><div class="text-3xl mb-4">💰 No Payslip Records</div></td></tr>`;
  } else {
    tbody.innerHTML = paginated.map((item, index) => `
      <tr class="hover:bg-green-50/50 border-b">
        <td class="p-3 font-medium border-r">${item.staff_id}</td>
        <td class="p-3 border-r">${item.name}</td>
        <td class="p-3 text-right border-r">${item.month}</td>
        <td class="p-3 text-right font-semibold border-r">${formatCurrency(item.gross_salary)}</td>
        <td class="p-3 text-right font-bold text-green-600 border-r">${formatCurrency(item.net_salary)}</td>
        <td class="p-3 text-center border-r">
          <span class="px-2 py-1 rounded-full text-xs font-medium ${
            item.status === 'Paid' ? 'bg-green-100 text-green-800' : 
            item.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'
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
      <div class="text-sm text-gray-700">Showing ${Math.min((currentPage-1)*pageSize +1, total)}-${Math.min(currentPage*pageSize, total)} of ${total}</div>
      <div class="flex items-center space-x-2">
        <button onclick="changePage('prev')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 ${currentPage===1?'opacity-50 cursor-not-allowed':''}">Previous</button>
        <span class="px-4 py-2 text-sm font-semibold bg-white border rounded-lg">${currentPage}/${totalPages}</span>
        <button onclick="changePage('next')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 ${currentPage===totalPages?'opacity-50 cursor-not-allowed':''}">Next</button>
      </div>
    </div>
  `;
}

function changePage(dir) {
  const totalPages = Math.ceil((currentView === 'excel' ? (filteredExcelData.length || currentExcelData.length) : (filteredPayslipData.length || currentPayslipData.length)) / pageSize);
  if (dir === 'prev' && currentPage > 1) currentPage--;
  if (dir === 'next' && currentPage < totalPages) currentPage++;
  if (currentView === 'excel') renderExcelTable(); else renderPayslipTable();
}

// ✅ EXPORTS - Make functions global
window.payrollTable = {
  init: initPayrollTable,
  loadPayslipRecords,
  renderExcelTable,
  renderPayslipTable,
  loadFilteredPayroll,    // ✅ NEW
  loadLatestPayroll,      // ✅ NEW
  showTableLoading        // ✅ NEW
};

window.renderExcelTable = renderExcelTable;
window.formatCurrency = formatCurrency;
window.currentExcelData = [];
window.filteredExcelData = [];