# Testing Guide for Multi-Store Implementation

## Test Flow

### 1. Login Test
1. Visit `http://127.0.0.1:4500/login`
2. Login with your credentials
3. **Expected Result**: You should be redirected to `/stores` (Stores Management Dashboard)

### 2. Stores Dashboard Test
On the Stores Dashboard, you should see:
- Three statistic cards: Total Stores, Active Stores, Total Products
- A list of your stores (if any exist)
- A "Create New Store" button in the top-right

### 3. Create New Store Test
1. Click "Create New Store"
2. Fill in the form:
   - Store Name: "Test Store"
   - Subdomain: "teststore"
   - Description: "My test store"
   - Keep "Store is active" checked
3. Click "Create Store"
4. **Expected Result**: 
   - Redirected to Stores Dashboard
   - See success message
   - New store appears in the list
   - Store is automatically set as "Currently Managing"

### 4. Store Switching Test
If you have multiple stores:
1. Click "Manage Store" on a different store
2. **Expected Result**: 
   - Redirected to `/app/dashboard`
   - See success message "Now managing: [Store Name]"
   - Navigation bar shows the selected store name in green badge

### 5. Store Context Test
1. From the app dashboard, try accessing:
   - Products (`/app/products`)
   - Categories (`/app/categories`)
   - Orders (`/app/orders`)
2. **Expected Result**: All pages work normally and show data for the selected store

### 6. Navigation Test
With a store selected:
1. Check the navigation bar shows:
   - Dashboard, Products, Categories, Orders, WhatsApp links
   - "More" dropdown with additional settings
   - Green badge showing current store name
2. Click on the store badge
3. **Expected Result**: Return to Stores Management Dashboard

### 7. No Store Selected Test
1. Visit Stores Dashboard
2. Clear the session (logout and login again)
3. Try to access `/app/dashboard` directly (paste URL)
4. **Expected Result**: 
   - Redirected to `/stores`
   - See warning message "Please select a store to manage first."

### 8. Edit Store Test
1. From Stores Dashboard, click "Edit" on a store
2. Modify the store name or description
3. Click "Update Store"
4. **Expected Result**: 
   - Redirected to Stores Dashboard
   - See success message
   - Changes are reflected in the store list

### 9. Delete Store Test
1. From Stores Dashboard, click "Delete" on a store
2. Confirm the deletion in the popup
3. **Expected Result**: 
   - Store is deleted
   - If it was the active store, you're no longer managing any store
   - Success message appears

### 10. Super Admin Test (if applicable)
If you have superadmin role:
1. Check the user dropdown menu
2. You should see "Super Admin" option
3. Click it to access super admin dashboard
4. **Expected Result**: Access to super admin features with additional stores management

## What to Check

### Visual Elements
- ✓ Store badge appears in navigation when store is selected
- ✓ Navigation menu changes based on store selection
- ✓ Store statistics are displayed correctly
- ✓ Active/Inactive badges show correct status
- ✓ "Currently Managing" indicator appears on the right store

### Functionality
- ✓ Can create new stores
- ✓ Can edit existing stores
- ✓ Can delete stores
- ✓ Can switch between stores
- ✓ Cannot access app routes without selecting a store
- ✓ All app features work within store context

### Edge Cases
- ✓ Direct URL access to `/app/dashboard` without store selection redirects correctly
- ✓ Deleting the currently active store clears the session
- ✓ Editing subdomain validates uniqueness
- ✓ Store ownership is enforced (can only manage own stores)

## Troubleshooting

### If you see errors:
1. Check `storage/logs/laravel.log` for error details
2. Clear cache: `php artisan cache:clear`
3. Clear view cache: `php artisan view:clear`
4. Restart the server

### If redirects aren't working:
1. Clear browser cache and cookies
2. Try in incognito/private mode
3. Check session configuration in `.env`

### If store selection doesn't persist:
1. Check session driver in `.env` (should be `file` or `database`)
2. Ensure `storage/framework/sessions` is writable
3. Clear sessions: `php artisan session:table` and migrate

## Success Criteria
✓ After login, users land on Stores Management Dashboard
✓ Users can create, edit, and delete stores
✓ Users must select a store before accessing app features
✓ Navigation shows store context clearly
✓ All existing features work within the selected store context
