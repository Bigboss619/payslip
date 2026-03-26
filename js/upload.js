// upload.js - COMPLETE FIXED VERSION
const API_BASE = '../includes/';
let previewData = [];
let currentBatchId = null;

async function loadTotalEmployees() {
  try {
    const response = await fetch(`${API_BASE}get-users.php`);
    const result = await response.json();
    if (result.success) {
      const totalEl = document.getElementById('total-employees');
      if (totalEl) totalEl.textContent = result.total_employees;
    }
  } catch (err) {
    console.error('Failed to load total employees:', err);
  }
}

async function loadPayrollData(monthNum = null, year = null) {
  const tbody = document.getElementById('excelTableBody');
  if (tbody) {
    tbody.innerHTML = '<tr><td colspan="6" class="p-12 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>Loading Excel...</td></tr>';
  }
  
  try {
    const params = new URLSearchParams({
      mode: 'get_excel',
      month: (monthNum || new Date().getMonth() + 1).toString().padStart(2, '0'),
      year: year || new Date().getFullYear()
    });
    
    console.log('📡 Fetching Excel:', `${API_BASE}upload-payroll.php?${params}`);
    
    const response = await fetch(`${API_BASE}upload-payroll.php?${params}`);
    
    const responseText = await response.text();
    console.log('📄 Raw response:', responseText.substring(0, 200));
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const result = JSON.parse(responseText);
    console.log('✅ Excel result:', result);
    
    if (result.success && result.excel_data && result.excel_data.length > 0) {
      // ✅ FIXED: Correct global variables for payroll-table.js
      window.currentExcelData = result.excel_data;
      window.filteredExcelData = [...window.currentExcelData];
      
      // ✅ CRITICAL: Force table re-render (multiple fallbacks)
      if (window.payrollTable?.renderExcelTable) {
        window.payrollTable.renderExcelTable();
      } else if (window.renderExcelTable) {
        window.renderExcelTable();
      }
      
      // ✅ Dispatch event for payroll-table.js listener
      console.log('🚀 [upload.js] Dispatching excelDataLoaded event');
      window.dispatchEvent(new CustomEvent('excelDataLoaded'));
      
      // ✅ Update summary cards
      updateExcelSummary(result);
      
      console.log('✅ TABLE UPDATED:', result.excel_data.length, 'rows from', result.file_path);
      
      const statusMsg = document.getElementById('statusMsg');
      if (statusMsg) {
        statusMsg.innerHTML = `
          <div class="flex items-center gap-2">
            <span class="text-green-600 font-semibold">✅ ${result.excel_data.length} rows</span>
            <span class="text-sm text-gray-500">${result.file_path.split('/').pop()}</span>
          </div>
        `;
        statusMsg.className = 'p-3 rounded-lg bg-green-100 text-green-800 border-l-4 border-green-500';
        statusMsg.classList.remove('hidden');
      }
      
      // Update summary cards immediately
      const totalEl = document.getElementById('total-employees');
      const grossEl = document.getElementById('total-gross');
      const netEl = document.getElementById('total-net');
      if (totalEl) totalEl.textContent = result.total_rows;
      
    } else {
      console.log('❌ No Excel data:', result);
      window.currentExcelData = [];
      window.filteredExcelData = [];
      
      // ✅ Trigger re-render even on empty data
      window.dispatchEvent(new CustomEvent('excelDataLoaded'));
      
      if (tbody) {
        tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-gray-500">
          <div class="text-xl mb-2">📄 No Excel Data Found</div>
          <div class="text-sm mt-2 text-gray-400">${result.error || 'Upload Excel file first'}</div>
        </td></tr>`;
      }
      
      const statusMsg = document.getElementById('statusMsg');
      if (statusMsg) {
        statusMsg.textContent = `❌ ${result.error || 'No Excel file found'}`;
        statusMsg.className = 'p-3 rounded-lg bg-red-100 text-red-800 border-l-4 border-red-500';
        statusMsg.classList.remove('hidden');
      }
    }
  } catch (err) {
    console.error('❌ Excel load error:', err);
    if (tbody) {
      tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-red-500">
        <div class="text-xl mb-2">❌ Error: ${err.message}</div>
      </td></tr>`;
    }
  }
}

async function handleFileUpload(e) {
  e.preventDefault();
  
  const form = e.target;
  const formData = new FormData(form);
  formData.append('mode', 'preview'); // Ensure mode is sent
  
  const uploadBtn = document.getElementById('uploadBtn');
  const uploadStatus = document.getElementById('uploadStatus');
  
  uploadBtn.disabled = true;
  uploadBtn.textContent = 'Uploading...';
  uploadStatus.classList.remove('hidden');
  uploadStatus.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 inline-block mr-2"></div>Uploading...';
  
  try {
    console.log('📤 Uploading to:', `${API_BASE}upload-payroll.php`);
    
    const response = await fetch(`${API_BASE}upload-payroll.php`, {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    console.log('✅ Upload result:', result);
    
    if (result.success) {
      uploadStatus.innerHTML = `<span class="text-green-600 font-semibold">✅ ${result.message}</span>`;
      currentBatchId = result.batch_id;
      document.getElementById('saveBtn').disabled = false;
      
      if (result.preview_data) {
        showPreview(result.preview_data);
      }
      
      // Reload Excel table with uploaded data
      const monthSelect = document.getElementById('monthSelectUpload');
      const yearSelect = document.getElementById('yearSelect');
      const monthNum = monthSelect?.value ? 
        Object.keys({
          'January': '01', 'February': '02', 'March': '03', 'April': '04',
          'May': '05', 'June': '06', 'July': '07', 'August': '08',
          'September': '09', 'October': '10', 'November': '11', 'December': '12'
        })[monthSelect.value] : null;
      
      loadPayrollData(monthNum, yearSelect?.value);
      
    } else {
      uploadStatus.innerHTML = `<span class="text-red-600">❌ ${result.error}</span>`;
    }
  } catch (error) {
    console.error('❌ Upload error:', error);
    uploadStatus.innerHTML = `<span class="text-red-600">❌ ${error.message}</span>`;
  } finally {
    setTimeout(() => {
      uploadBtn.disabled = false;
      uploadBtn.textContent = 'Upload & Preview';
      uploadStatus.classList.add('hidden');
    }, 3000);
  }
}

function showPreview(data) {
  previewData = data;
  const previewSection = document.getElementById('previewSection');
  const previewTable = document.getElementById('previewTable');
  const saveBtn = document.getElementById('saveBtn');
  
  if (previewTable) {
    previewTable.innerHTML = data.map((row, index) => `
      <tr class="hover:bg-gray-50 border-b">
        <td class="p-2 border font-mono text-sm">${index + 1}</td>
        <td class="p-2 border font-medium">${row.staff_id || ''}</td>
        <td class="p-2 border">${row.name || ''}</td>
        <td class="p-2 border">${row.department || ''}</td>
        <td class="p-2 border text-right font-semibold">${formatAmount(row.gross_salary)}</td>
        <td class="p-2 border text-right">${formatAmount(row.pro_rata)}</td>
        <td class="p-2 border text-center">${row.days_worked || ''}</td>
        <td class="p-2 border text-right">${formatAmount(row.basic_salary)}</td>
        <td class="p-2 border text-right">${formatAmount(row.housing)}</td>
        <td class="p-2 border text-right">${formatAmount(row.transport)}</td>
        <td class="p-2 border text-right">${formatAmount(row.medical)}</td>
        <td class="p-2 border text-right">${formatAmount(row.utility)}</td>
        <td class="p-2 border text-right text-red-600">${formatAmount(row.paye)}</td>
        <td class="p-2 border text-right">${formatAmount(row.deductions)}</td>
        <td class="p-2 border text-right">${formatAmount(row.pension)}</td>
        <td class="p-2 border text-right font-bold text-green-600">${formatAmount(row.net_salary)}</td>
      </tr>
    `).join('');
  }
  
  previewSection.classList.remove('hidden');
  if (saveBtn) saveBtn.disabled = false;
}

function formatAmount(amount) {
  return `₦${parseFloat(amount || 0).toLocaleString()}`;
}

// ✅ FIXED: Uses correct window.currentExcelData
function updateExcelSummary(result) {
  const data = window.currentExcelData || [];  // ✅ FIXED: was window.currentPayrollData
  const totalGross = data.reduce((sum, item) => sum + parseFloat(item.gross_salary || 0), 0);
  const totalNet = data.reduce((sum, item) => sum + parseFloat(item.net_salary || 0), 0);
  
  const totalEl = document.getElementById('total-employees');
  const grossEl = document.getElementById('total-gross');
  const netEl = document.getElementById('total-net');
  
  if (totalEl) totalEl.textContent = data.length;
  if (grossEl) grossEl.textContent = formatAmount(totalGross);
  if (netEl) netEl.textContent = formatAmount(totalNet);
}

async function savePayroll() {
  if (!currentBatchId) {
    alert('No preview data to save!');
    return;
  }
  
  const saveBtn = document.getElementById('saveBtn');
  saveBtn.disabled = true;
  saveBtn.textContent = 'Saving...';
  
  const formData = new FormData();
  formData.append('mode', 'save');
  formData.append('batch_id', currentBatchId);
  
  try {
    const response = await fetch(`${API_BASE}upload-payroll.php`, {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      alert(`✅ Success! ${result.message}`);
      document.getElementById('previewSection').classList.add('hidden');
      currentBatchId = null;
      
      // Reload tables
      loadPayrollData();
      if (window.payrollTable?.loadPayslipRecords) {
        window.payrollTable.loadPayslipRecords();
      }
    } else {
      alert(`❌ Save failed: ${result.error}`);
    }
  } catch (error) {
    alert(`❌ Network error: ${error.message}`);
  } finally {
    saveBtn.disabled = false;
    saveBtn.textContent = 'Save Payroll';
  }
}

async function cancelPreview() {
  if (!currentBatchId) return;
  
  const formData = new FormData();
  formData.append('mode', 'cancel');
  formData.append('batch_id', currentBatchId);
  
  try {
    await fetch(`${API_BASE}upload-payroll.php`, {
      method: 'POST',
      body: formData
    });
    document.getElementById('previewSection').classList.add('hidden');
    currentBatchId = null;
  } catch (error) {
    console.error('Cancel error:', error);
  }
}

function checkPayrollStatus() {
  const month = document.getElementById('statusMonthSelect')?.value;
  const year = document.getElementById('statusYearSelect')?.value;
  if (month && year) {
    loadPayrollData(month, year);
  }
}

// ✅ EXPORTS
window.loadExcelPreview = function() {
  window.uploadManager.loadPayrollData(
    document.getElementById('statusMonthSelect')?.value,
    document.getElementById('statusYearSelect')?.value
  );
};

window.uploadManager = {
  loadTotalEmployees,
  loadPayrollData,
  handleFileUpload,
  showPreview,
  updateExcelSummary,
  savePayroll,
  cancelPreview,
  checkPayrollStatus
};

// ✅ Ensure globals exist for payroll-table.js
window.currentExcelData = window.currentExcelData || [];
window.filteredExcelData = window.filteredExcelData || [];
