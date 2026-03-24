const API_BASE = '../includes/';
let currentPayrollData = [];
let filteredData = [];
let currentPage = 1;
const pageSize = 10;
let previewData = [];
let currentBatchId = null;
let selectedMonth = '';

const formatCurrency = (amount) => `₦${parseFloat(amount || 0).toLocaleString()}`;

document.addEventListener('DOMContentLoaded', function() {
  loadTotalEmployees();
  loadPayrollData();
  setupEventListeners();
});

async function loadTotalEmployees() {
  try {
    const response = await fetch(`${API_BASE}get-users.php`);
    const result = await response.json();
    if (result.success) {
      document.getElementById('total-employees').textContent = result.total_employees;
    }
  } catch (err) {
    console.error('Failed to load total employees:', err);
  }
}

function setupEventListeners() {
  document.getElementById('uploadForm')?.addEventListener('submit', handleFileUpload);
  document.getElementById('applyFilters')?.addEventListener('click', applyFilters);
  document.getElementById('viewPayslipsBtn')?.addEventListener('click', showViewPayslipsModal);
  
  // Month select change
  const monthSelect = document.getElementById('monthSelect');
  if (monthSelect) {
    monthSelect.addEventListener('change', function() {
      loadPayrollData(this.value);
    });
  }
  
  // Real-time filters
  ['nameFilter', 'staffIdFilter', 'monthFilter', 'deptFilter'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', debounce(applyFilters, 300));
  });
}

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

async function loadPayrollData(month = '', year = new Date().getFullYear()) {
  // Show loading
  const tbody = document.getElementById('payrollTableBody');
  tbody.innerHTML = '<tr><td colspan="15" class="p-8 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p>Loading Excel data...</p></td></tr>';
  
  try {
    // Use Excel endpoint by default
    const params = new URLSearchParams({
      mode: 'get_excel',
      month: month,
      year: year,
      limit: pageSize,
      offset: (currentPage - 1) * pageSize
    });
    
    const response = await fetch(`${API_BASE}upload-payroll.php?${params}`);
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    
    const result = await response.json();
    
    if (result.success) {
      currentPayrollData = result.excel_data || [];
      filteredData = [...currentPayrollData];
      renderExcelTable(); // New Excel render function
      updateExcelSummary(result);
      if (result.months) populatePreviousMonths(result.months); // Keep for navigation
    } else {
      tbody.innerHTML = `<tr><td colspan="15" class="p-8 text-center text-gray-500">No Excel data: ${result.error || 'Unknown error'}</td></tr>`;
    }
  } catch (err) {
    console.error('Excel load error:', err);
    tbody.innerHTML = `<tr><td colspan="15" class="p-8 text-center text-red-500">
      <div>Error loading Excel: ${err.message}</div>
      <div class="text-xs mt-1">Ensure Excel file exists for selected month</div>
    </td></tr>`;
  }
}

async function handleFileUpload(e) {
  e.preventDefault();
  const formData = new FormData(e.target);
  formData.append('mode', 'preview');
  const uploadBtn = document.getElementById('uploadBtn');
  const status = document.getElementById('uploadStatus');
  
  uploadBtn.disabled = true;
  uploadBtn.textContent = 'Uploading...';
  status.classList.remove('hidden');
  status.innerHTML = '<div class="text-blue-600">Processing...</div>';
  
  try {
    const response = await fetch(API_BASE + 'upload-payroll.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    const result = await response.json();
    
    if (result.success) {

      status.innerHTML = `<div class="text-green-600">${result.message}</div>`;
      if (result.preview_data && result.preview_data.length > 0) {
        showPreview(result);
        loadPayrollData();
      } else {
        status.innerHTML += '<div class="text-yellow-600">No data to preview</div>';
      }
    } else {
      status.innerHTML = `<div class="text-red-600 bg-red-100 p-2 rounded">${result.error}</div>`;
    }
  } catch (err) {
    status.innerHTML = `<div class="text-red-600 bg-red-100 p-2 rounded">Error: ${err.message}</div>`;
  }
  
  uploadBtn.disabled = false;
  uploadBtn.textContent = 'Upload & Preview';
}

function showPreview(result) {
  currentBatchId = result.batch_id;
  previewData = result.preview_data || result.preview || [];
  
  const previewSection = document.getElementById('previewSection');
  const previewTable = document.getElementById('previewTable');
  
  if (!previewData || previewData.length === 0) {
    console.warn('No preview data');
    document.getElementById('uploadStatus').innerHTML = '<div class="text-yellow-600">No valid data found in file</div>';
    return;
  }
  
  // Show first 20 rows
  previewTable.innerHTML = previewData.slice(0, 20).map(item => `
    <tr>
      <td class="p-2 border">${item.staff_id || ''}</td>
      <td class="p-2 border">${item.name}</td>
      <td class="p-2 border">${item.department}</td>
      <td class="p-2 border">${formatCurrency(item.gross_salary)}</td>
      <td class="p-2 border">${formatCurrency(item.pro_rata)}</td>
      <td class="p-2 border">${item.days_worked || ''}</td>
      <td class="p-2 border">${formatCurrency(item.basic_salary)}</td>
      <td class="p-2 border">${formatCurrency(item.housing)}</td>
      <td class="p-2 border">${formatCurrency(item.transport)}</td>
      <td class="p-2 border">${formatCurrency(item.medical)}</td>
      <td class="p-2 border">${formatCurrency(item.utility)}</td>
      <td class="p-2 border">${formatCurrency(item.paye)}</td>
      <td class="p-2 border">${formatCurrency(item.deductions)}</td>
      <td class="p-2 border">${formatCurrency(item.pension)}</td>
      <td class="p-2 border">${formatCurrency(item.net_salary)}</td>
    </tr>
  `).join('') || '<tr><td colspan="15" class="p-4 text-center">No preview data</td></tr>';
  
  previewSection.classList.remove('hidden');
  const saveBtn = document.getElementById('saveBtn');
  if (saveBtn) {
    saveBtn.disabled = previewData.length === 0;

  }
  
  // Show count
  const header = previewSection.querySelector('h2');
  if (header) {
    header.textContent = `Preview Data (${previewData.length} rows) - Save to confirm`;
  }
}

function populatePreviousMonths(months) {
  const container = document.getElementById('previousMonths');
  if (container) {
    container.innerHTML = months.map(m => 
      `<span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm cursor-pointer hover:bg-blue-200" onclick="loadPayrollData('${m}')">${m}</span>`
    ).join('');
  }
}

function renderExcelTable() {
  const data = filteredData.length ? filteredData : currentPayrollData;
  const start = (currentPage - 1) * pageSize;
  const end = start + pageSize;
  const paginated = data.slice(start, end);
  
  const tbody = document.getElementById('payrollTableBody');
  tbody.innerHTML = paginated.length ? paginated.map((item, index) => `
    <tr class="hover:bg-gray-50/50 border-b">
      <td class="p-2 text-xs font-mono border-r">${item.row_index || '-'}</td>
      <td class="p-2 font-medium border-r">${item.staff_id || ''}</td>
      <td class="p-2 border-r">${item.name || ''}</td>
      <td class="p-2 border-r">${item.department || ''}</td>
      <td class="p-2 text-right font-semibold border-r">${formatCurrency(item.gross_salary)}</td>
      <td class="p-2 text-right border-r">${formatCurrency(item.pro_rata)}</td>
      <td class="p-2 text-center border-r">${item.days_worked || ''}</td>
      <td class="p-2 text-right border-r">${formatCurrency(item.basic_salary)}</td>
      <td class="p-2 text-right border-r">${formatCurrency(item.housing)}</td>
      <td class="p-2 text-right border-r">${formatCurrency(item.transport)}</td>
      <td class="p-2 text-right border-r">${formatCurrency(item.medical)}</td>
      <td class="p-2 text-right border-r">${formatCurrency(item.utility)}</td>
      <td class="p-2 text-right font-semibold border-r">${formatCurrency(item.paye)}</td>
      <td class="p-2 text-right border-r">${formatCurrency(item.deductions)}</td>
      <td class="p-2 text-right border-r">${formatCurrency(item.pension)}</td>
      <td class="p-2 text-right font-bold text-green-600 border-r">${formatCurrency(item.net_salary)}</td>
    </tr>
  `).join('') : '<tr><td colspan="16" class="p-12 text-center text-gray-500"><div class="text-xl mb-2">📄 No Excel Data</div><div class="text-lg">Upload an Excel payroll file to view raw content</div></td></tr>';
  
  renderPagination(data.length);
}

function updateExcelSummary(result) {
  // Calculate summary from Excel data
  const data = currentPayrollData;
  const totalGross = data.reduce((sum, item) => sum + (item.gross_salary || 0), 0);
  const totalNet = data.reduce((sum, item) => sum + (item.net_salary || 0), 0);
  const totalRows = data.length;
  
  document.getElementById('total-employees')?.textContent = totalRows;
  document.getElementById('total-gross')?.textContent = formatCurrency(totalGross);
  document.getElementById('total-net')?.textContent = formatCurrency(totalNet);
}

function renderPagination(total) {
  const totalPages = Math.ceil(total / pageSize);
  const pagination = document.getElementById('payrollPagination');
  if (pagination) {
    pagination.innerHTML = `
      <div class="flex items-center justify-between mt-4 px-4 py-3 bg-gray-50 rounded-lg">
        <div class="text-sm text-gray-700">Showing ${Math.min((currentPage-1)*pageSize +1, total)}-${Math.min(currentPage*pageSize, total)} of ${total}</div>
        <div class="flex space-x-2">
          <button onclick="changePage('prev')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 ${currentPage===1 ? 'opacity-50 cursor-not-allowed' : ''}" ${currentPage===1 ? 'disabled' : ''}>Previous</button>
          <span class="px-3 py-2 text-sm font-medium">${currentPage} / ${totalPages}</span>
          <button onclick="changePage('next')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 ${currentPage===totalPages ? 'opacity-50 cursor-not-allowed' : ''}" ${currentPage===totalPages ? 'disabled' : ''}>Next</button>
        </div>
      </div>
    `;
  }
}

function changePage(dir) {
  const totalPages = Math.ceil((filteredData.length || currentPayrollData.length) / pageSize);
  if (dir === 'prev' && currentPage > 1) currentPage--;
  if (dir === 'next' && currentPage < totalPages) currentPage++;
  renderPayrollTable();
}

function updateSummary(summary) {
  document.getElementById('total-employees').textContent = summary.total_employees || 0;
  document.getElementById('total-gross').textContent = formatCurrency(summary.total_gross || 0);
  document.getElementById('total-net').textContent = formatCurrency(summary.total_net || 0);
}

function applyFilters() {
  const name = document.getElementById('nameFilter')?.value.toLowerCase() || '';
  const staffId = document.getElementById('staffIdFilter')?.value.toLowerCase() || '';
  const month = document.getElementById('monthFilter')?.value || '';
  const dept = document.getElementById('deptFilter')?.value || '';
  
  filteredData = currentPayrollData.filter(item => 
    (!name || item.name.toLowerCase().includes(name)) &&
    (!staffId || item.staff_id.toLowerCase().includes(staffId)) &&
    (!month || item.month === month) &&
    (!dept || item.department === dept)
  );
  currentPage = 1;
  renderPayrollTable();
  updateSummary({});
}

function toggleFilters() {
  document.getElementById('filterSection')?.classList.toggle('hidden');
}

function showViewPayslipsModal() {
  // Implement or redirect
  window.location.href = 'payslip.php';
}

async function savePayroll() {

  
  if (!currentBatchId) {
    alert('No preview data to save');
    return;
  }
  
  const formData = new FormData();
  formData.append('mode', 'save');
  formData.append('batch_id', currentBatchId);
  
  const status = document.getElementById('uploadStatus');
  const saveBtn = document.getElementById('saveBtn');
  
  saveBtn.disabled = true;
  saveBtn.textContent = 'Saving...';
  status.innerHTML = '<div class="text-blue-600">Saving payroll data...</div>';
  
  try {

    const response = await fetch(API_BASE + 'upload-payroll.php', {
      method: 'POST',
      body: formData
    });
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const result = await response.json();

    
    if (result.success) {
      status.innerHTML = `<div class="text-green-600 bg-green-100 p-2 rounded">${result.message}</div>`;
      document.getElementById('previewSection').classList.add('hidden');
      loadPayrollData();
      document.getElementById('uploadForm')?.reset();
      currentBatchId = null;
    } else {
      throw new Error(result.error || 'Save failed');
    }
  } catch (err) {
    console.error('Save payroll error:', err);
    status.innerHTML = `<div class="text-red-600 bg-red-100 p-2 rounded">Save failed: ${err.message}</div>`;
    alert(`Save failed: ${err.message}. Check console for details.`);
  } finally {
    saveBtn.disabled = false;
    saveBtn.textContent = 'Save Payroll';
  }
}

async function cancelPreview() {
  if (!currentBatchId) {
    document.getElementById('previewSection').classList.add('hidden');
    document.getElementById('uploadForm')?.reset();
    return;
  }
  
  const formData = new FormData();
  formData.append('mode', 'cancel');
  formData.append('batch_id', currentBatchId);
  
  try {
    const response = await fetch(API_BASE + 'upload-payroll.php', {
      method: 'POST',
      body: formData
    });
    const result = await response.json();
    // Ignore result, just cleanup
  } catch (err) {
    console.error('Cancel error:', err);
  }
  
  document.getElementById('previewSection').classList.add('hidden');
  document.getElementById('uploadForm')?.reset();
  currentBatchId = null;
}

// Init
function checkMonthStatus() {
  const monthSelect = document.getElementById('monthSelect');
  if (monthSelect?.value) loadPayrollData(monthSelect.value);
}

