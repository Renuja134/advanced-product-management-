-- දත්ත ගබඩාව සාදා ගැනීම
CREATE DATABASE IF NOT EXISTS inventory_db;
USE inventory_db;

-- පරිශීලක වගුව
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('admin','manager','staff') DEFAULT 'staff',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- භාණ්ඩ කාණ්ඩ වගුව
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- භාණ්ඩ වගුව
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    cost_price DECIMAL(10,2),
    quantity INT DEFAULT 0,
    barcode VARCHAR(50) UNIQUE,
    image_path VARCHAR(255),
    status ENUM('active','inactive') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- පාරිභෝගික වගුව
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- වෙළඳාම් වගුව
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    payment_method ENUM('cash','card','cheque','online'),
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sold_by INT,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (sold_by) REFERENCES users(id)
);

-- වෙළඳාම් අයිතම වගුව
CREATE TABLE sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT,
    product_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ආරම්භක දත්ත ඇතුළත් කිරීම
-- පරිපාලක පරිශීලකයා
INSERT INTO users (username, password, full_name, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'පද්ධති පරිපාලක', 'admin');

-- කාණ්ඩ
INSERT INTO categories (name) VALUES 
('එලවලු'), ('පළතුරු'), ('කිරි භාණ්ඩ'), ('බෙකරි');

-- භාණ්ඩ
INSERT INTO products (category_id, name, price, quantity) VALUES
(1, 'තක්කාලි', 120.00, 50),
(1, 'අල', 80.00, 100),
(2, 'අම්බටේ', 60.00, 75),
(3, 'එළකිරි 1L', 200.00, 30);
