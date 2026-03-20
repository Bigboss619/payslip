# Payslip Login Defaults Implementation Plan

## Steps:
- [x] Step 1: Add default user credentials to index.html login inputs (email: user@example.com, password: userpass)
- [x] Step 2: Update js/login.js with hardcoded credential validation and role-based redirects:
  - user@example.com / userpass → pages/dashboard.html
  - hr@example.com / hrpass → HR/dashboard.html
  - Fix redirect path typo
  - Handle invalid credentials
- [ ] Step 3: Test logins with both sets of credentials
- [ ] Step 4: Demo with browser open command

**Status: Starting implementation...**
