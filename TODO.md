# Login Status Check - COMPLETE ✓

**Changes:**
- logsub.php: Added `u.status` to SELECT
- After password_verify: `if ($user['status'] !== 'active') { JSON error "Your account is suspended. Reach out to HR." }`

**Test:**
1. Set user inactive (edit-user.php toggle off → save)
2. Login with creds → see suspend msg
3. Toggle active → login success → HR/dashboard

All login checks complete! Deploy logsub.php.
