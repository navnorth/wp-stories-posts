/** Import Stories **/
function processImport(btn, file) {
    if ( document.getElementById(file).files.length == 0 ) {
            return false;
    }
    if (jQuery('.notice-red').length>0) {
            jQuery('.notice-red').remove();
    }
    if (getFileExtension(document.getElementById(file).value)!=="xls") {
            jQuery('#'+file).closest('div').find('.stories-upload-notice').html('<span class="notice-red">Import file must be in Excel format with .xls extension</span>');
            return false;
    }
    jQuery(btn).prop('value','Processing...');
    jQuery('.import-wrap .import-row input[type=submit]').prop('disabled',true);
    return(true); 
}

/** Check File extension **/
function getFileExtension(filename) {
    return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2);
}