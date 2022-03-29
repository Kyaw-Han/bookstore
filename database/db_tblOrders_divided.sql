-- codes here go the order of required creation of tables.

-- Publisher Table
CREATE TABLE tbl_publishers(
    pub_name VARCHAR(100) PRIMARY KEY NOT NULL , 
    pub_username VARCHAR(50) NOT NULL, 
    pub_password VARCHAR(50) NOT NULL, 
    pub_address VARCHAR(50) NOT NULL, 
    pub_phone VARCHAR(50) NOT NULL
);


ALTER TABLE tbl_publishers
ADD CONSTRAINT unique_name
UNIQUE(pub_name),
ADD CONSTRAINT unique_email
UNIQUE(pub_username);


-- Users Tables

CREATE TABLE tbl_users (
  user_id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  f_name varchar(50) NOT NULL,
  l_name varchar(50) NOT NULL,
  username varchar(20) NOT NULL,
  password varchar(20) NOT NULL
);

CREATE TABLE tbl_user_address (
  address_ID int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  address_line1 varchar(80) NOT NULL,
  address_line2 varchar(80),
  city varchar(10) NOT NULL,
  postal_code varchar(15) NOT NULL,
  telephone varchar(50),
  mobile varchar(50) NOT NULL,
  country varchar(50) NOT NULL,
  FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE
);

CREATE TABLE tbl_user_payment (
  user_payment_ID int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  payment_type int(5) NOT NULL,
  card_number varchar(20) NOT NULL,
  cvv int(3) NOT NULL,
  Expiry_Date VARCHAR(5) NOT NULL,
  FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE
);

CREATE TABLE tbl_user_premium (
  user_premium_id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  start_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  end_date DATETIME DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE
);

-- Books Tables
CREATE TABLE tbl_books(
    book_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, 
    isbn VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL, 
    genre VARCHAR(50) NOT NULL,
    book_type INT NOT NULL, 
    price FLOAT NOT NULL DEFAULT 0,
    rating FLOAT NOT NULL DEFAULT 0,
    publisher_name VARCHAR(100),
    FOREIGN KEY (publisher_name) REFERENCES tbl_publishers(pub_name) ON DELETE CASCADE,
    CONSTRAINT UC_Books UNIQUE (isbn, title, book_type)
);




CREATE TABLE tbl_bookInventory(
    inventory_id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, 
    book_id INT NOT NULL,
    purchased_count INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT current_timestamp(),
    modified_at DATETIME NOT NULL DEFAULT current_timestamp(),
    expired_at DATETIME DEFAULT NULL,
    FOREIGN KEY (book_id) REFERENCES tbl_books(book_id) ON DELETE CASCADE  
);

-- Trigger to auto add row to inventory 
CREATE TRIGGER `add_new_inventory_row_for_new_book` AFTER INSERT ON `tbl_books`
 FOR EACH ROW INSERT INTO tbl_bookinventory(book_id) VALUES (
	(
    	SELECT MAX(book_id)
        FROM tbl_books
    )
);

-- Discount Tables
CREATE TABLE tbl_discounts(
    discount_code varchar(10) NOT NULL,
    discount_name char(30)NOT NULL,
    discount_percentage int(2)NOT NULL, 
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    expired_at datetime DEFAULT NULL
);

ALTER TABLE tbl_discounts
ADD UNIQUE(discount_code);


-- Author Tables
CREATE TABLE tbl_is_author(
    user_id int NOT NULL,
    author_id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
    author_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE,
    CONSTRAINT UC_authors_user UNIQUE (user_id)
);

CREATE TABLE tbl_author_books(
    author_id INT NOT NULL, 
    book_id INT NOT NULL,
    FOREIGN KEY (author_id) REFERENCES tbl_is_author(author_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES tbl_books(book_id) ON DELETE CASCADE,
    CONSTRAINT UC_author_book UNIQUE(author_id, book_id) 
);


-- Shopping cart 
CREATE TABLE tbl_shopping_cart (
    user_id INT NOT NULL,
    book_id int NOT NULL, 
    book_count int NOT NULL DEFAULT 0,
    amount float(4) NOT NULL,
    status boolean NOT NULL DEFAULT FALSE,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT cart_user_fkey FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE,
    CONSTRAINT cart_book_fkey FOREIGN KEY (book_id) REFERENCES tbl_books(book_id) ON DELETE CASCADE
);

ALTER TABLE tbl_shopping_cart
ADD UNIQUE (user_id, book_id, book_count, amount, status , created_at);


-- Order history 
-- New Cide
CREATE TABLE tbl_user_order(
    order_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    CONSTRAINT user_order_fkey FOREIGN KEY(user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE
);

CREATE TABLE tbl_order_history(
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    comment VARCHAR(100) NOT NULL ,
    rating FLOAT(4) NOT NULL DEFAULT 0,
    amount FLOAT(4) NOT NULL DEFAULT 0,
    book_count INT NOT NULL DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT order_history_fkey FOREIGN KEY(order_id) REFERENCES tbl_user_order(order_id) ON DELETE CASCADE,
    CONSTRAINT order_book_fkey FOREIGN KEY(book_id) REFERENCES tbl_books(book_id) ON DELETE CASCADE
);


ALTER TABLE tbl_order_history
ADD UNIQUE (order_id, book_id);



-- shipping methods
CREATE TABLE tbl_shipping_methods(
    ship_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    book_type INT NOT NULL,
    is_premium BOOLEAN NOT NULL DEFAULT FALSE,
    shipping_method VARCHAR(50) NOT NULL,
    price FLOAT(4) NOT NULL DEFAULT 0
);

ALTER TABLE tbl_shipping_methods
ADD UNIQUE(book_type, is_premium, shipping_method);


-- Data Insertions
-- Insert Publisher Table

INSERT INTO tbl_publishers(pub_name, pub_username, pub_password, pub_address, pub_phone) VALUES('Scholastic Inc.', 'publisher0@gmail.com', 'password', '777 Brockton Avenue Abington MA 2351', '337-7840-37'),
('Scholastic', 'publisher1@gmail.com', 'password', '250 Hartford Avenue Bellingham MA 2019', '593-1424-37'),
('Nimble Books', 'publisher2@gmail.com', 'password', '591 Memorial Dr Chicopee MA 1020', '914-1342-94'),
('Gramercy Books', 'publisher3@gmail.com', 'password', '137 Teaticket Hwy East Falmouth MA 2536', '216-8439-40'),
('Del Rey Books', 'publisher4@gmail.com', 'password', '42 Fairhaven Commons Way Fairhaven MA 2719', '083-4282-39'),
('Crown', 'publisher5@gmail.com', 'password', '374 William S Canning Blvd Fall River MA 2721', '022-3193-95');

-- Insert Users Tables

INSERT INTO tbl_users (f_name, l_name, username, password) VALUES
("John", "Smith", "user1", "pass"),
("Stacy", "Saga", "user2", "pass"),
("Sallie", "Fritz", "user3", "pass"),
("Oskar", "Mclellan", "user4", "pass"),
("Piper", "Downes", "user5", "pass"),
("Richard", "Gomez", "user6", "pass"),
("Alice", "Sing", "user7", "pass"),
("Rex", "Jones", "user8", "pass"),
("Cathy", "Kells", "user9", "pass"),
("Gene", "Martinez", "user10", "pass");

-- Insert User Address Tables

INSERT INTO tbl_user_address (user_id, address_line1, city, postal_code, mobile, country) VALUES
(1, "831 Pleasant Dr.", "New York", "14075", "1111111111", "US"),
(2, "961 Sugar Ave.", "Elmhurt", "60126", "2222222222", "US"),
(3, "3984 Spadafore St.", "Egypt", "16801", "3333333333", "Africa"),
(4, "15 Fincham Way", "Bordeaux", "83702", "4444444444", "France"),
(5, "734 Philli Lane.", "Sydney", "74352", "5555555555", "Australia"),
(6, "1274 Lochmere Lane", "Hartford", "06103", "6666666666", "US"),
(7, "199 Pen Street", "Reno", "89501", "7777777777", "US"),
(8, "426 Takuhoku 5-jo", "Kita-ku", "74352", "8888888888", "Japan"),
(9, "9797 Hasler Villages", "Lucerne", "74352", "9999999999", "Switzerland"),
(10, "826 Caynor Circle", "Belleville", "01867", "234567890", "US");

-- Insert Users Payment Tables

INSERT INTO tbl_user_payment ( user_id, Payment_type, Card_number, CVV, Expiry_Date) VALUES
(1, 0, "4024007158886563", 992, "01/24"),
(2, 1, "5545901056284508", 862, "5/28"),
(3, 1, "347679352269123", 389, "08/25"),
(4, 0, "6222026194685065", 123, "5/22"),
(5, 1, "6011717720747974", 553, "2/27"),
(6, 0, "4024007157406207", 158, "3/29"),
(7, 0, "6011008441283071", 302, "2/26"),
(8, 0, "378422181710351", 186, "12/26"),
(9, 1, "6223050752471469", 864, "1/29"),
(10, 1, "3614377566445858", 486, "9/23");

-- Insert Users Premium Tables

INSERT INTO tbl_user_premium (user_id, start_date, end_date) VALUES
(1, "2018-12-14", "2025-08-18"),
(2, "2019-04-15", "2023-03-28"),
(3, "2020-11-04", "2026-08-05"),
(4, "2022-10-06", "2024-04-24"),
(5, "2021-03-23", "2025-06-26");

-- Insert Books Tables
INSERT INTO tbl_books(isbn, title, genre, book_type, price, publisher_name) VALUES("9780439785969","Harry Potter and the HalfBlood Prince Harry Potter  6", "history", 1,  78.80, "Scholastic Inc."),
("9780439358071","Harry Potter 5", "fiction", 2,  95.15, "Scholastic Inc."),
("9780439554893","Harry Potter 2", "psychology", 1,  89.88, "Scholastic"),
("9780439655484","Harry Potter 3", "science", 1,  13.57, "Scholastic Inc."),
("9780439682589","Harry Potter 15", "economics", 3,  35.19, "Scholastic"),
("9780976540601","Harry Potter Book 7", "comic", 4,  20.21, "Nimble Books"),
("9780439827607","Harry Potter Collection", "comic", 3,  92.57, "Scholastic"),
("9780517226957","The Hitchhikers Guide 15", "philosophy", 2,  4.35, "Gramercy Books"),
("9780345453747","The Ultimate Hitchhikers Guide", "science", 2,  18.90, "Del Rey Books"),
("9781400052929","The Guide to the Galaxy ", "computer_science", 4,  17.63, "Crown");


-- Insert Author Table
INSERT INTO tbl_is_author(user_id, author_name)
VALUES (1, 'John Smith'),
(2, 'Stacy Saga'),
(3, 'Sallie Fritz'),
(4, 'Oskar Mclellan'),
(5, 'Piper Downer');


INSERT INTO tbl_author_books(author_id, book_id)
VALUES (1,1),
(1,2),
(1,3),
(2,4),
(2,1),
(2,2),
(3,5),
(3,6),
(3,7),
(4,8),
(4,7),
(5,9),
(5,10);


-- Insert shopping cart Table

INSERT INTO tbl_shopping_cart( user_id, book_id, book_count, amount, status)
VALUES (1,1, 5, 35.99, true),
(2,2, 5, 35.99, true),
(3,5, 5, 35.99, false),
(7,3, 5, 35.99, true),
(8,9, 5, 35.99, false),
(9,8, 5, 35.99, true);


-- Insert Order History Table

INSERT INTO tbl_user_order(user_id)
VALUES(1),(2),(3),(4), (5);

INSERT INTO tbl_order_history(order_id, book_id, comment, rating, amount, book_count)
VALUES
(1,1,'good',2,35.93, 10),
(2, 2,'poor',2,25.33,3),
(3,4,'decent',2,25.23,3),
(4,7, 'ok',2,13.99,3),
(5,8, 'great',2,10.10,3);



-- Insert Shipping Methods
INSERT INTO tbl_shipping_methods (book_type, is_premium, shipping_method, price)
VALUES
(1, 0, "FedEx 2 Days", 12.99),
(2, 0, "UPS 2 Days", 7.99),
(3, 0, "Email Delivery", 12.99),
(4, 0, "Application Delivery", 12.99),

(1, 1, "FedEx 2 Days", 0.00),
(2, 1, "UPS 2 Days",  0.00),
(3, 1, "Email Delivery",  0.00),
(4, 1, "Application Delivery",  0.00);