CREATE TABLE User
(
    `id` INTEGER(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(128) NOT NULL UNIQUE KEY,
    `password` VARCHAR(128) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE KEY,
    `displayName` VARCHAR(128) NOT NULL,
    `language` VARCHAR(16) NOT NULL DEFAULT 'en',
    `theme` VARCHAR(32) NOT NULL DEFAULT 'start',
    `accessType` VARCHAR(32) NOT NULL DEFAULT 'user' COMMENT 'user, paid, facilitator, moderator, admin',
    `accessLevel` TINYINT(1) NOT NULL DEFAULT '1',
    `isActive` ENUM('0','1') NULL, 
    `createdOn` DATETIME NOT NULL,
    `createdGmtOn` DATETIME NOT NULL
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE UserDetails
(
    `userId` INTEGER(10) UNSIGNED NOT NULL PRIMARY KEY,
    `passwordHint` TEXT NULL,
    `isEmailConfirmed` ENUM('0','1') NOT NULL DEFAULT '0',
    `emailConfirmationKey` VARCHAR(64) NOT NULL DEFAULT '',
    `isEmailVisible` ENUM('0','1') NOT NULL DEFAULT '0',
    `isDisplayNameEditable` ENUM('0','1') NOT NULL DEFAULT '1',
    `firstName` VARCHAR(128) NULL,
    `middleName` VARCHAR(128) NULL,
    `lastName` VARCHAR(128) NULL,
    `gender` ENUM('male','female') NULL,
    `birthDate` DATE NULL,
    `textStatus` TEXT NULL,
    `lastLoginOn` DATETIME NULL,
    `lastLoginGmtOn` DATETIME NULL,
    `lastSeenOn` DATETIME NULL,
    `lastSeenGmtOn` DATETIME NULL,
    `totalTimeLoggedIn` INTEGER(9) NOT NULL DEFAULT '0',
    `secretQuestion` TEXT NULL,
    `secretAnswer` VARCHAR(255) NULL,
    `adminComment` TEXT NULL,
    `oldPassword` VARCHAR(128) NULL,
    `updatedOn` DATETIME NULL,
    `updatedGmtOn` DATETIME NULL,
    CONSTRAINT FK_user FOREIGN KEY (`userId`)
        REFERENCES User (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO User (`id`, `username`, `password`, `email`, `displayName`, `accessType`, `accessLevel`, `isActive`, `createdOn`, `createdGmtOn`) VALUES ('1','admin','21232f297a57a5a743894a0e4a801fc3','admin@example.com','admin','Admin','4','1',NOW(),NOW());
INSERT INTO User (`id`, `username`, `password`, `email`, `displayName`, `accessType`, `accessLevel`, `isActive`, `createdOn`, `createdGmtOn`) VALUES ('2','demo','fe01ce2a7fbac8fafaed7c982a04e229','demo@example.com','demo','User','1','1',NOW(),NOW());
INSERT INTO UserDetails (`userId`, `emailConfirmationKey`) VALUES ('1','fbc07066a1a79166a9098664821869d1');
INSERT INTO UserDetails (`userId`, `emailConfirmationKey`) VALUES ('2','fbc07066a1a79166a9098664821869d1');