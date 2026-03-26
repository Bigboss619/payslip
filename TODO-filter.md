# Month/Year Filter Implementation - TODO

## Plan Breakdown

### ✅ Step 1: Create TODO-filter.md [COMPLETED]

### ⏳ Step 2: Backend - Add `get_latest_batch` endpoint to upload-payroll.php
- Query: `SELECT * FROM payroll_batches WHERE status = 'completed' ORDER BY created_at DESC LIMIT 1`
- Return batch_id, month_num, year for default load

### ⏳ Step 3: Frontend - Load latest on page load (js/payroll-table.js)
- On init: fetch latest → set #statusMonthSelect/#statusYearSelect → auto-load both tables
- Add `loadBothTables(month, year)` function

### ⏳ Step 4: Excel Filter (upload-payroll.php)
- Modify `get_excel`: Accept `batch_id` param OR fall back to month/year
- `?mode=get_excel&batch_id=16` → direct file parse (faster)

### ⏳ Step 5: Payslip Filter (get-payroll.php)
- Add `batch_id` filter: `WHERE p.batch_id = ?`

### ⏳ Step 6: UI Events (HR/upload.php + js)
- `#statusMonthSelect/#statusYearSelect change` → `loadBothTables`
- "Check Status" button → same

### ⏳ Step 7: Test & Complete
- Default: Latest batch data in both tabs
- Filter: Select Jan 2026 → matching Excel/payslips only

**Status: 1/7**
