-- Step : 1
/********************************************************************************
-- Doel : Maak een nieuwe database aan heet lessen
-- ******************************************************************************
-- Versie     Datum          Auteur         Omschrijving
-- ******     **********     *******        **************
-- 01         26-02-2026     Xingru pan     database aangemaakt
*********************************************************************************/ 
drop database if exists lessen;
create database lessen;
use lessen;


-- Step : 2
/********************************************************************************
-- Doel : Maak een nieuwe tabel aan heet lessenoverzicht
-- ******************************************************************************
-- Versie     Datum          Auteur         Omschrijving
-- ******     **********     *******        **************
-- 01         26-02-2026     Xingru pan     tabel aangemaakt
*********************************************************************************/

create table lessenoverzicht
(
<<<<<<< HEAD
    Id                  INT UNSIGNED            NOT NULL    AUTO_INCREMENT
    ,lessen             VARCHAR(255)            NOT NULL
    ,trainer            VARCHAR(255)            NOT NULL
    ,locatie            VARCHAR(255)            NOT NULL    default 'Sportschool Utrecht'
    ,datum              DATE                    NOT NULL
    ,tijd               TIME                    NOT NULL
    ,lesprijs           DECIMAL(5,2)            NOT NULL
    ,PRIMARY KEY (Id)
=======
    Id                  INT UNSIGNED            NOT NULL    AUTO_INCREMENT,
    lessen              VARCHAR(255)            NOT NULL,
    trainer             VARCHAR(255)            NOT NULL,
    locatie             VARCHAR(255)            NOT NULL    default 'Sportschool Utrecht',
    datum               DATE                    NOT NULL,
    tijd                TIME                    NOT NULL,
    PRIMARY KEY (Id)
>>>>>>> DEV-Branch
)ENGINE=InnoDB;

-- Step : 3
/********************************************************************************
-- Doel : waarde toevoegen aan de tabel lessenoverzicht
-- ******************************************************************************
-- Versie     Datum          Auteur         Omschrijving
-- ******     **********     *******        **************
-- 01         26-02-2026     Xingru pan     waarde toegevoegd
*********************************************************************************/

INSERT INTO lessenoverzicht
(
<<<<<<< HEAD
    lessen
    ,trainer
    ,locatie
    ,datum
    ,tijd
    ,lesprijs
    
=======
    lessen,
    trainer,
    locatie,
    datum,
    tijd
>>>>>>> DEV-Branch
)
VALUES 
('Bootcamp', 'Mike Jansen', 'Sportschool Utrecht', '2026-03-13', '09:00:00', 12.50),
('Yoga', 'Sanne de Vries', 'Sportschool Utrecht', '2026-03-14', '10:00:00', 10.00),
('Spinning', 'Tom Bakker', 'Sportschool Utrecht', '2026-03-14', '18:30:00', 11.50),
('Crossfit', 'Kevin Smit', 'Sportschool Utrecht', '2026-03-15', '17:00:00', 15.00),
('Zumba', 'Laura Meijer', 'Sportschool Utrecht', '2026-03-16', '19:30:00', 9.50),
('Pilates', 'Emma Willems', 'Sportschool Utrecht', '2026-03-17', '08:30:00', 10.50),
('Kickboksen', 'Rachid El Amrani', 'Sportschool Utrecht', '2026-03-18', '20:00:00', 13.00),
('Bodypump', 'Nina Verhoeven', 'Sportschool Utrecht', '2026-03-19', '18:00:00', 12.00),
('HIIT Training', 'Daan Mulder', 'Sportschool Utrecht', '2026-03-20', '17:30:00', 14.00),
('Core Stability', 'Sophie Kramer', 'Sportschool Utrecht', '2026-03-21', '09:30:00', 11.00);

-- Step : 4
/********************************************************************************
-- Doel : Maak een nieuwe tabel aan heet ledenoverzicht
-- ******************************************************************************
-- Versie     Datum          Auteur         Omschrijving
-- ******     **********     *******        **************
-- 01         26-02-2026     Xingru pan     tabel aangemaakt
*********************************************************************************/

create table ledenoverzicht
(
    Id                  INT UNSIGNED            NOT NULL    AUTO_INCREMENT,
    leden               VARCHAR(255)            NOT NULL,
    lidnummer           INT UNSIGNED            NOT NULL    AUTO_INCREMENT,
    lessen              VARCHAR(255)            NOT NULL,
    leeftijd            DECIMAL(3,0)            NOT NULL,
    email               VARCHAR(255)            NOT NULL,
    PRIMARY KEY (Id),
    UNIQUE (lidnummer)
)ENGINE=InnoDB;

-- ❗ FIX: MySQL staat maar 1 AUTO_INCREMENT toe → dus lidnummer aanpassen

DROP TABLE ledenoverzicht;

create table ledenoverzicht
(
    Id                  INT UNSIGNED            NOT NULL    AUTO_INCREMENT,
    leden               VARCHAR(255)            NOT NULL,
    lidnummer           INT UNSIGNED            NOT NULL,
    lessen              VARCHAR(255)            NOT NULL,
    leeftijd            DECIMAL(3,0)            NOT NULL,
    email               VARCHAR(255)            NOT NULL,
    PRIMARY KEY (Id)
)ENGINE=InnoDB;


-- Step : 5
/********************************************************************************
-- Doel : waarde toevoegen aan de tabel ledenoverzicht
-- ******************************************************************************
-- Versie     Datum          Auteur         Omschrijving
-- ******     **********     *******        **************
-- 01         26-02-2026     Xingru pan     waarde toegevoegd
*********************************************************************************/

INSERT INTO ledenoverzicht
(
    leden,
    lidnummer,
    lessen,
    leeftijd,
    email
)
VALUES
('Jan Jansen', 1, 'Yoga, Fitness', 28, 'jan.jansen@example.com'),
('Mike Jansen', 2, 'Fitness, Boksen', 31, 'mike.jansen@example.com'),
('Piet Pietersen', 3, 'Zwemmen, Yoga', 24, 'piet.pietersen@example.com'),
('Sanne de Vries', 4, 'Yoga, Pilates', 26, 'sanne.vries@example.com'),
('Anna de Vries', 5, 'Dans, Fitness', 22, 'anna.devries@example.com'),
('Tom Bakker', 6, 'Fitness, Boksen', 29, 'tom.bakker@example.com'),
('Mark Bakker', 7, 'Zwemmen, Fitness', 35, 'mark.bakker@example.com'),
('Kevin Smit', 8, 'Fitness, Hardlopen', 27, 'kevin.smit@example.com'),
('Lisa Meijer', 9, 'Pilates, Yoga', 30, 'lisa.meijer@example.com');