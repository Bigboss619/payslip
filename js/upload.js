const API_BASE = '../includes/';
let currentPayrollData = [];
let filteredData = [];
let currentPage = 1;
const pageSize = 10;
let previewData = [];
let selectedMonth = '';

const formatCurrency = (amount) => `₦${parseFloat(amount || 0).toLocaleString()}`;

document.addEventListener('DOMContentLoaded', function() {
  loadPayrollData();
  setupEventListeners();
  populateMonthSelectors();
});

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

async function loadPayrollData(month = '') {
  showLoading(true);
  try {
    const params = new URLSearchParams({ limit: pageSize, offset: 0 });
    if (month) params.append('month', month);
    const response = await fetch(`${API_BASE}get-payroll.php?${params}`);
    const result = await response.json();
    
    if (result.success) {
      currentPayrollData = result.data;
      filteredData = [...currentPayrollData];
      renderPayrollTable();
      updateSummary(result.summary);
      populatePreviousMonths(result.months);
      if (month) selectedMonth = month;
    } else {
      showError(result.error || 'Failed to load data');
    }
  } catch (err) {
    showError('Network error: ' + err.message);
  }
  showLoading(false);
}

async function handleFileUpload(e) {
  e.preventDefault();
  const formData = new FormData(e.target);
  const uploadBtn = document.getElementById('uploadBtn');
  const status = document.getElementById('uploadStatus');
  
  uploadBtn.disabled = true;
  uploadBtn.textContent = 'Uploading...';
  status.classList.remove('hidden');
  status.innerHTML = '<div class="text-blue-600">Processing file...</div>';
  
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
      status.innerHTML = `<div class="text-green-600">${result.message}</div>`;
      showPreview(result);
      loadPayrollData(); // Refresh table
    } else {
      status.innerHTML = `<div class="text-red-600 bg-red-100 p-2 rounded">${result.error}</div>`;
    }
  } catch (err) {
    status.innerHTML = `<div class="text-red-600">Error: ${err.message}</div>`;
  }
  
  uploadBtn.disabled = false;
  uploadBtn.textContent = 'Upload & Preview';
}

function showPreview(result) {
  previewData = result.preview_data || result.preview || []; 
  const previewSection = document.getElementById('previewSection');
  const previewTable = document.getElementById('previewTable');
  
  previewTable.innerHTML = previewData.slice(0, 10).map(item => `
    <tr>
      <td class="p-2 border">${item.staff_id || ''}</td>
      <td class="p-2 border">${item.name}</td>
      <td class="p-2 border">${item.department}</td>
      <td class="p-2 border">${formatCurrency(item.gross)}</td>
      <td class="p-2 border">${item.days_worked || ''}</td>
      <td class="p-2 border">${formatCurrency(item.net)}</td>
    </tr>
  `).join('') || '<tr><td colspan="6" class="p-4 text-center">No preview data</td></tr>';
  
  previewSection.classList.remove('hidden');
  document.getElementById('saveBtn').disabled = previewData.length === 0;
}

function savePayroll() {
  // Backend handles save in upload endpoint; call again or separate
  alert('Saved! (Backend handles on upload)');
  cancelPreview();
}

function cancelPreview() {
  document.getElementById('previewSection').classList.add('hidden');
  document.getElementById('uploadForm').reset();
  document.getElementById('fileName').textContent = '';
  previewData = [];
}

function populatePreviousMonths(months) {
  const container = document.getElementById('previousMonths');
  if (container) {
    container.innerHTML = months.map(m => 
      `<span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm cursor-pointer hover:bg-blue-200" onclick="loadPayrollData('${m}')">${m}</span>`
    ).join('');
  }
}

function populateMonthSelectors() {
  // Populated by loadPayrollData
}

function checkMonthStatus() {
  const month = document.getElementById('monthSelect').value;
  if (!month) return alert('Select month');
  loadPayrollData(month);
}

function applyFilters() {
  const name = document.getElementById('nameFilter')?.value.toLowerCase() || '';
  const dept = document.getElementById('deptFilter')?.value || '';
  // Apply month etc.
  
  filteredData = currentPayrollData.filter(item => 
    (!name || item.name.toLowerCase().includes(name)) &&
    (!dept || item.department === dept)
  );
  currentPage = 1;
  renderPayrollTable();
}

function renderPayrollTable() {
  const data = filteredData.length ? filteredData : currentPayrollData;
  const start = (currentPage - 1) * pageSize;
  const end = start + pageSize;
  const paginated = data.slice(start, end);
  
  const tbody = document.getElementById('payrollTableBody');
  if (paginated.length === 0) {
    tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-gray-500">No data</td></tr>';
  } else {
    tbody.innerHTML = paginated.map(item => `
      <tr class="border-b hover:bg-gray-50">
        <td class="p-2">${item.name}</td>
        <td class="p-2">${item.department}</td>
        <td class="p-2">${formatCurrency(item.gross_salary)}</td>
        <td class="p-2 font-semibold">${formatCurrency(item.net_salary)}</td>
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
    <div class="flex items-center justify-between mt-4 px-4 py-3 bg-gray-50 rounded-lg">
      <div class="text-sm text-gray-700">Showing ${Math.min((currentPage-1)*pageSize +1, total)}-${Math.min(currentPage*pageSize, total)} of ${total}</div>
      <div class="flex space-x-2">
        <button onclick="changePage('prev')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 ${currentPage===1 ? 'disabled:bg-gray-400' : ''}" ${currentPage===1 ? 'disabled' : ''}>Previous</button>
        <span class="px-3 py-2 text-sm font-medium">${currentPage} / ${totalPages}</span>
        <button onclick="changePage('next')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 ${currentPage===totalPages ? 'disabled:bg-gray-400' : ''}" ${currentPage===totalPages ? 'disabled' : ''}>Next</button>
      </div>
    </div>
  `;
}

function changePage(dir) {
  const totalPages = Math.ceil((filteredData.length || currentPayrollData.length) / pageSize);
  if (dir === 'prev' && currentPage > 1) currentPage--;
  if (dir === 'next' && currentPage < totalPages) currentPage++;
  renderPayrollTable();
}

function updateSummary(summary) {
  document.getElementById('total-employees').textContent = summary.total_employees || 0;
  document.getElementById('total-gross').textContent = formatCurrency(summary.total_gross);
  document.getElementById('total-net').textContent = formatCurrency(summary.total_net);
}

function toggleFilters() {
  document.getElementById('filterSection').classList.toggle('hidden');
}

// View Payslips (simplified)
function showViewPayslipsModal() {
  alert('View Payslips modal - integrate with payslip-view.php');
}

// Utils
function showLoading(show) {
  // Add spinner logic
}
function showError(msg) {
  // Toast error
}

// Backend preview: Modify upload-payroll.php to return preview_data array in success JSON for full integration

