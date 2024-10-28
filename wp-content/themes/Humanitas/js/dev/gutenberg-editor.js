/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./js/src/gutenberg-editor.js":
/*!************************************!*\
  !*** ./js/src/gutenberg-editor.js ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _modules_sliders__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./modules/sliders */ \"./js/src/modules/sliders.js\");\n/* harmony import */ var _modules_block_offer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./modules/block-offer */ \"./js/src/modules/block-offer.js\");\n\n\n/**\n * @Author: Roni Laukkarinen\n * @Date:   2022-02-11 15:38:14\n * @Last Modified by:   Roni Laukkarinen\n * @Last Modified time: 2022-09-29 13:53:14\n */\n// Declare the blocks you'd like to style.\n// eslint-disable-next-line\nwp.blocks.registerBlockStyle('core/paragraph', {\n  name: 'paragraph-lead',\n  label: 'Lead'\n});\n\n// When document is ready as in when blocks are fully loaded\nwindow.addEventListener('load', function () {\n  /**\n   * initializeBlock\n   *\n   * Adds custom JavaScript to the block HTML.\n   *\n   * @date    15/4/19\n   * @since   1.0.0\n   *\n   * @param   object $block The block jQuery element.\n   * @param   object attributes The block attributes (only available when editing).\n   * @return  void\n   *\n   * @source https://www.advancedcustomfields.com/resources/acf_register_block_type/\n  */\n  (0,_modules_block_offer__WEBPACK_IMPORTED_MODULE_1__[\"default\"])();\n  (0,_modules_sliders__WEBPACK_IMPORTED_MODULE_0__[\"default\"])();\n  // sliders();\n\n  // eslint-disable-next-line\n  var initializeBlock = function initializeBlock($block) {\n    // Your scripts here\n    (0,_modules_sliders__WEBPACK_IMPORTED_MODULE_0__[\"default\"])();\n  };\n\n  // Initialize dynamic block preview (editor).\n  if (window.acf) {\n    window.acf.addAction('render_block_preview', initializeBlock);\n  }\n});\n\n//# sourceURL=webpack://humanitas/./js/src/gutenberg-editor.js?");

/***/ }),

/***/ "./js/src/modules/block-offer.js":
/*!***************************************!*\
  !*** ./js/src/modules/block-offer.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\nvar blockOffer = function blockOffer() {\n  var allOfferCards = document.querySelectorAll('button.button-offer-card');\n  allOfferCards.forEach(function (offerCard) {\n    offerCard.addEventListener('click', function (e) {\n      var activeCard = document.querySelector('button.button-offer-card.active');\n      // remove active\n      activeCard === null || activeCard === void 0 ? void 0 : activeCard.classList.remove('active');\n      // add active to clicked card\n      e.currentTarget.classList.add('active');\n      var offerId = e.currentTarget.dataset.id;\n      var sidebarWithId = document.querySelector(\".block-offer-links__sidebar-single#\".concat(offerId));\n      var allSidebarSingle = document.querySelectorAll('.block-offer-links__sidebar-single');\n      allSidebarSingle.forEach(function (sidebarSingle) {\n        sidebarSingle.classList.remove('block-offer-links__sidebar-single--active');\n      });\n      sidebarWithId.classList.add('block-offer-links__sidebar-single--active');\n      if (window.innerWidth < 599) {\n        var sidebarWithIdTop = sidebarWithId.getBoundingClientRect().top;\n        var sidebarWithIdTopWithOffset = sidebarWithIdTop + window.scrollY - 100;\n        window.scrollTo({\n          top: sidebarWithIdTopWithOffset,\n          behavior: 'smooth'\n        });\n      }\n    });\n  });\n};\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (blockOffer);\n\n//# sourceURL=webpack://humanitas/./js/src/modules/block-offer.js?");

/***/ }),

/***/ "./js/src/modules/sliders.js":
/*!***********************************!*\
  !*** ./js/src/modules/sliders.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ \"./node_modules/@babel/runtime/helpers/esm/defineProperty.js\");\n\nfunction ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }\nfunction _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }\nvar sliders = function sliders() {\n  var allSliders = document.querySelectorAll('.block-hero-slider__slider');\n  var allCardsSliders = document.querySelectorAll('.card-slider');\n  var logosSliders = document.querySelectorAll('.block-logo-slider__logos');\n  var secondaryMenu = document.querySelector('.secondary-menu__list');\n  var controlButtons = document.querySelectorAll('.block-hero-slider__controls-button');\n  var sharedSettings = {\n    prevArrow: \"<button type=\\\"button\\\" class=\\\"slick-prev\\\"><span class=\\\"screen-reader-text\\\">Previous</span>\\n    <svg xmlns=\\\"http://www.w3.org/2000/svg\\\" width=\\\"32\\\" height=\\\"32\\\" viewBox=\\\"0 0 32 32\\\" fill=\\\"none\\\">\\n    <path d=\\\"M17.5606 16.0009L10.9609 9.40124L12.8466 7.51562L21.3318 16.0009L12.8466 24.4861L10.9609 22.6005L17.5606 16.0009Z\\\" fill=\\\"var(--color-slick-arrow)\\\"/>\\n  </svg>\\n    </button>\",\n    nextArrow: \"<button type=\\\"button\\\" class=\\\"slick-next\\\"><span class=\\\"screen-reader-text\\\">Next</span>\\n    <svg xmlns=\\\"http://www.w3.org/2000/svg\\\" width=\\\"32\\\" height=\\\"32\\\" viewBox=\\\"0 0 32 32\\\" fill=\\\"none\\\">\\n    <path d=\\\"M17.5606 16.0009L10.9609 9.40124L12.8466 7.51562L21.3318 16.0009L12.8466 24.4861L10.9609 22.6005L17.5606 16.0009Z\\\" fill=\\\"var(--color-slick-arrow)\\\"/>\\n  </svg>\\n    </button >\"\n  };\n\n  // slider settings\n  var settings = _objectSpread({\n    autoplay: true,\n    autoplaySpeed: 5000,\n    dots: true,\n    fade: false,\n    pauseOnHover: true,\n    pauseOnFocus: true,\n    pauseOnDotsHover: false,\n    speed: 450,\n    arrows: true,\n    infinite: false,\n    slidesToShow: 1\n  }, sharedSettings);\n  var settingsCards = _objectSpread(_objectSpread({\n    autoplay: false,\n    autoplaySpeed: 5000,\n    dots: false,\n    fade: false,\n    speed: 450,\n    arrows: true,\n    infinite: false,\n    slidesToShow: 3,\n    slidesToScroll: 1\n  }, sharedSettings), {}, {\n    responsive: [{\n      breakpoint: 1024,\n      settings: {\n        slidesToShow: 2\n      }\n    }, {\n      breakpoint: 671,\n      settings: {\n        slidesToShow: 1\n      }\n    }]\n  });\n  var settingsLogos = {\n    autoplay: true,\n    autoplaySpeed: 5000,\n    dots: false,\n    fade: false,\n    speed: 450,\n    arrows: false,\n    infinite: true,\n    slidesToScroll: 1,\n    // centerMode: true,\n    variableWidth: true,\n    slidesToShow: 1\n  };\n  var secondaryMenuSettings = _objectSpread({\n    autoplay: false,\n    dots: false,\n    fade: false,\n    speed: 450,\n    arrows: true,\n    infinite: false,\n    slidesToScroll: 1,\n    slidesToShow: 1,\n    centerMode: false,\n    variableWidth: true\n  }, sharedSettings);\n\n  // init slider\n  allSliders.forEach(function (slider) {\n    jQuery(slider).not('.slick-initialized').slick(settings);\n  });\n  allCardsSliders.forEach(function (slider) {\n    jQuery(slider).not('.slick-initialized').slick(settingsCards);\n  });\n  logosSliders.forEach(function (slider) {\n    jQuery(slider).not('.slick-initialized').slick(settingsLogos);\n  });\n  if (controlButtons.length > 0) {\n    // play pause autoplay on click\n    controlButtons.forEach(function (button) {\n      button.addEventListener('click', function (e) {\n        e.preventDefault();\n        var slider = button.closest('.block-hero-slider').querySelector('.block-hero-slider__slider');\n        if (slider) {\n          if (slider.classList.contains('slick-initialized')) {\n            if (button.classList.contains('autoplay')) {\n              jQuery('.block-hero-slider__slider').slick('slickSetOption', 'autoplay', false).slick('slickPause');\n              button.classList.remove('autoplay');\n            } else {\n              jQuery('.block-hero-slider__slider').slick('slickSetOption', 'autoplay', true).slick('slickPlay');\n              button.classList.add('autoplay');\n            }\n          }\n        }\n      });\n    });\n  }\n  if (secondaryMenu) {\n    jQuery(secondaryMenu).not('.slick-initialized').slick(secondaryMenuSettings);\n    var childrenWidth = 0;\n    jQuery('.secondary-menu__list .slick-track').children().each(function () {\n      childrenWidth += jQuery(this).width() + 56;\n    });\n    var outerContainerWidth = jQuery('.secondary-menu__list').width();\n    if (childrenWidth < outerContainerWidth) {\n      var nextArrow = jQuery('.secondary-menu__list .slick-next');\n      jQuery('.secondary-menu__list').addClass('with-scroll');\n      if (!nextArrow.hasClass('slick-disabled')) {\n        nextArrow.addClass('slick-disabled');\n        nextArrow.attr('aria-disabled', 'true');\n      }\n    }\n  }\n};\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (sliders);\n\n//# sourceURL=webpack://humanitas/./js/src/modules/sliders.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (/* binding */ _defineProperty)\n/* harmony export */ });\n/* harmony import */ var _toPropertyKey_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./toPropertyKey.js */ \"./node_modules/@babel/runtime/helpers/esm/toPropertyKey.js\");\n\nfunction _defineProperty(obj, key, value) {\n  key = (0,_toPropertyKey_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(key);\n  if (key in obj) {\n    Object.defineProperty(obj, key, {\n      value: value,\n      enumerable: true,\n      configurable: true,\n      writable: true\n    });\n  } else {\n    obj[key] = value;\n  }\n  return obj;\n}\n\n//# sourceURL=webpack://humanitas/./node_modules/@babel/runtime/helpers/esm/defineProperty.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/toPrimitive.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toPrimitive.js ***!
  \****************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (/* binding */ toPrimitive)\n/* harmony export */ });\n/* harmony import */ var _typeof_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./typeof.js */ \"./node_modules/@babel/runtime/helpers/esm/typeof.js\");\n\nfunction toPrimitive(t, r) {\n  if (\"object\" != (0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(t) || !t) return t;\n  var e = t[Symbol.toPrimitive];\n  if (void 0 !== e) {\n    var i = e.call(t, r || \"default\");\n    if (\"object\" != (0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(i)) return i;\n    throw new TypeError(\"@@toPrimitive must return a primitive value.\");\n  }\n  return (\"string\" === r ? String : Number)(t);\n}\n\n//# sourceURL=webpack://humanitas/./node_modules/@babel/runtime/helpers/esm/toPrimitive.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/toPropertyKey.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toPropertyKey.js ***!
  \******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (/* binding */ toPropertyKey)\n/* harmony export */ });\n/* harmony import */ var _typeof_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./typeof.js */ \"./node_modules/@babel/runtime/helpers/esm/typeof.js\");\n/* harmony import */ var _toPrimitive_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./toPrimitive.js */ \"./node_modules/@babel/runtime/helpers/esm/toPrimitive.js\");\n\n\nfunction toPropertyKey(t) {\n  var i = (0,_toPrimitive_js__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(t, \"string\");\n  return \"symbol\" == (0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"])(i) ? i : String(i);\n}\n\n//# sourceURL=webpack://humanitas/./node_modules/@babel/runtime/helpers/esm/toPropertyKey.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/typeof.js":
/*!***********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/typeof.js ***!
  \***********************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (/* binding */ _typeof)\n/* harmony export */ });\nfunction _typeof(o) {\n  \"@babel/helpers - typeof\";\n\n  return _typeof = \"function\" == typeof Symbol && \"symbol\" == typeof Symbol.iterator ? function (o) {\n    return typeof o;\n  } : function (o) {\n    return o && \"function\" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? \"symbol\" : typeof o;\n  }, _typeof(o);\n}\n\n//# sourceURL=webpack://humanitas/./node_modules/@babel/runtime/helpers/esm/typeof.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = __webpack_require__("./js/src/gutenberg-editor.js");
/******/ 	
/******/ })()
;