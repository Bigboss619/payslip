// upload.js - CLEAN VERSION (NO DUPLICATES)
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

// async function loadPayrollData(monthNum = null, year = null) {
//   if (!monthNum || !year) {
//     monthNum = new Date().toISOString().slice(0,7).slice(-2);
//     year = new Date().getFullYear();
//   }
  
//   const tbody = document.getElementById('payrollTableBody');
//   if (tbody) {
//     tbody.innerHTML = '<tr><td colspan="16" class="p-12 text-center"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div><p>Loading Excel payroll data...</p></td></tr>';
//   }
  
//   try {
//     const params = new URLSearchParams({
//       mode: 'get_excel',
//       month: monthNum.padStart(2, '0'),
//       year: year
//     });
    
//     const response = await fetch(`${API_BASE}upload-payroll.php?${params}`);
//     if (!response.ok) throw new Error(`HTTP ${response.status}`);
    
//     const result = await response.json();
//     const statusMsg = document.getElementById('statusMsg');
    
//     if (result.success && result.excel_data && result.excel_data.length > 0) {
//       window.currentPayrollData = result.excel_data;
//       window.filteredData = [...window.currentPayrollData];
      
//       if (window.payrollTable) {
//         window.payrollTable.populateFilterDropdowns();
//         window.payrollTable.renderExcelTable();
//       }
//       updateExcelSummary(result);
      
//       if (statusMsg) {
//         statusMsg.textContent = `✅ Payroll loaded: ${result.excel_data.length} rows`;
//         statusMsg.className = 'p-3 rounded-lg bg-green-100 text-green-800';
//         statusMsg.classList.remove('hidden');
//       }
//     } else {
//       if (statusMsg) {
//         statusMsg.textContent = `❌ No payroll found: ${result.error || 'No data'}`;
//         statusMsg.className = 'p-3 rounded-lg bg-red-100 text-red-800';
//         statusMsg.classList.remove('hidden');
//       }
//       if (tbody) {
//         tbody.innerHTML = `<tr><td colspan="16" class="p-12 text-center text-gray-500">
//           <div class="text-xl mb-2">📄 No Excel Data Found</div>
//           <div class="text-sm mt-2 text-gray-400">Upload Excel file first</div>
//         </td></tr>`;
//       }
//     }
//   } catch (err) {
//     console.error('Load error:', err);
//     if (tbody) {
//       tbody.innerHTML = `<tr><td colspan="16" class="p-12 text-center text-red-500">
//         <div class="text-xl mb-2">❌ Error Loading Excel</div>
//         <div>${err.message}</div>
//       </td></tr>`;
//     }
//   }
// }

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
    
    // ✅ DEBUG: Log raw response
    const responseText = await response.text();
    console.log('📄 Raw response:', responseText.substring(0, 200));
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }
    
    const result = JSON.parse(responseText);
    
    console.log('✅ Excel result:', result);
    
    if (result.success && result.excel_data && result.excel_data.length > 0) {
      window.currentExcelData = result.excel_data;  // ✅ Use correct global var
      window.filteredExcelData = [...window.currentExcelData];
      
      if (window.payrollTable) {
        window.payrollTable.renderExcelTable();
      }
      
      const statusMsg = document.getElementById('statusMsg');
      if (statusMsg) {
        statusMsg.textContent = `✅ Excel loaded: ${result.excel_data.length} rows`;
        statusMsg.className = 'p-3 rounded-lg bg-green-100 text-green-800';
        statusMsg.classList.remove('hidden');
      }
    } else {
      console.log('❌ No Excel data:', result);
      window.currentExcelData = [];
      window.filteredExcelData = [];
      
      if (tbody) {
        tbody.innerHTML = `<tr><td colspan="6" class="p-12 text-center text-gray-500">
          <div class="text-xl mb-2">📄 No Excel Data Found</div>
          <div class="text-sm mt-2 text-gray-400">${result.error || 'Upload Excel file first'}</div>
        </td></tr>`;
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

// async function handleFileUpload(e) {
//   e.preventDefault();
  
//   const form = e.target;
//   const formData = new FormData(form);
//   const uploadBtn = document.getElementById('uploadBtn');
//   const uploadStatus = document.getElementById('uploadStatus');
  
//   uploadBtn.disabled = true;
//   uploadBtn.textContent = 'Uploading...';
//   uploadStatus.classList.remove('hidden');
//   uploadStatus.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600 inline-block mr-2"></div>Uploading Excel file...';
  
//   try {
//     const response = await fetch(`${API_BASE}upload-payroll.php`, {
//       method: 'POST',
//       body: formData
//     });
    
//     const result = await response.json();
    
//     if (result.success) {
//       uploadStatus.innerHTML = '<span class="text-green-600">✅ File uploaded successfully!</span>';
//       showPreview(result.excel_data);
//       loadPayrollData();
//     } else {
//       uploadStatus.innerHTML = `<span class="text-red-600">❌ ${result.error || 'Upload failed'}</span>`;
//     }
//   } catch (error) {
//     uploadStatus.innerHTML = `<span class="text-red-600">❌ Network error: ${error.message}</span>`;
//   } finally {
//     uploadBtn.disabled = false;
//     uploadBtn.textContent = 'Upload & Preview';
//     setTimeout(() => uploadStatus.classList.add('hidden'), 3000);
//   }
// }
// ✅ FIXED VERSION - Replace your handleFileUpload function
async function handleFileUpload(e) {
  e.preventDefault();
  
  const form = e.target;
  const formData = new FormData(form);
  
  // ✅ CRITICAL: Ensure mode=preview is sent
  formData.append('mode', 'preview');
  
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
      body: formData  // ✅ FormData handles multipart automatically
    });
    
    const result = await response.json();
    console.log('✅ Upload result:', result);
    
    if (result.success) {
      uploadStatus.innerHTML = `<span class="text-green-600">✅ ${result.message}</span>`;
      currentBatchId = result.batch_id;
      document.getElementById('saveBtn').disabled = false;
      
      // ✅ Show preview + reload Excel table
      if (result.preview_data) {
        showPreview(result.preview_data);
      }
      
      // ✅ Reload Excel data immediately
      loadPayrollData(
        document.getElementById('monthSelectUpload')?.value ? 
        Object.keys({
          'January': '01', 'February': '02', 'March': '03', 'April': '04',
          'May': '05', 'June': '06', 'July': '07', 'August': '08',
          'September': '09', 'October': '10', 'November': '11', 'December': '12'
        })[document.getElementById('monthSelectUpload')?.value] : null,
        document.getElementById('yearSelect')?.value
      );
      
      updateExcelSummary({excel_data: result.preview_data});
      
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
    }, 2000);
  }
}
function showPreview(data) {
  previewData = data;
  const previewSection = document.getElementById('previewSection');
  const previewTable = document.getElementById('previewTable');
  const saveBtn = document.getElementById('saveBtn');
  
  if (previewTable) {
    previewTable.innerHTML = data.map((row) => `
      <tr class="hover:bg-gray-50">
        <td class="p-2 border">${row.staff_id || ''}</td>
        <td class="p-2 border">${row.name || ''}</td>
        <td class="p-2 border">${row.department || ''}</td>
        <td class="p-2 border text-right">${formatAmount(row.gross_salary)}</td>
        <td class="p-2 border text-right">${formatAmount(row.pro_rata)}</td>
        <td class="p-2 border text-center">${row.days_worked || ''}</td>
        <td class="p-2 border text-right">${formatAmount(row.basic_salary)}</td>
        <td class="p-2 border text-right">${formatAmount(row.housing)}</td>
        <td class="p-2 border text-right">${formatAmount(row.transport)}</td>
        <td class="p-2 border text-right">${formatAmount(row.medical)}</td>
        <td class="p-2 border text-right">${formatAmount(row.utility)}</td>
        <td class="p-2 border text-right">${formatAmount(row.paye)}</td>
        <td class="p-2 border text-right">${formatAmount(row.deductions)}</td>
        <td class="p-2 border text-right">${formatAmount(row.pension)}</td>
        <td class="p-2 border text-right font-bold text-green-600">${formatAmount(row.net_salary)}</td>
      </tr>
    `).join('');
  }
  
  previewSection.classList.remove('hidden');
  saveBtn.disabled = false;
}

// ✅ Helper function (different name to avoid conflict)
function formatAmount(amount) {
  return `₦${parseFloat(amount || 0).toLocaleString()}`;
}

function updateExcelSummary(result) {
  const data = window.currentPayrollData || [];
  const totalGross = data.reduce((sum, item) => sum + parseFloat(item.gross_salary || 0), 0);
  const totalNet = data.reduce((sum, item) => sum + parseFloat(item.net_salary || 0), 0);
  
  const totalEl = document.getElementById('total-employees');
  const grossEl = document.getElementById('total-gross');
  const netEl = document.getElementById('total-net');
  
  if (totalEl) totalEl.textContent = data.length;
  if (grossEl) grossEl.textContent = formatAmount(totalGross);
  if (netEl) netEl.textContent = formatAmount(totalNet);
}

function cancelPreview() {
  document.getElementById('previewSection').classList.add('hidden');
}
// ✅ ADD THIS - Replace your savePayroll function
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
      
      // ✅ Hide preview + reload everything
      document.getElementById('previewSection').classList.add('hidden');
      
      // Reload Excel + Payslip tables
      loadPayrollData();
      window.payrollTable.loadPayslipRecords();
      
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

// ✅ ADD Cancel function
async function cancelPreview() {
  if (!currentBatchId) return;
  
  const formData = new FormData();
  formData.append('mode', 'cancel');
  formData.append('batch_id', currentBatchId);
  
  try {
    const response = await fetch(`${API_BASE}upload-payroll.php`, {
      method: 'POST',
      body: formData
    });
    const result = await response.json();
    
    if (result.success) {
      document.getElementById('previewSection').classList.add('hidden');
      currentBatchId = null;
    }
  } catch (error) {
    console.error('Cancel error:', error);
  }
}

function checkPayrollStatus() {
  const month = document.getElementById('statusMonthSelect').value;
  const year = document.getElementById('statusYearSelect').value;
  if (month && year) {
    loadPayrollData(month, year);
  }
}

function showViewPayslipsModal() {
  alert('View payslips modal - implement modal logic here');
}

// ✅ EXPORT
window.uploadManager = {
  loadTotalEmployees,
  loadPayrollData,
  handleFileUpload,
  showPreview,
  updateExcelSummary,
  showViewPayslipsModal
};