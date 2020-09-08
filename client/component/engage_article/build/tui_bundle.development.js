/******/ (function(modules) { // webpackBootstrap
/******/ 	// install a JSONP callback for chunk loading
/******/ 	function webpackJsonpCallback(data) {
/******/ 		var chunkIds = data[0];
/******/ 		var moreModules = data[1];
/******/ 		var executeModules = data[2];
/******/
/******/ 		// add "moreModules" to the modules object,
/******/ 		// then flag all "chunkIds" as loaded and fire callback
/******/ 		var moduleId, chunkId, i = 0, resolves = [];
/******/ 		for(;i < chunkIds.length; i++) {
/******/ 			chunkId = chunkIds[i];
/******/ 			if(Object.prototype.hasOwnProperty.call(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 				resolves.push(installedChunks[chunkId][0]);
/******/ 			}
/******/ 			installedChunks[chunkId] = 0;
/******/ 		}
/******/ 		for(moduleId in moreModules) {
/******/ 			if(Object.prototype.hasOwnProperty.call(moreModules, moduleId)) {
/******/ 				modules[moduleId] = moreModules[moduleId];
/******/ 			}
/******/ 		}
/******/ 		if(parentJsonpFunction) parentJsonpFunction(data);
/******/
/******/ 		while(resolves.length) {
/******/ 			resolves.shift()();
/******/ 		}
/******/
/******/ 		// add entry modules from loaded chunk to deferred list
/******/ 		deferredModules.push.apply(deferredModules, executeModules || []);
/******/
/******/ 		// run deferred modules when all chunks ready
/******/ 		return checkDeferredModules();
/******/ 	};
/******/ 	function checkDeferredModules() {
/******/ 		var result;
/******/ 		for(var i = 0; i < deferredModules.length; i++) {
/******/ 			var deferredModule = deferredModules[i];
/******/ 			var fulfilled = true;
/******/ 			for(var j = 1; j < deferredModule.length; j++) {
/******/ 				var depId = deferredModule[j];
/******/ 				if(installedChunks[depId] !== 0) fulfilled = false;
/******/ 			}
/******/ 			if(fulfilled) {
/******/ 				deferredModules.splice(i--, 1);
/******/ 				result = __webpack_require__(__webpack_require__.s = deferredModule[0]);
/******/ 			}
/******/ 		}
/******/
/******/ 		return result;
/******/ 	}
/******/
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// object to store loaded and loading chunks
/******/ 	// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 	// Promise = chunk loading, 0 = chunk loaded
/******/ 	var installedChunks = {
/******/ 		"engage_article.development": 0
/******/ 	};
/******/
/******/ 	var deferredModules = [];
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
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
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
/******/ 	var jsonpArray = window["webpackJsonp"] = window["webpackJsonp"] || [];
/******/ 	var oldJsonpFunction = jsonpArray.push.bind(jsonpArray);
/******/ 	jsonpArray.push = webpackJsonpCallback;
/******/ 	jsonpArray = jsonpArray.slice();
/******/ 	for(var i = 0; i < jsonpArray.length; i++) webpackJsonpCallback(jsonpArray[i]);
/******/ 	var parentJsonpFunction = oldJsonpFunction;
/******/
/******/
/******/ 	// add entry module to deferred list
/******/ 	deferredModules.push(["./client/component/engage_article/src/tui.json","tui/build/vendors.development"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/component/engage_article/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!*******************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \*******************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./CreateArticle\": \"./client/component/engage_article/src/components/CreateArticle.vue\",\n\t\"./CreateArticle.vue\": \"./client/component/engage_article/src/components/CreateArticle.vue\",\n\t\"./card/ArticleCard\": \"./client/component/engage_article/src/components/card/ArticleCard.vue\",\n\t\"./card/ArticleCard.vue\": \"./client/component/engage_article/src/components/card/ArticleCard.vue\",\n\t\"./card/RelatedCard\": \"./client/component/engage_article/src/components/card/RelatedCard.vue\",\n\t\"./card/RelatedCard.vue\": \"./client/component/engage_article/src/components/card/RelatedCard.vue\",\n\t\"./content/ArticleContent\": \"./client/component/engage_article/src/components/content/ArticleContent.vue\",\n\t\"./content/ArticleContent.vue\": \"./client/component/engage_article/src/components/content/ArticleContent.vue\",\n\t\"./content/ArticleTitle\": \"./client/component/engage_article/src/components/content/ArticleTitle.vue\",\n\t\"./content/ArticleTitle.vue\": \"./client/component/engage_article/src/components/content/ArticleTitle.vue\",\n\t\"./form/ArticleForm\": \"./client/component/engage_article/src/components/form/ArticleForm.vue\",\n\t\"./form/ArticleForm.vue\": \"./client/component/engage_article/src/components/form/ArticleForm.vue\",\n\t\"./form/EditArticleContentForm\": \"./client/component/engage_article/src/components/form/EditArticleContentForm.vue\",\n\t\"./form/EditArticleContentForm.vue\": \"./client/component/engage_article/src/components/form/EditArticleContentForm.vue\",\n\t\"./form/EditArticleTitleForm\": \"./client/component/engage_article/src/components/form/EditArticleTitleForm.vue\",\n\t\"./form/EditArticleTitleForm.vue\": \"./client/component/engage_article/src/components/form/EditArticleTitleForm.vue\",\n\t\"./separator/ArticleSeparator\": \"./client/component/engage_article/src/components/separator/ArticleSeparator.vue\",\n\t\"./separator/ArticleSeparator.vue\": \"./client/component/engage_article/src/components/separator/ArticleSeparator.vue\",\n\t\"./sidepanel/ArticleSidePanel\": \"./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue\",\n\t\"./sidepanel/ArticleSidePanel.vue\": \"./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue\",\n\t\"./sidepanel/Related\": \"./client/component/engage_article/src/components/sidepanel/Related.vue\",\n\t\"./sidepanel/Related.vue\": \"./client/component/engage_article/src/components/sidepanel/Related.vue\",\n\t\"./sidepanel/content/ArticlePlaylistBox\": \"./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue\",\n\t\"./sidepanel/content/ArticlePlaylistBox.vue\": \"./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/engage_article/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/engage_article/src/components_sync_^(?:(?");

/***/ }),

/***/ "./client/component/engage_article/src/components/CreateArticle.vue":
/*!**************************************************************************!*\
  !*** ./client/component/engage_article/src/components/CreateArticle.vue ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _CreateArticle_vue_vue_type_template_id_20bcfe1b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./CreateArticle.vue?vue&type=template&id=20bcfe1b& */ \"./client/component/engage_article/src/components/CreateArticle.vue?vue&type=template&id=20bcfe1b&\");\n/* harmony import */ var _CreateArticle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CreateArticle.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/CreateArticle.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _CreateArticle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./CreateArticle.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/CreateArticle.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _CreateArticle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _CreateArticle_vue_vue_type_template_id_20bcfe1b___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _CreateArticle_vue_vue_type_template_id_20bcfe1b___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/CreateArticle.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/CreateArticle.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/CreateArticle.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CreateArticle.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/CreateArticle.vue?vue&type=style&index=0&lang=scss&":
/*!************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/CreateArticle.vue?vue&type=style&index=0&lang=scss& ***!
  \************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../tooling/webpack/css_raw_loader.js??ref--3-1!../../../../../node_modules/postcss-loader/src??ref--3-2!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CreateArticle.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/CreateArticle.vue?vue&type=template&id=20bcfe1b&":
/*!*********************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/CreateArticle.vue?vue&type=template&id=20bcfe1b& ***!
  \*********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_template_id_20bcfe1b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CreateArticle.vue?vue&type=template&id=20bcfe1b& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=template&id=20bcfe1b&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_template_id_20bcfe1b___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_template_id_20bcfe1b___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/ArticleCard.vue":
/*!*****************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/ArticleCard.vue ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleCard_vue_vue_type_template_id_18b826b6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleCard.vue?vue&type=template&id=18b826b6& */ \"./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=template&id=18b826b6&\");\n/* harmony import */ var _ArticleCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleCard.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticleCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticleCard.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _ArticleCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticleCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticleCard_vue_vue_type_template_id_18b826b6___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleCard_vue_vue_type_template_id_18b826b6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _ArticleCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_ArticleCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/card/ArticleCard.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleCard.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=style&index=0&lang=scss&":
/*!***************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=style&index=0&lang=scss& ***!
  \***************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../tooling/webpack/css_raw_loader.js??ref--3-1!../../../../../../node_modules/postcss-loader/src??ref--3-2!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleCard.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=template&id=18b826b6&":
/*!************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=template&id=18b826b6& ***!
  \************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_template_id_18b826b6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleCard.vue?vue&type=template&id=18b826b6& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=template&id=18b826b6&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_template_id_18b826b6___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_template_id_18b826b6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/RelatedCard.vue":
/*!*****************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/RelatedCard.vue ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _RelatedCard_vue_vue_type_template_id_f16a1e2a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RelatedCard.vue?vue&type=template&id=f16a1e2a& */ \"./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=template&id=f16a1e2a&\");\n/* harmony import */ var _RelatedCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RelatedCard.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _RelatedCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./RelatedCard.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _RelatedCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _RelatedCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _RelatedCard_vue_vue_type_template_id_f16a1e2a___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _RelatedCard_vue_vue_type_template_id_f16a1e2a___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _RelatedCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_RelatedCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/card/RelatedCard.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./RelatedCard.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=style&index=0&lang=scss&":
/*!***************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=style&index=0&lang=scss& ***!
  \***************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../tooling/webpack/css_raw_loader.js??ref--3-1!../../../../../../node_modules/postcss-loader/src??ref--3-2!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./RelatedCard.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=template&id=f16a1e2a&":
/*!************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=template&id=f16a1e2a& ***!
  \************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_template_id_f16a1e2a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./RelatedCard.vue?vue&type=template&id=f16a1e2a& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=template&id=f16a1e2a&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_template_id_f16a1e2a___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_template_id_f16a1e2a___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleContent.vue":
/*!***********************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleContent.vue ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleContent_vue_vue_type_template_id_7827ff08___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleContent.vue?vue&type=template&id=7827ff08& */ \"./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=template&id=7827ff08&\");\n/* harmony import */ var _ArticleContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleContent.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticleContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticleContent.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticleContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticleContent_vue_vue_type_template_id_7827ff08___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleContent_vue_vue_type_template_id_7827ff08___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/content/ArticleContent.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleContent.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=style&index=0&lang=scss&":
/*!*********************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=style&index=0&lang=scss& ***!
  \*********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../tooling/webpack/css_raw_loader.js??ref--3-1!../../../../../../node_modules/postcss-loader/src??ref--3-2!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleContent.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=template&id=7827ff08&":
/*!******************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=template&id=7827ff08& ***!
  \******************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_template_id_7827ff08___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleContent.vue?vue&type=template&id=7827ff08& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=template&id=7827ff08&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_template_id_7827ff08___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_template_id_7827ff08___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleTitle.vue":
/*!*********************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleTitle.vue ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleTitle_vue_vue_type_template_id_be80d1b2___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleTitle.vue?vue&type=template&id=be80d1b2& */ \"./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=template&id=be80d1b2&\");\n/* harmony import */ var _ArticleTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleTitle.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticleTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticleTitle.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticleTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticleTitle_vue_vue_type_template_id_be80d1b2___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleTitle_vue_vue_type_template_id_be80d1b2___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/content/ArticleTitle.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleTitle.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../tooling/webpack/css_raw_loader.js??ref--3-1!../../../../../../node_modules/postcss-loader/src??ref--3-2!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleTitle.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=template&id=be80d1b2&":
/*!****************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=template&id=be80d1b2& ***!
  \****************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_template_id_be80d1b2___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleTitle.vue?vue&type=template&id=be80d1b2& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=template&id=be80d1b2&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_template_id_be80d1b2___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_template_id_be80d1b2___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/ArticleForm.vue":
/*!*****************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/ArticleForm.vue ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleForm_vue_vue_type_template_id_01a1681e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleForm.vue?vue&type=template&id=01a1681e& */ \"./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=template&id=01a1681e&\");\n/* harmony import */ var _ArticleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleForm.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticleForm.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _ArticleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticleForm_vue_vue_type_template_id_01a1681e___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleForm_vue_vue_type_template_id_01a1681e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _ArticleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_ArticleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/form/ArticleForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleForm.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=style&index=0&lang=scss&":
/*!***************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=style&index=0&lang=scss& ***!
  \***************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../tooling/webpack/css_raw_loader.js??ref--3-1!../../../../../../node_modules/postcss-loader/src??ref--3-2!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleForm.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=template&id=01a1681e&":
/*!************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=template&id=01a1681e& ***!
  \************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_template_id_01a1681e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleForm.vue?vue&type=template&id=01a1681e& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=template&id=01a1681e&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_template_id_01a1681e___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_template_id_01a1681e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleContentForm.vue":
/*!****************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleContentForm.vue ***!
  \****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _EditArticleContentForm_vue_vue_type_template_id_93461ec6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./EditArticleContentForm.vue?vue&type=template&id=93461ec6& */ \"./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=template&id=93461ec6&\");\n/* harmony import */ var _EditArticleContentForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./EditArticleContentForm.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _EditArticleContentForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _EditArticleContentForm_vue_vue_type_template_id_93461ec6___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _EditArticleContentForm_vue_vue_type_template_id_93461ec6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/form/EditArticleContentForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleContentForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleContentForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./EditArticleContentForm.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleContentForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleContentForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=template&id=93461ec6&":
/*!***********************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=template&id=93461ec6& ***!
  \***********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleContentForm_vue_vue_type_template_id_93461ec6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./EditArticleContentForm.vue?vue&type=template&id=93461ec6& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=template&id=93461ec6&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleContentForm_vue_vue_type_template_id_93461ec6___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleContentForm_vue_vue_type_template_id_93461ec6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleContentForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleTitleForm.vue":
/*!**************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleTitleForm.vue ***!
  \**************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _EditArticleTitleForm_vue_vue_type_template_id_11c34d08___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./EditArticleTitleForm.vue?vue&type=template&id=11c34d08& */ \"./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=template&id=11c34d08&\");\n/* harmony import */ var _EditArticleTitleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./EditArticleTitleForm.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _EditArticleTitleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./EditArticleTitleForm.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _EditArticleTitleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _EditArticleTitleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _EditArticleTitleForm_vue_vue_type_template_id_11c34d08___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _EditArticleTitleForm_vue_vue_type_template_id_11c34d08___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _EditArticleTitleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_EditArticleTitleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/form/EditArticleTitleForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./EditArticleTitleForm.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=style&index=0&lang=scss&":
/*!************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=style&index=0&lang=scss& ***!
  \************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../tooling/webpack/css_raw_loader.js??ref--3-1!../../../../../../node_modules/postcss-loader/src??ref--3-2!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./EditArticleTitleForm.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=template&id=11c34d08&":
/*!*********************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=template&id=11c34d08& ***!
  \*********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_template_id_11c34d08___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./EditArticleTitleForm.vue?vue&type=template&id=11c34d08& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=template&id=11c34d08&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_template_id_11c34d08___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_template_id_11c34d08___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/separator/ArticleSeparator.vue":
/*!***************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/separator/ArticleSeparator.vue ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleSeparator_vue_vue_type_template_id_01d50df0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleSeparator.vue?vue&type=template&id=01d50df0& */ \"./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=template&id=01d50df0&\");\n/* harmony import */ var _ArticleSeparator_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleSeparator.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\nvar script = {}\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  script,\n  _ArticleSeparator_vue_vue_type_template_id_01d50df0___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleSeparator_vue_vue_type_template_id_01d50df0___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":false,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/separator/ArticleSeparator.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/separator/ArticleSeparator.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=style&index=0&lang=scss&":
/*!*************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=style&index=0&lang=scss& ***!
  \*************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSeparator_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../tooling/webpack/css_raw_loader.js??ref--3-1!../../../../../../node_modules/postcss-loader/src??ref--3-2!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleSeparator.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSeparator_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSeparator_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSeparator_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSeparator_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSeparator_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/separator/ArticleSeparator.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=template&id=01d50df0&":
/*!**********************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=template&id=01d50df0& ***!
  \**********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSeparator_vue_vue_type_template_id_01d50df0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleSeparator.vue?vue&type=template&id=01d50df0& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=template&id=01d50df0&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSeparator_vue_vue_type_template_id_01d50df0___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSeparator_vue_vue_type_template_id_01d50df0___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/separator/ArticleSeparator.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue":
/*!***************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleSidePanel_vue_vue_type_template_id_3c516de8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleSidePanel.vue?vue&type=template&id=3c516de8& */ \"./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=template&id=3c516de8&\");\n/* harmony import */ var _ArticleSidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleSidePanel.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticleSidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticleSidePanel.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _ArticleSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticleSidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticleSidePanel_vue_vue_type_template_id_3c516de8___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleSidePanel_vue_vue_type_template_id_3c516de8___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _ArticleSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_ArticleSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleSidePanel.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=style&index=0&lang=scss&":
/*!*************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=style&index=0&lang=scss& ***!
  \*************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../tooling/webpack/css_raw_loader.js??ref--3-1!../../../../../../node_modules/postcss-loader/src??ref--3-2!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleSidePanel.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=template&id=3c516de8&":
/*!**********************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=template&id=3c516de8& ***!
  \**********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_template_id_3c516de8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleSidePanel.vue?vue&type=template&id=3c516de8& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=template&id=3c516de8&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_template_id_3c516de8___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_template_id_3c516de8___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/Related.vue":
/*!******************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/Related.vue ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Related_vue_vue_type_template_id_44b6de2c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Related.vue?vue&type=template&id=44b6de2c& */ \"./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=template&id=44b6de2c&\");\n/* harmony import */ var _Related_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Related.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _Related_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Related.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _Related_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _Related_vue_vue_type_template_id_44b6de2c___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _Related_vue_vue_type_template_id_44b6de2c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/sidepanel/Related.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Related.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=style&index=0&lang=scss&":
/*!****************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=style&index=0&lang=scss& ***!
  \****************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../tooling/webpack/css_raw_loader.js??ref--3-1!../../../../../../node_modules/postcss-loader/src??ref--3-2!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Related.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=template&id=44b6de2c&":
/*!*************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=template&id=44b6de2c& ***!
  \*************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_template_id_44b6de2c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Related.vue?vue&type=template&id=44b6de2c& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=template&id=44b6de2c&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_template_id_44b6de2c___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_template_id_44b6de2c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue":
/*!*************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue ***!
  \*************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticlePlaylistBox_vue_vue_type_template_id_2c100fac___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticlePlaylistBox.vue?vue&type=template&id=2c100fac& */ \"./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=template&id=2c100fac&\");\n/* harmony import */ var _ArticlePlaylistBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticlePlaylistBox.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticlePlaylistBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticlePlaylistBox.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _ArticlePlaylistBox_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticlePlaylistBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticlePlaylistBox_vue_vue_type_template_id_2c100fac___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticlePlaylistBox_vue_vue_type_template_id_2c100fac___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _ArticlePlaylistBox_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_ArticlePlaylistBox_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!************************************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticlePlaylistBox.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=style&index=0&lang=scss&":
/*!***********************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=style&index=0&lang=scss& ***!
  \***********************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../../../tooling/webpack/css_raw_loader.js??ref--3-1!../../../../../../../node_modules/postcss-loader/src??ref--3-2!../../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticlePlaylistBox.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=template&id=2c100fac&":
/*!********************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=template&id=2c100fac& ***!
  \********************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_template_id_2c100fac___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticlePlaylistBox.vue?vue&type=template&id=2c100fac& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=template&id=2c100fac&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_template_id_2c100fac___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_template_id_2c100fac___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!**************************************************************************************************!*\
  !*** ./client/component/engage_article/src/pages sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \**************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./ArticleView\": \"./client/component/engage_article/src/pages/ArticleView.vue\",\n\t\"./ArticleView.vue\": \"./client/component/engage_article/src/pages/ArticleView.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/engage_article/src/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/engage_article/src/pages_sync_^(?:(?");

/***/ }),

/***/ "./client/component/engage_article/src/pages/ArticleView.vue":
/*!*******************************************************************!*\
  !*** ./client/component/engage_article/src/pages/ArticleView.vue ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleView_vue_vue_type_template_id_34cec6b4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleView.vue?vue&type=template&id=34cec6b4& */ \"./client/component/engage_article/src/pages/ArticleView.vue?vue&type=template&id=34cec6b4&\");\n/* harmony import */ var _ArticleView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleView.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/pages/ArticleView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticleView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticleView.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/pages/ArticleView.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _ArticleView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/pages/ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticleView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticleView_vue_vue_type_template_id_34cec6b4___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleView_vue_vue_type_template_id_34cec6b4___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _ArticleView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_ArticleView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/pages/ArticleView.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/pages/ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!******************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/pages/ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \******************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/pages/ArticleView.vue?vue&type=script&lang=js&":
/*!********************************************************************************************!*\
  !*** ./client/component/engage_article/src/pages/ArticleView.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleView.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/pages/ArticleView.vue?vue&type=style&index=0&lang=scss&":
/*!*****************************************************************************************************!*\
  !*** ./client/component/engage_article/src/pages/ArticleView.vue?vue&type=style&index=0&lang=scss& ***!
  \*****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../tooling/webpack/css_raw_loader.js??ref--3-1!../../../../../node_modules/postcss-loader/src??ref--3-2!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleView.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_tooling_webpack_css_raw_loader_js_ref_3_1_node_modules_postcss_loader_src_index_js_ref_3_2_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/pages/ArticleView.vue?vue&type=template&id=34cec6b4&":
/*!**************************************************************************************************!*\
  !*** ./client/component/engage_article/src/pages/ArticleView.vue?vue&type=template&id=34cec6b4& ***!
  \**************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_template_id_34cec6b4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleView.vue?vue&type=template&id=34cec6b4& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=template&id=34cec6b4&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_template_id_34cec6b4___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_template_id_34cec6b4___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/tui.json":
/*!******************************************************!*\
  !*** ./client/component/engage_article/src/tui.json ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"engage_article\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"engage_article\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"engage_article\")\ntui._bundle.addModulesFromContext(\"engage_article/components\", __webpack_require__(\"./client/component/engage_article/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\ntui._bundle.addModulesFromContext(\"engage_article/pages\", __webpack_require__(\"./client/component/engage_article/src/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/component/engage_article/src/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*****************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_article\": [\n    \"addtoplaylist\",\n    \"numberwithinplaylist\",\n    \"timelessthanfive\",\n    \"timefivetoten\",\n    \"timemorethanten\"\n  ],\n  \"totara_engage\": [\n    \"more\",\n    \"share\",\n    \"numberofcomments\",\n    \"numberoflikes\",\n    \"numberofshares\",\n    \"time\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*****************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_engage\": [\n    \"time\",\n    \"like\"\n  ],\n  \"engage_article\": [\n    \"timelessthanfive\",\n    \"timefivetoten\",\n    \"timemorethanten\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*****************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"moodle\": [\n    \"next\",\n    \"info\"\n  ],\n  \"totara_core\": [\n    \"save\"\n  ],\n  \"engage_article\": [\n    \"entertitle\",\n    \"entercontent\",\n    \"articletitle\",\n    \"content\",\n    \"createarticleshort\"\n  ],\n  \"totara_engage\": [\n    \"contributetip\",\n    \"contributetip_help\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_article\": [\n    \"articletitle\",\n    \"entertitle\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_article\": [\n    \"deletewarningmsg\",\n    \"deletewarningtitle\",\n    \"timelessthanfive\",\n    \"timefivetoten\",\n    \"timemorethanten\",\n    \"likearticle\",\n    \"removelikearticle\",\n    \"reportresource\",\n    \"error:reportresource\"\n  ],\n  \"moodle\": [\n    \"delete\"\n  ],\n  \"totara_reportedcontent\": [\n    \"reported\",\n    \"reported_failed\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_article\": [\n    \"appears_in\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*******************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*******************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_article\": [\n    \"entercontent\",\n    \"entertitle\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_article_components_form_ArticleForm__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_article/components/form/ArticleForm */ \"engage_article/components/form/ArticleForm\");\n/* harmony import */ var engage_article_components_form_ArticleForm__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_form_ArticleForm__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/components/form/AccessForm */ \"totara_engage/components/form/AccessForm\");\n/* harmony import */ var totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_article_graphql_create_article__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_article/graphql/create_article */ \"./server/totara/engage/resources/article/webapi/ajax/create_article.graphql\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/mixins/container_mixin */ \"totara_engage/mixins/container_mixin\");\n/* harmony import */ var totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n// Graphql queries\n\n\n\n// Mixins\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ArticleForm: (engage_article_components_form_ArticleForm__WEBPACK_IMPORTED_MODULE_0___default()),\n    AccessForm: (totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1___default()),\n  },\n\n  mixins: [totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4___default.a],\n\n  data() {\n    return {\n      stage: 0,\n      maxStage: 1,\n      article: {\n        name: '',\n        content: null,\n        itemId: null,\n      },\n      submitting: false,\n    };\n  },\n\n  computed: {\n    privateDisabled() {\n      return this.containerValues.access\n        ? !totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"AccessManager\"].isPrivate(this.containerValues.access)\n        : false;\n    },\n    restrictedDisabled() {\n      return this.containerValues.access\n        ? totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"AccessManager\"].isPublic(this.containerValues.access)\n        : false;\n    },\n  },\n\n  methods: {\n    /**\n     * @param {String} content\n     * @param {String} name\n     * @param {String|Number} itemId\n     */\n    next({ content, name, itemId }) {\n      if (this.stage < this.maxStage) {\n        this.stage += 1;\n      }\n\n      this.article.content = content;\n      this.article.name = name;\n      this.article.itemId = itemId;\n\n      this.$emit('change-title', this.stage);\n    },\n    back() {\n      if (this.stage > 0) {\n        this.stage -= 1;\n      }\n\n      this.$emit('change-title', this.stage);\n    },\n\n    /**\n     * @param {String} access\n     * @param {Array} topics\n     * @param {Array} shares\n     * @param {String|null} timeView\n     */\n    done({ access, topics, timeView, shares }) {\n      this.submitting = true;\n      let params = {\n        content: this.article.content,\n        name: this.article.name,\n        access: access,\n        topics: topics.map(topic => topic.id),\n        shares: shares,\n        draft_id: this.article.itemId,\n      };\n\n      if (timeView) {\n        params.timeview = timeView;\n      }\n\n      this.$apollo\n        .mutate({\n          mutation: engage_article_graphql_create_article__WEBPACK_IMPORTED_MODULE_2__[\"default\"],\n          refetchQueries: [\n            'totara_engage_contribution_cards',\n            'container_workspace_contribution_cards',\n            'container_workspace_shared_cards',\n          ],\n          variables: params,\n          update: (\n            cache,\n            {\n              data: {\n                article: {\n                  resource: { id },\n                },\n              },\n            }\n          ) => {\n            this.$emit('done', { resourceId: id });\n          },\n        })\n        .then(() => {\n          this.$emit('cancel');\n        })\n        .finally(() => {\n          this.submitting = false;\n        });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_engage_components_card_BaseCard__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_engage/components/card/BaseCard */ \"totara_engage/components/card/BaseCard\");\n/* harmony import */ var totara_engage_components_card_BaseCard__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_card_BaseCard__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_components_card_ImageHeader__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/components/card/ImageHeader */ \"totara_engage/components/card/ImageHeader\");\n/* harmony import */ var totara_engage_components_card_ImageHeader__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_card_ImageHeader__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var totara_engage_components_icons_StatIcon__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! totara_engage/components/icons/StatIcon */ \"totara_engage/components/icons/StatIcon\");\n/* harmony import */ var totara_engage_components_icons_StatIcon__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_icons_StatIcon__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var totara_engage_components_card_CardHeader__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/components/card/CardHeader */ \"totara_engage/components/card/CardHeader\");\n/* harmony import */ var totara_engage_components_card_CardHeader__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_card_CardHeader__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/buttons/ButtonIcon */ \"tui/components/buttons/ButtonIcon\");\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_components_icons_Share__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/components/icons/Share */ \"tui/components/icons/Share\");\n/* harmony import */ var tui_components_icons_Share__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Share__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_components_icons_AddToList__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/components/icons/AddToList */ \"tui/components/icons/AddToList\");\n/* harmony import */ var tui_components_icons_AddToList__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_AddToList__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! tui/components/icons/Like */ \"tui/components/icons/Like\");\n/* harmony import */ var tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var tui_components_icons_Comment__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! tui/components/icons/Comment */ \"tui/components/icons/Comment\");\n/* harmony import */ var tui_components_icons_Comment__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Comment__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var tui_components_icons_More__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! tui/components/icons/More */ \"tui/components/icons/More\");\n/* harmony import */ var tui_components_icons_More__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_More__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_10__);\n/* harmony import */ var totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! totara_engage/components/icons/access/computed/AccessIcon */ \"totara_engage/components/icons/access/computed/AccessIcon\");\n/* harmony import */ var totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_11__);\n/* harmony import */ var tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! tui/components/icons/Time */ \"tui/components/icons/Time\");\n/* harmony import */ var tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_12__);\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! totara_engage/components/buttons/BookmarkButton */ \"totara_engage/components/buttons/BookmarkButton\");\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_13__);\n/* harmony import */ var totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! totara_engage/graphql/update_bookmark */ \"./server/totara/engage/webapi/ajax/update_bookmark.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n// GraphQL\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ButtonIcon: (tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_4___default()),\n    BaseCard: (totara_engage_components_card_BaseCard__WEBPACK_IMPORTED_MODULE_0___default()),\n    ImageHeader: (totara_engage_components_card_ImageHeader__WEBPACK_IMPORTED_MODULE_1___default()),\n    StatIcon: (totara_engage_components_icons_StatIcon__WEBPACK_IMPORTED_MODULE_2___default()),\n    CardHeader: (totara_engage_components_card_CardHeader__WEBPACK_IMPORTED_MODULE_3___default()),\n    ShareIcon: (tui_components_icons_Share__WEBPACK_IMPORTED_MODULE_5___default()),\n    AddToListIcon: (tui_components_icons_AddToList__WEBPACK_IMPORTED_MODULE_6___default()),\n    MoreIcon: (tui_components_icons_More__WEBPACK_IMPORTED_MODULE_9___default()),\n    AccessIcon: (totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_11___default()),\n    TimeIcon: (tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_12___default()),\n    BookmarkButton: (totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_13___default()),\n  },\n\n  mixins: [totara_engage_index__WEBPACK_IMPORTED_MODULE_10__[\"cardMixin\"]],\n\n  data() {\n    return {\n      // Assign the value to the inner child, as we do not want to mutate the prop.\n      innerBookmarked: this.bookmarked,\n      hovered: false,\n      statIcons: [\n        {\n          type: 'reaction',\n          title: this.$str(\n            'numberoflikes',\n            'totara_engage',\n            this.totalReactions\n          ),\n          icon: tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_7___default.a,\n          statNumber: this.totalReactions,\n        },\n        {\n          type: 'comment',\n          title: this.$str(\n            'numberofcomments',\n            'totara_engage',\n            this.totalComments\n          ),\n          icon: tui_components_icons_Comment__WEBPACK_IMPORTED_MODULE_8___default.a,\n          statNumber: this.totalComments,\n        },\n      ],\n      actions: [\n        {\n          alt: this.$str('addtoplaylist', 'engage_article'),\n          component: 'AddToListIcon',\n        },\n        {\n          alt: this.$str('share', 'totara_engage'),\n          component: 'ShareIcon',\n        },\n        {\n          alt: this.$str('more', 'totara_engage'),\n          component: 'MoreIcon',\n        },\n      ],\n      extraData: JSON.parse(this.extra),\n    };\n  },\n\n  computed: {\n    getTimeView() {\n      if (totara_engage_index__WEBPACK_IMPORTED_MODULE_10__[\"TimeViewType\"].isLessThanFive(this.extraData.timeview)) {\n        return this.$str('timelessthanfive', 'engage_article');\n      } else if (totara_engage_index__WEBPACK_IMPORTED_MODULE_10__[\"TimeViewType\"].isFiveToTen(this.extraData.timeview)) {\n        return this.$str('timefivetoten', 'engage_article');\n      } else if (totara_engage_index__WEBPACK_IMPORTED_MODULE_10__[\"TimeViewType\"].isMoreThanTen(this.extraData.timeview)) {\n        return this.$str('timemorethanten', 'engage_article');\n      }\n      return null;\n    },\n  },\n\n  created() {\n    // Add more stat icons depending on the visibility status of the card\n    if (totara_engage_index__WEBPACK_IMPORTED_MODULE_10__[\"AccessManager\"].isPublic(this.access)) {\n      this.statIcons = this.statIcons.concat([\n        {\n          type: 'share',\n          title: this.$str(\n            'numberofshares',\n            'totara_engage',\n            this.sharedbycount\n          ),\n          icon: tui_components_icons_Share__WEBPACK_IMPORTED_MODULE_5___default.a,\n          statNumber: this.sharedbycount,\n        },\n        {\n          type: 'playlistUsage',\n          title: this.$str(\n            'numberwithinplaylist',\n            'engage_article',\n            this.extraData.usage\n          ),\n          icon: tui_components_icons_AddToList__WEBPACK_IMPORTED_MODULE_6___default.a,\n          statNumber: this.extraData.usage,\n        },\n      ]);\n    }\n  },\n\n  methods: {\n    $_handleHovered(hovered) {\n      this.hovered = hovered;\n    },\n\n    updateBookmark() {\n      this.innerBookmarked = !this.innerBookmarked;\n      this.$apollo.mutate({\n        mutation: totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_14__[\"default\"],\n        refetchAll: false,\n        refetchQueries: ['totara_engage_contribution_cards'],\n        variables: {\n          itemid: this.instanceId,\n          component: 'engage_article',\n          bookmarked: this.innerBookmarked,\n        },\n      });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/card/Card */ \"tui/components/card/Card\");\n/* harmony import */ var tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/icons/Time */ \"tui/components/icons/Time\");\n/* harmony import */ var tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/icons/Like */ \"tui/components/icons/Like\");\n/* harmony import */ var tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/components/buttons/BookmarkButton */ \"totara_engage/components/buttons/BookmarkButton\");\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    BookmarkButton: (totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4___default()),\n    Card: (tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0___default()),\n    TimeIcon: (tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_1___default()),\n    Like: (tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_2___default()),\n  },\n\n  props: {\n    resourceId: {\n      type: [Number, String],\n      required: true,\n    },\n    name: {\n      type: String,\n      required: true,\n    },\n    bookmarked: {\n      type: Boolean,\n      default: false,\n    },\n    image: {\n      type: String,\n      required: true,\n    },\n    reactions: {\n      type: [Number, String],\n      required: true,\n    },\n    timeview: {\n      type: String,\n      default: '',\n    },\n    url: {\n      type: String,\n      required: true,\n    },\n  },\n\n  data() {\n    return {\n      innerBookmarked: this.bookmarked,\n    };\n  },\n\n  computed: {\n    timeviewString() {\n      if (totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"TimeViewType\"].isLessThanFive(this.timeview)) {\n        return this.$str('timelessthanfive', 'engage_article');\n      }\n\n      if (totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"TimeViewType\"].isFiveToTen(this.timeview)) {\n        return this.$str('timefivetoten', 'engage_article');\n      }\n\n      if (totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"TimeViewType\"].isMoreThanTen(this.timeview)) {\n        return this.$str('timemorethanten', 'engage_article');\n      }\n\n      return '';\n    },\n  },\n\n  methods: {\n    handleClickBookmark() {\n      this.innerBookmarked = !this.innerBookmarked;\n      this.$emit('update', this.resourceId, this.innerBookmarked);\n    },\n    handleClickCard() {\n      window.location.href = this.url;\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_engage/components/form/InlineEditing */ \"totara_engage/components/form/InlineEditing\");\n/* harmony import */ var totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var engage_article_components_form_EditArticleContentForm__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! engage_article/components/form/EditArticleContentForm */ \"engage_article/components/form/EditArticleContentForm\");\n/* harmony import */ var engage_article_components_form_EditArticleContentForm__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_form_EditArticleContentForm__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_tui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/tui */ \"tui/tui\");\n/* harmony import */ var tui_tui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_tui__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var engage_article_graphql_update_article__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! engage_article/graphql/update_article */ \"./server/totara/engage/resources/article/webapi/ajax/update_article.graphql\");\n/* harmony import */ var engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! engage_article/graphql/get_article */ \"./server/totara/engage/resources/article/webapi/ajax/get_article.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n// GraphQL queries\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    InlineEditing: (totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0___default()),\n    EditArticleForm: (engage_article_components_form_EditArticleContentForm__WEBPACK_IMPORTED_MODULE_1___default()),\n  },\n\n  props: {\n    /**\n     * For fetching the draft content of article.\n     */\n    resourceId: {\n      type: [String, Number],\n      required: true,\n    },\n\n    content: {\n      type: String,\n      required: true,\n    },\n\n    updateAble: {\n      type: Boolean,\n      required: true,\n    },\n  },\n\n  data() {\n    return {\n      editing: false,\n      submitting: false,\n    };\n  },\n\n  mounted() {\n    this.$_scan();\n  },\n\n  updated() {\n    this.$_scan();\n  },\n\n  methods: {\n    $_scan() {\n      this.$nextTick().then(() => {\n        let content = this.$refs.content;\n        if (!content) {\n          return;\n        }\n\n        tui_tui__WEBPACK_IMPORTED_MODULE_2___default.a.scan(content);\n      });\n    },\n\n    /**\n     *\n     * @param {String} content\n     * @param {Number} format\n     */\n    updateArticle({ content, format, itemId }) {\n      this.submitting = true;\n\n      this.$apollo\n        .mutate({\n          mutation: engage_article_graphql_update_article__WEBPACK_IMPORTED_MODULE_3__[\"default\"],\n          variables: {\n            resourceid: this.resourceId,\n            content: content,\n            format: format,\n            draft_id: itemId,\n          },\n\n          /**\n           *\n           * @param {DataProxy} proxy\n           * @param {Object} data\n           */\n          updateQuery: (proxy, data) => {\n            proxy.writeQuery({\n              query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_4__[\"default\"],\n              variables: {\n                resourceid: this.resourceId,\n              },\n\n              data: data,\n            });\n          },\n        })\n        .finally(() => {\n          this.editing = false;\n          this.submitting = false;\n        });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_engage/components/form/InlineEditing */ \"totara_engage/components/form/InlineEditing\");\n/* harmony import */ var totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/components/buttons/BookmarkButton */ \"totara_engage/components/buttons/BookmarkButton\");\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_article_components_form_EditArticleTitleForm__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_article/components/form/EditArticleTitleForm */ \"engage_article/components/form/EditArticleTitleForm\");\n/* harmony import */ var engage_article_components_form_EditArticleTitleForm__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_form_EditArticleTitleForm__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var engage_article_components_separator_ArticleSeparator__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! engage_article/components/separator/ArticleSeparator */ \"engage_article/components/separator/ArticleSeparator\");\n/* harmony import */ var engage_article_components_separator_ArticleSeparator__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_separator_ArticleSeparator__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var engage_article_graphql_update_article__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! engage_article/graphql/update_article */ \"./server/totara/engage/resources/article/webapi/ajax/update_article.graphql\");\n/* harmony import */ var engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_article/graphql/get_article */ \"./server/totara/engage/resources/article/webapi/ajax/get_article.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n// GraphQL queries\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    EditArticleTitleForm: (engage_article_components_form_EditArticleTitleForm__WEBPACK_IMPORTED_MODULE_2___default()),\n    InlineEditing: (totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0___default()),\n    BookmarkButton: (totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_1___default()),\n    ArticleSeparator: (engage_article_components_separator_ArticleSeparator__WEBPACK_IMPORTED_MODULE_3___default()),\n  },\n\n  props: {\n    title: {\n      type: String,\n      required: true,\n    },\n\n    updateAble: {\n      type: Boolean,\n      required: true,\n    },\n\n    bookmarked: {\n      type: Boolean,\n      required: true,\n    },\n\n    resourceId: {\n      type: [Number, String],\n      required: true,\n    },\n\n    owned: {\n      type: Boolean,\n      required: true,\n    },\n  },\n\n  data() {\n    return {\n      editing: false,\n      submitting: false,\n    };\n  },\n\n  methods: {\n    /**\n     *\n     * @param {String} title\n     */\n    updateTitle(title) {\n      this.submitting = true;\n\n      this.$apollo\n        .mutate({\n          mutation: engage_article_graphql_update_article__WEBPACK_IMPORTED_MODULE_4__[\"default\"],\n          refetchAll: false,\n          variables: {\n            resourceid: this.resourceId,\n            name: title,\n          },\n\n          /**\n           *\n           * @param {DataProxy} proxy\n           * @param {Object} data\n           */\n          updateQuery: (proxy, data) => {\n            proxy.writeQuery({\n              query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_5__[\"default\"],\n              variables: {\n                resourceid: this.resourceId,\n              },\n\n              data: data,\n            });\n          },\n        })\n        .finally(() => {\n          this.submitting = false;\n          this.editing = false;\n        });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/InputText */ \"tui/components/form/InputText\");\n/* harmony import */ var tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/buttons/ButtonGroup */ \"tui/components/buttons/ButtonGroup\");\n/* harmony import */ var tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/buttons/ButtonIcon */ \"tui/components/buttons/ButtonIcon\");\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/buttons/Cancel */ \"tui/components/buttons/Cancel\");\n/* harmony import */ var tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/buttons/Button */ \"tui/components/buttons/Button\");\n/* harmony import */ var tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/components/popover/Popover */ \"tui/components/popover/Popover\");\n/* harmony import */ var tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_util__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/util */ \"tui/util\");\n/* harmony import */ var tui_util__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_util__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! editor_weka/components/Weka */ \"editor_weka/components/Weka\");\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! tui/components/form/Form */ \"tui/components/form/Form\");\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Form__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! tui/components/form/FormRow */ \"tui/components/form/FormRow\");\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var tui_components_icons_Info__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! tui/components/icons/Info */ \"tui/components/icons/Info\");\n/* harmony import */ var tui_components_icons_Info__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Info__WEBPACK_IMPORTED_MODULE_10__);\n/* harmony import */ var core_graphql_file_unused_draft_item_id__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! core/graphql/file_unused_draft_item_id */ \"./server/lib/webapi/ajax/file_unused_draft_item_id.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n\n\n\n\n// GraphQL queries\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ButtonIcon: (tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_2___default()),\n    InputText: (tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_0___default()),\n    ButtonGroup: (tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_1___default()),\n    Button: (tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_4___default()),\n    CancelButton: (tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_3___default()),\n    Popover: (tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_5___default()),\n    Weka: (editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_7___default()),\n    Form: (tui_components_form_Form__WEBPACK_IMPORTED_MODULE_8___default()),\n    FormRow: (tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_9___default()),\n    InfoIcon: (tui_components_icons_Info__WEBPACK_IMPORTED_MODULE_10___default()),\n  },\n\n  props: {\n    articleContent: {\n      type: String,\n      default: '',\n    },\n\n    articleName: {\n      type: String,\n      required: true,\n    },\n  },\n\n  data() {\n    return {\n      // Caching the name separately\n      name: this.articleName,\n      content: {\n        // Default state of editor\n        doc: null,\n        isEmpty: true,\n      },\n\n      draftId: null,\n      submitting: false,\n    };\n  },\n\n  computed: {\n    disabled() {\n      return this.name.length === 0 || this.content.isEmpty;\n    },\n  },\n\n  watch: {\n    articleContent: {\n      immediate: true,\n      /**\n       *\n       * @param {String} value\n       */\n      handler(value) {\n        if (!value) {\n          return;\n        }\n\n        try {\n          this.content.doc = JSON.parse(value);\n        } catch (e) {\n          // Silenced any invalid json string.\n          this.content.doc = null;\n        }\n      },\n    },\n  },\n\n  async mounted() {\n    await this.$_loadDraftId();\n  },\n\n  methods: {\n    /**\n     *\n     * @param {Object} opt\n     */\n    handleUpdate(opt) {\n      this.$_readJson(opt);\n    },\n\n    async $_loadDraftId() {\n      const {\n        data: { item_id },\n      } = await this.$apollo.mutate({ mutation: core_graphql_file_unused_draft_item_id__WEBPACK_IMPORTED_MODULE_11__[\"default\"] });\n      this.draftId = item_id;\n    },\n\n    $_readJson: Object(tui_util__WEBPACK_IMPORTED_MODULE_6__[\"debounce\"])(\n      /**\n       *\n       * @param {Object} opt\n       */\n      function(opt) {\n        this.content.doc = opt.getJSON();\n        this.content.isEmpty = opt.isEmpty();\n      },\n      250,\n      { perArgs: false }\n    ),\n\n    submit() {\n      const params = {\n        name: this.name,\n        content: JSON.stringify(this.content.doc),\n        itemId: this.draftId,\n      };\n\n      this.$emit('next', params);\n      this.$_loadDraftId();\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/Form */ \"tui/components/form/Form\");\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! editor_weka/components/Weka */ \"editor_weka/components/Weka\");\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_util__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/util */ \"tui/util\");\n/* harmony import */ var tui_util__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_util__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/components/buttons/DoneCancelGroup */ \"totara_engage/components/buttons/DoneCancelGroup\");\n/* harmony import */ var totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/loader/Loader */ \"tui/components/loader/Loader\");\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var engage_article_graphql_draft_item__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_article/graphql/draft_item */ \"./server/totara/engage/resources/article/webapi/ajax/draft_item.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n// GraphQL queries\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Form: (tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default()),\n    Weka: (editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_1___default()),\n    DoneCancelGroup: (totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_3___default()),\n    Loader: (tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_4___default()),\n  },\n\n  props: {\n    resourceId: {\n      type: [String, Number],\n      required: true,\n    },\n\n    submitting: Boolean,\n  },\n\n  apollo: {\n    draft: {\n      query: engage_article_graphql_draft_item__WEBPACK_IMPORTED_MODULE_5__[\"default\"],\n      fetchPolicy: 'network-only',\n\n      variables() {\n        return {\n          resourceid: this.resourceId,\n        };\n      },\n\n      result({\n        data: {\n          draft: { content },\n        },\n      }) {\n        if (content) {\n          this.content.doc = JSON.parse(content);\n          this.content.empty = false;\n        }\n      },\n    },\n  },\n\n  data() {\n    return {\n      draft: {},\n      editorMounted: false,\n      content: {\n        doc: null,\n        empty: true,\n      },\n    };\n  },\n\n  methods: {\n    $_readJSON: Object(tui_util__WEBPACK_IMPORTED_MODULE_2__[\"debounce\"])(\n      /**\n       * @param {{\n       *   getJSON: Function,\n       *   isEmpty: Function,\n       *   getFileStorageItemId: Function,\n       * }} option\n       */\n      function(option) {\n        this.content.doc = option.getJSON();\n        this.content.empty = option.isEmpty();\n        this.content.itemId = option.getFileStorageItemId();\n      },\n      100\n    ),\n\n    /**\n     *\n     * @param {Object} option\n     */\n    handleUpdate(option) {\n      this.$_readJSON(option);\n    },\n\n    submit() {\n      const params = {\n        resourceId: this.resourceId,\n        content: JSON.stringify(this.content.doc),\n\n        // This seems to be redundant, but lets keep it here, who know in the future, we\n        format: this.draft.format,\n        itemId: this.content.itemId,\n      };\n\n      this.$emit('submit', params);\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleContentForm.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/Form */ \"tui/components/form/Form\");\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/InputText */ \"tui/components/form/InputText\");\n/* harmony import */ var tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! totara_engage/components/buttons/DoneCancelGroup */ \"totara_engage/components/buttons/DoneCancelGroup\");\n/* harmony import */ var totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_2__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    DoneCancelGroup: (totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_2___default()),\n    Form: (tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default()),\n    InputText: (tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_1___default()),\n  },\n\n  props: {\n    submitting: {\n      type: Boolean,\n      default: false,\n    },\n\n    title: {\n      type: String,\n      required: true,\n    },\n  },\n\n  data() {\n    return {\n      // Caching the inner title, as we will emit the event to update it.\n      innerTitle: this.title,\n    };\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_apollo_client__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/apollo_client */ \"tui/apollo_client\");\n/* harmony import */ var tui_apollo_client__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_apollo_client__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/loader/Loader */ \"tui/components/loader/Loader\");\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/modal/ModalPresenter */ \"tui/components/modal/ModalPresenter\");\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var totara_comment_components_box_SidePanelCommentBox__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_comment/components/box/SidePanelCommentBox */ \"totara_comment/components/box/SidePanelCommentBox\");\n/* harmony import */ var totara_comment_components_box_SidePanelCommentBox__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_comment_components_box_SidePanelCommentBox__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/components/sidepanel/access/AccessDisplay */ \"totara_engage/components/sidepanel/access/AccessDisplay\");\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! totara_engage/components/sidepanel/access/AccessSetting */ \"totara_engage/components/sidepanel/access/AccessSetting\");\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var totara_engage_components_sidepanel_EngageSidePanel__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! totara_engage/components/sidepanel/EngageSidePanel */ \"totara_engage/components/sidepanel/EngageSidePanel\");\n/* harmony import */ var totara_engage_components_sidepanel_EngageSidePanel__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_EngageSidePanel__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_engage/components/modal/EngageWarningModal */ \"totara_engage/components/modal/EngageWarningModal\");\n/* harmony import */ var totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! totara_engage/components/sidepanel/media/MediaSetting */ \"totara_engage/components/sidepanel/media/MediaSetting\");\n/* harmony import */ var totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! tui/components/profile/MiniProfileCard */ \"tui/components/profile/MiniProfileCard\");\n/* harmony import */ var tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var tui_components_dropdown_DropdownItem__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! tui/components/dropdown/DropdownItem */ \"tui/components/dropdown/DropdownItem\");\n/* harmony import */ var tui_components_dropdown_DropdownItem__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(tui_components_dropdown_DropdownItem__WEBPACK_IMPORTED_MODULE_10__);\n/* harmony import */ var engage_article_components_sidepanel_content_ArticlePlaylistBox__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! engage_article/components/sidepanel/content/ArticlePlaylistBox */ \"engage_article/components/sidepanel/content/ArticlePlaylistBox\");\n/* harmony import */ var engage_article_components_sidepanel_content_ArticlePlaylistBox__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_sidepanel_content_ArticlePlaylistBox__WEBPACK_IMPORTED_MODULE_11__);\n/* harmony import */ var engage_article_components_sidepanel_Related__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! engage_article/components/sidepanel/Related */ \"engage_article/components/sidepanel/Related\");\n/* harmony import */ var engage_article_components_sidepanel_Related__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_sidepanel_Related__WEBPACK_IMPORTED_MODULE_12__);\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! tui/notifications */ \"tui/notifications\");\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(tui_notifications__WEBPACK_IMPORTED_MODULE_13__);\n/* harmony import */ var engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! engage_article/graphql/get_article */ \"./server/totara/engage/resources/article/webapi/ajax/get_article.graphql\");\n/* harmony import */ var engage_article_graphql_update_article__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! engage_article/graphql/update_article */ \"./server/totara/engage/resources/article/webapi/ajax/update_article.graphql\");\n/* harmony import */ var engage_article_graphql_delete_article__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! engage_article/graphql/delete_article */ \"./server/totara/engage/resources/article/webapi/ajax/delete_article.graphql\");\n/* harmony import */ var totara_engage_graphql_advanced_features__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! totara_engage/graphql/advanced_features */ \"./server/totara/engage/webapi/ajax/advanced_features.graphql\");\n/* harmony import */ var totara_reportedcontent_graphql_create_review__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! totara_reportedcontent/graphql/create_review */ \"./server/totara/reportedcontent/webapi/ajax/create_review.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n// GraphQL queries\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AccessDisplay: (totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_4___default()),\n    AccessSetting: (totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_5___default()),\n    ArticlePlaylistBox: (engage_article_components_sidepanel_content_ArticlePlaylistBox__WEBPACK_IMPORTED_MODULE_11___default()),\n    EngageSidePanel: (totara_engage_components_sidepanel_EngageSidePanel__WEBPACK_IMPORTED_MODULE_6___default()),\n    EngageWarningModal: (totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_7___default()),\n    Loader: (tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_1___default()),\n    MediaSetting: (totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_8___default()),\n    ModalPresenter: (tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_2___default()),\n    Related: (engage_article_components_sidepanel_Related__WEBPACK_IMPORTED_MODULE_12___default()),\n    SidePanelCommentBox: (totara_comment_components_box_SidePanelCommentBox__WEBPACK_IMPORTED_MODULE_3___default()),\n    MiniProfileCard: (tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_9___default()),\n    DropdownItem: (tui_components_dropdown_DropdownItem__WEBPACK_IMPORTED_MODULE_10___default()),\n  },\n\n  props: {\n    resourceId: {\n      type: [Number, String],\n      required: true,\n    },\n  },\n\n  apollo: {\n    article: {\n      query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_14__[\"default\"],\n      variables() {\n        return {\n          id: this.resourceId,\n        };\n      },\n    },\n\n    features: {\n      query: totara_engage_graphql_advanced_features__WEBPACK_IMPORTED_MODULE_17__[\"default\"],\n    },\n  },\n\n  data() {\n    return {\n      article: {},\n      submitting: false,\n      openModalFromButtonLabel: false,\n      openModalFromAction: false,\n      features: {},\n    };\n  },\n\n  computed: {\n    user() {\n      if (!this.article.resource || !this.article.resource.user) {\n        return {};\n      }\n\n      return this.article.resource.user;\n    },\n\n    sharedByCount() {\n      return this.article.sharedByCount;\n    },\n\n    likeButtonLabel() {\n      if (this.article.reacted) {\n        return this.$str(\n          'removelikearticle',\n          'engage_article',\n          this.article.resource.name\n        );\n      }\n\n      return this.$str(\n        'likearticle',\n        'engage_article',\n        this.article.resource.name\n      );\n    },\n\n    featureRecommenders() {\n      return this.features && this.features.recommenders;\n    },\n  },\n\n  methods: {\n    /**\n     * Updates Access for this article\n     *\n     * @param {String} access The access level of the article\n     * @param {Array} topics Topics that this article should be shared with\n     * @param {Array} shares An array of group id's that this article is shared with\n     */\n    updateAccess({ access, topics, shares, timeView }) {\n      this.submitting = true;\n      this.$apollo\n        .mutate({\n          mutation: engage_article_graphql_update_article__WEBPACK_IMPORTED_MODULE_15__[\"default\"],\n          refetchAll: false,\n          variables: {\n            resourceid: this.resourceId,\n            access: access,\n            topics: topics.map(({ id }) => id),\n            shares: shares,\n            timeview: timeView,\n          },\n\n          update: (proxy, { data }) => {\n            proxy.writeQuery({\n              query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_14__[\"default\"],\n              variables: { id: this.resourceId },\n              data,\n            });\n          },\n        })\n        .finally(() => {\n          this.submitting = false;\n        });\n    },\n\n    handleDelete() {\n      this.$apollo\n        .mutate({\n          mutation: engage_article_graphql_delete_article__WEBPACK_IMPORTED_MODULE_16__[\"default\"],\n          variables: {\n            resourceid: this.resourceId,\n          },\n          refetchAll: false,\n        })\n        .then(({ data }) => {\n          if (data.result) {\n            this.$children.openModal = false;\n            window.location.href = this.$url(\n              '/totara/engage/your_resources.php'\n            );\n          }\n        });\n    },\n\n    /**\n     *\n     * @param {Boolean} status\n     */\n    updateLikeStatus(status) {\n      let { article } = tui_apollo_client__WEBPACK_IMPORTED_MODULE_0___default.a.readQuery({\n        query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_14__[\"default\"],\n        variables: {\n          id: this.resourceId,\n        },\n      });\n\n      article = Object.assign({}, article);\n      article.reacted = status;\n\n      tui_apollo_client__WEBPACK_IMPORTED_MODULE_0___default.a.writeQuery({\n        query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_14__[\"default\"],\n        variables: { id: this.resourceId },\n        data: { article: article },\n      });\n    },\n\n    /**\n     * Report the attached resource\n     * @returns {Promise<void>}\n     */\n    async reportResource() {\n      if (this.submitting) {\n        return;\n      }\n      this.submitting = true;\n      try {\n        let response = await this.$apollo\n          .mutate({\n            mutation: totara_reportedcontent_graphql_create_review__WEBPACK_IMPORTED_MODULE_18__[\"default\"],\n            refetchAll: false,\n            variables: {\n              component: 'engage_article',\n              area: '',\n              item_id: this.resourceId,\n              url: window.location.href,\n            },\n          })\n          .then(response => response.data.review);\n\n        if (response.success) {\n          await Object(tui_notifications__WEBPACK_IMPORTED_MODULE_13__[\"notify\"])({\n            message: this.$str('reported', 'totara_reportedcontent'),\n            type: 'success',\n          });\n        } else {\n          await Object(tui_notifications__WEBPACK_IMPORTED_MODULE_13__[\"notify\"])({\n            message: this.$str('reported_failed', 'totara_reportedcontent'),\n            type: 'error',\n          });\n        }\n      } catch (e) {\n        await Object(tui_notifications__WEBPACK_IMPORTED_MODULE_13__[\"notify\"])({\n          message: this.$str('error:reportresource', 'engage_article'),\n          type: 'error',\n        });\n      } finally {\n        this.submitting = false;\n      }\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_article_components_card_RelatedCard__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_article/components/card/RelatedCard */ \"engage_article/components/card/RelatedCard\");\n/* harmony import */ var engage_article_components_card_RelatedCard__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_card_RelatedCard__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var ml_recommender_graphql_get_recommended_articles__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ml_recommender/graphql/get_recommended_articles */ \"./server/ml/recommender/webapi/ajax/get_recommended_articles.graphql\");\n/* harmony import */ var totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/graphql/update_bookmark */ \"./server/totara/engage/webapi/ajax/update_bookmark.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    RelatedCard: (engage_article_components_card_RelatedCard__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n  props: {\n    resourceId: {\n      type: [Number, String],\n      required: true,\n    },\n  },\n\n  data() {\n    return {\n      articles: [],\n    };\n  },\n\n  mounted() {\n    this.getRecommendations();\n  },\n\n  methods: {\n    getRecommendations() {\n      this.$apollo\n        .query({\n          query: ml_recommender_graphql_get_recommended_articles__WEBPACK_IMPORTED_MODULE_2__[\"default\"],\n          refetchAll: false,\n          variables: {\n            article_id: this.resourceId,\n            source: totara_engage_index__WEBPACK_IMPORTED_MODULE_1__[\"UrlSourceType\"].article(this.resourceId),\n          },\n        })\n        .then(({ data }) => {\n          this.articles = data.articles.map(item => {\n            const {\n              bookmarked,\n              extra,\n              name,\n              instanceid,\n              reactions,\n              url,\n            } = item;\n            const { image, timeview } = JSON.parse(extra);\n            return {\n              bookmarked,\n              instanceid,\n              image,\n              name,\n              reactions,\n              timeview,\n              url,\n            };\n          });\n        });\n    },\n\n    update(resourceId, bookmarked) {\n      this.$apollo.mutate({\n        mutation: totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_3__[\"default\"],\n        refetchAll: false,\n        variables: {\n          itemid: resourceId,\n          component: 'engage_article',\n          bookmarked,\n        },\n      });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_playlist_components_box_ResourcePlaylistBox__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_playlist/components/box/ResourcePlaylistBox */ \"totara_playlist/components/box/ResourcePlaylistBox\");\n/* harmony import */ var totara_playlist_components_box_ResourcePlaylistBox__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_playlist_components_box_ResourcePlaylistBox__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/icons/Loading */ \"tui/components/icons/Loading\");\n/* harmony import */ var tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_2__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ResourcePlaylistBox: (totara_playlist_components_box_ResourcePlaylistBox__WEBPACK_IMPORTED_MODULE_0___default()),\n    Loading: (tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_1___default()),\n  },\n\n  props: {\n    resourceId: {\n      type: [String, Number],\n      required: true,\n    },\n  },\n\n  data() {\n    return {\n      loading: false,\n      show: true,\n    };\n  },\n\n  computed: {\n    urlSource() {\n      return totara_engage_index__WEBPACK_IMPORTED_MODULE_2__[\"UrlSourceType\"].article(this.resourceId);\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/layouts/LayoutOneColumnContentWithSidePanel */ \"tui/components/layouts/LayoutOneColumnContentWithSidePanel\");\n/* harmony import */ var tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/loader/Loader */ \"tui/components/loader/Loader\");\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_article_components_sidepanel_ArticleSidePanel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_article/components/sidepanel/ArticleSidePanel */ \"engage_article/components/sidepanel/ArticleSidePanel\");\n/* harmony import */ var engage_article_components_sidepanel_ArticleSidePanel__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_sidepanel_ArticleSidePanel__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var engage_article_components_content_ArticleContent__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! engage_article/components/content/ArticleContent */ \"engage_article/components/content/ArticleContent\");\n/* harmony import */ var engage_article_components_content_ArticleContent__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_content_ArticleContent__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var engage_article_components_content_ArticleTitle__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! engage_article/components/content/ArticleTitle */ \"engage_article/components/content/ArticleTitle\");\n/* harmony import */ var engage_article_components_content_ArticleTitle__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_content_ArticleTitle__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! totara_engage/components/header/ResourceNavigationBar */ \"totara_engage/components/header/ResourceNavigationBar\");\n/* harmony import */ var totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! engage_article/graphql/get_article */ \"./server/totara/engage/resources/article/webapi/ajax/get_article.graphql\");\n/* harmony import */ var totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_engage/graphql/update_bookmark */ \"./server/totara/engage/webapi/ajax/update_bookmark.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n// GraphQL\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ArticleTitle: (engage_article_components_content_ArticleTitle__WEBPACK_IMPORTED_MODULE_4___default()),\n    ArticleSidePanel: (engage_article_components_sidepanel_ArticleSidePanel__WEBPACK_IMPORTED_MODULE_2___default()),\n    ArticleContent: (engage_article_components_content_ArticleContent__WEBPACK_IMPORTED_MODULE_3___default()),\n    Layout: (tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_0___default()),\n    Loader: (tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_1___default()),\n    ResourceNavigationBar: (totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_5___default()),\n  },\n\n  props: {\n    resourceId: {\n      type: Number,\n      required: true,\n    },\n\n    backButton: {\n      type: Object,\n      required: false,\n    },\n\n    navigationButtons: {\n      type: Object,\n      required: false,\n    },\n  },\n\n  data() {\n    return {\n      article: {},\n      bookmarked: false,\n    };\n  },\n\n  computed: {\n    articleName() {\n      if (!this.article.resource || !this.article.resource.name) {\n        return '';\n      }\n\n      return this.article.resource.name;\n    },\n  },\n\n  apollo: {\n    article: {\n      query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n      variables() {\n        return {\n          id: this.resourceId,\n        };\n      },\n      result({ data: { article } }) {\n        this.bookmarked = article.bookmarked;\n      },\n    },\n  },\n\n  methods: {\n    updateBookmark() {\n      this.bookmarked = !this.bookmarked;\n      this.$apollo.mutate({\n        mutation: totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n        refetchAll: false,\n        variables: {\n          itemid: this.resourceId,\n          component: 'engage_article',\n          bookmarked: this.bookmarked,\n        },\n      });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=style&index=0&lang=scss&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=style&index=0&lang=scss& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=style&index=0&lang=scss&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=style&index=0&lang=scss& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=style&index=0&lang=scss&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=style&index=0&lang=scss& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=style&index=0&lang=scss&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=style&index=0&lang=scss& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=style&index=0&lang=scss&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=style&index=0&lang=scss& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/separator/ArticleSeparator.vue?./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=style&index=0&lang=scss&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=style&index=0&lang=scss& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=style&index=0&lang=scss&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=style&index=0&lang=scss& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=style&index=0&lang=scss&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=style&index=0&lang=scss& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=style&index=0&lang=scss&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=style&index=0&lang=scss& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?./node_modules/mini-css-extract-plugin/dist/loader.js!./client/tooling/webpack/css_raw_loader.js??ref--3-1!./node_modules/postcss-loader/src??ref--3-2!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=template&id=20bcfe1b&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=template&id=20bcfe1b& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-engageArticle-createArticle\" },\n    [\n      _c(\"ArticleForm\", {\n        directives: [\n          {\n            name: \"show\",\n            rawName: \"v-show\",\n            value: _vm.stage === 0,\n            expression: \"stage === 0\"\n          }\n        ],\n        attrs: {\n          \"article-name\": _vm.article.name,\n          \"article-content\": _vm.article.content\n        },\n        on: {\n          next: _vm.next,\n          cancel: function($event) {\n            return _vm.$emit(\"cancel\")\n          }\n        }\n      }),\n      _vm._v(\" \"),\n      _c(\"AccessForm\", {\n        directives: [\n          {\n            name: \"show\",\n            rawName: \"v-show\",\n            value: _vm.stage === 1,\n            expression: \"stage === 1\"\n          }\n        ],\n        attrs: {\n          \"item-id\": \"0\",\n          component: \"engage_article\",\n          \"show-back\": true,\n          submitting: _vm.submitting,\n          \"selected-access\": _vm.containerValues.access,\n          \"private-disabled\": _vm.privateDisabled,\n          \"restricted-disabled\": _vm.restrictedDisabled,\n          container: _vm.container,\n          \"enable-time-view\": true\n        },\n        on: {\n          done: _vm.done,\n          back: _vm.back,\n          cancel: function($event) {\n            return _vm.$emit(\"cancel\")\n          }\n        }\n      })\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=template&id=18b826b6&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=template&id=18b826b6& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"BaseCard\",\n    {\n      staticClass: \"tui-engageArticle-articleCard\",\n      attrs: {\n        \"data-card-unique\": _vm.instanceId,\n        href: _vm.url,\n        \"show-footnotes\": _vm.showFootnotes,\n        footnotes: _vm.footnotes\n      },\n      on: {\n        mouseover: function($event) {\n          return _vm.$_handleHovered(true)\n        },\n        mouseleave: function($event) {\n          return _vm.$_handleHovered(false)\n        }\n      }\n    },\n    [\n      _c(\n        \"ImageHeader\",\n        {\n          staticClass: \"tui-engageArticle-articleCard__imageheader\",\n          attrs: { slot: \"header-image\", \"show-cover\": _vm.hovered },\n          slot: \"header-image\"\n        },\n        [\n          _c(\"img\", {\n            staticClass: \"tui-engageArticle-articleCard__image\",\n            attrs: { slot: \"image\", alt: _vm.name, src: _vm.extraData.image },\n            slot: \"image\"\n          }),\n          _vm._v(\" \"),\n          _c(\n            \"div\",\n            {\n              staticClass: \"tui-engageArticle-articleCard__icons\",\n              attrs: { slot: \"actions\" },\n              slot: \"actions\"\n            },\n            _vm._l(_vm.actions, function(action, i) {\n              return _c(\n                \"ButtonIcon\",\n                {\n                  key: i,\n                  attrs: {\n                    \"aria-label\": action.alt,\n                    styleclass: { primary: false, circle: true }\n                  }\n                },\n                [_c(action.component, { tag: \"component\" })],\n                1\n              )\n            }),\n            1\n          )\n        ]\n      ),\n      _vm._v(\" \"),\n      _c(\n        \"CardHeader\",\n        {\n          staticClass: \"tui-engageArticle-articleCard__header\",\n          attrs: { slot: \"header\" },\n          slot: \"header\"\n        },\n        [\n          _c(\"BookmarkButton\", {\n            directives: [\n              {\n                name: \"show\",\n                rawName: \"v-show\",\n                value: !_vm.owned,\n                expression: \"!owned\"\n              }\n            ],\n            staticClass: \"tui-engageArticle-articleCard__bookmark\",\n            attrs: {\n              slot: \"first\",\n              size: \"300\",\n              bookmarked: _vm.innerBookmarked,\n              primary: false,\n              circle: false,\n              small: true,\n              transparent: true\n            },\n            on: { click: _vm.updateBookmark },\n            slot: \"first\"\n          }),\n          _vm._v(\" \"),\n          _c(\n            \"h3\",\n            {\n              staticClass: \"tui-engageArticle-articleCard__title\",\n              attrs: { slot: \"second\", id: _vm.labelId },\n              slot: \"second\"\n            },\n            [_vm._v(\"\\n      \" + _vm._s(_vm.name) + \"\\n    \")]\n          ),\n          _vm._v(\" \"),\n          _vm.extraData.timeview\n            ? _c(\n                \"div\",\n                {\n                  staticClass: \"tui-engageArticle-articleCard__subTitle\",\n                  attrs: { slot: \"third\" },\n                  slot: \"third\"\n                },\n                [\n                  _c(\"TimeIcon\", {\n                    attrs: {\n                      size: \"200\",\n                      alt: _vm.$str(\"time\", \"totara_engage\"),\n                      \"custom-class\": \"tui-icon--dimmed\"\n                    }\n                  }),\n                  _vm._v(\" \"),\n                  _c(\n                    \"span\",\n                    {\n                      staticClass:\n                        \"tui-engageArticle-articleCard__subTitle-text\"\n                    },\n                    [_vm._v(_vm._s(_vm.getTimeView))]\n                  )\n                ],\n                1\n              )\n            : _vm._e()\n        ],\n        1\n      ),\n      _vm._v(\" \"),\n      _c(\n        \"div\",\n        {\n          staticClass: \"tui-engageArticle-articleCard__footer\",\n          attrs: { slot: \"footer\" },\n          slot: \"footer\"\n        },\n        [\n          _vm._l(_vm.statIcons, function(statIcon) {\n            return _c(\n              \"StatIcon\",\n              {\n                key: statIcon.type,\n                attrs: {\n                  title: statIcon.title,\n                  \"stat-number\": statIcon.statNumber\n                }\n              },\n              [\n                _c(statIcon.icon, {\n                  tag: \"component\",\n                  attrs: { title: statIcon.title }\n                })\n              ],\n              1\n            )\n          }),\n          _vm._v(\" \"),\n          _c(\"AccessIcon\", {\n            attrs: {\n              access: _vm.access,\n              size: \"300\",\n              \"custom-class\": \"tui-engageArticle-articleCard__visibilityIcon\"\n            }\n          })\n        ],\n        2\n      )\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=template&id=f16a1e2a&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=template&id=f16a1e2a& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"Card\",\n    {\n      staticClass: \"tui-articleRelatedCard\",\n      attrs: { clickable: true },\n      on: { click: _vm.handleClickCard }\n    },\n    [\n      _c(\"img\", {\n        staticClass: \"tui-articleRelatedCard__img\",\n        attrs: { src: _vm.image, alt: _vm.name }\n      }),\n      _vm._v(\" \"),\n      _c(\"section\", { staticClass: \"tui-articleRelatedCard__content\" }, [\n        _c(\"a\", { attrs: { href: _vm.url } }, [\n          _vm._v(\"\\n      \" + _vm._s(_vm.name) + \"\\n    \")\n        ]),\n        _vm._v(\" \"),\n        _c(\n          \"p\",\n          [\n            _vm.timeviewString\n              ? _c(\n                  \"span\",\n                  { staticClass: \"tui-articleRelatedCard__timeview\" },\n                  [\n                    _c(\"TimeIcon\", {\n                      attrs: {\n                        size: \"200\",\n                        alt: _vm.$str(\"time\", \"totara_engage\"),\n                        \"custom-class\": \"tui-articleRelatedCard--dimmed\"\n                      }\n                    }),\n                    _vm._v(\n                      \"\\n        \" + _vm._s(_vm.timeviewString) + \"\\n      \"\n                    )\n                  ],\n                  1\n                )\n              : _vm._e(),\n            _vm._v(\" \"),\n            _c(\"Like\", {\n              attrs: {\n                size: \"200\",\n                alt: _vm.$str(\"like\", \"totara_engage\"),\n                \"custom-class\": \"tui-articleRelatedCard--dimmed\"\n              }\n            }),\n            _vm._v(\" \"),\n            _c(\"span\", [_vm._v(_vm._s(_vm.reactions))])\n          ],\n          1\n        )\n      ]),\n      _vm._v(\" \"),\n      _c(\"BookmarkButton\", {\n        staticClass: \"tui-articleRelatedCard__bookmark\",\n        attrs: {\n          size: \"300\",\n          bookmarked: _vm.innerBookmarked,\n          primary: false,\n          circle: false,\n          small: true,\n          transparent: true\n        },\n        on: { click: _vm.handleClickBookmark }\n      })\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=template&id=7827ff08&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=template&id=7827ff08& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-articleContent\" },\n    [\n      _c(\n        \"InlineEditing\",\n        {\n          directives: [\n            {\n              name: \"show\",\n              rawName: \"v-show\",\n              value: !_vm.editing,\n              expression: \"!editing\"\n            }\n          ],\n          attrs: {\n            \"full-width\": true,\n            \"restricted-mode\": true,\n            \"update-able\": _vm.updateAble\n          },\n          on: {\n            click: function($event) {\n              _vm.editing = true\n            }\n          }\n        },\n        [\n          _c(\"div\", {\n            ref: \"content\",\n            staticClass: \"tui-articleContent__content\",\n            attrs: { slot: \"content\" },\n            domProps: { innerHTML: _vm._s(_vm.content) },\n            slot: \"content\"\n          })\n        ]\n      ),\n      _vm._v(\" \"),\n      _vm.editing\n        ? _c(\"EditArticleForm\", {\n            attrs: {\n              \"resource-id\": _vm.resourceId,\n              submitting: _vm.submitting\n            },\n            on: {\n              submit: _vm.updateArticle,\n              cancel: function($event) {\n                _vm.editing = false\n              }\n            }\n          })\n        : _vm._e()\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=template&id=be80d1b2&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=template&id=be80d1b2& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-articleTitle\" },\n    [\n      _c(\n        \"div\",\n        { staticClass: \"tui-articleTitle__head\" },\n        [\n          _c(\n            \"InlineEditing\",\n            {\n              directives: [\n                {\n                  name: \"show\",\n                  rawName: \"v-show\",\n                  value: !_vm.editing,\n                  expression: \"!editing\"\n                }\n              ],\n              attrs: { \"update-able\": _vm.updateAble, \"full-width\": true },\n              on: {\n                click: function($event) {\n                  _vm.editing = true\n                }\n              }\n            },\n            [\n              _c(\n                \"h3\",\n                {\n                  staticClass: \"tui-articleTitle__head__title\",\n                  attrs: { slot: \"content\" },\n                  slot: \"content\"\n                },\n                [_vm._v(\"\\n        \" + _vm._s(_vm.title) + \"\\n      \")]\n              )\n            ]\n          ),\n          _vm._v(\" \"),\n          _vm.editing\n            ? _c(\"EditArticleTitleForm\", {\n                attrs: { title: _vm.title, submitting: _vm.submitting },\n                on: {\n                  cancel: function($event) {\n                    _vm.editing = false\n                  },\n                  submit: _vm.updateTitle\n                }\n              })\n            : _vm._e(),\n          _vm._v(\" \"),\n          !_vm.owned\n            ? _c(\"BookmarkButton\", {\n                attrs: {\n                  primary: false,\n                  circle: true,\n                  bookmarked: _vm.bookmarked,\n                  size: \"300\"\n                },\n                on: {\n                  click: function($event) {\n                    return _vm.$emit(\"bookmark\", $event)\n                  }\n                }\n              })\n            : _vm._e()\n        ],\n        1\n      ),\n      _vm._v(\" \"),\n      _c(\"ArticleSeparator\")\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=template&id=01a1681e&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=template&id=01a1681e& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"Form\",\n    {\n      staticClass: \"tui-articleForm\",\n      attrs: { vertical: true, \"input-width\": \"full\" }\n    },\n    [\n      _c(\"FormRow\", {\n        staticClass: \"tui-articleForm__title\",\n        attrs: {\n          hidden: true,\n          label: _vm.$str(\"articletitle\", \"engage_article\"),\n          required: true\n        },\n        scopedSlots: _vm._u([\n          {\n            key: \"default\",\n            fn: function(ref) {\n              var id = ref.id\n              return [\n                _c(\"InputText\", {\n                  attrs: {\n                    id: id,\n                    name: \"article-title\",\n                    maxlength: 75,\n                    placeholder: _vm.$str(\"entertitle\", \"engage_article\"),\n                    disabled: _vm.submitting,\n                    required: true\n                  },\n                  model: {\n                    value: _vm.name,\n                    callback: function($$v) {\n                      _vm.name = $$v\n                    },\n                    expression: \"name\"\n                  }\n                })\n              ]\n            }\n          }\n        ])\n      }),\n      _vm._v(\" \"),\n      _c(\n        \"div\",\n        { staticClass: \"tui-articleForm__description\" },\n        [\n          _c(\"FormRow\", {\n            staticClass: \"tui-articleForm__description__formRow\",\n            attrs: {\n              hidden: true,\n              label: _vm.$str(\"content\", \"engage_article\"),\n              required: true,\n              \"is-stacked\": false\n            },\n            scopedSlots: _vm._u([\n              {\n                key: \"default\",\n                fn: function(ref) {\n                  var id = ref.id\n                  return [\n                    _vm.draftId\n                      ? _c(\"Weka\", {\n                          attrs: {\n                            id: id,\n                            component: \"engage_article\",\n                            area: \"content\",\n                            doc: _vm.content.doc,\n                            \"file-item-id\": _vm.draftId,\n                            placeholder: _vm.$str(\n                              \"entercontent\",\n                              \"engage_article\"\n                            )\n                          },\n                          on: { update: _vm.handleUpdate }\n                        })\n                      : _vm._e()\n                  ]\n                }\n              }\n            ])\n          }),\n          _vm._v(\" \"),\n          _c(\n            \"div\",\n            { staticClass: \"tui-articleForm__description__tip\" },\n            [\n              _c(\"p\", [\n                _vm._v(_vm._s(_vm.$str(\"contributetip\", \"totara_engage\")))\n              ]),\n              _vm._v(\" \"),\n              _c(\n                \"Popover\",\n                {\n                  attrs: { position: \"right\" },\n                  scopedSlots: _vm._u([\n                    {\n                      key: \"trigger\",\n                      fn: function(ref) {\n                        var isOpen = ref.isOpen\n                        return [\n                          _c(\n                            \"ButtonIcon\",\n                            {\n                              staticClass:\n                                \"tui-articleForm__description__iconButton\",\n                              attrs: {\n                                \"aria-expanded\": isOpen.toString(),\n                                \"aria-label\": _vm.$str(\"info\", \"moodle\"),\n                                styleclass: {\n                                  primary: true,\n                                  small: true,\n                                  transparentNoPadding: true\n                                }\n                              }\n                            },\n                            [_c(\"InfoIcon\")],\n                            1\n                          )\n                        ]\n                      }\n                    }\n                  ])\n                },\n                [\n                  _vm._v(\" \"),\n                  _c(\n                    \"p\",\n                    {\n                      staticClass: \"tui-articleForm__description__tip__content\"\n                    },\n                    [\n                      _vm._v(\n                        \"\\n          \" +\n                          _vm._s(\n                            _vm.$str(\"contributetip_help\", \"totara_engage\")\n                          ) +\n                          \"\\n        \"\n                      )\n                    ]\n                  )\n                ]\n              )\n            ],\n            1\n          )\n        ],\n        1\n      ),\n      _vm._v(\" \"),\n      _c(\n        \"ButtonGroup\",\n        { staticClass: \"tui-articleForm__buttons\" },\n        [\n          _c(\"Button\", {\n            attrs: {\n              loading: _vm.submitting,\n              styleclass: { primary: \"true\" },\n              disabled: _vm.disabled,\n              \"aria-label\": _vm.$str(\"createarticleshort\", \"engage_article\"),\n              text: _vm.$str(\"next\", \"moodle\")\n            },\n            on: { click: _vm.submit }\n          }),\n          _vm._v(\" \"),\n          _c(\"CancelButton\", {\n            attrs: { disabled: _vm.submitting },\n            on: {\n              click: function($event) {\n                return _vm.$emit(\"cancel\")\n              }\n            }\n          })\n        ],\n        1\n      )\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=template&id=93461ec6&":
/*!************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=template&id=93461ec6& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"Form\",\n    { staticClass: \"tui-editArticleContentForm\" },\n    [\n      _c(\"Loader\", { attrs: { fullpage: true, loading: !_vm.editorMounted } }),\n      _vm._v(\" \"),\n      !_vm.$apollo.loading\n        ? _c(\"Weka\", {\n            staticClass: \"tui-editArticleContentForm__editor\",\n            attrs: {\n              component: \"engage_article\",\n              area: \"content\",\n              \"instance-id\": _vm.resourceId,\n              doc: _vm.content.doc,\n              \"file-item-id\": _vm.draft.file_item_id\n            },\n            on: {\n              \"editor-mounted\": function($event) {\n                _vm.editorMounted = true\n              },\n              update: _vm.handleUpdate\n            }\n          })\n        : _vm._e(),\n      _vm._v(\" \"),\n      _c(\"DoneCancelGroup\", {\n        attrs: {\n          loading: _vm.submitting,\n          disabled: _vm.content.empty || _vm.submitting\n        },\n        on: {\n          done: _vm.submit,\n          cancel: function($event) {\n            return _vm.$emit(\"cancel\")\n          }\n        }\n      })\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleContentForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=template&id=11c34d08&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=template&id=11c34d08& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"Form\",\n    { staticClass: \"tui-editArticleTitleForm\" },\n    [\n      _c(\"InputText\", {\n        staticClass: \"tui-editArticleTitleForm__input\",\n        attrs: {\n          name: \"title\",\n          disabled: _vm.submitting,\n          maxlength: 60,\n          placeholder: _vm.$str(\"entertitle\", \"engage_article\"),\n          \"aria-label\": _vm.$str(\"articletitle\", \"engage_article\")\n        },\n        on: {\n          submit: function($event) {\n            return _vm.$emit(\"submit\", _vm.innerTitle)\n          }\n        },\n        model: {\n          value: _vm.innerTitle,\n          callback: function($$v) {\n            _vm.innerTitle = $$v\n          },\n          expression: \"innerTitle\"\n        }\n      }),\n      _vm._v(\" \"),\n      _c(\"DoneCancelGroup\", {\n        attrs: {\n          loading: _vm.submitting,\n          disabled: _vm.submitting || !_vm.innerTitle\n        },\n        on: {\n          done: function($event) {\n            return _vm.$emit(\"submit\", _vm.innerTitle)\n          },\n          cancel: function($event) {\n            return _vm.$emit(\"cancel\")\n          }\n        }\n      })\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=template&id=01d50df0&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=template&id=01d50df0& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"div\", { staticClass: \"tui-articleSeparator\" })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/separator/ArticleSeparator.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=template&id=3c516de8&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=template&id=3c516de8& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return !_vm.$apollo.loading\n    ? _c(\n        \"EngageSidePanel\",\n        {\n          staticClass: \"tui-articleSidePanel\",\n          scopedSlots: _vm._u(\n            [\n              {\n                key: \"modal\",\n                fn: function() {\n                  return [\n                    _c(\n                      \"ModalPresenter\",\n                      {\n                        attrs: { open: _vm.openModalFromAction },\n                        on: {\n                          \"request-close\": function($event) {\n                            _vm.openModalFromAction = false\n                          }\n                        }\n                      },\n                      [\n                        _c(\"EngageWarningModal\", {\n                          attrs: {\n                            title: _vm.$str(\n                              \"deletewarningtitle\",\n                              \"engage_article\"\n                            ),\n                            \"message-content\": _vm.$str(\n                              \"deletewarningmsg\",\n                              \"engage_article\"\n                            )\n                          },\n                          on: { delete: _vm.handleDelete }\n                        })\n                      ],\n                      1\n                    )\n                  ]\n                },\n                proxy: true\n              },\n              {\n                key: \"overview\",\n                fn: function() {\n                  return [\n                    _c(\"Loader\", {\n                      attrs: { fullpage: true, loading: _vm.submitting }\n                    }),\n                    _vm._v(\" \"),\n                    _c(\n                      \"p\",\n                      { staticClass: \"tui-articleSidePanel__timeDescription\" },\n                      [\n                        _vm._v(\n                          \"\\n      \" +\n                            _vm._s(_vm.article.timedescription) +\n                            \"\\n    \"\n                        )\n                      ]\n                    ),\n                    _vm._v(\" \"),\n                    _vm.article.owned\n                      ? _c(\"AccessSetting\", {\n                          attrs: {\n                            \"item-id\": _vm.resourceId,\n                            component: \"engage_article\",\n                            \"access-value\": _vm.article.resource.access,\n                            topics: _vm.article.topics,\n                            submitting: false,\n                            \"open-modal\": _vm.openModalFromButtonLabel,\n                            \"selected-time-view\": _vm.article.timeview,\n                            \"enable-time-view\": true\n                          },\n                          on: {\n                            \"access-update\": _vm.updateAccess,\n                            \"close-modal\": function($event) {\n                              _vm.openModalFromButtonLabel = false\n                            }\n                          }\n                        })\n                      : _c(\"AccessDisplay\", {\n                          attrs: {\n                            \"access-value\": _vm.article.resource.access,\n                            \"time-view\": _vm.article.timeview,\n                            topics: _vm.article.topics,\n                            \"show-button\": false\n                          }\n                        }),\n                    _vm._v(\" \"),\n                    _c(\"MediaSetting\", {\n                      attrs: {\n                        owned: _vm.article.owned,\n                        \"access-value\": _vm.article.resource.access,\n                        \"instance-id\": _vm.resourceId,\n                        \"shared-by-count\": _vm.article.sharedbycount,\n                        \"like-button-aria-label\": _vm.likeButtonLabel,\n                        liked: _vm.article.reacted,\n                        \"component-name\": \"engage_article\"\n                      },\n                      on: {\n                        \"access-update\": _vm.updateAccess,\n                        \"access-modal\": function($event) {\n                          _vm.openModalFromButtonLabel = true\n                        },\n                        \"update-like-status\": _vm.updateLikeStatus\n                      }\n                    }),\n                    _vm._v(\" \"),\n                    _c(\"ArticlePlaylistBox\", {\n                      staticClass: \"tui-articleSidePanel__playlistBox\",\n                      attrs: { \"resource-id\": _vm.resourceId }\n                    })\n                  ]\n                },\n                proxy: true\n              },\n              {\n                key: \"comments\",\n                fn: function() {\n                  return [\n                    _c(\"SidePanelCommentBox\", {\n                      attrs: {\n                        component: \"engage_article\",\n                        area: \"comment\",\n                        \"instance-id\": _vm.resourceId\n                      }\n                    })\n                  ]\n                },\n                proxy: true\n              },\n              _vm.featureRecommenders\n                ? {\n                    key: \"related\",\n                    fn: function() {\n                      return [\n                        _c(\"Related\", {\n                          attrs: {\n                            component: \"engage_article\",\n                            area: \"related\",\n                            \"resource-id\": _vm.resourceId\n                          }\n                        })\n                      ]\n                    },\n                    proxy: true\n                  }\n                : null\n            ],\n            null,\n            true\n          )\n        },\n        [\n          _c(\"MiniProfileCard\", {\n            attrs: {\n              slot: \"author-profile\",\n              display: _vm.user.card_display,\n              \"no-border\": true\n            },\n            slot: \"author-profile\",\n            scopedSlots: _vm._u(\n              [\n                {\n                  key: \"drop-down-items\",\n                  fn: function() {\n                    return [\n                      _vm.article.owned\n                        ? _c(\n                            \"DropdownItem\",\n                            {\n                              on: {\n                                click: function($event) {\n                                  _vm.openModalFromAction = true\n                                }\n                              }\n                            },\n                            [\n                              _vm._v(\n                                \"\\n        \" +\n                                  _vm._s(_vm.$str(\"delete\", \"moodle\")) +\n                                  \"\\n      \"\n                              )\n                            ]\n                          )\n                        : _c(\n                            \"DropdownItem\",\n                            { on: { click: _vm.reportResource } },\n                            [\n                              _vm._v(\n                                \"\\n        \" +\n                                  _vm._s(\n                                    _vm.$str(\"reportresource\", \"engage_article\")\n                                  ) +\n                                  \"\\n      \"\n                              )\n                            ]\n                          )\n                    ]\n                  },\n                  proxy: true\n                }\n              ],\n              null,\n              false,\n              3333225133\n            )\n          })\n        ],\n        1\n      )\n    : _vm._e()\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=template&id=44b6de2c&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=template&id=44b6de2c& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-articleRelated\" },\n    _vm._l(_vm.articles, function(ref) {\n      var bookmarked = ref.bookmarked\n      var instanceid = ref.instanceid\n      var image = ref.image\n      var name = ref.name\n      var reactions = ref.reactions\n      var timeview = ref.timeview\n      var url = ref.url\n      return _c(\n        \"article\",\n        { key: instanceid },\n        [\n          _c(\"RelatedCard\", {\n            attrs: {\n              \"resource-id\": instanceid,\n              bookmarked: bookmarked,\n              image: image,\n              name: name,\n              reactions: reactions,\n              timeview: timeview,\n              url: url\n            },\n            on: { update: _vm.update }\n          })\n        ],\n        1\n      )\n    }),\n    0\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=template&id=2c100fac&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=template&id=2c100fac& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-articlePlaylistBox\" },\n    [\n      _vm.show\n        ? [\n            _c(\n              \"p\",\n              { staticClass: \"tui-articlePlaylistBox__label\" },\n              [\n                _c(\"span\", [\n                  _vm._v(\n                    \"\\n        \" +\n                      _vm._s(_vm.$str(\"appears_in\", \"engage_article\")) +\n                      \"\\n      \"\n                  )\n                ]),\n                _vm._v(\" \"),\n                _vm.loading ? _c(\"Loading\") : _vm._e()\n              ],\n              1\n            ),\n            _vm._v(\" \"),\n            _c(\"ResourcePlaylistBox\", {\n              staticClass: \"tui-articlePlaylistBox__playlistsBox\",\n              attrs: {\n                \"resource-id\": _vm.resourceId,\n                \"url-source\": _vm.urlSource\n              },\n              on: {\n                \"update-has-playlists\": function($event) {\n                  _vm.show = $event\n                },\n                \"load-records\": function($event) {\n                  _vm.loading = $event\n                }\n              }\n            })\n          ]\n        : _vm._e()\n    ],\n    2\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=template&id=34cec6b4&":
/*!***************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=template&id=34cec6b4& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"Layout\", {\n    staticClass: \"tui-articleView\",\n    scopedSlots: _vm._u(\n      [\n        _vm.backButton || _vm.navigationButtons\n          ? {\n              key: \"header\",\n              fn: function() {\n                return [\n                  _c(\"ResourceNavigationBar\", {\n                    attrs: {\n                      \"back-button\": _vm.backButton,\n                      \"navigation-buttons\": _vm.navigationButtons\n                    }\n                  })\n                ]\n              },\n              proxy: true\n            }\n          : null,\n        {\n          key: \"column\",\n          fn: function() {\n            return [\n              _c(\"Loader\", {\n                attrs: { loading: _vm.$apollo.loading, fullpage: true }\n              }),\n              _vm._v(\" \"),\n              !_vm.$apollo.loading\n                ? _c(\n                    \"div\",\n                    { staticClass: \"tui-articleView__layout\" },\n                    [\n                      _c(\"ArticleTitle\", {\n                        attrs: {\n                          title: _vm.articleName,\n                          \"resource-id\": _vm.resourceId,\n                          owned: _vm.article.owned,\n                          bookmarked: _vm.bookmarked,\n                          \"update-able\": _vm.article.updateable\n                        },\n                        on: { bookmark: _vm.updateBookmark }\n                      }),\n                      _vm._v(\" \"),\n                      _c(\"ArticleContent\", {\n                        attrs: {\n                          \"update-able\": _vm.article.updateable,\n                          content: _vm.article.content,\n                          \"resource-id\": _vm.resourceId\n                        }\n                      })\n                    ],\n                    1\n                  )\n                : _vm._e()\n            ]\n          },\n          proxy: true\n        },\n        {\n          key: \"sidepanel\",\n          fn: function() {\n            return [\n              _c(\"ArticleSidePanel\", {\n                attrs: { \"resource-id\": _vm.resourceId }\n              })\n            ]\n          },\n          proxy: true\n        }\n      ],\n      null,\n      true\n    )\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./server/lib/webapi/ajax/file_unused_draft_item_id.graphql":
/*!******************************************************************!*\
  !*** ./server/lib/webapi/ajax/file_unused_draft_item_id.graphql ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"core_file_unused_draft_item_id\"},\"variableDefinitions\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"item_id\"},\"name\":{\"kind\":\"Name\",\"value\":\"core_file_unused_draft_item_id\"},\"arguments\":[],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/lib/webapi/ajax/file_unused_draft_item_id.graphql?");

/***/ }),

/***/ "./server/ml/recommender/webapi/ajax/get_recommended_articles.graphql":
/*!****************************************************************************!*\
  !*** ./server/ml/recommender/webapi/ajax/get_recommended_articles.graphql ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"ml_recommender_get_recommended_articles\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"article_id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"cursor\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_text\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"source\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"String\"}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"cursor\"},\"name\":{\"kind\":\"Name\",\"value\":\"ml_recommender_recommended_articles_cursor\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"article_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"article_id\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"cursor\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"cursor\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"total\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"next\"},\"arguments\":[],\"directives\":[]}]}},{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"articles\"},\"name\":{\"kind\":\"Name\",\"value\":\"ml_recommender_recommended_articles\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"article_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"article_id\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"cursor\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"cursor\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"summary\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"tuicomponent\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"comments\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"reactions\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timecreated\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"sharedbycount\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"extra\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"owned\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimageurl\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimagealt\"},\"arguments\":[],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"url\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"source\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"source\"}}}],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/ml/recommender/webapi/ajax/get_recommended_articles.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/article/webapi/ajax/create_article.graphql":
/*!***********************************************************************************!*\
  !*** ./server/totara/engage/resources/article/webapi/ajax/create_article.graphql ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_create_article\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"String\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_text\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_access\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_timeview\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_recipient_in\"}}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"draft_id\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"article\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_create\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"draft_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"draft_id\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"resource\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"time\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimagealt\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimageurl\"},\"arguments\":[],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"HTML\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"image\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"owned\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"sharedbycount\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"reacted\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/article/webapi/ajax/create_article.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/article/webapi/ajax/delete_article.graphql":
/*!***********************************************************************************!*\
  !*** ./server/totara/engage/resources/article/webapi/ajax/delete_article.graphql ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_delete_article\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"result\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_delete\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}}}],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/article/webapi/ajax/delete_article.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/article/webapi/ajax/draft_item.graphql":
/*!*******************************************************************************!*\
  !*** ./server/totara/engage/resources/article/webapi/ajax/draft_item.graphql ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_draft_item\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"draft\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_draft_item\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"file_item_id\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/article/webapi/ajax/draft_item.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/article/webapi/ajax/get_article.graphql":
/*!********************************************************************************!*\
  !*** ./server/totara/engage/resources/article/webapi/ajax/get_article.graphql ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_get_article\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"article\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_get_article\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"resource\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"time\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"card_display\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_picture_alt\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_picture_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"display_fields\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"label\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"associate_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"is_custom\"},\"arguments\":[],\"directives\":[]}]}}]}}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"HTML\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"image\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"updateable\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"owned\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timedescription\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"sharedbycount\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"reacted\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/article/webapi/ajax/get_article.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/article/webapi/ajax/update_article.graphql":
/*!***********************************************************************************!*\
  !*** ./server/totara/engage/resources/article/webapi/ajax/update_article.graphql ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_update_article\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_text\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"String\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_access\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_timeview\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_recipient_in\"}}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"draft_id\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"article\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_update\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"draft_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"draft_id\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"resource\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"time\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"card_display\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_picture_alt\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_picture_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"display_fields\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"label\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"associate_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"is_custom\"},\"arguments\":[],\"directives\":[]}]}}]}}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"HTML\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"image\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"updateable\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"owned\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timedescription\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"sharedbycount\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"reacted\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/article/webapi/ajax/update_article.graphql?");

/***/ }),

/***/ "./server/totara/engage/webapi/ajax/advanced_features.graphql":
/*!********************************************************************!*\
  !*** ./server/totara/engage/webapi/ajax/advanced_features.graphql ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_advanced_features\"},\"variableDefinitions\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"features\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_advanced_features\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"library\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"recommenders\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"workspaces\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/webapi/ajax/advanced_features.graphql?");

/***/ }),

/***/ "./server/totara/engage/webapi/ajax/update_bookmark.graphql":
/*!******************************************************************!*\
  !*** ./server/totara/engage/webapi/ajax/update_bookmark.graphql ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_update_bookmark\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"itemid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_component\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_boolean\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"result\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_update_bookmark\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"itemid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"itemid\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"}}}],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/webapi/ajax/update_bookmark.graphql?");

/***/ }),

/***/ "./server/totara/reportedcontent/webapi/ajax/create_review.graphql":
/*!*************************************************************************!*\
  !*** ./server/totara/reportedcontent/webapi/ajax/create_review.graphql ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_reportedcontent_create_review\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_component\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_area\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"item_id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"url\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_url\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"review\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_reportedcontent_create_review\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"item_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"item_id\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"url\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"url\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"success\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/reportedcontent/webapi/ajax/create_review.graphql?");

/***/ }),

/***/ "editor_weka/components/Weka":
/*!***************************************************************!*\
  !*** external "tui.require(\"editor_weka/components/Weka\")" ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"editor_weka/components/Weka\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22editor_weka/components/Weka\\%22)%22?");

/***/ }),

/***/ "engage_article/components/card/RelatedCard":
/*!******************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/card/RelatedCard\")" ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/card/RelatedCard\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/card/RelatedCard\\%22)%22?");

/***/ }),

/***/ "engage_article/components/content/ArticleContent":
/*!************************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/content/ArticleContent\")" ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/content/ArticleContent\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/content/ArticleContent\\%22)%22?");

/***/ }),

/***/ "engage_article/components/content/ArticleTitle":
/*!**********************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/content/ArticleTitle\")" ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/content/ArticleTitle\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/content/ArticleTitle\\%22)%22?");

/***/ }),

/***/ "engage_article/components/form/ArticleForm":
/*!******************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/form/ArticleForm\")" ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/form/ArticleForm\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/form/ArticleForm\\%22)%22?");

/***/ }),

/***/ "engage_article/components/form/EditArticleContentForm":
/*!*****************************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/form/EditArticleContentForm\")" ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/form/EditArticleContentForm\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/form/EditArticleContentForm\\%22)%22?");

/***/ }),

/***/ "engage_article/components/form/EditArticleTitleForm":
/*!***************************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/form/EditArticleTitleForm\")" ***!
  \***************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/form/EditArticleTitleForm\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/form/EditArticleTitleForm\\%22)%22?");

/***/ }),

/***/ "engage_article/components/separator/ArticleSeparator":
/*!****************************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/separator/ArticleSeparator\")" ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/separator/ArticleSeparator\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/separator/ArticleSeparator\\%22)%22?");

/***/ }),

/***/ "engage_article/components/sidepanel/ArticleSidePanel":
/*!****************************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/sidepanel/ArticleSidePanel\")" ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/sidepanel/ArticleSidePanel\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/sidepanel/ArticleSidePanel\\%22)%22?");

/***/ }),

/***/ "engage_article/components/sidepanel/Related":
/*!*******************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/sidepanel/Related\")" ***!
  \*******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/sidepanel/Related\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/sidepanel/Related\\%22)%22?");

/***/ }),

/***/ "engage_article/components/sidepanel/content/ArticlePlaylistBox":
/*!**************************************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/sidepanel/content/ArticlePlaylistBox\")" ***!
  \**************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/sidepanel/content/ArticlePlaylistBox\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/sidepanel/content/ArticlePlaylistBox\\%22)%22?");

/***/ }),

/***/ "totara_comment/components/box/SidePanelCommentBox":
/*!*************************************************************************************!*\
  !*** external "tui.require(\"totara_comment/components/box/SidePanelCommentBox\")" ***!
  \*************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_comment/components/box/SidePanelCommentBox\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_comment/components/box/SidePanelCommentBox\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/buttons/BookmarkButton":
/*!***********************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/buttons/BookmarkButton\")" ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/buttons/BookmarkButton\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/buttons/BookmarkButton\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/buttons/DoneCancelGroup":
/*!************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/buttons/DoneCancelGroup\")" ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/buttons/DoneCancelGroup\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/buttons/DoneCancelGroup\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/card/BaseCard":
/*!**************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/card/BaseCard\")" ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/card/BaseCard\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/card/BaseCard\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/card/CardHeader":
/*!****************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/card/CardHeader\")" ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/card/CardHeader\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/card/CardHeader\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/card/ImageHeader":
/*!*****************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/card/ImageHeader\")" ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/card/ImageHeader\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/card/ImageHeader\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/form/AccessForm":
/*!****************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/form/AccessForm\")" ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/form/AccessForm\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/form/AccessForm\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/form/InlineEditing":
/*!*******************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/form/InlineEditing\")" ***!
  \*******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/form/InlineEditing\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/form/InlineEditing\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/header/ResourceNavigationBar":
/*!*****************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/header/ResourceNavigationBar\")" ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/header/ResourceNavigationBar\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/header/ResourceNavigationBar\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/icons/StatIcon":
/*!***************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/icons/StatIcon\")" ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/icons/StatIcon\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/icons/StatIcon\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/icons/access/computed/AccessIcon":
/*!*********************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/icons/access/computed/AccessIcon\")" ***!
  \*********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/icons/access/computed/AccessIcon\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/icons/access/computed/AccessIcon\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/modal/EngageWarningModal":
/*!*************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/modal/EngageWarningModal\")" ***!
  \*************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/modal/EngageWarningModal\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/modal/EngageWarningModal\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/sidepanel/EngageSidePanel":
/*!**************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/sidepanel/EngageSidePanel\")" ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/sidepanel/EngageSidePanel\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/sidepanel/EngageSidePanel\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/sidepanel/access/AccessDisplay":
/*!*******************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/sidepanel/access/AccessDisplay\")" ***!
  \*******************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/sidepanel/access/AccessDisplay\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/sidepanel/access/AccessDisplay\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/sidepanel/access/AccessSetting":
/*!*******************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/sidepanel/access/AccessSetting\")" ***!
  \*******************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/sidepanel/access/AccessSetting\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/sidepanel/access/AccessSetting\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/sidepanel/media/MediaSetting":
/*!*****************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/sidepanel/media/MediaSetting\")" ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/sidepanel/media/MediaSetting\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/sidepanel/media/MediaSetting\\%22)%22?");

/***/ }),

/***/ "totara_engage/index":
/*!*******************************************************!*\
  !*** external "tui.require(\"totara_engage/index\")" ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/index\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/index\\%22)%22?");

/***/ }),

/***/ "totara_engage/mixins/container_mixin":
/*!************************************************************************!*\
  !*** external "tui.require(\"totara_engage/mixins/container_mixin\")" ***!
  \************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/mixins/container_mixin\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/mixins/container_mixin\\%22)%22?");

/***/ }),

/***/ "totara_playlist/components/box/ResourcePlaylistBox":
/*!**************************************************************************************!*\
  !*** external "tui.require(\"totara_playlist/components/box/ResourcePlaylistBox\")" ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_playlist/components/box/ResourcePlaylistBox\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_playlist/components/box/ResourcePlaylistBox\\%22)%22?");

/***/ }),

/***/ "tui/apollo_client":
/*!*****************************************************!*\
  !*** external "tui.require(\"tui/apollo_client\")" ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/apollo_client\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/apollo_client\\%22)%22?");

/***/ }),

/***/ "tui/components/buttons/Button":
/*!*****************************************************************!*\
  !*** external "tui.require(\"tui/components/buttons/Button\")" ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/buttons/Button\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/buttons/Button\\%22)%22?");

/***/ }),

/***/ "tui/components/buttons/ButtonGroup":
/*!**********************************************************************!*\
  !*** external "tui.require(\"tui/components/buttons/ButtonGroup\")" ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/buttons/ButtonGroup\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/buttons/ButtonGroup\\%22)%22?");

/***/ }),

/***/ "tui/components/buttons/ButtonIcon":
/*!*********************************************************************!*\
  !*** external "tui.require(\"tui/components/buttons/ButtonIcon\")" ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/buttons/ButtonIcon\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/buttons/ButtonIcon\\%22)%22?");

/***/ }),

/***/ "tui/components/buttons/Cancel":
/*!*****************************************************************!*\
  !*** external "tui.require(\"tui/components/buttons/Cancel\")" ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/buttons/Cancel\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/buttons/Cancel\\%22)%22?");

/***/ }),

/***/ "tui/components/card/Card":
/*!************************************************************!*\
  !*** external "tui.require(\"tui/components/card/Card\")" ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/card/Card\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/card/Card\\%22)%22?");

/***/ }),

/***/ "tui/components/dropdown/DropdownItem":
/*!************************************************************************!*\
  !*** external "tui.require(\"tui/components/dropdown/DropdownItem\")" ***!
  \************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/dropdown/DropdownItem\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/dropdown/DropdownItem\\%22)%22?");

/***/ }),

/***/ "tui/components/form/Form":
/*!************************************************************!*\
  !*** external "tui.require(\"tui/components/form/Form\")" ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/Form\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/Form\\%22)%22?");

/***/ }),

/***/ "tui/components/form/FormRow":
/*!***************************************************************!*\
  !*** external "tui.require(\"tui/components/form/FormRow\")" ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/FormRow\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/FormRow\\%22)%22?");

/***/ }),

/***/ "tui/components/form/InputText":
/*!*****************************************************************!*\
  !*** external "tui.require(\"tui/components/form/InputText\")" ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/InputText\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/InputText\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/AddToList":
/*!******************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/AddToList\")" ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/AddToList\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/AddToList\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Comment":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Comment\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Comment\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Comment\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Info":
/*!*************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Info\")" ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Info\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Info\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Like":
/*!*************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Like\")" ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Like\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Like\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Loading":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Loading\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Loading\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Loading\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/More":
/*!*************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/More\")" ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/More\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/More\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Share":
/*!**************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Share\")" ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Share\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Share\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Time":
/*!*************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Time\")" ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Time\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Time\\%22)%22?");

/***/ }),

/***/ "tui/components/layouts/LayoutOneColumnContentWithSidePanel":
/*!**********************************************************************************************!*\
  !*** external "tui.require(\"tui/components/layouts/LayoutOneColumnContentWithSidePanel\")" ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/layouts/LayoutOneColumnContentWithSidePanel\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/layouts/LayoutOneColumnContentWithSidePanel\\%22)%22?");

/***/ }),

/***/ "tui/components/loader/Loader":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/loader/Loader\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/loader/Loader\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/loader/Loader\\%22)%22?");

/***/ }),

/***/ "tui/components/modal/ModalPresenter":
/*!***********************************************************************!*\
  !*** external "tui.require(\"tui/components/modal/ModalPresenter\")" ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/modal/ModalPresenter\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/modal/ModalPresenter\\%22)%22?");

/***/ }),

/***/ "tui/components/popover/Popover":
/*!******************************************************************!*\
  !*** external "tui.require(\"tui/components/popover/Popover\")" ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/popover/Popover\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/popover/Popover\\%22)%22?");

/***/ }),

/***/ "tui/components/profile/MiniProfileCard":
/*!**************************************************************************!*\
  !*** external "tui.require(\"tui/components/profile/MiniProfileCard\")" ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/profile/MiniProfileCard\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/profile/MiniProfileCard\\%22)%22?");

/***/ }),

/***/ "tui/notifications":
/*!*****************************************************!*\
  !*** external "tui.require(\"tui/notifications\")" ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/notifications\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/notifications\\%22)%22?");

/***/ }),

/***/ "tui/tui":
/*!*******************************************!*\
  !*** external "tui.require(\"tui/tui\")" ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/tui\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/tui\\%22)%22?");

/***/ }),

/***/ "tui/util":
/*!********************************************!*\
  !*** external "tui.require(\"tui/util\")" ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/util\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/util\\%22)%22?");

/***/ })

/******/ });