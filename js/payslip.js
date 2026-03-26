// Payslip page - loads all payslips from backend API
let currentData = [];
let currentPage = 1;
const pageSize = 10;
let totalItems = 0;
let currentSort = { column: 'batch_date', direction: 'desc' };

// DOM elements
const tableBody = document.querySelector('#payslip-table tbody');
const emptyState = document.querySelector('#empty-state');
const pagination = document.querySelector('#pagination');
const searchInput = document.querySelector('#search');
const monthSelect = document.querySelector('#month-filter');
const yearSelect = document.querySelector('#year-filter');
const pageInfo = document.querySelector('#page-info');

// Status colors
const statusColors = {
  'Paid': 'bg-green-100 text-green-700',
  'Pending': 'bg-yellow-100 text-yellow-700',
  'Completed': 'bg-blue-100 text-blue-700',
  'Failed': 'bg-red-100 text-red-700',
  'Processing': 'bg-indigo-100 text-indigo-700'
};

// Format currency
const formatCurrency = (amount) => `₦${Number(amount || 0).toLocaleString()}`;

// Format month/year
const formatMonthYear = (month, year) => `${month || ''} ${year || ''}`.trim();

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
      <td colspan="7" class="py-12 text-center">
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
      currentData = result.data || [];
      totalItems = result.total || 0;
      currentPage = page;
      renderTable(result.data || []);
      populateFilters(result.months || []);
      updatePaginationInfo();
    } else {
      console.error('API error:', result.error);
      showError(`Error: ${result.error}`);
    }
  } catch (error) {
    console.error('Fetch error:', error);
    showError('Network error. Please try again.');
  }
}

function showError(message) {
  tableBody.innerHTML = `
    <tr>
      <td colspan="7" class="py-12 text-center text-red-500">${message}</td>
    </tr>
  `;
  emptyState?.classList.remove('hidden');
  pagination?.classList.add('hidden');
}

function renderTable(data) {
  if (!data || data.length === 0) {
    tableBody.innerHTML = `
      <tr>
        <td colspan="7" class="py-12 text-center text-gray-500">
          <p>No payslips found matching your criteria.</p>
        </td>
      </tr>
    `;
    emptyState?.classList.remove('hidden');
    pagination?.classList.add('hidden');
    return;
  }

  emptyState?.classList.add('hidden');
  pagination?.classList.remove('hidden');

  tableBody.innerHTML = data.map(item => `
    <tr class="hover:bg-gray-50/50 transition-colors border-b border-gray-100 last:border-b-0">
      <td class="py-4 px-6 font-medium text-gray-900">${formatMonthYear(item.month, item.year)}</td>

      <td class="py-4 px-6">${formatCurrency(item.grossSalary)}</td>

      <td class="py-4 px-6 text-gray-600">${formatCurrency(item.deductions)}</td>

      <td class="py-4 px-6 font-semibold text-green-700">${formatCurrency(item.netSalary)}</td>

      <td class="py-4 px-6 text-center">
        <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColors[item.status] || 'bg-gray-100 text-gray-700'} uppercase tracking-wide">
          ${item.status || 'Paid'}
        </span>
      </td>

      <td class="py-4 px-6">
        <div class="flex flex-col space-y-1 text-sm">
          <span class="font-medium text-gray-900 truncate max-w-[120px]">${item.employeeName}</span>

          <span class="text-xs text-gray-500 font-mono">${item.employeeId}</span>
        </div>
      </td>
      <td class="py-4 px-6">
        <div class="flex items-center space-x-2">
          <a href="payslip-view.php?id=${item.id}" 
             class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 p-2 rounded-xl transition-all" 
             title="View payslip details">
            👁️
          </a>
          ${item.file_path ? 
            `<a href="${item.file_path}" target="_blank" 
               class="text-green-600 hover:text-green-800 hover:bg-green-50 p-2 rounded-xl transition-all" 
               title="Download Excel">
              📊
            </a>` : ''
          }

          <button onclick="downloadPayslip(event, ${item.id})" 
                  class="text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 p-2 rounded-xl transition-all" 
                  title="Download PDF">
            ⬇️
          </button>
        </div>
      </td>
    </tr>
  `).join('');

  updatePaginationInfo();
}

function updatePaginationInfo() {
  const totalPages = Math.ceil(totalItems / pageSize);
  if (pageInfo) {
    pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
  }

  // Update prev/next buttons
  const prevBtn = document.querySelector('#pagination button[onclick="handlePagination(\'prev\')"]');
  const nextBtn = document.querySelector('#pagination button[onclick="handlePagination(\'next\')"]');
  
  if (prevBtn) prevBtn.disabled = currentPage === 1;
  if (nextBtn) nextBtn.disabled = currentPage >= totalPages || totalPages === 0;
}

function populateFilters(months = []) {
  let monthHtml = '<option value="">All Months</option>';
  let yearHtml = '<option value="">All Years</option>';
  
  // Group by year for better UX
  const years = {};
  months.forEach(m => {
    if (!years[m.year]) years[m.year] = [];
    years[m.year].push(m.month);
  });

  // Sort years descending
  Object.keys(years).sort((a, b) => b - a).forEach(year => {
    yearHtml += `<option value="${year}">${year}</option>`;
    
    // Add months for this year
    years[year].sort().forEach(month => {
      monthHtml += `<option value="${month}">${month} ${year}</option>`;
    });
  });

  monthSelect.innerHTML = monthHtml;
  yearSelect.innerHTML = yearHtml;
}

function handlePagination(direction) {
  const totalPages = Math.ceil(totalItems / pageSize);
  if (direction === 'prev' && currentPage > 1) {
    loadData(currentPage - 1);
  } else if (direction === 'next' && currentPage < totalPages) {
    loadData(currentPage + 1);
  }
}

async function downloadPayslip(event, id) {
  event.preventDefault(); // Prevent any default action
  
  const item = currentData.find(d => d.id == id);
  if (!item) return;

  const button = event.target; // Now button is defined!
  
  try {
    // Show loading
    const originalText = button.innerHTML;
    button.innerHTML = '⏳';
    button.disabled = true;

    // Fetch PDF from server
    const response = await fetch(`../includes/payslip-template.php?id=${item.id}`);
    // const response = await fetch(`payslip-pdf.php?id=${item.id}`);
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    const blob = await response.blob();
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `payslip-${item.employeeId}-${item.month}-${item.year}.pdf`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
  } catch (error) {
    console.error('PDF download error:', error);
    alert('Failed to download PDF. Please refresh and try again.');
  } finally {
    // Reset button
    button.innerHTML = '⬇️';
    button.disabled = false;
  }
}
// Event listeners
const debouncedSearch = debounce(() => loadData(1), 300);
searchInput?.addEventListener('input', debouncedSearch);
monthSelect?.addEventListener('change', () => loadData(1));
yearSelect?.addEventListener('change', () => loadData(1));

// Initial load
document.addEventListener('DOMContentLoaded', () => {
  loadData(1);
});