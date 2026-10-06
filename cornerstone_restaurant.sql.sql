CREATE DATABASE IF NOT EXISTS cornerstone_restaurant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cornerstone_restaurant;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(120) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS menu_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    image VARCHAR(255) DEFAULT NULL,
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_menu_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS customer_orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    phone_number VARCHAR(30) NOT NULL,
    menu_item_id INT UNSIGNED NULL,
    food_item_name VARCHAR(150) NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    delivery_location TEXT NOT NULL,
    payment_method ENUM('Cash','Mobile Money') NOT NULL DEFAULT 'Cash',
    notes VARCHAR(255) DEFAULT NULL,
    order_status ENUM('Pending','Confirmed','Preparing','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_item FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS testimonials (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    location VARCHAR(150) DEFAULT NULL,
    message TEXT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS promotions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO categories (id, name, slug) VALUES
(1, 'Breakfast', 'breakfast'),
(2, 'Lunch', 'lunch'),
(3, 'Dinner', 'dinner'),
(4, 'Drinks', 'drinks'),
(5, 'Snacks', 'snacks');

INSERT IGNORE INTO menu_items (id, category_id, name, description, price, image, is_available) VALUES
(1, 1, 'Rolex Special', 'Chapati wrapped with eggs, fresh vegetables, and house sauce.', 7000, 'https://images.unsplash.com/photo-1512058564366-c9e3f5c5ff2d?auto=format&fit=crop&w=1000&q=80', 1),
(2, 2, 'Chicken & Chips', 'Crispy fried chicken served with golden chips and salad.', 18000, 'https://images.unsplash.com/photo-1562967916-eb82221dfb92?auto=format&fit=crop&w=1000&q=80', 1),
(3, 2, 'Beef Pilau', 'Fragrant spiced rice with tender beef and seasonal sides.', 16000, 'https://images.unsplash.com/photo-1512058454905-6b841e7ad132?auto=format&fit=crop&w=1000&q=80', 1),
(4, 3, 'Tilapia Platter', 'Well-seasoned tilapia with matooke or chips and kachumbari.', 22000, 'https://images.unsplash.com/photo-1559847844-5315695dadae?auto=format&fit=crop&w=1000&q=80', 1),
(5, 4, 'Fresh Juice', 'Chilled seasonal fruit juice prepared fresh on order.', 6000, 'https://images.unsplash.com/photo-1622597467836-f3285f2131b8?auto=format&fit=crop&w=1000&q=80', 1),
(6, 5, 'Samosa Basket', 'Crunchy beef or vegetable samosas served with dipping sauce.', 5000, 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=1000&q=80', 1);

INSERT IGNORE INTO testimonials (id, customer_name, location, message, is_active) VALUES
(1, 'Sarah N.', 'Jinja', 'The ordering page is easy to use and the meals are always fresh.', 1),
(2, 'Mark T.', 'Mafubira', 'A beautiful menu website with fast service and tasty food.', 1),
(3, 'Office Team', 'Jinja Town', 'We love the WhatsApp ordering flow. It is simple and practical.', 1);

INSERT IGNORE INTO promotions (id, title, description, is_active) VALUES
(1, 'Lunch Combo Deal', 'Get selected lunch meals with a fresh juice at a discounted combo price.', 1),
(2, 'Breakfast Power Start', 'Enjoy a warm breakfast plate and tea every morning before 10:00 AM.', 1);
