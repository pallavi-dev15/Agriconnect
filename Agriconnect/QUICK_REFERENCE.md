# Smart AgriConnect - Quick Reference Guide

## **Files Created**

### **Backend API Endpoints**
1. ✅ `api_add_crop.php` - Add new crop (POST)
2. ✅ `api_get_crops.php` - Get all crops (GET)
3. ✅ `api_delete_crop.php` - Delete crop (POST)
4. ✅ `api_search_crops.php` - Search crops (GET)
5. ✅ `api_place_order.php` - Place order (POST)
6. ✅ `api_get_orders.php` - Get orders (GET)
7. ✅ `api_update_order_status.php` - Update order status (POST)

### **Frontend Pages**
1. ✅ `mylistings.php` - Farmer's crop management page
2. ✅ `marketplace.php` - Buyer's shopping page
3. ✅ `myorders.php` - Order tracking for both roles
4. ✅ `dashboard.php` - Updated with new links

### **Documentation**
1. ✅ `database_setup.sql` - SQL schema
2. ✅ `SETUP_GUIDE.md` - Complete documentation
3. ✅ `QUICK_REFERENCE.md` - This file

---

## **Database Setup (Required)**

**IMPORTANT**: Run this before testing!

Open phpMyAdmin or MySQL CLI and execute:
```sql
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

---

## **Testing Workflow**

### **Step 1: Setup**
- [ ] Create database tables (see above)
- [ ] Have 2 test accounts: 1 farmer, 1 buyer

### **Step 2: Test Farmer Workflow**

1. **Login as Farmer**
   - Go to: `dashboard.php`
   - Click "Manage My Crops"
   - Should see: "No crops yet" message

2. **Add Crop**
   - Click "+ Add New Crop" button
   - Fill in:
     - Crop Name: "Tomato"
     - Price: "15.50"
     - Quantity: "500"
     - Grade: "A+"
     - Location: "Pune"
     - Description: "Fresh red tomatoes"
   - Click "Add Crop"
   - ✅ Crop appears in grid

3. **Add Another Crop**
   - Repeat with different crop (e.g., Rice)
   - Now should see 2 crops in grid

4. **Search Crops**
   - Enter "tomato" in search box
   - Click "Search"
   - ✅ Only tomato appears
   - Click "Clear"
   - ✅ All crops show again

5. **Delete Crop**
   - Click "Delete" on a crop
   - Confirm deletion
   - ✅ Crop disappears from grid

6. **Check Orders**
   - Go to "My Orders" (from dashboard or nav)
   - Should be empty (no buyers yet)

### **Step 3: Test Buyer Workflow**

1. **Login as Buyer**
   - Go to: `dashboard.php`
   - Click "Explore Products"
   - Should see: Farmer's crops from Step 2

2. **Browse Crops**
   - ✅ View all crops in grid layout
   - Each card shows: price, quantity, grade, location

3. **Search Crops**
   - Enter search term
   - Click "Search"
   - ✅ Filtered results appear
   - Click "Clear"
   - ✅ All crops show again

4. **Place Order**
   - Click "Order Now" on any crop
   - Modal form appears
   - Fill in:
     - Quantity: "50" (must be ≤ available)
     - Delivery Address: "123 Main St, City"
     - Special Notes: "Please pack carefully"
   - Notice: Total price auto-calculates
   - Click "Place Order"
   - ✅ "Order placed successfully" message
   - ✅ Shows Order ID and Total Price

5. **Check Orders**
   - Go to "My Orders"
   - ✅ See order with:
     - Order ID
     - Crop name
     - Farmer name
     - Quantity
     - Total price
     - Status: "pending"
     - Delivery address

6. **Place Another Order**
   - Repeat order process
   - ✅ See multiple orders in list

### **Step 4: Test Farmer's Order Management**

1. **Login as Farmer**
   - Go to "My Orders"
   - ✅ See orders from buyers
   - ✅ Orders show:
     - Crop name
     - Quantity ordered
     - Total price
     - Buyer's delivery address
     - Current status

2. **Update Order Status**
   - Click "Update Status" on an order
   - Modal appears
   - Select "accepted"
   - Click "Update Status"
   - ✅ Status changes to "accepted"

3. **Progress Order Through Stages**
   - Update same order: accepted → shipped
   - Update again: shipped → delivered
   - ✅ Each update reflects in the list

### **Step 5: Verify Quantity Updates**

1. **Check Crop Quantity**
   - As farmer, go to "My Listings"
   - Note quantity of a crop (e.g., 500)
   - Calculate: 500 - (all orders for this crop)

2. **Place Order as Buyer**
   - Order 100 kg of that crop
   - Go back to listings
   - ✅ Quantity should be 400 now

3. **Try Ordering More Than Available**
   - Try to order 500 kg when only 400 available
   - ✅ Error message: "Insufficient quantity available"

---

## **AJAX Implementation Details**

### **All Requests Use XMLHttpRequest**

Example GET request:
```javascript
var xhr = new XMLHttpRequest();
xhr.open('GET', 'api_get_crops.php', true);
xhr.send();
```

Example POST request:
```javascript
var xhr = new XMLHttpRequest();
xhr.open('POST', 'api_add_crop.php', true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
xhr.send('crop_name=Rice&price=25&quantity=1000');
```

### **All Responses Are XML**

```xml
<?xml version="1.0"?>
<response>
    <success>true</success>
    <message>Success message</message>
    <!-- Additional data based on endpoint -->
</response>
```

---

## **Key Features**

✅ **Farmers Can**:
- Add unlimited crops
- Edit crop details (coming soon)
- Delete crops
- Search their own crops
- See all incoming orders
- Update order status
- Track sales

✅ **Buyers Can**:
- Browse all crops from all farmers
- Search crops by name, location, description
- See real-time availability and pricing
- Place orders with custom quantities
- Track order status
- View delivery details

✅ **System Features**:
- Session-based authentication
- Real-time quantity tracking
- Automatic total price calculation
- XML API responses
- Old-style AJAX (XMLHttpRequest)
- Prepared SQL statements (secure)
- Error handling and validation

---

## **Common Issues & Solutions**

### **"Crops not loading"**
- [ ] Check browser console (F12)
- [ ] Verify `api_get_crops.php` file exists
- [ ] Ensure you're logged in (session active)
- [ ] Check database tables exist

### **"Can't add crops"**
- [ ] Verify database `crops` table exists
- [ ] Check you're logged in as farmer
- [ ] Fill in all required fields (name, price, quantity)
- [ ] Check file exists: `api_add_crop.php`

### **"Can't place orders"**
- [ ] Verify database `orders` table exists
- [ ] Check you're logged in as buyer
- [ ] Verify quantity is available and valid
- [ ] Check file exists: `api_place_order.php`

### **"Orders not showing"**
- [ ] Verify database `orders` table has data
- [ ] Check you're logged in with correct role
- [ ] As farmer, verify you have crops
- [ ] Check file exists: `api_get_orders.php`

---

## **Testing Checklist**

### **As Farmer**
- [ ] Add crop
- [ ] See crop in "My Listings"
- [ ] Delete crop
- [ ] Search crops
- [ ] Clear search
- [ ] See incoming orders
- [ ] Update order status
- [ ] Verify quantity decreases when order placed

### **As Buyer**
- [ ] See all crops in Marketplace
- [ ] Search crops
- [ ] Filter/clear search
- [ ] Place order
- [ ] See order in "My Orders"
- [ ] View order details
- [ ] Try order with insufficient quantity (error)
- [ ] See quantity update after order

---

## **Technical Stack**

- **Frontend**: HTML5, CSS3, JavaScript (ES6)
- **AJAX**: XMLHttpRequest (old syntax, not Fetch API)
- **Backend**: PHP 7+
- **Database**: MySQL
- **Authentication**: PHP Sessions
- **API Format**: XML

---

## **File Size Reference**

| File | Type | Purpose |
|------|------|---------|
| api_add_crop.php | Backend | Add crops via AJAX |
| api_get_crops.php | Backend | Fetch crops (role-based) |
| api_delete_crop.php | Backend | Delete crop |
| api_search_crops.php | Backend | Search crops |
| api_place_order.php | Backend | Create order |
| api_get_orders.php | Backend | Fetch orders |
| api_update_order_status.php | Backend | Update order status |
| mylistings.php | Frontend | Farmer dashboard |
| marketplace.php | Frontend | Buyer marketplace |
| myorders.php | Frontend | Order tracking |
| dashboard.php | Updated | Role-based dashboard |

---

## **Next: Advanced Features (Optional)**

- [ ] Edit crops functionality
- [ ] Order history with filters
- [ ] Payment integration
- [ ] Star ratings & reviews
- [ ] Notifications system
- [ ] Bulk order discounts
- [ ] Wishlist feature
- [ ] Real-time chat
- [ ] Admin dashboard

---

## **Support**

For detailed information, see: `SETUP_GUIDE.md`

For API documentation, see: `SETUP_GUIDE.md` → **API Response Examples** section

Enjoy! 🚀
