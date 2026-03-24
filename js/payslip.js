// Payslip page - loads all payslips from backend API
let currentData = [];
let currentPage = 1;
const pageSize = 10;
let currentSort = { column: 'date', direction: 'desc' };
let totalItems = 0;

// DOM elements
const tableBody = document.querySelector('#payslip-table tbody');
const emptyState = document.querySelector('#empty-state');
const pagination = document.querySelector('#pagination');
const searchInput = document.querySelector('#search');
const monthSelect = document.querySelector('#month-filter');
const yearSelect = document.querySelector('#year-filter');

// Status colors
const statusColors = {
  'Paid': 'bg-green-100 text-green-700',
  'Pending': 'bg-yellow-100 text-yellow-700',
  'Completed': 'bg-blue-100 text-blue-700',
  'Failed': 'bg-red-100 text-red-700'
};

// Format currency
const formatCurrency = (amount) => `₦${Number(amount).toLocaleString()}`;

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

// Load data from backend
async function loadData(page = 1) {
  const month = monthSelect.value;
  const year = yearSelect.value;
  const name = searchInput.value.trim();
  const offset = (page - 1) * pageSize;

  // Show loading
  tableBody.innerHTML = `
    <tr>
      <td colspan="6" class="py-12 text-center">
        <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-500 rounded-full animate-spin mx-auto mb-4"></div>
        <p>Loading payslips...</p>
      </td>
    </tr>
  `;

  try {
    const params = new URLSearchParams({
      limit: pageSize,
      offset: offset,
      month: month || '',
      year: year || '',
      name: name || ''
    });
    
    const response = await fetch(`../includes/get-payroll.php?${params}`);
    const result = await response.json();

    if (result.success) {
      currentData = result.data;
      totalItems = result.total;
      currentPage = page;
      renderTable(result.data);
      populateFilters(result.months || []);
    } else {
      console.error('API error:', result.error);
      tableBody.innerHTML = `
        <tr>
          <td colspan="6" class="py-12 text-center text-red-500">Error loading data: ${result.error}</td>
        </tr>
      `;
    }
  } catch (error) {
    console.error('Fetch error:', error);
    tableBody.innerHTML = `
      <tr>
        <td colspan="6" class="py-12 text-center text-red-500">Network error. Please try again.</td>
      </tr>
    `;
  }
}

function renderTable(data) {
  if (!data || data.length === 0) {
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

  tableBody.innerHTML = data.map(item => `
    <tr class="hover:bg-gray-50/50 transition-colors">
      <td class="py-4 px-6 font-medium text-gray-900">${formatMonthYear(item.month, item.year)}</td>
      <td class="py-4 px-6">${formatCurrency(item.grossSalary)}</td>
      <td class="py-4 px-6 text-gray-600">${formatCurrency(item.deductions)}</td>
      <td class="py-4 px-6 font-semibold text-green-700">${formatCurrency(item.netSalary)}</td>
      <td class="py-4 px-6">
        <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColors[item.status] || 'bg-gray-100 text-gray-700'} uppercase tracking-wide">
          ${item.status || 'Paid'}
        </span>
      </td>
      <td class="py-4 px-6">
        <div class="flex space-x-2">
          <a href="payslip-view.php?id=${item.id}" class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 font-medium text-sm p-2 rounded-xl transition-all duration-200 inline-block" title="View details">
            👁️ View
          </a>
          <button onclick="downloadPayslip(${item.id})" class="text-green-600 hover:text-green-800 hover:bg-green-50 font-medium text-sm p-2 rounded-xl transition-all duration-200" title="Download PDF">
            ⬇️ Download
          </button>
        </div>
      </td>
    </tr>
  `).join('');

  updatePaginationInfo(totalItems);
}

function handleSort(column) {
  if (currentSort.column === column) {
    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
  } else {
    currentSort.column = column;
    currentSort.direction = 'asc';
  }

  // Local sort for current page data
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

  renderTable(currentData);
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
  const totalPages = Math.ceil(totalItems / pageSize);
  if (direction === 'prev' && currentPage > 1) {
    loadData(currentPage - 1);
  } else if (direction === 'next' && currentPage < totalPages) {
    loadData(currentPage + 1);
  }
}

function updatePaginationInfo(itemsCount) {
  const totalPages = Math.ceil(totalItems / pageSize);
  
  if (document.getElementById('page-info')) {
    document.getElementById('page-info').textContent = `Page ${currentPage} of ${totalPages}`;
  }

  const prevBtn = document.querySelector('#pagination button[onclick="handlePagination(\'prev\')"]');
  const nextBtn = document.querySelector('#pagination button[onclick="handlePagination(\'next\')"]');
  if (prevBtn) prevBtn.disabled = currentPage === 1;
  if (nextBtn) nextBtn.disabled = currentPage === totalPages || totalPages === 0;
}

function populateFilters(months = []) {
  let monthHtml = '<option value="">All Months</option>';
  let yearHtml = '<option value="">All Years</option>';

  months.forEach(m => {
    monthHtml += `<option value="${m.month}">${m.month}</option>`;
    yearHtml += `<option value="${m.year}">${m.year}</option>`;
  });

  monthSelect.innerHTML = monthHtml;
  yearSelect.innerHTML = yearHtml;
}

function downloadPayslip(id) {
  const item = currentData.find(d => d.id === id);
  if (item) {
    const content = `PayslipSys HR - ${formatMonthYear(item.month, item.year)}
Employee: ${item.employeeName}
ID: ${item.employeeId}
Department: ${item.department}
Net Pay: ${formatCurrency(item.netSalary)}
Status: ${item.status}
Generated: ${item.date}`;
    const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `payslip-${item.employeeId}-${item.month}-${item.year}.txt`;
    a.click();
    URL.revokeObjectURL(url);
  }
}

function downloadAll() {
  if (currentData.length === 0) return;
  const content = currentData.map(item => `${item.employeeName}: ${formatMonthYear(item.month, item.year)} - ${formatCurrency(item.netSalary)} (${item.status})`).join('\n');
  const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `all-payslips-${new Date().toISOString().slice(0,10)}.txt`;
  a.click();
  URL.revokeObjectURL(url);
}

// Event listeners
const debouncedSearch = debounce(() => loadData(1), 300);
searchInput.addEventListener('input', debouncedSearch);
monthSelect.addEventListener('change', () => loadData(1));
yearSelect.addEventListener('change', () => loadData(1));

// Initial load
document.addEventListener('DOMContentLoaded', () => {
  loadData(1);
});

