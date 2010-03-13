CREATE TABLE w3_user
(
    `id` INTEGER(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(128) NOT NULL UNIQUE KEY,
    `password` VARCHAR(128) NOT NULL,
    `salt` VARCHAR(128) NOT NULL DEFAULT '',
    `email` VARCHAR(255) NOT NULL UNIQUE KEY,
    `screenName` VARCHAR(128) NOT NULL,
    `language` VARCHAR(24) NOT NULL DEFAULT 'en',
    `interface` VARCHAR(64) NULL,
    `accessType` VARCHAR(32) NOT NULL DEFAULT 'member' COMMENT 'member, client, consultant, manager, administrator',
    `accessLevel` INTEGER(1) NOT NULL DEFAULT '1' COMMENT '1, 2, 3, 4, 5',
    `isActive` ENUM('0','1') NULL,
    `createTime` INTEGER(10) NOT NULL DEFAULT '1234567890'
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE w3_user_details
(
    `userId` INTEGER(10) UNSIGNED NOT NULL PRIMARY KEY,
    `passwordHint` TEXT NULL,
    `isEmailConfirmed` ENUM('0','1') NOT NULL DEFAULT '0',
    `emailConfirmationKey` VARCHAR(32) NOT NULL DEFAULT '',
    `isEmailVisible` ENUM('0','1') NULL,
    `isScreenNameEditable` ENUM('0','1') NULL,
    `deactivationTime` INTEGER(10) NULL,
    `firstName` VARCHAR(128) NULL,
    `middleName` VARCHAR(128) NULL,
    `lastName` VARCHAR(128) NULL,
    `initials` VARCHAR(16) NULL,
    `occupation` VARCHAR(128) NULL,
    `gender` ENUM('male','female') NULL,
    `birthDate` DATE NULL,
    `textStatus` TEXT NULL,
    `lastLoginTime` INTEGER(10) NULL,
    `lastVisitTime` INTEGER(10) NULL,
    `totalTimeLoggedIn` INTEGER(9) NOT NULL DEFAULT '0',
    `secretQuestion` TEXT NULL,
    `secretAnswer` VARCHAR(255) NULL,
    `administratorNote` TEXT NULL,
    `updateTime` INTEGER(10) NULL,
    CONSTRAINT FK_user FOREIGN KEY (`userId`)
        REFERENCES w3_user (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO w3_user (`id`, `username`, `password`, `email`, `screenName`, `accessType`, `accessLevel`, `isActive`, `createTime`) VALUES ('1','admin','21232f297a57a5a743894a0e4a801fc3','admin@example.com','Administrator','administrator','5','1',UNIX_TIMESTAMP());
INSERT INTO w3_user (`id`, `username`, `password`, `email`, `screenName`, `accessType`, `accessLevel`, `isActive`, `createTime`) VALUES ('2','demo','fe01ce2a7fbac8fafaed7c982a04e229','demo@example.com','Demo Member','member','1','1',UNIX_TIMESTAMP());
INSERT INTO w3_user_details (`userId`, `emailConfirmationKey`) VALUES ('1','fbc07066a1a79166a9098664821869d1');
INSERT INTO w3_user_details (`userId`, `emailConfirmationKey`) VALUES ('2','fbc07066a1a79166a9098664821869d1');