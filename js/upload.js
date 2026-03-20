const mockPayrollData = [
  { staffId: 'EMP001', name: 'Emmanuel Ugochukwu', department: 'IT', grossSalary: 300000, netSalary: 250000, month: 'January', year: 2026 },
  { staffId: 'EMP002', name: 'John Doe', department: 'HR', grossSalary: 250000, netSalary: 210000, month: 'January', year: 2026 },
  { staffId: 'EMP003', name: 'Jane Smith', department: 'Finance', grossSalary: 280000, netSalary: 235000, month: 'January', year: 2026 },
  { staffId: 'EMP004', name: 'Mike Johnson', department: 'Sales', grossSalary: 220000, netSalary: 185000, month: 'January', year: 2026 },
  { staffId: 'EMP001', name: 'Emmanuel Ugochukwu', department: 'IT', grossSalary: 300000, netSalary: 245000, month: 'December', year: 2025 },
  { staffId: 'EMP002', name: 'John Doe', department: 'HR', grossSalary: 250000, netSalary: 205000, month: 'December', year: 2025 },
  { staffId: 'EMP005', name: 'Sarah Wilson', department: 'IT', grossSalary: 320000, netSalary: 270000, month: 'November', year: 2025 },
  { staffId: 'EMP006', name: 'David Brown', department: 'Finance', grossSalary: 260000, netSalary: 218000, month: 'November', year: 2025 },
  { staffId: 'EMP007', name: 'Lisa Davis', department: 'Sales', grossSalary: 240000, netSalary: 200000, month: 'October', year: 2025 },
  { staffId: 'EMP008', name: 'Tom Wilson', department: 'HR', grossSalary: 270000, netSalary: 225000, month: 'October', year: 2025 },
  { staffId: 'EMP009', name: 'Anna Taylor', department: 'IT', grossSalary: 310000, netSalary: 260000, month: 'September', year: 2025 },
  { staffId: 'EMP010', name: 'Robert Anderson', department: 'Finance', grossSalary: 290000, netSalary: 242000, month: 'September', year: 2025 },
  { staffId: 'EMP001', name: 'Emmanuel Ugochukwu', department: 'IT', grossSalary: 295000, netSalary: 243000, month: 'August', year: 2025 },
  { staffId: 'EMP002', name: 'John Doe', department: 'HR', grossSalary: 255000, netSalary: 212000, month: 'August', year: 2025 },
  { staffId: 'EMP011', name: 'Emily Clark', department: 'Sales', grossSalary: 230000, netSalary: 192000, month: 'July', year: 2025 },
  { staffId: 'EMP012', name: 'James Lee', department: 'Finance', grossSalary: 275000, netSalary: 230000, month: 'July', year: 2025 },
  { staffId: 'EMP013', name: 'Maria Garcia', department: 'HR', grossSalary: 260000, netSalary: 218000, month: 'June', year: 2025 },
  { staffId: 'EMP014', name: 'Chris Evans', department: 'IT', grossSalary: 340000, netSalary: 285000, month: 'June', year: 2025 },
  { staffId: 'EMP015', name: 'Patricia White', department: 'Sales', grossSalary: 245000, netSalary: 205000, month: 'May', year: 2025 },
  { staffId: 'EMP016', name: 'Daniel Harris', department: 'Finance', grossSalary: 285000, netSalary: 238000, month: 'May', year: 2025 },
  { staffId: 'EMP017', name: 'Jessica Martinez', department: 'HR', grossSalary: 265000, netSalary: 222000, month: 'April', year: 2025 },
  { staffId: 'EMP018', name: 'Kevin Thomas', department: 'IT', grossSalary: 315000, netSalary: 265000, month: 'April', year: 2025 },
  { staffId: 'EMP019', name: 'Amanda Lewis', department: 'Sales', grossSalary: 235000, netSalary: 197000, month: 'March', year: 2025 },
  { staffId: 'EMP020', name: 'Steven Walker', department: 'Finance', grossSalary: 300000, netSalary: 250000, month: 'March', year: 2025 },
  { staffId: 'EMP001', name: 'Emmanuel Ugochukwu', department: 'IT', grossSalary: 310000, netSalary: 261000, month: 'January', year: 2025 }
];

const mockUploadedMonths = new Set(['January 2026', 'December 2025', 'January 2025']);

let currentPayrollData = [...mockPayrollData];
let selectedMonthYear = '';
let currentPage = 1;
const pageSize = 10;


const fileInput = document.getElementById("fileInput");
const fileName = document.getElementById("fileName");
const previewSection = document.getElementById("previewSection");
const previewTable = document.getElementById("previewTable");

const formatCurrency = (amount) => `₦${amount.toLocaleString()}`;

const formatMonthYear = (month, year) => `${month} ${year}`;

function renderPayrollTable(data = currentPayrollData) {
  const tableBody = document.getElementById('payrollTableBody');
  if (!tableBody) return;

  const pageSize = 10;
  const start = (currentPage - 1) * pageSize;
  const end = start + pageSize;
  const paginatedData = data.slice(start, end);

  if (paginatedData.length === 0) {
    tableBody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-gray-500">No data found</td></tr>';
  } else {
    tableBody.innerHTML = paginatedData.map(item => `
      <tr class="border-b hover:bg-gray-50">
        <td class="p-2">${item.name}</td>
        <td class="p-2">${item.department}</td>
        <td class="p-2">${formatCurrency(item.grossSalary)}</td>
        <td class="p-2 font-semibold">${formatCurrency(item.netSalary)}</td>
      </tr>
    `).join('');
  }

  const paginationDiv = document.getElementById('payrollPagination');
  if (paginationDiv) {
    const totalPages = Math.ceil(data.length / pageSize);
    paginationDiv.innerHTML = `
      <div class="flex items-center justify-between mt-4 px-4 py-3 bg-gray-50 rounded-lg">
        <div class="text-sm text-gray-700">
          Showing ${start + 1} to ${Math.min(end, data.length)} of ${data.length} entries
        </div>
        <div class="flex space-x-2">
          <button onclick="handlePayrollPagination('prev')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed" ${currentPage === 1 ? 'disabled' : ''}>
            Previous
          </button>
          <span class="text-sm font-medium px-3 py-2">${currentPage} of ${totalPages}</span>
          <button onclick="handlePayrollPagination('next')" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed" ${currentPage === totalPages ? 'disabled' : ''}>
            Next
          </button>
        </div>
      </div>
    `;
  }
}

function updateSummary() {
  const data = currentPayrollData;
  const totalEmployees = data.length;
  const totalGross = data.reduce((sum, item) => sum + item.grossSalary, 0);
  const totalNet = data.reduce((sum, item) => sum + item.netSalary, 0);

  document.getElementById('total-employees') && (document.getElementById('total-employees').textContent = totalEmployees);
  document.getElementById('total-gross') && (document.getElementById('total-gross').textContent = formatCurrency(totalGross));
  document.getElementById('total-net') && (document.getElementById('total-net').textContent = formatCurrency(totalNet));
}

function populatePreviousMonths() {
  const container = document.getElementById('previousMonths');
  if (!container) return;

  container.innerHTML = Array.from(mockUploadedMonths).map(month => 
    `<span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm mr-2 mb-2 cursor-pointer hover:bg-blue-200" onclick="selectPreviousMonth('${month}')">${month}</span>`
  ).join('');
}

function selectPreviousMonth(monthYear) {
  selectedMonthYear = monthYear;
  document.getElementById('monthSelect') && (document.getElementById('monthSelect').value = monthYear);
  currentPayrollData = mockPayrollData.filter(item => formatMonthYear(item.month, item.year) === monthYear);
  currentPage = 1;
  renderPayrollTable();
  updateSummary();
  alert(`Switched to ${monthYear} data`);
}

function populateSelectors() {
  const months = [...new Set(mockPayrollData.map(d => d.month))].sort();
  const depts = [...new Set(mockPayrollData.map(d => d.department))].sort();
  const monthSelect = document.getElementById('monthSelect');
  const deptSelect = document.getElementById('deptSelect');

  if (monthSelect) {
    monthSelect.innerHTML = '<option value="">All Months</option>' + 
      months.map(m => `<option value="${m}">${m}</option>`).join('');
  }
  if (deptSelect) {
    deptSelect.innerHTML = '<option value="">All Departments</option>' + 
      depts.map(d => `<option value="${d}">${d}</option>`).join('');
  }
}

function checkMonthUploaded(monthYear) {
  return mockUploadedMonths.has(monthYear);
}

function applyFilters() {
  const nameSearch = document.getElementById('nameFilter')?.value.toLowerCase() || '';
  const staffIdSearch = document.getElementById('staffIdFilter')?.value.toLowerCase() || '';
  const monthFilter = document.getElementById('monthSelect')?.value || '';
  const deptFilter = document.getElementById('deptSelect')?.value || '';

  currentPayrollData = mockPayrollData.filter(item => {
    const monthYear = formatMonthYear(item.month, item.year);
    return (!nameSearch || item.name.toLowerCase().includes(nameSearch)) &&
           (!staffIdSearch || item.staffId.toLowerCase().includes(staffIdSearch)) &&
           (!monthFilter || item.month === monthFilter) &&
           (!deptFilter || item.department === deptFilter);
  });

  currentPage = 1;
  renderPayrollTable();
  updateSummary();
}

function handleMonthSelect() {
  const monthSelect = document.getElementById('monthSelect');
  if (monthSelect) {
    selectedMonthYear = monthSelect.value;
    if (selectedMonthYear) {
      currentPayrollData = mockPayrollData.filter(item => item.month === selectedMonthYear.split(' ')[0]);
      currentPage = 1;
      renderPayrollTable();
      updateSummary();
    }
  }
}

function handleCheckUpload() {
  if (!selectedMonthYear) {
    alert('Please select a month first');
    return;
  }
  if (checkMonthUploaded(selectedMonthYear)) {
    alert(`${selectedMonthYear} already uploaded!`);
  } else {
    alert(`Ready to upload for ${selectedMonthYear} (simulated)`);
  }
}

function handleUpload() {
  const file = fileInput.files[0];

  if (!file) {
    alert("Please select a file");
    return;
  }

  if (!selectedMonthYear) {
    alert('Please select a month first');
    return;
  }

  if (!checkMonthUploaded(selectedMonthYear)) {
    const confirmUpload = confirm(`Month ${selectedMonthYear} not previously uploaded. Proceed?`);
    if (!confirmUpload) return;

    // Simulate upload
    mockUploadedMonths.add(selectedMonthYear);
    populatePreviousMonths();

    // Update preview with filtered data
    const monthPreviewData = mockPayrollData.filter(item => formatMonthYear(item.month, item.year) === selectedMonthYear);
    previewTable.innerHTML = monthPreviewData.slice(0, 5).map(item => `
      <tr>
        <td class="p-2 border">${item.name}</td>
        <td class="p-2 border">${item.department}</td>
        <td class="p-2 border">${formatCurrency(item.grossSalary)}</td>
        <td class="p-2 border">22</td>
        <td class="p-2 border">${formatCurrency(item.grossSalary * 0.4)}</td>
        <td class="p-2 border">${formatCurrency(item.grossSalary * 0.25)}</td>
        <td class="p-2 border">${formatCurrency(item.grossSalary * 0.2)}</td>
        <td class="p-2 border">${formatCurrency(item.netSalary)}</td>
      </tr>
    `).join('');

    fileName.innerText = `Selected: ${file.name} for ${selectedMonthYear}`;
    previewSection.classList.remove("hidden");
    alert(`Payroll uploaded successfully for ${selectedMonthYear}!`);
  } else {
    alert(`${selectedMonthYear} already uploaded.`);
  }
}

function handlePayrollPagination(direction) {
  const totalPages = Math.ceil(currentPayrollData.length / pageSize);
  if (direction === 'prev' && currentPage > 1) {
    currentPage--;
  } else if (direction === 'next' && currentPage < totalPages) {
    currentPage++;
  }
  renderPayrollTable();
}

// View Payslips Modal and Handler
function showViewPayslipsModal() {
  const modal = document.createElement('div');
  modal.id = 'view-slips-modal';
  modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
  modal.innerHTML = `
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-6">View Payslip</h3>
        <div class="space-y-4 mb-6">
          <input id="modal-staff-name" placeholder="Staff Name" class="w-full border p-3 rounded-lg">
          <input id="modal-staff-id" placeholder="Staff ID (EMP001)" class="w-full border p-3 rounded-lg">
          <select id="modal-month" class="w-full border p-3 rounded-lg">
            <option value="">Select Month</option>
          </select>
          <select id="modal-dept" class="w-full border p-3 rounded-lg">
            <option value="">Select Department</option>
          </select>
        </div>
        <div class="flex justify-end space-x-3">
          <button onclick="closeViewSlipsModal()" class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
          <button id="view-slip-btn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-gray-400" disabled>View Payslip</button>
        </div>
      </div>
    </div>
  `;
  document.body.appendChild(modal);
  
  // Populate dropdowns
  const months = [...new Set(mockPayrollData.map(d => formatMonthYear(d.month, d.year)))];
  document.getElementById('modal-month').innerHTML += months.map(m => `<option value="${m}">${m}</option>`).join('');
  
  const depts = [...new Set(mockPayrollData.map(d => d.department))];
  document.getElementById('modal-dept').innerHTML += depts.map(d => `<option value="${d}">${d}</option>`).join('');
  
  // Filter and enable button
  const inputs = ['modal-staff-name', 'modal-staff-id', 'modal-month', 'modal-dept'];
  inputs.forEach(id => {
    document.getElementById(id).addEventListener('input', updateViewButton);
  });
  
  document.getElementById('view-slip-btn').addEventListener('click', handleViewPayslip);
}

function closeViewSlipsModal() {
  const modal = document.getElementById('view-slips-modal');
  if (modal) modal.remove();
}

function updateViewButton() {
  const name = document.getElementById('modal-staff-name').value.toLowerCase();
  const id = document.getElementById('modal-staff-id').value.toLowerCase();
  const month = document.getElementById('modal-month').value;
  const dept = document.getElementById('modal-dept').value;
  
  const matches = mockPayrollData.filter(emp => 
    (!name || emp.name.toLowerCase().includes(name)) &&
    (!id || emp.staffId.toLowerCase().includes(id)) &&
    (!month || formatMonthYear(emp.month, emp.year) === month) &&
    (!dept || emp.department === dept)
  );
  
  const btn = document.getElementById('view-slip-btn');
  if (matches.length === 1) {
    btn.disabled = false;
    btn.textContent = 'View ' + matches[0].name + "'s Payslip";
  } else {
    btn.disabled = true;
btn.textContent = matches.length === 0 ? 'No match' : `${matches.length} matches`;
  }
}

function handleViewPayslip() {
  const name = document.getElementById('modal-staff-name').value.toLowerCase();
  const id = document.getElementById('modal-staff-id').value.toLowerCase();
  const month = document.getElementById('modal-month').value;
  const dept = document.getElementById('modal-dept').value;
  
  const match = mockPayrollData.find(emp => 
    emp.name.toLowerCase().includes(name) &&
    emp.staffId.toLowerCase().includes(id) &&
    formatMonthYear(emp.month, emp.year) === month &&
    emp.department === dept
  );
  
  if (match) {
    const payslipData = {
      ...match,
      date: new Date().toLocaleDateString(),
      status: 'Paid',
      deductions: match.netSalary * 0.15, // approximate
      position: 'Staff' // default
    };
    
    const params = new URLSearchParams({ payslipData: JSON.stringify(payslipData) });
window.open(`pages/payslip-view.html?${params.toString()}`, '_blank');
    closeViewSlipsModal();
  }
}

// Event listener for View Payslips button
document.addEventListener('DOMContentLoaded', () => {
  populateSelectors();
  populatePreviousMonths();
  currentPage = 1;
  renderPayrollTable();
  updateSummary();

  // Event listeners for filters (debounced)
  const nameFilter = document.getElementById('nameFilter');
  const staffIdFilter = document.getElementById('staffIdFilter');
  const monthSelect = document.getElementById('monthSelect');
  const deptSelect = document.getElementById('deptSelect');
  const filterBtn = document.getElementById('filterBtn');

  if (nameFilter) nameFilter.addEventListener('input', () => { currentPage = 1; applyFilters(); });
  if (staffIdFilter) staffIdFilter.addEventListener('input', () => { currentPage = 1; applyFilters(); });
  if (monthSelect) monthSelect.addEventListener('change', () => { currentPage = 1; handleMonthSelect(); });
  if (deptSelect) deptSelect.addEventListener('change', () => { currentPage = 1; applyFilters(); });
  if (filterBtn) filterBtn.addEventListener('click', () => { currentPage = 1; applyFilters(); });
});