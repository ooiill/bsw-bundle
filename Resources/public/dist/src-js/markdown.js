'use strict';

bsw.configure({
    logic: {
        init: function init(v) {
            var allLi = $('.markdown-content .index li');
            var anchor = bsw.leftTrim(window.location.hash, '#');
            if (anchor.length) {
                var currentLi = $('li.id-' + anchor);
                var currentMd = $('#' + anchor);
                if (currentLi.length) {
                    $('.markdown-content .index').scrollTop(bsw.offset(currentLi).top);
                    currentLi.addClass('current');
                }
                if (currentMd.length) {
                    $('.markdown-content .content').scrollTop(bsw.offset(currentMd).top);
                }
            }
            allLi.click(function () {
                var thisLi = $(this);
                var url = thisLi.find('a').attr('href');
                var urlItems = bsw.parseQueryString(url, true);
                var currentItems = bsw.parseQueryString(null, true);
                if (urlItems['hostPart'] !== currentItems['hostPart']) {
                    return;
                }
                allLi.removeClass('current');
                thisLi.addClass('current');
                setTimeout(function () {
                    return bsw.prominentAnchor();
                }, 100);
            });
        }
    }
});
