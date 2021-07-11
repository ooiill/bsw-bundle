'use strict';

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

bsw.configure({
    method: {
        grantChangeSelectedList: function grantChangeSelectedList(handler) {
            var that = this;
            var form = 'persistenceForm';
            $.each(that.init.selectedList, function (key, meta) {
                var disabled = bsw.arrayIntersect(meta, that.init.disabledList);
                var selected = that[form].getFieldValue(key);
                var values = [];
                var _iteratorNormalCompletion = true;
                var _didIteratorError = false;
                var _iteratorError = undefined;

                try {
                    for (var _iterator = meta[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                        var v = _step.value;

                        var result = handler(v, disabled, selected);
                        if (result) {
                            values.push(v);
                        }
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

                that[form].setFieldsValue(_defineProperty({}, key, values));
            });
        },
        grantSelectAll: function grantSelectAll() {
            this.grantChangeSelectedList(function (v, disabled, selected) {
                if (disabled.includes(v)) {
                    return selected.includes(v);
                }
                return true;
            });
        },
        grantUnSelectAll: function grantUnSelectAll() {
            this.grantChangeSelectedList(function (v, disabled, selected) {
                return disabled.includes(v) && selected.includes(v);
            });
        }
    }
});
