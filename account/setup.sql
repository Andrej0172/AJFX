-- ============================================
-- AJFX Database
-- ============================================

CREATE DATABASE IF NOT EXISTS ajfx CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ajfx;

-- ============================================
-- GEBRUIKERS (accounts / inloggen)
-- ============================================
CREATE TABLE IF NOT EXISTS gebruikers (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    naam          VARCHAR(100)        NOT NULL,
    email         VARCHAR(150)        NOT NULL UNIQUE,
    wachtwoord    VARCHAR(255)        NOT NULL,
    aangemaakt_op TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- MEDEWERKERS
-- ============================================
CREATE TABLE IF NOT EXISTS medewerkers (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    naam          VARCHAR(100)        NOT NULL,
    email         VARCHAR(150)        NOT NULL UNIQUE,
    telefoon      VARCHAR(20),
    functie       VARCHAR(100),
    aangemaakt_op TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- LEDEN
-- ============================================
CREATE TABLE IF NOT EXISTS leden (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    voornaam      VARCHAR(100)        NOT NULL,
    achternaam    VARCHAR(100)        NOT NULL,
    email         VARCHAR(150)        NOT NULL UNIQUE,
    telefoon      VARCHAR(20),
    geboortedatum DATE,
    aangemaakt_op TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- LESSEN
-- ============================================
CREATE TABLE IF NOT EXISTS lessen (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    naam          VARCHAR(150)        NOT NULL,
    beschrijving  TEXT,
    datum         DATE                NOT NULL,
    tijd          TIME                NOT NULL,
    duur          INT                 NOT NULL COMMENT 'Duur in minuten',
    max_deelnemers INT                DEFAULT 20,
    medewerker_id INT,
    aangemaakt_op TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (medewerker_id) REFERENCES medewerkers(id) ON DELETE SET NULL
);

-- ============================================
-- RESERVERINGEN
-- ============================================
CREATE TABLE IF NOT EXISTS reserveringen (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    lid_id        INT                 NOT NULL,
    les_id        INT                 NOT NULL,
    status        ENUM('bevestigd', 'geannuleerd', 'wachtlijst') DEFAULT 'bevestigd',
    aangemaakt_op TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lid_id) REFERENCES leden(id) ON DELETE CASCADE,
    FOREIGN KEY (les_id) REFERENCES lessen(id) ON DELETE CASCADE,
    UNIQUE KEY uniek_reservering (lid_id, les_id)
);

-- ============================================
-- VOORBEELDDATA
-- ============================================

-- Medewerkers
INSERT INTO medewerkers (naam, email, telefoon, functie) VALUES
('Jan de Vries',    'jan@ajfx.nl',    '0612345678', 'Trainer'),
('Sara Bakker',     'sara@ajfx.nl',   '0623456789', 'Trainer'),
('Tom Smit',        'tom@ajfx.nl',    '0634567890', 'Manager');

-- Leden
INSERT INTO leden (voornaam, achternaam, email, telefoon, geboortedatum) VALUES
('Ali',     'Hassan',   'ali@email.com',   '0645678901', '1995-03-12'),
('Fatima',  'Yilmaz',   'fatima@email.com','0656789012', '1998-07-22'),
('Kevin',   'Peters',   'kevin@email.com', '0667890123', '1990-11-05'),
('Lena',    'Visser',   'lena@email.com',  '0678901234', '2000-01-30'),
('Marco',   'de Boer',  'marco@email.com', '0689012345', '1993-09-18');

-- Lessen
INSERT INTO lessen (naam, beschrijving, datum, tijd, duur, max_deelnemers, medewerker_id) VALUES
('Beginners Yoga',       'Yoga voor beginners, rustig tempo.',         '2025-04-01', '09:00:00', 60, 15, 1),
('Gevorderd Fitness',    'Intensieve training voor gevorderden.',       '2025-04-02', '10:30:00', 90, 12, 2),
('Pilates Basis',        'Pilates gericht op core kracht.',            '2025-04-03', '08:00:00', 60, 10, 1),
('Zumba Fun',            'Dansen en bewegen op muziek.',               '2025-04-04', '11:00:00', 60, 20, 2),
('Kickboksen Intro',     'Introductie kickboksen voor iedereen.',      '2025-04-05', '14:00:00', 75, 12, 3);

-- Reserveringen
INSERT INTO reserveringen (lid_id, les_id, status) VALUES
(1, 1, 'bevestigd'),
(2, 1, 'bevestigd'),
(3, 2, 'bevestigd'),
(4, 3, 'bevestigd'),
(5, 4, 'bevestigd'),
(1, 5, 'bevestigd'),
(2, 3, 'wachtlijst');

-- Testgebruiker (wachtwoord: test123)
INSERT INTO gebruikers (naam, email, wachtwoord) VALUES
('Test Gebruiker', 'test@ajfx.nl', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');