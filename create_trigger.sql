-- TRIGGER untuk otomatis menghitung waktu_dasar sebelum INSERT
DELIMITER //
CREATE TRIGGER IF NOT EXISTS trg_calculate_waktu_dasar_insert
BEFORE INSERT ON routes
FOR EACH ROW
BEGIN
    DECLARE speed FLOAT DEFAULT 21;
    
    IF NEW.kondisi_medan = 'Medan Beraspal' THEN
        SET speed = 26;
    ELSEIF NEW.kondisi_medan = 'Medan Berkerikil' THEN
        SET speed = 17.5;
    ELSE
        SET speed = 21; -- Default / Medan Campuran
    END IF;

    SET NEW.waktu_dasar = ROUND((NEW.jarak / speed) * 60);
END;
//

-- TRIGGER untuk otomatis menghitung waktu_dasar sebelum UPDATE
CREATE TRIGGER IF NOT EXISTS trg_calculate_waktu_dasar_update
BEFORE UPDATE ON routes
FOR EACH ROW
BEGIN
    DECLARE speed FLOAT DEFAULT 21;
    
    -- Hanya hitung ulang jika medan atau jarak berubah
    IF NEW.kondisi_medan != OLD.kondisi_medan OR NEW.jarak != OLD.jarak OR NEW.waktu_dasar = 0 THEN
        IF NEW.kondisi_medan = 'Medan Beraspal' THEN
            SET speed = 26;
        ELSEIF NEW.kondisi_medan = 'Medan Berkerikil' THEN
            SET speed = 17.5;
        ELSE
            SET speed = 21; -- Default / Medan Campuran
        END IF;

        SET NEW.waktu_dasar = ROUND((NEW.jarak / speed) * 60);
    END IF;
END;
//
DELIMITER ;
