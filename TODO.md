# HR Profile Page Implementation TODO

## Plan Overview:
**Goal:** Make HR/profile.php fully functional:
- Load user data from $_SESSION/DB.
- Edit profile (name, email).
- Change password (verify current, hash new).
- Backend handler includes/hrprofile.php.

**Information Gathered:**
- HR/profile.php: Static mockup (header/nav, hardcoded Emmanuel data, forms no action).
- includes/hrprofile.php: Empty.
- DB users: name, staff_id, email, password, role (no phone/dept; mock those).
- Session: user_id, name, staff_id, email, role (from logsub.php).

**File Updates:**
1. includes/hrprofile.php: Backend - update profile/PW.
2. HR/profile.php: Dynamic data, forms POST hrprofile.php, session protect.

**Dependent Files:** config/config.php, header.php (session_start).

**Followup:** Test edit/save, pw change. Add dept/phone if DB updated.

**Steps:**
- [ ] Step 1: Implement includes/hrprofile.php (update profile + change pw)
- [ ] Step 2: Update HR/profile.php (dynamic data, forms, protection)
- [ ] Step 3: Test (login HR, edit profile, change pw)

**Ready to proceed?**
