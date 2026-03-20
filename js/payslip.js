const mockData = [
  { 
    id: 1, 
    month: "January", 
    year: 2026, 
    grossSalary: 300000, 
    deductions: 50000, 
    netSalary: 250000, 
    status: "Paid", 
    date: "2026-01-31",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 2, 
    month: "December", 
    year: 2025, 
    grossSalary: 300000, 
    deductions: 55000, 
    netSalary: 245000, 
    status: "Paid", 
    date: "2025-12-31",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 3, 
    month: "November", 
    year: 2025, 
    grossSalary: 310000, 
    deductions: 48000, 
    netSalary: 262000, 
    status: "Paid", 
    date: "2025-11-30",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 4, 
    month: "October", 
    year: 2025, 
    grossSalary: 295000, 
    deductions: 52000, 
    netSalary: 243000, 
    status: "Paid", 
    date: "2025-10-31",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 5, 
    month: "September", 
    year: 2025, 
    grossSalary: 305000, 
    deductions: 45000, 
    netSalary: 260000, 
    status: "Pending", 
    date: "2025-09-30",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 6, 
    month: "August", 
    year: 2025, 
    grossSalary: 320000, 
    deductions: 60000, 
    netSalary: 260000, 
    status: "Paid", 
    date: "2025-08-31",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 7, 
    month: "July", 
    year: 2025, 
    grossSalary: 290000, 
    deductions: 47000, 
    netSalary: 243000, 
    status: "Paid", 
    date: "2025-07-31",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 8, 
    month: "June", 
    year: 2025, 
    grossSalary: 315000, 
    deductions: 51000, 
    netSalary: 264000, 
    status: "Paid", 
    date: "2025-06-30",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 9, 
    month: "May", 
    year: 2025, 
    grossSalary: 280000, 
    deductions: 43000, 
    netSalary: 237000, 
    status: "Paid", 
    date: "2025-05-31",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 10, 
    month: "April", 
    year: 2025, 
    grossSalary: 300000, 
    deductions: 50000, 
    netSalary: 250000, 
    status: "Paid", 
    date: "2025-04-30",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 11, 
    month: "March", 
    year: 2025, 
    grossSalary: 325000, 
    deductions: 55000, 
    netSalary: 270000, 
    status: "Paid", 
    date: "2025-03-31",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 12, 
    month: "February", 
    year: 2025, 
    grossSalary: 285000, 
    deductions: 46000, 
    netSalary: 239000, 
    status: "Paid", 
    date: "2025-02-28",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 13, 
    month: "January", 
    year: 2025, 
    grossSalary: 310000, 
    deductions: 49000, 
    netSalary: 261000, 
    status: "Paid", 
    date: "2025-01-31",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 14, 
    month: "December", 
    year: 2024, 
    grossSalary: 295000, 
    deductions: 52000, 
    netSalary: 243000, 
    status: "Paid", 
    date: "2024-12-31",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 15, 
    month: "November", 
    year: 2024, 
    grossSalary: 305000, 
    deductions: 48000, 
    netSalary: 257000, 
    status: "Paid", 
    date: "2024-11-30",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 16, 
    month: "October", 
    year: 2024, 
    grossSalary: 290000, 
    deductions: 45000, 
    netSalary: 245000, 
    status: "Paid", 
    date: "2024-10-31",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 17, 
    month: "September", 
    year: 2024, 
    grossSalary: 320000, 
    deductions: 58000, 
    netSalary: 262000, 
    status: "Paid", 
    date: "2024-09-30",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 18, 
    month: "August", 
    year: 2024, 
    grossSalary: 275000, 
    deductions: 42000, 
    netSalary: 233000, 
    status: "Paid", 
    date: "2024-08-31",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 19, 
    month: "July", 
    year: 2024, 
    grossSalary: 335000, 
    deductions: 61000, 
    netSalary: 274000, 
    status: "Paid", 
    date: "2024-07-31",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  },
  { 
    id: 20, 
    month: "June", 
    year: 2024, 
    grossSalary: 300000, 
    deductions: 50000, 
    netSalary: 250000, 
    status: "Paid", 
    date: "2024-06-30",
    employeeName: "Emmanuel Ugochukwu",
    employeeId: "EMP001",
    department: "IT",
    position: "Software Engineer"
  }
];

let currentData = [...mockData];
let currentPage = 1;
const pageSize = 10;
let currentSort = { column: 'date', direction: 'desc' };

// DOM elements
const tableBody = document.querySelector('#payslip-table tbody');
const emptyState = document.querySelector('#empty-state');
const pagination = document.querySelector('#pagination');
const searchInput = document.querySelector('#search');
const monthSelect = document.querySelector('#month-filter');
const yearSelect = document.querySelector('#year-filter');

// Status colors
const statusColors = {
  Paid: 'bg-green-100 text-green-700',
  Pending: 'bg-yellow-100 text-yellow-700'
};

// Format currency
const formatCurrency = (amount) => `₦${amount.toLocaleString()}`;

// Format month/year
const formatMonthYear = (month, year) => `${month} ${year}`;

// Debounce function
const debounce = (func, delay) => {
  let timeoutId;
  return (...args) => {
    clearTimeout(timeoutId);
    timeoutId = setTimeout(() => func.apply(null, args), delay);
  };
};

function renderTable(data = currentData) {
  const start = (currentPage - 1) * pageSize;
  const end = start + pageSize;
  const paginatedData = data.slice(start, end);

  if (paginatedData.length === 0) {
    tableBody.innerHTML = '';
    tableBody.closest('table').style.display = 'none';
    if (emptyState) emptyState.style.display = 'flex';
    if (pagination) pagination.style.display = 'none';
    updatePaginationInfo(0);
    return;
  }

  tableBody.closest('table').style.display = 'table';
  if (emptyState) emptyState.style.display = 'none';
  if (pagination) pagination.style.display = 'flex';

  tableBody.innerHTML = paginatedData.map(item => `
    <tr class="hover:bg-gray-50/50 transition-colors">
      <td class="py-4 px-6 font-medium text-gray-900">${formatMonthYear(item.month, item.year)}</td>
      <td class="py-4 px-6">${formatCurrency(item.grossSalary)}</td>
      <td class="py-4 px-6 text-gray-600">${formatCurrency(item.deductions)}</td>
      <td class="py-4 px-6 font-semibold text-green-700">${formatCurrency(item.netSalary)}</td>
      <td class="py-4 px-6">
        <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColors[item.status] || 'bg-gray-100 text-gray-700'} uppercase tracking-wide">
          ${item.status}
        </span>
      </td>
      <td class="py-4 px-6">
        <div class="flex space-x-2">
          <a href="payslip-view.html?payslipData=${encodeURIComponent(JSON.stringify(item))}" class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 font-medium text-sm p-2 rounded-xl transition-all duration-200 inline-block" title="View details">
            👁️ View
          </a>
          <button onclick="downloadPayslip(${item.id})" class="text-green-600 hover:text-green-800 hover:bg-green-50 font-medium text-sm p-2 rounded-xl transition-all duration-200" title="Download PDF">
            ⬇️ Download
          </button>
        </div>
      </td>
    </tr>
  `).join('');

  updatePaginationInfo(data.length);
}

function handleSort(column) {
  if (currentSort.column === column) {
    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
  } else {
    currentSort.column = column;
    currentSort.direction = 'asc';
  }

  currentData.sort((a, b) => {
    let valA = a[column];
    let valB = b[column];
    if (['grossSalary', 'deductions', 'netSalary'].includes(column)) {
      valA = Number(valA);
      valB = Number(valB);
    } else if (column === 'date') {
      valA = new Date(valA);
      valB = new Date(valB);
    }
    if (valA < valB) return currentSort.direction === 'asc' ? -1 : 1;
    if (valA > valB) return currentSort.direction === 'asc' ? 1 : -1;
    return 0;
  });

  renderTable();
  updateSortIcons();
}

function updateSortIcons() {
  document.querySelectorAll('.sort-header').forEach(th => {
    let label = th.dataset.column.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase());
    th.innerHTML = label;
    if (th.dataset.column === currentSort.column) {
      th.innerHTML += currentSort.direction === 'asc' ? ' <span class="text-blue-500 font-bold">▲</span>' : ' <span class="text-blue-500 font-bold">▼</span>';
    }
  });
}

function handlePagination(direction) {
  const totalPages = Math.ceil(currentData.length / pageSize);
  if (direction === 'prev' && currentPage > 1) currentPage--;
  if (direction === 'next' && currentPage < totalPages) currentPage++;
  renderTable();
}

function updatePaginationInfo(totalItems) {
  const totalPages = Math.ceil(totalItems / pageSize);
  const startItem = (currentPage - 1) * pageSize + 1;
  const endItem = Math.min(currentPage * pageSize, totalItems);
  
  if (document.getElementById('page-info')) {
    document.getElementById('page-info').textContent = `Page ${currentPage} of ${totalPages}`;
  }
}

function filterData() {
  const searchTerm = searchInput.value.toLowerCase().trim();
  const monthFilter = monthSelect.value;
  const yearFilter = yearSelect.value;

  currentData = mockData.filter(item => {
    const itemMonthYear = formatMonthYear(item.month, item.year).toLowerCase();
    const matchesSearch = itemMonthYear.includes(searchTerm) ||
      item.month.toLowerCase().includes(searchTerm) ||
      item.status.toLowerCase().includes(searchTerm) ||
      formatCurrency(item.grossSalary).toLowerCase().includes(searchTerm) ||
      formatCurrency(item.netSalary).toLowerCase().includes(searchTerm);
    
    const matchesMonth = !monthFilter || item.month === monthFilter;
    const matchesYear = !yearFilter || item.year.toString() === yearFilter;
    
    return matchesSearch && matchesMonth && matchesYear;
  });

  currentPage = 1;
  renderTable();
}

// Debounced filter
const debouncedFilter = debounce(filterData, 300);
searchInput.addEventListener('input', debouncedFilter);
monthSelect.addEventListener('change', filterData);
yearSelect.addEventListener('change', filterData);

// Download functions (mock PDFs as txt)
function downloadPayslip(id) {
  const item = mockData.find(d => d.id === id);
  const content = `PayslipSys - ${formatMonthYear(item.month, item.year)}\n\nEmployee: Emmanuel\nDepartment: IT\n\nGross Salary: ${formatCurrency(item.grossSalary)}\nDeductions: ${formatCurrency(item.deductions)}\nNet Salary: ${formatCurrency(item.netSalary)}\nStatus: ${item.status}\n\nGenerated: ${new Date().toLocaleDateString()}`;
  const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `payslip-${item.month}-${item.year}.txt`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

function downloadAll() {
  const content = mockData.map(item => 
    `Payslip ${formatMonthYear(item.month, item.year)}: ${formatCurrency(item.netSalary)} (${item.status})`
  ).join('\n');
  const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `payslip-all-${new Date().toISOString().slice(0,10)}.txt`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
}

function viewPayslip(id) {
  const item = mockData.find(d => d.id === id);
  alert(`View Payslip Modal\n\n${formatMonthYear(item.month, item.year)}\nGross: ${formatCurrency(item.grossSalary)}\nNet: ${formatCurrency(item.netSalary)}\nStatus: ${item.status}`);
  // TODO: Replace with actual modal
}

function populateFilters() {
  const months = [...new Set(mockData.map(d => d.month))].sort();
  const years = [...new Set(mockData.map(d => d.year))].sort((a, b) => b - a);

  monthSelect.innerHTML = '<option value="">All Months</option>' + 
    months.map(m => `<option value="${m}">${m}</option>`).join('');
  yearSelect.innerHTML = '<option value="">All Years</option>' + 
    years.map(y => `<option value="${y}">${y}</option>`).join('');
}

// Init
document.addEventListener('DOMContentLoaded', () => {
  populateFilters();
  currentData.sort((a, b) => new Date(b.date) - new Date(a.date));
  renderTable();
  updateSortIcons();
  updatePaginationInfo(mockData.length);
});
