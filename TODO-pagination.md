# Add Pagination to Upload Payroll Table

**Information Gathered:**
- js/upload.js: renderPayrollTable shows all data; no pagination. Has applyFilters, updateSummary.
- pages/upload.html: Payroll table (#payrollTableBody), no pagination UI.
- Model from js/payslip.js: pageSize=10, currentPage, slice(start,end), prev/next buttons, page info.

**Plan:**
- **js/upload.js**:
  - Add: const pageSize = 10; let currentPage = 1; let payrollPagination = document.getElementById('payrollPagination');
  - Update renderPayrollTable: slice((currentPage-1)*pageSize, currentPage*pageSize), call updatePayrollPagination(total).
  - New functions: handlePayrollPagination(direction), updatePayrollPagination(totalItems).
  - In applyFilters, handleMonthSelect, selectPreviousMonth: currentPage = 1; renderPayrollTable().
- **pages/upload.html**:
  - After payroll table: Add div#payrollPagination with prev/page info/next buttons.
- Update init DOMContentLoaded.

**Dependent Files:** js/upload.js, pages/upload.html
**Followup:** Test pagination with filters.

Confirm before implementing?

