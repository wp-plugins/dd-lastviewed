jQuery(function($) {
    $(document).on('click','.dd-switch', function(){
        $(this).toggleClass('on');
        var value = $(this).is('.on') ? true : false;
        $(this).next('input').attr('checked', value);
    })
});