<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Annex
 * @since Annex 1.0
 */
?>

	</div><!-- #main -->

	<footer id="colophon" role="contentinfo">

			<?php
				/* A sidebar in the footer? Yep. You can can customize
				 * your footer with three columns of widgets.
				 */
				get_sidebar( 'footer' );
			?>

			<div id="site-generator">
				<?php do_action( 'annex_credits' ); ?>
				<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'annex' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'annex' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'annex' ), 'WordPress' ); ?></a>
			</div><!-- #site-generator -->
			
			<div id="copyright">
				&copy; <?php echo date("Y"); ?> <a href="<?php echo home_url( '/' ) ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a> All Rights Reserved. <?php annex_credits(); ?>
			</div><!-- #copyright -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>