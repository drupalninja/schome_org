<?php
function motopressCEPocketFix($post_id, $post_type) {
    if ($post_type == 'pockets'){
        $single_pocket_layout = get_post_meta( $post_id, 'single_pocket_layout', true);
        if ( empty( $single_pocket_layout ) ){
            update_post_meta($post_id, 'single_pocket_layout', 'pocket-layout-wide');
        }
    }
}
add_action('mp_post_meta', 'motopressCEPocketFix', 10, 2);

function motopressCEAddHeadwayFix($post_id, $tmp_post_id, $post_type) {
    if (defined('HEADWAY_VERSION')) {
        global $wpdb;
        if (
            version_compare(HEADWAY_VERSION, '3.7.10', '>=') ||
            property_exists($wpdb, 'hw_wrappers')
        ) {
            global $wp_query;
            $originalWpQuery = $wp_query;
            $key = ($post_type === 'page') ? 'page_id' : 'p';
            $wp_query = new WP_Query($key . '=' . $post_id);
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                }
            }

            $layoutId = HeadwayLayout::get_current_in_use();
            $layoutTmpId = str_replace($post_id, $tmp_post_id, $layoutId);
            $wrappers = HeadwayWrappersData::get_wrappers_by_layout($layoutId);
            foreach ($wrappers as $wrapper_id => $wrapper) {
                $wrapperId = HeadwayWrappersData::add_wrapper($layoutTmpId, $wrapper);
                $blocks = HeadwayBlocksData::get_blocks_by_wrapper($layoutId, $wrapper_id);
                foreach ($blocks as $block_id => &$block) {
                    $block['wrapper_id'] = $wrapperId;
                    $block['wrapper'] = $wrapperId;
                    HeadwayBlocksData::add_block($layoutTmpId, $block);
                }
                unset($block);
            }

            $transient_id_customized_layouts = 'hw_customized_layouts_template_' . HeadwayOption::$current_skin;
            $customized_layouts = get_transient($transient_id_customized_layouts);
            if (!$customized_layouts) {
                $customized_layouts = array_unique($wpdb->get_col($wpdb->prepare("SELECT layout FROM $wpdb->hw_blocks WHERE template = '%s'", HeadwayOption::$current_skin)));
            }
            if (!in_array($layoutTmpId, $customized_layouts)) {
                $customized_layouts[] = $layoutTmpId;
                set_transient($transient_id_customized_layouts, $customized_layouts);
            }

            wp_reset_postdata();
            $wp_query = $originalWpQuery;
        } else {
            $layout = get_option('headway_layout_options_' . $post_id);
            if ($layout) {
                update_option('headway_layout_options_' . $tmp_post_id, $layout);
            } else {
                delete_option('headway_layout_options_' . $tmp_post_id);
            }
        }
    }
}
add_action('mp_theme_fix', 'motopressCEAddHeadwayFix', 10, 3);

function motopressCERemoveHeadwayFix($post_id, $post_type) {
    if (defined('HEADWAY_VERSION')) {
        global $wpdb;
        if (
            version_compare(HEADWAY_VERSION, '3.7.10', '>=') ||
            property_exists($wpdb, 'hw_wrappers')
        ) {
            global $wp_query;
            $originalWpQuery = $wp_query;
            $key = ($post_type === 'page') ? 'page_id' : 'p';
            $wp_query = new WP_Query($key . '=' . $post_id);
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                }
            }

            $layoutId = HeadwayLayout::get_current_in_use();
            HeadwayWrappersData::delete_by_layout($layoutId);

            $transient_id_customized_layouts = 'hw_customized_layouts_template_' . HeadwayOption::$current_skin;
            $customized_layouts = get_transient($transient_id_customized_layouts);
            if ($customized_layouts) {
                $index = array_search($layoutId, $customized_layouts);
                if ($index !== false) {
                    unset($customized_layouts[$layoutId]);
                    set_transient($transient_id_customized_layouts, $customized_layouts);
                }
            }

            wp_reset_postdata();
            $wp_query = $originalWpQuery;
        }
    }
}