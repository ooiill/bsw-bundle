bsw.configure({
    logic: {
        init(v) {
            let allLi = $('.markdown-content .index li');
            let anchor = bsw.leftTrim(window.location.hash, '#');
            if (anchor.length) {
                let currentLi = $(`li.id-${anchor}`);
                let currentMd = $(`#${anchor}`);
                if (currentLi.length) {
                    $('.markdown-content .index').scrollTop(bsw.offset(currentLi).top);
                    currentLi.addClass('current');
                }
                if (currentMd.length) {
                    $('.markdown-content .content').scrollTop(bsw.offset(currentMd).top);
                }
            }
            allLi.click(function () {
                let thisLi = $(this);
                let url = thisLi.find('a').attr('href');
                let urlItems = bsw.parseQueryString(url, true);
                let currentItems = bsw.parseQueryString(null, true)
                if (urlItems['hostPart'] !== currentItems['hostPart']) {
                    return;
                }
                allLi.removeClass('current');
                thisLi.addClass('current');
                setTimeout(() => bsw.prominentAnchor(), 100)
            });
        },
    }
});