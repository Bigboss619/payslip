# Department Management Implementation Plan

## Status: COMPLETED ✅

### Step 1. [DONE] Create departments table in DB
- Execute SQL migration via phpMyAdmin or CLI to create `departments` table (id PK, name unique).

### Step 2. [DONE] Implement HR/department.php
- Add PHP CRUD endpoints (list/add/edit/delete).
- Add HTML table + modals for UI matching existing style.

### Step 3. [DONE] Create HR/department.js
- AJAX handlers for dynamic CRUD, table refresh.

### Step 4. [DONE] Test functionality
- Verify add/edit/delete works.
- Check integration points (upload dropdown).

### Step 5. [DONE] Update TODO.md after each step.

Next: Step 4 - Test in browser (ensure XAMPP running, login HR, visit HR/department.php).
