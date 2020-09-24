$(function () {
    $('div.show-tips').each(function () {
        var content = $(this).next('div.show-tips-hidden').html();
        if (typeof content === 'undefined' || content.length <= 0) {
            return;
        }
        tippy(this, {
            content: decodeURIComponent(content),
            theme: 'light-border',
            animation: 'shift-away',
            inertia: true,
            arrow: true,
            placement: 'right',
            duration: 300,
            hideOnClick: false,
        });
    });
});