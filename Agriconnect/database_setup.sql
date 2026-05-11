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

-- Create order items table (for future enhancement)
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    crop_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE
);

-- Saved items / wishlist for buyers
CREATE TABLE IF NOT EXISTS saved_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    buyer_id INT NOT NULL,
    crop_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (crop_id) REFERENCES crops(id) ON DELETE CASCADE
);

-- Messages / feedback from buyers to farmers
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    farmer_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE
);
