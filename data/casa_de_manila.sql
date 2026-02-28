-- ============================================================
--  Casa De Manila — Full Database Setup
--  File: database/casa_de_manila.sql
--  Import this ONCE via phpMyAdmin → Import tab
-- ============================================================

CREATE DATABASE IF NOT EXISTS casa_de_manila
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE casa_de_manila;

-- ── TABLE: users_tbl1 ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users_tbl1 (
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
CREATE TABLE IF NOT EXISTS menu_tbl (
  menu_id      INT(11)      NOT NULL AUTO_INCREMENT,
  name         VARCHAR(100) NOT NULL,
  description  TEXT         DEFAULT NULL,
  price        DECIMAL(8,2) NOT NULL,
  category     VARCHAR(50)  NOT NULL,
  image        VARCHAR(255) DEFAULT NULL,
  is_available TINYINT(1)   NOT NULL DEFAULT 1,
  created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (menu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLE: orders_tbl ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS orders_tbl (
  order_id     INT(11)      NOT NULL AUTO_INCREMENT,
  uid          INT(11)      NOT NULL,
  order_date   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  total_amount DECIMAL(10,2) NOT NULL,
  status       ENUM('pending','confirmed','preparing','ready','completed','cancelled')
                             NOT NULL DEFAULT 'pending',
  notes        TEXT         DEFAULT NULL,
  PRIMARY KEY (order_id),
  FOREIGN KEY (uid) REFERENCES users_tbl1(uid) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLE: order_items_tbl ───────────────────────────────────
CREATE TABLE IF NOT EXISTS order_items_tbl (
  item_id   INT(11)      NOT NULL AUTO_INCREMENT,
  order_id  INT(11)      NOT NULL,
  menu_id   INT(11)      DEFAULT NULL,
  item_name VARCHAR(100) NOT NULL,
  price     DECIMAL(8,2) NOT NULL,
  quantity  INT(5)       NOT NULL DEFAULT 1,
  subtotal  DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (item_id),
  FOREIGN KEY (order_id) REFERENCES orders_tbl(order_id)  ON DELETE CASCADE,
  FOREIGN KEY (menu_id)  REFERENCES menu_tbl(menu_id)     ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── TABLE: reservations_tbl ──────────────────────────────────
CREATE TABLE IF NOT EXISTS reservations_tbl (
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
CREATE TABLE IF NOT EXISTS events_tbl (
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
CREATE TABLE IF NOT EXISTS contacts_tbl (
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

-- Default admin (password: Admin@1234 — CHANGE AFTER FIRST LOGIN)
INSERT INTO users_tbl1 (username, email, password_us, role) VALUES
('admin', 'admin@casamanila.ph',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Menu items (matches your menu.html exactly)
INSERT INTO menu_tbl (name, description, price, category, image) VALUES
('Chicken Adobo',
 'Succulent chicken slow-braised in a savory-tangy blend of fermented vinegar, premium soy sauce, and toasted garlic. Finished with cracked black peppercorns for a deep, aromatic flavor.',
 250.00, 'Mains', './images/adobo.jpg'),
('Pork Steak',
 'Tender pork slices marinated in soy sauce and calamansi, topped with a generous serving of caramelized onion rings.',
 250.00, 'Mains', './images/pork-steak-5.jpg'),
('Beef Afritada',
 'Tender beef chunks slow-cooked in a rich tomato sauce with bell peppers, potatoes, and carrots.',
 250.00, 'Mains', './images/beef-afritada.jpg'),
('Pork Afritada',
 'Succulent pork chunks slow-cooked in a savory tomato sauce with potatoes, carrots, and bell peppers.',
 250.00, 'Mains', './images/pork-afritada.jpg'),
('Lechon Kawali',
 'Crispy deep-fried pork belly served with liver sauce.',
 320.00, 'Mains', './images/Lechon Kawali.jpg'),
('Kare-Kare',
 'Oxtail stew in peanut sauce with vegetables and bagoong.',
 300.00, 'Mains', './images/karekare.jpg'),
('Chopsuey',
 'A vibrant medley of crisp cauliflower, carrots, and cabbage stir-fried in a silky, savory glaze.',
 180.00, 'Veggies', './images/chapsuey.webp'),
('Pakbet',
 'Sautéed bitter melon with eggs, tomatoes, and onions. Healthy and flavorful.',
 180.00, 'Veggies', './images/pakbet.jpg'),
('Leche Flan',
 'Rich caramel custard dessert. Smooth and creamy.',
 150.00, 'Desserts', './images/Leche-flan.jpg'),
('Halo-Halo',
 'Mixed shaved ice with sweet beans, fruits, and ube ice cream.',
 180.00, 'Desserts', './images/halo-halo.jpg'),
('Turon',
 'Fried banana spring rolls with jackfruit and brown sugar.',
 120.00, 'Desserts', './images/turon.webp'),
('Buko Pie',
 'A classic Filipino favorite featuring a buttery, flaky crust filled with a creamy, sweet custard and tender strips of young coconut.',
 120.00, 'Desserts', './images/buko-pie.jpg'),
('Calamansi Juice',
 'Fresh squeezed calamansi lime juice. Refreshing and tangy.',
 80.00, 'Drinks', './images/kalamnsi.webp'),
('Mango Shake',
 'Creamy mango shake made with fresh Philippine mangoes.',
 100.00, 'Drinks', './images/Mango-Shake-Wide.webp'),
('Buko Juice',
 'Fresh young coconut water. Natural and hydrating.',
 90.00, 'Drinks', './images/buko.webp');

-- Sample events
INSERT INTO events_tbl (title, description, event_date, event_time, image) VALUES
('Fiesta Filipino Night',
 'Celebrate Philippine culture with live music, folk dances, and a special fiesta menu.',
 '2026-03-15', '18:00:00', './images/events/fiesta.jpg'),
('Lechon Sunday Feast',
 'Every Sunday, enjoy a whole roasted lechon carving station with all the sides.',
 '2026-03-08', '12:00:00', './images/events/lechon.jpg'),
('Cooking Masterclass',
 'Learn the secrets behind classic Filipino dishes with our head chef.',
 '2026-04-05', '10:00:00', './images/events/masterclass.jpg');