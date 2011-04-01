CREATE TABLE `tbl_user` (
    `id` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(128) NOT NULL,
    `password` VARCHAR(128) NOT NULL,
    `email` VARCHAR(128) NOT NULL,
    `isAdmin` ENUM ('0', '1') NOT NULL DEFAULT '0'
);

INSERT INTO `tbl_user` (`username`, `password`, `email`, `isAdmin`) VALUES ('admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@example.com', '1');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('demo', 'fe01ce2a7fbac8fafaed7c982a04e229', 'demo@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test1', '5a105e8b9d40e1329780d62ea2265d8a', 'test1@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test2', 'ad0234829205b9033196ba818f7a872b', 'test2@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test3', '8ad8757baa8564dc136c1e07507f4a98', 'test3@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test4', '86985e105f79b95d6bc918fb45ec7727', 'test4@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test5', 'e3d704f3542b44a621ebed70dc0efe13', 'test5@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test6', '4cfad7076129962ee70c36839a1e3e15', 'test6@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test7', 'b04083e53e242626595e2b8ea327e525', 'test7@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test8', '5e40d09fa0529781afd1254a42913847', 'test8@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test9', '739969b53246b2c727850dbb3490ede6', 'test9@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test10', 'c1a8e059bfd1e911cf10b626340c9a54', 'test10@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test11', 'f696282aa4cd4f614aa995190cf442fe', 'test11@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test12', '60474c9c10d7142b7508ce7a50acf414', 'test12@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test13', '33fc3dbd51a8b38a38b1b85b6a76b42b', 'test13@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test14', 'b99c94f62fb2a61433c4e44e27406050', 'test14@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test15', '4b377d23309d4ed39c9da5791417aeff', 'test15@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test16', '0c1ccf98666ed505310c0471529429db', 'test16@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test17', 'fcb1a7bbe091b4ee78748946cb762a84', 'test17@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test18', 'df71df92c31111f810a7d89bd2c2e35d', 'test18@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test19', '45357a5c731751a44000d1ba2c0e25fb', 'test19@example.com');
INSERT INTO `tbl_user` (`username`, `password`, `email`) VALUES ('test20', 'b428cbb02358afc32cf32f9bdb725a51', 'test20@example.com');