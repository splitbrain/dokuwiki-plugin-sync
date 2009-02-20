
addInitEvent(function(){
    var diffs = getElementsByClass('sync_popup',document,'a');

    for(var i=0; i<diffs.length; i++){
        addEvent(diffs[i],'click',function(e){
            window.open(this.href,'diff',"width=700,height=500,left=100,top=100,menubar=no,location=no,resizable=yes,scrollbars=yes,status=no,toolbar=no");
            e.preventDefault();
            e.stopPropagation();
            return false;
        });
    }

});
