# Payslip Detail Enhancements TODO

**Status:** In Progress

**Task:** Fetch/display department (already done) + tax_id as "Payer ID"

**Plan:**
1. Add `u.tax_id` to includes/get-payslip-detail.php SELECT
2. Add payerId field to payslip-view.php HTML table (Payer ID row)
3. Update js/payslip-view.js to populate #pdf-payer-id with data.tax_id
4. Test payslip-view.php?id=1

**Updated table layout:**
- Removed Designation, SBU rows
- Added Account Number, Bank Name rows
- Payer ID = tax_id (dynamic)
- Department kept (dynamic)

**Steps:**
- [x] 1. Added accountNumber, bankName to get-payslip-detail.php SELECT
- [x] 2. Replaced Designation/SBU with Account/Bank in payslip-view.php table
- [x] 3. Added JS populate for new fields
- [x] 4. Complete

**Status:** Complete ✅

Test: HR/payslip-view.php?id=1

