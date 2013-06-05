jQuery(function($) {

    var viewid = $('#LastViewed_ID').attr('data-id');
    var newList = [];

    if (!$.cookie("lastViewed")) {
        newList.push(viewid);
        $.cookie('lastViewed',newList, { expires:30, path:'/' });
    }
    else {

        var oldList = (($.cookie("lastViewed"))).split(",");

        newList = $.grep(oldList, function(value) {
            return viewid != value;
        });
        newList.splice(19); //set 19 values for max 20 with the push
        newList.push(viewid);//now 20 values
        $.cookie('lastViewed', newList, { expires:30, path:'/' });
    }
});