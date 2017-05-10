<?php
/**
 * The template for displaying search forms in Hit
 *
 * @package Hit
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">	
	<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'hit' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php esc_attr_x( 'Search for:', 'label', 'hit' ); ?>">	<input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'hit' ); ?>">
</form>