-- Step : 1
/********************************************************************************
-- Doel : Maak een nieuwe database aan heet lessen
-- ******************************************************************************
-- Versie     Datum          Auteur			Omschrijving
-- ******     **********     *******		**************
-- 01         26-02-2026     Xingru pan     database aangemaakt
*********************************************************************************/ 
drop database if exists lessen;
create database lessen;
use lessen;


-- Step : 2
/********************************************************************************
-- Doel : Maak een nieuwe tabel aan heet lessenoverzicht
-- ******************************************************************************
-- Versie     Datum          Auteur			Omschrijving
-- ******     **********     *******		**************
-- 01         26-02-2026     Xingru pan     tabel aangemaakt
*********************************************************************************/

create table lessenoverzicht
(
    Id                  INT UNSIGNED            NOT NULL    AUTO_INCREMENT
    ,lessen             VARCHAR(255)            NOT NULL
    ,trainer            VARCHAR(255)            NOT NULL
    ,locatie            VARCHAR(255)            NOT NULL    default 'Sportschool Utrecht'
    ,datum              DATE                    NOT NULL
    ,tijd               TIME                    NOT NULL
    ,PRIMARY KEY (Id)
)ENGINE=InnoDB;

-- Step : 3
/********************************************************************************
-- Doel : waarde toevoegen aan de tabel lessenoverzicht
-- ******************************************************************************
-- Versie     Datum          Auteur			Omschrijving
-- ******     **********     *******		**************
-- 01         26-02-2026     Xingru pan     waarde toegevoegd
*********************************************************************************/

INSERT INTO lessenoverzicht
(
    lessen
    ,trainer
    ,locatie
    ,datum
    ,tijd
)
VALUES 
('Bootcamp', 'Mike Jansen', 'Sportschool Utrecht', '2026-03-13', '09:00:00'),
('Yoga', 'Sanne de Vries', 'Sportschool Utrecht', '2026-03-14', '10:00:00'),
('Spinning', 'Tom Bakker', 'Sportschool Utrecht', '2026-03-14', '18:30:00'),
('Crossfit', 'Kevin Smit', 'Sportschool Utrecht', '2026-03-15', '17:00:00'),
('Zumba', 'Laura Meijer', 'Sportschool Utrecht', '2026-03-16', '19:30:00'),
('Pilates', 'Emma Willems', 'Sportschool Utrecht', '2026-03-17', '08:30:00'),
('Kickboksen', 'Rachid El Amrani', 'Sportschool Utrecht', '2026-03-18', '20:00:00'),
('Bodypump', 'Nina Verhoeven', 'Sportschool Utrecht', '2026-03-19', '18:00:00'),
('HIIT Training', 'Daan Mulder', 'Sportschool Utrecht', '2026-03-20', '17:30:00'),
('Core Stability', 'Sophie Kramer', 'Sportschool Utrecht', '2026-03-21', '09:30:00');