-- ============================================================
--  Casa De Manila — Orders Tables
--  File: database/orders.sql
--  Run this to add order tracking to your database
-- ============================================================

USE casa_de_manila;

-- ------------------------------------------------------------
--  TABLE: orders_tbl
--  One row per order (checkout session)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders_tbl (
  order_id      INT(11)        NOT NULL AUTO_INCREMENT,
  uid           INT(11)        NOT NULL,
  order_date    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  total_amount  DECIMAL(10,2)  NOT NULL,
  status        ENUM('pending','confirmed','preparing','ready','completed','cancelled')
                               NOT NULL DEFAULT 'pending',
  notes         TEXT           DEFAULT NULL,
  PRIMARY KEY (order_id),
  FOREIGN KEY (uid) REFERENCES users_tbl1(uid) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  TABLE: order_items_tbl
--  One row per item inside an order
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items_tbl (
  item_id       INT(11)        NOT NULL AUTO_INCREMENT,
  order_id      INT(11)        NOT NULL,
  menu_id       INT(11)        DEFAULT NULL,
  item_name     VARCHAR(100)   NOT NULL,
  price         DECIMAL(8,2)   NOT NULL,
  quantity      INT(5)         NOT NULL DEFAULT 1,
  subtotal      DECIMAL(10,2)  NOT NULL,
  PRIMARY KEY (item_id),
  FOREIGN KEY (order_id) REFERENCES orders_tbl(order_id) ON DELETE CASCADE,
  FOREIGN KEY (menu_id)  REFERENCES menu_tbl(menu_id)    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;