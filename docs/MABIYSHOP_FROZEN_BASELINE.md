MABIY SHOP FROZEN BASELINE
==================================================

Treat all verified items below as FROZEN.

RULE:

Before proposing or modifying anything in a future task:

1. Read this file first.
2. Identify whether the requested change touches any frozen functionality.
3. If it does, prove with evidence that the frozen functionality is actually related to the new defect.
4. Never rewrite, re-audit, refactor, replace, or "improve" a frozen fix merely because another implementation appears cleaner.
5. Preserve existing behavior unless the new defect demonstrably requires a minimal change.
6. Never replace a verified Production implementation wholesale with a Local implementation.
7. Prefer integration into the current verified Production code.
8. One problem at a time.
9. Do not repeat previously solved work.
10. Do not deploy until static verification, package verification, backup/hash verification, and live regression verification are complete.

==================================================
1. ADDRESS RESOLVER V37 - FROZEN
==================================================

File:

system_operator/app/Services/AddressLocationResolver.php

Verified V37 SHA-256:

83998CE6C983F748193624CD6E9D92E42B3FD3B9AA226B2D67A0CB9F05CAE9C3

Config:

system_operator/config/address_location_aliases.php

Verified SHA-256:

BE2195FF3C2F8E0CFD0A446102F45162F80A1EC9C8858780BC58743A6480FA5E

Frozen V37 behavior includes:

- Runtime generic-area-title filtering.
- Protection against stale/polluted cached generic structural records.
- Cache key:
  address-location-resolver-index-v37
- STRONG_SCORE = 85
- UNIQUE_MARGIN = 20
- Existing district contradiction handling.
- Existing alias handling.
- Existing structured Mirpur matching.
- Existing nationwide compound-area matching.

Verified regression behavior:

section 07 mirpur
-> strong / Mirpur 7

section 07 mirpur dhaka
-> strong / Mirpur 7

section 10 mirpur
-> strong / Mirpur 10

section 10 mirpur dhaka
-> strong / Mirpur 10

mirpur 7 dhaka
-> strong / Mirpur 7

mirpur 11 dhaka
-> strong / Mirpur 11

dhaka mirpur
-> ambiguous

halishahar chattogram
-> strong / Halishahar

halishohor chattogram
-> strong / Halishahar

halishohar chittagong
-> strong / Halishahar

road dhaka
-> district_only

zoo road mirpur 1 dhaka
-> strong / Mirpur 1 / Zoo Road

Rail road Bagerhat Sadar Bagerhat
-> strong / Bagerhat Sadar / Rail road

IMPORTANT:

Never solve a POS/UI problem by changing the resolver unless evidence proves the resolver itself is the root cause.

==================================================
2. POS CUSTOMER SELECTION SYNCHRONIZATION - FROZEN
==================================================

Primary file:

system_operator/resources/views/backend/pages/product/pos.blade.php

The verified customer-selection synchronization fix must remain intact.

Required behavior:

- Customer-list AJAX requests use monotonically increasing request sequencing.
- Superseded requests are aborted where applicable.
- Stale success/error callbacks are ignored.
- The authoritative customer ID is re-read from:
  localStorage.current_user_id
  immediately before replacing customer options.
- Selected customer options are preserved during filtered searches.
- Selectpicker is refreshed.
- The authoritative customer selection is restored through Selectpicker.
- getCustomerList() returns the AJAX promise.
- Clearing customer search reloads the normal customer list.
- Initial customer-list loading is preserved.
- Existing customer result rendering remains intact.
- Production custom customer-search UI remains intact.
- Hidden #select_user remains the authoritative submitted customer field.

Never remove or bypass these protections.

==================================================
3. NEW-CUSTOMER TIMING FIX - FROZEN
==================================================

When a new customer is created:

1. Store the new customer ID in localStorage.current_user_id.
2. Refresh/get the customer list.
3. Wait until the new customer option actually exists.
4. Synchronize/select that option.
5. Trigger the existing customer change handler only when required.
6. Keep the visible Production customer-search label synchronized.
7. Add Address must continue using the authoritative #select_user.

Do NOT assume that populating customer name/phone fields means the customer select is synchronized.

Do NOT weaken the existing "You should select customer first." validation.

Do NOT replace authoritative #select_user validation with modal field values.

==================================================
4. POS UNIQUE-DISTRICT AMBIGUOUS HANDLING - FROZEN
==================================================

For resolver response:

match_type === "ambiguous"

If all valid candidates belong to exactly one district:

- Select/load that district.
- Load its Thana/Upazila options.
- Leave Thana/Upazila unselected.
- Preserve the ambiguity confirmation warning.

If candidates span multiple districts or there is no valid district:

- Keep the existing generic fallback behavior.

Never automatically select an ambiguous Thana.

Example:

dhaka mirpur

must preserve:

District = Dhaka
Thana = unselected
Thana options = loaded
status = ambiguous / confirmation required

This behavior is POS UI handling and must not be moved into the resolver unless independently proven necessary.

==================================================
5. ADD ADDRESS VALIDATION - FROZEN
==================================================

Existing validation must remain.

The authoritative customer comes from:

#select_user

If no valid customer is selected:

- Do not bypass validation.
- Do not silently infer a customer from modal fields.
- Do not submit an address against an arbitrary/stale customer.

The customer-selection state must remain synchronized among:

- #select_user
- localStorage.current_user_id
- visible customer UI
- submitted customer ID

==================================================
6. CUSTOMER CHANGE HANDLER - FROZEN
==================================================

The existing customer change handler must remain intact.

It is responsible for:

- reading the selected customer
- updating localStorage.current_user_id
- resetting previous customer address state where applicable
- loading customer cart
- loading customer addresses
- clearing customer state when -1 is selected

Do not duplicate this logic elsewhere unless absolutely necessary.

==================================================
7. CART / ORDER / SHIPPING / PATHAO - FROZEN
==================================================

Previously verified cart, order, shipping and Pathao behavior is frozen.

Do not modify these areas while solving unrelated address/POS UI issues.

Do not change:

- cart calculation
- order submission
- quantity behavior
- shipping behavior
- Pathao validation/integration
- customer/order association
- database/schema

unless exact evidence proves the new defect originates there.

==================================================
8. CHECKOUT / FRONTEND - FROZEN
==================================================

Protected frontend files include:

resources/assets/js/vue/components/Checkout.vue
resources/assets/js/vue/components/Header.vue
resources/assets/js/vue/store.js
resources/assets/js/vue/components/Product.vue

Do not modify these for a POS-only issue.

Protected verified hashes:

Checkout.vue
5CCA0326489E5567983EB12555A57799CFB56E173CC556809C623BF92E585363

Header.vue
EE20AC4C148C23DAD060148D1A50788283F586DB28CF6FF59B7E02CE740674C9

store.js
AE2D799259A2A8D6679B1E1BF9EB6055007269C733183C72152EC8BA98955412

Product.vue
C0D1CA741077FF62F53288FA71E26588447CC075F7942FABE5CCF486C4CC6E09

==================================================
9. BACKEND/API - FROZEN
==================================================

Protected files include:

system_operator/app/Http/Controllers/Api/ApiController.php
system_operator/app/Http/Controllers/Backend/PosController.php

Verified hashes:

ApiController.php
17120CEF23276EA12C20B45EA2750843FB313EE9D6EA9315437650BBD00F1C62

PosController.php
9E9B717E2D8F1D7CB2C89BD7DC2101285A73DC4D7D9F2B20346FB02D43ED0FD0

Do not change these for a POS Blade/UI issue unless evidence requires it.

==================================================
10. COMPILED FILES - FROZEN
==================================================

Compiled frontend bundles and their license files are frozen.

Do not rebuild frontend bundles unless the new defect demonstrably requires a frontend build.

A source-only POS Blade fix must not trigger an unrelated frontend rebuild.

==================================================
11. REQUIRED WORKFLOW FOR EVERY FUTURE ISSUE
==================================================

Before changing anything:

STEP 1
Read this frozen baseline.

STEP 2
Analyze the new defect read-only.

STEP 3
Return:

ROOT CAUSE
EVIDENCE
FILE
WHY
EXPECTED IMPACT
PROTECTED/FROZEN FILES
MINIMAL PATCH
REGRESSION RISKS
VERIFICATION PLAN

STEP 4
Do not modify files until the proposed patch is explicitly approved.

STEP 5
Before modifying a file:

- record current SHA-256
- create a live backup
- verify backup SHA-256
- verify exact patch scope

STEP 6
Apply only the approved minimal change.

STEP 7
Run:

- syntax validation
- git diff --check
- targeted regression tests
- protected hash comparison

STEP 8
Before packaging:

- confirm only intended files changed
- confirm no frozen file changed
- confirm package contains exactly intended files
- verify package integrity
- verify source/archive SHA-256

STEP 9
Before production deployment:

- upload package
- verify package SHA-256 on server
- extract to /tmp
- verify extracted file hash
- record final live hash
- create live backup
- deploy only intended file(s)

STEP 10
After deployment:

- verify live deployed SHA-256
- perform live regression tests
- explicitly test previously solved cases
- do not claim browser PASS unless actually observed

==================================================
12. REGRESSION TEST REQUIREMENT
==================================================

Every new POS/address change must re-check previously solved behavior relevant to the affected area.

At minimum, when POS customer/address code changes, verify:

- existing customer selected without reload
- customer search/filter
- customer selection persistence
- new customer creation without reload
- Add Address customer validation
- dhaka mirpur ambiguity
- section 07 mirpur
- section 10 mirpur
- mirpur 7 dhaka
- mirpur 11 dhaka
- road dhaka
- zoo road mirpur 1 dhaka

A failed previous case must never be silently accepted as a new baseline.

==================================================
13. HARD SAFETY RULE
==================================================

NEVER replace a verified Production file wholesale with a Local file unless a complete Production-vs-Local comparison proves that the Local file contains no required Production-only behavior.

If Production contains custom behavior, integrate the minimal approved fix into Production.

Never assume that a cleaner Local implementation is safer.

==================================================
14. CURRENT OPEN ISSUE
==================================================

Current unresolved issue:

English address works in POS, but equivalent Bengali address does not always resolve.

Example English:

House 10 Road 22 block K, banani Dhaka

Expected:
Dhaka / Banani / Road 22

Equivalent Bengali:

হাউস ১০ রোড ২২ ব্লক কে, বনানী ঢাকা

Current POS behavior:
Area could not be identified safely.

Pathao can accept a similar Bengali address and identify:

Habiganj / Madhabpur

Therefore investigate Bengali matching independently.

Do NOT modify the frozen V37 resolver or POS fixes until the exact root cause is proven.

==================================================
15. DOCUMENTATION RULE
==================================================

Whenever a fix is completed, append/update this document with:

- Date
- Issue
- Root cause
- Exact changed file(s)
- Exact behavior fixed
- Regression tests
- Final SHA-256
- Package SHA-256 if deployed
- Deployment status
- Frozen/protected files confirmed unchanged

This document is the mandatory first-read baseline for every future MabiY Shop Codex task.

END OF FROZEN BASELINE.
