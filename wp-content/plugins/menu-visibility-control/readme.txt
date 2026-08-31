=== Menu Visibility Control ===
Contributors: davisw3
Donate link: https://knowledge.buzz/donate
Tags: menu, visibility, roles, navigation, conditional
Requires at least: 5.8
Tested up to: 6.9
Stable tag: 1.0.9
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Control WordPress menu item visibility based on login status, user roles, device type, or specific pages — lightweight and theme-agnostic.

== Description ==

**Menu Visibility Control** is a lightweight, privacy-friendly WordPress plugin that lets you decide exactly **who sees each menu item**, directly inside the WordPress menu editor.

No settings pages.  
No lock-in.  
No performance overhead.

Everything is managed where it belongs: **Appearance → Menus**.

### 👁️ Visibility Options Per Menu Item

You can control visibility based on:

* 👥 Everyone
* 🔒 Logged-in users
* 🚪 Logged-out users
* 🧩 Specific user roles (Administrator, Editor, Subscriber, etc.)
* 📱 Device type (Desktop / Tablet / Mobile)
* 📄 Specific pages (auto-detected list)

All conditions are optional and safely combined.

### 💡 Perfect For

* Membership and community websites
* Client dashboards and intranets
* Multi-role WordPress sites
* Sites with mobile-specific navigation
* Blogs that need different menus for visitors vs members

### 🔧 Key Features

* Native integration with **Appearance → Menus**
* Works with **any theme or page builder**
* Role-based menu visibility
* Device-based menu visibility
* Page-specific menu visibility
* Auto-hidden UI (only shows options when enabled)
* Secure (nonces, sanitization, strict validation)
* Performance-optimized (runs only during menu rendering)
* 100% free, open-source, and donation-supported

### 🧠 Why Use Menu Visibility Control?

Unlike large menu or membership plugins, this plugin:

* Uses **only WordPress core hooks**
* Stores **minimal metadata**
* Is compatible with caching, multilingual sites, and block themes
* Does not track users or collect data

It does one thing — and does it well.

---

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/menu-visibility-control/`, or install it from the WordPress Plugin Directory.
2. Activate the plugin via **Plugins → Installed Plugins**.
3. Go to **Appearance → Menus**.
4. Expand a menu item and choose its **Visibility** options.

No configuration required.

---

== Frequently Asked Questions ==

= Where are the plugin settings? =
There is no global settings page. All options appear directly within each menu item in **Appearance → Menus**.

= Can I hide menu items by user role? =
Yes. Select **User Roles** and choose the roles that should see the menu item.

= Can I show or hide menu items by device? =
Yes. You can restrict menu items to Desktop, Tablet, or Mobile devices.

= Can I show menu items only on certain pages? =
Yes. You can select specific pages where a menu item should appear.

= Will existing menus break after updating? =
No. All existing settings remain untouched. New features are opt-in only.

= Does this work with all themes and builders? =
Yes. Any theme or builder using `wp_nav_menu()` is fully supported.

= Is the plugin translation-ready? =
Yes. The text domain is `menu-visibility-control`.

---

== Screenshots ==

1. Visibility controls inside the WordPress menu editor.
2. Role selection checkboxes.
3. Device visibility options.
4. Page-specific visibility selector.

---

== Changelog ==
= 1.0.9 =
* Improved admin notice design on the Appearance → Menus screen.
* Added informative admin notice with documentation, review, and support links.
* Enhanced notice UX with dismiss handling and once-per-day visibility.
* Minor UI polish and internal code cleanup.

= 1.0.8 =
* Added device-based menu visibility (desktop, tablet, mobile).
* Added page-specific visibility with automatic page selector.
* Improved menu editor UI with auto-hidden options.
* Performance optimizations.
* No changes to existing user settings.

= 1.0.4 =
* Security hardening and nonce validation.
* Confirmed compatibility with WordPress 6.9.

= 1.0.3 =
* Performance improvements and internal cleanup.

= 1.0.2 =
* Added role-based menu visibility.

= 1.0.1 =
* Initial public release.

---

== Upgrade Notice ==

= 1.0.8 =
Safe update. New visibility options added. Existing menus are not affected.

---

== Support ==

Need help or want to share feedback?

* Visit the [support forum](https://wordpress.org/support/plugin/menu-visibility-control/)
* Leave a [review](https://wordpress.org/support/plugin/menu-visibility-control/reviews/#new-post)
* Support development via [donation](https://knowledge.buzz/donate)

---

== License ==

This plugin is licensed under the **GPL v2 or later**.

You are free to use, modify, and redistribute it under the same license.

Code is Poetry. ❤️
