<?php
	// Loads child theme textdomain
	load_child_theme_textdomain( CURRENT_THEME, CHILD_DIR . '/languages' );

	// Loads custom scripts.
	// require_once( 'custom-js.php' );

	// Add the postmeta to Partners
	require_once('theme-partnersmeta.php');

	add_action( 'wp_enqueue_scripts', 'cherry_child_custom_scripts' );
	function cherry_child_custom_scripts() {
		wp_enqueue_script( 'my_script', CHILD_URL . '/js/my_script.js', array( 'jquery' ), '1.0', true );
		wp_enqueue_script( 'parallaxSlider', CHILD_URL . '/js/parallaxSlider.js', array( 'jquery' ), '1.0' );
		wp_enqueue_script( 'chrome-smoothing-scroll', CHILD_URL . '/js/smoothing-scroll.js', array( 'jquery' ), '1.0' );
	}

	add_filter( 'cherry_stickmenu_selector', 'cherry_change_selector' );
	function cherry_change_selector($selector) {
		$selector = '.nav-wrapper';
		return $selector;
	}

	function antwortzeit_move_media () {
	    global $menu; 
	    $menu[14] = $menu[10]; 
	    unset($menu[10]);
	} 
	add_action('admin_menu', 'antwortzeit_move_media');

	/**
	 * Service Box
	 *
	 */
	if (!function_exists('service_box_shortcode')) {

		function service_box_shortcode( $atts, $content = null, $shortcodename = '' ) {
			extract(shortcode_atts(
				array(
					'title'        => '',
					'subtitle'     => '',
					'icon'         => '',
					'text'         => '',
					'btn_text'     => '',
					'btn_link'     => '',
					'btn_size'     => '',
					'target'       => '',
					'custom_class' => ''
			), $atts));

			$output =  '<div class="service-box '.$custom_class.'">';			

			$output .= '<div class="service-box_body">';

			if ($title!="") {
				$output .= '<h2 class="title"><a href="'.$btn_link.'" title="'.$title.'" target="'.$target.'">';				
			}

				if($icon != 'no') {
					$icon_url = CHERRY_PLUGIN_URL . 'includes/images/' . strtolower($icon) . '.png' ;
					if( defined ('CHILD_DIR') ) {
						if(file_exists(CHILD_DIR.'/images/'.strtolower($icon).'.png')){
							$icon_url = CHILD_URL.'/images/'.strtolower($icon).'.png';
						}
					}

					switch ($icon) {
						case 'icon9':
							$output .= '<figure class="icon"><img src="'.$icon_url.'" alt="" /></figure>';
							break;

						case 'icon10':
							$output .= '<figure class="icon"><img src="'.$icon_url.'" alt="" /></figure>';
							break;
						
						default:			
							$output .= '<figure class="icon extra"><img src="'.$icon_url.'" alt="" /><span class="hover"><img src="'.$icon_url.'" alt="" /></span></figure>';									
							break;
					}				
				}	

			if ($title!="") {
				$output .= '<br>';
				$output .= '<span class="title-wrap">' . $title . '</span>';
				$output .= '</a></h2>';
			}

			if ($subtitle!="") {
				$output .= '<h5 class="sub-title">';
				$output .= $subtitle;
				$output .= '</h5>';
			}
			if ($text!="") {
				$output .= '<div class="service-box_txt">';
				$output .= $text;
				$output .= '</div>';
			}
			if (($btn_link!="") && ($btn_text!="")) {
				$output .=  '<div class="btn-align"><a href="'.$btn_link.'" title="'.$btn_text.'" class="btn btn-inverse btn-'.$btn_size.' btn-primary " target="'.$target.'">';
				$output .= $btn_text;
				$output .= '</a></div>';
			}
			$output .= '</div>';
			$output .= '</div><!-- /Service Box -->';

			$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

			return $output;
		}
		add_shortcode('service_box', 'service_box_shortcode');

	}

	/**
	 * Post Cycle
	 *
	 */
	if (!function_exists('shortcode_post_cycle')) {

		function shortcode_post_cycle( $atts, $content = null, $shortcodename = '' ) {
			extract(shortcode_atts(array(
					'num'              => '5',
					'type'             => 'post',
					'meta'             => '',
					'effect'           => 'slide',
					'thumb'            => 'true',
					'thumb_width'      => '200',
					'thumb_height'     => '180',
					'more_text_single' => __('Read more', CURRENT_THEME),
					'category'         => '',
					'custom_category'  => '',
					'excerpt_count'    => '15',
					'pagination'       => 'true',
					'navigation'       => 'true',
					'custom_class'     => ''
			), $atts));

			$type_post         = $type;
			$slider_pagination = $pagination;
			$slider_navigation = $navigation;
			$random            = gener_random(10);
			$i                 = 0;
			$rand              = rand();
			$count             = 0;
			if ( is_rtl() ) {
				$is_rtl = 'true';
			} else {
				$is_rtl = 'false';
			}

			$output = '<script type="text/javascript">
							jQuery(window).load(function() {
								jQuery("#flexslider_'.$random.'").flexslider({
									animation: "'.$effect.'",
									smoothHeight : true,
									directionNav: '.$slider_navigation.',
									controlNav: '.$slider_pagination.',
									rtl: '.$is_rtl.',
									slideshow: false
								});
							});';
			$output .= '</script>';
			$output .= '<div id="flexslider_'.$random.'" class="flexslider no-bg '.$custom_class.'">';
				$output .= '<ul class="slides">';

				global $post;
				global $my_string_limit_words;

				// WPML filter
				$suppress_filters = get_option('suppress_filters');

				$args = array(
					'post_type'              => $type_post,
					'category_name'          => $category,
					$type_post . '_category' => $custom_category,
					'numberposts'            => $num,
					'orderby'                => 'post_date',
					'order'                  => 'DESC',
					'suppress_filters'       => $suppress_filters
				);

				$latest = get_posts($args);

				$counter = 1;
				$output .= '<li class="list-item-'.$count.'">';
				$output .= '<div class="row">';

				foreach($latest as $key => $post) {
					//Check if WPML is activated
					if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
						global $sitepress;

						$post_lang = $sitepress->get_language_for_element($post->ID, 'post_' . $type_post);
						$curr_lang = $sitepress->get_current_language();
						// Unset not translated posts
						if ( $post_lang != $curr_lang ) {
							unset( $latest[$key] );
						}
						// Post ID is different in a second language Solution
						if ( function_exists( 'icl_object_id' ) ) {
							$post = get_post( icl_object_id( $post->ID, $type_post, true ) );
						}
					}
					setup_postdata($post);
					$excerpt        = get_the_excerpt();
					$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
					$url            = $attachment_url['0'];
					$image          = aq_resize($url, $thumb_width, $thumb_height, true);					

					if ($counter > 9) { $counter = 1;
						$output .= '<li class="list-item-'.$count.'">';
						$output .= '<div class="row">';
					}

						$output .= '<div class="list-item-inner span4">';

						if ($thumb == 'true') {

							if ( has_post_thumbnail($post->ID) ){
								$output .= '<figure class="thumbnail featured-thumbnail"><a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
								$output .= '<img  src="'.$image.'" alt="'.get_the_title($post->ID).'" />';
								$output .= '</a></figure>';
							} else {

								$thumbid = 0;
								$thumbid = get_post_thumbnail_id($post->ID);

								$images = get_children( array(
									'orderby'        => 'menu_order',
									'order'          => 'ASC',
									'post_type'      => 'attachment',
									'post_parent'    => $post->ID,
									'post_mime_type' => 'image',
									'post_status'    => null,
									'numberposts'    => -1
								) );

								if ( $images ) {

									$k = 0;
									//looping through the images
									foreach ( $images as $attachment_id => $attachment ) {
										// $prettyType = "prettyPhoto-".$rand ."[gallery".$i."]";
										//if( $attachment->ID == $thumbid ) continue;

										$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' ); // returns an array
										$img = aq_resize( $image_attributes[0], $thumb_width, $thumb_height, true ); //resize & crop img
										$alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
										$image_title = $attachment->post_title;

										if ( $k == 0 ) {
											$output .= '<figure class="featured-thumbnail">';
											$output .= '<a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
											$output .= '<img  src="'.$img.'" alt="'.get_the_title($post->ID).'" />';
											$output .= '</a></figure>';
										} break;
										$k++;
									}
								}
							}
						}

						$output .= '<h5><a href="'.get_permalink($post->ID).'" title="'.get_the_title($post->ID).'">';
						$output .= get_the_title($post->ID);
						$output .= '</a></h5>';

						if($meta == 'true'){
							$output .= '<span class="meta">';
							$output .= '<span class="post-date">';
							$output .= get_the_date();
							$output .= '</span>';
							$output .= '<span class="post-comments">'.__('Comments', CURRENT_THEME).": ";
							$output .= '<a href="'.get_comments_link($post->ID).'">';
							$output .= get_comments_number($post->ID);
							$output .= '</a>';
							$output .= '</span>';
							$output .= '</span>';
						}
						//display post options
						$output .= '<div class="post_options">';

						switch( $type_post ) {

							case "team":
								$teampos    = get_post_meta( $post->ID, 'my_team_pos', true );
								$team_email = sanitize_email( get_post_meta( $post->ID, 'my_team_email', true ) );
								$teaminfo   = get_post_meta( $post->ID, 'my_team_info', true );

								if ( !empty( $teampos ) ) {
									$output .= "<span class='page-desc'>" . $teampos . "</span><br>";
								}

								if ( !empty( $team_email ) && is_email( $team_email ) ) {
									$output .= '<span class="team-email"><a href="mailto:' . antispambot( $team_email, 1 ) . '">' . antispambot( $team_email ) . ' </a></span><br>';
								}

								if ( !empty( $teaminfo ) ) {
									$output .= '<span class="team-content post-content team-info">' . esc_html( $teaminfo ) . '</span>';
								}

								$output .= cherry_get_post_networks(array('post_id' => $post->ID, 'display_title' => false, 'output_type' => 'return'));
								break;

							case "testi":
								$testiname  = get_post_meta( $post->ID, 'my_testi_caption', true );
								$testiurl   = esc_url( get_post_meta( $post->ID, 'my_testi_url', true ) );
								$testiinfo  = get_post_meta( $post->ID, 'my_testi_info', true );
								$testiemail = sanitize_email( get_post_meta($post->ID, 'my_testi_email', true ) );

								if ( !empty( $testiname ) ) {
									$output .= '<span class="user">' . $testiname . '</span>, ';
								}

								if ( !empty( $testiinfo ) ) {
									$output .= '<span class="info">' . $testiinfo . '</span><br>';
								}

								if ( !empty( $testiurl ) ) {
									$output .= '<a class="testi-url" href="' . $testiurl . '" target="_blank">' . $testiurl . '</a><br>';
								}

								if ( !empty( $testiemail ) && is_email( $testiemail ) ) {
									$output .= '<a class="testi-email" href="mailto:' . antispambot( $testiemail, 1 ) . '">' . antispambot( $testiemail ) . ' </a>';
								}
								break;

							case "portfolio":
								$portfolioClient = (get_post_meta($post->ID, 'tz_portfolio_client', true)) ? get_post_meta($post->ID, 'tz_portfolio_client', true) : "";
								$portfolioDate = (get_post_meta($post->ID, 'tz_portfolio_date', true)) ? get_post_meta($post->ID, 'tz_portfolio_date', true) : "";
								$portfolioInfo = (get_post_meta($post->ID, 'tz_portfolio_info', true)) ? get_post_meta($post->ID, 'tz_portfolio_info', true) : "";
								$portfolioURL = (get_post_meta($post->ID, 'tz_portfolio_url', true)) ? get_post_meta($post->ID, 'tz_portfolio_url', true) : "";
								$output .="<strong class='portfolio-meta-key'>".__('Client', CURRENT_THEME).": </strong><span> ".$portfolioClient."</span><br>";
								$output .="<strong class='portfolio-meta-key'>".__('Date', CURRENT_THEME).": </strong><span> ".$portfolioDate."</span><br>";
								$output .="<strong class='portfolio-meta-key'>".__('Info', CURRENT_THEME).": </strong><span> ".$portfolioInfo."</span><br>";
								$output .="<a href='".$portfolioURL."'>".__('Launch Project', CURRENT_THEME)."</a><br>";
								break;

							default:
								$output .="";
						};
						$output .= '</div>';

						if($excerpt_count >= 1){
							$output .= '<p class="excerpt">';
							$output .= my_string_limit_words($excerpt,$excerpt_count);
							$output .= '</p>';
						}

						if($more_text_single!=""){
							$output .= '<a href="'.get_permalink($post->ID).'" class="btn btn-primary" title="'.get_the_title($post->ID).'">';
							$output .= $more_text_single;
							$output .= '</a>';
						}

						$output .= '</div>';

					if ($counter % 9 == 0) {
						$output .= '</div>';
						$output .= '</li>';
					}
					$count++;
					$counter++;
				}
				wp_reset_postdata(); // restore the global $post variable
				$output .= '</ul>';
			$output .= '</div>';

			$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

			return $output;
		}
		add_shortcode('post_cycle', 'shortcode_post_cycle');

	}

	/**
	 * Carousel Elastislide
	 */
	if ( !function_exists('shortcode_carousel') ) {
		function shortcode_carousel( $atts, $content = null, $shortcodename = '' ) {
			extract( shortcode_atts( array(
				'title'            => '',
				'num'              => 8,
				'type'             => 'post',
				'thumb'            => 'true',
				'thumb_width'      => 220,
				'thumb_height'     => 180,
				'more_text_single' => '',
				'category'         => '',
				'custom_category'  => '',
				'excerpt_count'    => 12,
				'date'             => '',
				'author'           => '',
				'comments'         => '',
				'min_items'        => 3,
				'spacer'           => 18,
				'custom_class'     => ''
			), $atts) );

			switch ( strtolower( str_replace(' ', '-', $type) ) ) {
				case 'blog':
					$type = 'post';
					break;
				case 'portfolio':
					$type = 'portfolio';
					break;
				case 'testimonial':
					$type = 'testi';
					break;
				case 'services':
					$type = 'services';
					break;
				case 'our-team':
					$type = 'team';
				break;
			}

			$carousel_uniqid = uniqid();
			$thumb_width     = absint( $thumb_width );
			$thumb_height    = absint( $thumb_height );
			$excerpt_count   = absint( $excerpt_count );
			$itemcount = 0;

			$output = '<div class="carousel-wrap ' . $custom_class . '">';
				if ( !empty( $title{0} ) ) {
					$output .= '<h2>' . esc_html( $title ) . '</h2>';
				}
				$output .= '<div id="carousel-' . $carousel_uniqid . '" class="es-carousel-wrapper">';
				$output .= '<div class="es-carousel">';
					$output .= '<ul class="es-carousel_list unstyled clearfix">';

						// WPML filter
						$suppress_filters = get_option( 'suppress_filters' );

						$args = array(
							'post_type'         => $type,
							'category_name'     => $category,
							$type . '_category' => $custom_category,
							'numberposts'       => $num,
							'orderby'           => 'post_date',
							'order'             => 'DESC',
							'suppress_filters'  => $suppress_filters
						);

						global $post; // very important
						$carousel_posts = get_posts( $args );

						foreach ( $carousel_posts as $key => $post ) {
							$post_id = $post->ID;

							//Check if WPML is activated
							if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
								global $sitepress;

								$post_lang = $sitepress->get_language_for_element( $post_id, 'post_' . $type );
								$curr_lang = $sitepress->get_current_language();
								// Unset not translated posts
								if ( $post_lang != $curr_lang ) {
									unset( $carousel_posts[$j] );
								}
								// Post ID is different in a second language Solution
								if ( function_exists( 'icl_object_id' ) ) {
									$post = get_post( icl_object_id( $post_id, $type, true ) );
								}
							}
							setup_postdata( $post ); // very important
							$post_title      = esc_html( get_the_title( $post_id ) );
							$post_title_attr = esc_attr( strip_tags( get_the_title( $post_id ) ) );
							$format          = get_post_format( $post_id );
							$format          = (empty( $format )) ? 'format-standart' : 'format-' . $format;
							if ( get_post_meta( $post_id, 'tz_link_url', true ) ) {
								$post_permalink = ( $format == 'format-link' ) ? esc_url( get_post_meta( $post_id, 'tz_link_url', true ) ) : get_permalink( $post_id );
							} else {
								$post_permalink = get_permalink( $post_id );
							}
							if ( has_excerpt( $post_id ) ) {
								$excerpt = wp_strip_all_tags( get_the_excerpt() );
							} else {
								$excerpt = wp_strip_all_tags( strip_shortcodes (get_the_content() ) );
							}

							$partners_url    = get_post_meta($post_id, 'my_partners_url', true);
							$partners_target = get_post_meta($post_id, 'my_partners_target', true);

							$output .= '<li class="es-carousel_li ' . $format . ' clearfix list-item-'.$itemcount.'">';

								if ( $thumb == 'true' ) :

									if ( has_post_thumbnail( $post_id ) ) {
										$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
										$url            = $attachment_url['0'];
										$image          = aq_resize($url, $thumb_width, $thumb_height, true);

										$output .= '<figure class="featured-thumbnail">';
											if (($type == 'partners') && ($partners_url != '')) {
												$output .= '<a href="' . $partners_url . '" title="' . $post_title . '" target="'.$partners_target.'">';	
											} else {
												$output .= '<a href="' . $post_permalink . '" title="' . $post_title . '">';	
											}											
												$output .= '<img src="' . $image . '" alt="' . $post_title . '" />';
											$output .= '</a>';
										$output .= '</figure>';

									} else {

										$attachments = get_children( array(
											'orderby'        => 'menu_order',
											'order'          => 'ASC',
											'post_type'      => 'attachment',
											'post_parent'    => $post_id,
											'post_mime_type' => 'image',
											'post_status'    => null,
											'numberposts'    => 1
										) );
										if ( $attachments ) {
											foreach ( $attachments as $attachment_id => $attachment ) {
												$image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' );
												$img              = aq_resize( $image_attributes[0], $thumb_width, $thumb_height, true );
												$alt              = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );

												$output .= '<figure class="featured-thumbnail">';
													if (($type == 'partners') && ($partners_url != '')) {
														$output .= '<a href="' . $partners_url . '" title="' . $post_title . '" target="'.$partners_target.'">';	
													} else {
														$output .= '<a href="' . $post_permalink . '" title="' . $post_title . '">';	
													}
															$output .= '<img src="' . $img . '" alt="' . $alt . '" />';
													$output .= '</a>';
												$output .= '</figure>';
											}
										}
									}

								endif;

								$output .= '<div class="desc">';

									// post date
									if ( $date == 'yes' ) {
										$output .= '<time datetime="' . get_the_time( 'Y-m-d\TH:i:s', $post_id ) . '">' . get_the_date() . '</time>';
									}

									// post author
									if ( $author == 'yes' ) {
										$output .= '<em class="author">&nbsp;<span>' . __('by', CURRENT_THEME) . '</span>&nbsp;<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a> </em>';
									}

									// post comment count
									if ( $comments == 'yes' ) {
										$comment_count = $post->comment_count;
										if ( $comment_count >= 1 ) :
											$comment_count = $comment_count . ' <span>' . __( 'Comments', CURRENT_THEME ) . '</span>';
										else :
											$comment_count = $comment_count . ' <span>' . __( 'Comment', CURRENT_THEME ) . '</span>';
										endif;
										$output .= '<a href="'. $post_permalink . '#comments" class="comments_link">' . $comment_count . '</a>';
									}

									// post title
									if ( !empty($post_title{0}) ) {
										$output .= '<h5><a href="' . $post_permalink . '" title="' . $post_title_attr . '">';
											$output .= $post_title;
										$output .= '</a></h5>';
									}

									// post excerpt
									if ( !empty($excerpt{0}) ) {
										$output .= $excerpt_count > 0 ? '<p class="excerpt">' . my_string_limit_words( $excerpt, $excerpt_count ) . '</p>' : '';
									}

									// post more button
									$more_text_single = esc_html( wp_kses_data( $more_text_single ) );
									if ( $more_text_single != '' ) {
										$output .= '<a href="' . get_permalink( $post_id ) . '" class="btn btn-primary" title="' . $post_title_attr . '">';
											$output .= __( $more_text_single, CURRENT_THEME );
										$output .= '</a>';
									}
								$output .= '</div>';
							$output .= '</li>';
							$itemcount++;
						}
						wp_reset_postdata(); // restore the global $post variable

					$output .= '</ul>';
				$output .= '</div></div>';
				$output .= '<script>
					jQuery(document).ready(function(){
						jQuery("#carousel-' . $carousel_uniqid . '").elastislide({
							imageW  : ' . $thumb_width . ',
							minItems: ' . $min_items . ',
							speed   : 600,
							easing  : "easeOutQuart",
							margin  : ' . $spacer . ',
							border  : 0
						});
					})';
				$output .= '</script>';
			$output .= '</div>';

			$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

			return $output;
		}
		add_shortcode('carousel', 'shortcode_carousel');
	}

	//Recent Testimonials
	if (!function_exists('shortcode_recenttesti')) {

		function shortcode_recenttesti( $atts, $content = null, $shortcodename = '' ) {
			extract(shortcode_atts(array(
					'num'           => '5',
					'thumb'         => 'true',
					'excerpt_count' => '30',
					'custom_class'  => '',
			), $atts));

			// WPML filter
			$suppress_filters = get_option('suppress_filters');

			$args = array(
					'post_type'        => 'testi',
					'numberposts'      => $num,
					'orderby'          => 'post_date',
					'suppress_filters' => $suppress_filters
				);
			$testi = get_posts($args);

			$itemcounter = 0;

			$output = '<div class="testimonials row '.$custom_class.'">';

			global $post;
			global $my_string_limit_words;

			foreach ($testi as $k => $post) {
				//Check if WPML is activated
				if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
					global $sitepress;

					$post_lang = $sitepress->get_language_for_element($post->ID, 'post_testi');
					$curr_lang = $sitepress->get_current_language();
					// Unset not translated posts
					if ( $post_lang != $curr_lang ) {
						unset( $testi[$k] );
					}
					// Post ID is different in a second language Solution
					if ( function_exists( 'icl_object_id' ) ) {
						$post = get_post( icl_object_id( $post->ID, 'testi', true ) );
					}
				}
				setup_postdata( $post );
				$post_id = $post->ID;
				$excerpt = get_the_excerpt();

				// Get custom metabox value.
				$testiname  = get_post_meta( $post_id, 'my_testi_caption', true );
				$testiurl   = esc_url( get_post_meta( $post_id, 'my_testi_url', true ) );
				$testiinfo  = get_post_meta( $post_id, 'my_testi_info', true );
				$testiemail = sanitize_email( get_post_meta( $post_id, 'my_testi_email', true ) );

				$attachment_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
				$url            = $attachment_url['0'];
				$image          = aq_resize($url, 116, 116, true);

				$output .= '<div class="testi-item span4 list-item-'.$itemcounter.'">';
					$output .= '<blockquote class="testi-item_blockquote">';
						$output .= '<a href="'.get_permalink( $post_id ).'">&quot;';
							$output .= my_string_limit_words($excerpt,$excerpt_count);
						$output .= '&quot;</a><div class="clear"></div>';

						$output .= '<small class="testi-meta">';
							if ( !empty( $testiname ) ) {
								$output .= '<span class="user">';
									$output .= $testiname;
								$output .= '</span>';
							}

							if ( !empty( $testiinfo ) ) {
								$output .= '<span class="info">';
									$output .= $testiinfo;
								$output .= '</span>';
							}

							if ( !empty( $testiurl ) ) {
								$output .= '<a class="testi-url" href="'.$testiurl.'">';
									$output .= $testiurl;
								$output .= '</a>';
							}

							if ( !empty( $testiemail ) && is_email( $testiemail ) ) {
								$output .= '<br><a class="testi-email" href="mailto:' . antispambot( $testiemail, 1 ) . '" >' . antispambot( $testiemail ) . ' </a>';
							}

						$output .= '</small>';

					$output .= '</blockquote>';					

					if ($thumb == 'true') {
						if ( has_post_thumbnail( $post_id ) ){
							$output .= '<figure class="featured-thumbnail">';
							$output .= '<img src="'.$image.'" alt="" />';
							$output .= '</figure>';
						}
					}

				$output .= '</div>';
				$itemcounter++;

			}
			wp_reset_postdata(); // restore the global $post variable
			$output .= '</div>';

			$output = apply_filters( 'cherry_plugin_shortcode_output', $output, $atts, $shortcodename );

			return $output;
		}
		add_shortcode('recenttesti', 'shortcode_recenttesti');

	}

	//------------------------------------------------------
	//  Related Posts
	//------------------------------------------------------
	if(!function_exists('cherry_related_posts')){
		function cherry_related_posts($args = array()){
			global $post;
			$default = array(
				'post_type' => get_post_type($post),
				'class' => 'related-posts',
				'class_list' => 'related-posts_list',
				'class_list_item' => 'related-posts_item',
				'display_title' => true,
				'display_link' => true,
				'display_thumbnail' => true,
				'width_thumbnail' => 170,
				'height_thumbnail' => 156,
				'before_title' => '<h3 class="related-posts_h">',
				'after_title' => '</h3>',
				'posts_count' => 4
			);
			extract(array_merge($default, $args));

			$post_tags = wp_get_post_terms($post->ID, $post_type.'_tag', array("fields" => "slugs"));
			$tags_type = $post_type=='post' ? 'tag' : $post_type.'_tag' ;
			$suppress_filters = get_option('suppress_filters');// WPML filter
			$blog_related = apply_filters( 'cherry_text_translate', of_get_option('blog_related'), 'blog_related' );
			if ($post_tags && !is_wp_error($post_tags)) {
				$args = array(
					"$tags_type" => implode(',', $post_tags),
					'post_status' => 'publish',
					'posts_per_page' => $posts_count,
					'ignore_sticky_posts' => 1,
					'post__not_in' => array($post->ID),
					'post_type' => $post_type,
					'suppress_filters' => $suppress_filters
					);
				query_posts($args);
				if ( have_posts() ) {
					$output = '<div class="'.$class.'">';
					$output .= $display_title ? $before_title.$blog_related.$after_title : '' ;
					$output .= '<ul class="'.$class_list.' clearfix">';
					while( have_posts() ) {
						the_post();
						$thumb   = has_post_thumbnail() ? get_post_thumbnail_id() : PARENT_URL.'/images/empty_thumb.gif';
						$blank_img = stripos($thumb, 'empty_thumb.gif');
						$img_url = $blank_img ? $thumb : wp_get_attachment_url( $thumb,'full');
						$image   = $blank_img ? $thumb : aq_resize($img_url, $width_thumbnail, $height_thumbnail, true) or $img_url;

						$output .= '<li class="'.$class_list_item.'">';
						$output .= $display_thumbnail ? '<figure class="thumbnail featured-thumbnail"><a href="'.get_permalink().'" title="'.get_the_title().'"><img data-src="'.$image.'" alt="'.get_the_title().'" /></a></figure>': '' ;
						$output .= $display_link ? '<a href="'.get_permalink().'" >'.get_the_title().'</a>': '' ;
						$output .= '</li>';
					}
					$output .= '</ul></div>';
					echo $output;
				}
				wp_reset_query();
			}
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* Custom Comments Structure
	/*-----------------------------------------------------------------------------------*/
	if ( !function_exists( 'mytheme_comment' ) ) {
		function mytheme_comment($comment, $args, $depth) {
			$GLOBALS['comment'] = $comment;
		?>
		<li <?php comment_class('clearfix'); ?> id="li-comment-<?php comment_ID() ?>">
			<div id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">
				<div class="wrapper">
					<div class="comment-author vcard">
						<?php echo get_avatar( $comment->comment_author_email, 95 ); ?>
						<?php printf('<span class="author">%1$s</span>', get_comment_author_link()) ?>
					</div>
					<?php if ($comment->comment_approved == '0') : ?>
						<em><?php echo theme_locals("your_comment") ?></em>
					<?php endif; ?>
					<div class="extra-wrap">
						<?php comment_text(); ?>

						<div class="extra-wrap">
							<div class="reply">
								<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
							</div>
							<div class="comment-meta commentmetadata"><?php printf('%1$s', get_comment_date()) ?></div>
						</div>
					</div>
				</div>				
			</div>
	<?php }
	}
?>