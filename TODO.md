# HR Payslip Page Implementation - All Payslips View with Filters

## Current Progress
- [x] Planning complete and approved

## TODO Steps

1. **[COMPLETE]** Enhance `includes/get-payroll.php`:
   - Added year filter
   - CamelCase fields, id/status/date/position
   - Improved months
   - Add year filter param.
   - Map fields to camelCase JS format in response (grossSalary, netSalary, employeeName etc.).
   - Add derived 'status' (e.g. from batch status or 'Paid'), 'date' (batch created_at).

2. **[COMPLETE]** Create new `includes/get-payslip-detail.php`:
   - Fetch detail by ID, full earnings/deductions, breakdowns
   - Fetch full payslip by ID, join users/payroll_batches for employee details, month/year.

3. **[COMPLETE]** Update `js/payslip.js`:
   - Replaced mockData with AJAX to get-payroll.php
   - Backend pagination, filters by month/year/name
   - ID-based view links

4. **[COMPLETE]** Update `js/payslip-view.js`:
   - Parse ?id=, fetch detail API
   - Uses real data from backend

5. **[COMPLETE]** Update `HR/payslip.php`:
   - Title "All Payslips", "all employee salary history"

6. **[PENDING]** Update `HR/payslip-view.php`:
   - Handle ?id= param for JS.

7. **[PENDING]** Test:
   - Load HR/payslip.php, verify data, filters, pagination, detail view.
   - Check console/errors.

## Next Action
Implement step 1: Enhance includes/get-payroll.php

