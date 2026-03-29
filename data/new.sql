-- ============================================================
--  Casa De Manila — Full Database Setup
--  File: data/casa_de_manila_full.sql
--  Import via phpMyAdmin → Import tab
--  This is a FULL RESET — drops and recreates everything
-- ============================================================

CREATE DATABASE IF NOT EXISTS casa_de_manila
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE casa_de_manila;

-- ── Drop tables in safe order ────────────────────────────────
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS order_items_tbl;
DROP TABLE IF EXISTS orders_tbl;
DROP TABLE IF EXISTS reservations_tbl;
DROP TABLE IF EXISTS contacts_tbl;
DROP TABLE IF EXISTS events_tbl;
DROP TABLE IF EXISTS menu_tbl;
DROP TABLE IF EXISTS users_tbl1;
SET FOREIGN_KEY_CHECKS = 1;

-- ── TABLE: users_tbl1 ────────────────────────────────────────
CREATE TABLE users_tbl1 (
  uid         INT(11)      NOT NULL AUTO_INCREMENT,
  username    VARCHAR(50)  NOT NULL UNIQUE,
  email       VARCHAR(100) NOT NULL UNIQUE,
  password_us VARCHAR(255) NOT NULL,
  role        ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLE: menu_tbl ──────────────────────────────────────────
CREATE TABLE menu_tbl (
  menu_id      INT(11)       NOT NULL AUTO_INCREMENT,
  name         VARCHAR(100)  NOT NULL,
  description  TEXT          DEFAULT NULL,
  price        DECIMAL(8,2)  NOT NULL,
  category     VARCHAR(50)   NOT NULL,
  image        VARCHAR(255)  DEFAULT NULL,
  is_available TINYINT(1)    NOT NULL DEFAULT 1,
  created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (menu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLE: orders_tbl ────────────────────────────────────────
CREATE TABLE orders_tbl (
  order_id      INT(11)        NOT NULL AUTO_INCREMENT,
  uid           INT(11)        NOT NULL,
  order_date    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  total_amount  DECIMAL(10,2)  NOT NULL,
  status        ENUM('pending','confirmed','preparing','ready','completed','cancelled')
                               NOT NULL DEFAULT 'pending',
  notes         TEXT           DEFAULT NULL,
  payment_method VARCHAR(20)   DEFAULT NULL,
  PRIMARY KEY (order_id),
  FOREIGN KEY (uid) REFERENCES users_tbl1(uid) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLE: order_items_tbl ───────────────────────────────────
CREATE TABLE order_items_tbl (
  item_id   INT(11)       NOT NULL AUTO_INCREMENT,
  order_id  INT(11)       NOT NULL,
  menu_id   INT(11)       DEFAULT NULL,
  item_name VARCHAR(100)  NOT NULL,
  price     DECIMAL(8,2)  NOT NULL,
  quantity  INT(5)        NOT NULL DEFAULT 1,
  subtotal  DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (item_id),
  FOREIGN KEY (order_id) REFERENCES orders_tbl(order_id)  ON DELETE CASCADE,
  FOREIGN KEY (menu_id)  REFERENCES menu_tbl(menu_id)     ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLE: reservations_tbl ──────────────────────────────────
CREATE TABLE reservations_tbl (
  reservation_id   INT(11)      NOT NULL AUTO_INCREMENT,
  uid              INT(11)      DEFAULT NULL,
  full_name        VARCHAR(100) NOT NULL,
  email            VARCHAR(100) NOT NULL,
  phone            VARCHAR(20)  NOT NULL,
  party_size       INT(3)       NOT NULL,
  reservation_date DATE         NOT NULL,
  reservation_time TIME         NOT NULL,
  special_request  TEXT         DEFAULT NULL,
  status           ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  created_at       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (reservation_id),
  FOREIGN KEY (uid) REFERENCES users_tbl1(uid) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLE: events_tbl ────────────────────────────────────────
CREATE TABLE events_tbl (
  event_id    INT(11)      NOT NULL AUTO_INCREMENT,
  title       VARCHAR(150) NOT NULL,
  description TEXT         DEFAULT NULL,
  event_date  DATE         NOT NULL,
  event_time  TIME         DEFAULT NULL,
  image       VARCHAR(255) DEFAULT NULL,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLE: contacts_tbl ──────────────────────────────────────
CREATE TABLE contacts_tbl (
  contact_id INT(11)      NOT NULL AUTO_INCREMENT,
  full_name  VARCHAR(100) NOT NULL,
  email      VARCHAR(100) NOT NULL,
  subject    VARCHAR(200) DEFAULT NULL,
  message    TEXT         NOT NULL,
  is_read    TINYINT(1)   NOT NULL DEFAULT 0,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (contact_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  SEED DATA
-- ============================================================

-- Admin user (password: Admin@1234)
INSERT INTO users_tbl1 (username, email, password_us, role) VALUES
('admin', 'admin@casamanila.ph',
 '$2y$10$TKh8H1.PfYi1l0V5dBLH4OeC6sZ8WqlWQ7f0V4f5p0Qv6N9a2K2Ky', 'admin');

-- ── Menu items ───────────────────────────────────────────────
INSERT INTO menu_tbl (name, description, price, category, image, is_available) VALUES

-- Mains
('Chicken Adobo',
 'Succulent chicken slow-braised in a savory-tangy blend of fermented vinegar, premium soy sauce, and toasted garlic. Finished with cracked black peppercorns for a deep, aromatic flavor.',
 250.00, 'Mains', './images/adobo.jpg', 1),

('Pork Steak',
 'Tender pork slices marinated in soy sauce and calamansi, topped with a generous serving of caramelized onion rings.',
 250.00, 'Mains', './images/pork-steak-5.jpg', 1),

('Beef Afritada',
 'Tender beef chunks slow-cooked in a rich tomato sauce with bell peppers, potatoes, and carrots.',
 250.00, 'Mains', './images/beef-afritada.jpg', 1),

('Pork Afritada',
 'Succulent pork chunks slow-cooked in a savory tomato sauce with potatoes, carrots, and bell peppers.',
 250.00, 'Mains', './images/pork-afritada.jpg', 1),

('Lechon Kawali',
 'Crispy deep-fried pork belly served with liver sauce.',
 320.00, 'Mains', './images/Lechon Kawali.jpg', 1),

('Kare-Kare',
 'Oxtail stew in peanut sauce with vegetables and bagoong.',
 300.00, 'Mains', './images/karekare.jpg', 1),

-- Veggies
('Chopsuey',
 'A vibrant medley of crisp cauliflower, carrots, and cabbage stir-fried in a silky, savory glaze.',
 180.00, 'Veggies', './images/chapsuey.webp', 1),

('Pakbet',
 'Sautéed bitter melon with eggs, tomatoes, and onions. Healthy and flavorful.',
 180.00, 'Veggies', './images/pakbet.jpg', 1),

-- Desserts
('Leche Flan',
 'Rich caramel custard dessert. Smooth and creamy.',
 150.00, 'Desserts', './images/Leche-flan.jpg', 1),

('Halo-Halo',
 'Mixed shaved ice with sweet beans, fruits, and ube ice cream.',
 180.00, 'Desserts', './images/halo-halo.jpg', 1),

('Turon',
 'Fried banana spring rolls with jackfruit and brown sugar.',
 120.00, 'Desserts', './images/turon.webp', 1),

('Buko Pie',
 'A classic Filipino favorite featuring a buttery, flaky crust filled with a creamy, sweet custard and tender strips of young coconut.',
 120.00, 'Desserts', './images/buko-pie.jpg', 1),

-- Drinks
('Calamansi Juice',
 'Fresh squeezed calamansi lime juice. Refreshing and tangy.',
 80.00, 'Drinks', './images/kalamnsi.webp', 1),

('Mango Shake',
 'Creamy mango shake made with fresh Philippine mangoes.',
 100.00, 'Drinks', './images/Mango-Shake-Wide.webp', 1),

('Buko Juice',
 'Fresh young coconut water. Natural and hydrating.',
 90.00, 'Drinks', './images/buko.webp', 1);

-- ── Sample events ────────────────────────────────────────────
INSERT INTO events_tbl (title, description, event_date, event_time, image, is_active) VALUES
('Fiesta Filipino Night',
 'Celebrate Philippine culture with live music, folk dances, and a special fiesta menu.',
 '2026-04-15', '18:00:00', './images/events/fiesta.jpg', 1),

('Lechon Sunday Feast',
 'Every Sunday, enjoy a whole roasted lechon carving station with all the sides.',
 '2026-04-20', '12:00:00', './images/events/lechon.jpg', 1),

('Cooking Masterclass',
 'Learn the secrets behind classic Filipino dishes with our head chef.',
 '2026-05-05', '10:00:00', './images/events/masterclass.jpg', 1);

-- ── Verify ───────────────────────────────────────────────────
SELECT 'users'  AS tbl, COUNT(*) AS rows FROM users_tbl1
UNION ALL
SELECT 'menu',   COUNT(*) FROM menu_tbl
UNION ALL
SELECT 'events', COUNT(*) FROM events_tbl;