# Hierarchical Navigation System with Dashboards

## Overview
The application implements a 3-level hierarchical navigation system with dedicated dashboards at each level, providing comprehensive overview and management capabilities.

## Navigation Flow with Dashboards

### Level 1: Global Workspace Management
**Route Pattern:** `workspaces.*`
**When:** First login or no workspace selected

**Sidebar Shows:**
- **Dashboard** (global overview of ALL workspaces)
- My Workspaces (list view)
- Create Workspace

**Dashboard Features:**
- Total workspaces count (active/inactive)
- Total stores across all workspaces
- Total products across all stores
- Total orders with new orders (last 7 days)
- Total revenue from completed orders
- Pending orders count
- Quick action buttons

**User Actions:**
- View dashboard overview
- View/manage all workspaces
- Create new workspace
- Edit workspace
- Delete workspace
- Switch to a workspace (redirects to Level 2)

---

### Level 2: Workspace-Specific Store Management
**Route Pattern:** `stores.dashboard`, `stores.create`, `stores.edit`
**When:** Workspace selected, but no store selected

**Sidebar Shows:**
- Back to Workspaces (navigation link)
- **Dashboard** (workspace-specific overview)
- My Stores (list view for this workspace)
- Create Store

**Dashboard Features:**
- Total stores in this workspace
- Active stores count
- Total products in workspace stores
- Total orders from workspace stores
- New orders (last 7 days) for this workspace
- Total revenue from this workspace
- Pending orders for this workspace
- Quick action buttons

**Context Indicator:**
Shows current workspace name in blue badge at the top of sidebar

**User Actions:**
- View workspace dashboard overview
- View stores in current workspace
- Create new store
- Edit store
- Delete store
- Setup custom domain
- Manage Store (redirects to Level 3)

---

### Level 3: Store Features (App)
**Route Pattern:** `app.*`
**When:** Both workspace AND store are selected

**Sidebar Shows:**
- Back to Stores (navigation link)
- Dashboard (store-specific)
- WhatsApp Accounts
- Orders
- Products (with submenu)
  - All Products
  - Create Product
  - Categories
- Website Customization
- Pixel Connect
- Social Media API (with submenu)
- AI API Integration (with submenu)
- System Connect

**Context Indicator:**
Shows current store name in green at the top of sidebar

---

## Dashboard Views

Each level has two view modes:

### Overview Mode (Default)
- Statistics cards with key metrics
- Charts and visualizations
- Quick action buttons
- Accessed via "Dashboard" link

### List Mode
- Detailed list of workspaces/stores
- Management actions (edit, delete, switch)
- Search and filter capabilities
- Accessed via "My Workspaces" or "My Stores" link

---

## Implementation Details

### Routes
```php
// Workspace Level
GET /workspaces/dashboard               -> Overview (default)
GET /workspaces/dashboard?view=list     -> List view

// Store Level
GET /stores                             -> Overview (default)
GET /stores?view=list                   -> List view
```

### Controllers

**WorkspaceController::dashboard()**
- Calculates global statistics across ALL workspaces
- Returns either overview or list view based on query parameter
- Statistics include: workspaces, stores, products, orders, revenue

**StoreManagementController::dashboard()**
- Calculates workspace-specific statistics
- Filters data by active workspace
- Returns either overview or list view
- Statistics include: stores, products, orders, revenue for the workspace

### View Files
```
resources/views/
├── workspaces/
│   ├── overview.blade.php   (Dashboard overview for all workspaces)
│   ├── list.blade.php       (List/management view)
│   ├── create.blade.php
│   └── edit.blade.php
└── stores/
    ├── overview.blade.php   (Dashboard overview for workspace stores)
    ├── list.blade.php       (List/management view)
    ├── create.blade.php
    └── edit.blade.php
```

---

## Statistics Breakdown

### Level 1 (Global Workspace Dashboard)
- Total Workspaces
- Active Workspaces
- Total Stores (all workspaces)
- Total Products (all stores)
- Total Orders (all stores)
- New Orders (last 7 days, all stores)
- Total Revenue (completed orders, all stores)
- Pending Orders (all stores)

### Level 2 (Workspace Store Dashboard)
- Total Stores (in this workspace)
- Active Stores (in this workspace)
- Total Products (workspace stores)
- Total Orders (workspace stores)
- New Orders (last 7 days, workspace stores)
- Total Revenue (completed orders, workspace stores)
- Pending Orders (workspace stores)

### Level 3 (Store App Dashboard)
- Store-specific metrics
- WhatsApp conversations
- Product performance
- Order management
- Revenue tracking

---

## Implementation Details

### Authentication Flow
1. **Login:** User is redirected to `workspaces.dashboard`
2. **Registration:** New user is redirected to `workspaces.dashboard`
3. **Dashboard route:** Always redirects to `workspaces.dashboard`

### Middleware Protection
- **Workspace routes:** Only require `auth` middleware
- **Store management routes:** Require `auth` + `require.workspace` middleware
- **App routes:** Require `auth` + `require.workspace` + `require.store` middleware

### Session Management
- `active_workspace_id` - Stores currently selected workspace
- `active_store_id` - Stores currently selected store

### Context Detection
The sidebar automatically detects context using:
- Route patterns (`request()->routeIs()`)
- Session variables
- Shared view variables (`$activeWorkspace`, `$activeStore`)

### Navigation Breadcrumbs
Each level provides a "back" button:
- Level 3 → Level 2: "Back to Stores"
- Level 2 → Level 1: "Back to Workspaces"

### User Profile Menu
The dropdown menu shows contextual options:
- My Workspaces (always visible)
- My Stores (only when workspace is selected)
- Profile Settings
- Log Out

---

## User Experience Flow

### First Time User
```
1. Register → Workspace Management
2. Create Workspace → Stay on Workspace Management
3. Switch to Workspace → Store Management
4. Create Store → Stay on Store Management
5. Manage Store → App Dashboard (full features)
```

### Returning User
```
1. Login → Workspace Management
2. (If last session had selections) Can resume or select different workspace/store
3. Quick access via context selector at top of sidebar
```

---

## Visual Indicators

### Color Coding
- **Blue:** Workspace context
- **Emerald/Green:** Store context

### Context Selector
Shows at top of sidebar when context is active:
- Displays workspace/store name
- Click to switch/manage
- Hover shows switch icon

### Active States
- Navigation items highlight in corresponding context color
- "Currently Managing" badge on active store/workspace

---

## Key Files Modified

1. **resources/views/layouts/customer.blade.php**
   - Dynamic sidebar based on route context
   - Context-aware header
   - Conditional user menu

2. **app/Http/Controllers/Auth/AuthenticatedSessionController.php**
   - Login redirects to `workspaces.dashboard`

3. **app/Http/Controllers/Auth/RegisteredUserController.php**
   - Registration redirects to `workspaces.dashboard`

---

## Benefits

1. **Clear Hierarchy:** Users understand the organizational structure
2. **Contextual Navigation:** Sidebar shows only relevant options
3. **Easy Navigation:** Back buttons at each level
4. **Visual Feedback:** Color coding and badges show current context
5. **Scalability:** Easy to add more levels or features per level
