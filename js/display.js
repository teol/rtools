/**
 * Display / Hide active button on top bar.
 * /!\ app var defined in each page. :/
 */
$(".top-bar-section li").each(function() {
    $(this).removeClass("active");
    if ($(this).attr('app') === app)
        $(this).addClass("active");
});

/**
 * Display / Hide Infobulles.
 * @type {*|jQuery|HTMLElement}
 */
var $help = $('.info');
$('#infoButton').click(function () {
    $(this).parent().toggleClass("active");
    if ($help.css('display') == 'block')
        $help.css('display', 'none');
    else
        $help.css('display', 'block');
});

