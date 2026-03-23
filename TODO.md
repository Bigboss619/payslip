# Fix Save Button Does Nothing After File Selection/Preview

## Approved Plan Steps:
1. **Add error handling and logging to js/upload.js savePayroll()** - Wrap fetch in try/catch, show status/errors, console.log batch_id/response.
2. **Ensure saveBtn properly enabled** - Verify previewData length check.
3. **Test upload → preview → save flow** - Use sample Excel, check console/Network/DB.
4. **Verify backend** - Check PHP errors, session, DB insert.
5. **Reload data & confirm table update**.

## Progress:
- [x] Analyzed files (HR/upload.php, js/upload.js, includes/upload-payroll.php, get-payroll.php)
- [x] Created diagnosis & plan
- [x] Edit js/upload.js (added try/catch, console logs, button state mgmt, error alerts to savePayroll() & showPreview())
- [ ] Test flow: Upload sample → check console → click Save → observe Network tab/response/status
- [ ] Fix any remaining backend/DB issues if errors appear

