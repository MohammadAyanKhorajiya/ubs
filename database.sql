CREATE TABLE users ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    name VARCHAR(100) NOT NULL, 
    email VARCHAR(150) NOT NULL UNIQUE, 
    password VARCHAR(255) NOT NULL, 
    phone VARCHAR(15), 
    state VARCHAR(100) NOT NULL, 
    city VARCHAR(100) NOT NULL,
    pincode VARCHAR(20) NOT NULL,
    house_no VARCHAR(255) NOT NULL,
    address VARCHAR(255), 
    otp VARCHAR(6), 
    is_verified TINYINT(1) DEFAULT 0, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_blocked TINYINT(1) DEFAULT 0
);

CREATE TABLE password_resets ( 
    reset_id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL, 
    token VARCHAR(100) NOT NULL, 
    expires_at TIMESTAMP NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE 
);

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL
);


CREATE TABLE books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    author VARCHAR(100),
    isbn VARCHAR(20),
    `condition` ENUM('New', 'Good', 'Fair', 'Old') DEFAULT 'Good',
    category_id INT,
    language VARCHAR(50),
    quantity INT DEFAULT 1,
    `type` ENUM('Paid', 'Donated') DEFAULT 'Paid',
    price DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('Available', 'Sold', 'Hidden') DEFAULT 'Available',
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);


CREATE TABLE book_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    path VARCHAR(255) NOT NULL,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

CREATE TABLE wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    id INT NOT NULL,
    book_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (id),
    INDEX (book_id)
);

CREATE TABLE cart (
  cart_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  book_id INT NOT NULL,
  quantity INT DEFAULT 1,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_user_book (user_id, book_id),
  CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_cart_book FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Completed', 'Canceled') DEFAULT 'Pending',
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('Cash', 'Card', 'UPI', 'Bank Transfer') DEFAULT 'Cash',
    status ENUM('Pending', 'Paid', 'Failed') DEFAULT 'Pending',
    paid_at TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE
);

CREATE TABLE order_cancel_reasons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

ALTER TABLE users 
ADD COLUMN role ENUM('user','admin') NOT NULL DEFAULT 'user' AFTER house_no;

INSERT INTO users (name, email, password, is_verified, role)
VALUES (
    'Admin',
    'jishanmarviya@gmail.com',
    '$2b$12$AonuyGJtCBvQFp6oKP63t.TyUIgHPlA20oOcleec6OTD3vrdwphfq',
    1,
    'admin'
);
