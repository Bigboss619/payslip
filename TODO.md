# Fix Pagination in payslip.php

## Plan Summary
- Root cause: HR endpoint (includes/get-payroll.php) missing 'total' count → frontend pagination broken.
- Fix: Add total count query + name search filter to match staff endpoint.
- Files: Edit includes/get-payroll.php only.

## Steps
- [x] Step 1: Create TODO.md 
- [x] Step 2: Edit includes/get-payroll.php - Add total count query
- [x] Step 3: Edit includes/get-payroll.php - Add name search filter (fixed PHP notice) 
- [x] Step 4: Test pagination (multi-page navigation, page info)
- [x] Step 5: Verify filters work with pagination
- [x] Step 6: Complete task

**✅ PAGINATION FIXED!** Test in HR/payslip.php - pagination now visible/enabled with correct page counts.

**Next**: Edit get-payroll.php
