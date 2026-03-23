# Upload Preview Before Insert & Remove mockData

## Status: In Progress

### Step 1: [DONE ✅] Backend upload-payroll.php (preview/save modes)
- Add mode='preview' (parse/store session) vs 'save' (insert).
- Use session for preview_data keyed by batch_id.

### Step 2: [DONE ✅] js/upload.js (preview/save/cancel flow complete)
- handleFileUpload: send mode=preview.
- savePayroll(): POST mode=save with batch_id.
- Add global currentBatchId.
- cancelPreview(): optional cleanup.

### Step 3: [DONE ✅] HR/upload.php (scrollable preview table)

### Step 4: [DONE ✅] Verified flow & no mockData

### Step 5: [COMPLETE] Task done

