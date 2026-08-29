-- ====================================================================
-- FASAL Automated Database Disaster Recovery SQL Dump
-- App: FASAL - Kopargaon Smart Agriculture Platform
-- Generated At: 2026-08-29 18:57:28
-- ====================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
START TRANSACTION;
SET time_zone = '+05:30';

-- Table `users`
DROP TABLE IF EXISTS `users`;
-- Table `iot_sensor_logs`
DROP TABLE IF EXISTS `iot_sensor_logs`;
-- Table `mandi_prices`
DROP TABLE IF EXISTS `mandi_prices`;
-- Table `crop_advisories`
DROP TABLE IF EXISTS `crop_advisories`;
-- Table `machinery_listings`
DROP TABLE IF EXISTS `machinery_listings`;
INSERT INTO `machinery_listings` (`equipment_name`, `owner_name`, `contact_phone`, `price_rate`, `location_area`, `status`, `created_at`, `id`) VALUES ('M1otseLJb/xz029Usu1VDTo6OoRm33+MXjtzP0QFl3Xva53bnW/uvRYuvbUzAL+B1OWTOjo6L7G/AlWSSHomL/5O732L277Bpy1J2XGnAVpaMFjWI6TeL+XTEhaO52WpmATohzkt', 'O5VTxT/B5ElN2THWfeKYXDo6OmGKc/L5+0wNjrgaSJjjSvRT+j3LCA8ZxGlYW6wCzKFQOjo6iHdGC67a4C9dFBVqbMGBqyvj8mTF1Yyh4TgZWkf4bpbI3a/sNIRPiHEOApnmpXCnpwvM9t+oLhw1k2ZPSg89ZA==', '5Ysx+3BaATmlWZ5uuvqaljo6Oox9BJftQ0zkFnR63QCoBfLHCjbfjZ2dSE6GlX+9Cl/3Ojo6MBjhw2dhMUfMTi4amGwslA==', 'fVds/BEXpVayvpjA0gI/hjo6On+Oe2zuNSWd1EMDdVdB/iHJdZ0Osv533/ZSZwXCX4RDOjo6E06GYKyP9eJxs8spoEuJPOwE/BTVTY9oOSvIuqQZsMQ=', 'cseWDoyh5uE5BZxw46j1qTo6OraAqnurBU2yH1Rw1bDJ4LL4NBylFUVAXODXmLekbEXyOjo6TGDuoo76DtBKV32zQVTGZ8KYAKQiEBbs/+Uv2gVPrDqHheY+s0LsG0oGJgqeym0M', 'AVAILABLE', '2026-08-29 18:43:40', '1');

-- Table `labour_listings`
DROP TABLE IF EXISTS `labour_listings`;
-- Table `otp_codes`
DROP TABLE IF EXISTS `otp_codes`;
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
-- Dump completed on 2026-08-29 18:57:28
