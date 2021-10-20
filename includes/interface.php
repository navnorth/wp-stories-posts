<?php
function ordersmenu()
{
	global $wpdb, $wp_locale;

	$taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : '';
	$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';

	$post_type_data = get_post_type_object($post_type);

	if (!taxonomy_exists($taxonomy))
		$taxonomy = '';

	?>
	<div class="wrap">
		<div class="icon32" id="icon-edit"><br></div>
		<h2><?php _e( "Taxonomy Order", SCP_SLUG ) ?></h2>

		<div id="ajax-response"></div>

		<noscript>
			<div class="error message">
				<p><?php _e( "This plugin requires JavaScript in order to function. Please enable JavaScript in your browser to continue.", SCP_SLUG ) ?></p>
			</div>
		</noscript>

		<div class="clear"></div>

		<form action="edit.php" method="get" id="to_form">
			<input type="hidden" name="page" value="ordersmenu-<?php echo $post_type ?>" />
			<?php

			 if ($post_type != 'post')
				echo '<input type="hidden" name="post_type" value="'. $post_type .'" />';

			//output all available taxonomies for this post type

			$post_type_taxonomies = get_object_taxonomies($post_type);

			foreach ($post_type_taxonomies as $key => $taxonomy_name)
			{
				$taxonomy_info = get_taxonomy($taxonomy_name);
				if ($taxonomy_info->hierarchical !== TRUE)
					unset($post_type_taxonomies[$key]);
			}

			//use the first taxonomy if emtpy taxonomy
			if ($taxonomy == '' || !taxonomy_exists($taxonomy))
			{
				reset($post_type_taxonomies);
				$taxonomy = current($post_type_taxonomies);
			}

			if (count($post_type_taxonomies) > 1)
			{
				?>
				<h2 class="subtitle"><?php echo ucfirst($post_type_data->labels->name) ?> <?php _e( "Taxonomies", SCP_SLUG ) ?></h2>
				<table cellspacing="0" class="wp-list-taxonomy">
					<thead>
						<tr>
							<th style="" class="column-cb check-column" id="cb" scope="col">&nbsp;</th>
							<th style="" class="" id="author" scope="col"><?php _e( "Taxonomy Title", SCP_SLUG ) ?></th>
							<th style="" class="manage-column" id="categories" scope="col"><?php _e( "Total Posts", SCP_SLUG ) ?></th>
						</tr>
					</thead>


					<tbody id="the-list">
					<?php

						$alternate = FALSE;
						foreach ($post_type_taxonomies as $post_type_taxonomy)
						{
							$taxonomy_info = get_taxonomy($post_type_taxonomy);
							$alternate = $alternate === TRUE ? FALSE :TRUE;
							$taxonomy_terms = get_terms($key);
						?>
							<tr valign="top" class="<?php if ($alternate === TRUE) {echo 'alternate ';} ?>" id="taxonomy-<?php echo $taxonomy  ?>">
								<th class="check-column" scope="row">
									<input type="radio" onclick="to_change_taxonomy(this)" value="<?php echo $post_type_taxonomy ?>" <?php if ($post_type_taxonomy == $taxonomy) {echo 'checked="checked"';} ?> name="taxonomy">&nbsp;
								</th>
								<td class="categories column-categories"><b>
									<?php echo $taxonomy_info->label ?></b> (<?php echo  $taxonomy_info->labels->singular_name; ?>)
								</td>
								<td class="categories column-categories">
									<?php 
										if (is_array($taxonomy_terms))
											echo count($taxonomy_terms); 
									?>
								</td>
							</tr>
						<?php
						}
					?>
					</tbody>
				</table>
				<br /><br />
			<?php
			}
			?>

		<div id="order-terms">
			<div id="post-body">
					<ul class="sortable" id="tto_sortable">
						<?php
							listTerms($taxonomy);
						?>
					</ul>
					<div class="clear"></div>
			</div>
			<div class="alignleft actions">
				<p class="submit">
					<a href="javascript:;" class="save-order button-primary"><?php _e( "Update", SCP_SLUG ) ?></a>
				</p>
			</div>
		</div>
	</form>

	<script type="text/javascript">
        jQuery(document).ready(function() {

            var NestedSortableSerializedData;
            jQuery("ul.sortable").sortable({
                    'tolerance':'intersect',
                    'cursor':'pointer',
                    'items':'> li',
                    'axi': 'y',
                    'placeholder':'placeholder',
                    'nested': 'ul'
                });
        });


        jQuery(".save-order").bind( "click", function() {

                    var mySortable = new Array();
                    jQuery(".sortable").each(  function()
					{
                        var serialized = jQuery(this).sortable("serialize");
                        var parent_tag = jQuery(this).parent().get(0).tagName;
                        parent_tag = parent_tag.toLowerCase()
                        	if (parent_tag == 'li')
                            {
                                //
                                var tag_id = jQuery(this).parent().attr('id');
                                mySortable[tag_id] = serialized;
                            }
                            else
                            {
                                //
                                mySortable[0] = serialized;
                            }
                    });

                    //serialize the array
                    var serialize_data = serialize(mySortable);

                    jQuery.post( ajaxurl, { action:'update-taxonomy-order', order: serialize_data, taxonomy : '<?php echo  $taxonomy ?>' }, function() {
                        jQuery("#ajax-response").html('<div class="message updated fade"><p><?php _e( "Items Order Updates", SCP_SLUG ) ?></p></div>');
                        jQuery("#ajax-response div").delay(3000).hide("slow");
                    });
                });
    </script>
	</div>
	<?php
}


function listTerms($taxonomy)
{
	// Query pages.
	$args = array(
				'orderby'       =>  'term_order',
				'depth'         =>  0,
				'child_of'      =>  0,
				'hide_empty'    =>  0
	);
	$taxonomy_terms = get_terms($taxonomy, $args);

	$output = '';
	if (count($taxonomy_terms) > 0)
	{
		$output = callwalker($taxonomy_terms, $args['depth'], $args);
	}
	echo $output;
}
function callwalker($taxonomy_terms, $depth, $r)
{
	$walker = new Custom_Terms_Walker;
	$args = array($taxonomy_terms, $depth, $r);
	return call_user_func_array(array(&$walker, 'walk'), $args);
}

// Display settings page
function show_settings_page(){
?>
	<div class="wrap">
		<div class="icon32" id="icon-edit"><br></div>
		<h2><?php _e( "Stories Custom Post Type Settings", SCP_SLUG ) ?></h2>
		<form action="options.php" method="post">
			<?php
			    settings_fields( 'stories-settings-section' );
			    do_settings_sections( 'stories-settings-page' );
			    submit_button();
			?>
		</form>
	</div>
	<div class="plugin-footer">
		<div class="plugin-info"><?php echo SCP_PLUGIN_NAME . " " . SCP_VERSION .""; ?></div>
		<div class="plugin-link"><a href='<?php echo SCP_PLUGIN_INFO ?>' target='_blank'><?php _e( 'More info' , SCP_SLUG ); ?></a></div>
		<div class="clear"></div>
	</div>
<?php
}

?>