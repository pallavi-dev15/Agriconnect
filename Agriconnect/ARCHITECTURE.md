# SmartAgriConnect - System Architecture & Data Flow

## **System Architecture**

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT SIDE (Browser)                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌──────────────────────┐        ┌──────────────────────┐       │
│  │  mylistings.php      │        │  marketplace.php     │       │
│  │  (Farmer View)       │        │  (Buyer View)        │       │
│  │                      │        │                      │       │
│  │ - Add Crop Form      │        │ - Browse Products    │       │
│  │ - Delete Btn         │        │ - Search Bar         │       │
│  │ - Search Input       │        │ - Order Modal        │       │
│  │ - Crop Grid Display  │        │ - Cart Calculation   │       │
│  └──────────┬───────────┘        └──────────┬───────────┘       │
│             │                               │                    │
│  ┌──────────▼──────────────────────────────▼──────────┐         │
│  │           XMLHttpRequest Handler                   │         │
│  │     (Old Syntax - Not Fetch API)                   │         │
│  └──────────┬──────────────────────────────┬──────────┘         │
│             │                              │                    │
└─────────────┼──────────────────────────────┼────────────────────┘
              │                              │
              │ HTTP POST/GET               │ HTTP POST/GET
              │                              │
┌─────────────▼──────────────────────────────▼────────────────────┐
│                        SERVER SIDE (PHP)                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  API Endpoints (All return XML):                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ CROPS MANAGEMENT                                         │  │
│  │ ┌────────────────────────────────────────────────────┐  │  │
│  │ │ POST: api_add_crop.php                             │  │  │
│  │ │ GET:  api_get_crops.php                            │  │  │
│  │ │ POST: api_delete_crop.php                          │  │  │
│  │ │ GET:  api_search_crops.php                         │  │  │
│  │ └────────────────────────────────────────────────────┘  │  │
│  │                                                          │  │
│  │ ORDER MANAGEMENT                                         │  │
│  │ ┌────────────────────────────────────────────────────┐  │  │
│  │ │ POST: api_place_order.php                          │  │  │
│  │ │ GET:  api_get_orders.php                           │  │  │
│  │ │ POST: api_update_order_status.php                  │  │  │
│  │ └────────────────────────────────────────────────────┘  │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                   │
│              ↓ Session Verification ↓                            │
│              ↓ SQL Prepared Statements ↓                         │
│              ↓ Input Validation ↓                                │
│              ↓ HTML Escaping ↓                                   │
│                                                                   │
└─────────────────────────────┬──────────────────────────────────┘
                              │
┌─────────────────────────────▼──────────────────────────────────┐
│                    DATABASE (MySQL)                             │
├──────────────────────────────────────────────────────────────┤
│                                                                │
│  TABLE: users (existing)                                      │
│  ├─ id (PK)                                                  │
│  ├─ username                                                 │
│  ├─ password                                                 │
│  └─ usertype (farmer/buyer)                                  │
│                                                                │
│  TABLE: crops (NEW)                                           │
│  ├─ id (PK) ←────────┐                                        │
│  ├─ farmer_id (FK) ──┼──→ users.id                            │
│  ├─ crop_name        │                                        │
│  ├─ price            │                                        │
│  ├─ quantity         │                                        │
│  ├─ grade            │                                        │
│  ├─ location         │                                        │
│  ├─ description      │                                        │
│  ├─ image_url        │                                        │
│  ├─ created_at       │                                        │
│  └─ updated_at       │                                        │
│                      │                                        │
│  TABLE: orders (NEW) │                                        │
│  ├─ id (PK)          │                                        │
│  ├─ crop_id (FK) ────┘                                        │
│  ├─ buyer_id (FK) ──→ users.id                               │
│  ├─ farmer_id (FK) ─→ users.id                               │
│  ├─ quantity         │                                        │
│  ├─ total_price      │                                        │
│  ├─ order_status     │                                        │
│  ├─ order_date       │                                        │
│  ├─ delivery_address │                                        │
│  └─ notes            │                                        │
│                                                                │
└────────────────────────────────────────────────────────────┘
```

---

## **User Flow Diagram**

### **Farmer Flow**

```
┌─────────────┐
│   LOGIN     │
│  (Farmer)   │
└──────┬──────┘
       │
       ▼
┌─────────────────────────────┐
│ Dashboard (dashboard.php)   │
├─────────────────────────────┤
│ [Manage My Crops] [My Orders]
└──────┬──────────────────────┘
       │
       ├─────────────────────────────┐
       │                             │
       ▼                             ▼
┌──────────────────────┐    ┌──────────────────────┐
│ My Listings Page     │    │ My Orders Page       │
│ (mylistings.php)     │    │ (myorders.php)       │
├──────────────────────┤    ├──────────────────────┤
│ + Add New Crop       │    │ List of Orders       │
│ Search Crops         │    │ [Update Status]      │
│ [Delete] [Edit]      │    │ View Details         │
│ Crop Grid Display    │    │ pending → accepted   │
└──────────────────────┘    │         → shipped    │
       │                    │         → delivered  │
       │ AJAX Calls         └──────────────────────┘
       │
       ├── api_add_crop.php
       ├── api_get_crops.php
       ├── api_delete_crop.php
       ├── api_search_crops.php
       └── (All return XML)
```

### **Buyer Flow**

```
┌─────────────┐
│   LOGIN     │
│   (Buyer)   │
└──────┬──────┘
       │
       ▼
┌─────────────────────────────┐
│ Dashboard (dashboard.php)   │
├─────────────────────────────┤
│ [Explore Products] [My Orders]
└──────┬──────────────────────┘
       │
       ├─────────────────────────────┐
       │                             │
       ▼                             ▼
┌──────────────────────┐    ┌──────────────────────┐
│ Marketplace          │    │ My Orders Page       │
│ (marketplace.php)    │    │ (myorders.php)       │
├──────────────────────┤    ├──────────────────────┤
│ Search Crops         │    │ List of My Orders    │
│ Browse Products      │    │ Order Status:        │
│ [Order Now]          │    │   - Pending          │
│ Crop Grid Display    │    │   - Accepted         │
└──────────────────────┘    │   - Shipped          │
       │                    │   - Delivered        │
       │ AJAX Calls         └──────────────────────┘
       │
       ├── api_get_crops.php
       ├── api_search_crops.php
       └── api_place_order.php
           (All return XML)
```

---

## **Data Flow Example: Adding a Crop**

```
1. FARMER INTERACTION
   ┌─────────────────────────────┐
   │ mylistings.php              │
   │ User fills form:            │
   │ - Crop Name: "Rice"         │
   │ - Price: 25.50              │
   │ - Quantity: 1000            │
   │ Clicks [Add Crop]           │
   └──────────────┬──────────────┘
                  │
2. FORM SUBMISSION
   ┌──────────────▼──────────────────────────────┐
   │ JavaScript Handler                          │
   │ - Collects form data                        │
   │ - Encodes as URL params                     │
   │ - Creates XMLHttpRequest                    │
   └──────────────┬──────────────────────────────┘
                  │
                  ▼
   POST /Agriconnect/api_add_crop.php
   crop_name=Rice&price=25.50&quantity=1000
   
3. SERVER PROCESSING
   ┌──────────────────────────────────────┐
   │ api_add_crop.php                     │
   │ ✓ Check session ($_SESSION['id'])    │
   │ ✓ Verify user is farmer              │
   │ ✓ Validate inputs                    │
   │ ✓ Sanitize inputs                    │
   │ ✓ Prepare SQL statement              │
   │ ✓ Bind parameters                    │
   │ ✓ Execute INSERT                     │
   │ ✓ Generate XML response              │
   └──────────────┬──────────────────────┘
                  │
4. DATABASE WRITE
   ┌──────────────▼──────────────────────┐
   │ MySQL INSERT                        │
   │ INSERT INTO crops (                 │
   │   farmer_id,                        │
   │   crop_name,                        │
   │   price,                            │
   │   quantity                          │
   │ ) VALUES (5, 'Rice', 25.50, 1000)   │
   │                                     │
   │ New crop_id: 42                     │
   └──────────────┬──────────────────────┘
                  │
5. XML RESPONSE
   ┌──────────────▼──────────────────────┐
   │ <?xml version="1.0"?>               │
   │ <response>                          │
   │   <success>true</success>           │
   │   <message>Crop added               │
   │             successfully</message>  │
   │   <crop_id>42</crop_id>             │
   │ </response>                         │
   └──────────────┬──────────────────────┘
                  │
6. CLIENT UPDATE
   ┌──────────────▼──────────────────────┐
   │ JavaScript receives XML             │
   │ - Parses response                   │
   │ - Checks success = true             │
   │ - Shows success message             │
   │ - Reloads crops list via AJAX       │
   │ - New crop appears in grid          │
   └──────────────────────────────────────┘
```

---

## **Data Flow Example: Placing an Order**

```
1. BUYER INTERACTION
   ┌──────────────────────────────┐
   │ marketplace.php              │
   │ Clicks [Order Now] on crop   │
   │ Modal appears with:          │
   │ - Crop details (pre-filled)  │
   │ - Quantity input             │
   │ - Delivery address           │
   │ - Special notes              │
   │                              │
   │ Enters qty: 50 kg            │
   │ Auto-calc: 50 × 25 = 1250    │
   │ Clicks [Place Order]         │
   └──────────────┬───────────────┘
                  │
2. FORM SUBMISSION
   ┌──────────────▼──────────────────────────────┐
   │ JavaScript Handler                          │
   │ - Validates quantity (≤ available)          │
   │ - Validates address (not empty)             │
   │ - Creates XMLHttpRequest                    │
   └──────────────┬──────────────────────────────┘
                  │
                  ▼
   POST /Agriconnect/api_place_order.php
   crop_id=42&quantity=50&delivery_address=...
   
3. SERVER PROCESSING
   ┌──────────────────────────────────────┐
   │ api_place_order.php                  │
   │ ✓ Check session (buyer)              │
   │ ✓ Get crop details                   │
   │ ✓ Verify quantity available          │
   │ ✓ Calculate total price              │
   │ ✓ INSERT into orders table           │
   │ ✓ UPDATE crops qty (1000 → 950)      │
   │ ✓ Generate response                  │
   └──────────────┬──────────────────────┘
                  │
4. DATABASE WRITES
   ┌──────────────▼──────────────────────┐
   │ INSERT INTO orders (...)            │
   │ VALUES (42, buyer_id, farmer_id...) │
   │ Order ID: 5                         │
   │                                     │
   │ UPDATE crops                        │
   │ SET quantity = 950                  │
   │ WHERE id = 42                       │
   └──────────────┬──────────────────────┘
                  │
5. XML RESPONSE
   ┌──────────────▼──────────────────────┐
   │ <response>                          │
   │   <success>true</success>           │
   │   <message>Order placed...</message>│
   │   <order_id>5</order_id>            │
   │   <total_price>1250.00</total_price>│
   │ </response>                         │
   └──────────────┬──────────────────────┘
                  │
6. CLIENT UPDATE
   ┌──────────────▼──────────────────────┐
   │ - Show success message              │
   │ - Close order modal                 │
   │ - Reload crop listings (qty updated)│
   │ - Can redirect to myorders.php      │
   └──────────────────────────────────────┘
```

---

## **Session Management Flow**

```
┌─────────────────────────────────────┐
│ User visits login.php               │
└─────────────────────┬───────────────┘
                      │
                      ▼
        ┌─────────────────────────┐
        │ Enter credentials       │
        │ Select role (farmer/buyer)
        └──────────┬──────────────┘
                   │
                   ▼
        ┌──────────────────────────────┐
        │ POST to authenticate.php     │
        │ - Query users table          │
        │ - Verify password            │
        │ - Verify usertype matches    │
        └──────────┬───────────────────┘
                   │
        ┌──────────▼──────────────┐
        │ Login Successful?       │
        └─────┬──────────┬────────┘
              │          │
          YES │          │ NO
              │          ▼
              │    Redirect to login?error=1
              │
              ▼
    ┌─────────────────────────────┐
    │ $_SESSION set:              │
    │ - $_SESSION['id']           │
    │ - $_SESSION['username']     │
    │ - $_SESSION['usertype']     │
    └─────────────┬───────────────┘
                  │
                  ▼
    ┌──────────────────────────────┐
    │ Redirect to dashboard.php    │
    └─────────────┬────────────────┘
                  │
                  ▼
    ┌──────────────────────────────┐
    │ Session Active!              │
    │ - Can access protected pages │
    │ - API calls verify session   │
    │ - Logout clears session      │
    └──────────────────────────────┘
```

---

## **Request/Response Cycle (AJAX)**

```
CLIENT (Browser)          │          SERVER (PHP)
                          │
  ┌─────────────────┐     │     ┌──────────────────┐
  │ Create XHR      │     │     │                  │
  │ Object          │     │     │                  │
  └────────┬────────┘     │     │                  │
           │              │     │                  │
           │ xhr.open()   │     │                  │
           │ xhr.onready- │     │                  │
           │ statechange  │     │                  │
           │              │     │                  │
           ├─────────────►│POST api_add_crop.php   │
           │ HTTP POST    │ + Parameters           │
           │              │     │                  │
           │              │     ├─► Parse POST     │
           │              │     ├─► Validate data  │
           │              │     ├─► Check session  │
           │              │     ├─► Query DB       │
           │              │     ├─► Insert/Update  │
           │              │     ├─► Create XML     │
           │              │     │                  │
           │              │◄────┤ HTTP 200 OK      │
           │◄─────────────┤ XML Response          │
           │ readyState=4 │     │                  │
           │ status=200   │     │                  │
           │              │     │                  │
           ├─► Parse XML  │     │                  │
           ├─► Check OK   │     │                  │
           ├─► Update DOM │     │                  │
           └─► Show UI    │     │                  │
```

---

## **Security Flow**

```
API Request Arrives
        │
        ▼
┌──────────────────────┐
│ Session Check        │ ← Verify $_SESSION['id'] exists
├──────────────────────┤
│ ✓ Logged in?        │
└──────────┬───────────┘
           │
        YES│
           ▼
┌──────────────────────┐
│ User Type Check      │ ← Verify usertype matches endpoint
├──────────────────────┤
│ ✓ Is farmer?        │ (for farmer-only endpoints)
│ ✓ Is buyer?         │ (for buyer-only endpoints)
└──────────┬───────────┘
           │
        YES│
           ▼
┌──────────────────────┐
│ Input Validation     │ ← Check required fields, data types
├──────────────────────┤
│ ✓ All fields present?│
│ ✓ Valid types?      │
│ ✓ Valid ranges?     │
└──────────┬───────────┘
           │
        YES│
           ▼
┌──────────────────────┐
│ Input Sanitization   │ ← Escape/encode user input
├──────────────────────┤
│ htmlspecialchars()   │
│ floatval()           │
│ intval()             │
└──────────┬───────────┘
           │
        YES│
           ▼
┌──────────────────────┐
│ Prepared Statements  │ ← Prevent SQL injection
├──────────────────────┤
│ $stmt->bind_param()  │
│ $stmt->execute()     │
└──────────┬───────────┘
           │
        YES│
           ▼
┌──────────────────────┐
│ Ownership Check      │ ← Verify ownership (if needed)
├──────────────────────┤
│ farmer_id = id?      │
│ buyer_id = id?       │
└──────────┬───────────┘
           │
        YES│
           ▼
┌──────────────────────┐
│ Business Logic       │ ← Process request
├──────────────────────┤
│ INSERT/UPDATE/DELETE │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│ XML Escape Output    │ ← Prevent XML injection
├──────────────────────┤
│ htmlspecialchars()   │
│ <![CDATA[...]]>      │
└──────────┬───────────┘
           │
           ▼
    Return XML Response
```

---

## **Summary**

This system uses:
- **Traditional AJAX** (XMLHttpRequest, not Fetch)
- **Session-based** security
- **XML** for API responses
- **Prepared statements** to prevent SQL injection
- **Input validation** at every step
- **Role-based** access control
- **Real-time** quantity tracking

All communication follows the XML request/response pattern, making it predictable and consistent across all endpoints.
