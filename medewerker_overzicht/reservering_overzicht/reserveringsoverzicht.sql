USE lessen;

CREATE TABLE IF NOT EXISTS reserveringen (
    Id      INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    lid_id  INT UNSIGNED   NOT NULL,
    les_id  INT UNSIGNED   NOT NULL,
    PRIMARY KEY (Id),
    FOREIGN KEY (lid_id) REFERENCES ledenoverzicht(Id),
    FOREIGN KEY (les_id) REFERENCES lessenoverzicht(Id)
) ENGINE=InnoDB;

INSERT INTO reserveringen (lid_id, les_id) VALUES
(1, 1), (2, 2), (3, 3), (4, 4), (5, 5),
(6, 6), (7, 7), (8, 8), (9, 9), (1, 10);