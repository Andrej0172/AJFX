CREATE DATABASE school_db;
USE school_db;

-- Tabel voor aanbiedingen/lessen
CREATE TABLE lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(8,2) NOT NULL
);

-- Tabel voor boekingen
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lesson_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id)
);

INSERT INTO lessons (title, description, price) VALUES
('Yoga voor beginners', 'Een introductie tot yoga.', 25.00),
('Fitness', 'Verbeter je kracht en conditie met een volledige fitnesssessie.', 25.00),
('Pilates', 'Focus op core-spieren en houding met Pilates-oefeningen.', 22.00);