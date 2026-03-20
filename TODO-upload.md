# TODO: Fix Y-Axis Scroll on upload.html

## Plan Steps:
- [x] Step 1: Create TODO.md with steps (done).
- [x] Step 2: Edit pages/upload.html - Remove `h-screen overflow-hidden` from flex div.
- [x] Step 3: Update main to `flex-1 p-6` without overflow constraints conflicting (main was already flex-1 implicitly; kept p-6 overflow-y-auto).
- [x] Step 4: Optimize summary grid for better space: `grid-cols-1 md:grid-cols-3` (already optimal).
- [x] Step 5: Add `min-h-screen` to body.
- [x] Step 6: Added `flex-1` to main to fill screen width properly (address feedback: content packed left).
- [ ] Step 7: Final test and complete.

Progress: Layout now fills screen: sidebar fixed, main expands to fill rest with internal scroll. 
