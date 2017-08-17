CREATE DATABASE book_sc;

USE book_sc;

CREATE TABLE customers (
  customerid int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name char(60) NOT NULL,
  address char(80) NOT NULL,
  city char(30) NOT NULL,
  state char(20),
  zip char(10),
  country char(20) NOT NULL
);

CREATE TABLE orders (
  orderid int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  customerid int unsigned NOT NULL,
  amount float(6,2),
  date date NOT NULL,
  order_status char(10),
  ship_name char(60) NOT NULL,
  ship_address char(80) NOT NULL,
  ship_city char(30) NOT NULL,
  ship_state char(20),
  ship_zip char(10),
  ship_country char(20) NOT NULL
);

CREATE TABLE books (
   isbn char(13) NOT NULL PRIMARY KEY,
   author char(80),
   title char(100),
   catid int unsigned,
   price float(4,2) NOT NULL,
   description varchar(255)
);

CREATE TABLE categories (
  catid int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  catname char(60) NOT NULL
);

CREATE TABLE order_items (
  orderid int unsigned NOT NULL,
  isbn char(13) NOT NULL,
  item_price float(4,2) NOT NULL,
  quantity tinyint unsigned NOT NULL,
  PRIMARY KEY (orderid, isbn)
);

CREATE TABLE admin (
  username char(16) NOT NULL PRIMARY KEY,
  password char(40) NOT NULL
);

GRANT SELECT, INSERT, UPDATE, DELETE
ON book_sc.*
TO book_sc@localhost identified by 'password';
