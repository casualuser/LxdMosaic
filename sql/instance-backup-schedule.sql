CREATE TABLE `Backup_Strategies` (
    `BS_ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `BS_Name` VARCHAR(255) NOT NULL
);

CREATE TABLE `Instance_Backup_Schedule` (
    `IBS_ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `IBS_User_ID` INT NOT NULL,
    `IBS_Date_Created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `IBS_Host_ID` INT NOT NULL,
    `IBS_Instance` VARCHAR(255) NOT NULL,
    `IBS_Project` VARCHAR(255) NOT NULL,
    `IBS_Schedule_String` VARCHAR(255) NOT NULL,
    `IBS_BS_ID` INT NOT NULL,
    `IBS_Retention` INT NOT NULL,
    `IBS_Disabled` TINYINT NOT NULL DEFAULT 0,
    `IBS_Disabled_Date` DATETIME,
    `IBS_Disabled_By` INT
);

INSERT INTO `Backup_Strategies` (`BS_ID`, `BS_Name`) VALUES
(1, "Backup & Import");

ALTER TABLE `Instance_Backup_Schedule`
    ADD FOREIGN KEY (`IBS_User_ID`)
    REFERENCES `Users`(`User_ID`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;

ALTER TABLE `Instance_Backup_Schedule`
    ADD FOREIGN KEY (`IBS_Host_ID`)
    REFERENCES `Hosts`(`Host_ID`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;

ALTER TABLE `Instance_Backup_Schedule`
    ADD FOREIGN KEY (`IBS_BS_ID`)
    REFERENCES `Backup_Strategies`(`BS_ID`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;

ALTER TABLE `Instance_Backup_Schedule`
    ADD FOREIGN KEY (`IBS_Disabled_By`)
    REFERENCES `Users`(`User_ID`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT; 
