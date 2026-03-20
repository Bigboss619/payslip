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

const fileInput = document.getElementById("fileInput");
const fileName = document.getElementById("fileName");
const previewSection = document.getElementById("previewSection");
const previewTable = document.getElementById("previewTable");

const formatCurrency = (amount) => `₦${amount.toLocaleString()}`;

const formatMonthYear = (month, year) => `${month} ${year}`;

function renderPayrollTable(data = currentPayrollData) {
  const tableBody = document.getElementById('payrollTableBody');
  if (!tableBody) return;

  if (data.length === 0) {
    tableBody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-gray-500">No data found</td></tr>';
    return;
  }

  tableBody.innerHTML = data.map(item => `
    <tr class="border-b hover:bg-gray-50">
      <td class="p-2">${item.name}</td>
      <td class="p-2">${item.department}</td>
      <td class="p-2">${formatCurrency(item.grossSalary)}</td>
      <td class="p-2 font-semibold">${formatCurrency(item.netSalary)}</td>
    </tr>
  `).join('');
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

  renderPayrollTable();
  updateSummary();
}

function handleMonthSelect() {
  const monthSelect = document.getElementById('monthSelect');
  if (monthSelect) {
    selectedMonthYear = monthSelect.value;
    if (selectedMonthYear) {
      currentPayrollData = mockPayrollData.filter(item => item.month === selectedMonthYear.split(' ')[0]);
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

// Init
document.addEventListener('DOMContentLoaded', () => {
  populateSelectors();
  populatePreviousMonths();
  renderPayrollTable();
  updateSummary();

  // Event listeners for filters (debounced)
  const nameFilter = document.getElementById('nameFilter');
  const staffIdFilter = document.getElementById('staffIdFilter');
  const monthSelect = document.getElementById('monthSelect');
  const deptSelect = document.getElementById('deptSelect');
  const filterBtn = document.getElementById('filterBtn');

  if (nameFilter) nameFilter.addEventListener('input', applyFilters);
  if (staffIdFilter) staffIdFilter.addEventListener('input', applyFilters);
  if (monthSelect) monthSelect.addEventListener('change', handleMonthSelect);
  if (deptSelect) deptSelect.addEventListener('change', applyFilters);
  if (filterBtn) filterBtn.addEventListener('click', applyFilters);
});
