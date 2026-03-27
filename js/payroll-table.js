// payroll-table.js - FIXED VERSION (Excel tab auto-loads uploads/excel/)
let currentExcelData = [];
let currentPayslipData = [];
let filteredExcelData = [];
let filteredPayslipData = [];
let currentPage = 1;
let currentView = 'excel'; // 'excel' or 'payslip'
const pageSize = 10;
// ✅ Add these GLOBAL VARIABLES at the top of payroll-table.js (after existing vars)
let filteredMonth = '';
let filteredYear = '';

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
  
  // Update buttons (your existing code)
  document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.classList.remove('active', 'bg-blue-600', 'text-white');
    btn.classList.add('bg-gray-200', 'text-gray-700');
  });
  const activeBtn = document.getElementById(view + 'Toggle');
  if (activeBtn) {
    activeBtn.classList.add('active', 'bg-blue-600', 'text-white');
    activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
  }
  
  // Update containers (your existing code)
  document.getElementById('excelTableContainer')?.classList.toggle('active', view === 'excel');
  document.getElementById('excelTableContainer')?.classList.toggle('hidden', view !== 'excel');
  document.getElementById('payslipTableContainer')?.classList.toggle('active', view === 'payslip');
  document.getElementById('payslipTableContainer')?.classList.toggle('hidden', view !== 'payslip');
  
  // ✅ NEW: Auto-show filter section on Payslip tab
  const filterSection = document.getElementById('filterSection');
  if (view === 'payslip' && filterSection) {
    filterSection.classList.remove('hidden');
  }
  
  if (view === 'excel') {
    loadExcelDataForCurrentMonth();
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
  
  // console.log('📊 [Excel Tab] Auto-loading Excel for Month:', month, 'Year:', year);
  
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
    // console.error('❌ Excel load failed:', error);
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
  console.log('🔍 DEBUG RENDER - currentExcelData:', currentExcelData.length);
  console.log('🔍 DEBUG RENDER - filteredExcelData:', filteredExcelData.length);
  console.log('🔍 DEBUG RENDER - window.currentExcelData:', window.currentExcelData?.length);
  

  // console.log('📊 [renderExcelTable] Data check - currentExcelData:', currentExcelData?.length || 0, 'filteredExcelData:', filteredExcelData?.length || 0);
  const data = filteredExcelData.length > 0 ? filteredExcelData : currentExcelData;
  console.log('📊 Using data length:', data.length);
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
        <td class="p-3 text-right font-bold text-green-600 border-r">${formatCurrency(item.net_salary || 0)}</td>
      </tr>
    `).join('');
  }
  
  renderPagination(data.length);
  
  // console.log('✅ [payroll-table] Rendered Excel table with', data.length, 'rows');
}


// ✅ Update your existing loadPayslipRecords to use global filters
async function loadPayslipRecords(month = filteredMonth, year = filteredYear) {
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
      updateFilterCount();
    } else {
      currentPayslipData = [];
      filteredPayslipData = [];
      if (tbody) {
        tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-yellow-500">
          <div class="text-xl mb-2">📋 No Payslip Records</div>
          ${filteredMonth || filteredYear ? '<div class="text-sm mt-2">Try different month/year</div>' : ''}
        </td></tr>`;
      }
      updateFilterCount();
    }
  } catch (err) {
    // console.error('Payslip error:', err);
    if (tbody) {
      tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-red-500">❌ Error: ${err.message}</td></tr>`;
    }
  }
}

// ✅ Update renderPayslipTable to show filter info
function renderPayslipTable() {
  const data = filteredPayslipData.length > 0 ? filteredPayslipData : currentPayslipData;
  const start = (currentPage - 1) * pageSize;
  const end = start + pageSize;
  const paginated = data.slice(start, end);
  
  const tbody = document.getElementById('payslipTableBody');
  if (!tbody) return;
  
  if (paginated.length === 0) {
    const filterText = filteredMonth || filteredYear ? 
      `No payslips found for ${getMonthName(filteredMonth)} ${filteredYear}` : 
      'No payslip records found';
      
    tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-gray-500 bg-gray-50">
      <div class="text-3xl mb-4">💰 ${filterText}</div>
      ${filteredMonth || filteredYear ? 
        '<button onclick="clearPayslipFilters()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 mt-4">Clear Filters</button>' : 
        ''
      }
    </td></tr>`;
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

// ✅ Auto-sync Month/Year changes with Excel tab + Excel Data Listener
document.addEventListener('DOMContentLoaded', function() {
  const monthSelect = document.getElementById('statusMonthSelect');
  const yearSelect = document.getElementById('statusYearSelect');
  
  // ✅ Set default month to current
  const now = new Date();
  const currentMonth = (now.getMonth() + 1).toString().padStart(2, '0');
  if (monthSelect && !monthSelect.value) {
    monthSelect.value = currentMonth;
    loadExcelDataForCurrentMonth(); // Initial load with current month
  }
  
  if (monthSelect) {
    monthSelect.addEventListener('change', function() {
      console.log('📅 Month changed to:', this.value, 'Excel view:', currentView === 'excel');
      if (currentView === 'excel') {
        loadExcelDataForCurrentMonth();
      }
    });
  }
  
  if (yearSelect) {
    yearSelect.addEventListener('change', function() {
      console.log('📅 Year changed to:', this.value, 'Excel view:', currentView === 'excel');
      if (currentView === 'excel') {
        loadExcelDataForCurrentMonth();
      }
    });
  }
  
// ✅ Listen for data loaded from upload.js - FIXED to always render
  window.addEventListener('excelDataLoaded', function(e) {
    console.log('🎉 [payroll-table] excelDataLoaded received');
    
    // Sync window globals to local vars
    currentExcelData = window.currentExcelData || [];
    filteredExcelData = window.filteredExcelData || [...currentExcelData];
    
    console.log('🔄 Synced data:', currentExcelData.length, 'rows');
    
    // Force render regardless of tab (Excel tab will show it)
    renderExcelTable();
    
    // Update filter count display
    const countEl = document.getElementById('filterCount');
    if (countEl && currentView === 'excel') {
      countEl.textContent = `${currentExcelData.length} Excel rows`;
      countEl.classList.remove('hidden');
    }
  });
  
  // Initial payroll table setup
  if (window.payrollTable) {
    window.payrollTable.init();
  }
});

// ✅ Make render function globally accessible
window.renderExcelTableGlobal = renderExcelTable;


// ✅ EXPORTS (unchanged)
window.formatCurrency = formatCurrency;

window.refreshPayslips = async function() {
  const monthSelect = document.getElementById('payslipMonth');
  const yearInput = document.getElementById('payslipYear');
  
  const month = monthSelect?.value?.trim() || '';
  const year = yearInput?.value?.trim() || '';
  
  // console.log('🔍 [refreshPayslips] Values:', { month, year });
  
  if (!month || !year) {
    alert('Please select both month and year');
    return;
  }
  
  const tbody = document.getElementById('payslipTableBody');
  if (tbody) {
    tbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>🔄 Filtering...</td></tr>';
  }
  
  try {
    // ✅ BULLETPROOF URL with proper encoding
    const params = new URLSearchParams({
      month: month,
      year: year
    });
    
    const url = `../includes/filter-payslips.php?${params.toString()}`;
    // console.log('🔗 [refreshPayslips] Fetching:', url);
    
    const response = await fetch(url, {
      method: 'GET',
      credentials: 'same-origin', // ✅ Include session cookies
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    
    // ✅ DEBUG: Check response details
    // console.log('📊 [Response] Status:', response.status, 'Type:', response.headers.get('content-type'));
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    // ✅ Check if response is actually JSON
    const contentType = response.headers.get('content-type');
    if (!contentType?.includes('application/json')) {
      const htmlText = await response.text();
      // console.error('❌ [HTML Response] Not JSON:', htmlText.substring(0, 300));
      throw new Error('Server returned HTML instead of JSON - check login/session');
    }
    
    const result = await response.json();
    // console.log('✅ [refreshPayslips] Result:', result);
    
    if (result.success && result.data) {
      // ✅ Map data with correct field names (match your PHP)
      currentPayslipData = result.data.map(item => ({
        staff_id: item.id || 'N/A',
        name: item.employeeName || 'Unknown',
        month: `${item.month} ${item.year}`,
        gross_salary: item.gross_salary,
        net_salary: item.net_salary,
        status: 'Generated' // Default for payslips
      }));
      
      filteredPayslipData = [...currentPayslipData];
      renderPayslipTable();
      
      // ✅ Update filter status
      const filterStatus = document.getElementById('filterStatus');
      if (filterStatus) {
        filterStatus.textContent = `${result.count} payslip${result.count !== 1 ? 's' : ''} found for ${month} ${year}`;
      }
      
    } else {
      // console.error('❌ API Error:', result?.error || 'Unknown error');
      if (tbody) {
        tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-yellow-500">
          <div class="text-xl mb-2">📭 ${result?.error || 'No payslips found'}</div>
          <div class="text-sm mt-2">${month} ${year}</div>
        </td></tr>`;
      }
    }
    
  } catch (error) {
    // console.error('💥 [refreshPayslips] Error:', error);
    
    if (tbody) {
      tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-red-500">
        <div class="text-xl mb-2">❌ Error</div>
        <div class="text-sm mt-2">${error.message}</div>
        <div class="text-xs mt-2 text-gray-500">Check browser console for details</div>
      </td></tr>`;
    }
  }
};
// ✅ NEW: Update filter count display
function updateFilterCount() {
  const countEl = document.getElementById('filterCount');
  const total = filteredPayslipData.length || currentPayslipData.length;
  
  if (filteredMonth || filteredYear) {
    countEl.textContent = `${total} records`;
    countEl.classList.remove('hidden');
  } else {
    countEl.classList.add('hidden');
  }
}

// ✅ NEW: Clear filters function
window.clearPayslipFilters = function() {
  document.getElementById('payslipMonth').value = '';
  document.getElementById('payslipYear').value = '';
  filteredMonth = '';
  filteredYear = '';
  
  loadPayslipRecords(); // Load all
  updateFilterCount();
};

window.payrollTable = {
  init: initPayrollTable,
  loadPayslipRecords,
  renderExcelTable,
  renderPayslipTable,
  toggleFilters
};

  // ... rest of your code
window.refreshPayslips = async function() {
  const monthSelect = document.getElementById('payslipMonth');
  const yearInput = document.getElementById('payslipYear');
  
  const month = monthSelect?.value?.trim() || '';
  const year = yearInput?.value?.trim() || '';
  
  // console.log('🔍 Filtering:', month, year);
  
  if (!month || !year) {
    alert('Please select month and year');
    return;
  }
  
  const tbody = document.getElementById('payslipTableBody');
  tbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto"></div>Loading...</td></tr>';
  
  try {
    const response = await fetch(`../includes/filter-payslip.php?month=${month}&year=${year}`, {
      credentials: 'same-origin'
    });
    
    const result = await response.json();
    // console.log('✅ FULL RESULT:', result); // ← Check this in console!
    
    if (result.success && result.data) {
      // ✅ FIXED: Match PHP field names EXACTLY
      currentPayslipData = result.data.map(item => ({
        staff_id: item.id || 'N/A',           // ✅ Use 'id' from payslip table
        name: item.employeeName || 'Unknown', // ✅ Matches PHP
        month: item.month,                    // ✅ Direct from PHP
        year: item.year,                      // ✅ Direct from PHP  
        gross_salary: item.gross_salary,      // ✅ snake_case from PHP
        net_salary: item.net_salary,          // ✅ snake_case from PHP
        status: 'Generated'                   // ✅ Default
      }));
      
      filteredPayslipData = [...currentPayslipData];
      
      // console.log('✅ Mapped data:', currentPayslipData[0]); // ← Check first row
      
      renderPayslipTable();
      
      // ✅ Show count
      const filterStatus = document.getElementById('filterStatus');
      if (filterStatus) {
        filterStatus.textContent = `${result.count || 0} payslip${result.count !== 1 ? 's' : ''} found`;
        filterStatus.classList.remove('hidden');
      }
      
    } else {
      // console.error('❌ No data:', result);
      tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-yellow-500">
        📭 No payslips for ${month}/${year}<br>
        <small>${result.error || 'Unknown error'}</small>
      </td></tr>`;
    }
  } catch (error) {
    // console.error('💥 Error:', error);
    tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-red-500">
      ❌ ${error.message}
    </td></tr>`;
  }
};
// ✅ Global access for upload.js
window.renderExcelTable = renderExcelTable;
window.currentExcelData = [];
window.filteredExcelData = [];
