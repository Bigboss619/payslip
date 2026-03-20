
const pageSize = 10;
let currentPage = 1;

function renderPayrollTable(data = currentPayrollData) {
  const tableBody = document.getElementById('payrollTableBody');
  if (!tableBody) return;

  const start = (currentPage - 1) * pageSize;
  const end = start + pageSize;
  const paginatedData = data.slice(start, end);

  if (paginatedData.length === 0) {
    tableBody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-gray-500">No data found</td></tr>';
    updatePayrollPagination(data.length);
    return;
  }

  tableBody.innerHTML = paginatedData.map(item => `
    <tr class="border-b hover:bg-gray-50">
      <td class="p-2">${item.name}</td>
      <td class="p-2">${item.department}</td>
      <td class="p-2">${formatCurrency(item.grossSalary)}</td>
      <td class="p-2 font-semibold">${formatCurrency(item.netSalary)}</td>
    </tr>
  `).join('');

  updatePayrollPagination(data.length);
}

function updatePayrollPagination(totalItems) {
  const pagination = document.getElementById('payrollPagination');
  if (!pagination) return;

  const totalPages = Math.ceil(totalItems / pageSize);
  const startItem = (currentPage - 1) * pageSize + 1;
  const endItem = Math.min(currentPage * pageSize, totalItems);

  pagination.innerHTML = `
    <div class="flex items-center justify-between mt-4">
      <div class="text-sm text-gray-700">
        Showing ${startItem} to ${endItem} of ${totalItems} entries
      </div>
      <div class="flex space-x-2">
        <button onclick="handlePayrollPagination('prev')" 
                ${currentPage === 1 ? 'disabled class="bg-gray-300 cursor-not-allowed"' : 'class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded text-sm"'} 
                id="prevBtn">
          Previous
        </button>
        <span class="text-sm font-medium mx-2">${currentPage} of ${totalPages}</span>
        <button onclick="handlePayrollPagination('next')" 
                ${currentPage === totalPages ? 'disabled class="bg-gray-300 cursor-not-allowed"' : 'class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded text-sm"'} 
                id="nextBtn">
          Next
        </button>
      </div>
    </div>
  `;
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

