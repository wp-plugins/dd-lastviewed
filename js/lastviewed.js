jQuery(function($) {
    var viewid = $('#LastViewed_ID').attr('data-id'),
        newList = [],
        cookie = $.cookie("lastViewed");

    if (cookie) {
        var oldList = ((cookie)).split(",");
        newList = $.grep(oldList, function(value) {
            return viewid != value;
        });
        newList.splice(39); //set 39 values for max 40 with the push
    }
    newList.push(viewid);
    $.cookie('lastViewed', newList, { expires:30, path:'/' });
});