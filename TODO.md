# Payroll Excel Data Display Fix - TODO

## Plan Breakdown (4 Steps)

### ✅ Step 1: Create/Update TODO.md [COMPLETED]

### ✅ Step 2: Fix js/payroll-table.js
- Add `window.onExcelDataLoaded` listener
- Export `renderExcelTable` globally  
- Add `net_salary` fallback `|| 0`
- Ensure `#excelTableContainer` visibility

### ✅ Step 3: Fix js/upload.js  
- Dispatch `new CustomEvent('excelDataLoaded')` after setting data
- Safe `window.payrollTable?.renderExcelTable()` call
- Verify global `window.currentExcelData` set

### ✅ Step 4: Fix HR/upload.php
- Script order: payroll-table.js BEFORE upload.js
- Add CSS for `.table-container.active { display: block !important; }`
- Ensure `#excelTableContainer` has `active` class

### ⏳ Step 4: Fix HR/upload.php
- Script order: payroll-table.js BEFORE upload.js
- Add CSS for `.table-container.active { display: block !important; }`
- Ensure `#excelTableContainer` has `active` class

### ⏳ Step 5: Test & Complete
- Reload HR/upload.php 
- Verify 5 Excel rows display in Excel tab
- `attempt_completion`

**Status: 4/5 completed**
