-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 31, 2026 at 10:54 PM
-- Server version: 10.11.18-MariaDB-cll-lve
-- PHP Version: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `i10977846_psb11`
--

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_options`
--

CREATE TABLE `hfpr_options` (
  `option_id` bigint(20) UNSIGNED NOT NULL,
  `option_name` varchar(191) NOT NULL DEFAULT '',
  `option_value` longtext NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `hfpr_options`
--

INSERT INTO `hfpr_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(957, 'rts_survey_settings', 'a:3:{i:3;a:5:{s:6:\"active\";i:1;s:8:\"excluded\";i:0;s:10:\"start_date\";s:19:\"2026-07-09 12:34:00\";s:8:\"end_date\";s:0:\"\";s:8:\"timezone\";s:6:\"+00:00\";}i:2;a:5:{s:6:\"active\";i:0;s:8:\"excluded\";i:1;s:10:\"start_date\";s:0:\"\";s:8:\"end_date\";s:0:\"\";s:8:\"timezone\";s:3:\"UTC\";}i:1;a:5:{s:6:\"active\";i:0;s:8:\"excluded\";i:1;s:10:\"start_date\";s:0:\"\";s:8:\"end_date\";s:0:\"\";s:8:\"timezone\";s:3:\"UTC\";}}', 'auto'),
(5449, 'rts_registration_processed_10', '2026-07-17 07:05:31', 'auto'),
(5917, 'rts_registration_processed_11', '2026-07-17 10:07:46|rts_process_6a59fef27268a0.58220458', 'auto'),
(6193, 'rts_registration_processed_12', '2026-07-17 11:20:07|rts_process_6a5a0fe7684392.04812559', 'auto'),
(8365, 'rts_registration_processed_13', '2026-07-20 06:52:15|rts_process_6a5dc59ff40987.48892738', 'auto'),
(8572, 'rts_registration_processed_14', '2026-07-20 07:32:43|rts_process_6a5dcf1b8e60e3.08414762', 'auto'),
(11479, 'rts_registration_processed_15', '2026-07-22 12:47:19|rts_process_6a60bbd7cbe8a7.54354044', 'auto'),
(11500, 'rts_registration_processed_16', '2026-07-22 12:54:45|rts_process_6a60bd958dbc53.70281170', 'auto'),
(11510, 'rts_registration_processed_17', '2026-07-22 12:58:53|rts_process_6a60be8d8473c6.15332672', 'auto'),
(13371, 'rts_qr_terms_version', '1.0', 'auto'),
(15261, 'rts_registration_processed_18', '2026-07-27 13:08:23|rts_process_6a6758472422e8.22765510', 'auto'),
(15630, 'rts_registration_processed_19', '2026-07-28 05:00:03|rts_process_6a683753c08171.09311782', 'auto'),
(15813, 'rts_registration_processed_20', '2026-07-28 06:54:10|rts_process_6a6852128ff976.62332178', 'auto'),
(15877, 'rts_registration_processed_21', '2026-07-28 07:13:40|rts_process_6a6856a47d0de0.07953947', 'auto'),
(15907, 'rts_registration_processed_22', '2026-07-28 07:21:20|rts_process_6a685870b4c040.65710566', 'auto'),
(15948, 'rts_registration_processed_23', '2026-07-28 07:33:11|rts_process_6a685b37d20d58.45935533', 'auto'),
(15970, 'rts_registration_processed_24', '2026-07-28 07:38:55|rts_process_6a685c8fda4ad0.58895923', 'auto'),
(16003, 'rts_registration_processed_25', '2026-07-28 07:54:08|rts_process_6a686020ea53c2.82833115', 'auto'),
(16078, 'rts_registration_processed_26', '2026-07-28 09:01:56|rts_process_6a68700465e560.82188178', 'auto'),
(16166, 'rts_registration_processed_27', '2026-07-28 09:47:49|rts_process_6a687ac5f003d8.82925112', 'auto'),
(16194, 'rts_registration_processed_28', '2026-07-28 09:55:50|rts_process_6a687ca6e97e67.89954574', 'auto'),
(16512, 'rts_registration_processed_29', '2026-07-28 13:08:59|rts_process_6a68a9eb0827e3.08253529', 'auto'),
(18426, 'rts_member_profile_page_id', '1213', 'auto'),
(18427, 'rts_member_qr_page_id', '1214', 'auto'),
(18605, 'rts_registration_processed_30', '2026-07-30 12:30:19|rts_process_6a6b43db3f4044.48669226', 'auto'),
(18661, 'rts_registration_processed_31', '2026-07-30 12:48:58|rts_process_6a6b483a82f043.47554607', 'auto'),
(18688, 'rts_registration_processed_32', '2026-07-30 12:58:24|rts_process_6a6b4a70414324.32230884', 'auto'),
(18718, 'rts_registration_processed_33', '2026-07-30 13:08:13|rts_process_6a6b4cbd868516.44841675', 'auto'),
(23007, 'rts_registration_processed_34', '2026-08-03 15:37:02|rts_process_6a70b59e8571d4.11670404', 'auto'),
(31640, 'rts_admin_dashboard_page_id', '3470', 'auto'),
(32412, 'rts_captains_update_page_id', '3628', 'auto'),
(33147, 'rts_registration_processed_35', '2026-08-07 04:40:31|rts_process_6a7561bf01c444.45543869', 'auto'),
(34088, 'rts_registration_processed_36', '2026-08-07 09:35:22|rts_process_6a75a6dac336f8.60162351', 'auto'),
(37300, 'rts_registration_processed_37', '2026-08-10 06:58:32|rts_process_6a797698a56e37.15617878', 'auto'),
(38871, 'rts_captains_suite_auth_assets', 'a:6:{s:10:\"login_logo\";s:72:\"https://runtheseas.com/wp-content/uploads/2026/08/new-logo-lifestyle.png\";s:11:\"frame_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/Frame-8717.svg\";s:13:\"divider_image\";s:63:\"https://runtheseas.com/wp-content/uploads/2026/08/Frame-192.png\";s:20:\"footer_divider_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/Frame-8716.svg\";s:12:\"button_image\";s:96:\"https://runtheseas.com/wp-content/uploads/2026/08/3-Captains-Suite-Jul-28-2026-01_39_01-AM-1.svg\";s:18:\"reset_button_image\";s:0:\"\";}', 'off'),
(38891, 'rts_survey_design_assets', 'a:6:{s:10:\"hero_image\";s:80:\"https://runtheseas.com/wp-content/uploads/2026/08/run-the-seas-survey-banner.png\";s:15:\"question_images\";s:675:\"https://runtheseas.com/wp-content/uploads/2026/08/banner.svg|https://runtheseas.com/wp-content/uploads/2026/08/2cc25dd3-60c0-49d5-8c75-8c6de2c9cdfe.png|https://runtheseas.com/wp-content/uploads/2026/08/5-Its-A-Lifestyle.svg|https://runtheseas.com/wp-content/uploads/2026/08/2Its-A-Lifestyle.svg|https://runtheseas.com/wp-content/uploads/2026/08/5-Its-A-Lifestyle-1.svg|https://runtheseas.com/wp-content/uploads/2026/08/4-Its-A-Lifestyle.svg|https://runtheseas.com/wp-content/uploads/2026/08/Full-rectangular-frame-fix-1.png|https://runtheseas.com/wp-content/uploads/2026/08/Background-Image.svg|https://runtheseas.com/wp-content/uploads/2026/08/run-the-seas-survey-banner.png\";s:17:\"header_left_image\";s:65:\"https://runtheseas.com/wp-content/uploads/2026/08/survey-left.png\";s:18:\"header_right_image\";s:66:\"https://runtheseas.com/wp-content/uploads/2026/08/survey-right.png\";s:22:\"progress_divider_image\";s:73:\"https://runtheseas.com/wp-content/uploads/2026/08/survey_page_divider.png\";s:16:\"completion_video\";s:75:\"https://runtheseas.com/wp-content/uploads/2026/08/captains-suite-update.mp4\";}', 'off'),
(42641, 'rts_verification_email_design_assets', 'a:9:{s:21:\"complete_header_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/ev-email-header.png\";s:12:\"header_image\";s:0:\"\";s:10:\"hero_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/ev-main-banner.png\";s:22:\"headline_divider_image\";s:66:\"https://runtheseas.com/wp-content/uploads/2026/08/ev-divider-2.png\";s:18:\"name_divider_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/ev-name-divider.png\";s:16:\"email_icon_image\";s:67:\"https://runtheseas.com/wp-content/uploads/2026/08/ev-email-icon.svg\";s:15:\"lock_icon_image\";s:85:\"https://runtheseas.com/wp-content/uploads/2026/08/ev-bitcoin-icons_shield-outline.png\";s:21:\"complete_button_image\";s:66:\"https://runtheseas.com/wp-content/uploads/2026/08/ev-email-btn.svg\";s:25:\"certificate_preview_image\";s:74:\"https://runtheseas.com/wp-content/uploads/2026/08/certificate-template.png\";}', 'off'),
(42654, 'rts_verification_email_issued_38', '2026-08-11 13:14:44', 'off'),
(42659, 'rts_registration_processed_38', '2026-08-11 13:14:55|rts_process_6a7b204f4c8b96.94358687', 'auto'),
(43267, 'rts_verification_email_issued_39', '2026-08-12 04:19:15', 'off'),
(43277, 'rts_registration_processed_39', '2026-08-12 04:19:18|rts_process_6a7bf446d6d748.59190474', 'auto'),
(43413, 'rts_verification_email_issued_40', '2026-08-12 05:03:43', 'off'),
(43419, 'rts_registration_processed_40', '2026-08-12 05:04:02|rts_process_6a7bfec2c65fa7.22298704', 'auto'),
(43569, 'rts_certificate_email_design_assets', 'a:17:{s:21:\"complete_header_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/cs_main_header.png\";s:17:\"header_logo_image\";s:0:\"\";s:10:\"hero_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/cs_banner_image.png\";s:18:\"hero_divider_image\";s:66:\"https://runtheseas.com/wp-content/uploads/2026/08/ev-divider-2.png\";s:25:\"certificate_preview_image\";s:74:\"https://runtheseas.com/wp-content/uploads/2026/08/certificate-template.png\";s:22:\"suite_background_image\";s:0:\"\";s:23:\"suite_top_divider_image\";s:62:\"https://runtheseas.com/wp-content/uploads/2026/08/cs_crown.png\";s:26:\"suite_bottom_divider_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/cs_divider.png\";s:17:\"suite_icon_voyage\";s:63:\"https://runtheseas.com/wp-content/uploads/2026/08/cs_voyage.png\";s:19:\"suite_icon_priority\";s:72:\"https://runtheseas.com/wp-content/uploads/2026/08/cs_priority_access.png\";s:19:\"suite_icon_marathon\";s:74:\"https://runtheseas.com/wp-content/uploads/2026/08/cs_referral_marathon.png\";s:17:\"suite_icon_avatar\";s:67:\"https://runtheseas.com/wp-content/uploads/2026/08/cs_own_avatar.png\";s:18:\"suite_icon_profile\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/cs_own_profile.png\";s:21:\"download_button_image\";s:0:\"\";s:18:\"suite_button_image\";s:63:\"https://runtheseas.com/wp-content/uploads/2026/08/cs_button.png\";s:17:\"footer_icon_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/cs_footer_icon.png\";s:20:\"footer_foliage_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/cs_footer_image.png\";}', 'off'),
(43762, 'rts_journey_page_id', '5952', 'auto'),
(45219, 'rts_journey_design_assets', 'a:10:{s:10:\"logo_image\";s:61:\"https://runtheseas.com/wp-content/uploads/2026/08/vj-logo.png\";s:16:\"print_icon_image\";s:67:\"https://runtheseas.com/wp-content/uploads/2026/08/vj-print-icon.png\";s:16:\"email_icon_image\";s:67:\"https://runtheseas.com/wp-content/uploads/2026/08/vj-email-icon.png\";s:10:\"hero_image\";s:63:\"https://runtheseas.com/wp-content/uploads/2026/08/Journey-2.svg\";s:20:\"founding_runner_icon\";s:72:\"https://runtheseas.com/wp-content/uploads/2026/08/vj-founding-runner.png\";s:11:\"frame_image\";s:65:\"https://runtheseas.com/wp-content/uploads/2026/08/Group-257-2.svg\";s:25:\"progress_start_icon_image\";s:71:\"https://runtheseas.com/wp-content/uploads/2026/08/vj-progress-start.png\";s:17:\"footer_icon_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/vj-footer-icon.png\";s:10:\"gold_color\";s:7:\"#d99214\";s:10:\"page_width\";i:1470;}', 'off'),
(46961, 'rts_dashboard_design_assets', 'a:17:{s:18:\"status_frame_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/Subtract-1.png\";s:20:\"referrals_icon_image\";s:63:\"https://runtheseas.com/wp-content/uploads/2026/08/mc-people.png\";s:19:\"progress_icon_image\";s:65:\"https://runtheseas.com/wp-content/uploads/2026/08/mc-runner-2.png\";s:19:\"trophies_icon_image\";s:65:\"https://runtheseas.com/wp-content/uploads/2026/08/mc-trophy-2.png\";s:22:\"next_trophy_icon_image\";s:62:\"https://runtheseas.com/wp-content/uploads/2026/08/mc-frame.png\";s:23:\"leaderboard_frame_image\";s:66:\"https://runtheseas.com/wp-content/uploads/2026/08/Subtract-1-1.png\";s:31:\"leaderboard_left_ornament_image\";s:66:\"https://runtheseas.com/wp-content/uploads/2026/08/mc-left-leaf.png\";s:32:\"leaderboard_right_ornament_image\";s:67:\"https://runtheseas.com/wp-content/uploads/2026/08/mc-right-leaf.png\";s:29:\"leaderboard_trophy_icon_image\";s:76:\"https://runtheseas.com/wp-content/uploads/2026/08/icon-park-solid_trophy.svg\";s:30:\"leaderboard_trophy_green_image\";s:78:\"https://runtheseas.com/wp-content/uploads/2026/08/icon-park-solid_trophy-3.svg\";s:29:\"leaderboard_trophy_blue_image\";s:78:\"https://runtheseas.com/wp-content/uploads/2026/08/icon-park-solid_trophy-1.svg\";s:31:\"leaderboard_trophy_purple_image\";s:78:\"https://runtheseas.com/wp-content/uploads/2026/08/icon-park-solid_trophy-2.svg\";s:29:\"leaderboard_trophy_gold_image\";s:67:\"https://runtheseas.com/wp-content/uploads/2026/08/mc-trophy-svg.svg\";s:31:\"leaderboard_trophy_orange_image\";s:67:\"https://runtheseas.com/wp-content/uploads/2026/08/mc-trophy-svg.svg\";s:28:\"leaderboard_trophy_red_image\";s:67:\"https://runtheseas.com/wp-content/uploads/2026/08/mc-trophy-svg.svg\";s:31:\"leaderboard_trophy_silver_image\";s:67:\"https://runtheseas.com/wp-content/uploads/2026/08/mc-trophy-svg.svg\";s:29:\"leaderboard_invite_icon_image\";s:75:\"https://runtheseas.com/wp-content/uploads/2026/08/mc-leaderboard-footer.png\";}', 'off'),
(48355, 'rts_trophy_case_design_assets', 'a:31:{s:16:\"background_image\";s:67:\"https://runtheseas.com/wp-content/uploads/2026/08/mc-background.png\";s:27:\"responsive_background_image\";s:80:\"https://runtheseas.com/wp-content/uploads/2026/08/mc2-mobile-trophy-case2-bg.png\";s:11:\"title_image\";s:62:\"https://runtheseas.com/wp-content/uploads/2026/08/tc-title.png\";s:16:\"title_icon_image\";s:65:\"https://runtheseas.com/wp-content/uploads/2026/08/tc-top-icon.png\";s:18:\"footer_frame_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/tc-bottom-badge.png\";s:22:\"founding_caption_image\";s:0:\"\";s:18:\"half_caption_image\";s:0:\"\";s:22:\"marathon_caption_image\";s:0:\"\";s:29:\"milestone_left_ornament_image\";s:67:\"https://runtheseas.com/wp-content/uploads/2026/08/tc-left-arrow.png\";s:30:\"milestone_right_ornament_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/tc-right-arrow.png\";s:30:\"founding_runner_unlocked_image\";s:63:\"https://runtheseas.com/wp-content/uploads/2026/08/fr-unlock.png\";s:28:\"founding_runner_locked_image\";s:63:\"https://runtheseas.com/wp-content/uploads/2026/08/fr-locked.png\";s:17:\"5k_unlocked_image\";s:63:\"https://runtheseas.com/wp-content/uploads/2026/08/5k-unlock.png\";s:15:\"5k_locked_image\";s:63:\"https://runtheseas.com/wp-content/uploads/2026/08/5k-locked.png\";s:18:\"10k_unlocked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/10k-unlock.png\";s:16:\"10k_locked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/10k-locked.png\";s:18:\"15k_unlocked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/15k-unlock.png\";s:16:\"15k_locked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/15k-locked.png\";s:18:\"20k_unlocked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/20k-unlock.png\";s:16:\"20k_locked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/20k-locked.png\";s:18:\"21k_unlocked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/21k-unlock.png\";s:16:\"21k_locked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/21k-locked.png\";s:18:\"25k_unlocked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/25k-unlock.png\";s:16:\"25k_locked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/25k-locked.png\";s:18:\"30k_unlocked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/30k-unlock.png\";s:16:\"30k_locked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/30k-locked.png\";s:18:\"35k_unlocked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/35k-unlock.png\";s:16:\"35k_locked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/35k-locked.png\";s:18:\"42k_unlocked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/42k-unlock.png\";s:16:\"42k_locked_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/42k-locked.png\";s:15:\"lock_icon_image\";s:0:\"\";}', 'off'),
(48841, 'rts_verification_email_issued_41', '2026-08-14 12:42:50', 'off'),
(48846, 'rts_registration_processed_41', '2026-08-14 12:42:58|rts_process_6a7f0d5203eec8.93098036', 'auto'),
(48935, 'rts_verification_email_issued_42', '2026-08-14 12:55:52', 'off'),
(48940, 'rts_registration_processed_42', '2026-08-14 12:55:54|rts_process_6a7f105a942427.84020130', 'auto'),
(50776, 'rts_verification_email_issued_43', '2026-08-17 06:40:31', 'off'),
(50782, 'rts_registration_processed_43', '2026-08-17 06:40:35|rts_process_6a82ace3c1b4d1.44248021', 'auto'),
(51609, 'rts_verification_email_issued_44', '2026-08-17 10:54:36', 'off'),
(51615, 'rts_registration_processed_44', '2026-08-17 10:54:48|rts_process_6a82e878978e02.38005777', 'auto'),
(52614, 'rts_marathon_one_trophy_case_design_assets', 'a:48:{s:16:\"background_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-background.png\";s:27:\"responsive_background_image\";s:79:\"https://runtheseas.com/wp-content/uploads/2026/08/mc1-mobile-trophy-case-bg.png\";s:11:\"title_image\";s:0:\"\";s:16:\"title_icon_image\";s:75:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-header-text-icon.png\";s:18:\"footer_frame_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-Frame-8789.png\";s:22:\"founding_caption_image\";s:0:\"\";s:18:\"half_caption_image\";s:0:\"\";s:22:\"marathon_caption_image\";s:0:\"\";s:29:\"milestone_left_ornament_image\";s:0:\"\";s:30:\"milestone_right_ornament_image\";s:0:\"\";s:30:\"founding_runner_unlocked_image\";s:70:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-fr-unlocked.png\";s:28:\"founding_runner_locked_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-fr-locked.png\";s:17:\"5k_unlocked_image\";s:70:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-5k-unlocked.png\";s:15:\"5k_locked_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-5k-locked.png\";s:18:\"10k_unlocked_image\";s:71:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-10k-unlocked.png\";s:16:\"10k_locked_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-10k-locked.png\";s:18:\"15k_unlocked_image\";s:71:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-15k-unlocked.png\";s:16:\"15k_locked_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-15k-locked.png\";s:18:\"20k_unlocked_image\";s:71:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-20k-unlocked.png\";s:16:\"20k_locked_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-20k-locked.png\";s:18:\"21k_unlocked_image\";s:73:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-21.1k-unlocked.png\";s:16:\"21k_locked_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-21k-locked.png\";s:18:\"25k_unlocked_image\";s:71:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-25k-unlocked.png\";s:16:\"25k_locked_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-25k-locked.png\";s:18:\"30k_unlocked_image\";s:71:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-30k-unlocked.png\";s:16:\"30k_locked_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-30k-locked.png\";s:18:\"35k_unlocked_image\";s:71:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-35k-unlocked.png\";s:16:\"35k_locked_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-35k-locked.png\";s:18:\"42k_unlocked_image\";s:73:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-42.2k-unlocked.png\";s:16:\"42k_locked_image\";s:71:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-42.2k-locked.png\";s:15:\"lock_icon_image\";s:0:\"\";s:25:\"title_left_flourish_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-left-title.png\";s:26:\"title_right_flourish_image\";s:70:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-title-right.png\";s:24:\"title_left_compass_image\";s:75:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-header-left-icon.png\";s:25:\"title_right_compass_image\";s:76:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-header-right-icon.png\";s:25:\"title_heading_frame_image\";s:0:\"\";s:27:\"title_nameplate_frame_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-Frame-278.png\";s:22:\"how_to_earn_icon_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-frame-icon.png\";s:23:\"how_to_earn_frame_image\";s:66:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-frame11.png\";s:26:\"learn_more_link_icon_image\";s:72:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-external-link.png\";s:24:\"race_progress_icon_image\";s:71:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-ri_team-fill.png\";s:25:\"race_progress_frame_image\";s:66:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-frame22.png\";s:25:\"view_race_link_icon_image\";s:72:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-external-link.png\";s:28:\"marathon_two_lock_icon_image\";s:63:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-lock.png\";s:31:\"marathon_two_compass_icon_image\";s:70:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-footer-icon.png\";s:24:\"marathon_two_frame_image\";s:66:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-frame33.png\";s:26:\"footer_calendar_icon_image\";s:70:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-fe_calendar.png\";s:25:\"footer_compass_icon_image\";s:70:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-footer-icon.png\";}', 'off'),
(54314, 'rts_marathon_challenge_page_id', '6729', 'auto'),
(54331, 'rts_marathon_challenge_design_assets', 'a:42:{s:9:\"map_image\";s:90:\"https://runtheseas.com/wp-content/uploads/2026/08/89b54703-4e14-4ccc-8bf7-78cea453e917.png\";s:18:\"header_frame_image\";s:0:\"\";s:24:\"guest_avatar_frame_image\";s:74:\"https://runtheseas.com/wp-content/uploads/2026/08/mcp-logged-out-avtar.png\";s:31:\"current_user_avatar_frame_image\";s:74:\"https://runtheseas.com/wp-content/uploads/2026/08/mcp-logged-out-avtar.png\";s:26:\"user_position_marker_image\";s:71:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-user-position.png\";s:35:\"user_position_marker_selected_image\";s:80:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-user-position-selected.png\";s:20:\"top_four_frame_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-frame1.png\";s:22:\"around_you_frame_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-frame3.png\";s:21:\"finishers_frame_image\";s:75:\"https://runtheseas.com/wp-content/uploads/2026/08/mcp-marathon-finisher.png\";s:22:\"milestones_frame_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-frame5.png\";s:23:\"over_target_frame_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-frame6.png\";s:19:\"top_four_icon_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/mcp-title-icon.png\";s:21:\"around_you_icon_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/mcp-title-icon.png\";s:21:\"milestones_icon_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/mcp-title-icon.png\";s:22:\"over_target_icon_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/mcp-title-icon.png\";s:17:\"footer_icon_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-footer-logo.png\";s:27:\"panel_heading_divider_image\";s:0:\"\";s:20:\"list_open_icon_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-right-arrow.png\";s:21:\"list_close_icon_image\";s:68:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-down-arrow.png\";s:33:\"user_list_header_right_icon_image\";s:69:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-right-arrow.png\";s:39:\"milestone_group_header_right_icon_image\";s:75:\"https://runtheseas.com/wp-content/uploads/2026/08/tcm1-header-text-icon.png\";s:25:\"around_you_up_arrow_image\";s:0:\"\";s:27:\"around_you_down_arrow_image\";s:0:\"\";s:28:\"around_you_right_arrow_image\";s:0:\"\";s:31:\"marathon2_position_marker_image\";s:65:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-m2-icon.png\";s:40:\"marathon2_position_marker_selected_image\";s:66:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-m3-badge.png\";s:21:\"marathon2_badge_image\";s:70:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-avatar-frame.png\";s:30:\"around_you_current_frame_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-frame2.png\";s:28:\"milestone_active_frame_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-frame6.png\";s:27:\"user_list_popup_frame_image\";s:64:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-frame4.png\";s:27:\"finisher_avatar_frame_image\";s:0:\"\";s:24:\"finisher_rank_icon_image\";s:0:\"\";s:15:\"trophy_5k_image\";s:60:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-5k.png\";s:16:\"trophy_10k_image\";s:61:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-10k.png\";s:16:\"trophy_15k_image\";s:61:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-15k.png\";s:16:\"trophy_20k_image\";s:61:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-20k.png\";s:16:\"trophy_21k_image\";s:0:\"\";s:16:\"trophy_25k_image\";s:61:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-25k.png\";s:16:\"trophy_30k_image\";s:61:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-30k.png\";s:16:\"trophy_35k_image\";s:61:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-35k.png\";s:16:\"trophy_42k_image\";s:63:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-42.2k.png\";s:17:\"trophy_over_image\";s:63:\"https://runtheseas.com/wp-content/uploads/2026/08/mcn-42.2k.png\";}', 'off'),
(58665, 'rts_admin_platform_db_version', '1.15.0', 'auto'),
(58684, 'rts_cron_last_campaign_triggers', '2026-09-01 05:06:57', 'auto'),
(58685, 'rts_cron_last_scheduled_reports', '2026-08-31 12:06:25', 'auto'),
(58686, 'rts_cron_last_action_items', '2026-08-31 12:06:25', 'auto'),
(58687, 'rts_cron_last_fr_sync', '2026-09-01 05:51:00', 'auto'),
(61513, 'rts_verification_email_issued_45', '2026-08-24 11:16:10', 'off'),
(61532, 'rts_registration_processed_45', '2026-08-24 11:16:27|rts_process_6a8c280bd1c577.07256219', 'auto'),
(61680, 'rts_verification_email_issued_46', '2026-08-24 11:40:10', 'off'),
(61685, 'rts_registration_processed_46', '2026-08-24 11:40:19|rts_process_6a8c2da3995ee2.93538763', 'auto'),
(68693, 'rts_default_email_template_content_version', '1.0', 'off'),
(68703, 'rts_verification_schema_version', '1.0', 'off'),
(68704, 'rts_production_email_template_content_version', '4.0', 'off'),
(68705, 'rts_trophy_reconciliation_version', '2', 'off'),
(73773, 'rts_verification_email_issued_47', '2026-08-31 11:10:24', 'off'),
(73780, 'rts_registration_processed_47', '2026-08-31 11:11:45|rts_process_6a956171806f52.44242032', 'auto'),
(74445, 'rts_verification_email_issued_48', '2026-08-31 13:22:19', 'off'),
(74450, 'rts_registration_processed_48', '2026-08-31 13:22:29|rts_process_6a958015eb0852.10873979', 'auto');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hfpr_options`
--
ALTER TABLE `hfpr_options`
  ADD PRIMARY KEY (`option_id`),
  ADD UNIQUE KEY `option_name` (`option_name`),
  ADD KEY `autoload` (`autoload`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hfpr_options`
--
ALTER TABLE `hfpr_options`
  MODIFY `option_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75652;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
