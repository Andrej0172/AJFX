DROP DATABASE IF EXISTS lessen;
CREATE DATABASE lessen;
USE lessen;

CREATE TABLE ledenoverzicht (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    lidnummer INT UNSIGNED NOT NULL DEFAULT 0,
    naam VARCHAR(100) NOT NULL,
    PRIMARY KEY (Id)
) ENGINE=InnoDB;

CREATE TABLE lessenoverzicht (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    lesnaam VARCHAR(100) NOT NULL,
    trainer VARCHAR(100) NOT NULL DEFAULT '',
    datum DATE NOT NULL DEFAULT '2025-01-01',
    tijd TIME NOT NULL DEFAULT '00:00:00',
    locatie VARCHAR(100) NOT NULL DEFAULT '',
    PRIMARY KEY (Id)
) ENGINE=InnoDB;

CREATE TABLE reserveringen (
    Id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    lid_id INT UNSIGNED NOT NULL,
    les_id INT UNSIGNED NOT NULL,
    datum TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (Id),
    INDEX (lid_id),
    INDEX (les_id),
    CONSTRAINT fk_lid
        FOREIGN KEY (lid_id) REFERENCES ledenoverzicht(Id)
        ON DELETE CASCADE,
    CONSTRAINT fk_les
        FOREIGN KEY (les_id) REFERENCES lessenoverzicht(Id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO ledenoverzicht (lidnummer, naam) VALUES
(1, 'Ali'),(2, 'Sophie'),(3, 'Jeroen'),(4, 'Fatima'),(5, 'Lucas'),
(6, 'Emma'),(7, 'Noah'),(8, 'Lotte'),(9, 'Milan'),(10, 'Sara');

INSERT INTO lessenoverzicht (lesnaam, trainer, datum, tijd, locatie) VALUES
('Yoga',          'Lisa',   '2025-06-01', '09:00:00', 'Zaal A'),
('Boxen',         'Mark',   '2025-06-01', '10:00:00', 'Zaal B'),
('Fitness',       'Sara',   '2025-06-02', '08:00:00', 'Zaal A'),
('Pilates',       'Tom',    '2025-06-02', '11:00:00', 'Zaal C'),
('Spinning',      'Nina',   '2025-06-03', '07:00:00', 'Zaal B'),
('Crossfit',      'Daan',   '2025-06-03', '12:00:00', 'Zaal A'),
('Zumba',         'Roos',   '2025-06-04', '09:30:00', 'Zaal C'),
('HIIT',          'Kevin',  '2025-06-04', '10:30:00', 'Zaal B'),
('Krachttraining','Hana',   '2025-06-05', '08:30:00', 'Zaal A'),
('Dans',          'Pieter', '2025-06-05', '13:00:00', 'Zaal C');

INSERT INTO reserveringen (lid_id, les_id) VALUES
(1,1),(2,2),(3,3),(4,4),(5,5),
(6,6),(7,7),(8,8),(9,9),(10,10);