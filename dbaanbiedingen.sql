-- Maak een nieuwe database aan genaamd school_db
CREATE DATABASE school_db;

-- Selecteer de database om ermee te werken
USE school_db;

-- ======================================================
-- Tabel voor aanbiedingen/lessen
-- ======================================================
CREATE TABLE lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- Uniek ID voor elke les
    title VARCHAR(255) NOT NULL,             -- Titel van de les
    description TEXT NOT NULL,               -- Beschrijving van de les
    price DECIMAL(8,2) NOT NULL             -- Prijs van de les (bijv. 25.00)
);

-- ======================================================
-- Tabel voor boekingen
-- ======================================================
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,       -- Uniek ID voor elke boeking
    lesson_id INT NOT NULL,                  -- Verwijzing naar de les (foreign key)
    name VARCHAR(100) NOT NULL,              -- Naam van de persoon die boekt
    email VARCHAR(100) NOT NULL,             -- E-mail van de persoon
    phone VARCHAR(20),                        -- Telefoonnummer (optioneel)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Datum/tijd van de boeking
    FOREIGN KEY (lesson_id) REFERENCES lessons(id)  -- Koppeling met lessons tabel
);

-- ======================================================
-- Voorbeeldgegevens invoegen in de lessons tabel
-- ======================================================
INSERT INTO lessons (title, description, price) VALUES
('Yoga voor beginners', 'Een introductie tot yoga.', 25.00),
('Fitness', 'Verbeter je kracht en conditie met een volledige fitnesssessie.', 25.00),
('Pilates', 'Focus op core-spieren en houding met Pilates-oefeningen.', 22.00);