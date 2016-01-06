<?php /* Wrapper Name: Footer */ ?>
<div class="container">
	<div class="row footer-widgets">
		<div class="span12" data-motopress-type="dynamic-sidebar" data-motopress-sidebar-id="footer-sidebar">
			<?php dynamic_sidebar("footer-sidebar"); ?>
		</div>
	</div>
	<div class="social-nets-wrapper" data-motopress-type="static" data-motopress-static-file="static/static-social-networks.php">
		<?php get_template_part("static/static-social-networks"); ?>
	</div>
	<div class="row copyright">
		<div class="span12">
			<div data-motopress-type="static" data-motopress-static-file="static/static-footer-text.php">
				<?php get_template_part("static/static-footer-text"); ?>
			</div>
			<div data-motopress-type="static" data-motopress-static-file="static/static-footer-nav.php">
				<?php get_template_part("static/static-footer-nav"); ?>
			</div>
		</div>
	</div>
</div>
<?php if (is_front_page()) { ?>
	<div id="map-wrapper" data-motopress-type="static" data-motopress-static-file="static/static-map.php">
		<?php get_template_part("static/static-map"); ?>
	</div>
<?php } ?>