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
const searchInput = document.querySelector('#search') || {value: ''};
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
// async function loadData(page = 1) {
//   const month = monthSelect.value;
//   const year = yearSelect.value;
//   const name = searchInput.value.trim();
//   const offset = (page - 1) * pageSize;

//   // Show loading
//   tableBody.innerHTML = `
//     <tr>
//       <td colspan="7" class="py-12 text-center">
//         <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-500 rounded-full animate-spin mx-auto mb-4"></div>
//         <p>Loading payslips...</p>
//       </td>
//     </tr>
//   `;

//   try {
//     const params = new URLSearchParams({
//       limit: pageSize,
//       offset: offset,
//       month: month || '',
//       year: year || '',
//       name: name || ''
//     });
    
//     // const endpoint = window.payslipEndpoint || 'get-payroll.php';
//     const response = await fetch(`${window.PAYSLOP_API}?${params}`);
//     const result = await response.json();

//     if (result.success) {
//       currentData = result.data || [];
//       totalItems = result.total || 0;
//       currentPage = page;
//       renderTable(result.data || []);
//       populateFilters(result.months || []);
//       updatePaginationInfo();
//     } else {
//       console.error('API error:', result.error);
//       showError(`Error: ${result.error}`);
//     }
//   } catch (error) {
//     console.error('Fetch error:', error);
//     showError('Network error. Please try again.');
//   }
// }

// 🔥 FIXED loadData (add console.log for debug)
// async function loadData(page = 1) {
//   const month = monthSelect.value;
//   const year = yearSelect.value;
//   const name = searchInput.value.trim();
//   const offset = (page - 1) * pageSize;

//   console.log('🔍 Filters:', { month, year, name, page }); // 👈 DEBUG

//   // Show loading
//   tableBody.innerHTML = `
//     <tr>
//       <td colspan="7" class="py-12 text-center">
//         <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-500 rounded-full animate-spin mx-auto mb-4"></div>
//         <p>Loading payslips...</p>
//       </td>
//     </tr>
//   `;

//   try {
//     const params = new URLSearchParams({
//       limit: pageSize,
//       offset: offset,
//       month: month || '',
//       year: year || '',
//       name: name || ''
//     });
    
//     const response = await fetch(`${window.PAYSLOP_API}?${params}`);
//     const result = await response.json();

//     console.log('📊 API Response:', result.debug); // 👈 DEBUG

//     if (result.success) {
//       currentData = result.data || [];
//       totalItems = result.total || 0;
//       currentPage = page;
//       renderTable(result.data || []);
//       populateFilters(result.months || []);
//       updatePaginationInfo();
//     } else {
//       showError(`Error: ${result.error}`);
//     }
//   } catch (error) {
//     console.error('Fetch error:', error);
//     showError('Network error. Please try again.');
//   }
// }
// 🔥 DEBUG VERSION - Replace your loadData function
async function loadData(page = 1) {
  const month = monthSelect.value;
  const year = yearSelect.value;
  const name = searchInput.value.trim();
  const offset = (page - 1) * pageSize;

  console.log('🔍 DEBUG REQUEST:', { month, year, name, page, offset, api: window.PAYSLOP_API });

  tableBody.innerHTML = `
    <tr><td colspan="7" class="py-12 text-center">
      <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-500 rounded-full animate-spin mx-auto mb-4"></div>
      <p></p>
    </td></tr>
  `;

  try {
    const params = new URLSearchParams({
      limit: pageSize,
      offset: offset,
      month: month || '',
      year: year || '',
      name: name || ''
    });
    
    console.log('🌐 FULL URL:', `${window.PAYSLOP_API}?${params}`);
    
    const response = await fetch(`${window.PAYSLOP_API}?${params}`);
    const result = await response.json();
    
    console.log('📊 FULL API RESPONSE:', result);

    if (result.success || result.DEBUG_MODE) {
      currentData = result.data || [];
      totalItems = result.total || 0;
      currentPage = page;
      
      // 🔥 Show debug info in table
      if (result.DEBUG_MODE) {
        tableBody.innerHTML = `
          <tr class="bg-yellow-50">
            <td colspan="7" class="p-8 text-center">
              <h3 class="text-xl font-bold text-yellow-800 mb-4">🔍 DEBUG INFO</h3>
              <pre class="bg-gray-100 p-4 rounded-xl text-sm max-h-96 overflow-auto text-left mx-auto max-w-4xl">
${JSON.stringify(result.debug, null, 2)}
              </pre>
              <button onclick="loadData(1)" class="mt-4 bg-blue-500 text-white px-6 py-2 rounded-xl hover:bg-blue-600">
                🔄 Retry Normal Load
              </button>
            </td>
          </tr>
        `;
        return;
      }
      
      renderTable(result.data || []);
      populateFilters(result.months || []);
      updatePaginationInfo();
    } else {
      console.error('API Error:', result);
      tableBody.innerHTML = `<tr><td colspan="7" class="py-12 text-center text-red-500">Error: ${result.error}</td></tr>`;
    }
  } catch (error) {
    console.error('Fetch Error:', error);
    tableBody.innerHTML = `<tr><td colspan="7" class="py-12 text-center text-red-500">Network Error: ${error.message}</td></tr>`;
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

  tableBody.innerHTML = data.map(item => {
    const deductions = Number(item.deductions || 0);
    const pension = Number(item.pension || 0);
    const paye = Number(item.paye || 0);
    const totalDeductions = deductions + pension + paye;
    console.log(pension, paye, deductions, totalDeductions);
    return `
    <tr class="hover:bg-gray-50/50 transition-colors border-b border-gray-100 last:border-b-0">
      <td class="py-4 px-6 font-medium text-gray-900">${formatMonthYear(item.month, item.year)}</td>

      <td class="py-4 px-6">${formatCurrency(item.grossSalary)}</td>

      <td class="py-4 px-6 text-gray-600">${formatCurrency(totalDeductions)}</td>

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
  `;
  }).join('');

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

// function populateFilters(months = []) {
//   let monthHtml = '<option value="">All Months</option>';
//   let yearHtml = '<option value="">All Years</option>';
  
//   // Group by year for better UX
//   const years = {};
//   months.forEach(m => {
//     if (!years[m.year]) years[m.year] = [];
//     years[m.year].push(m.month);
//   });

//   // Sort years descending
//   Object.keys(years).sort((a, b) => b - a).forEach(year => {
//     yearHtml += `<option value="${year}">${year}</option>`;
    
//     // Add months for this year
//     years[year].sort().forEach(month => {
//       monthHtml += `<option value="${month}">${month} ${year}</option>`;
//     });
//   });

//   monthSelect.innerHTML = monthHtml;
//   yearSelect.innerHTML = yearHtml;
// }
// function populateFilters(months = []) {
//   // Clear options
//   monthSelect.innerHTML = '<option value="">All Months</option>';
//   yearSelect.innerHTML = '<option value="">All Years</option>';
  
//   if (months.length === 0) return;
  
//   // Group by year
//   const years = {};
//   months.forEach(m => {
//     if (!years[m.year]) years[m.year] = [];
//     if (!years[m.year].includes(m.month)) {
//       years[m.year].push(m.month);
//     }
//   });

//   // Add sorted years
//   Object.keys(years)
//     .sort((a, b) => b - a)  // Newest first
//     .forEach(year => {
//       yearSelect.innerHTML += `<option value="${year}">${year}</option>`;
//     });

//   // Add all unique months (sorted)
//   const allMonths = [...new Set(months.map(m => m.month))].sort();
//   allMonths.forEach(month => {
//     monthSelect.innerHTML += `<option value="${month}">${month}</option>`;
//   });
// }

// function populateFilters(months = []) {
//   // Clear options first
//   monthSelect.innerHTML = '<option value="">All Months</option>';
//   yearSelect.innerHTML = '<option value="">All Years</option>';
  
//   if (months.length === 0) return;
  
//   const years = {};
//   months.forEach(m => {
//     if (!years[m.year]) years[m.year] = [];
//     if (!years[m.year].includes(m.month)) {
//       years[m.year].push(m.month);
//     }
//   });

//   // 🔥 FIXED: Populate YEARS dropdown
//   Object.keys(years)
//     .sort((a, b) => b - a)  // Newest first
//     .forEach(year => {
//       yearSelect.innerHTML += `<option value="${year}">${year}</option>`;
//     });

//   // 🔥 FIXED: Populate MONTHS dropdown (show full month names)
//   const allMonths = [...new Set(months.map(m => m.month))].sort();
//   allMonths.forEach(month => {
//     monthSelect.innerHTML += `<option value="${month}">${month}</option>`;
//   });
// }

function populateFilters(months = []) {
  // 🔥 UX FIX: Preserve current selections
  const currentMonth = monthSelect.value;
  const currentYear = yearSelect.value;
  
  monthSelect.innerHTML = '<option value="">All Months'
  yearSelect.innerHTML = '<option value="">All Years</option>';
  
  if (!months.length) return;
  
  // Years
  const years = [...new Set(months.map(m => m.year))].sort((a,b) => b-a);
  years.forEach(year => {
    yearSelect.innerHTML += `<option value="${year}">${year}</option>`;
  });
  
  // Months
  const monthNames = [...new Set(months.map(m => m.month))].sort();
  monthNames.forEach(month => {
    monthSelect.innerHTML += `<option value="${month}">${month}</option>`;
  });
  
  // 🔥 UX FIX: Restore selections AFTER populating
  if (monthSelect.querySelector(`[value="${currentMonth}"]`)) {
    monthSelect.value = currentMonth;
  }
  if (yearSelect.querySelector(`[value="${currentYear}"]`)) {
    yearSelect.value = currentYear;
  }
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
if (searchInput && typeof searchInput.addEventListener === 'function') {
  searchInput.addEventListener('input', debouncedSearch);
}
monthSelect?.addEventListener('change', () => loadData(1));
yearSelect?.addEventListener('change', () => loadData(1));

// 🔥 CLEAR FILTERS
window.clearFilters = function() {
  if (searchInput && typeof searchInput.value !== 'undefined') searchInput.value = '';
  if (monthSelect) monthSelect.value = '';
  if (yearSelect) yearSelect.value = '';
  loadData(1);
};

// Initial load
document.addEventListener('DOMContentLoaded', () => {
  loadData(1);
});