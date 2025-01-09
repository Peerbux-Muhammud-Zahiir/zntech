-- Admin Table
CREATE TABLE `admin` (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_name VARCHAR(100) NOT NULL,
    admin_email VARCHAR(100) NOT NULL UNIQUE,
    admin_password VARCHAR(255) NOT NULL
);

-- User Table
CREATE TABLE `user` (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100) NOT NULL UNIQUE,
    user_phone VARCHAR(15),
    user_password VARCHAR(255) NOT NULL
);

-- Product Table (Includes Image URL for product)
CREATE TABLE `product` (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    product_description TEXT,
    product_price DECIMAL(10, 2) NOT NULL,
    product_stock INT NOT NULL,
    product_image_url VARCHAR(255) NOT NULL -- To store image URL
);

-- Order Table (Now includes delivery address)
CREATE TABLE `order` (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_date DATE NOT NULL,
    order_status ENUM('pending', 'shipped', 'delivered', 'cancelled') NOT NULL,
    user_id INT NOT NULL,
    delivery_address VARCHAR(255) NOT NULL, -- Delivery address for the order
    FOREIGN KEY (user_id) REFERENCES `user`(user_id) ON DELETE CASCADE
);

-- OrderProduct Table (Join Table for Orders and Products)
CREATE TABLE `order_product` (
    order_product_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES `order`(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES `product`(product_id) ON DELETE CASCADE
);

CREATE TABLE `review` (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    review_text TEXT NOT NULL,
    review_rating INT NOT NULL CHECK (review_rating BETWEEN 1 AND 5), -- Rating between 1 and 5
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES `user`(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES `product`(product_id) ON DELETE CASCADE,
    CONSTRAINT user_product_review_constraint UNIQUE (user_id, product_id) -- Prevent duplicate reviews by the same user for the same product
);



-- StockLog Table
CREATE TABLE `stock_log` (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    old_stock INT NOT NULL,
    new_stock INT NOT NULL,
    change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES `product`(product_id) ON DELETE CASCADE
);

-- Cart Table
CREATE TABLE `cart` (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES `user`(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES `product`(product_id) ON DELETE CASCADE
);

-- Payment Table
CREATE TABLE `payment` (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    payment_method ENUM('debit', 'credit', 'cash', 'pay_on_delivery') NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    order_id INT NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES `order`(order_id) ON DELETE CASCADE
);
