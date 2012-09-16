jQuery(function(){
    /**
     * Attach diff popup
     */
    jQuery('a[class=sync_popup]').click(function(e){ 
            window.open(jQuery(this).attr("href"),'diff',"width=700,height=500,left=100,top=100,menubar=no,location=no,resizable=yes,scrollbars=yes,status=no,toolbar=no");
            e.preventDefault();
            return false;
        });

    /**
     * Attach the select all actions
     */
    jQuery('#sync__push').click(function(){ sync_select('push'); }).addClass('sync__action');
    jQuery('#sync__skip').click(function(){ sync_select('skip'); }).addClass('sync__action');
    jQuery('#sync__pull').click(function(){ sync_select('pull'); }).addClass('sync__action');
});

/**
 * Check all radio buttons of the given type
 */
function sync_select(type){
     jQuery('input[class=sync'+type+']').prop('checked',true);
}
