<?php
/** Import Page **/
global $wpdb;

$message = null;
$type = null;

//Stories Import
if(isset($_POST['stories_imprt']))
{
	$import_response = importStories();
	if ($import_response){
	    $message = $import_response["message"];
	    $type = $import_response["type"];
	}
}
?>
<div class="wrap">
    <div id="icon-themes" class="icon32"></div>
    <h2 class="heading">Import</h2>
    <?php settings_errors(); ?>
    <div class="import-body">
	<div class="import-row">
		<h2 class="hidden"></h2>
		<?php if ($message) { ?>
		<div class="notice notice-<?php echo $type; ?> is-dismissible">
		    <p><?php echo $message; ?></p>
		</div>
		<?php } ?>
		</div>
		<div class="import-row">
		    <div class="import-wrap">
			<form method="post" enctype="multipart/form-data" onsubmit="return processImport('#stories_submit','stories_import')">
			    <fieldset>
				    <legend><div class="heading"><?php _e("Import Stories", OER_SLUG); ?></div></legend>
				    <div class="import-row">
					<p>Import Stories Spreadsheet Tool</p>
				    </div>
				    <div class="import-row">
					    <div class="row-left">
						    <div class="fields">
							    <input type="file" id="stories_import" name="stories_import"/>
							    <input type="hidden" value="" name="stories_imprt" />
							    <div class="stories-upload-notice"></div>
						    </div>
					    </div>
					    <div class="row-right">
						    <div class="fields alignRight">
							    <input type="submit" id="stories_submit" name="stories_submit" value="<?php _e("Import", OER_SLUG); ?>" class="button button-primary"/>
						    </div>
					    </div>
				    </div>
			    </fieldset>
			</form>
		</div>
	</div>
    </div>
</div><!-- /.wrap -->
<div class="plugin-footer">
	<div class="plugin-info"><?php echo SCP_PLUGIN_NAME . " " . SCP_VERSION .""; ?></div>
	<div class="plugin-link"><a href='<?php echo SCP_PLUGIN_INFO; ?>' target='_blank'><?php _e("More info", OER_SLUG); ?></a></div>
	<div class="clear"></div>
</div>