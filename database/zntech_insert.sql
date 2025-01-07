

-- Insert into Product Table
INSERT INTO `product` (product_name, product_description, product_price, product_stock, product_image_url)
VALUES 
('Laptop', 'High performance laptop', 999.99, 50, 'http://example.com/images/laptop.jpg'),
('Smartphone', 'Latest model smartphone', 499.99, 100, 'http://example.com/images/smartphone.jpg');

-- Insert into Order Table
INSERT INTO `order` (order_date, order_status, user_id, delivery_address)
VALUES 
('2025-01-06', 'pending', 1, '123 Main St, City, Country'),
('2025-01-06', 'shipped', 2, '456 Elm St, Town, Country');

-- Insert into OrderProduct Table
INSERT INTO `order_product` (order_id, product_id, quantity)
VALUES 
(1, 1, 2),
(2, 2, 1);

-- Insert into Review Table
INSERT INTO `review` (reviewer_name, reviewer_gender, rental_date_from, rental_date_to, house_rating, house_comment, flagged, banned, product_id)
VALUES 
('Alice Johnson', 'female', '2025-01-01', '2025-01-10', 5, 'Great laptop, highly recommend!', FALSE, FALSE, 1),
('Bob Smith', 'male', '2025-01-02', '2025-01-15', 4, 'Smartphone is good, but could be improved', FALSE, FALSE, 2);

-- Insert into StockLog Table
INSERT INTO `stock_log` (product_id, old_stock, new_stock, change_date)
VALUES 
(1, 50, 48, '2025-01-06 10:00:00'),
(2, 100, 99, '2025-01-06 10:05:00');

-- Insert into Cart Table
INSERT INTO `cart` (user_id, product_id, quantity, added_at)
VALUES 
(1, 2, 1, '2025-01-06 09:30:00'),
(2, 1, 2, '2025-01-06 09:35:00');
