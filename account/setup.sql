-- Voer dit uit in phpMyAdmin of de MySQL console

CREATE DATABASE IF NOT EXISTS ajfx_db CHARACTER SET utf8 COLLATE utf8_general_ci;

USE ajfx_db;

-- Gebruikers tabel
CREATE TABLE IF NOT EXISTS gebruikers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    wachtwoord VARCHAR(255) NOT NULL,
    lidmaatschap ENUM('Basis', 'Premium', 'Pro') DEFAULT 'Basis',
    aangemaakt_op DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Lessen tabel
CREATE TABLE IF NOT EXISTS lessen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    beschrijving TEXT,
    dag VARCHAR(20),
    tijd TIME,
    max_deelnemers INT DEFAULT 10
);

-- Reserveringen tabel
CREATE TABLE IF NOT EXISTS reserveringen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gebruiker_id INT NOT NULL,
    les_id INT NOT NULL,
    datum DATE NOT NULL,
    FOREIGN KEY (gebruiker_id) REFERENCES gebruikers(id),
    FOREIGN KEY (les_id) REFERENCES lessen(id)
);

-- Testgebruiker aanmaken (wachtwoord: test123)
INSERT INTO gebruikers (naam, email, wachtwoord, lidmaatschap) VALUES
('Jan de Vries', 'jan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Premium');

-- Testlessen aanmaken
INSERT INTO lessen (naam, beschrijving, dag, tijd, max_deelnemers) VALUES
('Yoga Beginners', 'Yoga les voor beginners', 'Maandag', '09:00:00', 12),
('Spinning', 'Intensieve spinning les', 'Woensdag', '18:00:00', 15),
('Pilates', 'Core training met Pilates', 'Vrijdag', '10:00:00', 10);

-- Testreservering
INSERT INTO reserveringen (gebruiker_id, les_id, datum) VALUES
(1, 1, CURDATE()),
(1, 2, DATE_ADD(CURDATE(), INTERVAL 2 DAY));
