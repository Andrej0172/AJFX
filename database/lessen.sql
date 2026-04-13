-- ============================================================
-- Database: lessen
-- Project:  AJFX Sportschool
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS lessen;
CREATE DATABASE lessen;
USE lessen;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- Tabel 1: lessenoverzicht
-- ============================================================

CREATE TABLE lessenoverzicht
(
    Id          INT UNSIGNED    NOT NULL    AUTO_INCREMENT,
    lesnaam     VARCHAR(255)    NOT NULL,
    trainer     VARCHAR(255)    NOT NULL,
    locatie     VARCHAR(255)    NOT NULL    DEFAULT 'Sportschool Utrecht',
    datum       DATE            NOT NULL,
    tijd        TIME            NOT NULL,
    lesprijs    DECIMAL(5,2)    NOT NULL    DEFAULT 0.00,
    PRIMARY KEY (Id)
) ENGINE=InnoDB;

INSERT INTO lessenoverzicht (lesnaam, trainer, locatie, datum, tijd, lesprijs) VALUES
('Bootcamp',        'Mike Jansen',      'Sportschool Utrecht', '2026-03-13', '09:00:00', 12.50),
('Yoga',            'Sanne de Vries',   'Sportschool Utrecht', '2026-03-14', '10:00:00', 10.00),
('Spinning',        'Tom Bakker',       'Sportschool Utrecht', '2026-03-14', '18:30:00', 11.50),
('Crossfit',        'Kevin Smit',       'Sportschool Utrecht', '2026-03-15', '17:00:00', 15.00),
('Zumba',           'Laura Meijer',     'Sportschool Utrecht', '2026-03-16', '19:30:00',  9.50),
('Pilates',         'Emma Willems',     'Sportschool Utrecht', '2026-03-17', '08:30:00', 10.50),
('Kickboksen',      'Rachid El Amrani', 'Sportschool Utrecht', '2026-03-18', '20:00:00', 13.00),
('Bodypump',        'Nina Verhoeven',   'Sportschool Utrecht', '2026-03-19', '18:00:00', 12.00),
('HIIT Training',   'Daan Mulder',      'Sportschool Utrecht', '2026-03-20', '17:30:00', 14.00),
('Core Stability',  'Sophie Kramer',    'Sportschool Utrecht', '2026-03-21', '09:30:00', 11.00);

-- ============================================================
-- Tabel 2: ledenoverzicht
-- ============================================================

CREATE TABLE ledenoverzicht
(
    Id          INT UNSIGNED    NOT NULL    AUTO_INCREMENT,
    naam        VARCHAR(255)    NOT NULL,
    lidnummer   INT UNSIGNED    NOT NULL,
    lessen      VARCHAR(255)    NOT NULL    DEFAULT '',
    leeftijd    DECIMAL(3,0)    NOT NULL    DEFAULT 0,
    email       VARCHAR(255)    NOT NULL    DEFAULT '',
    PRIMARY KEY (Id)
) ENGINE=InnoDB;

INSERT INTO ledenoverzicht (naam, lidnummer, lessen, leeftijd, email) VALUES
('Jan Jansen',      1, 'Yoga, Fitness',       28, 'jan.jansen@example.com'),
('Mike Jansen',     2, 'Fitness, Boksen',     31, 'mike.jansen@example.com'),
('Piet Pietersen',  3, 'Zwemmen, Yoga',       24, 'piet.pietersen@example.com'),
('Sanne de Vries',  4, 'Yoga, Pilates',       26, 'sanne.vries@example.com'),
('Anna de Vries',   5, 'Dans, Fitness',       22, 'anna.devries@example.com'),
('Tom Bakker',      6, 'Fitness, Boksen',     29, 'tom.bakker@example.com'),
('Mark Bakker',     7, 'Zwemmen, Fitness',    35, 'mark.bakker@example.com'),
('Kevin Smit',      8, 'Fitness, Hardlopen',  27, 'kevin.smit@example.com'),
('Lisa Meijer',     9, 'Pilates, Yoga',       30, 'lisa.meijer@example.com');

-- ============================================================
-- Tabel 3: reserveringen
-- ============================================================

CREATE TABLE reserveringen
(
    Id          INT UNSIGNED    NOT NULL    AUTO_INCREMENT,
    lid_id      INT UNSIGNED    NOT NULL,
    les_id      INT UNSIGNED    NOT NULL,
    PRIMARY KEY (Id),
    CONSTRAINT fk_lid FOREIGN KEY (lid_id) REFERENCES ledenoverzicht(Id) ON DELETE CASCADE,
    CONSTRAINT fk_les FOREIGN KEY (les_id) REFERENCES lessenoverzicht(Id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO reserveringen (lid_id, les_id) VALUES
(1,1),(2,2),(3,3),(4,4),(5,5),
(6,6),(7,7),(8,8),(9,9),(1,10);