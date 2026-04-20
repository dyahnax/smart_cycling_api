<?php
// FILE: apply_trigger.php
require 'koneksi.php';

echo "Mengaplikasikan Database Trigger...\n";

// Drop existing triggers if any to avoid errors
mysqli_query($con, "DROP TRIGGER IF EXISTS trg_calculate_waktu_dasar_insert");
mysqli_query($con, "DROP TRIGGER IF EXISTS trg_calculate_waktu_dasar_update");

// SQL Trigger Insert
$sql_insert = "
CREATE TRIGGER trg_calculate_waktu_dasar_insert
BEFORE INSERT ON routes
FOR EACH ROW
BEGIN
    DECLARE speed FLOAT DEFAULT 21;
    IF NEW.kondisi_medan LIKE '%Beraspal%' THEN
        SET speed = 26;
    ELSEIF NEW.kondisi_medan LIKE '%Berkerikil%' THEN
        SET speed = 17.5;
    ELSEIF NEW.kondisi_medan LIKE '%Campuran%' THEN
        SET speed = 21;
    ELSE
        SET speed = 21;
    END IF;
    SET NEW.waktu_dasar = ROUND((NEW.jarak / speed) * 60);
END;";

// SQL Trigger Update
$sql_update = "
CREATE TRIGGER trg_calculate_waktu_dasar_update
BEFORE UPDATE ON routes
FOR EACH ROW
BEGIN
    DECLARE speed FLOAT DEFAULT 21;
    IF NEW.kondisi_medan != OLD.kondisi_medan OR NEW.jarak != OLD.jarak OR NEW.waktu_dasar = 0 THEN
        IF NEW.kondisi_medan LIKE '%Beraspal%' THEN
            SET speed = 26;
        ELSEIF NEW.kondisi_medan LIKE '%Berkerikil%' THEN
            SET speed = 17.5;
        ELSEIF NEW.kondisi_medan LIKE '%Campuran%' THEN
            SET speed = 21;
        ELSE
            SET speed = 21;
        END IF;
        SET NEW.waktu_dasar = ROUND((NEW.jarak / speed) * 60);
    END IF;
END;";

if (mysqli_query($con, $sql_insert)) {
    echo "OK: Trigger INSERT berhasil dibuat.\n";
} else {
    echo "ERROR (Insert): " . mysqli_error($con) . "\n";
}

if (mysqli_query($con, $sql_update)) {
    echo "OK: Trigger UPDATE berhasil dibuat.\n";
} else {
    echo "ERROR (Update): " . mysqli_error($con) . "\n";
}

echo "Proses Selesai.";
?>
