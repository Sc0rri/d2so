SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for armors
-- ----------------------------
DROP TABLE IF EXISTS `armors`;
CREATE TABLE `armors`  (
  `Id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Hash` int(11) UNSIGNED NOT NULL,
  `Type` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Equippable` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Masterwork_Type` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Mobility` int(11) NOT NULL,
  `Recovery` int(11) NOT NULL,
  `Resilience` int(11) NOT NULL,
  `Intellect` int(11) NOT NULL,
  `Discipline` int(11) NOT NULL,
  `Strength` int(11) NOT NULL,
  `Total` int(11) NOT NULL,
  `Power_Limit` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `Mobility_Recovery` int(11) NOT NULL,
  `Mobility_Resilience` int(11) NOT NULL,
  `Mobility_Intellect` int(11) NOT NULL,
  `Mobility_Discipline` int(11) NOT NULL,
  `Mobility_Strength` int(11) NOT NULL,
  `Recovery_Resilience` int(11) NOT NULL,
  `Recovery_Intellect` int(11) NOT NULL,
  `Recovery_Discipline` int(11) NOT NULL,
  `Recovery_Strength` int(11) NOT NULL,
  `Resilience_Intellect` int(11) NOT NULL,
  `Resilience_Discipline` int(11) NOT NULL,
  `Resilience_Strength` int(11) NOT NULL,
  `Intellect_Discipline` int(11) NOT NULL,
  `Intellect_Strength` int(11) NOT NULL,
  `Discipline_Strength` int(11) NOT NULL,
  `Season_mod` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`Id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for auth
-- ----------------------------
DROP TABLE IF EXISTS `auth`;
CREATE TABLE `auth`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `source` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `source_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `auth_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for godroll
-- ----------------------------
DROP TABLE IF EXISTS `godroll`;
CREATE TABLE `godroll`  (
  `Name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Sight_Barrel` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Mag_Perk` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Perk_1` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Perk_2` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Masterwork` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Type` varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `wtype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `rpm` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`Name`, `Type`) USING BTREE,
  UNIQUE INDEX `Name`(`Name`, `Type`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for migration
-- ----------------------------
DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration`  (
  `version` varchar(180) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `apply_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`version`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for usage
-- ----------------------------
DROP TABLE IF EXISTS `usage`;
CREATE TABLE `usage`  (
  `Hash` int(11) UNSIGNED NOT NULL,
  `Name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `pve_usage` double NULL DEFAULT NULL,
  `pvp_usage` double NULL DEFAULT NULL,
  PRIMARY KEY (`Hash`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password_reset_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT 10,
  `last_sync_weapon` datetime(0) NOT NULL,
  `last_sync_armor` datetime(0) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for weapon_perks
-- ----------------------------
DROP TABLE IF EXISTS `weapon_perks`;
CREATE TABLE `weapon_perks`  (
  `weapon_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `godroll` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`weapon_id`, `name`) USING BTREE,
  CONSTRAINT `weapon_perks_ibfk_1` FOREIGN KEY (`weapon_id`) REFERENCES `weapons` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for weapons
-- ----------------------------
DROP TABLE IF EXISTS `weapons`;
CREATE TABLE `weapons`  (
  `Id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Hash` int(11) UNSIGNED NOT NULL,
  `Type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Rpm` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Dmg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Masterwork_Type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Masterwork_Type_godroll` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Masterwork_Tier` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `Power_Limit` int(11) NOT NULL,
  `pve_godrolls` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `pvp_godrolls` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`Id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `weapons_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
