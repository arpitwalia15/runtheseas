<?php
/**
 * Header bar message dropdown template.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress/header-bar/templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $messages_template;
$menu_link            = trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() );
$unread_message_count = messages_get_unread_count();
$messages_icon        = ( isset( $settings['messages_icon']['value'] ) && '' !== $settings['messages_icon']['value'] ) ? $settings['messages_icon']['value'] : 'wbe-icon-envelope';
?>
<div id="header-messages-dropdown-elem" class="dropdown-passive dropdown-right notification-wrap messages-wrap menu-item-has-children">
	<a href="<?php echo esc_url( $menu_link ); ?>"
		ref="notification_bell"
		class="notification-link">
		<span data-balloon-pos="down" data-balloon="<?php esc_html_e( 'Messages', 'wbcom-essential' ); ?>">
			<i class="<?php echo esc_attr( $messages_icon ); ?>"></i>
			<?php if ( $unread_message_count > 0 ) : ?>
				<span class="count"><?php echo esc_html( $unread_message_count ); ?></span>
			<?php endif; ?>
		</span>
	</a>
	<section class="notification-dropdown">
		<header class="notification-header">
			<h2 class="title"><?php esc_html_e( 'Messages', 'wbcom-essential' ); ?></h2>
		</header>

		<ul class="notification-list">
			<?php

			global $messages_template;

			$recipients       = array();
			$recipient_names  = array();
			$excerpt          = '';
			$last_message_id  = 0;
			$first_message_id = 0;

			if ( bp_has_message_threads( bp_ajax_querystring( 'messages' ) . '&user_id=' . get_current_user_id() ) ) :

				while ( bp_message_threads() ) :
					bp_message_thread();
					$excerpt = '';
					foreach ( array_reverse( $messages_template->thread->messages ) as $message ) {
						if ( '' !== wp_strip_all_tags( $message->message ) ) {
							$messages_template->thread->last_message_content = $message->message;
							$excerpt                                   = wp_strip_all_tags( bp_create_excerpt( $messages_template->thread->last_message_content, 50, array( 'ending' => '&hellip;' ) ) );
							$last_message_id                           = (int) $message->id;
							$messages_template->thread->thread_id      = $message->thread_id;
							$messages_template->thread->last_sender_id = $message->sender_id;
						}
					}
					if ( '' === $excerpt ) {
						$thread_messages = BP_Messages_Thread::get_messages( bp_get_message_thread_id(), null, 99999999 );
						foreach ( $thread_messages as $thread_message ) {
							$excerpt = wp_strip_all_tags( do_shortcode( $thread_message->message ) );
							if ( '' !== $excerpt ) {
								$last_message_id                                 = (int) $thread_message->id;
								$messages_template->thread->last_message_id      = $thread_message->id;
								$messages_template->thread->thread_id            = $thread_message->thread_id;
								$messages_template->thread->last_message_subject = $thread_message->subject;
								$messages_template->thread->last_message_content = $thread_message->message;
								$messages_template->thread->last_sender_id       = $thread_message->sender_id;
								$messages_template->thread->last_message_date    = $thread_message->date_sent;
								$excerpt = wp_strip_all_tags( bp_create_excerpt( $messages_template->thread->last_message_content, 50, array( 'ending' => '&hellip;' ) ) );
								break;
							}
						}
					}

					$group_id = bp_messages_get_meta( $last_message_id, 'group_id', true );
					if ( 0 === $last_message_id && ! $group_id ) {
						$first_message           = BP_Messages_Thread::get_first_message( bp_get_message_thread_id() );
						$group_message_thread_id = bp_messages_get_meta( $first_message->id, 'group_message_thread_id', true ); // group.
						$group_id                = (int) bp_messages_get_meta( $first_message->id, 'group_id', true );
					}

					$group_name                = '';
					$group_avatar              = '';
					$group_link                = '';
					$group_message_users       = '';
					$group_message_type        = '';
					$group_message_thread_type = '';
					$group_message_fresh       = '';

					$is_deleted_group = 0;
					if ( ! empty( $group_id ) ) {
						$group_message_users       = bp_messages_get_meta( $last_message_id, 'group_message_users', true );
						$group_message_type        = bp_messages_get_meta( $last_message_id, 'group_message_type', true );
						$group_message_thread_type = bp_messages_get_meta( $last_message_id, 'group_message_thread_type', true );
						$group_message_fresh       = bp_messages_get_meta( $last_message_id, 'group_message_fresh', true );
						$message_from              = bp_messages_get_meta( $last_message_id, 'message_from', true );

						if ( bp_is_active( 'groups' ) ) {
							$group_name = bp_get_group_name( groups_get_group( $group_id ) );
							if ( empty( $group_name ) ) {
								$group_link = 'javascript:void(0);';
							} else {
								if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) {
									$group_link = bp_get_group_url( groups_get_group( $group_id ) );
								} else {
									$group_link = bp_get_group_permalink( groups_get_group( $group_id ) );
								}
							}
							$group_avatar = bp_core_fetch_avatar(
								array(
									'item_id'    => $group_id,
									'object'     => 'group',
									'type'       => 'full',
									'avatar_dir' => 'group-avatars',
									/* translators: %s: Group Name */
									'alt'        => sprintf( __( 'Group logo of %s', 'wbcom-essential' ), $group_name ),
									'title'      => $group_name,
									'html'       => false,
								)
							);
						} else {

							$prefix                   = apply_filters( 'bp_core_get_table_prefix', $wpdb->base_prefix );
							$groups_table             = $prefix . 'bp_groups';
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter -- Table name is safe (prefix + constant), value is prepared with %d.
						$group_name               = $wpdb->get_var( $wpdb->prepare( "SELECT `name` FROM `{$groups_table}` WHERE `id` = %d", absint( $group_id ) ) ); // db call ok; no-cache ok;.
							$group_link               = 'javascript:void(0);';
							$group_avatar             = buddypress()->plugin_url . 'bp-core/images/mystery-group.png';
							$legacy_group_avatar_name = '-groupavatar-full';
							$legacy_user_avatar_name  = '-avatar2';

							if ( ! empty( $group_name ) ) {
								$group_link        = 'javascript:void(0);';
								$directory         = 'group-avatars';
								$avatar_size       = '-bpfull';
								$avatar_folder_dir = bp_core_avatar_upload_path() . '/' . $directory . '/' . $group_id;
								$avatar_folder_url = bp_core_avatar_url() . '/' . $directory . '/' . $group_id;

								if ( file_exists( $avatar_folder_dir ) ) {

									$group_avatar = '';

									// Open directory.
									if ( $av_dir = opendir( $avatar_folder_dir ) ) {

										// Stash files in an array once to check for one that matches.
										$avatar_files = array();
										while ( false !== ( $avatar_file = readdir( $av_dir ) ) ) {
											// Only add files to the array (skip directories).
											if ( 2 < strlen( $avatar_file ) ) {
												$avatar_files[] = $avatar_file;
											}
										}

										// Check for array.
										if ( 0 < count( $avatar_files ) ) {

											// Check for current avatar.
											foreach ( $avatar_files as $key => $value ) {
												if ( strpos( $value, $avatar_size ) !== false ) {
													$group_avatar = $avatar_folder_url . '/' . $avatar_files[ $key ];
												}
											}

											// Legacy avatar check.
											if ( ! isset( $group_avatar ) ) {
												foreach ( $avatar_files as $key => $value ) {
													if ( strpos( $value, $legacy_user_avatar_name ) !== false ) {
														$group_avatar = $avatar_folder_url . '/' . $avatar_files[ $key ];
													}
												}

												// Legacy group avatar check.
												if ( ! isset( $group_avatar ) ) {
													foreach ( $avatar_files as $key => $value ) {
														if ( strpos( $value, $legacy_group_avatar_name ) !== false ) {
															$group_avatar = $avatar_folder_url . '/' . $avatar_files[ $key ];
														}
													}
												}
											}
										}
									}
									// Close the avatar directory.
									closedir( $av_dir );
								}
							}
						}

						$is_deleted_group = ( empty( $group_name ) ) ? 1 : 0;
						$group_name       = ( empty( $group_name ) ) ? __( 'Deleted Group', 'wbcom-essential' ) : $group_name;

					}

					$is_group_thread = 0;
					if ( (int) $group_id > 0 ) {

						$first_message           = BP_Messages_Thread::get_first_message( bp_get_message_thread_id() );
						$group_message_thread_id = bp_messages_get_meta( $first_message->id, 'group_message_thread_id', true ); // group.
						$group_id                = (int) bp_messages_get_meta( $first_message->id, 'group_id', true );
						$message_users           = bp_messages_get_meta( $first_message->id, 'group_message_users', true ); // all - individual.
						$message_type            = bp_messages_get_meta( $first_message->id, 'group_message_type', true ); // open - private.
						$message_from            = bp_messages_get_meta( $first_message->id, 'message_from', true ); // group.

						if ( 'group' === $message_from && bp_get_message_thread_id() === (int) $group_message_thread_id && 'all' === $message_users && 'open' === $message_type ) {
							$is_group_thread = 1;
						}
					}

					$recipients       = array();
					$other_recipients = array();
					$currentuser      = false;
					if ( is_array( $messages_template->thread->recipients ) ) {
						foreach ( $messages_template->thread->recipients as $recipient ) {
							if ( empty( $recipient->is_deleted ) ) {
								$is_you         = bp_loggedin_user_id() === $recipient->user_id;
								$recipient_data = array(
									'avatar'    => esc_url(
										bp_core_fetch_avatar(
											array(
												'item_id' => $recipient->user_id,
												'object'  => 'user',
												'type'    => 'thumb',
												'width'   => BP_AVATAR_THUMB_WIDTH,
												'height'  => BP_AVATAR_THUMB_HEIGHT,
												'html'    => false,
											)
										)
									),
									'user_link' => bp_core_get_userlink( $recipient->user_id, false, true ),
									'user_name' => bp_core_get_user_displayname( $recipient->user_id ),
									'is_you'    => $is_you,
								);
								$recipients[]   = $recipient_data;

								if ( ! $is_you ) {
									$other_recipients[] = $recipient_data;
								} else {
									$currentuser = ( ! isset( $_REQUEST['customize_theme'] ) || ! sanitize_text_field( wp_unslash( $_REQUEST['customize_theme'] ) ) ) ? $recipient_data : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Used for display context only
								}
							}
						}
					}
					$include_you = count( $other_recipients ) >= 2;
					$first_three = array_slice( $other_recipients, 0, 3 );
					if ( count( $first_three ) === 0 ) {
						$include_you = true;
					}
					?>

					<li class="read-item <?php echo bp_message_thread_has_unread() ? 'unread' : ''; ?>">
						<span class="wbcom-essential--full-link">
							<a href="<?php echo esc_url( bp_message_thread_view_link( bp_get_message_thread_id() ) ); ?>">
								<?php bp_message_thread_subject(); ?>
							</a>
						</span>

						<?php
						if ( function_exists( 'bp_messages_get_avatars' ) && ! empty( bp_messages_get_avatars( bp_get_message_thread_id(), get_current_user_id() ) ) ) {
							$avatars = bp_messages_get_avatars( bp_get_message_thread_id(), get_current_user_id() );
							?>
							<div class="notification-avatar">
								<a href="<?php  echo esc_url( bp_message_thread_view_link( bp_get_message_thread_id() ) ); ?>">
									<?php
									if ( count( $avatars ) > 1 ) {
										echo '<div class="thread-multiple-avatar">';
									}
									foreach ( $avatars as $avatar ) {
										echo '<img src="' . esc_url( $avatar['url'] ) . '" alt="' . esc_attr( $avatar['name'] ) . '" />';
									}
									if ( count( $avatars ) > 1 ) {
										echo '</div>';
									}
									?>
								</a>
							</div>
							<?php
						} elseif ( $is_group_thread ) {
							?>
							<div class="notification-avatar">
								<a href="<?php  echo esc_url( bp_message_thread_view_link( bp_get_message_thread_id() ) ); ?>">
									<img src="<?php echo esc_url( $group_avatar ); ?>" alt=""> </a>
							</div>
							<?php
						} else {
							?>
							<div class="notification-avatar">
								<?php
								if ( count( $other_recipients ) > 1 ) {
									?>
									<?php if ( function_exists( 'buddypress' ) && version_compare( buddypress()->version, '12.0', '>=' ) ) : ?>
										<a href="<?php echo esc_url( bp_members_get_user_url( $messages_template->thread->last_sender_id ) ); ?>">
											<?php bp_message_thread_avatar(); ?>
										</a>
									<?php else : ?>
										<a href="<?php echo esc_url( bp_core_get_user_domain( $messages_template->thread->last_sender_id ) ); ?>">
											<?php bp_message_thread_avatar(); ?>
										</a>
									<?php endif; ?>
									<?php
								} else {
									$recipient = ! empty( $first_three[0] ) ? $first_three[0] : $currentuser;
									?>
									<a href="<?php echo esc_url( $recipient['user_link'] ); ?>">
										<img class="avatar" src="<?php echo esc_url( $recipient['avatar'] ); ?>" alt="<?php echo esc_attr( $recipient['user_name'] ); ?>" />
									</a>
									<?php
								}
								?>
							</div>
							<?php
						}
						?>
						<div class="notification-content">
							<span class="wbcom-essential--full-link">
								<a href="<?php  echo esc_url( bp_message_thread_view_link( bp_get_message_thread_id() ) ); ?>">
									<?php bp_message_thread_subject(); ?>
								</a>
							</span>

							<?php
							if ( $is_group_thread ) {
								?>
								<span class="notification-users">
									<a href="<?php  echo esc_url( bp_message_thread_view_link( bp_get_message_thread_id() ) ); ?>">
										<?php
										echo esc_html( ucwords( $group_name ) );
										?>
									</a>
								</span>
								<?php
							} else {
								?>
								<span class="notification-users">
									<a href="<?php  echo esc_url( bp_message_thread_view_link( bp_get_message_thread_id() ) ); ?>">
										<?php
										$recipients      = (array) $messages_template->thread->recipients;
										$recipient_names = array();

										foreach ( $recipients as $recipient ) :
											if ( bp_loggedin_user_id() !== (int) $recipient->user_id ) :
												$recipient_name = bp_core_get_user_displayname( $recipient->user_id );

												if ( empty( $recipient_name ) ) :
													$recipient_name = __( 'Deleted User', 'wbcom-essential' );
												endif;

												if ( bp_is_active( 'moderation' ) ) {
													if ( bp_moderation_is_user_suspended( $recipient->user_id ) ) {
														$recipient_name = __( 'Suspended Member', 'wbcom-essential' );
													} elseif ( bp_moderation_is_user_blocked( $recipient->user_id ) ) {
														$recipient_name = __( 'Blocked Member', 'wbcom-essential' );
													}
												}

												$recipient_names[] = ( $recipient_name ) ? ucwords( $recipient_name ) : '';
											endif;
										endforeach;

										echo esc_html( ! empty( $recipient_names ) ? implode( ', ', $recipient_names ) : '' );
										?>
									</a>
								</span>
								<?php
							}
							?>
							<span class="posted">
								<?php
								if ( bp_is_active( 'moderation' ) ) {
									$is_last_sender_suspended = bp_moderation_is_user_suspended( $messages_template->thread->last_sender_id );
									$is_last_sender_blocked   = bp_moderation_is_user_blocked( $messages_template->thread->last_sender_id );
								}

								$exerpt = wp_strip_all_tags( bp_create_excerpt( $messages_template->thread->last_message_content, 50, array( 'ending' => '&hellip;' ) ) );

								if ( function_exists( 'buddypress' ) && bp_is_active( 'media' ) ) :
									if ( function_exists( 'bp_is_messages_media_support_enabled' ) && bp_is_messages_media_support_enabled() ) :
										$media_ids = bp_messages_get_meta( $last_message_id, 'bp_media_ids', true );

										if ( ! empty( $media_ids ) ) :
											$media_ids = explode( ',', $media_ids );

											if ( count( $media_ids ) < 2 ) :
												$exerpt = __( 'sent a photo', 'wbcom-essential' );
											else :
												$exerpt = __( 'sent some photos', 'wbcom-essential' );
											endif;
										endif;
									endif;

									if ( function_exists( 'bp_is_messages_video_support_enabled' ) && bp_is_messages_video_support_enabled() ) :
										$video_ids = bp_messages_get_meta( $last_message_id, 'bp_video_ids', true );

										if ( ! empty( $video_ids ) ) :
											$video_ids = explode( ',', $video_ids );

											if ( count( $video_ids ) < 2 ) :
												$exerpt = __( 'sent a video', 'wbcom-essential' );
											else :
												$exerpt = __( 'sent some videos', 'wbcom-essential' );
											endif;
										endif;
									endif;

									if ( function_exists( 'bp_is_messages_document_support_enabled' ) && bp_is_messages_document_support_enabled() ) :
										$document_ids = bp_messages_get_meta( $last_message_id, 'bp_document_ids', true );

										if ( ! empty( $document_ids ) ) :
											$document_ids = explode( ',', $document_ids );

											if ( count( $document_ids ) < 2 ) :
												$exerpt = __( 'sent a document', 'wbcom-essential' );
											else :
												$exerpt = __( 'sent some documents', 'wbcom-essential' );
											endif;
										endif;
									endif;

									if ( function_exists( 'bp_is_messages_gif_support_enabled' ) && bp_is_messages_gif_support_enabled() ) :
										$gif_data = bp_messages_get_meta( $last_message_id, '_gif_data', true );

										if ( ! empty( $gif_data ) ) :
											$exerpt = __( 'sent a gif', 'wbcom-essential' );
										endif;
									endif;
								endif;

								if ( bp_is_active( 'moderation' ) ) {
									if ( $is_last_sender_suspended ) {
										$exerpt = __( 'This content has been hidden as the member is suspended.', 'wbcom-essential' );
									} elseif ( $is_last_sender_blocked ) {
										$exerpt = __( 'This content has been hidden as you have blocked this member.', 'wbcom-essential' );
									}
								}

								if ( bp_loggedin_user_id() === $messages_template->thread->last_sender_id ) {
									echo esc_html__( 'You', 'wbcom-essential' ) . ': ' . esc_html( stripslashes_deep( $exerpt ) );
									// } else if ( 1 === count( $recipient_names) ) {
									// echo stripslashes_deep( $exerpt );
								} else {
									$last_sender = bp_core_get_user_displayname( $messages_template->thread->last_sender_id );
									if ( bp_is_active( 'moderation' ) ) {
										if ( $is_last_sender_suspended ) {
											$last_sender = __( 'Suspended Member', 'wbcom-essential' );
										} elseif ( $is_last_sender_blocked ) {
											$last_sender = __( 'Blocked Member', 'wbcom-essential' );
										}
									}
									if ( $last_sender ) {
										echo esc_html( $last_sender ) . ': ' . esc_html( stripslashes_deep( $exerpt ) );
									}
								}
								?>
							</span>
						</div>
					</li>
					<?php
				endwhile;

			else :
				?>
				<li class="bs-item-wrap">
					<div class="notification-content"><?php esc_html_e( 'No new messages!', 'wbcom-essential' ); ?></div>
				</li>
				<?php
			endif;
			?>

		</ul>

		<footer class="notification-footer">
			<a href="<?php echo esc_url( $menu_link ); ?>" class="delete-all">
				<?php esc_html_e( 'View Inbox', 'wbcom-essential' ); ?>
				<i class="wbcom-essential-icon-angle-right"></i>
			</a>
		</footer>
	</section>
</div>
