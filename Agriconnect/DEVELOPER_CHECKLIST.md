# SmartAgriConnect - Developer's Setup Checklist

## **Pre-Implementation Verification**

### **Environment Requirements**
- [ ] XAMPP/LAMP/WAMP stack installed
- [ ] PHP 7.0+ installed
- [ ] MySQL 5.7+ installed
- [ ] Apache running
- [ ] MySQL service running

### **Workspace Setup**
- [ ] Project folder: `c:\xampp\htdocs\WTproject\Agriconnect\`
- [ ] All files located in the correct directory
- [ ] `.php` extension not blocked
- [ ] File permissions set correctly (readable/writable)

---

## **Step 1: Database Setup (CRITICAL)**

### **Verify Database Exists**
- [ ] MySQL database `agri_db` exists
  ```sql
  SHOW DATABASES;
  ```
- [ ] Database contains `users` table from previous setup
  ```sql
  USE agri_db;
  SHOW TABLES;
  ```

### **Create Required Tables**

Run these SQL commands in phpMyAdmin or MySQL CLI:

```sql
-- For crops table
CREATE TABLE IF NOT EXISTS crops (
    id INT PRIMARY KEY AUTO_INCREMENT,
    farmer_id INT NOT NULL,
    crop_name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    grade VARCHAR(50),
    location VARCHAR(150),
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- For orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    crop_id INT NOT NULL,
    buyer_id INT NOT NULL,
    farmer_id INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    order_status VARCHAR(50) DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivery_address TEXT,
    notes TEXT,
    FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### **Verify Tables Created**
```sql
SHOW TABLES;  -- Should show: users, crops, orders
DESCRIBE crops;
DESCRIBE orders;
```
- [ ] `crops` table created successfully
- [ ] `orders` table created successfully
- [ ] All columns present
- [ ] Foreign keys configured

### **Check Existing Users**
```sql
SELECT * FROM users;
```
- [ ] At least 1 farmer account exists
- [ ] At least 1 buyer account exists
- [ ] Accounts have correct `usertype` values

---

## **Step 2: File Verification**

### **Backend API Files**
- [ ] `api_add_crop.php` exists and is readable
- [ ] `api_get_crops.php` exists and is readable
- [ ] `api_delete_crop.php` exists and is readable
- [ ] `api_search_crops.php` exists and is readable
- [ ] `api_place_order.php` exists and is readable
- [ ] `api_get_orders.php` exists and is readable
- [ ] `api_update_order_status.php` exists and is readable

### **Frontend Pages**
- [ ] `mylistings.php` exists and is readable
- [ ] `marketplace.php` exists and is readable
- [ ] `myorders.php` exists and is readable
- [ ] `dashboard.php` updated with new links

### **Database Connection**
- [ ] `db.php` exists with correct connection:
  ```php
  $conn = new mysqli("localhost", "root", "", "agri_db");
  ```
- [ ] Database credentials match your setup

### **Documentation Files**
- [ ] `database_setup.sql` created
- [ ] `SETUP_GUIDE.md` created
- [ ] `QUICK_REFERENCE.md` created
- [ ] `ARCHITECTURE.md` created

---

## **Step 3: Server Configuration Check**

### **PHP Configuration**
- [ ] `session.auto_start` or `session_start()` used
- [ ] `extension=mysqli` enabled in php.ini
- [ ] `error_reporting` set appropriately

### **File Permissions**
```bash
# In terminal/command line
ls -la /xampp/htdocs/WTproject/Agriconnect/
```
- [ ] All files readable (644 or 755)
- [ ] Directories writable (755)

### **Apache Configuration**
- [ ] `.php` files execute (not downloaded)
- [ ] Rewrite module enabled (if using .htaccess)
- [ ] Document root points to correct directory

---

## **Step 4: Session Management Verification**

### **Test Session Functionality**

1. Create test file: `test_session.php`
```php
<?php
session_start();
$_SESSION['test'] = 'working';
echo "Session ID: " . session_id();
echo "Session Data: " . $_SESSION['test'];
?>
```

- [ ] File created in `Agriconnect/` folder
- [ ] Accessible via browser
- [ ] Shows session ID and "working"
- [ ] Session persists between page reloads

2. Test session in API:
   - [ ] Login as farmer
   - [ ] Check that `api_get_crops.php` works
   - [ ] Check browser console for AJAX responses

---

## **Step 5: AJAX/XMLHttpRequest Verification**

### **Test XMLHttpRequest**

1. Create test file: `test_ajax.html`
```html
<!DOCTYPE html>
<html>
<head>
    <title>AJAX Test</title>
</head>
<body>
    <button onclick="testAjax()">Test AJAX</button>
    <div id="result"></div>
    
    <script>
    function testAjax() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById('result').innerHTML = 'AJAX Works!<br>' + xhr.responseText;
            }
        };
        xhr.open('GET', 'api_get_crops.php', true);
        xhr.send();
    }
    </script>
</body>
</html>
```

- [ ] File created
- [ ] Accessible via browser (logged in)
- [ ] Button click works
- [ ] XML response received
- [ ] No console errors

### **Browser Console Check**
- [ ] Open DevTools (F12)
- [ ] Go to Console tab
- [ ] No JavaScript errors should appear
- [ ] Check Network tab for API calls

---

## **Step 6: Database Connectivity Test**

### **Test PHP-MySQL Connection**

1. Create test file: `test_db.php`
```php
<?php
require 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Test crops table
$result = $conn->query("SELECT COUNT(*) as count FROM crops");
$row = $result->fetch_assoc();
echo "Crops in database: " . $row['count'];

$conn->close();
?>
```

- [ ] File created
- [ ] No connection errors
- [ ] Shows crop count (0 initially)

### **Verify Foreign Keys**
```sql
-- Check foreign keys are working
SHOW CREATE TABLE crops;
SHOW CREATE TABLE orders;
```

- [ ] `farmer_id` references `users.id`
- [ ] `buyer_id` references `users.id`
- [ ] `crop_id` references `crops.id`

---

## **Step 7: Authentication Testing**

### **Login As Farmer**
- [ ] Go to `login.php`
- [ ] Enter farmer credentials
- [ ] Select "farmer" as user type
- [ ] Redirects to `dashboard.php`
- [ ] Session variables set (`$_SESSION['username']`, `$_SESSION['usertype']`)

### **Verify Session in Code**
```php
<?php
session_start();
echo $_SESSION['id'];
echo $_SESSION['username'];
echo $_SESSION['usertype'];
?>
```

- [ ] All three variables have values
- [ ] `usertype` is 'farmer'

### **Test Logout**
- [ ] Click "Logout" link
- [ ] Redirected to `login.php`
- [ ] Session cleared
- [ ] Cannot access protected pages

---

## **Step 8: Farmer Functionality Testing**

### **Access Farmer Pages**
- [ ] Login as farmer
- [ ] Click "Manage My Crops" in dashboard
- [ ] Redirects to `mylistings.php`
- [ ] Page loads without errors

### **Add Crop Test**
- [ ] Click "+ Add New Crop" button
- [ ] Modal form appears
- [ ] Fill all fields:
  - Crop Name: "Test Crop"
  - Price: "100"
  - Quantity: "500"
  - Grade: "A"
  - Location: "Test Location"
  - Description: "Test description"
- [ ] Click "Add Crop"
- [ ] Success message appears
- [ ] Modal closes
- [ ] Crop appears in grid
- [ ] Crop saved in database:
  ```sql
  SELECT * FROM crops WHERE crop_name = 'Test Crop';
  ```

### **View Crops**
- [ ] Refresh page
- [ ] Crops still visible
- [ ] Data matches database
- [ ] Images display (or "No Image" placeholder)

### **Search Crops**
- [ ] Enter "Test" in search box
- [ ] Click "Search"
- [ ] Only matching crops appear
- [ ] Click "Clear"
- [ ] All crops appear again

### **Delete Crop**
- [ ] Click "Delete" button on a crop
- [ ] Confirmation dialog appears
- [ ] Accept deletion
- [ ] Success message
- [ ] Crop removed from grid
- [ ] Crop removed from database:
  ```sql
  SELECT COUNT(*) FROM crops WHERE crop_name = 'Test Crop';
  ```

### **Check Orders**
- [ ] Click "My Orders" in navigation
- [ ] Page loads (no orders yet)
- [ ] Shows "No orders" message

---

## **Step 9: Buyer Functionality Testing**

### **Login As Buyer**
- [ ] Logout as farmer
- [ ] Login as buyer
- [ ] Dashboard shows buyer view
- [ ] "Explore Products" button visible

### **Access Marketplace**
- [ ] Click "Explore Products"
- [ ] Redirects to `marketplace.php`
- [ ] Shows all crops (including farmer's crops)
- [ ] Each crop shows:
  - [ ] Name
  - [ ] Price
  - [ ] Quantity
  - [ ] Grade
  - [ ] Location
  - [ ] Description

### **Search Crops**
- [ ] Search by crop name
- [ ] Search by location
- [ ] Results filter correctly
- [ ] "Clear" button resets search

### **Place Order**
- [ ] Click "Order Now" on a crop
- [ ] Modal appears with crop details
- [ ] Enter quantity: "50"
- [ ] Total price auto-calculates
- [ ] Enter delivery address: "123 Test St"
- [ ] Click "Place Order"
- [ ] Success message with Order ID
- [ ] Modal closes
- [ ] Check database:
  ```sql
  SELECT * FROM orders WHERE buyer_id = (SELECT id FROM users WHERE username = 'buyer_username');
  ```

### **Verify Quantity Update**
- [ ] Check crop quantity before order
- [ ] Place order for 50 kg
- [ ] Check crop quantity after
- [ ] Should be reduced by 50
- [ ] Database reflects change

### **Try Invalid Order**
- [ ] Try to order more than available quantity
- [ ] Error message appears
- [ ] Order not created

### **Check My Orders**
- [ ] Click "My Orders"
- [ ] See all buyer's orders
- [ ] Shows:
  - Order ID
  - Crop name
  - Farmer name
  - Quantity
  - Total price
  - Delivery address
  - Status (pending)

---

## **Step 10: Order Management Testing**

### **Farmer Updates Order Status**
- [ ] Login as farmer
- [ ] Go to "My Orders"
- [ ] See order from buyer
- [ ] Click "Update Status"
- [ ] Modal appears
- [ ] Select "accepted"
- [ ] Click "Update Status"
- [ ] Status changes in list
- [ ] Database updated:
  ```sql
  SELECT order_status FROM orders WHERE id = 1;
  ```

### **Progress Order Through Status**
- [ ] Update to "shipped"
- [ ] Update to "delivered"
- [ ] Status reflects at each step

### **Buyer Views Updated Status**
- [ ] Login as buyer
- [ ] Go to "My Orders"
- [ ] See order status has changed
- [ ] Status matches farmer's updates in real-time

---

## **Step 11: Security Testing**

### **Session Security**
- [ ] Try accessing `api_add_crop.php` without login (direct URL)
- [ ] Should show error or redirect
- [ ] Cannot add crops without session

### **Role-Based Access**
- [ ] Login as buyer
- [ ] Try accessing farmer endpoints (via URL/console)
- [ ] Should deny access

### **SQL Injection Test**
- [ ] Try searching: `" OR "1"="1`
- [ ] Should not break database
- [ ] Display no results or sanitized results

### **CSRF Protection**
- [ ] Verify all POST requests use proper headers
- [ ] Check for CSRF tokens (if implemented)

---

## **Step 12: Performance Testing**

### **Page Load Times**
- [ ] `mylistings.php` with 50+ crops: < 2 seconds
- [ ] `marketplace.php` with 100+ crops: < 3 seconds
- [ ] Search results: < 1 second

### **Database Optimization**
- [ ] Verify indexes exist:
  ```sql
  SHOW INDEX FROM crops;
  SHOW INDEX FROM orders;
  ```

### **AJAX Response Times**
- [ ] API calls complete in < 500ms
- [ ] No timeout errors in console

---

## **Step 13: Error Handling Testing**

### **Test Error Scenarios**
- [ ] Database connection failure:
  ```php
  // Temporarily change db.php connection
  ```
  - Should show graceful error

- [ ] Missing required fields:
  - Try adding crop with empty name
  - Should show validation error

- [ ] Invalid data types:
  - Try entering text in price field
  - Should be rejected or converted

- [ ] Network timeout:
  - Simulate with DevTools Network throttling
  - Should show appropriate message

---

## **Step 14: Cross-Browser Testing**

- [ ] Google Chrome
  - [ ] AJAX works
  - [ ] Modals function
  - [ ] Search works

- [ ] Mozilla Firefox
  - [ ] Same tests

- [ ] Microsoft Edge
  - [ ] Same tests

- [ ] Safari (if available)
  - [ ] Same tests

---

## **Step 15: Mobile Responsiveness**

- [ ] Desktop (1920x1080)
  - [ ] All elements visible
  - [ ] Layout intact

- [ ] Tablet (768px width)
  - [ ] Grid adjusts
  - [ ] Navigation accessible
  - [ ] Forms usable

- [ ] Mobile (375px width)
  - [ ] Single column layout
  - [ ] Touch-friendly buttons
  - [ ] No horizontal scroll

---

## **Final Verification Checklist**

### **Core Functionality**
- [ ] Farmers can add crops ✓
- [ ] Farmers can delete crops ✓
- [ ] Farmers can search crops ✓
- [ ] Buyers can browse crops ✓
- [ ] Buyers can search crops ✓
- [ ] Buyers can place orders ✓
- [ ] Farmers can manage order status ✓
- [ ] Both can track orders ✓

### **Technical Requirements**
- [ ] Uses AJAX with XMLHttpRequest ✓
- [ ] XML responses from API ✓
- [ ] Session-based authentication ✓
- [ ] PHP backend ✓
- [ ] MySQL database ✓
- [ ] Prepared statements ✓
- [ ] Input validation ✓

### **Documentation**
- [ ] Setup guide complete ✓
- [ ] Quick reference available ✓
- [ ] Architecture diagram provided ✓
- [ ] This checklist complete ✓

---

## **Troubleshooting Quick Links**

| Issue | Check | Solution |
|-------|-------|----------|
| AJAX not working | Browser console | Check file paths, verify XMLHttpRequest syntax |
| Crops not showing | Database | Run CREATE TABLE queries, verify farmer has crops |
| Orders failing | Session | Verify buyer is logged in, check database tables |
| Quantity not updating | PHP logic | Check `api_place_order.php` UPDATE statement |
| Search not working | API response | Check `api_search_crops.php` LIKE queries |
| Page not loading | PHP errors | Enable error_reporting, check syntax |
| Login failing | Database | Verify users table, check password encryption |

---

## **Deployment Checklist**

Before going live:

- [ ] Error messages hidden from users
- [ ] Error logging enabled
- [ ] Security headers set (HTTPS recommended)
- [ ] Rate limiting implemented
- [ ] Input validation comprehensive
- [ ] Database backups configured
- [ ] Performance optimized
- [ ] Documentation updated

---

## **Success Criteria**

✅ All items in this checklist verified
✅ All tests passed
✅ No console errors
✅ Database operations working
✅ AJAX calls functional
✅ User workflows complete
✅ Security measures in place
✅ Documentation provided

**System is ready for production!**

---

For detailed information, refer to:
- `SETUP_GUIDE.md` - Complete setup guide
- `QUICK_REFERENCE.md` - Quick testing guide  
- `ARCHITECTURE.md` - System architecture
