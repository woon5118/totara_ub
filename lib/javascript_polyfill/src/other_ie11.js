(function() {

// ECMA-262 7.3.5. CreateMethodProperty ( O, P, V )
function createMethodProperty(O, P, V) {
	Object.defineProperty(O, P, { value: V, writable: true, enumerable: false, configurable: true });
}

/*
 *  ES6 custom events polyfill required for IE11 when not using jQuery
 *  https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/CustomEvent
 */

(function() {

    if (typeof window.CustomEvent === "function") {
        return false;
    }
    function CustomEvent(event, params) {
        params = params || {bubbles: false, cancelable: false, detail: undefined};
        var evt = document.createEvent('CustomEvent');
        evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
        return evt;
    }

   CustomEvent.prototype = window.Event.prototype;

   window.CustomEvent = CustomEvent;
 })();


/*
 *  ES6 Element.closest() polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/API/Element/closest
 */

if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
    Element.prototype.closest = function(s) {
        var el = this;
        if (!document.documentElement.contains(el)) {
             return null;
        }
        do {
            if (el.matches(s)) {
                return el;
            }
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}

/*
 *  ES6 Element.matches() polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/API/Element/matches
 */

 if (!Element.prototype.matches) {
     Element.prototype.matches = Element.prototype.msMatchesSelector;
 }


 /*
 *  ES6 ChildNode.remove() polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/API/ChildNode/remove
 */

(function(arr) {
    arr.forEach(function(item) {
        if (item.hasOwnProperty('remove')) {
            return;
        }
        Object.defineProperty(item, 'remove', {
            configurable: true,
            enumerable: true,
            writable: true,
            value: function remove() {
                if (this.parentNode !== null) {
                    this.parentNode.removeChild(this);
                }
            }
        });
    });
})([Element.prototype, CharacterData.prototype, DocumentType.prototype]);


/*
 *  ES6 Object.assign() polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/assign
 */

 if (typeof Object.assign != 'function') {
    // Must be writable: true, enumerable: false, configurable: true
    createMethodProperty(Object, "assign", function assign(target, varArgs) { // .length of function is 2
        'use strict';
        if (target == null) { // TypeError if undefined or null
            throw new TypeError('Cannot convert undefined or null to object');
        }

        var to = Object(target);

        for (var index = 1; index < arguments.length; index++) {
            var nextSource = arguments[index];
            if (nextSource != null) { // Skip over if undefined or null
                for (var nextKey in nextSource) {
                    // Avoid bugs when hasOwnProperty is shadowed
                    if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                        to[nextKey] = nextSource[nextKey];
                    }
                }
            }
        }
        return to;
    });
}


/*
 *  ES6 String.prototype.startsWith() polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/startsWith
 */

if (!String.prototype.startsWith) {
    createMethodProperty(String.prototype, 'startsWith', function(search, pos) {
        return this.substr(!pos || pos < 0 ? 0 : +pos, search.length) === search;
    });
}


/*
 *  ES6 String.prototype.endsWith() polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/endsWith
 */

if (!String.prototype.endsWith) {
    createMethodProperty(String.prototype, 'endsWith', function(search, this_len) {
        if (this_len === undefined || this_len > this.length) {
            this_len = this.length;
        }
        return this.substring(this_len - search.length, this_len) === search;
    });
}


/*
 *  ES6 String.prototype.includes polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/includes
 */

if (!String.prototype.includes) {
    createMethodProperty(String.prototype, 'includes', function(search, start) {
        if (typeof start !== 'number') {
            start = 0
        }

        if (start + search.length > this.length) {
            return false
        } else {
            return this.indexOf(search, start) !== -1
        }
    });
}


/*
 *  ES6 Array.prototype.includes() polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/includes
 */

if (!Array.prototype.includes) {
    createMethodProperty(Array.prototype, 'includes', function(valueToFind, fromIndex) {
        if (this == null) {
            throw new TypeError('"this" is null or not defined');
        }

        // 1. Let O be ? ToObject(this value).
        var o = Object(this);

        // 2. Let len be ? ToLength(? Get(O, "length")).
        var len = o.length >>> 0;

        // 3. If len is 0, return false.
        if (len === 0) {
            return false;
        }

        // 4. Let n be ? ToInteger(fromIndex).
        //    (If fromIndex is undefined, this step produces the value 0.)
        var n = fromIndex | 0;

        // 5. If n ≥ 0, then
        //  a. Let k be n.
        // 6. Else n < 0,
        //  a. Let k be len + n.
        //  b. If k < 0, let k be 0.
        var k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);

        function sameValueZero(x, y) {
            return (
                x === y ||
                (typeof x === 'number' &&
                    typeof y === 'number' &&
                    isNaN(x) &&
                    isNaN(y))
            );
        }

        // 7. Repeat, while k < len
        while (k < len) {
            // a. Let elementK be the result of ? Get(O, ! ToString(k)).
            // b. If SameValueZero(valueToFind, elementK) is true, return true.
            if (sameValueZero(o[k], valueToFind)) {
                return true;
            }
            // c. Increase k by 1.
            k++;
        }

        // 8. Return false
        return false;
    });
}


/*
 *  ES6 Array.prototype.findIndex() polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/findIndex
 */

if (!Array.prototype.findIndex) {
    createMethodProperty(Array.prototype, 'findIndex', function(predicate) {
        // 1. Let O be ? ToObject(this value).
        if (this == null) {
            throw new TypeError('"this" is null or not defined');
        }

        var o = Object(this);

        // 2. Let len be ? ToLength(? Get(O, "length")).
        var len = o.length >>> 0;

        // 3. If IsCallable(predicate) is false, throw a TypeError exception.
        if (typeof predicate !== 'function') {
            throw new TypeError('predicate must be a function');
        }

        // 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
        var thisArg = arguments[1];

        // 5. Let k be 0.
        var k = 0;

        // 6. Repeat, while k < len
        while (k < len) {
            // a. Let Pk be ! ToString(k).
            // b. Let kValue be ? Get(O, Pk).
            // c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
            // d. If testResult is true, return k.
            var kValue = o[k];
            if (predicate.call(thisArg, kValue, k, o)) {
                return k;
            }
            // e. Increase k by 1.
            k++;
        }

        // 7. Return -1.
        return -1;
    });
}


/*
 *  ES6 Array.prototype.find() polyfill required for IE11
 */

if (!Array.prototype.find) {
    createMethodProperty(Array.prototype, 'find', function(predicate) {
        var o = Object(this);
        var index = Array.prototype.findIndex.apply(this, arguments);
        return index === -1 ? undefined : o[index];
    });
}


/*
 *  ES6 NodeList.prototype.forEach() polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/API/NodeList/forEach
 */

if (window.NodeList && !NodeList.prototype.forEach) {
    NodeList.prototype.forEach = Array.prototype.forEach;
}

/*
 *  ES6 Object.entries polyfill required for IE11
 */

if (!Object.entries) {
    createMethodProperty(Object, 'entries', function entries(obj) {
        return Object.keys(obj).map(function(key) {
            return [key, obj[key]];
        });
    });
}


/*
 *  ES6 Object.values polyfill required for IE11
 */

if (!Object.values) {
    createMethodProperty(Object, 'values', function values(obj) {
        return Object.keys(obj).map(function(key) {
            return obj[key];
        });
    });
}

/*
 *  ES6 Number.isFinite polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isFinite
 */

if (!Number.isFinite) {
    createMethodProperty(Number, 'isFinite', function(value) {
        return typeof value === 'number' && isFinite(value);
    });
}

/*
 *  ES6 Number.isInteger polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isInteger
 */

if (!Number.isInteger) {
    createMethodProperty(Number, 'isInteger', function(value) {
        return typeof value === 'number' && isFinite(value) && Math.floor(value) === value;
    });
}

/*
 *  ES6 Number.isNaN polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/isNaN
 */

if (!Number.isNaN) {
    createMethodProperty(Number, 'isNaN', function(value) {
        return typeof value === 'number' && isNaN(value);
    });
}

/*
 *  ES6 Number.parseFloat polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/parseFloat
 */

if (!Number.parseFloat) {
    createMethodProperty(Number, 'parseFloat', parseFloat);
}

/*
 *  ES6 Number.parseInt polyfill required for IE11
 *  https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Number/parseInt
 */

if (!Number.parseInt) {
    createMethodProperty(Number, 'parseInt', parseInt);
}

})();
