# Payslip Detail Enhancements TODO

**Status:** In Progress

**Task:** Fetch/display department (already done) + tax_id as "Payer ID"

**Plan:**
1. Add `u.tax_id` to includes/get-payslip-detail.php SELECT
2. Add payerId field to payslip-view.php HTML table (Payer ID row)
3. Update js/payslip-view.js to populate #pdf-payer-id with data.tax_id
4. Test payslip-view.php?id=1

**Steps:**
- [x] Create TODO-payslip-detail.md
- [x] 1. Edit get-payslip-detail.php: Added `COALESCE(u.tax_id, 'N/A') AS taxId`
- [x] 2. Edit payslip-view.php: `<td id="pdf-payer-id">N/A</td>`
- [x] 3. Edit js/payslip-view.js: `pdf-payer-id.textContent = data.taxId || 'N/A'`
- [x] 4. Complete TODO & test

**Status:** Complete ✅

Department already displays via `data.department`. Test HR/payslip-view.php?id=1

