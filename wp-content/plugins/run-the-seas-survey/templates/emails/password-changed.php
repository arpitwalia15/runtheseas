<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html__('Run The Seas — Passcode Changed Confirmation', 'run-the-seas'); ?></title>
</head>
<body style="margin:0;padding:0;background-color:#070D16;font-family:Georgia,'Times New Roman',serif;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">
        <?php echo esc_html__('Your Captain’s Suite passcode was just changed. Here’s confirmation, and what to do if this wasn’t you.', 'run-the-seas'); ?>
    </div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#070D16;padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:linear-gradient(180deg,#0E1A2B 0%,#0B1420 100%);border:1px solid #C9A24B;border-radius:6px;box-shadow:0 20px 60px rgba(0,0,0,0.6);">
                    <tr>
                        <td style="padding:2px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid rgba(201,162,75,0.35);border-radius:5px;">
                                <tr>
                                    <td align="center" style="padding:44px 30px 20px;">
                                        <?php if (!empty($logo_url)) : ?>
                                            <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr__('Run The Seas Logo', 'run-the-seas'); ?>" width="220" style="display:block;margin:0 auto 15px;width:220px;height:auto;max-width:100%;background:transparent;border:none;outline:none;object-fit:contain;">
                                        <?php endif; ?>
                                        <div style="margin-top:20px;width:60px;border-top:1px solid #C9A24B;"></div>
                                        <div style="margin-top:16px;font-size:13px;letter-spacing:3px;color:#C9A24B;text-transform:uppercase;">Captain&rsquo;s Suite &middot; Private Access</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:6px 40px 0;">
                                        <table role="presentation" cellpadding="0" cellspacing="0"><tr><td align="center" style="width:52px;height:52px;border:2px solid #7FBF6A;border-radius:50%;"><div style="font-size:22px;line-height:50px;color:#7FBF6A;">&#10003;</div></td></tr></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:16px 40px 0;"><div style="font-size:22px;color:#F3E6C8;letter-spacing:1px;">Passcode Successfully Changed</div></td>
                                </tr>
                                <tr>
                                    <td style="padding:22px 46px 6px;text-align:center;">
                                        <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#C9D3E0;">Ahoy, Captain <strong style="color:#F3E6C8;"><?php echo esc_html($first_name); ?></strong>,</p>
                                        <p style="margin:0 0 16px;font-size:15px;line-height:1.7;color:#C9D3E0;">This confirms the passcode for your Captain&rsquo;s Suite was changed successfully. You&rsquo;re all set to sail back in.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:8px 46px 0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid rgba(201,162,75,0.3);border-radius:4px;background:rgba(201,162,75,0.06);">
                                            <tr>
                                                <td style="padding:16px 20px;">
                                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                        <tr><td style="font-size:12px;letter-spacing:1.5px;color:#8FA3BE;text-transform:uppercase;padding-bottom:6px;">Date &amp; Time</td><td align="right" style="font-size:13px;color:#F3E6C8;padding-bottom:6px;"><?php echo esc_html($change_datetime); ?></td></tr>
                                                        <tr><td style="font-size:12px;letter-spacing:1.5px;color:#8FA3BE;text-transform:uppercase;padding-bottom:6px;">Device</td><td align="right" style="font-size:13px;color:#F3E6C8;padding-bottom:6px;"><?php echo esc_html($device_info); ?></td></tr>
                                                        <tr><td style="font-size:12px;letter-spacing:1.5px;color:#8FA3BE;text-transform:uppercase;">Location</td><td align="right" style="font-size:13px;color:#F3E6C8;"><?php echo esc_html($approx_location); ?></td></tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:26px 40px 8px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0"><tr><td align="center" style="border-radius:4px;background:linear-gradient(180deg,#E4C77A 0%,#C9A24B 100%);"><a href="<?php echo esc_url($login_link); ?>" target="_blank" style="display:inline-block;padding:15px 42px;font-family:Georgia,serif;font-size:14px;letter-spacing:2px;text-transform:uppercase;color:#1A1204;text-decoration:none;font-weight:bold;">&#10022;&nbsp; Return to Captain&rsquo;s Suite</a></td></tr></table>
                                    </td>
                                </tr>
                                <tr><td align="center" style="padding:30px 40px 0;"><div style="width:100%;border-top:1px solid rgba(201,162,75,0.25);"></div></td></tr>
                                <tr>
                                    <td align="center" style="padding:20px 46px 4px;">
                                        <p style="margin:0;font-size:12.5px;line-height:1.7;color:#6E829C;"><strong style="color:#C9A24B;">Wasn&rsquo;t you?</strong> If you didn&rsquo;t make this change, your account may be compromised. Contact us immediately at <a href="mailto:<?php echo esc_attr($support_email); ?>" style="color:#C9A24B;"><?php echo esc_html($support_email); ?></a> and we&rsquo;ll help you secure your Suite.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:34px 30px 40px;">
                                        <div style="font-size:20px;color:#C9A24B;margin-bottom:2px;">&#9875;</div>
                                        <div style="margin-top:8px;font-size:12px;letter-spacing:2.5px;color:#E4C77A;text-transform:uppercase;">Run. Explore. Celebrate. Belong.</div>
                                        <div style="margin-top:10px;font-size:13px;font-style:italic;color:#8FA3BE;line-height:1.6;">More than a race.<br>It&rsquo;s the adventure of a lifetime.</div>
                                        <div style="margin-top:22px;font-size:11px;color:#4E5F76;">Run The Seas&reg; &middot; <a href="<?php echo esc_url($site_url); ?>" style="color:#4E5F76;">runtheseas.com</a></div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
