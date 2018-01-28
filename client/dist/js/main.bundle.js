/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

module.exports = jQuery;

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(jQuery) {(function ($) {

    $.entwine('ss', function ($) {
        $('.cms-content-fields > #Form_EditForm_error').entwine({
            'onadd': function onadd() {
                var $target = $('.blog-admin-outer');
                if ($target.length == 1) {
                    $target.prepend(this);
                }
            }
        });

        $('.toggle-description').entwine({
            'onadd': function onadd() {
                var $this = $(this);

                if ($this.hasClass('toggle-description-enabled')) {
                    return;
                }

                $this.addClass('toggle-description-enabled');

                var shown = false;
                var $helpInfo = $this.closest('.field').find('.form-text');

                $this.on('click', function () {
                    $helpInfo[shown ? 'hide' : 'show']();
                    $this.toggleClass('toggle-description-shown');
                    shown = !shown;
                });

                $helpInfo.hide();

                $this.parent().addClass('toggle-description-correct-right');
                $this.parent().prev('.middleColumn').addClass('toggle-description-correct-middle');
                $this.parent().next('.description').addClass('toggle-description-correct-description');
            }
        });

        $('.MergeAction').entwine({
            'onadd': function onadd() {
                var $this = $(this);

                $this.on('click', 'select', function () {
                    return false;
                });

                $this.children('button').each(function (i, button) {
                    var $button = $(button);
                    var $select = $button.prev('select');

                    $button.before('<input type="hidden" name="' + $button.attr('data-target') + '" value="' + $select.val() + '" />');
                });

                $this.on('change', 'select', function (e) {
                    var $target = $(e.target);

                    $target.next('input').val($target.val());
                });

                $this.children('button, select').hide();

                $this.on('click', '.MergeActionReveal', function (e) {
                    var $target = $(e.target);

                    $target.parent().children('button, select').show();
                    $target.hide();

                    return false;
                });
            }
        });

        $('.blog-admin-sidebar.cms-panel').entwine({
            MinInnerWidth: 620,
            onadd: function onadd() {
                this._super();
                this.updateLayout();

                if (!this.hasClass('collapsed') && $(".blog-admin-outer").width() < this.getMinInnerWidth()) {
                    this.collapsePanel();
                }

                window.onresize = function () {
                    this.updateLayout();
                }.bind(this);
            },
            togglePanel: function togglePanel(bool, silent) {
                this._super(bool, silent);
                this.updateLayout();
            },

            updateLayout: function updateLayout() {
                $(this).css('height', '100%');
                var currentHeight = $(this).outerHeight();
                var bottomHeight = $('.cms-content-actions').eq(0).outerHeight();
                $(this).css('height', currentHeight - bottomHeight + "px");
                $(this).css('bottom', bottomHeight + "px");

                $('.cms-container').updateLayoutOptions({
                    minContentWidth: 820 + this.width()
                });
            }
        });
    });
})(jQuery);
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(0)))

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(jQuery) {(function ($) {
    $.entwine('ss', function ($) {
        $('.add-existing-autocompleter input.text').entwine({
            'onkeydown': function onkeydown(e) {
                if (e.which == 13) {
                    $parent = $(this).parents('.add-existing-autocompleter');
                    $parent.find('button[type="submit"]').click();
                    return false;
                }
            }
        });
    });
})(jQuery);
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(0)))

/***/ }),
/* 3 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_bundles_cms_js__ = __webpack_require__(1);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0_bundles_cms_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0_bundles_cms_js__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_bundles_gridfieldaddbydbfield_js__ = __webpack_require__(2);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_bundles_gridfieldaddbydbfield_js___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_bundles_gridfieldaddbydbfield_js__);



/***/ })
/******/ ]);
//# sourceMappingURL=main.bundle.js.map