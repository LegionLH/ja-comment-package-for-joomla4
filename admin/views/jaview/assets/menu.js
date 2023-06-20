/*! js-cookie v3.0.1 | MIT */
;
(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
  typeof define === 'function' && define.amd ? define(factory) :
  (global = global || self, (function () {
    var current = global.Cookies;
    var exports = global.Cookies = factory();
    exports.noConflict = function () { global.Cookies = current; return exports; };
  }()));
}(this, (function () { 'use strict';

  /* eslint-disable no-var */
  function assign (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        target[key] = source[key];
      }
    }
    return target
  }
  /* eslint-enable no-var */

  /* eslint-disable no-var */
  var defaultConverter = {
    read: function (value) {
      if (value[0] === '"') {
        value = value.slice(1, -1);
      }
      return value.replace(/(%[\dA-F]{2})+/gi, decodeURIComponent)
    },
    write: function (value) {
      return encodeURIComponent(value).replace(
        /%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g,
        decodeURIComponent
      )
    }
  };
  /* eslint-enable no-var */

  /* eslint-disable no-var */

  function init (converter, defaultAttributes) {
    function set (key, value, attributes) {
      if (typeof document === 'undefined') {
        return
      }

      attributes = assign({}, defaultAttributes, attributes);

      if (typeof attributes.expires === 'number') {
        attributes.expires = new Date(Date.now() + attributes.expires * 864e5);
      }
      if (attributes.expires) {
        attributes.expires = attributes.expires.toUTCString();
      }

      key = encodeURIComponent(key)
        .replace(/%(2[346B]|5E|60|7C)/g, decodeURIComponent)
        .replace(/[()]/g, escape);

      var stringifiedAttributes = '';
      for (var attributeName in attributes) {
        if (!attributes[attributeName]) {
          continue
        }

        stringifiedAttributes += '; ' + attributeName;

        if (attributes[attributeName] === true) {
          continue
        }

        // Considers RFC 6265 section 5.2:
        // ...
        // 3.  If the remaining unparsed-attributes contains a %x3B (";")
        //     character:
        // Consume the characters of the unparsed-attributes up to,
        // not including, the first %x3B (";") character.
        // ...
        stringifiedAttributes += '=' + attributes[attributeName].split(';')[0];
      }

      return (document.cookie =
        key + '=' + converter.write(value, key) + stringifiedAttributes)
    }

    function get (key) {
      if (typeof document === 'undefined' || (arguments.length && !key)) {
        return
      }

      // To prevent the for loop in the first place assign an empty array
      // in case there are no cookies at all.
      var cookies = document.cookie ? document.cookie.split('; ') : [];
      var jar = {};
      for (var i = 0; i < cookies.length; i++) {
        var parts = cookies[i].split('=');
        var value = parts.slice(1).join('=');

        try {
          var foundKey = decodeURIComponent(parts[0]);
          jar[foundKey] = converter.read(value, foundKey);

          if (key === foundKey) {
            break
          }
        } catch (e) {}
      }

      return key ? jar[key] : jar
    }

    return Object.create(
      {
        set: set,
        get: get,
        remove: function (key, attributes) {
          set(
            key,
            '',
            assign({}, attributes, {
              expires: -1
            })
          );
        },
        withAttributes: function (attributes) {
          return init(this.converter, assign({}, this.attributes, attributes))
        },
        withConverter: function (converter) {
          return init(assign({}, this.converter, converter), this.attributes)
        }
      },
      {
        attributes: { value: Object.freeze(defaultAttributes) },
        converter: { value: Object.freeze(converter) }
      }
    )
  }

  var api = init(defaultConverter, { path: '/' });
  /* eslint-enable no-var */

  return api;

})));

/**
 * ------------------------------------------------------------------------
 * JA Comment Package for Joomla 2.5 & 3.x
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
var JATreeMenu = new function() {
	this.menuid = 'jacom-mainnav';
	this.openedcls = 'opened';
	this.closedcls = 'closed';
	this.initmenu = function() {
		var mainnav = document.getElementById(this.menuid);
		if (!mainnav)
			return;
		var uls = mainnav.getElementsByTagName('ul');
		var menustatus = Cookies.get('menustatus');
		//var menustatus = Cookie.read('menustatus');

		open_obj = document.getElementById('menu_open');
		close_obj = document.getElementById('menu_close');

		if (menustatus == this.closedcls) {
			close_obj.className = 'closeall closed';
			open_obj.className = 'openall';
		} else {
			open_obj.className = 'openall opened';
			close_obj.className = 'closeall';
		}

		for ( var i = 1; i < uls.length; i++) {
			var li = uls[i].parentNode;
			if (li.tagName.toLowerCase() != 'li')
				continue;

			if (li.className.indexOf('opened') == -1) {

				if (menustatus == "" || menustatus == null) {
					menustatus = this.openedcls;
				}
				li.className += " " + menustatus;
			}
			var a = li.getElementsByTagName('a')[0];
			a._p = li;
			a._o = this.openedcls;
			a._c = this.closedcls;
			a.onclick = function() {
				var _p = this._p;
				if (_p.className.indexOf(this._o) == -1) {
					_p.className = _p.className.replace(new RegExp(" "
							+ this._c + "\\b"), " " + this._o);
				} else {
					_p.className = _p.className.replace(new RegExp(" "
							+ this._o + "\\b"), " " + this._c);
				}
			}
			a.href = 'javascript:;';
		}
	};

	this.openall = function() {
		open_obj = document.getElementById('menu_open');
		open_obj.className = 'openall opened';
		close_obj = document.getElementById('menu_close');
		close_obj.className = 'closeall';
		Cookies.set('menustatus',this.openedcls);
		//Cookie.write('menustatus', this.openedcls);
		var mainnav = document.getElementById(this.menuid);
		if (!mainnav)
			return;
		var uls = mainnav.getElementsByTagName('ul');
		for ( var i = 1; i < uls.length; i++) {
			var li = uls[i].parentNode;
			if (li.tagName.toLowerCase() != 'li')
				continue;
			li.className = li.className.replace(new RegExp(" " + this.closedcls
					+ "\\b"), " " + this.openedcls);
		}

	};
	this.closeall = function() {
		close_obj = document.getElementById('menu_close');
		close_obj.className = 'closed closeall';
		open_obj = document.getElementById('menu_open');
		open_obj.className = 'openall';
		//Cookie.write('menustatus', this.closedcls);
		Cookies.set('menustatus',this.closedcls);
		var mainnav = document.getElementById(this.menuid);
		if (!mainnav)
			return;
		var uls = mainnav.getElementsByTagName('ul');
		for ( var i = 1; i < uls.length; i++) {
			var li = uls[i].parentNode;
			if (li.tagName.toLowerCase() != 'li')
				continue;
			li.className = li.className.replace(new RegExp(" " + this.openedcls
					+ "\\b"), " " + this.closedcls);
		}
	};
}
