use dans;
drop procedure create_attendance_column;

DELIMITER //
CREATE PROCEDURE create_attendance_column(
    IN table_name VARCHAR(255),
    IN column_name VARCHAR(255)
)
BEGIN
    SET @query = CONCAT('ALTER TABLE ', table_name, ' ADD ', column_name, ' BOOLEAN DEFAULT TRUE;');
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //
DELIMITER ;



DELIMITER //

CREATE PROCEDURE CalculateAttendancePercentage(subject_code VARCHAR(100))
BEGIN
    DECLARE total_days INT;
    DECLARE percentage_var DECIMAL(5,2);

    -- Calculate total days dynamically (assuming dayspresent and daysabsent are columns)
    SET @sql = CONCAT('SELECT COUNT(*) AS total_days FROM ', subject_code);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    FETCH stmt INTO total_days;
    DEALLOCATE PREPARE stmt;

    -- Calculate percentage and update in one statement
    SET @update_sql = CONCAT(
        'UPDATE ', subject_code, ' AS t ',
        'JOIN (SELECT RegNo, ',
        '       IF(', total_days, ' > 0, (dayspresent / ', total_days, ') * 100, 0) AS percentage ',
        '      FROM ', subject_code, ') AS s ',
        'ON t.RegNo = s.RegNo ',
        'SET t.percentage = s.percentage'
    );
    PREPARE update_stmt FROM @update_sql;
    EXECUTE update_stmt;
    DEALLOCATE PREPARE update_stmt;
END //


DELIMITER ;



CREATE DEFINER=`root`@`localhost` PROCEDURE `insert_oec_choices_procedure`(
    IN `RegNo_param` VARCHAR(45),
    IN `Name_param` VARCHAR(45),
    IN `Department_param` VARCHAR(45)
)
BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE table_name VARCHAR(45);
    DECLARE limit_oec INT;
    DECLARE row_count INT;
    DECLARE selected BOOLEAN DEFAULT FALSE;

    -- Loop through columns 1 to 8
    WHILE i <= 8 DO
        -- Determine the table name from the i-th column
        CASE i
            WHEN 1 THEN SELECT `1` INTO table_name FROM oec_choices WHERE RegNo = RegNo_param;
            WHEN 2 THEN SELECT `2` INTO table_name FROM oec_choices WHERE RegNo = RegNo_param;
            WHEN 3 THEN SELECT `3` INTO table_name FROM oec_choices WHERE RegNo = RegNo_param;
            WHEN 4 THEN SELECT `4` INTO table_name FROM oec_choices WHERE RegNo = RegNo_param;
            WHEN 5 THEN SELECT `5` INTO table_name FROM oec_choices WHERE RegNo = RegNo_param;
            WHEN 6 THEN SELECT `6` INTO table_name FROM oec_choices WHERE RegNo = RegNo_param;
            WHEN 7 THEN SELECT `7` INTO table_name FROM oec_choices WHERE RegNo = RegNo_param;
            WHEN 8 THEN SELECT `8` INTO table_name FROM oec_choices WHERE RegNo = RegNo_param;
           
        END CASE;

        IF table_name IS NOT NULL AND NOT SELECTED THEN
            -- Get the limit_oec for the subject
            SELECT limit_oec INTO limit_oec FROM oec WHERE Subject_code = table_name;

            -- Get the number of rows in the table
            SET @count_query = CONCAT('SELECT COUNT(*) INTO @row_count FROM ', table_name);
            PREPARE stmt FROM @count_query;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;

            -- Fetch the row count into a local variable
            SELECT @row_count INTO row_count;

            IF row_count < limit_oec THEN
                -- Insert Name and RegNo into the table dynamically
                SET @insert_stmt = CONCAT('INSERT INTO ', table_name, ' (Name, RegNo) VALUES (?, ?)');
                SET @Name_param = Name_param;
                SET @RegNo_param = RegNo_param;
                PREPARE stmt FROM @insert_stmt;
                EXECUTE stmt USING @Name_param, @RegNo_param;
                DEALLOCATE PREPARE stmt;

                -- Update the status and alloted columns in oec_choices
                UPDATE oec_choices
                SET status = 'selected', alloted = table_name
                WHERE RegNo = RegNo_param;

                SET selected = TRUE;
            END IF;
        END IF;

        -- Exit the loop if selected is true

        SET i = i + 1;
    END WHILE;

    -- If no suitable table was found, set status to 'waiting'
    IF NOT selected THEN
        UPDATE oec_choices
        SET status = 'waiting'
        WHERE RegNo = RegNo_param;
    END IF;
END



