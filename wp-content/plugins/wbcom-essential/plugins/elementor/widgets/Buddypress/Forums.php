<?php
/**
 * Elementor widget forums.
 *
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress
 */

namespace WBCOM_ESSENTIAL\ELEMENTOR\Widgets\Buddypress;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use WBCOM_ESSENTIAL\ELEMENTOR\Plugin;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

/**
 * Elementor widget forum.
 *
 * @since      1.0.0
 *
 * @package    Wbcom_Essential
 * @subpackage Wbcom_Essential/plugins/elementor/widget/buddypress
 */
class Forums extends \Elementor\Widget_Base {

	/**
	 * Construct.
	 *
	 * @param  array  $data Data.
	 * @param  string $args Args.
	 * @return void
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'forum-lists', WBCOM_ESSENTIAL_ELEMENTOR_URL . 'assets/css/forum-lists.css', array(), WBCOM_ESSENTIAL_VERSION );
	}

	/**
	 * Get Name.
	 */
	public function get_name() {
		return 'wbcom-forums';
	}

	/**
	 * Get Title.
	 */
	public function get_title() {
		return esc_html__( 'Forums', 'wbcom-essential' );
	}

	/**
	 * Get Icon.
	 */
	public function get_icon() {
		return 'eicon-post-list';
	}

	/**
	 * Get dependent style..
	 */
	public function get_style_depends() {
		return array( 'forum-lists' );
	}

	/**
	 * Get categories.
	 */
	public function get_categories() {
		return array( 'wbcom-elements' );
	}

	/**
	 * Get keywords.
	 */
	public function get_keywords() {
		return array( 'forums', 'bbpress', 'discussion', 'board', 'topics' );
	}

	/**
	 * Register Controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			array(
				'label' => esc_html__( 'Layout', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'forums_count',
			array(
				'label'   => esc_html__( 'Forums Count', 'wbcom-essential' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 5,
				),
				'range'   => array(
					'px' => array(
						'min'  => 1,
						'max'  => 20,
						'step' => 1,
					),
				),
			)
		);

		$this->add_control(
			'row_space',
			array(
				'label'     => esc_html__( 'Row Space', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 20,
				),
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-forums__list > li' => 'margin-bottom: {{SIZE}}px;padding-bottom: {{SIZE}}px',
				),
			)
		);

		$this->add_control(
			'switch_more',
			array(
				'label'   => esc_html__( 'Show All Forums Link', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'switch_avatar',
			array(
				'label'   => esc_html__( 'Show Avatar', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'switch_meta',
			array(
				'label'   => esc_html__( 'Show Meta Data', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'switch_meta_replies',
			array(
				'label'     => esc_html__( 'Show Meta Replies', 'wbcom-essential' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'switch_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			'switch_last_reply',
			array(
				'label'   => esc_html__( 'Show Last Reply', 'wbcom-essential' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'heading_text',
			array(
				'label'       => __( 'Heading Text', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'Forums', 'wbcom-essential' ),
				'placeholder' => __( 'Enter heading text', 'wbcom-essential' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'forum_link_text',
			array(
				'label'       => __( 'Forum Link Text', 'wbcom-essential' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'All Forums', 'wbcom-essential' ),
				'placeholder' => __( 'Enter forum link text', 'wbcom-essential' ),
				'label_block' => true,
				'condition'   => array(
					'switch_more' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_box',
			array(
				'label' => esc_html__( 'Box', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'box_border',
				'label'       => __( 'Border', 'wbcom-essential' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .wbcom-essential-forums',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => '4',
					'right'  => '4',
					'bottom' => '4',
					'left'   => '4',
				),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-essential-forums' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'background_color',
				'label'    => __( 'Background', 'wbcom-essential' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .wbcom-essential-forums',
			)
		);

		$this->add_control(
			'separator_all',
			array(
				'label'     => __( 'All Forums Link', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'extra_color',
			array(
				'label'     => __( 'All Forums Link Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-block-header__extra a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_avatar',
			array(
				'label'     => esc_html__( 'Avatar', 'wbcom-essential' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'switch_avatar' => 'yes',
				),
			)
		);

		$this->add_control(
			'avatar_size',
			array(
				'label'     => __( 'Size', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 52,
				),
				'range'     => array(
					'px' => array(
						'min'  => 20,
						'max'  => 100,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-forums__avatar' => 'flex: 0 0 {{SIZE}}px;',
					'{{WRAPPER}} .wbcom-essential-forums__avatar img.avatar' => 'width: {{SIZE}}px;',
					'{{WRAPPER}} .wbcom-essential-forums__avatar img.avatar' => 'max-width: {{SIZE}}px;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'avatar_border',
				'label'       => __( 'Border', 'wbcom-essential' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .wbcom-essential-forums__avatar img.avatar',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'avatar_border_radius',
			array(
				'label'      => __( 'Border Radius', 'wbcom-essential' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wbcom-essential-forums__avatar img.avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_control(
			'avatar_opacity',
			array(
				'label'     => __( 'Opacity (%)', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 1,
				),
				'range'     => array(
					'px' => array(
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-forums__avatar img.avatar' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_control(
			'avatar_spacing',
			array(
				'label'     => __( 'Spacing', 'wbcom-essential' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 15,
				),
				'range'     => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wbcom-essential-forums__avatar'  => 'margin-right: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => __( 'Content', 'wbcom-essential' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography_title',
				'label'    => __( 'Typography Title', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .item-title a',
			)
		);

		$this->start_controls_tabs(
			'title_tabs'
		);

		$this->start_controls_tab(
			'title_normal_tab',
			array(
				'label' => __( 'Normal', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'title_item_color',
			array(
				'label'     => __( 'Title/Links Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#122B46',
				'selectors' => array(
					'{{WRAPPER}} .item-title a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .wbcom-essential-forums__ww .bs-replied a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_hover_tab',
			array(
				'label' => __( 'Hover', 'wbcom-essential' ),
			)
		);

		$this->add_control(
			'title_item_color_hover',
			array(
				'label'     => __( 'Title/Links Color', 'wbcom-essential' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#007CFF',
				'selectors' => array(
					'{{WRAPPER}} .item-title a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .wbcom-essential-forums__ww .bs-replied a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography_content',
				'label'    => __( 'Typography Content', 'wbcom-essential' ),
				'selector' => '{{WRAPPER}} .item-meta > div,{{WRAPPER}} .wbcom-essential-forums__item .wbcom-essential-forums__ww .bs-replied > a.bbp-author-link span',
			)
		);

		$this->add_control(
			'meta_color',
			array(
				'label'     => __( 'Meta Data Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#A3A5A9',
				'selectors' => array(
					'{{WRAPPER}} .item-meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reply_color',
			array(
				'label'     => __( 'Last Reply Color', 'wbcom-essential' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .bs-last-reply' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render forums widget.
	 */
	protected function render() {
		// Check if bbPress is active before rendering.
		if ( ! class_exists( 'bbPress' ) ) {
			if ( current_user_can( 'manage_options' ) ) {
				echo '<p>' . esc_html__( 'bbPress is required for this widget.', 'wbcom-essential' ) . '</p>';
			}
			return;
		}

		$settings = $this->get_settings_for_display();

		$args = array(
			'order'          => 'DESC',
			'posts_per_page' => esc_attr( $settings['forums_count']['size'] ),
			'max_num_pages'  => esc_attr( $settings['forums_count']['size'] ),
		); ?>

		<div class="wbcom-essential-forums <?php echo ( ! bbp_has_topics( $args ) ) ? 'wbcom-essential-forums--blank' : ''; ?>">

			<?php if ( bbp_has_topics( $args ) ) : ?>

				<div class="wbcom-essential-block-header flex align-items-center">
					<div class="wbcom-essential-block-header__title"><h3><?php echo esc_html( $settings['heading_text'] ); ?></h3></div>
					<?php if ( $settings['switch_more'] ) : ?>
						<div class="wbcom-essential-block-header__extra push-right">
						<?php if ( '' !== $settings['forum_link_text'] ) { ?>
							<a href="<?php echo esc_url( home_url( bbp_get_root_slug() ) ); ?>" class="count-more"><?php echo esc_html( $settings['forum_link_text'] ); ?><i class="eicon-chevron-right"></i></a>
						<?php } ?>
						</div>
					<?php endif; ?>
				</div>

				<?php do_action( 'bbp_template_before_topics_loop' ); ?>

				<div class="bbel-list-flow">
					<ul class="wbcom-essential-forums__list bbp-topics1 bs-item-list bs-forums-items list-view">

						<?php
						while ( bbp_topics() ) :
							bbp_the_topic();
							?>

							<li>
								<?php $class = bbp_is_topic_open() ? '' : 'closed'; ?>
								<div class="wbcom-essential-forums__item">
									<div class="flex">
										<?php if ( $settings['switch_avatar'] ) : ?>
											<div class="wbcom-essential-forums__avatar">
												<?php echo wp_kses_post( bbp_get_topic_author_link( array( 'size' => '180' ) ) ); ?>
											</div>
										<?php endif; ?>
										<div class="item">
											<div class="item-title">
												<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink(); ?>"><?php bbp_topic_title(); ?></a>
											</div>
											<div class="item-meta wbcom-essential-reply-meta">
												<?php if ( $settings['switch_meta'] ) : ?>
													<div class="wbcom-essential-forums__ww">
														<span class="bs-replied">
														<?php
														$author_link = bbp_author_link(
															array(
																'post_id' => bbp_get_topic_last_active_id(),
																'size'    => 1,
															)
														);
														$bbp_author_link = str_replace(
															'&nbsp;',
															'',
															$author_link ?? ''
														);
														?>
															<span class="bbp-topic-freshness-author"><?php // echo $bbp_author_link; ?></span> <?php esc_html_e( 'replied', 'wbcom-essential' ); ?> <?php bbp_topic_freshness_link(); ?>
														</span>
														<?php if ( $settings['switch_meta_replies'] ) : ?>
															<span class="bs-voices-wrap">
																<?php
																	$voice_count = bbp_get_topic_voice_count( bbp_get_topic_id() );
																	$voice_text  = $voice_count > 1 ? __( 'Members', 'wbcom-essential' ) : __( 'Member', 'wbcom-essential' );

																	$topic_reply_count = bbp_get_topic_reply_count( bbp_get_topic_id() );
																	$topic_post_count  = bbp_get_topic_post_count( bbp_get_topic_id() );
																	$topic_reply_text  = '';
																?>
																<span class="bs-voices"><?php bbp_topic_voice_count(); ?> <?php echo esc_html( $voice_text ); ?></span>
																<span class="bs-separator">&middot;</span>
																<span class="bs-replies">
																<?php
																if ( bbp_show_lead_topic() ) {
																	bbp_topic_reply_count();
																	$topic_reply_text = $topic_reply_count > 1 ? __( ' Replies', 'wbcom-essential' ) : __( ' Reply', 'wbcom-essential' );
																} else {
																	bbp_topic_post_count();
																	$topic_reply_text = $topic_post_count > 1 ? __( ' Replies', 'wbcom-essential' ) : __( ' Reply', 'wbcom-essential' );
																}

																	echo esc_html( $topic_reply_text );

																?>
																</span>
															<?php endif; ?>
														</span>
													</div>
												<?php endif; ?>
												<?php if ( $settings['switch_last_reply'] ) : ?>
												<div class="wbcom-essential-forums__last-reply">
													<?php
													$get_last_reply_id = bbp_get_topic_last_reply_id( bbp_get_topic_id() );
													?>
													<span class="bs-last-reply <?php echo ( ! empty( bbp_get_reply_excerpt( $get_last_reply_id ) ) ) ? '' : 'is-empty'; ?>">
														<?php
														if ( bbp_is_topic( $get_last_reply_id ) ) {
															add_filter( 'bbp_get_topic_reply_link', 'wbcom_essential_theme_elementor_topic_link_attribute_change', 9999, 3 );
															// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bbPress function returns safe HTML
															echo bbp_get_topic_reply_link(
																array(
																	'id' => $get_last_reply_id,
																	'reply_text' => '',
																)
															);
															remove_filter( 'bbp_get_topic_reply_link', 'wbcom_essential_theme_elementor_topic_link_attribute_change', 9999, 3 );
															// If post is a reply, print the reply admin links instead.
														} else {
															add_filter( 'bbp_get_reply_to_link', 'wbcom_essential_theme_elementor_reply_link_attribute_change', 9999, 3 );
															// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bbPress function returns safe HTML
															echo bbp_get_reply_to_link(
																array(
																	'id' => $get_last_reply_id,
																	'reply_text' => '',
																)
															);
															remove_filter( 'bbp_get_reply_to_link', 'wbcom_essential_theme_elementor_reply_link_attribute_change', 9999, 3 );
														}
														?>
														<?php echo esc_html( bbp_get_reply_excerpt( $get_last_reply_id, 90 ) ); ?>
													</span>
												</div>
												<?php endif; ?>
											</div>
										</div>
									</div>

								</div>
							</li>

						<?php endwhile; ?>

					</ul><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->
				</div>

				<?php do_action( 'bbp_template_after_topics_loop' ); ?>

			<?php else : ?>

				<div class="wbcom-essential-no-data wbcom-essential-no-data--forums">
					<img class="wbcom-essential-no-data__image" src="<?php echo esc_url( WBCOM_ESSENTIAL_ASSETS_URL ); ?>images/no-data-found.svg" alt="Forums" />
					<br />
					<?php bbp_get_template_part( 'feedback', 'no-topics' ); ?>
					<a href="<?php echo esc_url( home_url( bbp_get_root_slug() ) ); ?>" class="wbcom-essential-no-data__link"><?php esc_html_e( 'Start a Discussion', 'wbcom-essential' ); ?></a>
				</div>

			<?php endif; ?>

		</div>
		<?php
	}

}
