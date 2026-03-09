
CREATE TABLE IF NOT EXISTS medewerkers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    functie VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    afdeling VARCHAR(50) NOT NULL
);


INSERT INTO medewerkers (naam, functie, email, afdeling) VALUES
('Sophie van den Berg', 'Frontend Developer', 'sophie.vdberg@bedrijf.nl', 'IT'),
('Daan Janssen',        'Product Manager',    'd.janssen@bedrijf.nl',     'Product'),
('Lena Hoekstra',       'UX Designer',        'l.hoekstra@bedrijf.nl',    'Design'),
('Pieter de Vries',     'Backend Developer',  'p.devries@bedrijf.nl',     'IT'),
('Iris Mulder',         'HR Adviseur',        'i.mulder@bedrijf.nl',      'HR'),
('Tom Bakker',          'Data Analist',       't.bakker@bedrijf.nl',      'Data'),
('Emma Smit',           'Scrum Master',       'e.smit@bedrijf.nl',        'IT'),
('Lars Visser',         'DevOps Engineer',    'l.visser@bedrijf.nl',      'IT');