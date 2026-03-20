# Enhance Upload Payroll Feature

## Approved Plan Steps:
- [ ] Step 1: Create this TODO file with breakdown.
- [x] Step 2: Update js/upload.js - Add mockPayrollData (25 diverse records: IT/HR/Finance/Sales depts, multiple employees/staffIds, months across years), mockUploadedMonths Set, helper functions (formatCurrency, populateSelectors, checkMonthUploaded, renderPayrollTable, applyFilters, handleMonthSelect, extended handleUpload).
- [x] Step 3: Update pages/upload.html - Add month selector dropdown, previous months chips div, filters row (name/staffId inputs, month/dept selects), ids for dynamic elements (payrollTableBody, summary totals, toast container); remove static tbody/summary/hardcoded success.
- [ ] Step 4: Init logic in js/upload.js DOMContentLoaded: populate table/filters/summary/chips.
- [ ] Step 5: Test filters, month check (alert/toast), simulate upload (add month, refresh).
- [ ] Step 6: Update TODO progress, attempt_completion.

**Progress**: Steps 2-3 complete (JS logic + HTML UI with month select, chips, filters, dynamic table/summary). Testing next.

