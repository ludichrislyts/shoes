$.cergis = $.cergis || {};
$.cergis.loadContent = function () {
	$("a").on('click', function (e) {
    pageUrl = $(this).attr('href');
    e.preventDefault();
});
    $('.ajax-loader').show();
    $.ajax({
        url: pageUrl + '?type=ajax',
        success: function (data) {
            $('#main-display').html(data);
            // hide ajax loader
            $('.ajax-loader').hide();
        }
    });
    if (pageUrl != window.location) {
        window.history.pushState({ path: pageUrl }, '', pageUrl);
    }
}
$.cergis.backForwardButtons = function () {
    $(window).on('popstate', function () {
        $.ajax({
            url: location.pathname + '?type=ajax',
            success: function (data) {
                $('#main-display').html(data);
            }
        });
    });
}
$("a").on('click', function (e) {
    pageUrl = $(this).attr('href');
    $.cergis.loadContent();
    e.preventDefault();
});
$.cergis.backForwardButtons();