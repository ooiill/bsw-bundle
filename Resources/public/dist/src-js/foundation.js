'use strict';

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

//
// Copyright 2019
//

//
// Foundation for prototype
//

var FoundationPrototype = function () {
    function FoundationPrototype() {
        _classCallCheck(this, FoundationPrototype);
    }

    _createClass(FoundationPrototype, [{
        key: 'trim',


        /**
         * String trim
         *
         * @param source
         * @param haystack
         *
         * @return {string}
         */
        value: function trim(source, haystack) {
            haystack = haystack ? '\\s' + haystack : '\\s';
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

    }, {
        key: 'leftTrim',
        value: function leftTrim(source, haystack) {
            haystack = haystack ? '\\s' + haystack : '\\s';
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

    }, {
        key: 'rightTrim',
        value: function rightTrim(source, haystack) {
            haystack = haystack ? '\\s' + haystack : '\\s';
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

    }, {
        key: 'pad',
        value: function pad(target, padStr, length, type) {
            padStr = padStr.toString();
            type = type || 'left';

            if (target.length >= length || !['left', 'right', 'both'].contains(type)) {
                return target;
            }

            var padNum = void 0,
                _padNum = void 0;
            var last = (length - target.length) % padStr.length;
            padNum = _padNum = Math.floor((length - target.length) / padStr.length);

            if (last > 0) {
                padNum += 1;
            }

            var _that = target;
            for (var i = 0; i < padNum; i++) {
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
                        _that = 0 === i % 2 ? padStr + _that : _that + padStr;
                        break;
                }
            }

            return _that;
        }
    }, {
        key: 'fill',


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
        value: function fill(target, fillStr, length, type) {
            fillStr = fillStr.toString();
            type = type || 'left';

            if (length < 1 || !['left', 'right', 'both'].contains(type)) {
                return target;
            }

            var _that = target;
            for (var i = 0; i < length; i++) {
                switch (type) {
                    case 'left':
                        _that = fillStr + _that;
                        break;
                    case 'right':
                        _that += fillStr;
                        break;
                    case 'both':
                        _that = 0 === i % 2 ? fillStr + _that : _that + fillStr;
                        break;
                }
            }

            return _that;
        }
    }, {
        key: 'repeat',


        /**
         * String repeat
         *
         * @param target
         * @param num
         *
         * @return {string}
         */
        value: function repeat(target, num) {
            num = isNaN(num) || num < 1 ? 1 : num + 1;
            return new Array(num).join(target);
        }
    }, {
        key: 'ucWords',


        /**
         * String upper first char of words
         *
         * @param target
         *
         * @return {*}
         */
        value: function ucWords(target) {
            return target.replace(/\b(\w)+\b/g, function (word) {
                return word.replace(word.charAt(0), word.charAt(0).toUpperCase());
            });
        }
    }, {
        key: 'ucFirst',


        /**
         * String upper first char
         *
         * @param target
         *
         * @return {*}
         */
        value: function ucFirst(target) {
            return target.replace(target.charAt(0), target.charAt(0).toUpperCase());
        }
    }, {
        key: 'lcFirst',


        /**
         * String lower first char
         *
         * @param target
         *
         * @return {*}
         */
        value: function lcFirst(target) {
            return target.replace(target.charAt(0), target.charAt(0).toLowerCase());
        }
    }, {
        key: 'bigHump',


        /**
         * String big hump style
         *
         * @param target
         * @param split
         *
         * @return {*}
         */
        value: function bigHump(target) {
            var split = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '_';

            var reg = new RegExp(split, 'g');
            return this.ucWords(target.replace(reg, ' ')).replace(/ /g, '');
        }
    }, {
        key: 'smallHump',


        /**
         * String small hump style
         *
         * @param target
         * @param split
         *
         * @return {*}
         */
        value: function smallHump(target) {
            var split = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '_';

            return this.lcFirst(this.bigHump(target, split));
        }
    }, {
        key: 'humpToUnder',


        /**
         * String hump to under
         *
         * @param target
         * @param split
         *
         * @returns {*}
         */
        value: function humpToUnder(target) {
            var split = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '_';

            return this.leftTrim(target.replace(/([A-Z])/g, split + '$1').toLowerCase(), split);
        }
    }, {
        key: 'format',


        /**
         * Date format
         *
         * @param target
         * @param fmt
         *
         * @return {*}
         */
        value: function format(target) {
            var fmt = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'yyyy-MM-dd hh:mm:ss';

            var o = {
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

            for (var k in o) {
                if (!o.hasOwnProperty(k)) {
                    continue;
                }
                if (new RegExp('(' + k + ')').test(fmt)) {
                    fmt = fmt.replace(RegExp.$1, RegExp.$1.length === 1 ? o[k] : ('00' + o[k]).substr(('' + o[k]).length));
                }
            }

            return fmt;
        }
    }, {
        key: 'arrayUnique',


        /**
         * Array unique
         *
         * @param target
         *
         * @returns {*}
         */
        value: function arrayUnique(target) {
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

    }, {
        key: 'arrayRemoveValue',
        value: function arrayRemoveValue(target, value) {
            var index = target.indexOf(value);
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

    }, {
        key: 'arrayIntersect',
        value: function arrayIntersect(first, second) {
            return first.filter(function (v) {
                return second.indexOf(v) > -1;
            });
        }

        /**
         * Array difference
         *
         * @param first
         * @param second
         *
         * @return {*}
         */

    }, {
        key: 'arrayDifference',
        value: function arrayDifference(first, second) {
            return first.filter(function (v) {
                return second.indexOf(v) === -1;
            });
        }

        /**
         * Array complement
         *
         * @param first
         * @param second
         *
         * @return {*}
         */

    }, {
        key: 'arrayComplement',
        value: function arrayComplement(first, second) {
            return first.filter(function (v) {
                return !(second.indexOf(v) > -1);
            }).concat(second.filter(function (v) {
                return !(first.indexOf(v) > -1);
            }));
        }

        /**
         * Array union
         *
         * @param first
         * @param second
         *
         * @return {*}
         */

    }, {
        key: 'arrayUnion',
        value: function arrayUnion(first, second) {
            return first.concat(second.filter(function (v) {
                return !(first.indexOf(v) > -1);
            }));
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

    }, {
        key: 'swap',
        value: function swap(source, first, last) {
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

    }, {
        key: 'up',
        value: function up(source, index) {
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

    }, {
        key: 'down',
        value: function down(source, index) {
            if (index === source.length - 1) {
                return source;
            }
            return this.swap(source, index, index + 1);
        }

        /**
         * Get width & height
         *
         * @returns {{eleH: number, eleW: number, srlY: number, srlW: number, scnW: number, bodyW: number, srlX: number, bodyH: number, srlH: number, scnH: number}}
         */

    }, {
        key: 'getWH',
        value: function getWH() {
            return {
                bodyW: document.body.clientWidth, // Body area
                bodyH: document.body.clientHeight,
                eleW: document.documentElement.clientWidth, // Element area
                eleH: document.documentElement.clientHeight,
                srlW: document.body.scrollWidth, // Scroll area
                srlH: document.body.scrollHeight,
                srlX: document.body.scrollLeft,
                srlY: document.body.scrollTop,
                scnH: window.screen.availHeight, // Screen area
                scnW: window.screen.availWidth
            };
        }
    }]);

    return FoundationPrototype;
}();

//
// Foundation for tools
//

var FoundationTools = function (_FoundationPrototype) {
    _inherits(FoundationTools, _FoundationPrototype);

    function FoundationTools() {
        _classCallCheck(this, FoundationTools);

        return _possibleConstructorReturn(this, (FoundationTools.__proto__ || Object.getPrototypeOf(FoundationTools)).apply(this, arguments));
    }

    _createClass(FoundationTools, [{
        key: 'blank',


        /**
         * Blank fn
         */
        value: function blank() {}

        /**
         * Is array
         *
         * @param value
         *
         * @return {boolean}
         */

    }, {
        key: 'isArray',
        value: function isArray(value) {
            if (null === value) {
                return false;
            }
            return (typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object' && value.constructor === Array;
        }

        /**
         * Is object
         *
         * @param value
         *
         * @return {boolean}
         */

    }, {
        key: 'isObject',
        value: function isObject(value) {
            if (null === value) {
                return false;
            }
            return (typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object' && value.constructor === Object;
        }

        /**
         * Is null
         *
         * @param value
         *
         * @return {boolean}
         */

    }, {
        key: 'isNull',
        value: function isNull(value) {
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

    }, {
        key: 'isJson',
        value: function isJson(value) {
            if (null === value) {
                return false;
            }
            return (typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object' && Object.prototype.toString.call(value).toLowerCase() === '[object object]';
        }

        /**
         * Is string
         *
         * @param value
         *
         * @return {boolean}
         */

    }, {
        key: 'isString',
        value: function isString(value) {
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

    }, {
        key: 'isNumeric',
        value: function isNumeric(value) {
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

    }, {
        key: 'isBoolean',
        value: function isBoolean(value) {
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

    }, {
        key: 'isFunction',
        value: function isFunction(value) {
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

    }, {
        key: 'jsonLength',
        value: function jsonLength(target) {
            var length = 0;
            for (var i in target) {
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

    }, {
        key: 'sortJsonArray',
        value: function sortJsonArray(array, field) {
            var desc = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

            if (array.length < 2 || !field || _typeof(array[0]) !== 'object') {
                return array;
            }
            if (typeof array[0][field] === "number") {
                array.sort(function (x, y) {
                    return x[field] - y[field];
                });
            }
            if (typeof array[0][field] === "string") {
                array.sort(function (x, y) {
                    return x[field].localeCompare(y[field]);
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

    }, {
        key: 'sortJson',
        value: function sortJson(object) {
            var keys = [];
            for (var key in object) {
                if (!object.hasOwnProperty(key)) {
                    continue;
                }
                keys.push(key);
            }
            keys.sort();
            var target = {};
            for (var _key in keys) {
                if (!keys.hasOwnProperty(_key)) {
                    continue;
                }
                var k = keys[_key];
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

    }, {
        key: 'offset',
        value: function offset(element) {
            element = element.jquery ? element : $(element);
            var pos = element.offset();
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

    }, {
        key: 'timestamp',
        value: function timestamp() {
            var second = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

            var time = new Date().getTime();
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

    }, {
        key: 'parseQueryString',
        value: function parseQueryString() {
            var url = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
            var hostPart = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

            url = decodeURIComponent(url || location.href);
            if (url.indexOf('#') === -1) {
                url = url + '#';
            }

            var items = {};
            var urlArr = url.split('#');
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
            var _iteratorNormalCompletion = true;
            var _didIteratorError = false;
            var _iteratorError = undefined;

            try {
                for (var _iterator = url[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                    var item = _step.value;

                    item = item.split('=');
                    items[item[0]] = item[1];
                }
            } catch (err) {
                _didIteratorError = true;
                _iteratorError = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion && _iterator.return) {
                        _iterator.return();
                    }
                } finally {
                    if (_didIteratorError) {
                        throw _iteratorError;
                    }
                }
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

    }, {
        key: 'jsonBuildQuery',
        value: function jsonBuildQuery(source) {
            var returnObject = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
            var needEncode = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;

            var query = '',
                _query = {},
                name = void 0,
                value = void 0,
                fullSubName = void 0,
                subName = void 0,
                subValue = void 0,
                innerObject = void 0,
                i = void 0;
            source = this.sortJson(source);
            for (var _name in source) {
                if (!source.hasOwnProperty(_name)) {
                    continue;
                }
                value = source[_name];

                if (this.isArray(value)) {
                    for (i = 0; i < value.length; ++i) {
                        subValue = value[i];
                        fullSubName = _name + '[' + i + ']';
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
                        fullSubName = _name + '[' + subName + ']';
                        innerObject = {};
                        innerObject[fullSubName] = subValue;
                        query += this.jsonBuildQuery(innerObject, returnObject, needEncode) + '&';
                        _query = Object.assign(_query, this.jsonBuildQuery(innerObject, returnObject, needEncode));
                    }
                } else if (value !== undefined && value !== null) {
                    if (needEncode) {
                        _name = encodeURIComponent(_name);
                        value = encodeURIComponent(value);
                    }
                    query += _name + '=' + value + '&';
                    _query[_name] = value;
                }
            }

            if (returnObject) {
                return _query;
            }

            return query.length ? query.substr(0, query.length - 1) : query;
        }

        /**
         * Parse url params
         *
         * @param url
         * @returns {(*|Array)[]}
         */

    }, {
        key: 'parseParams',
        value: function parseParams() {
            var url = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

            var queryParams = this.parseQueryString(url, true);
            var host = queryParams.hostPart;
            var anchor = queryParams.anchorPart;
            delete queryParams.hostPart;
            delete queryParams.anchorPart;

            return [host, anchor, queryParams];
        }

        /**
         * Combine url
         *
         * @param host
         * @param anchor
         * @param params
         * @param needEncode
         * @returns {string}
         */

    }, {
        key: 'combineUrl',
        value: function combineUrl(host, anchor, params) {
            var needEncode = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

            var queryString = this.jsonBuildQuery(params, false, needEncode);
            var url = host + '?' + queryString;
            if (anchor.length) {
                url = this.trim(url, '?') + '#' + anchor;
            }

            return this.trim(url, '?');
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

    }, {
        key: 'setParams',
        value: function setParams(items) {
            var url = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
            var needEncode = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

            var _parseParams = this.parseParams(url),
                _parseParams2 = _slicedToArray(_parseParams, 3),
                host = _parseParams2[0],
                anchor = _parseParams2[1],
                queryParams = _parseParams2[2];

            queryParams = Object.assign(queryParams, this.jsonBuildQuery(items, true, needEncode));

            return this.combineUrl(host, anchor, queryParams);
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

    }, {
        key: 'unsetParams',
        value: function unsetParams(items, url) {
            var needEncode = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
            var effect = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};

            var _parseParams3 = this.parseParams(url),
                _parseParams4 = _slicedToArray(_parseParams3, 3),
                host = _parseParams4[0],
                anchor = _parseParams4[1],
                queryParams = _parseParams4[2];

            var _iteratorNormalCompletion2 = true;
            var _didIteratorError2 = false;
            var _iteratorError2 = undefined;

            try {
                for (var _iterator2 = (items || [])[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
                    var v = _step2.value;

                    if (typeof queryParams[v] !== 'undefined') {
                        effect[v] = queryParams[v];
                        delete queryParams[v];
                    }
                }
            } catch (err) {
                _didIteratorError2 = true;
                _iteratorError2 = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion2 && _iterator2.return) {
                        _iterator2.return();
                    }
                } finally {
                    if (_didIteratorError2) {
                        throw _iteratorError2;
                    }
                }
            }

            return this.combineUrl(host, anchor, queryParams, needEncode);
        }

        /**
         * Trigger params
         *
         * @param items
         * @param url
         * @param needEncode
         */

    }, {
        key: 'triggerParams',
        value: function triggerParams(items, url) {
            var needEncode = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

            var _parseParams5 = this.parseParams(url),
                _parseParams6 = _slicedToArray(_parseParams5, 3),
                host = _parseParams6[0],
                anchor = _parseParams6[1],
                queryParams = _parseParams6[2];

            for (var v in items) {
                if (!items.hasOwnProperty(v)) {
                    continue;
                }
                if (typeof queryParams[v] !== 'undefined') {
                    delete queryParams[v];
                } else {
                    queryParams[v] = items[v];
                }
            }

            return this.combineUrl(host, anchor, queryParams, needEncode);
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

    }, {
        key: 'unsetParamsBeginWith',
        value: function unsetParamsBeginWith(items, url) {
            var needEncode = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
            var effect = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};

            var _parseParams7 = this.parseParams(url),
                _parseParams8 = _slicedToArray(_parseParams7, 3),
                host = _parseParams8[0],
                anchor = _parseParams8[1],
                queryParams = _parseParams8[2];

            var _iteratorNormalCompletion3 = true;
            var _didIteratorError3 = false;
            var _iteratorError3 = undefined;

            try {
                for (var _iterator3 = (items || [])[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
                    var v = _step3.value;

                    for (var w in queryParams) {
                        if (!queryParams.hasOwnProperty(w)) {
                            continue;
                        }
                        if (w.startsWith(v)) {
                            effect[w] = queryParams[w];
                            delete queryParams[w];
                        }
                    }
                }
            } catch (err) {
                _didIteratorError3 = true;
                _iteratorError3 = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion3 && _iterator3.return) {
                        _iterator3.return();
                    }
                } finally {
                    if (_didIteratorError3) {
                        throw _iteratorError3;
                    }
                }
            }

            return this.combineUrl(host, anchor, queryParams, needEncode);
        }

        /**
         * Get anchor
         *
         * @param url
         * @returns {string|*}
         */

    }, {
        key: 'getAnchor',
        value: function getAnchor() {
            var url = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

            var anchor = bsw.leftTrim(window.location.hash, '#');
            if (anchor.length > 0) {
                return anchor;
            }

            var _parseParams9 = this.parseParams(url),
                _parseParams10 = _slicedToArray(_parseParams9, 3),
                _ = _parseParams10[0],
                __ = _parseParams10[1],
                queryParams = _parseParams10[2];

            anchor = queryParams.anchor || '';
            return anchor;
        }

        /**
         * Count px of padding and margin
         *
         * @param parentElement
         * @param element
         * @returns {{column: number, row: number}}
         */

    }, {
        key: 'pam',
        value: function pam(parentElement, element) {
            var px = {
                row: 0,
                column: 0
            };
            var _arr = ['margin', 'padding', 'border'];
            for (var _i = 0; _i < _arr.length; _i++) {
                var m = _arr[_i];
                if (typeof px[m] === 'undefined') {
                    px[m] = {};
                }
                var _arr3 = ['left', 'right', 'top', 'bottom'];
                for (var _i3 = 0; _i3 < _arr3.length; _i3++) {
                    var _n = _arr3[_i3];
                    if (m === 'border') {
                        px[m][_n] = parseInt(parentElement.css(m + '-' + _n + '-width'));
                    } else {
                        px[m][_n] = parseInt(parentElement.css(m + '-' + _n));
                    }
                    if (_n === 'left' || _n === 'right') {
                        px.row += px[m][_n];
                    } else if (_n === 'top' || _n === 'bottom') {
                        px.column += px[m][_n];
                    }
                }
            }
            if (element) {
                var _arr2 = ['left', 'right', 'top', 'bottom'];

                for (var _i2 = 0; _i2 < _arr2.length; _i2++) {
                    var n = _arr2[_i2];
                    if (n === 'left' || n === 'right') {
                        px.row += parseInt(element.css('border-' + n + '-width'));
                    } else if (n === 'top' || n === 'bottom') {
                        px.column += parseInt(element.css('border-' + n + '-width'));
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

    }, {
        key: 'device',
        value: function device() {
            var u = navigator.userAgent;
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

    }, {
        key: 'isMobile',
        value: function isMobile() {
            return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
            );
        }

        /**
         * Rand a number
         *
         * @param end
         * @param begin
         * @return {*}
         */

    }, {
        key: 'rand',
        value: function rand(end, begin) {
            begin = begin || 0;
            var rank = begin;
            var _end = end - rank;

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

    }, {
        key: 'keyBind',
        value: function keyBind(num, callback, element) {
            var ctrl = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

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

    }, {
        key: 'media',
        value: function media() {
            var width = document.body.clientWidth;
            return {
                'xs': width < 576,
                'sm': width >= 576,
                'md': width >= 768,
                'lg': width >= 992,
                'xl': width >= 1200,
                'xxl': width >= 1600
            };
        }

        /**
         * Int value
         *
         * @param value
         *
         * @return int
         */

    }, {
        key: 'parseInt',
        value: function (_parseInt) {
            function parseInt(_x5) {
                return _parseInt.apply(this, arguments);
            }

            parseInt.toString = function () {
                return _parseInt.toString();
            };

            return parseInt;
        }(function (value) {
            value = parseInt(value);
            return isNaN(value) ? 0 : value;
        })

        /**
         * Cookie tools
         *
         * @return {{}}
         */

    }, {
        key: 'cookie',
        value: function cookie() {
            return {
                set: function set(name, value) {
                    var time = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 86400 * 365;
                    var domain = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;

                    var expires = new Date();
                    expires.setTime(expires.getTime() + time * 1000);
                    if (!domain) {
                        domain = '';
                    } else {
                        domain = '; domain=' + domain;
                    }
                    document.cookie = name + '=' + encodeURI(value) + '; expires=' + expires.toUTCString() + '; path=/' + domain;
                    return value;
                },
                get: function get(name) {
                    var def = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

                    var cookieArray = document.cookie.split('; ');
                    for (var i = 0; i < cookieArray.length; i++) {
                        var arr = cookieArray[i].split('=');
                        if (arr[0] === name) {
                            return unescape(arr[1]);
                        }
                    }
                    return def;
                },
                delete: function _delete(name) {
                    var domain = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

                    if (!domain) {
                        domain = '';
                    } else {
                        domain = '; domain=' + domain;
                    }
                    document.cookie = name + '=; expires=' + new Date(0).toUTCString() + '; path=/' + domain;
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

    }, {
        key: 'cookieMapNext',
        value: function cookieMapNext(name, map, def) {
            var set = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
            var tips = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;

            var ck = this.cookie();
            var current = ck.get(name, def);
            var next = map[current] || def;

            if (tips) {
                var _next = this.ucFirst(next);
                this.message('success', tips + ': ' + _next);
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

    }, {
        key: 'cookieMapCurrent',
        value: function cookieMapCurrent(name, map, def) {
            var current = this.cookie().get(name, def);

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

    }, {
        key: 'evalExpr',
        value: function evalExpr(expression) {
            var def = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

            var data = null;
            try {
                data = window.eval('(' + expression + ')');
            } catch (e) {
                console.warn('Expression has syntax error: ' + expression);
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

    }, {
        key: 'switchClass',
        value: function switchClass(cls, add) {
            var selector = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'html';

            var container = $(selector);
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

    }, {
        key: 'checkJsonDeep',
        value: function checkJsonDeep(target, keys) {
            var origin = target;
            keys = keys.split('.');
            var _iteratorNormalCompletion4 = true;
            var _didIteratorError4 = false;
            var _iteratorError4 = undefined;

            try {
                for (var _iterator4 = keys[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
                    var key = _step4.value;

                    if (typeof origin[key] === 'undefined' || !origin[key]) {
                        return false;
                    }
                    origin = origin[key];
                }
            } catch (err) {
                _didIteratorError4 = true;
                _iteratorError4 = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion4 && _iterator4.return) {
                        _iterator4.return();
                    }
                } finally {
                    if (_didIteratorError4) {
                        throw _iteratorError4;
                    }
                }
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

    }, {
        key: 'setJsonDeep',
        value: function setJsonDeep(target, keys, value) {
            var origin = target;
            keys = keys.split('.');
            var _iteratorNormalCompletion5 = true;
            var _didIteratorError5 = false;
            var _iteratorError5 = undefined;

            try {
                for (var _iterator5 = keys[Symbol.iterator](), _step5; !(_iteratorNormalCompletion5 = (_step5 = _iterator5.next()).done); _iteratorNormalCompletion5 = true) {
                    var key = _step5.value;

                    var last = key === keys[keys.length - 1];
                    if (typeof origin[key] === 'undefined' || !origin[key]) {
                        origin[key] = last ? value : {};
                    } else {
                        origin[key] = last ? value : origin[key];
                    }
                    origin = origin[key];
                }
            } catch (err) {
                _didIteratorError5 = true;
                _iteratorError5 = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion5 && _iterator5.return) {
                        _iterator5.return();
                    }
                } finally {
                    if (_didIteratorError5) {
                        throw _iteratorError5;
                    }
                }
            }
        }

        /**
         * Json filter
         *
         * @param target
         * @param filter
         */

    }, {
        key: 'jsonFilter',
        value: function jsonFilter(target) {
            var filter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : ['', null];

            for (var i in target) {
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

    }, {
        key: 'cloneJson',
        value: function cloneJson(target) {
            var newObj = Array.isArray(target) ? [] : {};
            if (target && (typeof target === 'undefined' ? 'undefined' : _typeof(target)) === "object") {
                for (var key in target) {
                    if (target.hasOwnProperty(key)) {
                        newObj[key] = target && _typeof(target[key]) === 'object' ? this.cloneJson(target[key]) : target[key];
                    }
                }
            }
            return newObj;
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

    }, {
        key: 'throttle',
        value: function throttle(delay, noTrailing, callback, debounceMode) {
            var timeoutID = void 0;
            var cancelled = false;
            var lastExec = 0;

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

            function wrapper() {
                for (var _len = arguments.length, arguments_ = Array(_len), _key2 = 0; _key2 < _len; _key2++) {
                    arguments_[_key2] = arguments[_key2];
                }

                var self = this;
                var elapsed = Date.now() - lastExec;
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
                    timeoutID = setTimeout(debounceMode ? clear : exec, debounceMode === undefined ? delay - elapsed : delay);
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

    }, {
        key: 'debounce',
        value: function debounce(delay, atBegin, callback) {
            if (typeof callback === 'undefined') {
                return bsw.throttle(delay, atBegin, false);
            }
            return bsw.throttle(delay, callback, atBegin !== false);
        }
    }]);

    return FoundationTools;
}(FoundationPrototype);

//
// Foundation for AntD
//

var FoundationAntD = function (_FoundationTools) {
    _inherits(FoundationAntD, _FoundationTools);

    /**
     * Constructor
     *
     * @param jQuery
     * @param Vue
     * @param AntD
     * @param lang
     */
    function FoundationAntD(jQuery, Vue, AntD) {
        var lang = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};

        _classCallCheck(this, FoundationAntD);

        var _this2 = _possibleConstructorReturn(this, (FoundationAntD.__proto__ || Object.getPrototypeOf(FoundationAntD)).call(this));

        _this2.v = Vue;
        _this2.d = AntD;
        _this2.config = {};
        _this2.lang = lang;
        _this2.cnf = {
            menuTheme: 'dark',
            menuWidth: 256,
            menuCollapsed: false,
            menuThemeMap: { dark: 'light', light: 'dark' },
            opposeMap: { yes: 'no', no: 'yes' },
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
        return _this2;
    }

    /**
     * Page configure
     *
     * @param config
     */


    _createClass(FoundationAntD, [{
        key: 'configure',
        value: function configure(config) {
            for (var key in config) {
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

    }, {
        key: 'vue',
        value: function vue() {
            var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '.bsw-vue';

            var that = this;
            var conf = {};
            return {
                template: function template(item) {
                    item && (conf.template = item);
                    return this;
                },
                data: function data() {
                    var item = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                    conf.data = function () {
                        return item;
                    };
                    return this;
                },
                computed: function computed() {
                    var item = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                    conf.computed = item;
                    return this;
                },
                method: function method() {
                    var item = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                    conf.methods = item;
                    return this;
                },
                component: function component() {
                    var item = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                    conf.components = item;
                    return this;
                },
                directive: function directive() {
                    var item = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                    conf.directives = item;
                    return this;
                },
                watch: function watch() {
                    var item = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                    conf.watch = item;
                    return this;
                },
                extra: function extra() {
                    var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                    conf = Object.assign(conf, options);
                    return this;
                },
                init: function init() {
                    var logic = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : bsw.blank;

                    conf.el = selector;
                    that.cnf.v = new that.v(conf);
                    that.cnf.v.$nextTick(function () {
                        // logic
                        for (var fn in that.config.logic || []) {
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

    }, {
        key: 'notification',
        value: function notification(type, description, duration) {
            var _onClose = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : bsw.blank;

            var that = this;
            if (that.cnf.alertTypeForce && that.cnf.alertTypeForce !== 'notification') {
                return that[that.cnf.alertTypeForce](type, description, duration, _onClose);
            }

            if (typeof duration === 'undefined') {
                duration = that.cnf.notificationDuration;
            }

            var message = {
                success: that.lang.success,
                info: that.lang.info,
                warning: that.lang.warning,
                error: that.lang.error
            }[type];

            return new Promise(function (resolve) {
                that.cnf.v.$notification[type]({
                    placement: that.cnf.notificationPlacement,
                    message: message,
                    description: description,
                    duration: duration,
                    onClose: function onClose() {
                        resolve();
                        _onClose && _onClose();
                    }
                });
            });
        }

        /**
         * Show use notification once
         *
         * @returns {*}
         */

    }, {
        key: 'notificationOnce',
        value: function notificationOnce() {
            var force = this.cnf.alertTypeForce;
            this.cnf.alertTypeForce = false;
            var result = this.notification.apply(this, arguments);
            this.cnf.alertTypeForce = force;
            return result;
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

    }, {
        key: 'message',
        value: function message(type, description, duration) {
            var onClose = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : bsw.blank;

            var that = this;
            if (that.cnf.alertTypeForce && that.cnf.alertTypeForce !== 'message') {
                return that[that.cnf.alertTypeForce](type, description, duration, onClose);
            }
            if (typeof duration === 'undefined') {
                duration = this.cnf.messageDuration;
            }
            return this.cnf.v.$message[type](description, duration, onClose);
        }

        /**
         * Show use message once
         *
         * @returns {*}
         */

    }, {
        key: 'messageOnce',
        value: function messageOnce() {
            var force = this.cnf.alertTypeForce;
            this.cnf.alertTypeForce = false;
            var result = this.message.apply(this, arguments);
            this.cnf.alertTypeForce = force;
            return result;
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

    }, {
        key: 'confirm',
        value: function confirm(type, description, duration) {
            var onClose = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : bsw.blank;
            var options = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : {};

            var that = this;
            if (that.cnf.alertTypeForce && that.cnf.alertTypeForce !== 'confirm') {
                return that[that.cnf.alertTypeForce](type, description, duration, onClose);
            }

            var title = options.title || {
                success: that.lang.success,
                info: that.lang.info,
                warning: that.lang.warning,
                error: that.lang.error
            }[type];

            if (type === 'confirm' && typeof options.width === 'undefined') {
                options.width = that.popupCosySize().width;
            }

            return new Promise(function (resolve) {
                var modal = that.cnf.v['$' + type](Object.assign({
                    title: title,
                    content: description,
                    okText: that.lang.i_got_it,
                    onOk: options.onOk || onClose,
                    onCancel: onClose,
                    keyboard: false,
                    transitionName: that.cnf.transitionName,
                    maskTransitionName: that.cnf.maskTransitionName
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
         * Show use confirm once
         *
         * @returns {*}
         */

    }, {
        key: 'confirmOnce',
        value: function confirmOnce() {
            var force = this.cnf.alertTypeForce;
            this.cnf.alertTypeForce = false;
            var result = this.confirm.apply(this, arguments);
            this.cnf.alertTypeForce = force;
            return result;
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

    }, {
        key: 'success',
        value: function success(description, duration, onClose, type) {
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

    }, {
        key: 'info',
        value: function info(description, duration, onClose, type) {
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

    }, {
        key: 'warning',
        value: function warning(description, duration, onClose, type) {
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

    }, {
        key: 'error',
        value: function error(description, duration, onClose, type) {
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

    }, {
        key: 'request',
        value: function request(url) {
            var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
            var type = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : bsw.cnf.method.post;
            var upload = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
            var times = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : 1;

            var that = this;
            return new Promise(function (resolve, reject) {
                $.ajax({
                    type: upload ? that.cnf.method.post : type,
                    data: data,
                    url: url,
                    processData: !upload,
                    contentType: upload ? false : 'application/x-www-form-urlencoded',
                    timeout: that.cnf.requestTimeout * 1000 * (upload ? 10 : 1),
                    beforeSend: function beforeSend() {
                        if (that.cnf.v.noLoadingOnce) {
                            that.cnf.v.noLoadingOnce = false;
                        } else {
                            that.cnf.v.spinning = true;
                        }
                    },
                    success: function success(data) {
                        that.cnf.v.spinning = false;
                        resolve(data);
                    },
                    error: function error(obj) {
                        that.cnf.v.spinning = false;
                        if (obj.responseJSON) {
                            var result = obj.responseJSON;
                            var message = '[' + result.code + '] ' + result.message;
                            return that.confirm(result.classify, message, 0);
                        }

                        if (obj.responseText) {
                            var _message = '[' + obj.status + '] ' + obj.statusText;
                            return that.confirm('error', _message, 0);
                        }

                        if (obj.statusText === 'timeout') {
                            console.warn('Client request timeout: ', obj);
                            console.warn('Retry current request in times ' + times);
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

    }, {
        key: 'responseLocation',
        value: function responseLocation(result) {
            var success = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

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

    }, {
        key: 'responseFunction',
        value: function responseFunction(result) {
            var success = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

            var v = bsw.cnf.v;
            v.$nextTick(function () {
                var sets = typeof result.sets.function === 'undefined' ? result.sets.logic || {} : result.sets;
                if (typeof sets.function === 'undefined') {
                    return;
                }
                if (typeof v[sets.function] !== 'undefined') {
                    return v[sets.function](sets.functionArgs || {});
                } else if (typeof bsw[sets.function] !== 'undefined') {
                    return bsw[sets.function](sets.functionArgs || {});
                }
                return console.warn('Method ' + sets.function + ' is undefined.');
            });
        }

        /**
         * Logic after response
         *
         * @param result
         * @param success
         */

    }, {
        key: 'responseLogic',
        value: function responseLogic(result) {
            var success = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

            bsw.responseLocation(result, success);
            bsw.responseFunction(result, success);
        }

        /**
         * Handler for response
         *
         * @param result
         * @param duration
         */

    }, {
        key: 'response',
        value: function response(result, duration) {
            if (typeof result.code === 'undefined') {
                return bsw.error(bsw.lang.response_error_message);
            }
            var that = this;
            return new Promise(function (resolve, reject) {
                if (result.message) {
                    var _duration = that.isNull(result.duration) ? undefined : result.duration;
                    that[result.classify](result.message, _duration, null, result.type).then(function () {
                        result.error ? reject(result) : resolve(result);
                    }).catch(function (reason) {
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

    }, {
        key: 'popupCosySize',
        value: function popupCosySize() {
            var honest = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
            var d = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : document;

            var cnf = bsw.cnf;
            var width = d.body.clientWidth;
            var height = d.body.clientHeight;
            if (!honest) {
                width *= width < cnf.cosyMinWidth ? cnf.cosyMinWidthLess : cnf.cosyMinWidthMore;
                height *= height < cnf.cosyMinHeight ? cnf.cosyMinHeightLess : cnf.cosyMinHeightMore;
            }

            return {
                width: width,
                height: height,
                offset: {
                    width: d.body.offsetWidth,
                    height: d.body.offsetHeight
                },
                scroll: {
                    width: d.body.scrollWidth,
                    height: d.body.scrollHeight
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

    }, {
        key: 'rsaEncrypt',
        value: function rsaEncrypt(text) {
            var encrypt = new JSEncrypt();
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

    }, {
        key: 'base64Decode',
        value: function base64Decode(text) {
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

    }, {
        key: 'arrayBase64Decode',
        value: function arrayBase64Decode(target) {
            var exclude = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];

            for (var key in target) {
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

    }, {
        key: 'decodeURIComponentSafe',
        value: function decodeURIComponentSafe(uri, mod) {
            var out = String(),
                arr = void 0,
                i = 0,
                l = void 0,
                x = void 0;
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

    }, {
        key: 'arrayUrlDecode',
        value: function arrayUrlDecode(target) {
            var exclude = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];

            for (var key in target) {
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

    }, {
        key: 'jsonFnHandler',
        value: function jsonFnHandler(target, fnPrefix) {
            var fnTag = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'fn';

            var that = this;
            for (var key in target) {
                if (!target.hasOwnProperty(key)) {
                    continue;
                }
                var item = target[key];
                if (that.isJson(item)) {
                    target[key] = that.jsonFnHandler(item, fnPrefix, fnTag);
                } else if (that.isString(item) && item.startsWith(fnTag + ':')) {
                    var fn = that.ucFirst(item.split(':')[1]);
                    fn = '' + fnPrefix + fn;
                    if (typeof that.cnf.v[fn] !== 'undefined') {
                        target[key] = that.cnf.v[fn];
                    } else if (typeof that[fn] !== 'undefined') {
                        target[key] = that[fn];
                    } else {
                        target[key] = that.blank;
                        console.warn('Method ' + fn + ' is undefined.', target);
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

    }, {
        key: 'chart',
        value: function chart(option) {
            var that = this;
            var o = option.option;
            var chart = echarts.init(document.getElementById('chart-' + option.id), option.theme);

            chart.setOption(that.jsonFnHandler(o, 'chartHandler'));
            that.cnf.v.$nextTick(function () {
                chart.resize();
            });

            $(window).resize(function () {
                return chart.resize();
            });
        }

        /**
         * Chart handler -> tooltip stack
         *
         * @param params
         *
         * @returns {string}
         */

    }, {
        key: 'chartHandlerTooltipStack',
        value: function chartHandlerTooltipStack(params) {
            var total = 0;
            var _iteratorNormalCompletion6 = true;
            var _didIteratorError6 = false;
            var _iteratorError6 = undefined;

            try {
                for (var _iterator6 = params[Symbol.iterator](), _step6; !(_iteratorNormalCompletion6 = (_step6 = _iterator6.next()).done); _iteratorNormalCompletion6 = true) {
                    var item = _step6.value;

                    total += Math.floor(Number.parseFloat(item.data) * 100);
                }
            } catch (err) {
                _didIteratorError6 = true;
                _iteratorError6 = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion6 && _iterator6.return) {
                        _iterator6.return();
                    }
                } finally {
                    if (_didIteratorError6) {
                        throw _iteratorError6;
                    }
                }
            }

            total /= 100;

            var tpl = params[0].name + ' (' + total + ')<br>';
            var _iteratorNormalCompletion7 = true;
            var _didIteratorError7 = false;
            var _iteratorError7 = undefined;

            try {
                for (var _iterator7 = params[Symbol.iterator](), _step7; !(_iteratorNormalCompletion7 = (_step7 = _iterator7.next()).done); _iteratorNormalCompletion7 = true) {
                    var _item = _step7.value;

                    var percent = (Number.parseFloat(_item.data) / total || 0) * 100;
                    percent = percent.toFixed(2);
                    tpl += _item.marker + ' ' + _item.seriesName + ': ' + _item.data + ' (' + percent + '%)<br>';
                }
            } catch (err) {
                _didIteratorError7 = true;
                _iteratorError7 = err;
            } finally {
                try {
                    if (!_iteratorNormalCompletion7 && _iterator7.return) {
                        _iterator7.return();
                    }
                } finally {
                    if (_didIteratorError7) {
                        throw _iteratorError7;
                    }
                }
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

    }, {
        key: 'chartHandlerTooltipNormal',
        value: function chartHandlerTooltipNormal(params) {
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

    }, {
        key: 'chartHandlerTooltipPositionFixed',
        value: function chartHandlerTooltipPositionFixed(pos, params, dom, rect, size) {
            var obj = { top: 20 };
            obj[['left', 'right'][+(pos[0] < size.viewSize[0] / 2)]] = 10;
            return obj;
        }

        /**
         * Show message
         *
         * @param options
         */

    }, {
        key: 'showMessage',
        value: function showMessage(options) {
            var classify = options.classify || 'info';
            var duration = bsw.isNull(options.duration) ? undefined : options.duration;
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

    }, {
        key: 'showModal',
        value: function showModal(options) {
            var cnf = bsw.cnf;
            var v = cnf.v;
            v.modal.visible = false;
            if (typeof options.width === 'undefined') {
                options.width = bsw.popupCosySize().width;
            }
            var meta = bsw.cloneJson(v.modalMeta);
            cnf.maxZIndex += 1;
            meta.zIndex = cnf.maxZIndex;
            options = Object.assign(meta, options);
            if (options.footer) {
                v.footer = '_footer';
            } else {
                v.footer = 'footer';
            }
            if (options.debug) {
                console.log("For debug modal options: ", options);
            }
            v.modal = options;
            if (options.afterShow) {
                v.$nextTick(function () {
                    if (typeof bsw.cnf.v[options.afterShow] !== 'undefined') {
                        return bsw.cnf.v[options.afterShow](options.afterShowArgs || {});
                    } else if (typeof bsw[options.afterShow] !== 'undefined') {
                        return bsw[options.afterShow](options.afterShowArgs || {});
                    }
                    return console.warn('Method ' + options.afterShow + ' is undefined.');
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

    }, {
        key: 'showConfirm',
        value: function showConfirm(options) {
            var that = this;
            var cnf = bsw.cnf;
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
                zIndex: cnf.maxZIndex
            }, options));
        }

        /**
         * Show drawer popup
         *
         * @param options
         */

    }, {
        key: 'showDrawer',
        value: function showDrawer(options) {
            var cnf = bsw.cnf;
            var v = cnf.v;
            v.drawer.visible = false;
            if (typeof options.width === 'undefined') {
                options.width = bsw.popupCosySize().width;
            }
            var meta = bsw.cloneJson(v.drawerMeta);
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
                    return console.warn('Method ' + options.afterShow + ' is undefined.');
                });
            }
        }

        /**
         * Show result popup
         *
         * @param options
         */

    }, {
        key: 'showResult',
        value: function showResult(options) {
            var cnf = bsw.cnf;
            var v = cnf.v;
            v.result.visible = false;
            var meta = bsw.cloneJson(v.resultMeta);
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

    }, {
        key: 'showModalAfterRequest',
        value: function showModalAfterRequest(data, element) {
            var that = this;
            that.request(data.location).then(function (res) {
                that.response(res).then(function () {
                    var options = that.jsonFilter(Object.assign(data, {
                        width: res.sets.width || data.width || undefined,
                        title: res.sets.title || data.title || that.lang.modal_title,
                        content: res.sets.content
                    }));
                    that.showModal(options);
                    bsw.responseLogic(res);
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        }

        /**
         * Auto adjust iframe height
         */

    }, {
        key: 'autoIFrameHeight',
        value: function autoIFrameHeight() {
            var iframe = $(parent.document).find('iframe.bsw-iframe-modal');
            if (iframe.length === 0) {
                return;
            }

            var minHeight = parseInt(iframe.data('min-height'));
            var maxHeight = parseInt(iframe.data('max-height'));
            var overOffset = iframe.data('over-offset');
            var debugHeight = iframe.data('debug-height');

            minHeight = minHeight ? minHeight : 0;
            maxHeight = maxHeight ? maxHeight : 0;
            if (!minHeight && !maxHeight) {
                return;
            }

            var content = $('.bsw-content');
            var height = content.height() + bsw.pam(content.parent(), content).column;
            height = Math.ceil(height);
            if (debugHeight) {
                bsw.info(bsw.lang.real_iframe_height + ': ' + height);
            }

            if (!maxHeight) {
                maxHeight = bsw.popupCosySize(false, parent.document).height;
            }
            if (minHeight > maxHeight) {
                minHeight = maxHeight;
            }

            var latest = height;
            if (height < minHeight) {
                latest = minHeight;
            } else if (height > maxHeight) {
                latest = maxHeight;
            }

            if (overOffset === 'no' && Math.abs(height - latest) > bsw.cnf.autoHeightOffset) {
                return;
            }

            iframe.animate({ height: latest }, bsw.cnf.autoHeightDuration);
        }

        /**
         * Show iframe by popup (modal/drawer)
         *
         * @param data
         * @param element
         */

    }, {
        key: 'showIFrame',
        value: function showIFrame(data, element) {
            var that = this;
            var v = that.cnf.v;
            var size = that.popupCosySize();
            var repair = $(element).prev().attr('id');
            data.location = that.setParams({ iframe: true, repair: repair }, data.location);

            var mode = data.shape || 'modal';
            var clsName = ['bsw-iframe', 'bsw-iframe-' + mode].join(' ');
            var options = that.jsonFilter(Object.assign(data, {
                width: data.width || size.width,
                title: data.title === false ? data.title : data.title || that.lang.please_select
            }));

            if (mode === 'drawer') {

                options = Object.assign(options, {
                    content: '<iframe class="' + clsName + '" src="' + data.location + '"></iframe>'
                });

                that.showDrawer(options);
                v.$nextTick(function () {
                    var iframe = $('.bsw-iframe-' + mode);
                    var headerHeight = options.title ? 55 : 0;
                    var footerHeight = options.footer ? 73 : 0;
                    var height = that.popupCosySize(true).height;
                    if (options.placement === 'top' || options.placement === 'bottom') {
                        height = options.height || 512;
                    }
                    iframe.height(height - headerHeight - footerHeight);
                    iframe.parents('div.ant-drawer-body').css({ margin: 0, padding: 0 });
                });
            } else {

                var headerHeight = options.title ? 55 : 0;
                var footerHeight = options.footer ? 53 : 0;
                var height = data.height || size.height - headerHeight - footerHeight;

                var attributes = [];
                var overOffset = typeof data.overOffset !== 'undefined' ? data.overOffset : that.cnf.autoHeightOverOffset;
                attributes.push('data-over-offset=' + overOffset);

                var debugHeight = typeof data.debugRealHeight !== 'undefined' ? data.debugRealHeight : false;
                attributes.push('data-debug-height=' + debugHeight);

                if (typeof data.minHeight === 'undefined' && that.cnf.autoHeightOffset) {
                    data.minHeight = Math.max(height - that.cnf.autoHeightOffset, 0);
                }
                if (data.minHeight) {
                    attributes.push('data-min-height="' + data.minHeight + '"');
                }

                if (typeof data.maxHeight === 'undefined' && that.cnf.autoHeightOffset) {
                    data.maxHeight = height + that.cnf.autoHeightOffset;
                }
                if (data.maxHeight) {
                    attributes.push('data-max-height="' + data.maxHeight + '"');
                }

                attributes = bsw.arrayUnique(attributes).join(' ');
                options = Object.assign(options, {
                    content: '<iframe class="' + clsName + '" ' + attributes + ' src="' + data.location + '"></iframe>'
                });

                that.showModal(options);
                v.$nextTick(function () {
                    var iframe = $('.bsw-iframe-' + mode);
                    iframe.height(height);
                    iframe.parents('div.ant-modal-body').css({ margin: 0, padding: 0 });
                });
            }
        }

        /**
         * Modal onclick ok
         *
         * @param event
         */

    }, {
        key: 'modalOnOk',
        value: function modalOnOk(event) {
            if (event) {
                var element = $(event.target).parents('.ant-modal-footer').prev().find('.bsw-modal-data');
                var result = bsw.dispatcherByBswDataElement(element, 'ok');
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

    }, {
        key: 'modalOnCancel',
        value: function modalOnCancel(event) {
            if (event) {
                var element = $(event.target).parents('.ant-modal-footer').prev().find('.bsw-modal-data');
                var result = bsw.dispatcherByBswDataElement(element, 'cancel');
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

    }, {
        key: 'drawerOnOk',
        value: function drawerOnOk(event) {
            if (event) {
                var element = $(event.target).parents('.bsw-footer-bar');
                var result = bsw.dispatcherByBswDataElement(element, 'ok');
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

    }, {
        key: 'drawerOnCancel',
        value: function drawerOnCancel(event) {
            if (event) {
                var element = $(event.target).parents('.bsw-footer-bar');
                var result = bsw.dispatcherByBswDataElement(element, 'cancel');
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

    }, {
        key: 'resultOnOk',
        value: function resultOnOk(event) {
            if (event) {
                var element = $(event.target).parent().find('.bsw-result-data');
                var result = bsw.dispatcherByBswDataElement(element, 'ok');
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

    }, {
        key: 'resultOnCancel',
        value: function resultOnCancel(event) {
            if (event) {
                var element = $(event.target).parent().find('.bsw-result-data');
                var result = bsw.dispatcherByBswDataElement(element, 'cancel');
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

    }, {
        key: 'changeImageCaptcha',
        value: function changeImageCaptcha() {
            var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'img.bsw-captcha';

            var that = this;
            $(selector).off('click').on('click', function () {
                var src = $(this).attr('src');
                src = that.setParams({ t: that.timestamp() }, src);
                $(this).attr('src', src);
            });
        }

        /**
         * Message auto discovery
         *
         * @param discovery
         */

    }, {
        key: 'messageAutoDiscovery',
        value: function messageAutoDiscovery(discovery) {
            var that = this;
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
         * Get data
         *
         * @param object
         * @param field
         * @param def
         *
         * @returns {*|{}}
         */

    }, {
        key: 'getXData',
        value: function getXData(object, field) {
            var def = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};

            var _field = bsw.ucFirst(field);
            return object[0]['data' + _field] || object.data(field) || def;
        }

        /**
         * Get bsw data
         *
         * @param object
         *
         * @returns {*|{}}
         */

    }, {
        key: 'getBswData',
        value: function getBswData(object) {
            var data = bsw.getXData(object, 'bsw');
            data = bsw.arrayUrlDecode(data, ['location', 'href', 'url', 'query']);
            return data;
        }

        /**
         * Dispatcher by bsw data
         *
         * @param data
         * @param element
         */

    }, {
        key: 'dispatcherByBswData',
        value: function dispatcherByBswData(data, element) {
            var that = this;
            if (data.iframe) {
                var d = bsw.cloneJson(data);
                delete d.iframe;
                parent.postMessage({ data: d, function: 'dispatcherByBswData' }, '*');
                return;
            }

            var action = function action() {
                var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

                if (!data.function || data.function.length === 0) {
                    return console.warn('Attribute function should be configure in options.', data);
                }
                data = Object.assign(data, options);
                if (typeof that.cnf.v[data.function] !== 'undefined') {
                    return that.cnf.v[data.function](data, element);
                } else if (typeof that[data.function] !== 'undefined') {
                    return that[data.function](data, element);
                }
                return console.warn('Method ' + data.function + ' is undefined.', data);
            };
            if (typeof data.confirm === 'undefined') {
                return action();
            }

            that.showConfirm({
                content: function content(h) {
                    if (!data.confirmCheckbox) {
                        return data.confirm;
                    }
                    return h('div', null, [h('p', null, data.confirm), h('a-checkbox', { class: 'bsw-confirm-checkbox', style: { marginTop: '10px' } }, data.confirmCheckbox)]);
                },
                onOk: function onOk() {
                    var options = { confirmIsCheck: false };
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

    }, {
        key: 'dispatcherByBswDataElement',
        value: function dispatcherByBswDataElement(element, fn) {
            if (!element.length) {
                return;
            }
            var that = this;
            var data = element[0].dataBsw;
            if (data[fn]) {

                if (typeof that.cnf.v[data[fn]] !== 'undefined') {
                    return that.cnf.v[data[fn]](data.extra || {}, element);
                } else if (typeof that[data[fn]] !== 'undefined') {
                    return that[data[fn]](data.extra || {}, element);
                }
                return console.warn('Method ' + data[fn] + ' is undefined.', data);
            }
        }

        /**
         * Redirect (by bsw data)
         *
         * @param data
         * {*}
         */

    }, {
        key: 'redirect',
        value: function redirect(data) {
            if (data.function && data.function !== 'redirect') {
                return bsw.dispatcherByBswData(data, $('body'));
            }
            var url = data.location;
            if (bsw.isMobile() && bsw.cnf.mobileDefaultCollapsed) {
                bsw.cookie().set('bsw_menu_collapsed', 'yes');
            }
            if (url.startsWith('http') || url.startsWith('/') || url.startsWith('#')) {
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

    }, {
        key: 'filterOptionForMentions',
        value: function filterOptionForMentions(input, option) {
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

    }, {
        key: 'filterOptionForAutoComplete',
        value: function filterOptionForAutoComplete(input, option) {
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

    }, {
        key: 'filterOptionForTransfer',
        value: function filterOptionForTransfer(input, option) {
            return option.title.indexOf(input) !== -1;
        }

        /**
         * Init highlight
         */

    }, {
        key: 'initHighlight',
        value: function initHighlight() {
            if (typeof hljs === 'undefined') {
                return;
            }
            hljs.initHighlighting();
        }

        /**
         * Init highlight block
         */

    }, {
        key: 'initHighlightBlock',
        value: function initHighlightBlock(params) {
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

    }, {
        key: 'initVConsole',
        value: function initVConsole() {
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

    }, {
        key: 'initCkEditor',
        value: function initCkEditor() {
            var form = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'persistenceForm';
            var selector = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '.bsw-persistence .bsw-ck';

            var CKE = window.DecoupledEditor || window.DecoupledDocumentEditor;
            if (typeof CKE === 'undefined' || !CKE) {
                return;
            }
            var that = this;
            var v = that.cnf.v;
            $(selector).each(function () {
                var em = this;
                var id = $(em).prev('textarea').attr('id');
                var container = $(em).find('.bsw-ck-editor');
                var options = {
                    language: that.lang.i18n_editor,
                    placeholder: $(em).attr('placeholder'),
                    removePlugins: ['Title'],
                    toolbar: {
                        items: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'todoList', '|', 'removeFormat', 'strikethrough', 'underline', 'subscript', 'superscript', 'horizontalLine', '|', 'fontSize', 'fontColor', 'fontBackgroundColor', 'fontFamily', 'highlight', '|', 'outdent', 'indent', 'alignment', '|', 'imageUpload', 'imageInsert', 'blockQuote', 'insertTable', 'mediaEmbed', '|', 'htmlEmbed', 'codeBlock', 'code', 'specialCharacters', '|', 'undo', 'redo']
                    },
                    image: {
                        toolbar: ['imageTextAlternative', 'imageStyle:full', 'imageStyle:side', 'linkImage']
                    },
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableCellProperties', 'tableProperties']
                    }
                };
                var _options = bsw.getXData($(em), 'options');
                options = Object.assign(options, _options);
                if (options.debug) {
                    console.log("For debug CkEditor options: ", options, _options);
                }
                CKE.create(container[0], options).then(function (editor) {
                    v.ckEditor[id] = editor;
                    editor.isReadOnly = $(em).attr('disabled') === 'disabled';
                    editor.plugins.get('FileRepository').createUploadAdapter = function (loader) {
                        return new FileUploadAdapter(editor, loader, v.init.uploadApiUrl);
                    };
                    v.ckEditor[id].model.document.on('change:data', function () {
                        if (v[form]) {
                            v[form].setFieldsValue(_defineProperty({}, id, v.ckEditor[id].getData()));
                        }
                    });
                    $(em).find('.bsw-ck-toolbar').append(editor.ui.view.toolbar.element);
                }).catch(function (err) {
                    console.warn(err.stack);
                });
            });
        }

        /**
         * Init clipboard
         *
         * @param selector
         */

    }, {
        key: 'initClipboard',
        value: function initClipboard() {
            var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '.ant-btn';

            if (!window.Clipboard) {
                return;
            }
            var that = this;
            var clipboard = new Clipboard(selector, {
                text: function text(trigger) {
                    if (typeof that.cnf.v.copy !== 'undefined' && that.cnf.v.copy) {
                        var text = that.cnf.v.copy;
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

    }, {
        key: 'initScrollX',
        value: function initScrollX() {
            var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '.bsw-scroll-x';

            var cnf = bsw.cnf;
            $(selector).each(function () {
                var arrow = $(this);
                var step = parseInt(arrow.data('step'));
                var target = function target(index) {
                    return typeof index === 'undefined' ? $(arrow.data('target-selector')) : $(arrow.data('target-selector'))[index];
                };
                if (target().length === 0) {
                    return true;
                }

                var offset = bsw.offset(arrow.parent());
                var position = $(this).hasClass('left') ? -1 : 1;
                if (position === -1) {
                    arrow.css({ left: offset.left });
                } else if (position === 1) {
                    arrow.css({ right: offset.right });
                }

                var maxScrollTop = function maxScrollTop() {
                    return document.body.scrollHeight - document.body.clientHeight;
                };
                var maxScrollLeft = function maxScrollLeft() {
                    return target(0).scrollWidth - target(0).clientWidth;
                };
                arrow.off('click').on('click', function () {
                    var nowScrollLeft = target().scrollLeft();
                    if (position === -1 && nowScrollLeft <= 1) {
                        return bsw.warning(bsw.lang.is_far_left, 1);
                    }
                    if (position === 1 && nowScrollLeft >= maxScrollLeft() - 1) {
                        return bsw.warning(bsw.lang.is_far_right, 1);
                    }
                    target().stop().animate({ scrollLeft: nowScrollLeft + step * position + 'px' });
                });

                $(window).resize(function () {
                    var x = maxScrollLeft();
                    var y = maxScrollTop();
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

    }, {
        key: 'doAnimateCSS',
        value: function doAnimateCSS(selector, animation) {
            var duration = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '1s';
            var prefix = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '';

            new Promise(function (resolve, reject) {
                var animationName = '' + prefix + animation;
                var node = $(selector);
                if (selector.length === 0) {
                    return;
                }
                node.css('animation-duration', duration);
                node.addClass(prefix + 'animated ' + animationName);

                function handleAnimationEnd() {
                    node.removeClass(prefix + 'animated ' + animationName);
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

    }, {
        key: 'prominentAnchor',
        value: function prominentAnchor() {
            var animate = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'flash';
            var duration = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '.65s';

            var anchor = bsw.getAnchor();
            if (!anchor.length) {
                return;
            }
            if (anchor.startsWith('trigger.')) {
                var name = anchor.split('.')[1];
                var trigger = $('[name="' + name + '"]');
                if (name.length && trigger.length) {
                    trigger.click();
                }
            } else if ($('#' + anchor).length) {
                bsw.doAnimateCSS('#' + anchor, animate, duration);
            }
        }

        /**
         * Cancel selection
         */

    }, {
        key: 'cancelSelection',
        value: function cancelSelection() {
            document.selection && document.selection.empty && document.selection.empty();
            window.getSelection && window.getSelection().removeAllRanges();
        }

        /**
         * Upward infect class
         *
         * @param selector
         */

    }, {
        key: 'initUpwardInfect',
        value: function initUpwardInfect() {
            var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '.bsw-upward-infect';

            $(selector).each(function () {
                var element = $(this);
                for (var i = 0; i < $(this).data('infect-level'); i++) {
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

    }, {
        key: 'dispatcherByBswDataInParent',
        value: function dispatcherByBswDataInParent(data, element) {
            var that = this;
            var d = data.data;
            var closeModal = typeof d.closePrevModal === 'undefined' ? true : d.closePrevModal;
            var closeDrawer = typeof d.closePrevDrawer === 'undefined' ? true : d.closePrevDrawer;
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

    }, {
        key: 'fillParentFormInParent',
        value: function fillParentFormInParent(data, element) {
            var form = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'persistenceForm';

            var v = bsw.cnf.v;
            var closeModal = typeof data.closePrevModal === 'undefined' ? true : data.closePrevModal;
            var closeDrawer = typeof data.closePrevDrawer === 'undefined' ? true : data.closePrevDrawer;
            closeModal && bsw.modalOnCancel();
            closeDrawer && bsw.drawerOnCancel();
            v.$nextTick(function () {
                if (v[form] && data.repair) {
                    v[form].setFieldsValue(_defineProperty({}, data.repair, data.ids));
                }
            });
        }

        /**
         * [in parent] fill form after ajax
         *
         * @param res
         * @param element
         */

    }, {
        key: 'fillParentFormAfterAjaxInParent',
        value: function fillParentFormAfterAjaxInParent(res, element) {
            var data = res.response.sets;
            data.repair = data.arguments.repair;
            bsw.fillParentFormInParent(data, element);
        }

        /**
         * [in parent] Handler response
         *
         * @param data
         * @param element
         */

    }, {
        key: 'handleResponseInParent',
        value: function handleResponseInParent(data, element) {
            var that = this;
            if (typeof data.response === 'undefined') {
                return;
            }

            var res = data.response;
            if (res.classify === 'success') {
                var closeModal = typeof res.sets.closePrevModal === 'undefined' ? true : res.sets.closePrevModal;
                var closeDrawer = typeof res.sets.closePrevDrawer === 'undefined' ? true : res.sets.closePrevDrawer;
                closeModal && that.modalOnCancel();
                closeDrawer && that.drawerOnCancel();
            }
            that.cnf.v.$nextTick(function () {
                that.response(res).then(function () {
                    bsw.responseLogic(res);
                }).catch(function (reason) {
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

    }, {
        key: 'showIFrameInParent',
        value: function showIFrameInParent(data, element) {
            bsw.showIFrame(data.response.sets, element);
        }

        /**
         * Full screen
         *
         * @param data
         * @param element
         */

    }, {
        key: 'fullScreenToggle',
        value: function fullScreenToggle(data, element) {
            if (!window.screenfull) {
                return;
            }
            var container = $(data.element)[0];
            if (screenfull.isEnabled) {
                screenfull.toggle(container);
            } else {
                console.warn('Your browser is not supported.');
            }
        }
    }]);

    return FoundationAntD;
}(FoundationTools);

// -- eof --
