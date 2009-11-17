
addInitEvent(function(){
    /**
     * Attach diff popup
     */
    var diffs = getElementsByClass('sync_popup',document,'a');
    for(var i=0; i<diffs.length; i++){
        addEvent(diffs[i],'click',function(e){
            window.open(this.href,'diff',"width=700,height=500,left=100,top=100,menubar=no,location=no,resizable=yes,scrollbars=yes,status=no,toolbar=no");
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
    }

    /**
     * Attach the select all actions
     */
    var push = $('sync__push');
    if(push){
        push.style.cursor = 'pointer';
        addEvent(push,'click',function(){ sync_select('push'); });
    }
    var skip = $('sync__skip');
    if(skip){
        skip.style.cursor = 'pointer';
        addEvent(skip,'click',function(){ sync_select('skip'); });
    }
    var pull = $('sync__pull');
    if(pull){
        pull.style.cursor = 'pointer';
        addEvent(pull,'click',function(){ sync_select('pull'); });
    }

});

/**
 * Check all radio buttons of the given type
 */
function sync_select(type){
    var items = getElementsByClass('sync'+type);
    for(var i=0; i<items.length; i++){
        items[i].checked = 'checked';
    }
}
