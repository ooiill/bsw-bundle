//
// Copyright 2019
//

//
// Foundation for prototype
//

class FoundationPrototype {

    /**
     * String trim
     *
     * @param source
     * @param haystack
     *
     * @return {string}
     */
    trim(source, haystack) {
        haystack = haystack ? ('\\s' + haystack) : '\\s';
        return source.replace(new RegExp('(^[' + haystack + ']*)|([' + haystack + ']*$)', 'g'), '');
    }

    /**
     * String left trim
     *
     * @param source
     * @param haystack
     *
     * @return {string}
     */
    leftTrim(source, haystack) {
        haystack = haystack ? ('\\s' + haystack) : '\\s';
        return source.replace(new RegExp('(^[' + haystack + ']*)', 'g'), '');
    }

    /**
     * String right trim
     *
     * @param source
     * @param haystack
     *
     * @return {string}
     */
    rightTrim(source, haystack) {
        haystack = haystack ? ('\\s' + haystack) : '\\s';
        return source.replace(new RegExp('([' + haystack + ']*$)', 'g'), '');
    }

    /**
     * String pad
     *
     * @param target
     * @param padStr
     * @param length
     * @param type
     *
     * @return {*}
     */
    pad(target, padStr, length, type) {
        padStr = padStr.toString();
        type = type || 'left';

        if (target.length >= length || !['left', 'right', 'both'].contains(type)) {
            return target;
        }

        let padNum, _padNum;
        let last = (length - target.length) % padStr.length;
        padNum = _padNum = Math.floor((length - target.length) / padStr.length);

        if (last > 0) {
            padNum += 1;
        }

        let _that = target;
        for (let i = 0; i < padNum; i++) {
            if (i === _padNum) {
                padStr = padStr.substr(0, last);
            }
            switch (type) {
                case 'left':
                    _that = padStr + _that;
                    break;
                case 'right':
                    _that += padStr;
                    break;
                case 'both':
                    _that = (0 === i % 2) ? (padStr + _that) : (_that + padStr);
                    break;
            }
        }

        return _that;
    };

    /**
     * String fill
     *
     * @param target
     * @param fillStr
     * @param length
     * @param type
     *
     * @return {*}
     */
    fill(target, fillStr, length, type) {
        fillStr = fillStr.toString();
        type = type || 'left';

        if (length < 1 || !['left', 'right', 'both'].contains(type)) {
            return target;
        }

        let _that = target;
        for (let i = 0; i < length; i++) {
            switch (type) {
                case 'left':
                    _that = fillStr + _that;
                    break;
                case 'right':
                    _that += fillStr;
                    break;
                case 'both':
                    _that = (0 === i % 2) ? (fillStr + _that) : (_that + fillStr);
                    break;
            }
        }

        return _that;
    };

    /**
     * String repeat
     *
     * @param target
     * @param num
     *
     * @return {string}
     */
    repeat(target, num) {
        num = (isNaN(num) || num < 1) ? 1 : num + 1;
        return new Array(num).join(target);
    };

    /**
     * String upper first char of words
     *
     * @param target
     *
     * @return {*}
     */
    ucWords(target) {
        return target.replace(/\b(\w)+\b/g, function (word) {
            return word.replace(word.charAt(0), word.charAt(0).toUpperCase());
        });
    };

    /**
     * String upper first char
     *
     * @param target
     *
     * @return {*}
     */
    ucFirst(target) {
        return target.replace(target.charAt(0), target.charAt(0).toUpperCase());
    };

    /**
     * String lower first char
     *
     * @param target
     *
     * @return {*}
     */
    lcFirst(target) {
        return target.replace(target.charAt(0), target.charAt(0).toLowerCase());
    };

    /**
     * String big hump style
     *
     * @param target
     * @param split
     *
     * @return {*}
     */
    bigHump(target, split = '_') {
        let reg = new RegExp(split, 'g');
        return this.ucWords(target.replace(reg, ' ')).replace(/ /g, '');
    };

    /**
     * String small hump style
     *
     * @param target
     * @param split
     *
     * @return {*}
     */
    smallHump(target, split = '_') {
        return this.lcFirst(this.bigHump(target, split));
    };

    /**
     * String hump to under
     *
     * @param target
     * @param split
     *
     * @returns {*}
     */
    humpToUnder(target, split = '_') {
        return this.leftTrim(target.replace(/([A-Z])/g, `${split}$1`).toLowerCase(), split);
    };

    /**
     * Date format
     *
     * @param target
     * @param fmt
     *
     * @return {*}
     */
    format(target, fmt = 'yyyy-MM-dd hh:mm:ss') {
        let o = {
            'M+': target.getMonth() + 1,
            'd+': target.getDate(),
            'h+': target.getHours(),
            'm+': target.getMinutes(),
            's+': target.getSeconds(),
            'q+': Math.floor((target.getMonth() + 3) / 3),
            'S': target.getMilliseconds()
        };

        if (/(y+)/.test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (target.getFullYear() + '').substr(4 - RegExp.$1.length));
        }

        for (let k in o) {
            if (!o.hasOwnProperty(k)) {
                continue;
            }
            if (new RegExp('(' + k + ')').test(fmt)) {
                fmt = fmt.replace(
                    RegExp.$1,
                    (RegExp.$1.length === 1) ? (o[k]) : (('00' + o[k]).substr(('' + o[k]).length))
                );
            }
        }

        return fmt;
    };

    /**
     * Array unique
     *
     * @param target
     *
     * @returns {*}
     */
    arrayUnique(target) {
        return Array.from(new Set(target));
    }

    /**
     * Array remove by value
     *
     * @param target
     * @param value
     *
     * @return {*}
     */
    arrayRemoveValue(target, value) {
        let index = target.indexOf(value);
        if (index > -1) {
            target.splice(index, 1);
        }
        return target;
    }

    /**
     * Array intersect
     *
     * @param first
     * @param second
     *
     * @return {*}
     */
    arrayIntersect(first, second) {
        return first.filter((v) => second.indexOf(v) > -1);
    }

    /**
     * Array difference
     *
     * @param first
     * @param second
     *
     * @return {*}
     */
    arrayDifference(first, second) {
        return first.filter((v) => second.indexOf(v) === -1);
    }

    /**
     * Array complement
     *
     * @param first
     * @param second
     *
     * @return {*}
     */
    arrayComplement(first, second) {
        return first.filter((v) => !(second.indexOf(v) > -1)).concat(second.filter((v) => !(first.indexOf(v) > -1)));
    }

    /**
     * Array union
     *
     * @param first
     * @param second
     *
     * @return {*}
     */
    arrayUnion(first, second) {
        return first.concat(second.filter((v) => !(first.indexOf(v) > -1)));
    }

    /**
     * Array swap
     *
     * @param source
     * @param first
     * @param last
     *
     * @return {array}
     */
    swap(source, first, last) {
        source[first] = source.splice(last, 1, source[first])[0];
        return source;
    }

    /**
     * Array up
     *
     * @param source
     * @param index
     *
     * @return {array}
     */
    up(source, index) {
        if (index === 0) {
            return source;
        }
        return this.swap(source, index, index - 1);
    }

    /**
     * Array down
     *
     * @param source
     * @param index
     *
     * @return {array}
     */
    down(source, index) {
        if (index === source.length - 1) {
            return source;
        }
        return this.swap(source, index, index + 1);
    }
}

//
// Foundation for tools
//

class FoundationTools extends FoundationPrototype {

    /**
     * Blank fn
     */
    blank() {
    }

    /**
     * Is array
     *
     * @param value
     *
     * @return {boolean}
     */
    isArray(value) {
        if (null === value) {
            return false;
        }
        return typeof value === 'object' && value.constructor === Array;
    }

    /**
     * Is object
     *
     * @param value
     *
     * @return {boolean}
     */
    isObject(value) {
        if (null === value) {
            return false;
        }
        return typeof value === 'object' && value.constructor === Object;
    }

    /**
     * Is null
     *
     * @param value
     *
     * @return {boolean}
     */
    isNull(value) {
        if (value) {
            return false;
        }
        return typeof value !== 'undefined' && value !== 0;
    }

    /**
     * Is json
     *
     * @param value
     *
     * @return {boolean}
     */
    isJson(value) {
        if (null === value) {
            return false;
        }
        return typeof value === 'object' && Object.prototype.toString.call(value).toLowerCase() === '[object object]';
    }

    /**
     * Is string
     *
     * @param value
     *
     * @return {boolean}
     */
    isString(value) {
        if (null === value) {
            return false;
        }
        return typeof value === 'string' && value.constructor === String;
    }

    /**
     * Is numeric
     *
     * @param value
     *
     * @return {boolean}
     */
    isNumeric(value) {
        if (null === value || '' === value) {
            return false;
        }
        return !isNaN(value);
    }

    /**
     * Is boolean
     *
     * @param value
     *
     * @return {boolean}
     */
    isBoolean(value) {
        if (null === value) {
            return false;
        }
        return typeof value === 'boolean' && value.constructor === Boolean;
    }

    /**
     * Is function
     *
     * @param value
     *
     * @return {boolean}
     */
    isFunction(value) {
        if (null === value) {
            return false;
        }
        return typeof value === 'function' && Object.prototype.toString.call(value).toLowerCase() === '[object function]';
    }

    /**
     * Get json length
     *
     * @param target
     *
     * @return {number}
     */
    jsonLength(target) {
        let length = 0;
        for (let i in target) {
            if (!target.hasOwnProperty(i)) {
                continue;
            }
            length++;
        }
        return length;
    }

    /**
     * Sort json array
     *
     * @param array
     * @param field
     * @param desc
     *
     * @returns {*}
     */
    sortJsonArray(array, field, desc = false) {
        if (array.length < 2 || !field || typeof array[0] !== 'object') {
            return array;
        }
        if (typeof array[0][field] === "number") {
            array.sort(function (x, y) {
                return x[field] - y[field]
            });
        }
        if (typeof array[0][field] === "string") {
            array.sort(function (x, y) {
                return x[field].localeCompare(y[field])
            });
        }
        if (desc) {
            array.reverse();
        }
        return array;
    }

    /**
     * Sort json
     *
     * @param object
     * @returns {{}}
     */
    sortJson(object) {
        let keys = [];
        for (let key in object) {
            if (!object.hasOwnProperty(key)) {
                continue;
            }
            keys.push(key)
        }
        keys.sort();
        let target = {};
        for (let key in keys) {
            if (!keys.hasOwnProperty(key)) {
                continue;
            }
            let k = keys[key];
            target[k] = object[k];
        }
        return target;
    }

    /**
     * Get element offset
     *
     * @param element
     *
     * @return {{left: *, top: *, width: number, height: number}}
     */
    offset(element) {
        element = element.jquery ? element : $(element);
        let pos = element.offset();
        return {
            left: pos.left,
            right: document.body.offsetWidth - (pos.left + element[0].offsetWidth),
            top: pos.top,
            bottom: document.body.offsetHeight - (pos.top + element[0].offsetHeight),
            width: element[0].offsetWidth,
            height: element[0].offsetHeight
        };
    }

    /**
     * Timestamp
     *
     * @param second
     *
     * @return {int}
     */
    timestamp(second = false) {
        let time = new Date().getTime();
        return second ? Math.ceil(time / 1000) : time;
    }

    /**
     * Parse query string
     *
     * @param url
     * @param hostPart
     *
     * @returns {array}
     */
    parseQueryString(url = null, hostPart = false) {
        url = decodeURIComponent(url || location.href);
        if (url.indexOf('#') === -1) {
            url = url + '#';
        }

        let items = {};
        let urlArr = url.split('#');
        items['anchorPart'] = urlArr[1];
        url = urlArr[0];

        if (url.indexOf('?') === -1) {
            url = url + '?';
        }

        urlArr = url.split('?');
        if (hostPart) {
            items['hostPart'] = urlArr[0];
        }

        url = urlArr[1];
        if (url.length === 0) {
            return items;
        }

        if (url.indexOf('#')) {
            url = url.split('#')[0];
        }

        url = url.split('&');
        for (let item of url) {
            item = item.split('=');
            items[item[0]] = item[1];
        }

        return items;
    }

    /**
     * Json to query string
     *
     * @param source
     * @param returnObject
     * @param needEncode
     *
     * @return {string}
     */
    jsonBuildQuery(source, returnObject = false, needEncode = true) {
        let query = '', _query = {}, name, value, fullSubName, subName, subValue, innerObject, i;
        source = this.sortJson(source);
        for (let name in source) {
            if (!source.hasOwnProperty(name)) {
                continue;
            }
            value = source[name];

            if (this.isArray(value)) {
                for (i = 0; i < value.length; ++i) {
                    subValue = value[i];
                    fullSubName = name + '[' + i + ']';
                    innerObject = {};
                    innerObject[fullSubName] = subValue;
                    query += this.jsonBuildQuery(innerObject, returnObject, needEncode) + '&';
                    _query = Object.assign(_query, this.jsonBuildQuery(innerObject, returnObject, needEncode));
                }
            } else if (this.isObject(value)) {
                for (subName in value) {
                    if (!value.hasOwnProperty(subName)) {
                        continue;
                    }
                    subValue = value[subName];
                    fullSubName = name + '[' + subName + ']';
                    innerObject = {};
                    innerObject[fullSubName] = subValue;
                    query += this.jsonBuildQuery(innerObject, returnObject, needEncode) + '&';
                    _query = Object.assign(_query, this.jsonBuildQuery(innerObject, returnObject, needEncode));
                }
            } else if (value !== undefined && value !== null) {
                if (needEncode) {
                    name = encodeURIComponent(name);
                    value = encodeURIComponent(value);
                }
                query += `${name}=${value}&`;
                _query[name] = value;
            }
        }

        if (returnObject) {
            return _query;
        }

        return query.length ? query.substr(0, query.length - 1) : query;
    }


    /**
     * Url add items
     *
     * @param items
     * @param url
     * @param needEncode
     *
     * @return {string}
     */
    setParams(items, url = null, needEncode = false) {
        let queryParams = this.parseQueryString(url, true);
        let host = queryParams.hostPart;
        let anchor = queryParams.anchorPart;
        delete queryParams.hostPart;
        delete queryParams.anchorPart;

        items = Object.assign(queryParams, this.jsonBuildQuery(items, true, needEncode));
        let queryString = this.jsonBuildQuery(items);
        url = `${host}?${queryString}`;
        if (anchor.length) {
            url = this.trim(url, '?') + '#' + anchor;
        }

        return this.trim(url, '?');
    }

    /**
     * Url remove items
     *
     * @param items
     * @param url
     * @param needEncode
     * @param effect
     *
     * @return {string}
     */
    unsetParams(items, url, needEncode = false, effect = {}) {
        url = url || location.href;
        let queryParams = this.parseQueryString(url, true);

        for (let v of (items || [])) {
            if (typeof queryParams[v] !== 'undefined') {
                effect[v] = queryParams[v];
                delete queryParams[v];
            }
        }

        let host = queryParams.hostPart;
        let anchor = queryParams.anchorPart;
        delete queryParams.hostPart;
        delete queryParams.anchorPart;

        url = host + '?' + this.jsonBuildQuery(queryParams, needEncode);
        if (anchor.length) {
            url = this.trim(url, '?') + '#' + anchor;
        }

        return this.trim(url, '?');
    }

    /**
     * Url remove items
     *
     * @param items
     * @param url
     * @param needEncode
     * @param effect
     *
     * @return {string}
     */
    unsetParamsBeginWith(items, url, needEncode = false, effect = {}) {
        url = url || location.href;
        let queryParams = this.parseQueryString(url, true);

        for (let v of (items || [])) {
            for (let w in queryParams) {
                if (!queryParams.hasOwnProperty(w)) {
                    continue;
                }
                if (w.startsWith(v)) {
                    effect[w] = queryParams[w];
                    delete queryParams[w];
                }
            }
        }

        let host = queryParams.hostPart;
        let anchor = queryParams.anchorPart;
        delete queryParams.hostPart;
        delete queryParams.anchorPart;

        url = host + '?' + this.jsonBuildQuery(queryParams, needEncode);
        if (anchor.length) {
            url = this.trim(url, '?') + '#' + anchor;
        }

        return this.trim(url, '?');
    }

    /**
     * Count px of padding and margin
     *
     * @param parentElement
     * @param element
     * @returns {{column: number, row: number}}
     */
    pam(parentElement, element) {
        let px = {
            row: 0,
            column: 0
        };
        for (let m of ['margin', 'padding', 'border']) {
            if (typeof px[m] === 'undefined') {
                px[m] = {};
            }
            for (let n of ['left', 'right', 'top', 'bottom']) {
                if (m === 'border') {
                    px[m][n] = parseInt(parentElement.css(`${m}-${n}-width`));
                } else {
                    px[m][n] = parseInt(parentElement.css(`${m}-${n}`));
                }
                if (n === 'left' || n === 'right') {
                    px.row += px[m][n];
                } else if (n === 'top' || n === 'bottom') {
                    px.column += px[m][n];
                }
            }
        }
        if (element) {
            for (let n of ['left', 'right', 'top', 'bottom']) {
                if (n === 'left' || n === 'right') {
                    px.row += parseInt(element.css(`border-${n}-width`));
                } else if (n === 'top' || n === 'bottom') {
                    px.column += parseInt(element.css(`border-${n}-width`));
                }
            }
        }

        return px;
    }

    /**
     * Device checker
     *
     * @return {{}}
     */
    device() {
        let u = navigator.userAgent;
        return {
            ie: u.indexOf('Trident') > -1,
            opera: u.indexOf('Presto') > -1,
            chrome: u.indexOf('AppleWebKit') > -1,
            firefox: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') === -1,
            mobile: !!u.match(/AppleWebKit.*Mobile.*/),
            ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),
            android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1,
            iPhone: u.indexOf('iPhone') > -1,
            iPad: u.indexOf('iPad') > -1,
            webApp: u.indexOf('Safari') === -1,
            version: navigator.appVersion
        };
    }

    /**
     * Mobile check
     *
     * @returns boolean
     */
    isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    /**
     * Rand a number
     *
     * @param end
     * @param begin
     * @return {*}
     */
    rand(end, begin) {
        begin = begin || 0;
        let rank = begin;
        let _end = end - rank;

        return parseInt(Number(Math.random() * _end).toFixed(0)) + rank;
    }

    /**
     * Bind key down
     *
     * @param num
     * @param callback
     * @param element
     * @param ctrl
     */
    keyBind(num, callback, element, ctrl = false) {
        element = element || $(document);
        element.unbind('keydown').bind('keydown', function (event) {
            if (ctrl) {
                if (event.keyCode === num && event.ctrlKey && callback) {
                    callback();
                }
            } else {
                if (event.keyCode === num && callback) {
                    callback();
                }
            }
        });
    }

    /**
     * Device media
     *
     * @return {{}}
     */
    media() {
        let width = document.body.clientWidth;
        return {
            'xs': width < 576,
            'sm': width >= 576,
            'md': width >= 768,
            'lg': width >= 992,
            'xl': width >= 1200,
            'xxl': width >= 1600,
        };
    }

    /**
     * Int value
     *
     * @param value
     *
     * @return int
     */
    parseInt(value) {
        value = parseInt(value);
        return isNaN(value) ? 0 : value;
    }

    /**
     * Cookie tools
     *
     * @return {{}}
     */
    cookie() {
        return {
            set: function (name, value, time = 86400 * 365, domain = null) {
                let expires = new Date();
                expires.setTime(expires.getTime() + time * 1000);
                if (!domain) {
                    domain = '';
                } else {
                    domain = '; domain=' + domain;
                }
                document.cookie = name + '=' + encodeURI(value) + '; expires=' + expires.toUTCString() + '; path=/' + domain;
                return value;
            },
            get: function (name, def = false) {
                let cookieArray = document.cookie.split('; ');
                for (let i = 0; i < cookieArray.length; i++) {
                    let arr = cookieArray[i].split('=');
                    if (arr[0] === name) {
                        return unescape(arr[1]);
                    }
                }
                return def;
            },
            delete: function (name, domain = null) {
                if (!domain) {
                    domain = '';
                } else {
                    domain = '; domain=' + domain;
                }
                document.cookie = name + '=; expires=' + (new Date(0)).toUTCString() + '; path=/' + domain;
            }
        };
    }

    /**
     * Get next value with map use cookie
     *
     * @param name
     * @param map
     * @param def
     * @param set
     * @param tips
     *
     * @return mixed
     */
    cookieMapNext(name, map, def, set = false, tips = null) {
        let ck = this.cookie();
        let current = ck.get(name, def);
        let next = map[current] || def;

        if (tips) {
            let _next = this.ucFirst(next);
            this.message('success', `${tips}: ${_next}`);
        }

        return set ? ck.set(name, next) : next;
    }

    /**
     * Get current value with map use cookie
     *
     * @param name
     * @param map
     * @param def
     *
     * @return mixed
     */
    cookieMapCurrent(name, map, def) {
        let current = this.cookie().get(name, def);

        return map[current] ? current : def;
    }

    /**
     * Eval expression
     *
     * @param expression
     * @param def
     *
     * @returns {{}}
     */
    evalExpr(expression, def = null) {
        let data = null;
        try {
            data = window.eval(`(${expression})`);
        } catch (e) {
            console.warn(`Expression has syntax error: ${expression}`);
            console.warn(e);
        }

        return data ? data : def;
    }

    /**
     * Switch class name
     *
     * @param cls
     * @param add
     * @param selector
     */
    switchClass(cls, add, selector = 'html') {
        let container = $(selector);
        container.removeClass(cls);
        add === 'yes' && container.addClass(cls);
    }

    /**
     * Check json keys exists
     *
     * @param target
     * @param keys
     *
     * @returns boolean
     */
    checkJsonDeep(target, keys) {
        let origin = target;
        keys = keys.split('.');
        for (let key of keys) {
            if (typeof origin[key] === 'undefined' || !origin[key]) {
                return false;
            }
            origin = origin[key];
        }

        return true;
    }

    /**
     * Set json value by deep
     *
     * @param target
     * @param keys
     * @param value
     *
     * @returns boolean
     */
    setJsonDeep(target, keys, value) {
        let origin = target;
        keys = keys.split('.');
        for (let key of keys) {
            let last = key === keys[keys.length - 1];
            if (typeof origin[key] === 'undefined' || !origin[key]) {
                origin[key] = last ? value : {};
            } else {
                origin[key] = last ? value : origin[key];
            }
            origin = origin[key];
        }
    }

    /**
     * Json filter
     *
     * @param target
     * @param filter
     */
    jsonFilter(target, filter = ['', null]) {
        for (let i in target) {
            if (!target.hasOwnProperty(i)) {
                continue;
            }
            if (filter.indexOf(target[i]) !== -1) {
                delete target[i];
            }
        }
        return target;
    }

    /**
     * Clone json
     *
     * @param target
     * @returns {[]|{}}
     */
    cloneJson(target) {
        let newObj = Array.isArray(target) ? [] : {};
        if (target && typeof target === "object") {
            for (let key in target) {
                if (target.hasOwnProperty(key)) {
                    newObj[key] = (target && typeof target[key] === 'object') ? this.cloneJson(target[key]) : target[key];
                }
            }
        }
        return newObj
    }

    /**
     * Throttle
     *
     * @param {number} delay
     * @param {boolean} [noTrailing]
     * @param {Function} callback
     * @param {boolean} [debounceMode]
     *
     * @returns {Function}
     */
    throttle(delay, noTrailing, callback, debounceMode) {
        let timeoutID;
        let cancelled = false;
        let lastExec = 0;

        function clearExistingTimeout() {
            if (timeoutID) {
                clearTimeout(timeoutID);
            }
        }

        function cancel() {
            clearExistingTimeout();
            cancelled = true;
        }

        if (typeof noTrailing !== 'boolean') {
            debounceMode = callback;
            callback = noTrailing;
            noTrailing = undefined;
        }

        function wrapper(...arguments_) {
            let self = this;
            let elapsed = Date.now() - lastExec;
            if (cancelled) {
                return;
            }

            function exec() {
                lastExec = Date.now();
                callback.apply(self, arguments_);
            }

            function clear() {
                timeoutID = undefined;
            }

            if (debounceMode && !timeoutID) {
                exec();
            }
            clearExistingTimeout();
            if (debounceMode === undefined && elapsed > delay) {
                exec();
            } else if (noTrailing !== true) {
                timeoutID = setTimeout(
                    debounceMode ? clear : exec,
                    debounceMode === undefined ? delay - elapsed : delay
                );
            }
        }

        wrapper.cancel = cancel;
        return wrapper;
    }

    /**
     * Debounce
     *
     * @param {number} delay
     * @param {boolean|Function} [atBegin]
     * @param {Function} callback
     *
     * @returns {Function}
     */
    debounce(delay, atBegin, callback) {
        if (typeof callback === 'undefined') {
            return bsw.throttle(delay, atBegin, false);
        }
        return bsw.throttle(delay, callback, atBegin !== false);
    }
}

//
// Foundation for AntD
//

class FoundationAntD extends FoundationTools {

    /**
     * Constructor
     *
     * @param jQuery
     * @param Vue
     * @param AntD
     * @param lang
     */
    constructor(jQuery, Vue, AntD, lang = {}) {
        super();
        this.v = Vue;
        this.d = AntD;
        this.config = {};
        this.lang = lang;
        this.cnf = {
            menuTheme: 'dark',
            menuWidth: 256,
            menuCollapsed: false,
            menuThemeMap: {dark: 'light', light: 'dark'},
            opposeMap: {yes: 'no', no: 'yes'},
            mobileDefaultCollapsed: true,
            weak: 'no',
            thirdMessage: 'no',
            thirdMessageSecond: 15,
            requestTimeout: 30,
            requestRetryTimes: 3,
            notificationDuration: 3,
            messageDuration: 3,
            confirmDuration: 3,
            confirmWidth: 350,
            alertType: 'message',
            alertTypeForce: null,
            maxZIndex: 1000,
            notificationPlacement: 'topRight',
            transitionName: 'bsw-zoom',
            maskTransitionName: 'fade',
            cosyMinWidth: 1285,
            cosyMinWidthLess: .95,
            cosyMinWidthMore: .65,
            cosyMinHeight: 666,
            cosyMinHeightLess: .95,
            cosyMinHeightMore: .75,
            autoHeightDuration: 100,
            autoHeightOffset: 0,
            autoHeightOverOffset: 'yes',
            scrollXMinHeight: 300,
            scrollXFadeDuration: 100,
            v: null,
            method: {
                get: 'GET',
                post: 'POST'
            }
        };
    }

    /**
     * Page configure
     *
     * @param config
     */
    configure(config) {
        for (let key in config) {
            if (!config.hasOwnProperty(key)) {
                continue;
            }
            bsw.config[key] = Object.assign(bsw.config[key] || {}, config[key]);
        }
    }

    /**
     * Init with VUE
     *
     * @param selector
     *
     * @return {*}
     */
    vue(selector = '.bsw-vue') {
        let that = this;
        let conf = {};
        return {
            template(item) {
                item && (conf.template = item);
                return this;
            },
            data(item = {}) {
                conf.data = () => item;
                return this;
            },
            computed(item = {}) {
                conf.computed = item;
                return this;
            },
            method(item = {}) {
                conf.methods = item;
                return this;
            },
            component(item = {}) {
                conf.components = item;
                return this;
            },
            directive(item = {}) {
                conf.directives = item;
                return this;
            },
            watch(item = {}) {
                conf.watch = item;
                return this;
            },
            extra(options = {}) {
                conf = Object.assign(conf, options);
                return this;
            },
            init(logic = bsw.blank) {
                conf.el = selector;
                that.cnf.v = new that.v(conf);
                that.cnf.v.$nextTick(function () {
                    // logic
                    for (let fn in that.config.logic || []) {
                        if (!that.config.logic.hasOwnProperty(fn)) {
                            continue;
                        }
                        that.config.logic[fn](that.cnf.v);
                    }
                });
                that.changeImageCaptcha();
                logic(that.cnf.v);
            }
        };
    }

    /**
     * Show use notification
     *
     * @param type
     * @param description
     * @param duration
     * @param onClose
     *
     * @returns {*}
     */
    notification(type, description, duration, onClose = bsw.blank) {
        let that = this;
        if (that.cnf.alertTypeForce && that.cnf.alertTypeForce !== 'notification') {
            return that[that.cnf.alertTypeForce](type, description, duration, onClose);
        }

        if (typeof duration === 'undefined') {
            duration = that.cnf.notificationDuration;
        }

        let message = {
            success: that.lang.success,
            info: that.lang.info,
            warning: that.lang.warning,
            error: that.lang.error,
        }[type];

        return new Promise(function (resolve) {
            that.cnf.v.$notification[type]({
                placement: that.cnf.notificationPlacement,
                message,
                description,
                duration,
                onClose() {
                    resolve();
                    onClose && onClose();
                },
            });
        });
    }

    /**
     * Show use message
     *
     * @param type
     * @param description
     * @param duration
     * @param onClose
     *
     * @returns {*}
     */
    message(type, description, duration, onClose = bsw.blank) {
        let that = this;
        if (that.cnf.alertTypeForce && that.cnf.alertTypeForce !== 'message') {
            return that[that.cnf.alertTypeForce](type, description, duration, onClose);
        }
        if (typeof duration === 'undefined') {
            duration = this.cnf.messageDuration;
        }
        return this.cnf.v.$message[type](description, duration, onClose);
    }

    /**
     * Show use confirm
     *
     * @param type
     * @param description
     * @param duration
     * @param onClose
     * @param options
     *
     * @returns {*}
     */
    confirm(type, description, duration, onClose = bsw.blank, options = {}) {
        let that = this;
        if (that.cnf.alertTypeForce && that.cnf.alertTypeForce !== 'confirm') {
            return that[that.cnf.alertTypeForce](type, description, duration, onClose);
        }

        let title = options.title || {
            success: that.lang.success,
            info: that.lang.info,
            warning: that.lang.warning,
            error: that.lang.error,
        }[type];

        if (type === 'confirm' && typeof options.width === 'undefined') {
            options.width = that.popupCosySize().width;
        }

        return new Promise(function (resolve) {
            let modal = that.cnf.v[`$${type}`](Object.assign({
                title,
                content: description,
                okText: that.lang.i_got_it,
                onOk: options.onOk || onClose,
                onCancel: onClose,
                keyboard: false,
                transitionName: that.cnf.transitionName,
                maskTransitionName: that.cnf.maskTransitionName,
            }, options));

            if (typeof duration === 'undefined') {
                duration = that.cnf.confirmDuration;
            }
            if (duration) {
                setTimeout(function () {
                    modal.destroy();
                    resolve(modal);
                }, duration * 1000);
            }
        });
    }

    /**
     * Show success
     *
     * @param description
     * @param duration
     * @param onClose
     * @param type
     *
     * @returns {*}
     */
    success(description, duration, onClose, type) {
        return this[type || bsw.cnf.alertType]('success', description, duration, onClose);
    }

    /**
     * Show info
     *
     * @param description
     * @param duration
     * @param onClose
     * @param type
     *
     * @returns {*}
     */
    info(description, duration, onClose, type) {
        return this[type || bsw.cnf.alertType]('info', description, duration, onClose);
    }

    /**
     * Show warning
     *
     * @param description
     * @param duration
     * @param onClose
     * @param type
     *
     * @returns {*}
     */
    warning(description, duration, onClose, type) {
        return this[type || bsw.cnf.alertType]('warning', description, duration, onClose);
    }

    /**
     * Show error
     *
     * @param description
     * @param duration
     * @param onClose
     * @param type
     *
     * @returns {*}
     */
    error(description, duration, onClose, type) {
        return this[type || bsw.cnf.alertType]('error', description, duration, onClose);
    }

    /**
     * Request
     *
     * @param url
     * @param data
     * @param type
     * @param upload
     * @param times
     *
     * @returns {Promise}
     */
    request(url, data = {}, type = bsw.cnf.method.post, upload = false, times = 1) {
        let that = this;
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: upload ? that.cnf.method.post : type,
                data,
                url,
                processData: !upload,
                contentType: upload ? false : 'application/x-www-form-urlencoded',
                timeout: that.cnf.requestTimeout * 1000 * (upload ? 10 : 1),
                beforeSend: function () {
                    if (that.cnf.v.noLoadingOnce) {
                        that.cnf.v.noLoadingOnce = false;
                    } else {
                        that.cnf.v.spinning = true;
                    }
                },
                success: function (data) {
                    that.cnf.v.spinning = false;
                    resolve(data);
                },
                error: function (obj) {
                    that.cnf.v.spinning = false;
                    if (obj.responseJSON) {
                        let result = obj.responseJSON;
                        let message = `[${result.code}] ${result.message}`;
                        return that.confirm(result.classify, message, 0);
                    }

                    if (obj.responseText) {
                        let message = `[${obj.status}] ${obj.statusText}`;
                        return that.confirm('error', message, 0);
                    }

                    if (obj.statusText === 'timeout') {
                        console.warn('Client request timeout: ', obj);
                        console.warn(`Retry current request in times ${times}`);
                        if (times <= that.cnf.requestRetryTimes) {
                            return that.request(url, data, type, upload, ++times);
                        }
                    }
                    reject(obj);
                }
            });
        });
    }

    /**
     * Location after response
     *
     * @param result
     * @param success
     */
    responseLocation(result, success = true) {
        if (typeof result.sets.href !== 'undefined') {
            location.href = result.sets.href || location.href;
        }
    }

    /**
     * Run function after response
     *
     * @param result
     * @param success
     * @returns {void|*}
     */
    responseFunction(result, success = true) {
        let v = bsw.cnf.v;
        v.$nextTick(function () {
            let sets = typeof result.sets.function === 'undefined' ? (result.sets.logic || {}) : result.sets;
            if (typeof sets.function === 'undefined') {
                return;
            }
            if (typeof v[sets.function] !== 'undefined') {
                return v[sets.function](sets.functionArgs || {});
            } else if (typeof bsw[sets.function] !== 'undefined') {
                return bsw[sets.function](sets.functionArgs || {});
            }
            return console.warn(`Method ${sets.function} is undefined.`);
        });
    }

    /**
     * Logic after response
     *
     * @param result
     * @param success
     */
    responseLogic(result, success = true) {
        bsw.responseLocation(result, success);
        bsw.responseFunction(result, success);
    }

    /**
     * Handler for response
     *
     * @param result
     * @param duration
     */
    response(result, duration) {
        if (typeof result.code === 'undefined') {
            return bsw.error(bsw.lang.response_error_message);
        }
        let that = this;
        return new Promise(function (resolve, reject) {
            if (result.message) {
                let duration = that.isNull(result.duration) ? undefined : result.duration;
                that[result.classify](result.message, duration, null, result.type).then(function () {
                    result.error ? reject(result) : resolve(result);
                }).catch(reason => {
                    console.warn(reason);
                });
            } else {
                result.error ? reject(result) : resolve(result);
            }
        });
    }

    /**
     * Cosy size for popup
     *
     * @returns {{width: number, height: number}}
     */
    popupCosySize(honest = false, d = document) {
        let cnf = bsw.cnf;
        let width = d.body.clientWidth;
        let height = d.body.clientHeight;
        if (!honest) {
            width *= (width < cnf.cosyMinWidth ? cnf.cosyMinWidthLess : cnf.cosyMinWidthMore);
            height *= (height < cnf.cosyMinHeight ? cnf.cosyMinHeightLess : cnf.cosyMinHeightMore);
        }

        return {
            width,
            height,
            offset: {
                width: d.body.offsetWidth,
                height: d.body.offsetHeight,
            },
            scroll: {
                width: d.body.scrollWidth,
                height: d.body.scrollHeight,
            }
        };
    }

    /**
     * Encrypt by rsa public key
     *
     * @param text
     *
     * @returns string
     */
    rsaEncrypt(text) {
        let encrypt = new JSEncrypt();
        encrypt.setPublicKey(bsw.cnf.v.rsaPublicKey);

        return encrypt.encrypt(text);
    }

    /**
     * Base64 decode
     *
     * @param text
     *
     * @returns {string}
     */
    base64Decode(text) {
        return decodeURIComponent(atob(text));
    }

    /**
     * Base64 decode (array)
     *
     * @param target
     * @param exclude
     *
     * @returns {*}
     */
    arrayBase64Decode(target, exclude = []) {
        for (let key in target) {
            if (!target.hasOwnProperty(key)) {
                continue;
            }
            if (bsw.isJson(target[key])) {
                target[key] = bsw.arrayBase64Decode(target[key]);
            } else if (bsw.isString(target[key]) && !exclude.includes(key)) {
                target[key] = bsw.base64Decode(target[key]);
            }
        }
        return target;
    }

    /**
     * Url decode (safe)
     *
     * @param uri
     * @param mod
     * @returns {string}
     */
    decodeURIComponentSafe(uri, mod) {
        let out = String(),
            arr,
            i = 0,
            l,
            x;
        typeof mod === "undefined" ? mod = 0 : 0;
        arr = uri.split(/(%(?:d0|d1)%.{2})/);
        for (l = arr.length; i < l; i++) {
            try {
                x = decodeURIComponent(arr[i]);
            } catch (e) {
                x = mod ? arr[i].replace(/%(?!\d+)/g, '%25') : arr[i];
            }
            out += x;
        }
        return out;
    }

    /**
     * Url decode (array)
     *
     * @param target
     * @param exclude
     *
     * @returns {*}
     */
    arrayUrlDecode(target, exclude = []) {
        for (let key in target) {
            if (!target.hasOwnProperty(key)) {
                continue;
            }
            if (bsw.isJson(target[key])) {
                target[key] = bsw.arrayUrlDecode(target[key]);
            } else if (bsw.isString(target[key]) && !exclude.includes(key)) {
                target[key] = bsw.decodeURIComponentSafe(target[key]);
            }
        }
        return target;
    }

    /**
     * Json fn handler
     *
     * @param target
     * @param fnPrefix
     * @param fnTag
     *
     * @returns {*}
     */
    jsonFnHandler(target, fnPrefix, fnTag = 'fn') {
        let that = this;
        for (let key in target) {
            if (!target.hasOwnProperty(key)) {
                continue;
            }
            let item = target[key];
            if (that.isJson(item)) {
                target[key] = that.jsonFnHandler(item, fnPrefix, fnTag);
            } else if (that.isString(item) && item.startsWith(`${fnTag}:`)) {
                let fn = that.ucFirst(item.split(':')[1]);
                fn = `${fnPrefix}${fn}`;
                if (typeof that.cnf.v[fn] !== 'undefined') {
                    target[key] = that.cnf.v[fn];
                } else if (typeof that[fn] !== 'undefined') {
                    target[key] = that[fn];
                } else {
                    target[key] = that.blank;
                    console.warn(`Method ${fn} is undefined.`, target);
                }
            }
        }
        return target;
    }

    /**
     * Create chart
     *
     * @param option
     *
     * @returns void
     */
    chart(option) {
        let that = this;
        let o = option.option;
        let chart = echarts.init(document.getElementById(`chart-${option.id}`), option.theme);

        chart.setOption(that.jsonFnHandler(o, 'chartHandler'));
        that.cnf.v.$nextTick(function () {
            chart.resize();
        });

        $(window).resize(() => chart.resize());
    }

    /**
     * Chart handler -> tooltip stack
     *
     * @param params
     *
     * @returns {string}
     */
    chartHandlerTooltipStack(params) {
        let total = 0;
        for (let item of params) {
            total += Math.floor(Number.parseFloat(item.data) * 100);
        }

        total /= 100;

        let tpl = `${params[0].name} (${total})<br>`;
        for (let item of params) {
            let percent = ((Number.parseFloat(item.data) / total) || 0) * 100;
            percent = percent.toFixed(2);
            tpl += `${item.marker} ${item.seriesName}: ${item.data} (${percent}%)<br>`;
        }

        return tpl;
    }

    /**
     * Chart handler -> tooltip normal
     *
     * @param params
     *
     * @returns {string}
     */
    chartHandlerTooltipNormal(params) {
        return params[0].name + ': ' + params[0].value;
    }

    /**
     * Chart handler -> tooltip position fixed
     *
     * @param pos
     * @param params
     * @param dom
     * @param rect
     * @param size
     *
     * @returns {{top: number}}
     */
    chartHandlerTooltipPositionFixed(pos, params, dom, rect, size) {
        let obj = {top: 20};
        obj[['left', 'right'][+(pos[0] < size.viewSize[0] / 2)]] = 10;
        return obj;
    }

    /**
     * Show message
     *
     * @param options
     */
    showMessage(options) {
        let classify = options.classify || 'info';
        let duration = bsw.isNull(options.duration) ? undefined : options.duration;
        try {
            this[classify](options.content, duration, null, options.type);
        } catch (e) {
            console.warn(bsw.lang.message_data_error, options);
            console.warn(e);
        }
    }

    /**
     * Show modal popup
     *
     * @param options
     */
    showModal(options) {
        let cnf = bsw.cnf;
        let v = cnf.v;
        v.modal.visible = false;
        if (typeof options.width === 'undefined') {
            options.width = bsw.popupCosySize().width;
        }
        let meta = bsw.cloneJson(v.modalMeta);
        cnf.maxZIndex += 1;
        meta.zIndex = cnf.maxZIndex;
        options = Object.assign(meta, options);
        if (options.footer) {
            v.footer = '_footer';
        } else {
            v.footer = 'footer';
        }
        v.modal = options;
        if (options.afterShow) {
            v.$nextTick(function () {
                if (typeof bsw.cnf.v[options.afterShow] !== 'undefined') {
                    return bsw.cnf.v[options.afterShow](options.afterShowArgs || {});
                } else if (typeof bsw[options.afterShow] !== 'undefined') {
                    return bsw[options.afterShow](options.afterShowArgs || {});
                }
                return console.warn(`Method ${options.afterShow} is undefined.`);
            });
        }
    }

    /**
     * Show confirm
     *
     * @param options
     *
     * @return {*}
     */
    showConfirm(options) {
        let that = this;
        let cnf = bsw.cnf;
        cnf.maxZIndex += 1;
        return bsw.cnf.v.$confirm(Object.assign({
            title: that.lang.confirm_title,
            content: null,
            keyboard: false,
            width: cnf.confirmWidth,
            okText: bsw.lang.confirm,
            cancelText: bsw.lang.cancel,
            onCancel: options.onClose || bsw.blank,
            transitionName: bsw.cnf.transitionName,
            maskTransitionName: bsw.cnf.maskTransitionName,
            zIndex: cnf.maxZIndex,
        }, options));
    }

    /**
     * Show drawer popup
     *
     * @param options
     */
    showDrawer(options) {
        let cnf = bsw.cnf;
        let v = cnf.v;
        v.drawer.visible = false;
        if (typeof options.width === 'undefined') {
            options.width = bsw.popupCosySize().width;
        }
        let meta = bsw.cloneJson(v.drawerMeta);
        cnf.maxZIndex += 1;
        meta.zIndex = cnf.maxZIndex;
        options = Object.assign(meta, options);
        v.drawer = options;
        if (options.afterShow) {
            v.$nextTick(function () {
                if (typeof bsw.cnf.v[options.afterShow] !== 'undefined') {
                    return bsw.cnf.v[options.afterShow](options.afterShowArgs || {});
                } else if (typeof bsw[options.afterShow] !== 'undefined') {
                    return bsw[options.afterShow](options.afterShowArgs || {});
                }
                return console.warn(`Method ${options.afterShow} is undefined.`);
            });
        }
    }

    /**
     * Show result popup
     *
     * @param options
     */
    showResult(options) {
        let cnf = bsw.cnf;
        let v = cnf.v;
        v.result.visible = false;
        let meta = bsw.cloneJson(v.resultMeta);
        cnf.maxZIndex += 1;
        meta.zIndex = cnf.maxZIndex;
        options = Object.assign(meta, options);
        v.result = options;
    }

    /**
     * Show modal after request
     *
     * @param data
     * @param element
     */
    showModalAfterRequest(data, element) {
        let that = this;
        that.request(data.location).then(res => {
            that.response(res).then(() => {
                let options = that.jsonFilter(Object.assign(data, {
                    width: res.sets.width || data.width || undefined,
                    title: res.sets.title || data.title || that.lang.modal_title,
                    content: res.sets.content,
                }));
                that.showModal(options);
                bsw.responseLogic(res);
            }).catch(reason => {
                console.warn(reason);
            });
        }).catch(reason => {
            console.warn(reason);
        });
    }

    /**
     * Auto adjust iframe height
     */
    autoIFrameHeight() {
        let iframe = $(parent.document).find('iframe.bsw-iframe-modal');
        if (iframe.length === 0) {
            return;
        }

        let minHeight = parseInt(iframe.data('min-height'));
        let maxHeight = parseInt(iframe.data('max-height'));
        let overOffset = iframe.data('over-offset');
        let debugHeight = iframe.data('debug-height');

        minHeight = minHeight ? minHeight : 0;
        maxHeight = maxHeight ? maxHeight : 0;
        if (!minHeight && !maxHeight) {
            return;
        }

        let content = $('.bsw-content');
        let height = content.height() + bsw.pam(content.parent(), content).column;
        height = Math.ceil(height);
        if (debugHeight) {
            bsw.info(`${bsw.lang.real_iframe_height}: ${height}`);
        }

        if (!maxHeight) {
            maxHeight = bsw.popupCosySize(false, parent.document).height;
        }
        if (minHeight > maxHeight) {
            minHeight = maxHeight;
        }

        let latest = height;
        if (height < minHeight) {
            latest = minHeight;
        } else if (height > maxHeight) {
            latest = maxHeight;
        }

        if (overOffset === 'no' && Math.abs(height - latest) > bsw.cnf.autoHeightOffset) {
            return;
        }

        iframe.animate({height: latest}, bsw.cnf.autoHeightDuration);
    }

    /**
     * Show iframe by popup (modal/drawer)
     *
     * @param data
     * @param element
     */
    showIFrame(data, element) {
        let that = this;
        let v = that.cnf.v;
        let size = that.popupCosySize();
        let repair = $(element).prev().attr('id');
        data.location = that.setParams({iframe: true, repair}, data.location);

        let mode = data.shape || 'modal';
        let clsName = ['bsw-iframe', `bsw-iframe-${mode}`].join(' ');
        let options = that.jsonFilter(Object.assign(data, {
            width: data.width || size.width,
            title: data.title === false ? data.title : (data.title || that.lang.please_select)
        }));

        if (mode === 'drawer') {

            options = Object.assign(options, {
                content: `<iframe class="${clsName}" src="${data.location}"></iframe>`
            });

            that.showDrawer(options);
            v.$nextTick(function () {
                let iframe = $(`.bsw-iframe-${mode}`);
                let headerHeight = options.title ? 55 : 0;
                let footerHeight = options.footer ? 73 : 0;
                let height = that.popupCosySize(true).height;
                if (options.placement === 'top' || options.placement === 'bottom') {
                    height = options.height || 512;
                }
                iframe.height(height - headerHeight - footerHeight);
                iframe.parents('div.ant-drawer-body').css({margin: 0, padding: 0});
            });

        } else {

            let headerHeight = options.title ? 55 : 0;
            let footerHeight = options.footer ? 53 : 0;
            let height = data.height || (size.height - headerHeight - footerHeight);

            let attributes = [];
            let overOffset = typeof data.overOffset !== 'undefined' ? data.overOffset : that.cnf.autoHeightOverOffset;
            attributes.push(`data-over-offset=${overOffset}`);

            let debugHeight = typeof data.debugRealHeight !== 'undefined' ? data.debugRealHeight : false;
            attributes.push(`data-debug-height=${debugHeight}`);

            if (typeof data.minHeight === 'undefined' && that.cnf.autoHeightOffset) {
                data.minHeight = Math.max(height - that.cnf.autoHeightOffset, 0);
            }
            if (data.minHeight) {
                attributes.push(`data-min-height="${data.minHeight}"`);
            }

            if (typeof data.maxHeight === 'undefined' && that.cnf.autoHeightOffset) {
                data.maxHeight = height + that.cnf.autoHeightOffset;
            }
            if (data.maxHeight) {
                attributes.push(`data-max-height="${data.maxHeight}"`);
            }

            attributes = bsw.arrayUnique(attributes).join(' ');
            options = Object.assign(options, {
                content: `<iframe class="${clsName}" ${attributes} src="${data.location}"></iframe>`
            });

            that.showModal(options);
            v.$nextTick(function () {
                let iframe = $(`.bsw-iframe-${mode}`);
                iframe.height(height);
                iframe.parents('div.ant-modal-body').css({margin: 0, padding: 0});
            });
        }
    }

    /**
     * Modal onclick ok
     *
     * @param event
     */
    modalOnOk(event) {
        if (event) {
            let element = $(event.target).parents('.ant-modal-footer').prev().find('.bsw-modal-data');
            let result = bsw.dispatcherByBswDataElement(element, 'ok');
            if (result === true) {
                return;
            }
        }
        if (typeof bsw.cnf.v.modal.visible !== 'undefined') {
            bsw.cnf.v.modal.visible = false;
        }
    }

    /**
     * Modal onclick cancel
     *
     * @param event
     */
    modalOnCancel(event) {
        if (event) {
            let element = $(event.target).parents('.ant-modal-footer').prev().find('.bsw-modal-data');
            let result = bsw.dispatcherByBswDataElement(element, 'cancel');
            if (result === true) {
                return;
            }
        }
        if (typeof bsw.cnf.v.modal.visible !== 'undefined') {
            bsw.cnf.v.modal.visible = false;
        }
    }

    /**
     * Drawer onclick ok
     *
     * @param event
     */
    drawerOnOk(event) {
        if (event) {
            let element = $(event.target).parents('.bsw-footer-bar');
            let result = bsw.dispatcherByBswDataElement(element, 'ok');
            if (result === true) {
                return;
            }
        }
        if (typeof bsw.cnf.v.drawer.visible !== 'undefined') {
            bsw.cnf.v.drawer.visible = false;
        }
    }

    /**
     * Drawer onclick cancel
     *
     * @param event
     */
    drawerOnCancel(event) {
        if (event) {
            let element = $(event.target).parents('.bsw-footer-bar');
            let result = bsw.dispatcherByBswDataElement(element, 'cancel');
            if (result === true) {
                return;
            }
        }
        if (typeof bsw.cnf.v.drawer.visible !== 'undefined') {
            bsw.cnf.v.drawer.visible = false;
        }
    }

    /**
     * Result onclick ok
     *
     * @param event
     */
    resultOnOk(event) {
        if (event) {
            let element = $(event.target).parent().find('.bsw-result-data');
            let result = bsw.dispatcherByBswDataElement(element, 'ok');
            if (result === true) {
                return;
            }
        }
        if (typeof bsw.cnf.v.result.visible !== 'undefined') {
            bsw.cnf.v.result.visible = false;
        }
    }

    /**
     * Result onclick cancel
     *
     * @param event
     */
    resultOnCancel(event) {
        if (event) {
            let element = $(event.target).parent().find('.bsw-result-data');
            let result = bsw.dispatcherByBswDataElement(element, 'cancel');
            if (result === true) {
                return;
            }
        }
        if (typeof bsw.cnf.v.result.visible !== 'undefined') {
            bsw.cnf.v.result.visible = false;
        }
    }

    /**
     * Change image captcha
     *
     * @param selector
     */
    changeImageCaptcha(selector = 'img.bsw-captcha') {
        let that = this;
        $(selector).off('click').on('click', function () {
            let src = $(this).attr('src');
            src = that.setParams({t: that.timestamp()}, src);
            $(this).attr('src', src);
        });
    }

    /**
     * Message auto discovery
     *
     * @param discovery
     */
    messageAutoDiscovery(discovery) {
        let that = this;
        // message
        if (typeof discovery.message.content !== 'undefined') {
            that.showMessage(that.arrayBase64Decode(discovery.message));
        }
        // modal
        if (typeof discovery.modal.content !== 'undefined') {
            that.showModal(that.arrayBase64Decode(discovery.modal));
        }
        // result
        if (typeof discovery.result.title !== 'undefined') {
            that.showResult(that.arrayBase64Decode(discovery.result));
        }
    }

    /**
     * Get bsw data
     *
     * @param object
     *
     * @returns {*|{}}
     */
    getBswData(object) {
        let data = object[0].dataBsw || object.data('bsw') || {};
        data = bsw.arrayUrlDecode(data, ['location', 'href', 'url', 'query']);
        return data;
    }

    /**
     * Dispatcher by bsw data
     *
     * @param data
     * @param element
     */
    dispatcherByBswData(data, element) {
        let that = this;
        if (data.iframe) {
            let d = bsw.cloneJson(data);
            delete d.iframe;
            parent.postMessage({data: d, function: 'dispatcherByBswData'}, '*');
            return;
        }

        let action = function (options = {}) {
            if (!data.function || data.function.length === 0) {
                return console.warn(`Attribute function should be configure in options.`, data);
            }
            data = Object.assign(data, options);
            if (typeof that.cnf.v[data.function] !== 'undefined') {
                return that.cnf.v[data.function](data, element);
            } else if (typeof that[data.function] !== 'undefined') {
                return that[data.function](data, element);
            }
            return console.warn(`Method ${data.function} is undefined.`, data);
        };
        if (typeof data.confirm === 'undefined') {
            return action();
        }

        that.showConfirm({
            content: function (h) {
                if (!data.confirmCheckbox) {
                    return data.confirm;
                }
                return h('div', null, [
                    h('p', null, data.confirm),
                    h('a-checkbox', {class: 'bsw-confirm-checkbox', style: {marginTop: '10px'}}, data.confirmCheckbox)
                ]);
            },
            onOk: () => {
                let options = {confirmIsCheck: false};
                if (data.confirmCheckbox) {
                    options.confirmIsCheck = $('label.bsw-confirm-checkbox > span > input[type="checkbox"]').is(':checked');
                }
                action(options);
                return false;
            }
        });
    }

    /**
     * Dispatcher by bsw data element
     *
     * @param element
     * @param fn
     */
    dispatcherByBswDataElement(element, fn) {
        if (!element.length) {
            return;
        }
        let that = this;
        let data = element[0].dataBsw;
        if (data[fn]) {

            if (typeof that.cnf.v[data[fn]] !== 'undefined') {
                return that.cnf.v[data[fn]](data.extra || {}, element);
            } else if (typeof that[data[fn]] !== 'undefined') {
                return that[data[fn]](data.extra || {}, element);
            }
            return console.warn(`Method ${data[fn]} is undefined.`, data);
        }
    }

    /**
     * Redirect (by bsw data)
     *
     * @param data
     * {*}
     */
    redirect(data) {
        if (data.function && data.function !== 'redirect') {
            return bsw.dispatcherByBswData(data, $('body'));
        }
        let url = data.location;
        if (bsw.isMobile() && bsw.cnf.mobileDefaultCollapsed) {
            bsw.cookie().set('bsw_menu_collapsed', 'yes');
        }
        if (url.startsWith('http') || url.startsWith('/')) {
            if (typeof data.window === 'undefined') {
                return location.href = url;
            } else {
                return window.open(url);
            }
        }
    }

    /**
     * Filter option for mentions
     *
     * @param input
     * @param option
     *
     * @returns {boolean}
     */
    filterOptionForMentions(input, option) {
        return option.children[0].text.toUpperCase().indexOf(input.toUpperCase()) >= 0;
    }

    /**
     * Filter option for auto complete
     *
     * @param input
     * @param option
     *
     * @returns {boolean}
     */
    filterOptionForAutoComplete(input, option) {
        return option.componentOptions.children[0].text.toUpperCase().indexOf(input.toUpperCase()) >= 0;
    }

    /**
     * Filter option for transfer
     *
     * @param input
     * @param option
     *
     * @returns {boolean}
     */
    filterOptionForTransfer(input, option) {
        return option.title.indexOf(input) !== -1;
    }

    /**
     * Init highlight
     */
    initHighlight() {
        if (typeof hljs === 'undefined') {
            return;
        }
        hljs.initHighlighting();
    }

    /**
     * Init highlight block
     */
    initHighlightBlock(params) {
        if (typeof hljs === 'undefined') {
            return;
        }
        if (typeof params.selector === 'undefined') {
            return;
        }
        if ($(params.selector).length === 0) {
            return;
        }
        $(params.selector).each(function () {
            hljs.highlightBlock(this);
        });
    }

    /**
     * Init vConsole
     */
    initVConsole() {
        if (typeof VConsole === 'undefined') {
            return;
        }
        bsw.cnf.v.vConsole = new VConsole();
    }

    /**
     * Init ck editor
     *
     * @param form
     * @param selector
     */
    initCkEditor(form = 'persistenceForm', selector = '.bsw-persistence .bsw-ck') {
        if (!window.DecoupledEditor) {
            return;
        }
        let that = this;
        let v = that.cnf.v;
        $(selector).each(function () {
            let em = this;
            let id = $(em).prev('textarea').attr('id');
            let container = $(em).find('.bsw-ck-editor');
            let options = {
                language: that.lang.i18n_editor,
                placeholder: $(em).attr('placeholder'),
            };
            let toolbar = $(em).data('toolbar');
            if (toolbar.length > 0) {
                options.toolbar = toolbar;
            }
            DecoupledEditor.create(container[0], options).then(editor => {
                v.ckEditor[id] = editor;
                editor.isReadOnly = $(em).attr('disabled') === 'disabled';
                editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                    return new FileUploadAdapter(editor, loader, v.init.uploadApiUrl);
                };
                v.ckEditor[id].model.document.on('change:data', function () {
                    if (v[form]) {
                        v[form].setFieldsValue({[id]: v.ckEditor[id].getData()});
                    }
                });
                $(em).find('.bsw-ck-toolbar').append(editor.ui.view.toolbar.element);
            }).catch(err => {
                console.warn(err.stack);
            });
        });
    }

    /**
     * Init clipboard
     *
     * @param selector
     */
    initClipboard(selector = '.ant-btn') {
        if (!window.Clipboard) {
            return;
        }
        let that = this;
        let clipboard = new Clipboard(selector, {
            text: function (trigger) {
                if (typeof that.cnf.v.copy !== 'undefined' && that.cnf.v.copy) {
                    let text = that.cnf.v.copy;
                    that.cnf.v.copy = null;
                    return text;
                }
                return trigger.getAttribute('data-clipboard-text');
            }
        });
        clipboard.on('success', function (e) {
            that.success(that.lang.copy_success);
            e.clearSelection();
        });
        clipboard.on('error', function (e) {
            that.error(that.lang.copy_failed);
            console.warn('Clipboard operation error', e);
        });
    }

    /**
     * Init scroll x
     *
     * @param selector
     */
    initScrollX(selector = '.bsw-scroll-x') {
        let cnf = bsw.cnf;
        $(selector).each(function () {
            let arrow = $(this);
            let step = parseInt(arrow.data('step'));
            let target = function (index) {
                return typeof index === 'undefined' ? $(arrow.data('target-selector')) : $(arrow.data('target-selector'))[index];
            };
            if (target().length === 0) {
                return true;
            }

            let offset = bsw.offset(arrow.parent());
            let position = $(this).hasClass('left') ? -1 : 1;
            if (position === -1) {
                arrow.css({left: offset.left});
            } else if (position === 1) {
                arrow.css({right: offset.right});
            }

            let maxScrollTop = () => document.body.scrollHeight - document.body.clientHeight;
            let maxScrollLeft = () => target(0).scrollWidth - target(0).clientWidth;
            arrow.off('click').on('click', function () {
                let nowScrollLeft = target().scrollLeft();
                if (position === -1 && nowScrollLeft <= 1) {
                    return bsw.warning(bsw.lang.is_far_left, 1);
                }
                if (position === 1 && nowScrollLeft >= maxScrollLeft() - 1) {
                    return bsw.warning(bsw.lang.is_far_right, 1);
                }
                target().stop().animate({scrollLeft: `${nowScrollLeft + (step * position)}px`})
            });

            $(window).resize(function () {
                let x = maxScrollLeft();
                let y = maxScrollTop();
                if (x > 0 && y > cnf.scrollXMinHeight) {
                    arrow.fadeIn(cnf.scrollXFadeDuration);
                } else {
                    arrow.fadeOut(cnf.scrollXFadeDuration);
                }
            });
        });
    }

    /**
     * Do animate css
     *
     * @param selector
     * @param animation
     * @param duration
     * @param prefix
     */
    doAnimateCSS(selector, animation, duration = '1s', prefix = '') {
        new Promise((resolve, reject) => {
            let animationName = `${prefix}${animation}`;
            let node = $(selector);
            if (selector.length === 0) {
                return;
            }
            node.css('animation-duration', duration);
            node.addClass(`${prefix}animated ${animationName}`);

            function handleAnimationEnd() {
                node.removeClass(`${prefix}animated ${animationName}`);
                node.off('animationend', handleAnimationEnd);
                resolve(animationName);
            }

            node.on('animationend', handleAnimationEnd);
        });
    }

    /**
     * Prominent the anchor
     *
     * @param animate
     * @param duration
     */
    prominentAnchor(animate = 'flash', duration = '.65s') {
        let anchor = bsw.leftTrim(window.location.hash, '#');
        if (!anchor.length) {
            return;
        }
        if (anchor.startsWith('trigger.')) {
            let name = anchor.split('.')[1];
            let trigger = $(`[name="${name}"]`);
            if (name.length && trigger.length) {
                trigger.click();
            }
        } else if ($(`#${anchor}`).length) {
            bsw.doAnimateCSS(`#${anchor}`, animate, duration);
        }
    }

    /**
     * Upward infect class
     *
     * @param selector
     */
    initUpwardInfect(selector = '.bsw-upward-infect') {
        $(selector).each(function () {
            let element = $(this);
            for (let i = 0; i < $(this).data('infect-level'); i++) {
                element = element.parent();
            }
            element.addClass($(this).data('infect-class'));
        });
    }

    /**
     * [in parent] Dispatcher by bsw data
     *
     * @param data
     * @param element
     */
    dispatcherByBswDataInParent(data, element) {
        let that = this;
        let d = data.data;
        let closeModal = (typeof d.closePrevModal === 'undefined') ? true : d.closePrevModal;
        let closeDrawer = (typeof d.closePrevDrawer === 'undefined') ? true : d.closePrevDrawer;
        closeModal && bsw.modalOnCancel();
        closeDrawer && bsw.drawerOnCancel();
        that.cnf.v.$nextTick(function () {
            if (typeof d.location !== 'undefined') {
                d.location = that.unsetParams(['iframe'], d.location);
            }
            that.dispatcherByBswData(d, element);
        });
    }

    /**
     * [in parent] Filter form item
     *
     * @param data
     * @param element
     * @param form
     */
    fillParentFormInParent(data, element, form = 'persistenceForm') {
        let v = bsw.cnf.v;
        let closeModal = (typeof data.closePrevModal === 'undefined') ? true : data.closePrevModal;
        let closeDrawer = (typeof data.closePrevDrawer === 'undefined') ? true : data.closePrevDrawer;
        closeModal && bsw.modalOnCancel();
        closeDrawer && bsw.drawerOnCancel();
        v.$nextTick(function () {
            if (v[form] && data.repair) {
                v[form].setFieldsValue({[data.repair]: data.ids});
            }
        });
    }

    /**
     * [in parent] fill form after ajax
     *
     * @param res
     * @param element
     */
    fillParentFormAfterAjaxInParent(res, element) {
        let data = res.response.sets;
        data.repair = data.arguments.repair;
        bsw.fillParentFormInParent(data, element);
    }

    /**
     * [in parent] Handler response
     *
     * @param data
     * @param element
     */
    handleResponseInParent(data, element) {
        let that = this;
        if (typeof data.response === 'undefined') {
            return;
        }

        let res = data.response;
        if (res.classify === 'success') {
            let closeModal = (typeof res.sets.closePrevModal === 'undefined') ? true : res.sets.closePrevModal;
            let closeDrawer = (typeof res.sets.closePrevDrawer === 'undefined') ? true : res.sets.closePrevDrawer;
            closeModal && that.modalOnCancel();
            closeDrawer && that.drawerOnCancel();
        }
        that.cnf.v.$nextTick(function () {
            that.response(res).then(() => {
                bsw.responseLogic(res);
            }).catch(reason => {
                console.warn(reason);
            });
        });
    }

    /**
     * [in parent] Show iframe
     *
     * @param data
     * @param element
     */
    showIFrameInParent(data, element) {
        bsw.showIFrame(data.response.sets, element);
    }

    /**
     * Full screen
     *
     * @param data
     * @param element
     */
    fullScreenToggle(data, element) {
        if (!window.screenfull) {
            return;
        }
        let container = $(data.element)[0];
        if (screenfull.isEnabled) {
            screenfull.toggle(container);
        } else {
            console.warn('Your browser is not supported.');
        }
    }

    /**
     * WeChat pay by js api
     *
     * @param config object
     *
     * @returns void
     */
    wxJsApiPay(config) {
        if (!window.WeixinJSBridge) {
            console.warn('The api just work in WeChat browser.');
            return;
        }
        WeixinJSBridge.invoke('getBrandWCPayRequest', config, function (result) {
            console.log(result);
            if (result.err_msg === 'get_brand_wcpay_request:ok') {
                console.log('Success');
            }
        });
    }
}

// -- eof --