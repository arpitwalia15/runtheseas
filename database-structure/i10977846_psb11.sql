-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 31, 2026 at 10:20 PM
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
-- Table structure for table `hfpr_actionscheduler_actions`
--

CREATE TABLE `hfpr_actionscheduler_actions` (
  `action_id` bigint(20) UNSIGNED NOT NULL,
  `hook` varchar(191) NOT NULL,
  `status` varchar(20) NOT NULL,
  `scheduled_date_gmt` datetime DEFAULT '0000-00-00 00:00:00',
  `scheduled_date_local` datetime DEFAULT '0000-00-00 00:00:00',
  `priority` tinyint(3) UNSIGNED NOT NULL DEFAULT 10,
  `args` varchar(191) DEFAULT NULL,
  `schedule` longtext DEFAULT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `last_attempt_gmt` datetime DEFAULT '0000-00-00 00:00:00',
  `last_attempt_local` datetime DEFAULT '0000-00-00 00:00:00',
  `claim_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `extended_args` varchar(8000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_actionscheduler_claims`
--

CREATE TABLE `hfpr_actionscheduler_claims` (
  `claim_id` bigint(20) UNSIGNED NOT NULL,
  `date_created_gmt` datetime DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_actionscheduler_groups`
--

CREATE TABLE `hfpr_actionscheduler_groups` (
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_actionscheduler_logs`
--

CREATE TABLE `hfpr_actionscheduler_logs` (
  `log_id` bigint(20) UNSIGNED NOT NULL,
  `action_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `log_date_gmt` datetime DEFAULT '0000-00-00 00:00:00',
  `log_date_local` datetime DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_activity_log`
--

CREATE TABLE `hfpr_bn_activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(64) NOT NULL,
  `object_type` varchar(32) DEFAULT NULL,
  `object_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_appeals`
--

CREATE TABLE `hfpr_bn_appeals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `suspension_id` bigint(20) UNSIGNED NOT NULL,
  `strike_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','approved','denied') NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewer_note` text DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `admin_note` text DEFAULT NULL,
  `resolved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_blocks`
--

CREATE TABLE `hfpr_bn_blocks` (
  `blocker_id` bigint(20) UNSIGNED NOT NULL,
  `blocked_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('block','mute','restrict') NOT NULL DEFAULT 'block',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_bookmarks`
--

CREATE TABLE `hfpr_bn_bookmarks` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_comments`
--

CREATE TABLE `hfpr_bn_comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `object_type` varchar(32) NOT NULL,
  `object_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `content` text NOT NULL,
  `is_edited` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `sync_reply_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_connections`
--

CREATE TABLE `hfpr_bn_connections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `requester_id` bigint(20) UNSIGNED NOT NULL,
  `recipient_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','accepted','declined','withdrawn') NOT NULL DEFAULT 'pending',
  `note` varchar(280) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `declined_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_email_log`
--

CREATE TABLE `hfpr_bn_email_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(64) NOT NULL,
  `digest_date` date DEFAULT NULL,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_email_templates`
--

CREATE TABLE `hfpr_bn_email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(64) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `preview_text` varchar(255) DEFAULT NULL,
  `body_html` longtext NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_follows`
--

CREATE TABLE `hfpr_bn_follows` (
  `follower_id` bigint(20) UNSIGNED NOT NULL,
  `following_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('approved','pending') NOT NULL DEFAULT 'approved',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_hashtags`
--

CREATE TABLE `hfpr_bn_hashtags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `post_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `follower_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_hashtag_follows`
--

CREATE TABLE `hfpr_bn_hashtag_follows` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `hashtag_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_invites`
--

CREATE TABLE `hfpr_bn_invites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(200) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `space_id` bigint(20) UNSIGNED DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `status` enum('pending','registered','bounced') NOT NULL DEFAULT 'pending',
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_member_types`
--

CREATE TABLE `hfpr_bn_member_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#0073aa',
  `text_color` varchar(7) NOT NULL DEFAULT '#ffffff',
  `icon_svg` mediumtext DEFAULT NULL,
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  `show_in_dir` tinyint(1) NOT NULL DEFAULT 1,
  `self_select` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_member_type_assignments`
--

CREATE TABLE `hfpr_bn_member_type_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type_id` int(10) UNSIGNED NOT NULL,
  `assigned_by` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_mod_log`
--

CREATE TABLE `hfpr_bn_mod_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actor_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(64) NOT NULL,
  `object_type` varchar(32) DEFAULT NULL,
  `object_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `note` text DEFAULT NULL,
  `space_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_notifications`
--

CREATE TABLE `hfpr_bn_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `recipient_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(64) NOT NULL,
  `object_type` varchar(32) DEFAULT NULL,
  `object_id` bigint(20) UNSIGNED DEFAULT NULL,
  `group_key` varchar(128) DEFAULT NULL,
  `group_count` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_notification_prefs`
--

CREATE TABLE `hfpr_bn_notification_prefs` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(64) NOT NULL,
  `on_site` tinyint(1) NOT NULL DEFAULT 1,
  `email_freq` enum('immediate','daily','weekly','off') NOT NULL DEFAULT 'immediate'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_outbound_webhooks`
--

CREATE TABLE `hfpr_bn_outbound_webhooks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(100) NOT NULL,
  `url` varchar(2083) NOT NULL,
  `secret` varchar(64) DEFAULT NULL,
  `events` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`events`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_outbound_webhook_log`
--

CREATE TABLE `hfpr_bn_outbound_webhook_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `webhook_id` bigint(20) UNSIGNED NOT NULL,
  `event` varchar(64) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `response_code` smallint(5) UNSIGNED DEFAULT NULL,
  `response_body` text DEFAULT NULL,
  `status` enum('success','error') NOT NULL DEFAULT 'success',
  `attempt` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_poll_options`
--

CREATE TABLE `hfpr_bn_poll_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `option_text` varchar(500) NOT NULL,
  `display_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `vote_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `end_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_poll_votes`
--

CREATE TABLE `hfpr_bn_poll_votes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `option_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `voted_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_posts`
--

CREATE TABLE `hfpr_bn_posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `space_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shared_post_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(32) NOT NULL DEFAULT 'text',
  `content` longtext DEFAULT NULL,
  `media_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`media_ids`)),
  `link_url` varchar(2083) DEFAULT NULL,
  `link_meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`link_meta`)),
  `privacy` enum('public','followers','connections','space_members','private') NOT NULL DEFAULT 'public',
  `status` enum('published','draft','pending','scheduled','deleted') NOT NULL DEFAULT 'published',
  `reaction_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `comment_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `share_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `is_announcement` tinyint(1) NOT NULL DEFAULT 0,
  `content_warning` tinyint(1) NOT NULL DEFAULT 0,
  `content_warning_type` varchar(32) DEFAULT NULL,
  `site_pin_expires_at` datetime DEFAULT NULL,
  `edited_at` datetime DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `last_activity_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_post_hashtags`
--

CREATE TABLE `hfpr_bn_post_hashtags` (
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `object_type` varchar(32) NOT NULL DEFAULT 'post',
  `hashtag_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_presence`
--

CREATE TABLE `hfpr_bn_presence` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `last_active` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_profile_fields`
--

CREATE TABLE `hfpr_bn_profile_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `field_key` varchar(100) NOT NULL,
  `label` varchar(255) NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT 'text',
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `description` varchar(255) DEFAULT NULL,
  `placeholder` varchar(255) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_searchable` tinyint(1) NOT NULL DEFAULT 0,
  `show_on_register` tinyint(1) NOT NULL DEFAULT 0,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `visibility` enum('public','members','followers','connections','private') NOT NULL DEFAULT 'public',
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_profile_groups`
--

CREATE TABLE `hfpr_bn_profile_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_key` varchar(100) NOT NULL,
  `label` varchar(255) NOT NULL,
  `type` enum('flat','repeater') NOT NULL DEFAULT 'flat',
  `visibility` enum('public','members','followers','connections','private') NOT NULL DEFAULT 'public',
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `type_restriction` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_profile_values`
--

CREATE TABLE `hfpr_bn_profile_values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `field_id` bigint(20) UNSIGNED NOT NULL,
  `entry_index` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `value` longtext DEFAULT NULL,
  `entry_visibility` enum('public','members','followers','connections','private') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_rate_limits`
--

CREATE TABLE `hfpr_bn_rate_limits` (
  `rl_key` varchar(191) NOT NULL,
  `hits` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_reactions`
--

CREATE TABLE `hfpr_bn_reactions` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `object_type` varchar(32) NOT NULL,
  `object_id` bigint(20) UNSIGNED NOT NULL,
  `emoji` varchar(32) NOT NULL DEFAULT 'like',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_reports`
--

CREATE TABLE `hfpr_bn_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reporter_id` bigint(20) UNSIGNED NOT NULL,
  `object_type` varchar(32) NOT NULL,
  `object_id` bigint(20) UNSIGNED NOT NULL,
  `reason` varchar(32) NOT NULL DEFAULT 'other',
  `notes` text DEFAULT NULL,
  `status` enum('pending','dismissed','escalated','resolved') NOT NULL DEFAULT 'pending',
  `resolved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `space_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_search_index`
--

CREATE TABLE `hfpr_bn_search_index` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `object_type` varchar(32) NOT NULL,
  `object_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(500) NOT NULL DEFAULT '',
  `content` longtext DEFAULT NULL,
  `content_members` longtext DEFAULT NULL,
  `author_id` bigint(20) UNSIGNED DEFAULT NULL,
  `space_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visibility` enum('public','private') NOT NULL DEFAULT 'public',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_shares`
--

CREATE TABLE `hfpr_bn_shares` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_spaces`
--

CREATE TABLE `hfpr_bn_spaces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('open','private','secret') NOT NULL DEFAULT 'open',
  `owner_id` bigint(20) UNSIGNED NOT NULL,
  `member_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_active_at` datetime DEFAULT NULL,
  `cover_image_url` varchar(500) DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `rules` text DEFAULT NULL,
  `required_ability` varchar(64) DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_space_bans`
--

CREATE TABLE `hfpr_bn_space_bans` (
  `space_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `banned_by` bigint(20) UNSIGNED NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_space_categories`
--

CREATE TABLE `hfpr_bn_space_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#0073aa',
  `text_color` varchar(7) NOT NULL DEFAULT '#ffffff',
  `icon_svg` mediumtext DEFAULT NULL,
  `show_in_dir` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_space_members`
--

CREATE TABLE `hfpr_bn_space_members` (
  `space_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('owner','moderator','member') NOT NULL DEFAULT 'member',
  `status` enum('active','pending','invited','banned') NOT NULL DEFAULT 'active',
  `notification_pref` enum('all','mentions_only','none') NOT NULL DEFAULT 'all',
  `joined_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_space_meta`
--

CREATE TABLE `hfpr_bn_space_meta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `bn_space_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_user_strikes`
--

CREATE TABLE `hfpr_bn_user_strikes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `issued_by` bigint(20) UNSIGNED NOT NULL,
  `reason` text DEFAULT NULL,
  `is_reversed` tinyint(1) NOT NULL DEFAULT 0,
  `reversed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reversed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_user_suspensions`
--

CREATE TABLE `hfpr_bn_user_suspensions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `suspended_by` bigint(20) UNSIGNED NOT NULL,
  `reason` text DEFAULT NULL,
  `duration_days` int(10) UNSIGNED DEFAULT NULL,
  `hide_posts` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `lifted_at` datetime DEFAULT NULL,
  `lifted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_verify_tokens`
--

CREATE TABLE `hfpr_bn_verify_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT 'email_verify',
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bn_webhook_log`
--

CREATE TABLE `hfpr_bn_webhook_log` (
  `id` bigint(20) NOT NULL,
  `source` varchar(100) NOT NULL DEFAULT '',
  `action` varchar(100) NOT NULL DEFAULT '',
  `user_id` bigint(20) NOT NULL DEFAULT 0,
  `payload` longtext NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'success',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_activity`
--

CREATE TABLE `hfpr_bp_activity` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `component` varchar(75) NOT NULL,
  `type` varchar(75) NOT NULL,
  `action` text NOT NULL,
  `content` longtext NOT NULL,
  `primary_link` text NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `secondary_item_id` bigint(20) DEFAULT NULL,
  `date_recorded` datetime NOT NULL,
  `hide_sitewide` tinyint(1) DEFAULT 0,
  `mptt_left` int(11) NOT NULL DEFAULT 0,
  `mptt_right` int(11) NOT NULL DEFAULT 0,
  `is_spam` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_activity_meta`
--

CREATE TABLE `hfpr_bp_activity_meta` (
  `id` bigint(20) NOT NULL,
  `activity_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_friends`
--

CREATE TABLE `hfpr_bp_friends` (
  `id` bigint(20) NOT NULL,
  `initiator_user_id` bigint(20) NOT NULL,
  `friend_user_id` bigint(20) NOT NULL,
  `is_confirmed` tinyint(1) DEFAULT 0,
  `is_limited` tinyint(1) DEFAULT 0,
  `date_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_groups`
--

CREATE TABLE `hfpr_bp_groups` (
  `id` bigint(20) NOT NULL,
  `creator_id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `description` longtext NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'public',
  `parent_id` bigint(20) NOT NULL DEFAULT 0,
  `enable_forum` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_groups_groupmeta`
--

CREATE TABLE `hfpr_bp_groups_groupmeta` (
  `id` bigint(20) NOT NULL,
  `group_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_groups_members`
--

CREATE TABLE `hfpr_bp_groups_members` (
  `id` bigint(20) NOT NULL,
  `group_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `inviter_id` bigint(20) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_mod` tinyint(1) NOT NULL DEFAULT 0,
  `user_title` varchar(100) NOT NULL,
  `date_modified` datetime NOT NULL,
  `comments` longtext NOT NULL,
  `is_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `is_banned` tinyint(1) NOT NULL DEFAULT 0,
  `invite_sent` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_invitations`
--

CREATE TABLE `hfpr_bp_invitations` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `inviter_id` bigint(20) NOT NULL,
  `invitee_email` varchar(100) DEFAULT NULL,
  `class` varchar(120) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `secondary_item_id` bigint(20) DEFAULT NULL,
  `type` varchar(12) NOT NULL DEFAULT 'invite',
  `content` longtext DEFAULT '',
  `date_modified` datetime NOT NULL,
  `invite_sent` tinyint(1) NOT NULL DEFAULT 0,
  `accepted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_messages_messages`
--

CREATE TABLE `hfpr_bp_messages_messages` (
  `id` bigint(20) NOT NULL,
  `thread_id` bigint(20) NOT NULL,
  `sender_id` bigint(20) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` longtext NOT NULL,
  `date_sent` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_messages_meta`
--

CREATE TABLE `hfpr_bp_messages_meta` (
  `id` bigint(20) NOT NULL,
  `message_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_messages_notices`
--

CREATE TABLE `hfpr_bp_messages_notices` (
  `id` bigint(20) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` longtext NOT NULL,
  `date_sent` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_messages_recipients`
--

CREATE TABLE `hfpr_bp_messages_recipients` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `thread_id` bigint(20) NOT NULL,
  `unread_count` int(10) NOT NULL DEFAULT 0,
  `sender_only` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_notifications`
--

CREATE TABLE `hfpr_bp_notifications` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `secondary_item_id` bigint(20) DEFAULT NULL,
  `component_name` varchar(75) NOT NULL,
  `component_action` varchar(75) NOT NULL,
  `date_notified` datetime NOT NULL,
  `is_new` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_notifications_meta`
--

CREATE TABLE `hfpr_bp_notifications_meta` (
  `id` bigint(20) NOT NULL,
  `notification_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_optouts`
--

CREATE TABLE `hfpr_bp_optouts` (
  `id` bigint(20) NOT NULL,
  `email_address_hash` varchar(255) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `email_type` varchar(255) NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_user_blogs`
--

CREATE TABLE `hfpr_bp_user_blogs` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `blog_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_user_blogs_blogmeta`
--

CREATE TABLE `hfpr_bp_user_blogs_blogmeta` (
  `id` bigint(20) NOT NULL,
  `blog_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_xprofile_data`
--

CREATE TABLE `hfpr_bp_xprofile_data` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `field_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `value` longtext NOT NULL,
  `last_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_xprofile_fields`
--

CREATE TABLE `hfpr_bp_xprofile_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(150) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` longtext NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_default_option` tinyint(1) NOT NULL DEFAULT 0,
  `field_order` bigint(20) NOT NULL DEFAULT 0,
  `option_order` bigint(20) NOT NULL DEFAULT 0,
  `order_by` varchar(15) NOT NULL DEFAULT '',
  `can_delete` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_xprofile_groups`
--

CREATE TABLE `hfpr_bp_xprofile_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` mediumtext NOT NULL,
  `group_order` bigint(20) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_bp_xprofile_meta`
--

CREATE TABLE `hfpr_bp_xprofile_meta` (
  `id` bigint(20) NOT NULL,
  `object_id` bigint(20) NOT NULL,
  `object_type` varchar(150) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_commentmeta`
--

CREATE TABLE `hfpr_commentmeta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `comment_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_comments`
--

CREATE TABLE `hfpr_comments` (
  `comment_ID` bigint(20) UNSIGNED NOT NULL,
  `comment_post_ID` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `comment_author` tinytext NOT NULL,
  `comment_author_email` varchar(100) NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text NOT NULL,
  `comment_karma` int(11) NOT NULL DEFAULT 0,
  `comment_approved` varchar(20) NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) NOT NULL DEFAULT '',
  `comment_type` varchar(20) NOT NULL DEFAULT 'comment',
  `comment_parent` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_email_log`
--

CREATE TABLE `hfpr_email_log` (
  `id` mediumint(9) NOT NULL,
  `to_email` varchar(500) NOT NULL,
  `subject` varchar(500) NOT NULL,
  `message` text NOT NULL,
  `headers` text NOT NULL,
  `attachments` text NOT NULL,
  `sent_date` timestamp NOT NULL,
  `attachment_name` varchar(1000) DEFAULT NULL,
  `ip_address` varchar(15) DEFAULT NULL,
  `result` tinyint(1) DEFAULT NULL,
  `error_message` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_e_events`
--

CREATE TABLE `hfpr_e_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_data` text DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_e_notes`
--

CREATE TABLE `hfpr_e_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `route_url` text DEFAULT NULL COMMENT 'Clean url where the note was created.',
  `route_title` varchar(255) DEFAULT NULL,
  `route_post_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'The post id of the route that the note was created on.',
  `post_id` bigint(20) UNSIGNED DEFAULT NULL,
  `element_id` varchar(60) DEFAULT NULL COMMENT 'The Elementor element ID the note is attached to.',
  `parent_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `author_id` bigint(20) UNSIGNED DEFAULT NULL,
  `author_display_name` varchar(250) DEFAULT NULL COMMENT 'Save the author name when the author was deleted.',
  `status` varchar(20) NOT NULL DEFAULT 'publish',
  `position` text DEFAULT NULL COMMENT 'A JSON string that represents the position of the note inside the element in percentages. e.g. {x:10, y:15}',
  `content` longtext DEFAULT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `last_activity_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_e_notes_users_relations`
--

CREATE TABLE `hfpr_e_notes_users_relations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(60) NOT NULL COMMENT 'The relation type between user and note (e.g mention, watch, read).',
  `note_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_e_submissions`
--

CREATE TABLE `hfpr_e_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(60) DEFAULT NULL,
  `hash_id` varchar(60) NOT NULL,
  `main_meta_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Id of main field. to represent the main meta field',
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `referer` varchar(500) NOT NULL,
  `referer_title` varchar(300) DEFAULT NULL,
  `element_id` varchar(20) NOT NULL,
  `form_name` varchar(60) NOT NULL,
  `campaign_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_ip` varchar(46) NOT NULL,
  `user_agent` text NOT NULL,
  `actions_count` int(11) DEFAULT 0,
  `actions_succeeded_count` int(11) DEFAULT 0,
  `status` varchar(20) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `meta` text DEFAULT NULL,
  `created_at_gmt` datetime NOT NULL,
  `updated_at_gmt` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_e_submissions_actions_log`
--

CREATE TABLE `hfpr_e_submissions_actions_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `action_name` varchar(60) NOT NULL,
  `action_label` varchar(60) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `log` text DEFAULT NULL,
  `created_at_gmt` datetime NOT NULL,
  `updated_at_gmt` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_e_submissions_values`
--

CREATE TABLE `hfpr_e_submissions_values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `key` varchar(60) DEFAULT NULL,
  `value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fbv`
--

CREATE TABLE `hfpr_fbv` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(250) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT 0,
  `type` int(2) NOT NULL DEFAULT 0,
  `ord` int(11) DEFAULT 0,
  `created_by` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fbv_attachment_folder`
--

CREATE TABLE `hfpr_fbv_attachment_folder` (
  `folder_id` int(11) UNSIGNED NOT NULL,
  `attachment_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_ff_scheduled_actions`
--

CREATE TABLE `hfpr_ff_scheduled_actions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) DEFAULT NULL,
  `form_id` bigint(20) UNSIGNED DEFAULT NULL,
  `origin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `feed_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) DEFAULT 'submission_action',
  `status` varchar(255) DEFAULT NULL,
  `data` longtext DEFAULT NULL,
  `note` tinytext DEFAULT NULL,
  `retry_count` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fluentform_draft_submissions`
--

CREATE TABLE `hfpr_fluentform_draft_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` int(10) UNSIGNED DEFAULT NULL,
  `hash` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT 'step_data',
  `step_completed` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `response` longtext DEFAULT NULL,
  `source_url` varchar(255) DEFAULT NULL,
  `browser` varchar(45) DEFAULT NULL,
  `device` varchar(45) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fluentform_entry_details`
--

CREATE TABLE `hfpr_fluentform_entry_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` bigint(20) UNSIGNED DEFAULT NULL,
  `submission_id` bigint(20) UNSIGNED DEFAULT NULL,
  `field_name` varchar(255) DEFAULT NULL,
  `sub_field_name` varchar(255) DEFAULT NULL,
  `field_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fluentform_forms`
--

CREATE TABLE `hfpr_fluentform_forms` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` varchar(45) DEFAULT 'Draft',
  `appearance_settings` text DEFAULT NULL,
  `form_fields` longtext DEFAULT NULL,
  `has_payment` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(45) DEFAULT NULL,
  `conditions` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fluentform_form_analytics`
--

CREATE TABLE `hfpr_fluentform_form_analytics` (
  `id` int(10) UNSIGNED NOT NULL,
  `form_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `source_url` text NOT NULL,
  `platform` char(30) DEFAULT NULL,
  `browser` char(30) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `ip` char(15) DEFAULT NULL,
  `count` int(11) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fluentform_form_meta`
--

CREATE TABLE `hfpr_fluentform_form_meta` (
  `id` int(10) UNSIGNED NOT NULL,
  `form_id` int(10) UNSIGNED DEFAULT NULL,
  `meta_key` varchar(255) NOT NULL,
  `value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fluentform_logs`
--

CREATE TABLE `hfpr_fluentform_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_source_id` int(10) UNSIGNED DEFAULT NULL,
  `source_type` varchar(255) DEFAULT NULL,
  `source_id` int(10) UNSIGNED DEFAULT NULL,
  `component` varchar(255) DEFAULT NULL,
  `status` char(30) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fluentform_order_items`
--

CREATE TABLE `hfpr_fluentform_order_items` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `type` varchar(255) DEFAULT 'single',
  `parent_holder` varchar(255) DEFAULT NULL,
  `billing_interval` varchar(255) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `item_price` bigint(20) UNSIGNED DEFAULT NULL,
  `line_total` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fluentform_submissions`
--

CREATE TABLE `hfpr_fluentform_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` int(10) UNSIGNED DEFAULT NULL,
  `serial_number` int(10) UNSIGNED DEFAULT NULL,
  `response` longtext DEFAULT NULL,
  `source_url` varchar(255) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `status` varchar(45) DEFAULT 'unread' COMMENT 'possible values: read, unread, trashed',
  `is_favourite` tinyint(1) NOT NULL DEFAULT 0,
  `browser` varchar(45) DEFAULT NULL,
  `device` varchar(45) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `payment_status` varchar(45) DEFAULT NULL,
  `payment_method` varchar(45) DEFAULT NULL,
  `payment_type` varchar(45) DEFAULT NULL,
  `currency` varchar(45) DEFAULT NULL,
  `payment_total` float DEFAULT NULL,
  `total_paid` float DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fluentform_submission_meta`
--

CREATE TABLE `hfpr_fluentform_submission_meta` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `response_id` bigint(20) UNSIGNED DEFAULT NULL,
  `form_id` int(10) UNSIGNED DEFAULT NULL,
  `meta_key` varchar(45) DEFAULT NULL,
  `value` longtext DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fluentform_subscriptions`
--

CREATE TABLE `hfpr_fluentform_subscriptions` (
  `id` int(20) NOT NULL,
  `submission_id` int(11) DEFAULT NULL,
  `form_id` int(11) DEFAULT NULL,
  `payment_total` int(11) DEFAULT 0,
  `item_name` varchar(255) DEFAULT NULL,
  `plan_name` varchar(255) DEFAULT NULL,
  `parent_transaction_id` int(11) DEFAULT NULL,
  `billing_interval` varchar(50) DEFAULT NULL,
  `trial_days` int(11) DEFAULT NULL,
  `initial_amount` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `recurring_amount` int(11) DEFAULT NULL,
  `bill_times` int(11) DEFAULT NULL,
  `bill_count` int(11) DEFAULT 0,
  `vendor_customer_id` varchar(255) DEFAULT NULL,
  `vendor_subscription_id` varchar(255) DEFAULT NULL,
  `vendor_plan_id` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'pending',
  `initial_tax_label` varchar(255) DEFAULT NULL,
  `initial_tax` int(11) DEFAULT NULL,
  `recurring_tax_label` varchar(255) DEFAULT NULL,
  `recurring_tax` int(11) DEFAULT NULL,
  `element_id` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `original_plan` text DEFAULT NULL,
  `vendor_response` longtext DEFAULT NULL,
  `expiration_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fluentform_transactions`
--

CREATE TABLE `hfpr_fluentform_transactions` (
  `id` int(11) NOT NULL,
  `transaction_hash` varchar(255) DEFAULT NULL,
  `payer_name` varchar(255) DEFAULT NULL,
  `payer_email` varchar(255) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `form_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `submission_id` int(11) DEFAULT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `transaction_type` varchar(255) DEFAULT 'onetime',
  `payment_method` varchar(255) DEFAULT NULL,
  `card_last_4` int(4) DEFAULT NULL,
  `card_brand` varchar(255) DEFAULT NULL,
  `charge_id` varchar(255) DEFAULT NULL,
  `payment_total` bigint(20) UNSIGNED DEFAULT 1,
  `status` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `payment_mode` varchar(255) DEFAULT NULL,
  `payment_note` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_fsmpt_email_logs`
--

CREATE TABLE `hfpr_fsmpt_email_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `site_id` int(10) UNSIGNED DEFAULT NULL,
  `to` varchar(255) DEFAULT NULL,
  `from` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `headers` longtext DEFAULT NULL,
  `attachments` longtext DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `response` text DEFAULT NULL,
  `extra` text DEFAULT NULL,
  `retries` int(10) UNSIGNED DEFAULT 0,
  `resent_count` int(10) UNSIGNED DEFAULT 0,
  `source` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_access_rules`
--

CREATE TABLE `hfpr_jt_access_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `space_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `rule_type` enum('membership','role','capability','trust_level','logged_in','everyone') NOT NULL DEFAULT 'everyone',
  `rule_value` varchar(255) DEFAULT NULL,
  `grants` enum('read','participate','full') NOT NULL DEFAULT 'read',
  `space_role` enum('viewer','member','moderator','admin') NOT NULL DEFAULT 'viewer',
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_activity_log`
--

CREATE TABLE `hfpr_jt_activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `action` varchar(50) NOT NULL DEFAULT '',
  `object_type` varchar(50) NOT NULL DEFAULT '',
  `object_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `metadata` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_attachments`
--

CREATE TABLE `hfpr_jt_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `object_type` enum('post','reply') NOT NULL,
  `object_id` bigint(20) UNSIGNED NOT NULL,
  `attachment_id` bigint(20) UNSIGNED NOT NULL,
  `sort` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_blocked_users`
--

CREATE TABLE `hfpr_jt_blocked_users` (
  `blocker_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `blocked_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_bookmarks`
--

CREATE TABLE `hfpr_jt_bookmarks` (
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_categories`
--

CREATE TABLE `hfpr_jt_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `description` longtext DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `space_count` int(11) NOT NULL DEFAULT 0,
  `visibility` enum('public','private','hidden') NOT NULL DEFAULT 'public',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_flags`
--

CREATE TABLE `hfpr_jt_flags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reporter_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `object_type` enum('post','reply','user') NOT NULL DEFAULT 'post',
  `object_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `reason` enum('spam','offensive','off_topic','harassment','other') NOT NULL DEFAULT 'other',
  `description` text DEFAULT NULL,
  `status` enum('pending','valid','dismissed') NOT NULL DEFAULT 'pending',
  `resolved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_invite_links`
--

CREATE TABLE `hfpr_jt_invite_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `space_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `max_uses` int(11) DEFAULT 0,
  `use_count` int(11) DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_join_requests`
--

CREATE TABLE `hfpr_jt_join_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `space_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `message` text DEFAULT NULL,
  `status` enum('pending','approved','denied') NOT NULL DEFAULT 'pending',
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_notifications`
--

CREATE TABLE `hfpr_jt_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `actor_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `actor_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(50) NOT NULL DEFAULT '',
  `object_type` enum('post','reply','space','badge','message') NOT NULL DEFAULT 'post',
  `object_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `message` varchar(500) NOT NULL DEFAULT '',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_posts`
--

CREATE TABLE `hfpr_jt_posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `space_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `author_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `type` enum('topic','question','idea','status') NOT NULL DEFAULT 'topic',
  `prefix` varchar(100) DEFAULT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `content_plain` longtext DEFAULT NULL,
  `status` enum('publish','pending','draft','spam','trash') NOT NULL DEFAULT 'publish',
  `published_at` datetime DEFAULT NULL,
  `is_sticky` tinyint(1) NOT NULL DEFAULT 0,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0,
  `idea_status` enum('planned','in_progress','shipped','declined') DEFAULT NULL,
  `vote_score` int(11) NOT NULL DEFAULT 0,
  `reply_count` int(11) NOT NULL DEFAULT 0,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `flag_count` int(11) NOT NULL DEFAULT 0,
  `last_reply_at` datetime DEFAULT NULL,
  `last_reply_by` bigint(20) UNSIGNED DEFAULT NULL,
  `accepted_reply_id` bigint(20) UNSIGNED DEFAULT NULL,
  `edited_at` datetime DEFAULT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_post_tags`
--

CREATE TABLE `hfpr_jt_post_tags` (
  `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `tag_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_read_status`
--

CREATE TABLE `hfpr_jt_read_status` (
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `last_read_reply_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_replies`
--

CREATE TABLE `hfpr_jt_replies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `author_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `content` longtext DEFAULT NULL,
  `content_plain` longtext DEFAULT NULL,
  `status` enum('publish','pending','spam','trash') NOT NULL DEFAULT 'publish',
  `vote_score` int(11) NOT NULL DEFAULT 0,
  `is_accepted` tinyint(1) NOT NULL DEFAULT 0,
  `edited_at` datetime DEFAULT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_restrictions`
--

CREATE TABLE `hfpr_jt_restrictions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `type` enum('global_ban','space_ban','silence','post_restrict','ip_ban') NOT NULL DEFAULT 'silence',
  `space_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `issued_by` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_revisions`
--

CREATE TABLE `hfpr_jt_revisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `object_type` enum('post','reply') NOT NULL DEFAULT 'post',
  `object_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `author_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `content` longtext DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `edit_summary` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_spaces`
--

CREATE TABLE `hfpr_jt_spaces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `parent_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `author_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `type` enum('forum','qa','ideas','feed') NOT NULL DEFAULT 'forum',
  `title` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `description` longtext DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `visibility` enum('public','private','hidden') NOT NULL DEFAULT 'public',
  `join_policy` enum('open','approval','invite') NOT NULL DEFAULT 'open',
  `status` enum('active','archived','locked') NOT NULL DEFAULT 'active',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `settings` longtext DEFAULT NULL,
  `post_count` int(11) NOT NULL DEFAULT 0,
  `member_count` int(11) NOT NULL DEFAULT 0,
  `last_activity_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_space_members`
--

CREATE TABLE `hfpr_jt_space_members` (
  `space_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `role` enum('viewer','member','moderator','admin') NOT NULL DEFAULT 'member',
  `joined_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_subscriptions`
--

CREATE TABLE `hfpr_jt_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `object_type` enum('space','post') NOT NULL DEFAULT 'space',
  `object_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `notify_via` enum('web','email','both') NOT NULL DEFAULT 'web',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_tags`
--

CREATE TABLE `hfpr_jt_tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `post_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_user_profiles`
--

CREATE TABLE `hfpr_jt_user_profiles` (
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `display_name` varchar(255) NOT NULL DEFAULT '',
  `bio` longtext DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `trust_level` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `reputation` int(11) NOT NULL DEFAULT 0,
  `post_count` int(11) NOT NULL DEFAULT 0,
  `reply_count` int(11) NOT NULL DEFAULT 0,
  `vote_received` int(11) NOT NULL DEFAULT 0,
  `badges` longtext DEFAULT NULL,
  `settings` longtext DEFAULT NULL,
  `last_seen_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime DEFAULT NULL,
  `verification_reminder_sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_jt_votes`
--

CREATE TABLE `hfpr_jt_votes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `object_type` enum('post','reply') NOT NULL DEFAULT 'post',
  `object_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `value` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_links`
--

CREATE TABLE `hfpr_links` (
  `link_id` bigint(20) UNSIGNED NOT NULL,
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `link_image` varchar(255) NOT NULL DEFAULT '',
  `link_target` varchar(25) NOT NULL DEFAULT '',
  `link_description` varchar(255) NOT NULL DEFAULT '',
  `link_visible` varchar(20) NOT NULL DEFAULT 'Y',
  `link_owner` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `link_rating` int(11) NOT NULL DEFAULT 0,
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) NOT NULL DEFAULT '',
  `link_notes` mediumtext NOT NULL,
  `link_rss` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_access_grants`
--

CREATE TABLE `hfpr_mvs_access_grants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `granted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `source` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_access_rules`
--

CREATE TABLE `hfpr_mvs_access_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `rule_type` varchar(50) NOT NULL,
  `rule_value` text NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_activity`
--

CREATE TABLE `hfpr_mvs_activity` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `album_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `content` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_album_items`
--

CREATE TABLE `hfpr_mvs_album_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `album_id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `position` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `added_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_blocks`
--

CREATE TABLE `hfpr_mvs_blocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `blocker_id` bigint(20) UNSIGNED NOT NULL,
  `blocked_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_bp_activity_media`
--

CREATE TABLE `hfpr_mvs_bp_activity_media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `object_type` varchar(32) NOT NULL DEFAULT 'bp_activity',
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `variant` varchar(20) NOT NULL DEFAULT 'image',
  `position` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_conversations`
--

CREATE TABLE `hfpr_mvs_conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'direct',
  `container_type` varchar(32) NOT NULL DEFAULT '',
  `container_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `title` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `last_message_id` bigint(20) UNSIGNED DEFAULT NULL,
  `last_message_preview` varchar(255) DEFAULT NULL,
  `last_activity_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_conversation_participants`
--

CREATE TABLE `hfpr_mvs_conversation_participants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'member',
  `last_read_at` datetime DEFAULT NULL,
  `typing_until` datetime DEFAULT NULL,
  `is_muted` tinyint(1) NOT NULL DEFAULT 0,
  `muted_until` datetime DEFAULT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `cleared_up_to` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `joined_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_error_log`
--

CREATE TABLE `hfpr_mvs_error_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `level` varchar(10) NOT NULL DEFAULT 'info',
  `context` varchar(50) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `metadata` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `ip_address` varchar(45) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_favorites`
--

CREATE TABLE `hfpr_mvs_favorites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `collection_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_follows`
--

CREATE TABLE `hfpr_mvs_follows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `follower_id` bigint(20) UNSIGNED NOT NULL,
  `following_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_media_index`
--

CREATE TABLE `hfpr_mvs_media_index` (
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `description` longtext DEFAULT NULL,
  `post_author` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'publish',
  `media_type` varchar(20) NOT NULL DEFAULT '',
  `privacy` varchar(20) NOT NULL DEFAULT 'public',
  `moderation_status` varchar(20) NOT NULL DEFAULT 'approved',
  `file_url` text DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `file_type` varchar(100) DEFAULT '',
  `file_size` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `file_hash` varchar(64) DEFAULT '',
  `width` int(11) UNSIGNED DEFAULT NULL,
  `height` int(11) UNSIGNED DEFAULT NULL,
  `duration` decimal(10,2) DEFAULT NULL,
  `album_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `view_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `reaction_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `comment_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_media_meta`
--

CREATE TABLE `hfpr_mvs_media_meta` (
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `meta_key` varchar(100) NOT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_media_stats`
--

CREATE TABLE `hfpr_mvs_media_stats` (
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `views` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `downloads` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `reactions` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `comments` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `shares` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_media_views`
--

CREATE TABLE `hfpr_mvs_media_views` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_hash` varchar(64) NOT NULL DEFAULT '',
  `event_type` enum('view','download') NOT NULL DEFAULT 'view',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_mentions`
--

CREATE TABLE `hfpr_mvs_mentions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `mentioned_user_id` bigint(20) UNSIGNED NOT NULL,
  `context` varchar(50) NOT NULL DEFAULT 'description',
  `comment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_messages`
--

CREATE TABLE `hfpr_mvs_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `content` longtext DEFAULT NULL,
  `message_type` varchar(20) NOT NULL DEFAULT 'text',
  `attachment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `media_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `metadata` text DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_for_all` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_message_reactions`
--

CREATE TABLE `hfpr_mvs_message_reactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `message_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `emoji` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_notifications`
--

CREATE TABLE `hfpr_mvs_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `actor_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `media_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `comment_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_reactions`
--

CREATE TABLE `hfpr_mvs_reactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `reaction_type` enum('like','love','haha','wow','sad','angry') NOT NULL DEFAULT 'like',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_reports`
--

CREATE TABLE `hfpr_mvs_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reporter_id` bigint(20) UNSIGNED NOT NULL,
  `target_type` varchar(20) NOT NULL,
  `target_id` bigint(20) UNSIGNED NOT NULL,
  `reason` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_mvs_transactions`
--

CREATE TABLE `hfpr_mvs_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `media_type` varchar(20) NOT NULL,
  `delta` int(11) NOT NULL,
  `balance_after` int(11) NOT NULL DEFAULT 0,
  `reason` varchar(100) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_postmeta`
--

CREATE TABLE `hfpr_postmeta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_posts`
--

CREATE TABLE `hfpr_posts` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `post_author` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext NOT NULL,
  `post_title` text NOT NULL,
  `post_excerpt` text NOT NULL,
  `post_status` varchar(20) NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) NOT NULL DEFAULT 'open',
  `post_password` varchar(255) NOT NULL DEFAULT '',
  `post_name` varchar(200) NOT NULL DEFAULT '',
  `to_ping` text NOT NULL,
  `pinged` text NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext NOT NULL,
  `post_parent` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `guid` varchar(255) NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT 0,
  `post_type` varchar(20) NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_achievements`
--

CREATE TABLE `hfpr_rts_achievements` (
  `id` bigint(20) NOT NULL,
  `participant_id` bigint(20) NOT NULL,
  `achievement_type` varchar(50) NOT NULL,
  `achievement_name` varchar(255) NOT NULL,
  `achievement_description` text DEFAULT NULL,
  `achievement_image_url` varchar(500) DEFAULT NULL,
  `achievement_date` datetime DEFAULT NULL,
  `is_displayed` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_action_items`
--

CREATE TABLE `hfpr_rts_action_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rule_key` varchar(60) NOT NULL,
  `category` varchar(60) NOT NULL,
  `recommendation` text NOT NULL,
  `backed_by` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'open',
  `outcome_note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_activity_logs`
--

CREATE TABLE `hfpr_rts_activity_logs` (
  `id` bigint(20) NOT NULL,
  `tracking_id` bigint(20) NOT NULL,
  `submission_id` varchar(36) NOT NULL,
  `action` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_audit_log`
--

CREATE TABLE `hfpr_rts_audit_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user` varchar(100) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `module` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `result` varchar(20) DEFAULT 'success',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `source_table` varchar(30) DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_backups`
--

CREATE TABLE `hfpr_rts_backups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `triggered_by` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'completed',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_cabin_credits`
--

CREATE TABLE `hfpr_rts_cabin_credits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `participant_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(20) DEFAULT 'issued',
  `value_usd` decimal(10,2) DEFAULT 100.00,
  `issued_at` datetime DEFAULT current_timestamp(),
  `cabin_reservation_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_campaigns`
--

CREATE TABLE `hfpr_rts_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `platform` varchar(50) DEFAULT NULL,
  `utm_campaign_code` varchar(100) NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `cost_charged` decimal(10,2) DEFAULT 0.00,
  `impressions` int(11) DEFAULT 0,
  `clicks` int(11) DEFAULT 0,
  `ad_wording` text DEFAULT NULL,
  `target_age_groups` varchar(100) DEFAULT NULL,
  `audience_focus` varchar(50) DEFAULT NULL,
  `geography` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_campaign_sends`
--

CREATE TABLE `hfpr_rts_campaign_sends` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `campaign_id` bigint(20) UNSIGNED NOT NULL,
  `participant_id` bigint(20) UNSIGNED NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_content_blocks`
--

CREATE TABLE `hfpr_rts_content_blocks` (
  `block_key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_customer_questions`
--

CREATE TABLE `hfpr_rts_customer_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `participant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `question_text` text NOT NULL,
  `source` varchar(20) DEFAULT 'manual',
  `status` varchar(20) DEFAULT 'open',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_draws`
--

CREATE TABLE `hfpr_rts_draws` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `draw_type` varchar(1) NOT NULL,
  `random_seed` varchar(64) NOT NULL,
  `eligible_entry_count` int(11) DEFAULT NULL,
  `winner_participant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `run_by` varchar(100) DEFAULT NULL,
  `run_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_duplicate_reviews`
--

CREATE TABLE `hfpr_rts_duplicate_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tracking_id` bigint(20) NOT NULL,
  `participant_id` bigint(20) DEFAULT NULL,
  `decision` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `reviewed_by` varchar(100) DEFAULT NULL,
  `reviewed_at` datetime NOT NULL,
  `participant_id_a` bigint(20) UNSIGNED NOT NULL,
  `participant_id_b` bigint(20) UNSIGNED NOT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(30) DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_email_campaigns`
--

CREATE TABLE `hfpr_rts_email_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `trigger_type` varchar(40) DEFAULT 'days_after_registration',
  `trigger_days` int(11) DEFAULT 3,
  `audience_filter` varchar(30) DEFAULT 'all',
  `category` varchar(20) DEFAULT 'general',
  `status` varchar(20) DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp(),
  `delivery_mode` varchar(20) DEFAULT 'automation',
  `scheduled_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_email_drafts`
--

CREATE TABLE `hfpr_rts_email_drafts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(20) NOT NULL,
  `audience_filter` varchar(30) DEFAULT 'all',
  `subject` varchar(255) NOT NULL,
  `body` longtext DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `test_self_sent` tinyint(1) DEFAULT 0,
  `test_self_sent_at` datetime DEFAULT NULL,
  `test_self_email` varchar(255) DEFAULT NULL,
  `test_group_sent` tinyint(1) DEFAULT 0,
  `test_group_sent_at` datetime DEFAULT NULL,
  `test_group_emails` text DEFAULT NULL,
  `bulk_sent` tinyint(1) DEFAULT 0,
  `bulk_sent_at` datetime DEFAULT NULL,
  `bulk_sent_forced` tinyint(1) DEFAULT 0,
  `bulk_sent_force_reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_email_outbox`
--

CREATE TABLE `hfpr_rts_email_outbox` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `to_email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body_html` longtext DEFAULT NULL,
  `kind` varchar(20) DEFAULT 'marketing',
  `mode` varchar(10) DEFAULT 'log',
  `delivered` tinyint(1) DEFAULT NULL,
  `error` text DEFAULT NULL,
  `meta` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_email_templates`
--

CREATE TABLE `hfpr_rts_email_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(30) DEFAULT 'general',
  `subject` varchar(255) NOT NULL,
  `html_body` longtext DEFAULT NULL,
  `plain_text_body` longtext DEFAULT NULL,
  `status` varchar(20) DEFAULT 'draft',
  `version` int(11) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp(),
  `template_key` varchar(64) DEFAULT NULL,
  `action_key` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_email_template_versions`
--

CREATE TABLE `hfpr_rts_email_template_versions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `template_id` bigint(20) UNSIGNED NOT NULL,
  `version` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `html_body` longtext DEFAULT NULL,
  `plain_text_body` longtext DEFAULT NULL,
  `saved_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_export_history`
--

CREATE TABLE `hfpr_rts_export_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dataset` varchar(50) NOT NULL,
  `format` varchar(10) DEFAULT 'csv',
  `requested_by` varchar(100) DEFAULT NULL,
  `row_count` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_external_founding_runners`
--

CREATE TABLE `hfpr_rts_external_founding_runners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `source` varchar(50) DEFAULT 'main_site',
  `matched_participant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_medals`
--

CREATE TABLE `hfpr_rts_medals` (
  `id` bigint(20) NOT NULL,
  `participant_id` bigint(20) NOT NULL,
  `medal_type` varchar(50) NOT NULL,
  `medal_name` varchar(255) NOT NULL,
  `medal_description` text DEFAULT NULL,
  `medal_image_url` varchar(500) DEFAULT NULL,
  `earned_date` datetime DEFAULT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `medal_rank` varchar(20) DEFAULT NULL,
  `is_displayed` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_participants`
--

CREATE TABLE `hfpr_rts_participants` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `survey_tracking_id` bigint(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(50) DEFAULT NULL,
  `registration_date` datetime DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `email_verification_token` varchar(64) DEFAULT NULL,
  `email_verification_date` datetime DEFAULT NULL,
  `cabin_credit_requested` tinyint(1) DEFAULT 0,
  `cabin_credit_number` varchar(50) DEFAULT NULL,
  `cabin_credit_status` varchar(20) DEFAULT 'pending',
  `cabin_credit_approved_date` datetime DEFAULT NULL,
  `captain_suite_status` varchar(20) DEFAULT 'inactive',
  `captain_referral_participation` varchar(20) DEFAULT 'not_started',
  `captain_miles_balance` int(11) DEFAULT 0,
  `total_captain_miles_earned` int(11) DEFAULT 0,
  `total_captain_miles_used` int(11) DEFAULT 0,
  `referral_code` varchar(50) DEFAULT NULL,
  `referral_count` int(11) DEFAULT 0,
  `successful_referrals` int(11) DEFAULT 0,
  `total_referral_bonus` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `referred_by` bigint(20) DEFAULT NULL,
  `referral_completed` tinyint(1) DEFAULT 0,
  `referral_completed_date` datetime DEFAULT NULL,
  `qr_code_url` varchar(500) DEFAULT NULL,
  `cabin_credit_amount` decimal(10,2) DEFAULT 100.00,
  `cabin_credit_issued_at` datetime DEFAULT NULL,
  `cabin_credit_issued_by` bigint(20) DEFAULT NULL,
  `captain_suite_activated_at` datetime DEFAULT NULL,
  `captain_suite_activated_by` bigint(20) DEFAULT NULL,
  `certificate_number` varchar(50) DEFAULT NULL,
  `certificate_issued_at` datetime DEFAULT NULL,
  `certificate_sent_at` datetime DEFAULT NULL,
  `age_consent_confirmed_at` datetime DEFAULT NULL,
  `age_consent_ip_address` varchar(45) DEFAULT NULL,
  `founding_runner_number` varchar(50) DEFAULT NULL,
  `unsubscribe_token` varchar(40) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `verification_token` varchar(64) DEFAULT NULL,
  `verification_sent_at` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `runner_status` varchar(20) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `age_range` varchar(20) DEFAULT NULL,
  `travel_party_size` int(11) DEFAULT NULL,
  `household_income_bracket` varchar(50) DEFAULT NULL,
  `marketing_source` varchar(50) DEFAULT NULL,
  `utm_campaign` varchar(100) DEFAULT NULL,
  `account_status` varchar(20) DEFAULT 'active',
  `wants_cruise_notification` tinyint(1) DEFAULT 0,
  `declined_further_contact` tinyint(1) DEFAULT 0,
  `referred_by_participant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `registered_at` datetime DEFAULT NULL,
  `merged_into_participant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `merged_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_participant_notes`
--

CREATE TABLE `hfpr_rts_participant_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `participant_id` bigint(20) UNSIGNED NOT NULL,
  `admin_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `admin_name` varchar(255) NOT NULL,
  `note_text` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_participant_survey_links`
--

CREATE TABLE `hfpr_rts_participant_survey_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `participant_id` bigint(20) UNSIGNED NOT NULL,
  `tracking_id` bigint(20) UNSIGNED NOT NULL,
  `linked_by` bigint(20) UNSIGNED DEFAULT NULL,
  `linked_by_name` varchar(255) DEFAULT NULL,
  `linked_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_question_response_drafts`
--

CREATE TABLE `hfpr_rts_question_response_drafts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_question_id` bigint(20) UNSIGNED NOT NULL,
  `version` int(11) NOT NULL,
  `draft_text` text NOT NULL,
  `feedback_that_prompted_this` text DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_question_response_log`
--

CREATE TABLE `hfpr_rts_question_response_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_question_id` bigint(20) UNSIGNED NOT NULL,
  `final_response` text NOT NULL,
  `version_count` int(11) DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_races`
--

CREATE TABLE `hfpr_rts_races` (
  `id` bigint(20) NOT NULL,
  `race_name` varchar(255) NOT NULL,
  `race_type` varchar(50) NOT NULL,
  `distance_km` decimal(10,2) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `trophy_image_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_race_participants`
--

CREATE TABLE `hfpr_rts_race_participants` (
  `id` bigint(20) NOT NULL,
  `participant_id` bigint(20) NOT NULL,
  `race_id` bigint(20) NOT NULL,
  `registration_date` datetime DEFAULT NULL,
  `completion_time` time DEFAULT NULL,
  `completion_date` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'registered',
  `rank_position` int(11) DEFAULT NULL,
  `medal_type` varchar(20) DEFAULT NULL,
  `achievement_points` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_referrals`
--

CREATE TABLE `hfpr_rts_referrals` (
  `id` bigint(20) NOT NULL,
  `referrer_id` bigint(20) NOT NULL,
  `referred_email` varchar(255) NOT NULL,
  `referred_participant_id` bigint(20) DEFAULT NULL,
  `referral_code` varchar(50) NOT NULL,
  `referral_source` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `bonus_earned` int(11) DEFAULT 0,
  `referral_date` datetime DEFAULT NULL,
  `completed_date` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `referring_participant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `clicked_at` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `fraud_review_status` varchar(20) DEFAULT 'clear'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_report_definitions`
--

CREATE TABLE `hfpr_rts_report_definitions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `data_source` varchar(40) NOT NULL,
  `fields_json` text NOT NULL,
  `filters_json` text DEFAULT NULL,
  `schedule_frequency` varchar(20) DEFAULT 'none',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_report_runs`
--

CREATE TABLE `hfpr_rts_report_runs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_id` bigint(20) UNSIGNED NOT NULL,
  `row_count` int(11) DEFAULT NULL,
  `run_by` varchar(100) DEFAULT NULL,
  `run_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_segments`
--

CREATE TABLE `hfpr_rts_segments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `filters_json` text NOT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_sent_emails`
--

CREATE TABLE `hfpr_rts_sent_emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(20) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `recipient_count` int(11) DEFAULT NULL,
  `excluded_unsubscribed_count` int(11) DEFAULT NULL,
  `sent_by` varchar(100) DEFAULT NULL,
  `test_mode` tinyint(1) DEFAULT 0,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_subscriptions`
--

CREATE TABLE `hfpr_rts_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `participant_id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(20) NOT NULL,
  `subscribed` tinyint(1) DEFAULT 1,
  `unsubscribe_reason` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_surveys`
--

CREATE TABLE `hfpr_rts_surveys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `language` varchar(10) DEFAULT 'EN',
  `status` varchar(20) DEFAULT 'draft',
  `version` int(11) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `source_form_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_survey_analytics`
--

CREATE TABLE `hfpr_rts_survey_analytics` (
  `id` bigint(20) NOT NULL,
  `form_id` bigint(20) NOT NULL,
  `question_id` varchar(255) NOT NULL,
  `answer_option` text NOT NULL,
  `total_votes` int(11) DEFAULT 0,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_survey_answers`
--

CREATE TABLE `hfpr_rts_survey_answers` (
  `id` bigint(20) NOT NULL,
  `tracking_id` bigint(20) NOT NULL,
  `tracking_submission_id` varchar(36) NOT NULL,
  `form_id` bigint(20) NOT NULL,
  `question_id` varchar(255) NOT NULL,
  `question_label` text DEFAULT NULL,
  `question_type` varchar(50) DEFAULT NULL,
  `answer_value` text DEFAULT NULL,
  `answer_label` text DEFAULT NULL,
  `step_number` int(11) DEFAULT 0,
  `answered_at` datetime DEFAULT NULL,
  `is_final_answer` tinyint(1) DEFAULT 0,
  `response_id` bigint(20) UNSIGNED DEFAULT NULL,
  `platform_question_id` bigint(20) UNSIGNED DEFAULT NULL,
  `comment_text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_survey_questions`
--

CREATE TABLE `hfpr_rts_survey_questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `survey_id` bigint(20) UNSIGNED NOT NULL,
  `question_number` int(11) NOT NULL,
  `section` varchar(100) DEFAULT NULL,
  `prompt` text NOT NULL,
  `question_type` varchar(30) NOT NULL,
  `options_json` text DEFAULT NULL,
  `required` tinyint(1) DEFAULT 1,
  `allow_comment` tinyint(1) DEFAULT 0,
  `conditional_on_question_id` bigint(20) UNSIGNED DEFAULT NULL,
  `conditional_equals` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `source_form_id` bigint(20) UNSIGNED DEFAULT NULL,
  `source_question_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_survey_responses`
--

CREATE TABLE `hfpr_rts_survey_responses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `survey_id` bigint(20) UNSIGNED NOT NULL,
  `participant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_token` varchar(64) NOT NULL,
  `status` varchar(20) DEFAULT 'in_progress',
  `started_at` datetime DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL,
  `source_tracking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `source_submission_id` varchar(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_survey_tracking`
--

CREATE TABLE `hfpr_rts_survey_tracking` (
  `id` bigint(20) NOT NULL,
  `submission_id` varchar(36) NOT NULL,
  `session_id` varchar(64) NOT NULL,
  `form_id` bigint(20) NOT NULL,
  `user_ip` varchar(45) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `location_accuracy` int(11) DEFAULT NULL,
  `location_source` varchar(20) DEFAULT 'ip',
  `user_agent` text DEFAULT NULL,
  `referrer_url` text DEFAULT NULL,
  `referral_source` varchar(255) DEFAULT NULL,
  `referral_code` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `completion_status` varchar(20) DEFAULT 'in_progress',
  `answered_questions` int(11) DEFAULT 0,
  `time_spent_seconds` int(11) DEFAULT 0,
  `current_step` int(11) DEFAULT 0,
  `is_duplicate` tinyint(1) DEFAULT 0,
  `duplicate_of` varchar(36) DEFAULT NULL,
  `referrer_participant_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_sync_logs`
--

CREATE TABLE `hfpr_rts_sync_logs` (
  `id` bigint(20) NOT NULL,
  `form_id` bigint(20) NOT NULL,
  `action` varchar(50) NOT NULL,
  `data` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_timeline`
--

CREATE TABLE `hfpr_rts_timeline` (
  `id` bigint(20) NOT NULL,
  `participant_id` bigint(20) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `activity_description` text NOT NULL,
  `activity_data` text DEFAULT NULL,
  `activity_date` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_trophies`
--

CREATE TABLE `hfpr_rts_trophies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `unlock_rule` varchar(50) DEFAULT NULL,
  `category` varchar(20) DEFAULT 'repeatable'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_trophy_unlocks`
--

CREATE TABLE `hfpr_rts_trophy_unlocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trophy_id` bigint(20) UNSIGNED NOT NULL,
  `participant_id` bigint(20) UNSIGNED NOT NULL,
  `unlocked_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_rts_user_trophies`
--

CREATE TABLE `hfpr_rts_user_trophies` (
  `id` bigint(20) NOT NULL,
  `participant_id` bigint(20) NOT NULL,
  `race_id` bigint(20) DEFAULT NULL,
  `trophy_name` varchar(255) NOT NULL,
  `trophy_type` varchar(50) NOT NULL,
  `trophy_key` varchar(50) DEFAULT NULL,
  `trophy_rank` varchar(20) DEFAULT NULL,
  `trophy_image_url` varchar(500) DEFAULT NULL,
  `earned_date` datetime DEFAULT NULL,
  `split_days` int(11) DEFAULT 0,
  `total_days` int(11) DEFAULT 0,
  `crew_members` int(11) DEFAULT 0,
  `miles_required` int(11) DEFAULT 0,
  `is_displayed` tinyint(1) DEFAULT 1,
  `achievement_points` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_signups`
--

CREATE TABLE `hfpr_signups` (
  `signup_id` bigint(20) UNSIGNED NOT NULL,
  `domain` varchar(200) NOT NULL DEFAULT '',
  `path` varchar(100) NOT NULL DEFAULT '',
  `title` longtext NOT NULL,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `activation_key` varchar(50) NOT NULL DEFAULT '',
  `meta` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_termmeta`
--

CREATE TABLE `hfpr_termmeta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `term_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_terms`
--

CREATE TABLE `hfpr_terms` (
  `term_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL DEFAULT '',
  `slug` varchar(200) NOT NULL DEFAULT '',
  `term_group` bigint(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_term_relationships`
--

CREATE TABLE `hfpr_term_relationships` (
  `object_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `term_taxonomy_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `term_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_term_taxonomy`
--

CREATE TABLE `hfpr_term_taxonomy` (
  `term_taxonomy_id` bigint(20) UNSIGNED NOT NULL,
  `term_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `taxonomy` varchar(32) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `parent` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `count` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_usermeta`
--

CREATE TABLE `hfpr_usermeta` (
  `umeta_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_users`
--

CREATE TABLE `hfpr_users` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(255) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT 0,
  `display_name` varchar(250) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_api_keys`
--

CREATE TABLE `hfpr_wb_gam_api_keys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key_hash` varchar(64) NOT NULL,
  `key_prefix` varchar(16) NOT NULL,
  `key_suffix` varchar(8) NOT NULL,
  `label` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `site_id` varchar(64) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_used` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_badge_defs`
--

CREATE TABLE `hfpr_wb_gam_badge_defs` (
  `id` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `is_credential` tinyint(1) DEFAULT 0,
  `validity_days` int(10) UNSIGNED DEFAULT NULL,
  `closes_at` datetime DEFAULT NULL,
  `max_earners` int(10) UNSIGNED DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_challenges`
--

CREATE TABLE `hfpr_wb_gam_challenges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(20) DEFAULT 'individual',
  `team_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action_id` varchar(100) NOT NULL,
  `target` int(10) UNSIGNED NOT NULL,
  `bonus_points` int(11) NOT NULL DEFAULT 0,
  `period` varchar(20) DEFAULT 'none',
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_challenge_log`
--

CREATE TABLE `hfpr_wb_gam_challenge_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `challenge_id` bigint(20) UNSIGNED NOT NULL,
  `progress` int(10) UNSIGNED DEFAULT 0,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_cohort_members`
--

CREATE TABLE `hfpr_wb_gam_cohort_members` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `cohort_id` varchar(50) NOT NULL,
  `tier` tinyint(3) UNSIGNED DEFAULT 0,
  `tier_end` tinyint(3) UNSIGNED DEFAULT NULL,
  `outcome` varchar(20) DEFAULT NULL,
  `week` varchar(10) NOT NULL,
  `pts_start` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_community_challenges`
--

CREATE TABLE `hfpr_wb_gam_community_challenges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `target_action` varchar(100) NOT NULL,
  `target_count` bigint(20) UNSIGNED NOT NULL,
  `global_progress` bigint(20) UNSIGNED DEFAULT 0,
  `bonus_points` int(11) NOT NULL DEFAULT 0,
  `status` varchar(20) DEFAULT 'active',
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_community_challenge_contributions`
--

CREATE TABLE `hfpr_wb_gam_community_challenge_contributions` (
  `challenge_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `contribution_count` bigint(20) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_events`
--

CREATE TABLE `hfpr_wb_gam_events` (
  `id` varchar(36) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action_id` varchar(100) NOT NULL,
  `object_id` bigint(20) UNSIGNED DEFAULT NULL,
  `metadata` longtext DEFAULT NULL,
  `point_type` varchar(60) NOT NULL DEFAULT 'points',
  `site_id` varchar(100) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_kudos`
--

CREATE TABLE `hfpr_wb_gam_kudos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `giver_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_leaderboard_cache`
--

CREATE TABLE `hfpr_wb_gam_leaderboard_cache` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `period` varchar(20) NOT NULL DEFAULT 'all',
  `point_type` varchar(60) NOT NULL DEFAULT 'points',
  `total_points` bigint(20) NOT NULL DEFAULT 0,
  `rank` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_levels`
--

CREATE TABLE `hfpr_wb_gam_levels` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `min_points` bigint(20) UNSIGNED NOT NULL,
  `icon_url` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_member_prefs`
--

CREATE TABLE `hfpr_wb_gam_member_prefs` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `leaderboard_opt_out` tinyint(1) DEFAULT 0,
  `show_rank` tinyint(1) DEFAULT 1,
  `notification_mode` varchar(20) DEFAULT 'smart'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_notifications_queue`
--

CREATE TABLE `hfpr_wb_gam_notifications_queue` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `event_type` varchar(64) NOT NULL,
  `payload_json` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_points`
--

CREATE TABLE `hfpr_wb_gam_points` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` varchar(36) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action_id` varchar(100) NOT NULL,
  `points` int(11) NOT NULL,
  `point_type` varchar(60) NOT NULL DEFAULT 'points',
  `object_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_point_types`
--

CREATE TABLE `hfpr_wb_gam_point_types` (
  `slug` varchar(60) NOT NULL,
  `label` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `position` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_point_type_conversions`
--

CREATE TABLE `hfpr_wb_gam_point_type_conversions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from_type` varchar(60) NOT NULL,
  `to_type` varchar(60) NOT NULL,
  `from_amount` int(10) UNSIGNED NOT NULL,
  `to_amount` int(10) UNSIGNED NOT NULL,
  `min_convert` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `cooldown_seconds` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `max_per_day` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_redemptions`
--

CREATE TABLE `hfpr_wb_gam_redemptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `points_cost` int(10) UNSIGNED NOT NULL,
  `status` varchar(30) DEFAULT 'pending',
  `coupon_code` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_redemption_items`
--

CREATE TABLE `hfpr_wb_gam_redemption_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `points_cost` int(10) UNSIGNED NOT NULL,
  `point_type` varchar(60) NOT NULL DEFAULT 'points',
  `reward_type` varchar(50) NOT NULL,
  `reward_config` longtext DEFAULT NULL,
  `stock` int(10) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_rules`
--

CREATE TABLE `hfpr_wb_gam_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rule_type` varchar(50) NOT NULL,
  `target_id` varchar(100) DEFAULT NULL,
  `rule_config` longtext NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_side_effect_failures`
--

CREATE TABLE `hfpr_wb_gam_side_effect_failures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` varchar(64) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `side_effect` varchar(64) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `event_payload` text NOT NULL,
  `error_message` varchar(500) NOT NULL DEFAULT '',
  `retry_count` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `last_attempt_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_streaks`
--

CREATE TABLE `hfpr_wb_gam_streaks` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `current_streak` int(10) UNSIGNED DEFAULT 0,
  `longest_streak` int(10) UNSIGNED DEFAULT 0,
  `last_active` date DEFAULT NULL,
  `timezone` varchar(50) DEFAULT 'UTC',
  `grace_used` tinyint(1) DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_submissions`
--

CREATE TABLE `hfpr_wb_gam_submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action_id` varchar(60) NOT NULL,
  `evidence` text DEFAULT NULL,
  `evidence_url` varchar(2048) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `reviewer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_user_badges`
--

CREATE TABLE `hfpr_wb_gam_user_badges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `badge_id` varchar(100) NOT NULL,
  `earned_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_user_intelligence`
--

CREATE TABLE `hfpr_wb_gam_user_intelligence` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `engagement_score` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `action_diversity` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `recency_days` smallint(5) UNSIGNED NOT NULL DEFAULT 999,
  `events_30d` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `churn_risk` decimal(4,3) NOT NULL DEFAULT 0.000,
  `anomaly_flag` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `computed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_user_totals`
--

CREATE TABLE `hfpr_wb_gam_user_totals` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `point_type` varchar(60) NOT NULL DEFAULT 'points',
  `total` bigint(20) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wb_gam_webhooks`
--

CREATE TABLE `hfpr_wb_gam_webhooks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `url` varchar(500) NOT NULL,
  `secret` varchar(255) NOT NULL,
  `events` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpfm_backup`
--

CREATE TABLE `hfpr_wpfm_backup` (
  `id` int(11) NOT NULL,
  `backup_name` text DEFAULT NULL,
  `backup_date` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpforms_analytics_forms`
--

CREATE TABLE `hfpr_wpforms_analytics_forms` (
  `form_id` bigint(20) UNSIGNED NOT NULL,
  `period_date` date NOT NULL,
  `views` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `unique_sessions` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `submissions` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpforms_analytics_snapshots`
--

CREATE TABLE `hfpr_wpforms_analytics_snapshots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` bigint(20) UNSIGNED NOT NULL,
  `session_id` varchar(64) NOT NULL,
  `trigger_type` tinyint(3) UNSIGNED NOT NULL,
  `page_number` tinyint(3) UNSIGNED DEFAULT NULL,
  `form_visible` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `payload` longtext NOT NULL,
  `occurred_at` datetime NOT NULL,
  `processed` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpforms_logs`
--

CREATE TABLE `hfpr_wpforms_logs` (
  `id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `types` varchar(255) NOT NULL,
  `create_at` datetime NOT NULL,
  `form_id` bigint(20) DEFAULT NULL,
  `entry_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpforms_payments`
--

CREATE TABLE `hfpr_wpforms_payments` (
  `id` bigint(20) NOT NULL,
  `form_id` bigint(20) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT '',
  `subtotal_amount` decimal(26,8) NOT NULL DEFAULT 0.00000000,
  `discount_amount` decimal(26,8) NOT NULL DEFAULT 0.00000000,
  `total_amount` decimal(26,8) NOT NULL DEFAULT 0.00000000,
  `currency` varchar(3) NOT NULL DEFAULT '',
  `entry_id` bigint(20) NOT NULL DEFAULT 0,
  `gateway` varchar(20) NOT NULL DEFAULT '',
  `type` varchar(12) NOT NULL DEFAULT '',
  `mode` varchar(4) NOT NULL DEFAULT '',
  `transaction_id` varchar(40) NOT NULL DEFAULT '',
  `customer_id` varchar(40) NOT NULL DEFAULT '',
  `subscription_id` varchar(40) NOT NULL DEFAULT '',
  `subscription_status` varchar(10) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `date_created_gmt` datetime NOT NULL,
  `date_updated_gmt` datetime NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpforms_payment_meta`
--

CREATE TABLE `hfpr_wpforms_payment_meta` (
  `id` bigint(20) NOT NULL,
  `payment_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpforms_tasks_meta`
--

CREATE TABLE `hfpr_wpforms_tasks_meta` (
  `id` bigint(20) NOT NULL,
  `action` varchar(255) NOT NULL,
  `data` longtext NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpmailsmtp_debug_events`
--

CREATE TABLE `hfpr_wpmailsmtp_debug_events` (
  `id` int(10) UNSIGNED NOT NULL,
  `content` text DEFAULT NULL,
  `initiator` text DEFAULT NULL,
  `event_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpmailsmtp_tasks_meta`
--

CREATE TABLE `hfpr_wpmailsmtp_tasks_meta` (
  `id` bigint(20) NOT NULL,
  `action` varchar(255) NOT NULL,
  `data` longtext NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpum_fieldmeta`
--

CREATE TABLE `hfpr_wpum_fieldmeta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `wpum_field_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpum_fields`
--

CREATE TABLE `hfpr_wpum_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `field_order` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT 'text',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpum_fieldsgroups`
--

CREATE TABLE `hfpr_wpum_fieldsgroups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_order` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(190) NOT NULL DEFAULT '',
  `description` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpum_registration_formmeta`
--

CREATE TABLE `hfpr_wpum_registration_formmeta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `wpum_registration_form_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpum_registration_forms`
--

CREATE TABLE `hfpr_wpum_registration_forms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpum_search_fields`
--

CREATE TABLE `hfpr_wpum_search_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meta_key` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpum_stripe_invoices`
--

CREATE TABLE `hfpr_wpum_stripe_invoices` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` varchar(255) NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `currency` varchar(20) NOT NULL,
  `gateway_mode` varchar(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wpum_stripe_subscriptions`
--

CREATE TABLE `hfpr_wpum_stripe_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` varchar(255) NOT NULL,
  `plan_id` varchar(255) NOT NULL,
  `subscription_id` varchar(255) NOT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `gateway_mode` varchar(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_wp_phpmyadmin_extension__errors_log`
--

CREATE TABLE `hfpr_wp_phpmyadmin_extension__errors_log` (
  `id` int(50) NOT NULL,
  `gmdate` datetime DEFAULT NULL,
  `function_name` longtext NOT NULL,
  `function_args` longtext NOT NULL,
  `message` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_yoast_expiring_store`
--

CREATE TABLE `hfpr_yoast_expiring_store` (
  `key_name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `exp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_yoast_indexable`
--

CREATE TABLE `hfpr_yoast_indexable` (
  `id` int(11) UNSIGNED NOT NULL,
  `permalink` longtext DEFAULT NULL,
  `permalink_hash` varchar(40) DEFAULT NULL,
  `object_id` bigint(20) DEFAULT NULL,
  `object_type` varchar(32) NOT NULL,
  `object_sub_type` varchar(32) DEFAULT NULL,
  `author_id` bigint(20) DEFAULT NULL,
  `post_parent` bigint(20) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `breadcrumb_title` text DEFAULT NULL,
  `post_status` varchar(20) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT NULL,
  `is_protected` tinyint(1) DEFAULT 0,
  `has_public_posts` tinyint(1) DEFAULT NULL,
  `number_of_pages` int(11) UNSIGNED DEFAULT NULL,
  `canonical` longtext DEFAULT NULL,
  `primary_focus_keyword` varchar(191) DEFAULT NULL,
  `primary_focus_keyword_score` int(3) DEFAULT NULL,
  `readability_score` int(3) DEFAULT NULL,
  `is_cornerstone` tinyint(1) DEFAULT 0,
  `is_robots_noindex` tinyint(1) DEFAULT 0,
  `is_robots_nofollow` tinyint(1) DEFAULT 0,
  `is_robots_noarchive` tinyint(1) DEFAULT 0,
  `is_robots_noimageindex` tinyint(1) DEFAULT 0,
  `is_robots_nosnippet` tinyint(1) DEFAULT 0,
  `twitter_title` text DEFAULT NULL,
  `twitter_image` longtext DEFAULT NULL,
  `twitter_description` longtext DEFAULT NULL,
  `twitter_image_id` varchar(191) DEFAULT NULL,
  `twitter_image_source` text DEFAULT NULL,
  `open_graph_title` text DEFAULT NULL,
  `open_graph_description` longtext DEFAULT NULL,
  `open_graph_image` longtext DEFAULT NULL,
  `open_graph_image_id` varchar(191) DEFAULT NULL,
  `open_graph_image_source` text DEFAULT NULL,
  `open_graph_image_meta` mediumtext DEFAULT NULL,
  `link_count` int(11) DEFAULT NULL,
  `incoming_link_count` int(11) DEFAULT NULL,
  `prominent_words_version` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `blog_id` bigint(20) NOT NULL DEFAULT 1,
  `language` varchar(32) DEFAULT NULL,
  `region` varchar(32) DEFAULT NULL,
  `schema_page_type` varchar(64) DEFAULT NULL,
  `schema_article_type` varchar(64) DEFAULT NULL,
  `has_ancestors` tinyint(1) DEFAULT 0,
  `estimated_reading_time_minutes` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT 1,
  `object_last_modified` datetime DEFAULT NULL,
  `object_published_at` datetime DEFAULT NULL,
  `inclusive_language_score` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_yoast_indexable_hierarchy`
--

CREATE TABLE `hfpr_yoast_indexable_hierarchy` (
  `indexable_id` int(11) UNSIGNED NOT NULL,
  `ancestor_id` int(11) UNSIGNED NOT NULL,
  `depth` int(11) UNSIGNED DEFAULT NULL,
  `blog_id` bigint(20) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_yoast_migrations`
--

CREATE TABLE `hfpr_yoast_migrations` (
  `id` int(11) UNSIGNED NOT NULL,
  `version` varchar(191) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_yoast_primary_term`
--

CREATE TABLE `hfpr_yoast_primary_term` (
  `id` int(11) UNSIGNED NOT NULL,
  `post_id` bigint(20) DEFAULT NULL,
  `term_id` bigint(20) DEFAULT NULL,
  `taxonomy` varchar(32) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `blog_id` bigint(20) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hfpr_yoast_seo_links`
--

CREATE TABLE `hfpr_yoast_seo_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `post_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target_post_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(8) DEFAULT NULL,
  `indexable_id` int(11) UNSIGNED DEFAULT NULL,
  `target_indexable_id` int(11) UNSIGNED DEFAULT NULL,
  `height` int(11) UNSIGNED DEFAULT NULL,
  `width` int(11) UNSIGNED DEFAULT NULL,
  `size` int(11) UNSIGNED DEFAULT NULL,
  `language` varchar(32) DEFAULT NULL,
  `region` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hfpr_actionscheduler_actions`
--
ALTER TABLE `hfpr_actionscheduler_actions`
  ADD PRIMARY KEY (`action_id`),
  ADD KEY `hook_status_scheduled_date_gmt` (`hook`(163),`status`,`scheduled_date_gmt`),
  ADD KEY `status_scheduled_date_gmt` (`status`,`scheduled_date_gmt`),
  ADD KEY `scheduled_date_gmt` (`scheduled_date_gmt`),
  ADD KEY `args` (`args`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `last_attempt_gmt` (`last_attempt_gmt`),
  ADD KEY `claim_id_status_priority_scheduled_date_gmt` (`claim_id`,`status`,`priority`,`scheduled_date_gmt`),
  ADD KEY `status_last_attempt_gmt` (`status`,`last_attempt_gmt`),
  ADD KEY `status_claim_id` (`status`,`claim_id`);

--
-- Indexes for table `hfpr_actionscheduler_claims`
--
ALTER TABLE `hfpr_actionscheduler_claims`
  ADD PRIMARY KEY (`claim_id`),
  ADD KEY `date_created_gmt` (`date_created_gmt`);

--
-- Indexes for table `hfpr_actionscheduler_groups`
--
ALTER TABLE `hfpr_actionscheduler_groups`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `slug` (`slug`(191));

--
-- Indexes for table `hfpr_actionscheduler_logs`
--
ALTER TABLE `hfpr_actionscheduler_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `action_id` (`action_id`),
  ADD KEY `log_date_gmt` (`log_date_gmt`);

--
-- Indexes for table `hfpr_bn_activity_log`
--
ALTER TABLE `hfpr_bn_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_action` (`user_id`,`action`,`created_at`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `hfpr_bn_appeals`
--
ALTER TABLE `hfpr_bn_appeals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_status` (`user_id`,`status`),
  ADD KEY `suspension` (`suspension_id`);

--
-- Indexes for table `hfpr_bn_blocks`
--
ALTER TABLE `hfpr_bn_blocks`
  ADD PRIMARY KEY (`blocker_id`,`blocked_id`),
  ADD KEY `blocked_type` (`blocked_id`,`type`),
  ADD KEY `blocker_type` (`blocker_id`,`type`);

--
-- Indexes for table `hfpr_bn_bookmarks`
--
ALTER TABLE `hfpr_bn_bookmarks`
  ADD PRIMARY KEY (`user_id`,`post_id`),
  ADD KEY `user_recent` (`user_id`,`created_at`);

--
-- Indexes for table `hfpr_bn_comments`
--
ALTER TABLE `hfpr_bn_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thread` (`object_type`,`object_id`,`parent_id`,`created_at`),
  ADD KEY `user` (`user_id`),
  ADD KEY `deleted` (`is_deleted`),
  ADD KEY `sync_reply` (`sync_reply_id`),
  ADD KEY `reply_lookup` (`parent_id`,`is_deleted`),
  ADD KEY `user_recent` (`user_id`,`created_at`);

--
-- Indexes for table `hfpr_bn_connections`
--
ALTER TABLE `hfpr_bn_connections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pair` (`requester_id`,`recipient_id`),
  ADD KEY `recipient_lookup` (`recipient_id`),
  ADD KEY `recipient_status` (`recipient_id`,`status`),
  ADD KEY `requester_status` (`requester_id`,`status`),
  ADD KEY `requester_recent` (`requester_id`,`status`,`created_at`),
  ADD KEY `recipient_recent` (`recipient_id`,`status`,`created_at`);

--
-- Indexes for table `hfpr_bn_email_log`
--
ALTER TABLE `hfpr_bn_email_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_type` (`user_id`,`type`,`digest_date`),
  ADD KEY `type` (`type`),
  ADD KEY `purge_window` (`sent_at`),
  ADD KEY `type_id` (`type`,`id`);

--
-- Indexes for table `hfpr_bn_email_templates`
--
ALTER TABLE `hfpr_bn_email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type` (`type`);

--
-- Indexes for table `hfpr_bn_follows`
--
ALTER TABLE `hfpr_bn_follows`
  ADD PRIMARY KEY (`follower_id`,`following_id`),
  ADD KEY `pending_inbox` (`following_id`,`status`,`created_at`),
  ADD KEY `follower_recent` (`following_id`,`created_at`),
  ADD KEY `follow_created` (`created_at`),
  ADD KEY `following_recent` (`follower_id`,`status`,`created_at`);

--
-- Indexes for table `hfpr_bn_hashtags`
--
ALTER TABLE `hfpr_bn_hashtags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `hfpr_bn_hashtag_follows`
--
ALTER TABLE `hfpr_bn_hashtag_follows`
  ADD PRIMARY KEY (`user_id`,`hashtag_id`),
  ADD KEY `hashtag` (`hashtag_id`);

--
-- Indexes for table `hfpr_bn_invites`
--
ALTER TABLE `hfpr_bn_invites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `email` (`email`),
  ADD KEY `status_expires` (`status`,`expires_at`);

--
-- Indexes for table `hfpr_bn_member_types`
--
ALTER TABLE `hfpr_bn_member_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_slug` (`slug`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `hfpr_bn_member_type_assignments`
--
ALTER TABLE `hfpr_bn_member_type_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_type` (`user_id`,`type_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_type_id` (`type_id`);

--
-- Indexes for table `hfpr_bn_mod_log`
--
ALTER TABLE `hfpr_bn_mod_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actor` (`actor_id`),
  ADD KEY `target_user` (`target_user_id`),
  ADD KEY `created` (`created_at`),
  ADD KEY `space` (`space_id`),
  ADD KEY `object` (`object_type`,`object_id`);

--
-- Indexes for table `hfpr_bn_notifications`
--
ALTER TABLE `hfpr_bn_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bell` (`recipient_id`,`is_read`,`created_at`),
  ADD KEY `recipient_group` (`recipient_id`,`group_key`),
  ADD KEY `purge_window` (`is_read`,`created_at`);

--
-- Indexes for table `hfpr_bn_notification_prefs`
--
ALTER TABLE `hfpr_bn_notification_prefs`
  ADD PRIMARY KEY (`user_id`,`type`),
  ADD KEY `digest_scan` (`email_freq`,`user_id`);

--
-- Indexes for table `hfpr_bn_outbound_webhooks`
--
ALTER TABLE `hfpr_bn_outbound_webhooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_bn_outbound_webhook_log`
--
ALTER TABLE `hfpr_bn_outbound_webhook_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `webhook_event` (`webhook_id`,`event`,`created_at`),
  ADD KEY `status_date` (`status`,`created_at`);

--
-- Indexes for table `hfpr_bn_poll_options`
--
ALTER TABLE `hfpr_bn_poll_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_options` (`post_id`,`display_order`);

--
-- Indexes for table `hfpr_bn_poll_votes`
--
ALTER TABLE `hfpr_bn_poll_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `one_vote_per_user` (`post_id`,`user_id`),
  ADD KEY `option_votes` (`option_id`);

--
-- Indexes for table `hfpr_bn_posts`
--
ALTER TABLE `hfpr_bn_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_feed` (`user_id`,`status`,`created_at`),
  ADD KEY `space_feed` (`space_id`,`status`,`created_at`),
  ADD KEY `announcement_feed` (`is_announcement`,`status`,`created_at`),
  ADD KEY `explore` (`privacy`,`created_at`),
  ADD KEY `active_feed` (`privacy`,`status`,`last_activity_at`),
  ADD KEY `status_scheduled` (`status`,`scheduled_at`),
  ADD KEY `post_created` (`created_at`),
  ADD KEY `shared_post` (`shared_post_id`),
  ADD KEY `link_lookup` (`type`,`link_url`(191));

--
-- Indexes for table `hfpr_bn_post_hashtags`
--
ALTER TABLE `hfpr_bn_post_hashtags`
  ADD PRIMARY KEY (`post_id`,`object_type`,`hashtag_id`),
  ADD KEY `hashtag_feed` (`hashtag_id`,`created_at`),
  ADD KEY `trending_window` (`created_at`);

--
-- Indexes for table `hfpr_bn_presence`
--
ALTER TABLE `hfpr_bn_presence`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `last_active` (`last_active`);

--
-- Indexes for table `hfpr_bn_profile_fields`
--
ALTER TABLE `hfpr_bn_profile_fields`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `field_key` (`field_key`),
  ADD KEY `group_idx` (`group_id`);

--
-- Indexes for table `hfpr_bn_profile_groups`
--
ALTER TABLE `hfpr_bn_profile_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_key` (`group_key`),
  ADD KEY `type_res` (`type_restriction`);

--
-- Indexes for table `hfpr_bn_profile_values`
--
ALTER TABLE `hfpr_bn_profile_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_field_entry` (`user_id`,`field_id`,`entry_index`),
  ADD KEY `field_idx` (`field_id`),
  ADD KEY `user_idx` (`user_id`),
  ADD KEY `field_value` (`field_id`,`value`(20));

--
-- Indexes for table `hfpr_bn_rate_limits`
--
ALTER TABLE `hfpr_bn_rate_limits`
  ADD PRIMARY KEY (`rl_key`),
  ADD KEY `expires_at` (`expires_at`);

--
-- Indexes for table `hfpr_bn_reactions`
--
ALTER TABLE `hfpr_bn_reactions`
  ADD PRIMARY KEY (`user_id`,`object_type`,`object_id`),
  ADD KEY `object_recent` (`object_type`,`object_id`,`created_at`),
  ADD KEY `reaction_created` (`created_at`),
  ADD KEY `user_recent` (`user_id`,`created_at`);

--
-- Indexes for table `hfpr_bn_reports`
--
ALTER TABLE `hfpr_bn_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `one_per_reporter` (`reporter_id`,`object_type`,`object_id`),
  ADD KEY `object_status` (`object_type`,`object_id`,`status`),
  ADD KEY `status_date` (`status`,`created_at`),
  ADD KEY `space` (`space_id`),
  ADD KEY `object_reported` (`object_type`,`object_id`,`created_at`);

--
-- Indexes for table `hfpr_bn_search_index`
--
ALTER TABLE `hfpr_bn_search_index`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `object` (`object_type`,`object_id`),
  ADD KEY `visibility_type` (`visibility`,`object_type`),
  ADD KEY `author` (`author_id`),
  ADD KEY `space` (`space_id`),
  ADD KEY `updated_order` (`updated_at`);
ALTER TABLE `hfpr_bn_search_index` ADD FULLTEXT KEY `ft_search` (`title`,`content`);
ALTER TABLE `hfpr_bn_search_index` ADD FULLTEXT KEY `ft_search_members` (`content_members`);

--
-- Indexes for table `hfpr_bn_shares`
--
ALTER TABLE `hfpr_bn_shares`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_post` (`user_id`,`post_id`),
  ADD KEY `post_shares` (`post_id`),
  ADD KEY `user_recent` (`user_id`,`created_at`);

--
-- Indexes for table `hfpr_bn_spaces`
--
ALTER TABLE `hfpr_bn_spaces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `owner` (`owner_id`),
  ADD KEY `category` (`category_id`),
  ADD KEY `parent` (`parent_id`),
  ADD KEY `is_archived` (`is_archived`),
  ADD KEY `dir_popular` (`parent_id`,`member_count`),
  ADD KEY `dir_name` (`parent_id`,`name`),
  ADD KEY `dir_recent` (`parent_id`,`created_at`),
  ADD KEY `admin_type` (`type`,`created_at`),
  ADD KEY `admin_recent` (`created_at`),
  ADD KEY `admin_active` (`last_active_at`);

--
-- Indexes for table `hfpr_bn_space_bans`
--
ALTER TABLE `hfpr_bn_space_bans`
  ADD PRIMARY KEY (`space_id`,`user_id`),
  ADD KEY `user_bans` (`user_id`);

--
-- Indexes for table `hfpr_bn_space_categories`
--
ALTER TABLE `hfpr_bn_space_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `hfpr_bn_space_members`
--
ALTER TABLE `hfpr_bn_space_members`
  ADD PRIMARY KEY (`space_id`,`user_id`),
  ADD KEY `user_role` (`user_id`,`role`),
  ADD KEY `user_status` (`user_id`,`status`),
  ADD KEY `space_status` (`space_id`,`status`,`joined_at`),
  ADD KEY `pending_all` (`status`,`joined_at`);

--
-- Indexes for table `hfpr_bn_space_meta`
--
ALTER TABLE `hfpr_bn_space_meta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `bn_space_id` (`bn_space_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_bn_user_strikes`
--
ALTER TABLE `hfpr_bn_user_strikes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_active` (`user_id`,`is_reversed`),
  ADD KEY `issued_by` (`issued_by`);

--
-- Indexes for table `hfpr_bn_user_suspensions`
--
ALTER TABLE `hfpr_bn_user_suspensions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_active` (`user_id`,`expires_at`),
  ADD KEY `active_check` (`lifted_at`,`expires_at`,`user_id`),
  ADD KEY `suspended_by` (`suspended_by`);

--
-- Indexes for table `hfpr_bn_verify_tokens`
--
ALTER TABLE `hfpr_bn_verify_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_type` (`user_id`,`type`);

--
-- Indexes for table `hfpr_bn_webhook_log`
--
ALTER TABLE `hfpr_bn_webhook_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action` (`action`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `hfpr_bp_activity`
--
ALTER TABLE `hfpr_bp_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date_recorded` (`date_recorded`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `secondary_item_id` (`secondary_item_id`),
  ADD KEY `component` (`component`),
  ADD KEY `type` (`type`),
  ADD KEY `mptt_left` (`mptt_left`),
  ADD KEY `mptt_right` (`mptt_right`),
  ADD KEY `hide_sitewide` (`hide_sitewide`),
  ADD KEY `is_spam` (`is_spam`);

--
-- Indexes for table `hfpr_bp_activity_meta`
--
ALTER TABLE `hfpr_bp_activity_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_bp_friends`
--
ALTER TABLE `hfpr_bp_friends`
  ADD PRIMARY KEY (`id`),
  ADD KEY `initiator_user_id` (`initiator_user_id`),
  ADD KEY `friend_user_id` (`friend_user_id`);

--
-- Indexes for table `hfpr_bp_groups`
--
ALTER TABLE `hfpr_bp_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `status` (`status`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `hfpr_bp_groups_groupmeta`
--
ALTER TABLE `hfpr_bp_groups_groupmeta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_bp_groups_members`
--
ALTER TABLE `hfpr_bp_groups_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `is_admin` (`is_admin`),
  ADD KEY `is_mod` (`is_mod`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `inviter_id` (`inviter_id`),
  ADD KEY `is_confirmed` (`is_confirmed`);

--
-- Indexes for table `hfpr_bp_invitations`
--
ALTER TABLE `hfpr_bp_invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `inviter_id` (`inviter_id`),
  ADD KEY `invitee_email` (`invitee_email`),
  ADD KEY `class` (`class`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `secondary_item_id` (`secondary_item_id`),
  ADD KEY `type` (`type`),
  ADD KEY `invite_sent` (`invite_sent`),
  ADD KEY `accepted` (`accepted`);

--
-- Indexes for table `hfpr_bp_messages_messages`
--
ALTER TABLE `hfpr_bp_messages_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `thread_id` (`thread_id`);

--
-- Indexes for table `hfpr_bp_messages_meta`
--
ALTER TABLE `hfpr_bp_messages_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_bp_messages_notices`
--
ALTER TABLE `hfpr_bp_messages_notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `hfpr_bp_messages_recipients`
--
ALTER TABLE `hfpr_bp_messages_recipients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `thread_id` (`thread_id`),
  ADD KEY `is_deleted` (`is_deleted`),
  ADD KEY `sender_only` (`sender_only`),
  ADD KEY `unread_count` (`unread_count`);

--
-- Indexes for table `hfpr_bp_notifications`
--
ALTER TABLE `hfpr_bp_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `secondary_item_id` (`secondary_item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_new` (`is_new`),
  ADD KEY `component_name` (`component_name`),
  ADD KEY `component_action` (`component_action`),
  ADD KEY `useritem` (`user_id`,`is_new`);

--
-- Indexes for table `hfpr_bp_notifications_meta`
--
ALTER TABLE `hfpr_bp_notifications_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_id` (`notification_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_bp_optouts`
--
ALTER TABLE `hfpr_bp_optouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `email_type` (`email_type`),
  ADD KEY `date_modified` (`date_modified`);

--
-- Indexes for table `hfpr_bp_user_blogs`
--
ALTER TABLE `hfpr_bp_user_blogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `blog_id` (`blog_id`);

--
-- Indexes for table `hfpr_bp_user_blogs_blogmeta`
--
ALTER TABLE `hfpr_bp_user_blogs_blogmeta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blog_id` (`blog_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_bp_xprofile_data`
--
ALTER TABLE `hfpr_bp_xprofile_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `field_id` (`field_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `hfpr_bp_xprofile_fields`
--
ALTER TABLE `hfpr_bp_xprofile_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `field_order` (`field_order`),
  ADD KEY `can_delete` (`can_delete`),
  ADD KEY `is_required` (`is_required`);

--
-- Indexes for table `hfpr_bp_xprofile_groups`
--
ALTER TABLE `hfpr_bp_xprofile_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `can_delete` (`can_delete`);

--
-- Indexes for table `hfpr_bp_xprofile_meta`
--
ALTER TABLE `hfpr_bp_xprofile_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `object_id` (`object_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_commentmeta`
--
ALTER TABLE `hfpr_commentmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_comments`
--
ALTER TABLE `hfpr_comments`
  ADD PRIMARY KEY (`comment_ID`),
  ADD KEY `comment_post_ID` (`comment_post_ID`),
  ADD KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  ADD KEY `comment_date_gmt` (`comment_date_gmt`),
  ADD KEY `comment_parent` (`comment_parent`),
  ADD KEY `comment_author_email` (`comment_author_email`(10));

--
-- Indexes for table `hfpr_email_log`
--
ALTER TABLE `hfpr_email_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_e_events`
--
ALTER TABLE `hfpr_e_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_at_index` (`created_at`);

--
-- Indexes for table `hfpr_e_notes`
--
ALTER TABLE `hfpr_e_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `route_url_index` (`route_url`(191)),
  ADD KEY `post_id_index` (`post_id`),
  ADD KEY `element_id_index` (`element_id`),
  ADD KEY `parent_id_index` (`parent_id`),
  ADD KEY `author_id_index` (`author_id`),
  ADD KEY `status_index` (`status`),
  ADD KEY `is_resolved_index` (`is_resolved`),
  ADD KEY `is_public_index` (`is_public`),
  ADD KEY `created_at_index` (`created_at`),
  ADD KEY `updated_at_index` (`updated_at`),
  ADD KEY `last_activity_at_index` (`last_activity_at`);

--
-- Indexes for table `hfpr_e_notes_users_relations`
--
ALTER TABLE `hfpr_e_notes_users_relations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_index` (`type`),
  ADD KEY `note_id_index` (`note_id`),
  ADD KEY `user_id_index` (`user_id`);

--
-- Indexes for table `hfpr_e_submissions`
--
ALTER TABLE `hfpr_e_submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hash_id_unique_index` (`hash_id`),
  ADD KEY `main_meta_id_index` (`main_meta_id`),
  ADD KEY `hash_id_index` (`hash_id`),
  ADD KEY `type_index` (`type`),
  ADD KEY `post_id_index` (`post_id`),
  ADD KEY `element_id_index` (`element_id`),
  ADD KEY `campaign_id_index` (`campaign_id`),
  ADD KEY `user_id_index` (`user_id`),
  ADD KEY `user_ip_index` (`user_ip`),
  ADD KEY `status_index` (`status`),
  ADD KEY `is_read_index` (`is_read`),
  ADD KEY `created_at_gmt_index` (`created_at_gmt`),
  ADD KEY `updated_at_gmt_index` (`updated_at_gmt`),
  ADD KEY `created_at_index` (`created_at`),
  ADD KEY `updated_at_index` (`updated_at`),
  ADD KEY `referer_index` (`referer`(191)),
  ADD KEY `referer_title_index` (`referer_title`(191));

--
-- Indexes for table `hfpr_e_submissions_actions_log`
--
ALTER TABLE `hfpr_e_submissions_actions_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id_index` (`submission_id`),
  ADD KEY `action_name_index` (`action_name`),
  ADD KEY `status_index` (`status`),
  ADD KEY `created_at_gmt_index` (`created_at_gmt`),
  ADD KEY `updated_at_gmt_index` (`updated_at_gmt`),
  ADD KEY `created_at_index` (`created_at`),
  ADD KEY `updated_at_index` (`updated_at`);

--
-- Indexes for table `hfpr_e_submissions_values`
--
ALTER TABLE `hfpr_e_submissions_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id_index` (`submission_id`),
  ADD KEY `key_index` (`key`);

--
-- Indexes for table `hfpr_fbv`
--
ALTER TABLE `hfpr_fbv`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `hfpr_fbv_attachment_folder`
--
ALTER TABLE `hfpr_fbv_attachment_folder`
  ADD PRIMARY KEY (`folder_id`,`attachment_id`);

--
-- Indexes for table `hfpr_ff_scheduled_actions`
--
ALTER TABLE `hfpr_ff_scheduled_actions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_fluentform_draft_submissions`
--
ALTER TABLE `hfpr_fluentform_draft_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_form_updated_idx` (`user_id`,`form_id`,`updated_at`),
  ADD KEY `hash_idx` (`hash`(191));

--
-- Indexes for table `hfpr_fluentform_entry_details`
--
ALTER TABLE `hfpr_fluentform_entry_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_fluentform_forms`
--
ALTER TABLE `hfpr_fluentform_forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_fluentform_form_analytics`
--
ALTER TABLE `hfpr_fluentform_form_analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id_ip` (`form_id`,`ip`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `hfpr_fluentform_form_meta`
--
ALTER TABLE `hfpr_fluentform_form_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id_meta_key` (`form_id`,`meta_key`(191)),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_fluentform_logs`
--
ALTER TABLE `hfpr_fluentform_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_fluentform_order_items`
--
ALTER TABLE `hfpr_fluentform_order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_fluentform_submissions`
--
ALTER TABLE `hfpr_fluentform_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id_status` (`form_id`,`status`),
  ADD KEY `form_id_created_at` (`form_id`,`created_at`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `serial_number` (`serial_number`);

--
-- Indexes for table `hfpr_fluentform_submission_meta`
--
ALTER TABLE `hfpr_fluentform_submission_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `response_id_meta_key` (`response_id`,`meta_key`);

--
-- Indexes for table `hfpr_fluentform_subscriptions`
--
ALTER TABLE `hfpr_fluentform_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_fluentform_transactions`
--
ALTER TABLE `hfpr_fluentform_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_fsmpt_email_logs`
--
ALTER TABLE `hfpr_fsmpt_email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_at_status` (`created_at`,`status`);

--
-- Indexes for table `hfpr_jt_access_rules`
--
ALTER TABLE `hfpr_jt_access_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `space_priority` (`space_id`,`priority`);

--
-- Indexes for table `hfpr_jt_activity_log`
--
ALTER TABLE `hfpr_jt_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_created` (`user_id`,`created_at`),
  ADD KEY `created` (`created_at`);

--
-- Indexes for table `hfpr_jt_attachments`
--
ALTER TABLE `hfpr_jt_attachments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `object_attachment` (`object_type`,`object_id`,`attachment_id`),
  ADD KEY `object` (`object_type`,`object_id`,`sort`),
  ADD KEY `attachment` (`attachment_id`);

--
-- Indexes for table `hfpr_jt_blocked_users`
--
ALTER TABLE `hfpr_jt_blocked_users`
  ADD PRIMARY KEY (`blocker_id`,`blocked_id`),
  ADD KEY `blocker_created` (`blocker_id`,`created_at`),
  ADD KEY `blocked_id` (`blocked_id`);

--
-- Indexes for table `hfpr_jt_bookmarks`
--
ALTER TABLE `hfpr_jt_bookmarks`
  ADD PRIMARY KEY (`user_id`,`post_id`),
  ADD KEY `user_created` (`user_id`,`created_at`);

--
-- Indexes for table `hfpr_jt_categories`
--
ALTER TABLE `hfpr_jt_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_sort` (`parent_id`,`sort_order`);

--
-- Indexes for table `hfpr_jt_flags`
--
ALTER TABLE `hfpr_jt_flags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_created` (`status`,`created_at`),
  ADD KEY `object` (`object_type`,`object_id`),
  ADD KEY `reporter` (`reporter_id`);

--
-- Indexes for table `hfpr_jt_invite_links`
--
ALTER TABLE `hfpr_jt_invite_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_space` (`space_id`);

--
-- Indexes for table `hfpr_jt_join_requests`
--
ALTER TABLE `hfpr_jt_join_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `space_user_status` (`space_id`,`user_id`,`status`),
  ADD KEY `space_status_created` (`space_id`,`status`,`created_at`);

--
-- Indexes for table `hfpr_jt_notifications`
--
ALTER TABLE `hfpr_jt_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_read_created` (`user_id`,`is_read`,`created_at`);

--
-- Indexes for table `hfpr_jt_posts`
--
ALTER TABLE `hfpr_jt_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `space_sticky_reply` (`space_id`,`is_sticky`,`last_reply_at`),
  ADD KEY `space_private_reply` (`space_id`,`is_private`,`last_reply_at`),
  ADD KEY `space_votes` (`space_id`,`vote_score`),
  ADD KEY `author_created` (`author_id`,`created_at`),
  ADD KEY `status_created` (`status`,`created_at`),
  ADD KEY `sitemap_status_id` (`status`,`id`);
ALTER TABLE `hfpr_jt_posts` ADD FULLTEXT KEY `ft_title_content` (`title`,`content_plain`);

--
-- Indexes for table `hfpr_jt_post_tags`
--
ALTER TABLE `hfpr_jt_post_tags`
  ADD PRIMARY KEY (`post_id`,`tag_id`),
  ADD KEY `tag_post` (`tag_id`,`post_id`);

--
-- Indexes for table `hfpr_jt_read_status`
--
ALTER TABLE `hfpr_jt_read_status`
  ADD PRIMARY KEY (`user_id`,`post_id`);

--
-- Indexes for table `hfpr_jt_replies`
--
ALTER TABLE `hfpr_jt_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_created` (`post_id`,`created_at`),
  ADD KEY `post_votes` (`post_id`,`vote_score`),
  ADD KEY `author_created` (`author_id`,`created_at`),
  ADD KEY `status_created` (`status`,`created_at`);
ALTER TABLE `hfpr_jt_replies` ADD FULLTEXT KEY `ft_content` (`content_plain`);

--
-- Indexes for table `hfpr_jt_restrictions`
--
ALTER TABLE `hfpr_jt_restrictions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_type_space` (`user_id`,`type`,`space_id`),
  ADD KEY `expires` (`expires_at`);

--
-- Indexes for table `hfpr_jt_revisions`
--
ALTER TABLE `hfpr_jt_revisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `object_created` (`object_type`,`object_id`,`created_at`);

--
-- Indexes for table `hfpr_jt_spaces`
--
ALTER TABLE `hfpr_jt_spaces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_sort` (`category_id`,`sort_order`),
  ADD KEY `parent_sort` (`parent_id`,`sort_order`),
  ADD KEY `visibility_sort` (`visibility`,`sort_order`),
  ADD KEY `sitemap_vis_status_id` (`visibility`,`status`,`id`);

--
-- Indexes for table `hfpr_jt_space_members`
--
ALTER TABLE `hfpr_jt_space_members`
  ADD PRIMARY KEY (`space_id`,`user_id`),
  ADD KEY `user_joined` (`user_id`,`joined_at`),
  ADD KEY `space_role_joined` (`space_id`,`role`,`joined_at`);

--
-- Indexes for table `hfpr_jt_subscriptions`
--
ALTER TABLE `hfpr_jt_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_object` (`user_id`,`object_type`,`object_id`),
  ADD KEY `object_lookup` (`object_type`,`object_id`);

--
-- Indexes for table `hfpr_jt_tags`
--
ALTER TABLE `hfpr_jt_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `hfpr_jt_user_profiles`
--
ALTER TABLE `hfpr_jt_user_profiles`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `trust_reputation` (`trust_level`,`reputation`),
  ADD KEY `trust_user` (`trust_level`,`user_id`);

--
-- Indexes for table `hfpr_jt_votes`
--
ALTER TABLE `hfpr_jt_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_object` (`user_id`,`object_type`,`object_id`),
  ADD KEY `object_created` (`object_type`,`object_id`,`created_at`);

--
-- Indexes for table `hfpr_links`
--
ALTER TABLE `hfpr_links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `link_visible` (`link_visible`);

--
-- Indexes for table `hfpr_mvs_access_grants`
--
ALTER TABLE `hfpr_mvs_access_grants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_user` (`media_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `hfpr_mvs_access_rules`
--
ALTER TABLE `hfpr_mvs_access_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_id` (`media_id`),
  ADD KEY `rule_type` (`rule_type`);

--
-- Indexes for table `hfpr_mvs_activity`
--
ALTER TABLE `hfpr_mvs_activity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_date` (`user_id`,`created_at`),
  ADD KEY `type_date` (`type`,`created_at`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `hfpr_mvs_album_items`
--
ALTER TABLE `hfpr_mvs_album_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `album_media` (`album_id`,`media_id`),
  ADD KEY `album_position` (`album_id`,`position`);

--
-- Indexes for table `hfpr_mvs_blocks`
--
ALTER TABLE `hfpr_mvs_blocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blocker_blocked` (`blocker_id`,`blocked_id`),
  ADD KEY `blocked_id` (`blocked_id`);

--
-- Indexes for table `hfpr_mvs_bp_activity_media`
--
ALTER TABLE `hfpr_mvs_bp_activity_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `media_id` (`media_id`),
  ADD KEY `activity_position` (`activity_id`,`position`),
  ADD KEY `object_lookup` (`object_type`,`activity_id`,`position`);

--
-- Indexes for table `hfpr_mvs_conversations`
--
ALTER TABLE `hfpr_mvs_conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `last_activity` (`last_activity_at`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `container` (`container_type`,`container_id`);

--
-- Indexes for table `hfpr_mvs_conversation_participants`
--
ALTER TABLE `hfpr_mvs_conversation_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `conv_user` (`conversation_id`,`user_id`),
  ADD KEY `user_status` (`user_id`,`status`),
  ADD KEY `conv_read` (`conversation_id`,`last_read_at`),
  ADD KEY `conv_typing` (`conversation_id`,`typing_until`),
  ADD KEY `user_archived` (`user_id`,`is_archived`,`status`);

--
-- Indexes for table `hfpr_mvs_error_log`
--
ALTER TABLE `hfpr_mvs_error_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `level_date` (`level`,`created_at`),
  ADD KEY `context_date` (`context`,`created_at`);

--
-- Indexes for table `hfpr_mvs_favorites`
--
ALTER TABLE `hfpr_mvs_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_user` (`media_id`,`user_id`),
  ADD UNIQUE KEY `unique_favorite` (`media_id`,`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `collection_id` (`collection_id`);

--
-- Indexes for table `hfpr_mvs_follows`
--
ALTER TABLE `hfpr_mvs_follows`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `follower_following` (`follower_id`,`following_id`),
  ADD UNIQUE KEY `unique_follow` (`follower_id`,`following_id`),
  ADD KEY `following_id` (`following_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `hfpr_mvs_media_index`
--
ALTER TABLE `hfpr_mvs_media_index`
  ADD PRIMARY KEY (`media_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `moderation_privacy_date` (`moderation_status`,`privacy`,`created_at`),
  ADD KEY `author_date` (`post_author`,`created_at`),
  ADD KEY `type_date` (`media_type`,`created_at`),
  ADD KEY `status_date` (`status`,`created_at`),
  ADD KEY `album_id` (`album_id`),
  ADD KEY `file_hash` (`file_hash`),
  ADD KEY `rank_scan` (`status`,`moderation_status`,`privacy`,`post_author`,`reaction_count`,`created_at`);
ALTER TABLE `hfpr_mvs_media_index` ADD FULLTEXT KEY `media_search_ft` (`title`,`description`);

--
-- Indexes for table `hfpr_mvs_media_meta`
--
ALTER TABLE `hfpr_mvs_media_meta`
  ADD PRIMARY KEY (`media_id`,`meta_key`),
  ADD KEY `meta_key` (`meta_key`);

--
-- Indexes for table `hfpr_mvs_media_stats`
--
ALTER TABLE `hfpr_mvs_media_stats`
  ADD PRIMARY KEY (`media_id`);

--
-- Indexes for table `hfpr_mvs_media_views`
--
ALTER TABLE `hfpr_mvs_media_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_user_date` (`media_id`,`user_id`,`created_at`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `hfpr_mvs_mentions`
--
ALTER TABLE `hfpr_mvs_mentions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_id` (`media_id`),
  ADD KEY `mentioned_user_id` (`mentioned_user_id`);

--
-- Indexes for table `hfpr_mvs_messages`
--
ALTER TABLE `hfpr_mvs_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conv_date` (`conversation_id`,`created_at`),
  ADD KEY `conv_id` (`conversation_id`),
  ADD KEY `sender` (`sender_id`),
  ADD KEY `conv_updated` (`conversation_id`,`updated_at`);

--
-- Indexes for table `hfpr_mvs_message_reactions`
--
ALTER TABLE `hfpr_mvs_message_reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `msg_user` (`message_id`,`user_id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `hfpr_mvs_notifications`
--
ALTER TABLE `hfpr_mvs_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_unread` (`user_id`,`read_at`),
  ADD KEY `user_date` (`user_id`,`created_at`);

--
-- Indexes for table `hfpr_mvs_reactions`
--
ALTER TABLE `hfpr_mvs_reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_user` (`media_id`,`user_id`),
  ADD UNIQUE KEY `unique_reaction` (`media_id`,`user_id`,`reaction_type`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `hfpr_mvs_reports`
--
ALTER TABLE `hfpr_mvs_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_report` (`reporter_id`,`target_type`,`target_id`),
  ADD KEY `reporter_target` (`reporter_id`,`target_type`,`target_id`),
  ADD KEY `target` (`target_type`,`target_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `hfpr_mvs_transactions`
--
ALTER TABLE `hfpr_mvs_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_type` (`user_id`,`media_type`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `hfpr_options`
--
ALTER TABLE `hfpr_options`
  ADD PRIMARY KEY (`option_id`),
  ADD UNIQUE KEY `option_name` (`option_name`),
  ADD KEY `autoload` (`autoload`);

--
-- Indexes for table `hfpr_postmeta`
--
ALTER TABLE `hfpr_postmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_posts`
--
ALTER TABLE `hfpr_posts`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `post_name` (`post_name`(191)),
  ADD KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  ADD KEY `post_parent` (`post_parent`),
  ADD KEY `post_author` (`post_author`),
  ADD KEY `type_status_author` (`post_type`,`post_status`,`post_author`);

--
-- Indexes for table `hfpr_rts_achievements`
--
ALTER TABLE `hfpr_rts_achievements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `achievement_type` (`achievement_type`);

--
-- Indexes for table `hfpr_rts_action_items`
--
ALTER TABLE `hfpr_rts_action_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rule_key` (`rule_key`);

--
-- Indexes for table `hfpr_rts_activity_logs`
--
ALTER TABLE `hfpr_rts_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tracking_id` (`tracking_id`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `action` (`action`);

--
-- Indexes for table `hfpr_rts_audit_log`
--
ALTER TABLE `hfpr_rts_audit_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `source_record` (`source_table`,`source_id`);

--
-- Indexes for table `hfpr_rts_backups`
--
ALTER TABLE `hfpr_rts_backups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_rts_cabin_credits`
--
ALTER TABLE `hfpr_rts_cabin_credits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `participant_id` (`participant_id`);

--
-- Indexes for table `hfpr_rts_campaigns`
--
ALTER TABLE `hfpr_rts_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `utm_campaign_code` (`utm_campaign_code`);

--
-- Indexes for table `hfpr_rts_campaign_sends`
--
ALTER TABLE `hfpr_rts_campaign_sends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `campaign_participant` (`campaign_id`,`participant_id`);

--
-- Indexes for table `hfpr_rts_content_blocks`
--
ALTER TABLE `hfpr_rts_content_blocks`
  ADD PRIMARY KEY (`block_key`);

--
-- Indexes for table `hfpr_rts_customer_questions`
--
ALTER TABLE `hfpr_rts_customer_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_rts_draws`
--
ALTER TABLE `hfpr_rts_draws`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_rts_duplicate_reviews`
--
ALTER TABLE `hfpr_rts_duplicate_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_id` (`tracking_id`),
  ADD UNIQUE KEY `pair` (`participant_id_a`,`participant_id_b`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `decision` (`decision`);

--
-- Indexes for table `hfpr_rts_email_campaigns`
--
ALTER TABLE `hfpr_rts_email_campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_rts_email_drafts`
--
ALTER TABLE `hfpr_rts_email_drafts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_rts_email_outbox`
--
ALTER TABLE `hfpr_rts_email_outbox`
  ADD PRIMARY KEY (`id`),
  ADD KEY `to_email` (`to_email`);

--
-- Indexes for table `hfpr_rts_email_templates`
--
ALTER TABLE `hfpr_rts_email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_key` (`template_key`),
  ADD UNIQUE KEY `action_key` (`action_key`);

--
-- Indexes for table `hfpr_rts_email_template_versions`
--
ALTER TABLE `hfpr_rts_email_template_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `hfpr_rts_export_history`
--
ALTER TABLE `hfpr_rts_export_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_rts_external_founding_runners`
--
ALTER TABLE `hfpr_rts_external_founding_runners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `hfpr_rts_medals`
--
ALTER TABLE `hfpr_rts_medals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `medal_type` (`medal_type`);

--
-- Indexes for table `hfpr_rts_participants`
--
ALTER TABLE `hfpr_rts_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cabin_credit_number` (`cabin_credit_number`),
  ADD UNIQUE KEY `referral_code` (`referral_code`),
  ADD UNIQUE KEY `certificate_number` (`certificate_number`),
  ADD UNIQUE KEY `founding_runner_number` (`founding_runner_number`),
  ADD UNIQUE KEY `unsubscribe_token` (`unsubscribe_token`),
  ADD KEY `survey_tracking_id` (`survey_tracking_id`),
  ADD KEY `email_verified` (`email_verified`),
  ADD KEY `cabin_credit_status` (`cabin_credit_status`),
  ADD KEY `captain_suite_status` (`captain_suite_status`),
  ADD KEY `merged_into_participant_id` (`merged_into_participant_id`);

--
-- Indexes for table `hfpr_rts_participant_notes`
--
ALTER TABLE `hfpr_rts_participant_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `admin_user_id` (`admin_user_id`);

--
-- Indexes for table `hfpr_rts_participant_survey_links`
--
ALTER TABLE `hfpr_rts_participant_survey_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_id` (`tracking_id`),
  ADD KEY `participant_id` (`participant_id`);

--
-- Indexes for table `hfpr_rts_question_response_drafts`
--
ALTER TABLE `hfpr_rts_question_response_drafts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_question_id` (`customer_question_id`);

--
-- Indexes for table `hfpr_rts_question_response_log`
--
ALTER TABLE `hfpr_rts_question_response_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_rts_races`
--
ALTER TABLE `hfpr_rts_races`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_rts_race_participants`
--
ALTER TABLE `hfpr_rts_race_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `race_id` (`race_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `hfpr_rts_referrals`
--
ALTER TABLE `hfpr_rts_referrals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_referral_per_user` (`referrer_id`,`referred_email`),
  ADD KEY `referrer_id` (`referrer_id`),
  ADD KEY `referred_email` (`referred_email`),
  ADD KEY `referral_code` (`referral_code`),
  ADD KEY `status` (`status`),
  ADD KEY `referring_participant_id` (`referring_participant_id`);

--
-- Indexes for table `hfpr_rts_report_definitions`
--
ALTER TABLE `hfpr_rts_report_definitions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_rts_report_runs`
--
ALTER TABLE `hfpr_rts_report_runs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`);

--
-- Indexes for table `hfpr_rts_segments`
--
ALTER TABLE `hfpr_rts_segments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_rts_sent_emails`
--
ALTER TABLE `hfpr_rts_sent_emails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_rts_subscriptions`
--
ALTER TABLE `hfpr_rts_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `participant_category` (`participant_id`,`category`);

--
-- Indexes for table `hfpr_rts_surveys`
--
ALTER TABLE `hfpr_rts_surveys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `source_form_id` (`source_form_id`);

--
-- Indexes for table `hfpr_rts_survey_analytics`
--
ALTER TABLE `hfpr_rts_survey_analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `hfpr_rts_survey_answers`
--
ALTER TABLE `hfpr_rts_survey_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tracking_id` (`tracking_id`),
  ADD KEY `tracking_submission_id` (`tracking_submission_id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `response_id` (`response_id`),
  ADD KEY `platform_question_id` (`platform_question_id`);

--
-- Indexes for table `hfpr_rts_survey_questions`
--
ALTER TABLE `hfpr_rts_survey_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `source_question` (`source_form_id`,`source_question_id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- Indexes for table `hfpr_rts_survey_responses`
--
ALTER TABLE `hfpr_rts_survey_responses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `source_tracking_id` (`source_tracking_id`),
  ADD UNIQUE KEY `source_submission_id` (`source_submission_id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- Indexes for table `hfpr_rts_survey_tracking`
--
ALTER TABLE `hfpr_rts_survey_tracking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `submission_id` (`submission_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `completion_status` (`completion_status`),
  ADD KEY `location_source` (`location_source`),
  ADD KEY `email` (`email`),
  ADD KEY `is_duplicate` (`is_duplicate`);

--
-- Indexes for table `hfpr_rts_sync_logs`
--
ALTER TABLE `hfpr_rts_sync_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `action` (`action`);

--
-- Indexes for table `hfpr_rts_timeline`
--
ALTER TABLE `hfpr_rts_timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `activity_type` (`activity_type`),
  ADD KEY `activity_date` (`activity_date`);

--
-- Indexes for table `hfpr_rts_trophies`
--
ALTER TABLE `hfpr_rts_trophies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_rts_trophy_unlocks`
--
ALTER TABLE `hfpr_rts_trophy_unlocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `trophy_participant` (`trophy_id`,`participant_id`);

--
-- Indexes for table `hfpr_rts_user_trophies`
--
ALTER TABLE `hfpr_rts_user_trophies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `trophy_type` (`trophy_type`),
  ADD KEY `trophy_key` (`trophy_key`);

--
-- Indexes for table `hfpr_signups`
--
ALTER TABLE `hfpr_signups`
  ADD PRIMARY KEY (`signup_id`),
  ADD KEY `activation_key` (`activation_key`),
  ADD KEY `user_email` (`user_email`),
  ADD KEY `user_login_email` (`user_login`,`user_email`),
  ADD KEY `domain_path` (`domain`(140),`path`(51));

--
-- Indexes for table `hfpr_termmeta`
--
ALTER TABLE `hfpr_termmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `term_id` (`term_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_terms`
--
ALTER TABLE `hfpr_terms`
  ADD PRIMARY KEY (`term_id`),
  ADD KEY `slug` (`slug`(191)),
  ADD KEY `name` (`name`(191));

--
-- Indexes for table `hfpr_term_relationships`
--
ALTER TABLE `hfpr_term_relationships`
  ADD PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  ADD KEY `term_taxonomy_id` (`term_taxonomy_id`);

--
-- Indexes for table `hfpr_term_taxonomy`
--
ALTER TABLE `hfpr_term_taxonomy`
  ADD PRIMARY KEY (`term_taxonomy_id`),
  ADD UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  ADD KEY `taxonomy` (`taxonomy`);

--
-- Indexes for table `hfpr_usermeta`
--
ALTER TABLE `hfpr_usermeta`
  ADD PRIMARY KEY (`umeta_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_users`
--
ALTER TABLE `hfpr_users`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user_login_key` (`user_login`),
  ADD KEY `user_nicename` (`user_nicename`),
  ADD KEY `user_email` (`user_email`);

--
-- Indexes for table `hfpr_wb_gam_api_keys`
--
ALTER TABLE `hfpr_wb_gam_api_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_key_hash` (`key_hash`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `hfpr_wb_gam_badge_defs`
--
ALTER TABLE `hfpr_wb_gam_badge_defs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_wb_gam_challenges`
--
ALTER TABLE `hfpr_wb_gam_challenges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `idx_status_action` (`status`,`action_id`);

--
-- Indexes for table `hfpr_wb_gam_challenge_log`
--
ALTER TABLE `hfpr_wb_gam_challenge_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_challenge` (`user_id`,`challenge_id`),
  ADD KEY `challenge_id` (`challenge_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `hfpr_wb_gam_cohort_members`
--
ALTER TABLE `hfpr_wb_gam_cohort_members`
  ADD PRIMARY KEY (`user_id`,`week`),
  ADD KEY `cohort_id` (`cohort_id`),
  ADD KEY `week` (`week`);

--
-- Indexes for table `hfpr_wb_gam_community_challenges`
--
ALTER TABLE `hfpr_wb_gam_community_challenges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `target_action` (`target_action`);

--
-- Indexes for table `hfpr_wb_gam_community_challenge_contributions`
--
ALTER TABLE `hfpr_wb_gam_community_challenge_contributions`
  ADD PRIMARY KEY (`challenge_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `hfpr_wb_gam_events`
--
ALTER TABLE `hfpr_wb_gam_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_action` (`user_id`,`action_id`),
  ADD KEY `idx_user_created` (`user_id`,`created_at`),
  ADD KEY `idx_user_type_created` (`user_id`,`point_type`,`created_at`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_site_id` (`site_id`);

--
-- Indexes for table `hfpr_wb_gam_kudos`
--
ALTER TABLE `hfpr_wb_gam_kudos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `giver_date` (`giver_id`,`created_at`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `hfpr_wb_gam_leaderboard_cache`
--
ALTER TABLE `hfpr_wb_gam_leaderboard_cache`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_period_type` (`user_id`,`period`,`point_type`),
  ADD KEY `idx_type_period_rank` (`point_type`,`period`,`rank`);

--
-- Indexes for table `hfpr_wb_gam_levels`
--
ALTER TABLE `hfpr_wb_gam_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `min_points` (`min_points`);

--
-- Indexes for table `hfpr_wb_gam_member_prefs`
--
ALTER TABLE `hfpr_wb_gam_member_prefs`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_opt_out` (`leaderboard_opt_out`);

--
-- Indexes for table `hfpr_wb_gam_notifications_queue`
--
ALTER TABLE `hfpr_wb_gam_notifications_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`,`id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `hfpr_wb_gam_points`
--
ALTER TABLE `hfpr_wb_gam_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event` (`event_id`),
  ADD KEY `idx_user_created` (`user_id`,`created_at`),
  ADD KEY `idx_user_action_created` (`user_id`,`action_id`,`created_at`),
  ADD KEY `idx_user_type_created` (`user_id`,`point_type`,`created_at`),
  ADD KEY `idx_action` (`action_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `hfpr_wb_gam_point_types`
--
ALTER TABLE `hfpr_wb_gam_point_types`
  ADD PRIMARY KEY (`slug`),
  ADD KEY `idx_default` (`is_default`);

--
-- Indexes for table `hfpr_wb_gam_point_type_conversions`
--
ALTER TABLE `hfpr_wb_gam_point_type_conversions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pair` (`from_type`,`to_type`),
  ADD KEY `idx_from_active` (`from_type`,`is_active`),
  ADD KEY `idx_to_active` (`to_type`,`is_active`);

--
-- Indexes for table `hfpr_wb_gam_redemptions`
--
ALTER TABLE `hfpr_wb_gam_redemptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `hfpr_wb_gam_redemption_items`
--
ALTER TABLE `hfpr_wb_gam_redemption_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `hfpr_wb_gam_rules`
--
ALTER TABLE `hfpr_wb_gam_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rule_type` (`rule_type`),
  ADD KEY `target_id` (`target_id`);

--
-- Indexes for table `hfpr_wb_gam_side_effect_failures`
--
ALTER TABLE `hfpr_wb_gam_side_effect_failures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_attempt` (`status`,`last_attempt_at`),
  ADD KEY `idx_event_id` (`event_id`);

--
-- Indexes for table `hfpr_wb_gam_streaks`
--
ALTER TABLE `hfpr_wb_gam_streaks`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `hfpr_wb_gam_submissions`
--
ALTER TABLE `hfpr_wb_gam_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_status` (`user_id`,`status`),
  ADD KEY `idx_status_created` (`status`,`created_at`);

--
-- Indexes for table `hfpr_wb_gam_user_badges`
--
ALTER TABLE `hfpr_wb_gam_user_badges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_badge` (`user_id`,`badge_id`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `hfpr_wb_gam_user_intelligence`
--
ALTER TABLE `hfpr_wb_gam_user_intelligence`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_churn_risk` (`churn_risk`),
  ADD KEY `idx_anomaly` (`anomaly_flag`,`computed_at`);

--
-- Indexes for table `hfpr_wb_gam_user_totals`
--
ALTER TABLE `hfpr_wb_gam_user_totals`
  ADD PRIMARY KEY (`user_id`,`point_type`),
  ADD KEY `idx_type_total` (`point_type`,`total`);

--
-- Indexes for table `hfpr_wb_gam_webhooks`
--
ALTER TABLE `hfpr_wb_gam_webhooks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_wpfm_backup`
--
ALTER TABLE `hfpr_wpfm_backup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_wpforms_analytics_forms`
--
ALTER TABLE `hfpr_wpforms_analytics_forms`
  ADD PRIMARY KEY (`form_id`,`period_date`);

--
-- Indexes for table `hfpr_wpforms_analytics_snapshots`
--
ALTER TABLE `hfpr_wpforms_analytics_snapshots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_unprocessed` (`processed`,`occurred_at`),
  ADD KEY `idx_session` (`session_id`,`form_id`,`trigger_type`),
  ADD KEY `idx_form_date` (`form_id`,`processed`,`form_visible`,`occurred_at`);

--
-- Indexes for table `hfpr_wpforms_logs`
--
ALTER TABLE `hfpr_wpforms_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_wpforms_payments`
--
ALTER TABLE `hfpr_wpforms_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`),
  ADD KEY `status` (`status`(8)),
  ADD KEY `total_amount` (`total_amount`),
  ADD KEY `type` (`type`(8)),
  ADD KEY `transaction_id` (`transaction_id`(32)),
  ADD KEY `customer_id` (`customer_id`(32)),
  ADD KEY `subscription_id` (`subscription_id`(32)),
  ADD KEY `subscription_status` (`subscription_status`(8)),
  ADD KEY `title` (`title`(64));

--
-- Indexes for table `hfpr_wpforms_payment_meta`
--
ALTER TABLE `hfpr_wpforms_payment_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `meta_key` (`meta_key`(191)),
  ADD KEY `meta_value` (`meta_value`(191));

--
-- Indexes for table `hfpr_wpforms_tasks_meta`
--
ALTER TABLE `hfpr_wpforms_tasks_meta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_wpmailsmtp_debug_events`
--
ALTER TABLE `hfpr_wpmailsmtp_debug_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_wpmailsmtp_tasks_meta`
--
ALTER TABLE `hfpr_wpmailsmtp_tasks_meta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_wpum_fieldmeta`
--
ALTER TABLE `hfpr_wpum_fieldmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `wpum_field_id` (`wpum_field_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_wpum_fields`
--
ALTER TABLE `hfpr_wpum_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `field_order` (`field_order`);

--
-- Indexes for table `hfpr_wpum_fieldsgroups`
--
ALTER TABLE `hfpr_wpum_fieldsgroups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `group_order` (`group_order`);

--
-- Indexes for table `hfpr_wpum_registration_formmeta`
--
ALTER TABLE `hfpr_wpum_registration_formmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `wpum_registration_form_id` (`wpum_registration_form_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `hfpr_wpum_registration_forms`
--
ALTER TABLE `hfpr_wpum_registration_forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_wpum_search_fields`
--
ALTER TABLE `hfpr_wpum_search_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_wpum_stripe_invoices`
--
ALTER TABLE `hfpr_wpum_stripe_invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_wpum_stripe_subscriptions`
--
ALTER TABLE `hfpr_wpum_stripe_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hfpr_wp_phpmyadmin_extension__errors_log`
--
ALTER TABLE `hfpr_wp_phpmyadmin_extension__errors_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `hfpr_yoast_expiring_store`
--
ALTER TABLE `hfpr_yoast_expiring_store`
  ADD PRIMARY KEY (`key_name`),
  ADD KEY `exp_index` (`exp`);

--
-- Indexes for table `hfpr_yoast_indexable`
--
ALTER TABLE `hfpr_yoast_indexable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `object_type_and_sub_type` (`object_type`,`object_sub_type`),
  ADD KEY `object_id_and_type` (`object_id`,`object_type`),
  ADD KEY `permalink_hash_and_object_type` (`permalink_hash`,`object_type`),
  ADD KEY `subpages` (`post_parent`,`object_type`,`post_status`,`object_id`),
  ADD KEY `prominent_words` (`prominent_words_version`,`object_type`,`object_sub_type`,`post_status`),
  ADD KEY `published_sitemap_index` (`object_published_at`,`is_robots_noindex`,`object_type`,`object_sub_type`);

--
-- Indexes for table `hfpr_yoast_indexable_hierarchy`
--
ALTER TABLE `hfpr_yoast_indexable_hierarchy`
  ADD PRIMARY KEY (`indexable_id`,`ancestor_id`),
  ADD KEY `indexable_id` (`indexable_id`),
  ADD KEY `ancestor_id` (`ancestor_id`),
  ADD KEY `depth` (`depth`);

--
-- Indexes for table `hfpr_yoast_migrations`
--
ALTER TABLE `hfpr_yoast_migrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hfpr_yoast_migrations_version` (`version`);

--
-- Indexes for table `hfpr_yoast_primary_term`
--
ALTER TABLE `hfpr_yoast_primary_term`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_taxonomy` (`post_id`,`taxonomy`),
  ADD KEY `post_term` (`post_id`,`term_id`);

--
-- Indexes for table `hfpr_yoast_seo_links`
--
ALTER TABLE `hfpr_yoast_seo_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `link_direction` (`post_id`,`type`),
  ADD KEY `indexable_link_direction` (`indexable_id`,`type`),
  ADD KEY `url_index` (`url`),
  ADD KEY `target_indexable_id_index` (`target_indexable_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hfpr_actionscheduler_actions`
--
ALTER TABLE `hfpr_actionscheduler_actions`
  MODIFY `action_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_actionscheduler_claims`
--
ALTER TABLE `hfpr_actionscheduler_claims`
  MODIFY `claim_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_actionscheduler_groups`
--
ALTER TABLE `hfpr_actionscheduler_groups`
  MODIFY `group_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_actionscheduler_logs`
--
ALTER TABLE `hfpr_actionscheduler_logs`
  MODIFY `log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_activity_log`
--
ALTER TABLE `hfpr_bn_activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_appeals`
--
ALTER TABLE `hfpr_bn_appeals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_comments`
--
ALTER TABLE `hfpr_bn_comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_connections`
--
ALTER TABLE `hfpr_bn_connections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_email_log`
--
ALTER TABLE `hfpr_bn_email_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_email_templates`
--
ALTER TABLE `hfpr_bn_email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_hashtags`
--
ALTER TABLE `hfpr_bn_hashtags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_invites`
--
ALTER TABLE `hfpr_bn_invites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_member_types`
--
ALTER TABLE `hfpr_bn_member_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_member_type_assignments`
--
ALTER TABLE `hfpr_bn_member_type_assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_mod_log`
--
ALTER TABLE `hfpr_bn_mod_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_notifications`
--
ALTER TABLE `hfpr_bn_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_outbound_webhooks`
--
ALTER TABLE `hfpr_bn_outbound_webhooks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_outbound_webhook_log`
--
ALTER TABLE `hfpr_bn_outbound_webhook_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_poll_options`
--
ALTER TABLE `hfpr_bn_poll_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_poll_votes`
--
ALTER TABLE `hfpr_bn_poll_votes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_posts`
--
ALTER TABLE `hfpr_bn_posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_profile_fields`
--
ALTER TABLE `hfpr_bn_profile_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_profile_groups`
--
ALTER TABLE `hfpr_bn_profile_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_profile_values`
--
ALTER TABLE `hfpr_bn_profile_values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_reports`
--
ALTER TABLE `hfpr_bn_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_search_index`
--
ALTER TABLE `hfpr_bn_search_index`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_shares`
--
ALTER TABLE `hfpr_bn_shares`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_spaces`
--
ALTER TABLE `hfpr_bn_spaces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_space_categories`
--
ALTER TABLE `hfpr_bn_space_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_space_meta`
--
ALTER TABLE `hfpr_bn_space_meta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_user_strikes`
--
ALTER TABLE `hfpr_bn_user_strikes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_user_suspensions`
--
ALTER TABLE `hfpr_bn_user_suspensions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_verify_tokens`
--
ALTER TABLE `hfpr_bn_verify_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bn_webhook_log`
--
ALTER TABLE `hfpr_bn_webhook_log`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_activity`
--
ALTER TABLE `hfpr_bp_activity`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_activity_meta`
--
ALTER TABLE `hfpr_bp_activity_meta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_friends`
--
ALTER TABLE `hfpr_bp_friends`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_groups`
--
ALTER TABLE `hfpr_bp_groups`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_groups_groupmeta`
--
ALTER TABLE `hfpr_bp_groups_groupmeta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_groups_members`
--
ALTER TABLE `hfpr_bp_groups_members`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_invitations`
--
ALTER TABLE `hfpr_bp_invitations`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_messages_messages`
--
ALTER TABLE `hfpr_bp_messages_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_messages_meta`
--
ALTER TABLE `hfpr_bp_messages_meta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_messages_notices`
--
ALTER TABLE `hfpr_bp_messages_notices`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_messages_recipients`
--
ALTER TABLE `hfpr_bp_messages_recipients`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_notifications`
--
ALTER TABLE `hfpr_bp_notifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_notifications_meta`
--
ALTER TABLE `hfpr_bp_notifications_meta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_optouts`
--
ALTER TABLE `hfpr_bp_optouts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_user_blogs`
--
ALTER TABLE `hfpr_bp_user_blogs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_user_blogs_blogmeta`
--
ALTER TABLE `hfpr_bp_user_blogs_blogmeta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_xprofile_data`
--
ALTER TABLE `hfpr_bp_xprofile_data`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_xprofile_fields`
--
ALTER TABLE `hfpr_bp_xprofile_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_xprofile_groups`
--
ALTER TABLE `hfpr_bp_xprofile_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_bp_xprofile_meta`
--
ALTER TABLE `hfpr_bp_xprofile_meta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_commentmeta`
--
ALTER TABLE `hfpr_commentmeta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_comments`
--
ALTER TABLE `hfpr_comments`
  MODIFY `comment_ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_email_log`
--
ALTER TABLE `hfpr_email_log`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_e_events`
--
ALTER TABLE `hfpr_e_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_e_notes`
--
ALTER TABLE `hfpr_e_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_e_notes_users_relations`
--
ALTER TABLE `hfpr_e_notes_users_relations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_e_submissions`
--
ALTER TABLE `hfpr_e_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_e_submissions_actions_log`
--
ALTER TABLE `hfpr_e_submissions_actions_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_e_submissions_values`
--
ALTER TABLE `hfpr_e_submissions_values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fbv`
--
ALTER TABLE `hfpr_fbv`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_ff_scheduled_actions`
--
ALTER TABLE `hfpr_ff_scheduled_actions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fluentform_draft_submissions`
--
ALTER TABLE `hfpr_fluentform_draft_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fluentform_entry_details`
--
ALTER TABLE `hfpr_fluentform_entry_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fluentform_forms`
--
ALTER TABLE `hfpr_fluentform_forms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fluentform_form_analytics`
--
ALTER TABLE `hfpr_fluentform_form_analytics`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fluentform_form_meta`
--
ALTER TABLE `hfpr_fluentform_form_meta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fluentform_logs`
--
ALTER TABLE `hfpr_fluentform_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fluentform_order_items`
--
ALTER TABLE `hfpr_fluentform_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fluentform_submissions`
--
ALTER TABLE `hfpr_fluentform_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fluentform_submission_meta`
--
ALTER TABLE `hfpr_fluentform_submission_meta`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fluentform_subscriptions`
--
ALTER TABLE `hfpr_fluentform_subscriptions`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fluentform_transactions`
--
ALTER TABLE `hfpr_fluentform_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_fsmpt_email_logs`
--
ALTER TABLE `hfpr_fsmpt_email_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_access_rules`
--
ALTER TABLE `hfpr_jt_access_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_activity_log`
--
ALTER TABLE `hfpr_jt_activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_attachments`
--
ALTER TABLE `hfpr_jt_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_categories`
--
ALTER TABLE `hfpr_jt_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_flags`
--
ALTER TABLE `hfpr_jt_flags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_invite_links`
--
ALTER TABLE `hfpr_jt_invite_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_join_requests`
--
ALTER TABLE `hfpr_jt_join_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_notifications`
--
ALTER TABLE `hfpr_jt_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_posts`
--
ALTER TABLE `hfpr_jt_posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_replies`
--
ALTER TABLE `hfpr_jt_replies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_restrictions`
--
ALTER TABLE `hfpr_jt_restrictions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_revisions`
--
ALTER TABLE `hfpr_jt_revisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_spaces`
--
ALTER TABLE `hfpr_jt_spaces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_subscriptions`
--
ALTER TABLE `hfpr_jt_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_tags`
--
ALTER TABLE `hfpr_jt_tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_jt_votes`
--
ALTER TABLE `hfpr_jt_votes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_links`
--
ALTER TABLE `hfpr_links`
  MODIFY `link_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_access_grants`
--
ALTER TABLE `hfpr_mvs_access_grants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_access_rules`
--
ALTER TABLE `hfpr_mvs_access_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_activity`
--
ALTER TABLE `hfpr_mvs_activity`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_album_items`
--
ALTER TABLE `hfpr_mvs_album_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_blocks`
--
ALTER TABLE `hfpr_mvs_blocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_bp_activity_media`
--
ALTER TABLE `hfpr_mvs_bp_activity_media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_conversations`
--
ALTER TABLE `hfpr_mvs_conversations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_conversation_participants`
--
ALTER TABLE `hfpr_mvs_conversation_participants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_error_log`
--
ALTER TABLE `hfpr_mvs_error_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_favorites`
--
ALTER TABLE `hfpr_mvs_favorites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_follows`
--
ALTER TABLE `hfpr_mvs_follows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_media_index`
--
ALTER TABLE `hfpr_mvs_media_index`
  MODIFY `media_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_media_views`
--
ALTER TABLE `hfpr_mvs_media_views`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_mentions`
--
ALTER TABLE `hfpr_mvs_mentions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_messages`
--
ALTER TABLE `hfpr_mvs_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_message_reactions`
--
ALTER TABLE `hfpr_mvs_message_reactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_notifications`
--
ALTER TABLE `hfpr_mvs_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_reactions`
--
ALTER TABLE `hfpr_mvs_reactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_reports`
--
ALTER TABLE `hfpr_mvs_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_mvs_transactions`
--
ALTER TABLE `hfpr_mvs_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_options`
--
ALTER TABLE `hfpr_options`
  MODIFY `option_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_postmeta`
--
ALTER TABLE `hfpr_postmeta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_posts`
--
ALTER TABLE `hfpr_posts`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_achievements`
--
ALTER TABLE `hfpr_rts_achievements`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_action_items`
--
ALTER TABLE `hfpr_rts_action_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_activity_logs`
--
ALTER TABLE `hfpr_rts_activity_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_audit_log`
--
ALTER TABLE `hfpr_rts_audit_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_backups`
--
ALTER TABLE `hfpr_rts_backups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_cabin_credits`
--
ALTER TABLE `hfpr_rts_cabin_credits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_campaigns`
--
ALTER TABLE `hfpr_rts_campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_campaign_sends`
--
ALTER TABLE `hfpr_rts_campaign_sends`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_customer_questions`
--
ALTER TABLE `hfpr_rts_customer_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_draws`
--
ALTER TABLE `hfpr_rts_draws`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_duplicate_reviews`
--
ALTER TABLE `hfpr_rts_duplicate_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_email_campaigns`
--
ALTER TABLE `hfpr_rts_email_campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_email_drafts`
--
ALTER TABLE `hfpr_rts_email_drafts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_email_outbox`
--
ALTER TABLE `hfpr_rts_email_outbox`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_email_templates`
--
ALTER TABLE `hfpr_rts_email_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_email_template_versions`
--
ALTER TABLE `hfpr_rts_email_template_versions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_export_history`
--
ALTER TABLE `hfpr_rts_export_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_external_founding_runners`
--
ALTER TABLE `hfpr_rts_external_founding_runners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_medals`
--
ALTER TABLE `hfpr_rts_medals`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_participants`
--
ALTER TABLE `hfpr_rts_participants`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_participant_notes`
--
ALTER TABLE `hfpr_rts_participant_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_participant_survey_links`
--
ALTER TABLE `hfpr_rts_participant_survey_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_question_response_drafts`
--
ALTER TABLE `hfpr_rts_question_response_drafts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_question_response_log`
--
ALTER TABLE `hfpr_rts_question_response_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_races`
--
ALTER TABLE `hfpr_rts_races`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_race_participants`
--
ALTER TABLE `hfpr_rts_race_participants`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_referrals`
--
ALTER TABLE `hfpr_rts_referrals`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_report_definitions`
--
ALTER TABLE `hfpr_rts_report_definitions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_report_runs`
--
ALTER TABLE `hfpr_rts_report_runs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_segments`
--
ALTER TABLE `hfpr_rts_segments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_sent_emails`
--
ALTER TABLE `hfpr_rts_sent_emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_subscriptions`
--
ALTER TABLE `hfpr_rts_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_surveys`
--
ALTER TABLE `hfpr_rts_surveys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_survey_analytics`
--
ALTER TABLE `hfpr_rts_survey_analytics`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_survey_answers`
--
ALTER TABLE `hfpr_rts_survey_answers`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_survey_questions`
--
ALTER TABLE `hfpr_rts_survey_questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_survey_responses`
--
ALTER TABLE `hfpr_rts_survey_responses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_survey_tracking`
--
ALTER TABLE `hfpr_rts_survey_tracking`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_sync_logs`
--
ALTER TABLE `hfpr_rts_sync_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_timeline`
--
ALTER TABLE `hfpr_rts_timeline`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_trophies`
--
ALTER TABLE `hfpr_rts_trophies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_trophy_unlocks`
--
ALTER TABLE `hfpr_rts_trophy_unlocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_rts_user_trophies`
--
ALTER TABLE `hfpr_rts_user_trophies`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_signups`
--
ALTER TABLE `hfpr_signups`
  MODIFY `signup_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_termmeta`
--
ALTER TABLE `hfpr_termmeta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_terms`
--
ALTER TABLE `hfpr_terms`
  MODIFY `term_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_term_taxonomy`
--
ALTER TABLE `hfpr_term_taxonomy`
  MODIFY `term_taxonomy_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_usermeta`
--
ALTER TABLE `hfpr_usermeta`
  MODIFY `umeta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_users`
--
ALTER TABLE `hfpr_users`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_api_keys`
--
ALTER TABLE `hfpr_wb_gam_api_keys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_challenges`
--
ALTER TABLE `hfpr_wb_gam_challenges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_challenge_log`
--
ALTER TABLE `hfpr_wb_gam_challenge_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_community_challenges`
--
ALTER TABLE `hfpr_wb_gam_community_challenges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_kudos`
--
ALTER TABLE `hfpr_wb_gam_kudos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_leaderboard_cache`
--
ALTER TABLE `hfpr_wb_gam_leaderboard_cache`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_levels`
--
ALTER TABLE `hfpr_wb_gam_levels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_notifications_queue`
--
ALTER TABLE `hfpr_wb_gam_notifications_queue`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_points`
--
ALTER TABLE `hfpr_wb_gam_points`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_point_type_conversions`
--
ALTER TABLE `hfpr_wb_gam_point_type_conversions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_redemptions`
--
ALTER TABLE `hfpr_wb_gam_redemptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_redemption_items`
--
ALTER TABLE `hfpr_wb_gam_redemption_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_rules`
--
ALTER TABLE `hfpr_wb_gam_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_side_effect_failures`
--
ALTER TABLE `hfpr_wb_gam_side_effect_failures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_submissions`
--
ALTER TABLE `hfpr_wb_gam_submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_user_badges`
--
ALTER TABLE `hfpr_wb_gam_user_badges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wb_gam_webhooks`
--
ALTER TABLE `hfpr_wb_gam_webhooks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpfm_backup`
--
ALTER TABLE `hfpr_wpfm_backup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpforms_analytics_snapshots`
--
ALTER TABLE `hfpr_wpforms_analytics_snapshots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpforms_logs`
--
ALTER TABLE `hfpr_wpforms_logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpforms_payments`
--
ALTER TABLE `hfpr_wpforms_payments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpforms_payment_meta`
--
ALTER TABLE `hfpr_wpforms_payment_meta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpforms_tasks_meta`
--
ALTER TABLE `hfpr_wpforms_tasks_meta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpmailsmtp_debug_events`
--
ALTER TABLE `hfpr_wpmailsmtp_debug_events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpmailsmtp_tasks_meta`
--
ALTER TABLE `hfpr_wpmailsmtp_tasks_meta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpum_fieldmeta`
--
ALTER TABLE `hfpr_wpum_fieldmeta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpum_fields`
--
ALTER TABLE `hfpr_wpum_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpum_fieldsgroups`
--
ALTER TABLE `hfpr_wpum_fieldsgroups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpum_registration_formmeta`
--
ALTER TABLE `hfpr_wpum_registration_formmeta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpum_registration_forms`
--
ALTER TABLE `hfpr_wpum_registration_forms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpum_search_fields`
--
ALTER TABLE `hfpr_wpum_search_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpum_stripe_invoices`
--
ALTER TABLE `hfpr_wpum_stripe_invoices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wpum_stripe_subscriptions`
--
ALTER TABLE `hfpr_wpum_stripe_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_wp_phpmyadmin_extension__errors_log`
--
ALTER TABLE `hfpr_wp_phpmyadmin_extension__errors_log`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_yoast_indexable`
--
ALTER TABLE `hfpr_yoast_indexable`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_yoast_migrations`
--
ALTER TABLE `hfpr_yoast_migrations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_yoast_primary_term`
--
ALTER TABLE `hfpr_yoast_primary_term`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hfpr_yoast_seo_links`
--
ALTER TABLE `hfpr_yoast_seo_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
