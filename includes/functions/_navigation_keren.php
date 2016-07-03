<?php 
if( ! defined('ABSPATH')) {die('Direct access forbidden'); }

function round_num($num, $to_nearest)
{
	return floor($num/$to_nearest)*$to_nearest;
}

if ( ! function_exists('keren_pagenavi')) :
	function keren_pagenavi($before='', $after='')
{
	global $wpdb, $wp_query;
	$pagenavi_options = array();
	$pagenavi_options['current_text'] = '%PAGE_NUMBER%';
	$pagenavi_options['page_text'] = '%PAGE_NUMBER%';
	$pagenavi_options['first_text'] = 'First Page';
	$pagenavi_options['last_text'] = 'Last Page';
	$pagenavi_options['next_text'] = '>>';
	$pagenavi_options['prev_text'] = '<<';
	$pagenavi_options['dotright_text'] = '...';
	$pagenavi_options['dotleft_text'] = '...';
	$pagenavi_options['num_pages'] = 3;
	$pagenavi_options['always_show'] = 0;
	$pagenavi_options['num_larger_page_numbers'] = 0;
	$pagenavi_options['larger_page_numbers_multiple'] = 5;

	if (!is_single()) {
		$request = $wp_query->request;
		$posts_per_page = intval(get_query_var('posts_per_page'));
		$paged = intval(get_query_var('paged'));
		$numposts = $wp_query->found_posts;
		$max_page = $wp_query->max_num_pages;

		if(empty($paged) || $paged == 0) { $paged = 1; }

		$pages_to_show = intval($pagenavi_options['num_pages']);
		$larger_page_to_show = intval($pagenavi_options['num_larger_page_numbers']);
		$larger_page_multiple = intval($pagenavi_options['larger_page_numbers_multiple']);
		$pages_to_show_minus_1 = $pages_to_show - 1;
		$half_page_start = floor($pages_to_show_minus_1/2);
		$half_page_end = ceil($pages_to_show_minus_1/2);
		$start_page = $paged - $half_page_start;

		if($start_page <= 0) { $start_page = 1; }

		$end_page = $paged + $half_page_end;
		if(($end_page - $start_page) != $pages_to_show_minus_1) { $end_page = $start_page + $pages_to_show_minus_1; }
		if($end_page > $max_page) { $start_page = $max_page - $pages_to_show_minus_1; $end_page = $max_page; }
		if($start_page <= 0) { $start_page = 1; }

		$larger_per_page = $larger_page_to_show*$larger_page_multiple;
		$larger_start_page_start = (round_num($start_page, 10) + $larger_page_multiple) - $larger_per_page;
		$larger_start_page_end = round_num($start_page, 10) + $larger_page_multiple;
		$larger_end_page_start = round_num($end_page, 10) + $larger_page_multiple;
		$larger_end_page_end = round_num($end_page, 10) + ($larger_per_page);

		if($larger_start_page_end - $larger_page_multiple == $start_page) {
			$larger_start_page_start = $larger_start_page_start - $larger_page_multiple;
			$larger_start_page_end = $larger_start_page_end - $larger_page_multiple;
		}
		if($larger_start_page_start <= 0) { $larger_start_page_start = $larger_page_multiple; }
		if($larger_start_page_end > $max_page) { $larger_start_page_end = $max_page; }
		if($larger_end_page_end > $max_page) { $larger_end_page_end = $max_page; }
		if($max_page > 1 || intval($pagenavi_options['always_show']) == 1) {
			echo $before.'<ul class="pagination">'."\n";
			$pages_text = str_replace("%CURRENT_PAGE%", number_format_i18n($paged), $pagenavi_options['pages_text']);
			$pages_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pages_text);
			if(!empty($pages_text)) {
				echo '<li class="disabled">';
				echo '<span>'.$pages_text.'</span>';
				echo '</li>';
			}
			if ( get_previous_posts_link() ) :
				echo '<li>';
			previous_posts_link($pagenavi_options['prev_text']);
			echo '</li>';
			endif;
			if ($start_page >= 2 && $pages_to_show < $max_page) {
				$first_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['first_text']);
				echo '<li><a href="'.esc_url(get_pagenum_link()).'" class="first" title="'.$first_page_text.'">1</a></li>';
				if(!empty($pagenavi_options['dotleft_text'])) {
					echo '<li class="disabled"><span>'.$pagenavi_options['dotleft_text'].'</span></li>';
				}
			}

			if($larger_page_to_show > 0 && $larger_start_page_start > 0 && $larger_start_page_end <= $max_page) {
				for($i = $larger_start_page_start; $i < $larger_start_page_end; $i+=$larger_page_multiple) {
					$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
					echo '<li><a href="'.esc_url(get_pagenum_link($i)).'" title="'.$page_text.'">'.$page_text.'</a></li>';
				}
			}

			for($i = $start_page; $i  <= $end_page; $i++) {
				if($i == $paged) {
					$current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
					$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
					echo '<li class="current"><a href="'.esc_url(get_pagenum_link($i)).'" title="'.$page_text.'">'.$current_page_text.'</a></li>';
				} else {
					$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
					echo '<li><a href="'.esc_url(get_pagenum_link($i)).'" title="'.$page_text.'">'.$page_text.'</a></li>';
				}
			}

			if ($end_page < $max_page) {
				if(!empty($pagenavi_options['dotright_text'])) {
					echo '<li class="disabled"><span>'.$pagenavi_options['dotright_text'].'</span></li>';
				}
				$last_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['last_text']);
				echo '<li><a href="'.esc_url(get_pagenum_link($max_page)).'" title="'.$last_page_text.'">'.$max_page.'</a></li>';
			}

			if($larger_page_to_show > 0 && $larger_end_page_start < $max_page) {
				for($i = $larger_end_page_start; $i <= $larger_end_page_end; $i+=$larger_page_multiple) {
					$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
					echo '<li><a href="'.esc_url(get_pagenum_link($i)).'" title="'.$page_text.'">'.$page_text.'</a></li>';
				}
			}
			if ( get_next_posts_link() ) :
				echo '<li>';
			next_posts_link($pagenavi_options['next_text'], $max_page);
			echo '</li>';
			endif;
			echo '</ul>'.$after."\n";
		}
	}
}
endif;

if( ! function_exists('keren_navigation')){
	function keren_navigation()
	{
		keren_pagenavi();
	}
}
?>