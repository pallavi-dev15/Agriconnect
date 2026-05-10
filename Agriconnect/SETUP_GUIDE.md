# Smart AgriConnect - Crop Management System with AJAX

## Complete Setup Guide

### **System Overview**

This is a complete e-commerce platform for agricultural products with the following features:
- **Farmers**: Can add, edit, delete, and search their crop listings
- **Buyers**: Can browse all crops, search, and place orders
- **Order Management**: Track orders with status updates
- Uses **PHP**, **MySQL**, **Sessions**, and **AJAX with XMLHttpRequest** (old syntax)

---

## **Database Setup**

### **Step 1: Import the SQL Schema**

1. Open your MySQL client (phpMyAdmin or MySQL CLI)
2. Execute the following SQL commands:

```sql
-- Create crops table
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

-- Create orders table
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

Or simply run the `database_setup.sql` file in your MySQL client.

---

## **File Descriptions**

### **Backend API Files (AJAX Endpoints)**

#### **1. `api_add_crop.php`**
- **Purpose**: Add new crop for logged-in farmer
- **Method**: POST
- **Parameters**: 
  - `crop_name` (required)
  - `price` (required)
  - `quantity` (required)
  - `grade`, `location`, `description`, `image_url` (optional)
- **Returns**: XML response with success/failure status
- **Authentication**: Farmer session required

#### **2. `api_get_crops.php`**
- **Purpose**: Retrieve all crops
  - Farmers see only their own crops
  - Buyers see all crops
- **Method**: GET
- **Optional Parameters**: `farmer_id` (to filter by specific farmer)
- **Returns**: XML list of all crops
- **No Authentication**: Works for both farmers and buyers

#### **3. `api_delete_crop.php`**
- **Purpose**: Delete a crop (farmers only)
- **Method**: POST
- **Parameters**: `crop_id` (required)
- **Returns**: XML response with success/failure status
- **Authentication**: Farmer session required (verifies ownership)

#### **4. `api_search_crops.php`**
- **Purpose**: Search crops by name, location, or description
- **Method**: GET
- **Parameters**: `query` (required)
- **Returns**: XML list of matching crops
- **Note**: Farmers search their own crops, buyers search all crops

#### **5. `api_place_order.php`**
- **Purpose**: Place an order for a crop
- **Method**: POST
- **Parameters**: 
  - `crop_id` (required)
  - `quantity` (required)
  - `delivery_address` (required)
  - `notes` (optional)
- **Returns**: XML response with order ID and total price
- **Authentication**: Buyer session required
- **Validates**: Quantity availability, auto-updates crop quantity

#### **6. `api_get_orders.php`**
- **Purpose**: Retrieve orders
  - Farmers see orders from buyers (for their crops)
  - Buyers see their own orders
- **Method**: GET
- **Returns**: XML list of orders
- **Authentication**: Required

#### **7. `api_update_order_status.php`**
- **Purpose**: Update order status (farmers only)
- **Method**: POST
- **Parameters**: 
  - `order_id` (required)
  - `status` (required: pending, accepted, shipped, delivered, cancelled)
- **Returns**: XML response
- **Authentication**: Farmer session required

---

### **Frontend Pages**

#### **1. `mylistings.php` (For Farmers)**
- **Features**:
  - View all their crop listings in a grid
  - Add new crop (modal form)
  - Delete crops with confirmation
  - Search crops by name
  - Real-time updates
- **AJAX Calls**:
  - `GET /api_get_crops.php` - Load crops on page load
  - `POST /api_add_crop.php` - Submit new crop form
  - `POST /api_delete_crop.php` - Delete crop
  - `GET /api_search_crops.php?query=...` - Search crops

#### **2. `marketplace.php` (For Buyers)**
- **Features**:
  - Browse all crops in grid view
  - Search crops
  - View crop details (price, quantity, grade, location)
  - Place orders (modal form)
  - Real-time total price calculation
- **AJAX Calls**:
  - `GET /api_get_crops.php` - Load all crops
  - `GET /api_search_crops.php?query=...` - Search crops
  - `POST /api_place_order.php` - Submit order

#### **3. `myorders.php` (For Both)**
- **Features**:
  - Farmers: View orders from buyers (for their crops)
  - Buyers: View their own orders
  - Farmers: Update order status (Pending → Accepted → Shipped → Delivered)
  - Buyers: Track order status
- **AJAX Calls**:
  - `GET /api_get_orders.php` - Load orders
  - `POST /api_update_order_status.php` - Update status (farmers only)

#### **4. `dashboard.php`**
- Updated to include links to My Listings (farmers) and My Orders

---

## **AJAX Implementation Details**

### **XMLHttpRequest Wrapper Function**

All AJAX calls use a consistent wrapper function (old syntax):

```javascript
function makeRequest(method, url, callback, data = null) {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    var xmlDoc = xhr.responseXML;
                    callback(null, xmlDoc);
                } catch (e) {
                    callback(e, null);
                }
            } else {
                callback(new Error("HTTP " + xhr.status), null);
            }
        }
    };

    xhr.onerror = function() {
        callback(new Error("Network error"), null);
    };

    xhr.open(method, url, true);
    
    if (method === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(data);
    } else {
        xhr.send();
    }
}
```

### **Usage Examples**

#### **GET Request (Load Crops)**
```javascript
makeRequest('GET', 'api_get_crops.php', function(err, xmlDoc) {
    if (err) {
        showMessage('Error loading crops', 'error');
        return;
    }
    var crops = xmlDoc.getElementsByTagName('crop');
    // Process crops...
});
```

#### **POST Request (Add Crop)**
```javascript
var params = 'crop_name=' + encodeURIComponent(cropName) +
             '&price=' + encodeURIComponent(price) +
             '&quantity=' + encodeURIComponent(quantity);

makeRequest('POST', 'api_add_crop.php', function(err, xmlDoc) {
    if (err) {
        showMessage('Error adding crop', 'error');
        return;
    }
    var success = xmlDoc.getElementsByTagName('success')[0].textContent;
    if (success === 'true') {
        showMessage('Crop added successfully', 'success');
        loadCrops(); // Reload list
    }
}, params);
```

### **XML Response Format**

All API responses follow this XML structure:

```xml
<?xml version="1.0"?>
<response>
    <success>true|false</success>
    <message>Response message</message>
    <crop_id>123</crop_id> <!-- Optional, depends on endpoint -->
    <crops>
        <crop>
            <id>1</id>
            <crop_name>Rice</crop_name>
            <price>25.00</price>
            ...
        </crop>
    </crops>
</response>
```

---

## **Session Management**

### **How Sessions Work**

When a user logs in via `authenticate.php`, these session variables are set:

```php
$_SESSION['id']       // User ID from database
$_SESSION['username'] // Username
$_SESSION['usertype'] // 'farmer' or 'buyer'
```

### **Authorization Check**

All API endpoints check session authorization:

```php
session_start();

// For farmer-only endpoints
if (!isset($_SESSION['id']) || $_SESSION['usertype'] !== 'farmer') {
    // Return error response
}
```

---

## **Usage Guide**

### **For Farmers**

1. **Add Crop**:
   - Click "+ Add New Crop" button
   - Fill in crop details (name, price, quantity, grade, location, etc.)
   - Click "Add Crop"
   - Crop appears in the listings grid

2. **Delete Crop**:
   - Find the crop in the grid
   - Click "Delete" button
   - Confirm deletion
   - Crop is removed

3. **Search Crops**:
   - Enter search term (crop name, location, etc.)
   - Click "Search"
   - Results appear in the grid
   - Click "Clear" to see all crops again

4. **Manage Orders**:
   - Go to "My Orders" page
   - See all orders from buyers
   - Click "Update Status" to change order status:
     - Pending → Accepted → Shipped → Delivered
     - Or mark as Cancelled

### **For Buyers**

1. **Browse Crops**:
   - Visit Marketplace page
   - View all available crops in grid layout
   - Each card shows: price, quantity, grade, location, description

2. **Search Crops**:
   - Enter search term (crop name, location, etc.)
   - Click "Search"
   - Browse filtered results

3. **Place Order**:
   - Click "Order Now" on any crop card
   - Enter quantity (must be ≤ available quantity)
   - System auto-calculates total price
   - Enter delivery address
   - Add optional special notes
   - Click "Place Order"
   - Order is confirmed and visible in "My Orders"

4. **Track Orders**:
   - Go to "My Orders" page
   - See all orders with status (Pending, Accepted, Shipped, Delivered)
   - See farmer name, crop details, delivery address, order date

---

## **Database Relationships**

```
users (existing table)
  ├── crops (farmer_id)
  │   └── orders (crop_id)
  │
  └── orders (buyer_id)
  └── orders (farmer_id)
```

---

## **Security Features**

1. **Session Authentication**: All endpoints require login
2. **User Type Verification**: Endpoints check `$_SESSION['usertype']`
3. **Ownership Verification**: Farmers can only delete/edit their own crops
4. **SQL Injection Prevention**: All queries use prepared statements
5. **HTML Escaping**: All user inputs are escaped in XML output
6. **Password Security**: Uses `password_verify()` for authentication

---

## **Error Handling**

All endpoints follow consistent error handling:

```xml
<!-- Success Response -->
<response>
    <success>true</success>
    <message>Operation successful</message>
</response>

<!-- Error Response -->
<response>
    <success>false</success>
    <message>Error description</message>
</response>
```

---

## **Customization**

### **Add More Crop Fields**

1. Add column to `crops` table:
   ```sql
   ALTER TABLE crops ADD COLUMN harvest_date DATE;
   ```

2. Update `api_add_crop.php` to accept new field:
   ```php
   $harvest_date = $_POST['harvest_date'] ?? '';
   // Add to INSERT query
   ```

3. Update form in `mylistings.php`:
   ```html
   <div class="form-group">
       <label for="harvestDate">Harvest Date</label>
       <input type="date" id="harvestDate">
   </div>
   ```

### **Add Order History**

Create an `order_history.php` page similar to `myorders.php` but with filters and export options.

### **Add Notifications**

Add a `notifications` table and create `api_get_notifications.php` endpoint.

---

## **Troubleshooting**

### **AJAX Calls Not Working**

1. Check browser console (F12 → Console tab)
2. Verify file paths are correct
3. Ensure session is active (`session_start()` called)
4. Check that `api_*.php` files exist

### **Crops Not Showing**

1. Verify database tables exist: `SHOW TABLES;`
2. Check if logged-in user has crops: `SELECT * FROM crops WHERE farmer_id = 1;`
3. Verify session variables: Add `console.log($_SESSION);` for debugging

### **Orders Not Updating**

1. Verify `orders` table exists and has data
2. Check user is farmer when trying to update status
3. Ensure order belongs to current farmer's crops

---

## **API Response Examples**

### **Get Crops - Success**
```xml
<?xml version="1.0"?>
<response>
    <success>true</success>
    <crops>
        <crop>
            <id>1</id>
            <crop_name>Rice</crop_name>
            <price>25.50</price>
            <quantity>1000</quantity>
            <grade>A+</grade>
            <location>Pune, Maharashtra</location>
            <description>Premium quality rice</description>
            <image_url>rice.jpg</image_url>
            <created_at>2026-05-10 10:30:00</created_at>
        </crop>
    </crops>
</response>
```

### **Add Crop - Success**
```xml
<?xml version="1.0"?>
<response>
    <success>true</success>
    <message>Crop added successfully</message>
    <crop_id>42</crop_id>
</response>
```

### **Place Order - Success**
```xml
<?xml version="1.0"?>
<response>
    <success>true</success>
    <message>Order placed successfully</message>
    <order_id>5</order_id>
    <total_price>250.00</total_price>
</response>
```

---

## **Next Steps**

1. Run the database setup SQL
2. Test as a farmer: Add crops, search, delete
3. Test as a buyer: Browse, search, place order
4. Check orders page for both roles
5. Update order status as farmer

Enjoy your SmartAgriConnect platform!
