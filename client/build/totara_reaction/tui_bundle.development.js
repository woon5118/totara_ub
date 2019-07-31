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
/******/ 		"totara_reaction/tui_bundle.development": 0
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
/******/ 	deferredModules.push(["./client/src/totara_reaction/tui.json","tui/vendors.development"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/src/totara_reaction/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!**********************************************************************************************!*\
  !*** ./client/src/totara_reaction/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./SidePanelLike\": \"./client/src/totara_reaction/components/SidePanelLike.vue\",\n\t\"./SidePanelLike.vue\": \"./client/src/totara_reaction/components/SidePanelLike.vue\",\n\t\"./SimpleLike\": \"./client/src/totara_reaction/components/SimpleLike.vue\",\n\t\"./SimpleLike.vue\": \"./client/src/totara_reaction/components/SimpleLike.vue\",\n\t\"./buttons/LikeButtonIcon\": \"./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue\",\n\t\"./buttons/LikeButtonIcon.vue\": \"./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue\",\n\t\"./modal/LikeRecordsModal\": \"./client/src/totara_reaction/components/modal/LikeRecordsModal.vue\",\n\t\"./modal/LikeRecordsModal.vue\": \"./client/src/totara_reaction/components/modal/LikeRecordsModal.vue\",\n\t\"./popover_content/LikeRecordsList\": \"./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue\",\n\t\"./popover_content/LikeRecordsList.vue\": \"./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/src/totara_reaction/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/src/totara_reaction/components_sync_^(?:(?");

/***/ }),

/***/ "./client/src/totara_reaction/components/SidePanelLike.vue":
/*!*****************************************************************!*\
  !*** ./client/src/totara_reaction/components/SidePanelLike.vue ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SidePanelLike_vue_vue_type_template_id_698b910c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SidePanelLike.vue?vue&type=template&id=698b910c& */ \"./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=template&id=698b910c&\");\n/* harmony import */ var _SidePanelLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SidePanelLike.vue?vue&type=script&lang=js& */ \"./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SidePanelLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SidePanelLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SidePanelLike_vue_vue_type_template_id_698b910c___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SidePanelLike_vue_vue_type_template_id_698b910c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SidePanelLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SidePanelLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/totara_reaction/components/SidePanelLike.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SidePanelLike.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SidePanelLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_lang_strings_loader.js!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SidePanelLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SidePanelLike.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=script&lang=js&":
/*!******************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SidePanelLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SidePanelLike.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SidePanelLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SidePanelLike.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=template&id=698b910c&":
/*!************************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=template&id=698b910c& ***!
  \************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SidePanelLike_vue_vue_type_template_id_698b910c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SidePanelLike.vue?vue&type=template&id=698b910c& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=template&id=698b910c&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SidePanelLike_vue_vue_type_template_id_698b910c___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SidePanelLike_vue_vue_type_template_id_698b910c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SidePanelLike.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/SimpleLike.vue":
/*!**************************************************************!*\
  !*** ./client/src/totara_reaction/components/SimpleLike.vue ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SimpleLike_vue_vue_type_template_id_2e39d483___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SimpleLike.vue?vue&type=template&id=2e39d483& */ \"./client/src/totara_reaction/components/SimpleLike.vue?vue&type=template&id=2e39d483&\");\n/* harmony import */ var _SimpleLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SimpleLike.vue?vue&type=script&lang=js& */ \"./client/src/totara_reaction/components/SimpleLike.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SimpleLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/totara_reaction/components/SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SimpleLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SimpleLike_vue_vue_type_template_id_2e39d483___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SimpleLike_vue_vue_type_template_id_2e39d483___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SimpleLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SimpleLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/totara_reaction/components/SimpleLike.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SimpleLike.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_lang_strings_loader.js!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SimpleLike.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/SimpleLike.vue?vue&type=script&lang=js&":
/*!***************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/SimpleLike.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SimpleLike.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/SimpleLike.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SimpleLike.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/SimpleLike.vue?vue&type=template&id=2e39d483&":
/*!*********************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/SimpleLike.vue?vue&type=template&id=2e39d483& ***!
  \*********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_template_id_2e39d483___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SimpleLike.vue?vue&type=template&id=2e39d483& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/SimpleLike.vue?vue&type=template&id=2e39d483&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_template_id_2e39d483___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_template_id_2e39d483___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SimpleLike.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue":
/*!**************************************************************************!*\
  !*** ./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _LikeButtonIcon_vue_vue_type_template_id_5958f8ac___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LikeButtonIcon.vue?vue&type=template&id=5958f8ac& */ \"./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?vue&type=template&id=5958f8ac&\");\n/* harmony import */ var _LikeButtonIcon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LikeButtonIcon.vue?vue&type=script&lang=js& */ \"./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _LikeButtonIcon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _LikeButtonIcon_vue_vue_type_template_id_5958f8ac___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _LikeButtonIcon_vue_vue_type_template_id_5958f8ac___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/totara_reaction/components/buttons/LikeButtonIcon.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeButtonIcon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeButtonIcon.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeButtonIcon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?vue&type=template&id=5958f8ac&":
/*!*********************************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?vue&type=template&id=5958f8ac& ***!
  \*********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeButtonIcon_vue_vue_type_template_id_5958f8ac___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeButtonIcon.vue?vue&type=template&id=5958f8ac& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?vue&type=template&id=5958f8ac&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeButtonIcon_vue_vue_type_template_id_5958f8ac___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeButtonIcon_vue_vue_type_template_id_5958f8ac___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/modal/LikeRecordsModal.vue":
/*!**************************************************************************!*\
  !*** ./client/src/totara_reaction/components/modal/LikeRecordsModal.vue ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _LikeRecordsModal_vue_vue_type_template_id_17becd7e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LikeRecordsModal.vue?vue&type=template&id=17becd7e& */ \"./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=template&id=17becd7e&\");\n/* harmony import */ var _LikeRecordsModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LikeRecordsModal.vue?vue&type=script&lang=js& */ \"./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _LikeRecordsModal_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _LikeRecordsModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _LikeRecordsModal_vue_vue_type_template_id_17becd7e___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _LikeRecordsModal_vue_vue_type_template_id_17becd7e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _LikeRecordsModal_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_LikeRecordsModal_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/totara_reaction/components/modal/LikeRecordsModal.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeRecordsModal.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=template&id=17becd7e&":
/*!*********************************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=template&id=17becd7e& ***!
  \*********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_template_id_17becd7e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeRecordsModal.vue?vue&type=template&id=17becd7e& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=template&id=17becd7e&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_template_id_17becd7e___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_template_id_17becd7e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue":
/*!***********************************************************************************!*\
  !*** ./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _LikeRecordsList_vue_vue_type_template_id_7a282a0f___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LikeRecordsList.vue?vue&type=template&id=7a282a0f& */ \"./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=template&id=7a282a0f&\");\n/* harmony import */ var _LikeRecordsList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LikeRecordsList.vue?vue&type=script&lang=js& */ \"./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _LikeRecordsList_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _LikeRecordsList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _LikeRecordsList_vue_vue_type_template_id_7a282a0f___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _LikeRecordsList_vue_vue_type_template_id_7a282a0f___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _LikeRecordsList_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_LikeRecordsList_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/totara_reaction/components/popover_content/LikeRecordsList.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**********************************************************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**********************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeRecordsList.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=template&id=7a282a0f&":
/*!******************************************************************************************************************!*\
  !*** ./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=template&id=7a282a0f& ***!
  \******************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_template_id_7a282a0f___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeRecordsList.vue?vue&type=template&id=7a282a0f& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=template&id=7a282a0f&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_template_id_7a282a0f___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_template_id_7a282a0f___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?");

/***/ }),

/***/ "./client/src/totara_reaction/tui.json":
/*!*********************************************!*\
  !*** ./client/src/totara_reaction/tui.json ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"totara_reaction\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"totara_reaction\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"totara_reaction\")\ntui._bundle.addModulesFromContext(\"totara_reaction/components\", __webpack_require__(\"./client/src/totara_reaction/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/src/totara_reaction/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*****************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*****************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_reaction\": [\n    \"error:create_like\",\n    \"error:remove_like\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SidePanelLike.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_reaction\": [\n    \"bracketcount\",\n    \"error:create_like\",\n    \"error:remove_like\",\n    \"like\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SimpleLike.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_reaction\": [\n    \"likesx\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_reaction\": [\n    \"nolikes\",\n    \"numberofmore\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_buttons_LabelledButtonTrigger__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/buttons/LabelledButtonTrigger */ \"tui/components/buttons/LabelledButtonTrigger\");\n/* harmony import */ var tui_components_buttons_LabelledButtonTrigger__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_LabelledButtonTrigger__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_reaction/components/popover_content/LikeRecordsList */ \"totara_reaction/components/popover_content/LikeRecordsList\");\n/* harmony import */ var totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! totara_reaction/components/modal/LikeRecordsModal */ \"totara_reaction/components/modal/LikeRecordsModal\");\n/* harmony import */ var totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_icons_common_Like__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/icons/common/Like */ \"tui/components/icons/common/Like\");\n/* harmony import */ var tui_components_icons_common_Like__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_common_Like__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_icons_common_LikeActive__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/icons/common/LikeActive */ \"tui/components/icons/common/LikeActive\");\n/* harmony import */ var tui_components_icons_common_LikeActive__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_common_LikeActive__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var totara_engage_components_icons_Loading__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! totara_engage/components/icons/Loading */ \"totara_engage/components/icons/Loading\");\n/* harmony import */ var totara_engage_components_icons_Loading__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_icons_Loading__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/components/modal/ModalPresenter */ \"tui/components/modal/ModalPresenter\");\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_reaction/graphql/get_likes */ \"./server/totara/reaction/webapi/ajax/get_likes.graphql\");\n/* harmony import */ var totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! totara_reaction/graphql/liked */ \"./server/totara/reaction/webapi/ajax/liked.graphql\");\n/* harmony import */ var totara_reaction_graphql_create_like__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! totara_reaction/graphql/create_like */ \"./server/totara/reaction/webapi/ajax/create_like.graphql\");\n/* harmony import */ var totara_reaction_graphql_remove_like__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! totara_reaction/graphql/remove_like */ \"./server/totara/reaction/webapi/ajax/remove_like.graphql\");\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! tui/notifications */ \"tui/notifications\");\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(tui_notifications__WEBPACK_IMPORTED_MODULE_11__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n// GraphQL queries\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ModalPresenter: (tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_6___default()),\n    ButtonIconWithLabel: (tui_components_buttons_LabelledButtonTrigger__WEBPACK_IMPORTED_MODULE_0___default()),\n    LikeRecordsList: (totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_1___default()),\n    LikeRecordsModal: (totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_2___default()),\n    Like: (tui_components_icons_common_Like__WEBPACK_IMPORTED_MODULE_3___default()),\n    LikeActive: (tui_components_icons_common_LikeActive__WEBPACK_IMPORTED_MODULE_4___default()),\n    Loading: (totara_engage_components_icons_Loading__WEBPACK_IMPORTED_MODULE_5___default()),\n  },\n\n  props: {\n    component: {\n      type: String,\n      required: true,\n    },\n\n    area: {\n      type: String,\n      required: true,\n    },\n\n    instanceId: {\n      type: [String, Number],\n      required: true,\n    },\n\n    liked: {\n      type: Boolean,\n      default: null,\n    },\n\n    totalLikes: {\n      type: Boolean,\n      default: null,\n    },\n\n    iconSize: [String, Number],\n\n    buttonAriaLabel: {\n      type: String,\n      required: true,\n    },\n\n    disabled: Boolean,\n  },\n\n  apollo: {\n    count: {\n      query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n      variables() {\n        return {\n          component: this.component,\n          area: this.area,\n          instanceid: this.instanceId,\n        };\n      },\n\n      /**\n       *\n       * @param {Number} count\n       * @return {Number}\n       */\n      update({ count }) {\n        return count;\n      },\n\n      skip() {\n        // Do not load from server if the property's value is provided.\n        return (\n          'undefined' !== typeof this.totalLikes && null !== this.totalLikes\n        );\n      },\n    },\n\n    hasLiked: {\n      query: totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_8__[\"default\"],\n      variables() {\n        return {\n          component: this.component,\n          area: this.area,\n          instanceid: this.instanceId,\n        };\n      },\n\n      /**\n       *\n       * @param {Boolean} result\n       * @return {Boolean}\n       */\n      update({ result }) {\n        return result;\n      },\n\n      skip() {\n        // Do not load from server when the property's value is provided.\n        return 'undefined' !== typeof this.liked && null !== this.liked;\n      },\n    },\n  },\n\n  data() {\n    return {\n      hasLiked: this.liked,\n      count: this.totalLikes,\n      showPopover: false,\n      showModal: false,\n      submitting: false,\n    };\n  },\n\n  watch: {\n    /**\n     *\n     * @param {Boolean} value\n     */\n    liked(value) {\n      if (value === this.hasLiked) {\n        return;\n      }\n\n      this.hasLiked = value;\n    },\n\n    /**\n     *\n     * @param {Number} value\n     */\n    totalLikes(value) {\n      if (value == this.count) {\n        return;\n      }\n\n      this.count = value;\n    },\n  },\n\n  methods: {\n    async like() {\n      if (this.hasLiked) {\n        await this.removeLike();\n      } else {\n        await this.createLike();\n      }\n    },\n\n    async createLike() {\n      if (this.submitting) {\n        return;\n      }\n\n      this.submitting = true;\n      let variables = {\n        component: this.component,\n        area: this.area,\n        instanceid: this.instanceId,\n      };\n\n      try {\n        await this.$apollo.mutate({\n          mutation: totara_reaction_graphql_create_like__WEBPACK_IMPORTED_MODULE_9__[\"default\"],\n          variables: variables,\n          refetchAll: false,\n          refetchQueries: [\n            {\n              query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n              variables: variables,\n            },\n            {\n              query: totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_8__[\"default\"],\n              variables: variables,\n            },\n          ],\n        });\n\n        this.$emit('created-like');\n      } catch (e) {\n        await Object(tui_notifications__WEBPACK_IMPORTED_MODULE_11__[\"notify\"])({\n          message: this.$str('error:create_like', 'totara_reaction'),\n          type: 'error',\n          duration: 5000,\n        });\n      } finally {\n        this.submitting = false;\n      }\n    },\n\n    async removeLike() {\n      if (this.submitting) {\n        return;\n      }\n\n      this.submitting = true;\n      let variables = {\n        component: this.component,\n        area: this.area,\n        instanceid: this.instanceId,\n      };\n\n      try {\n        await this.$apollo.mutate({\n          mutation: totara_reaction_graphql_remove_like__WEBPACK_IMPORTED_MODULE_10__[\"default\"],\n          variables: variables,\n          refetchAll: false,\n          refetchQueries: [\n            {\n              query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n              variables: variables,\n            },\n            {\n              query: totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_8__[\"default\"],\n              variables: variables,\n            },\n          ],\n        });\n\n        this.$emit('removed-like');\n      } catch (e) {\n        await Object(tui_notifications__WEBPACK_IMPORTED_MODULE_11__[\"notify\"])({\n          message: this.$str('error:remove_like', 'totara_reaction'),\n          type: 'error',\n          duration: 5000,\n        });\n      } finally {\n        this.submitting = false;\n      }\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SidePanelLike.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/SimpleLike.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/SimpleLike.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_reaction/components/modal/LikeRecordsModal */ \"totara_reaction/components/modal/LikeRecordsModal\");\n/* harmony import */ var totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/modal/ModalPresenter */ \"tui/components/modal/ModalPresenter\");\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/popover/Popover */ \"tui/components/popover/Popover\");\n/* harmony import */ var tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var totara_reaction_components_buttons_LikeButtonIcon__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_reaction/components/buttons/LikeButtonIcon */ \"totara_reaction/components/buttons/LikeButtonIcon\");\n/* harmony import */ var totara_reaction_components_buttons_LikeButtonIcon__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_reaction_components_buttons_LikeButtonIcon__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_reaction/components/popover_content/LikeRecordsList */ \"totara_reaction/components/popover_content/LikeRecordsList\");\n/* harmony import */ var totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/notifications */ \"tui/notifications\");\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_notifications__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! totara_reaction/graphql/get_likes */ \"./server/totara/reaction/webapi/ajax/get_likes.graphql\");\n/* harmony import */ var totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_reaction/graphql/liked */ \"./server/totara/reaction/webapi/ajax/liked.graphql\");\n/* harmony import */ var totara_reaction_graphql_create_like__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! totara_reaction/graphql/create_like */ \"./server/totara/reaction/webapi/ajax/create_like.graphql\");\n/* harmony import */ var totara_reaction_graphql_remove_like__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! totara_reaction/graphql/remove_like */ \"./server/totara/reaction/webapi/ajax/remove_like.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n// GraphQL\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    LikeRecordsModal: (totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_0___default()),\n    ModalPresenter: (tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_1___default()),\n    Popover: (tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_2___default()),\n    LikeButtonIcon: (totara_reaction_components_buttons_LikeButtonIcon__WEBPACK_IMPORTED_MODULE_3___default()),\n    LikeRecordsList: (totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_4___default()),\n  },\n\n  props: {\n    component: {\n      type: String,\n      required: true,\n    },\n    area: {\n      type: String,\n      required: true,\n    },\n    instanceId: {\n      type: [String, Number],\n      required: true,\n    },\n    disabled: Boolean,\n    /**\n     * Passing this prop to tell whether user has liked record or not. So that this component\n     * will not try to fire a request to the server.\n     */\n    liked: {\n      type: Boolean,\n      default: null,\n    },\n    /**\n     * Passing this prop with a valid value to prevent firing request to the server.\n     * We cant use zero as default, because it might not trigger the query.\n     */\n    totalLikes: {\n      type: [String, Number],\n      default: null,\n    },\n\n    buttonAriaLabel: {\n      type: String,\n      required: true,\n    },\n\n    showText: Boolean,\n  },\n\n  apollo: {\n    count: {\n      query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n      variables() {\n        return {\n          component: this.component,\n          area: this.area,\n          instanceid: this.instanceId,\n        };\n      },\n\n      /**\n       *\n       * @param {Number|String} count\n       * @return {Number}\n       */\n      update({ count }) {\n        return parseInt(count, 9);\n      },\n\n      skip() {\n        // Only start fetching, when the data is not provided.\n        return (\n          'undefined' !== typeof this.totalLikes && null !== this.totalLikes\n        );\n      },\n    },\n\n    hasLiked: {\n      query: totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n      variables() {\n        return {\n          component: this.component,\n          area: this.area,\n          instanceid: this.instanceId,\n        };\n      },\n\n      /**\n       *\n       * @param {Boolean} result\n       * @return {Boolean}\n       */\n      update({ result }) {\n        return result;\n      },\n\n      skip() {\n        // Only start fetching when the data is not provided.\n        return 'undefined' !== typeof this.liked && null !== this.liked;\n      },\n\n      result({ data: { result } }) {\n        this.$emit('update-like-status', result);\n      },\n    },\n  },\n\n  data() {\n    return {\n      hasLiked: this.liked,\n      count: this.totalLikes,\n      showModal: false,\n      showPopover: false,\n      submitting: false,\n    };\n  },\n\n  computed: {\n    buttonText() {\n      if (!this.showText) {\n        return '';\n      }\n\n      return this.$str('like', 'totara_reaction');\n    },\n  },\n\n  watch: {\n    /**\n     *\n     * @param {Boolean} value\n     */\n    liked(value) {\n      if (value === this.hasLiked) {\n        return;\n      }\n\n      this.hasLiked = value;\n    },\n\n    /**\n     *\n     * @param {Number} value\n     */\n    totalLikes(value) {\n      if (value == this.count) {\n        return;\n      }\n\n      this.count = value;\n    },\n  },\n\n  methods: {\n    async like() {\n      if (this.hasLiked) {\n        await this.removeLike();\n      } else {\n        await this.createLike();\n      }\n    },\n\n    async createLike() {\n      if (this.submitting) {\n        return;\n      }\n\n      this.submitting = true;\n      let variables = {\n        component: this.component,\n        area: this.area,\n        instanceid: this.instanceId,\n      };\n\n      try {\n        await this.$apollo.mutate({\n          mutation: totara_reaction_graphql_create_like__WEBPACK_IMPORTED_MODULE_8__[\"default\"],\n          variables: variables,\n          refetchAll: false,\n          refetchQueries: [\n            {\n              query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n              variables: variables,\n            },\n            {\n              query: totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n              variables: variables,\n            },\n          ],\n        });\n\n        this.$emit('created-like');\n      } catch (e) {\n        await Object(tui_notifications__WEBPACK_IMPORTED_MODULE_5__[\"notify\"])({\n          message: this.$str('error:create_like', 'totara_reaction'),\n          type: 'error',\n          duration: 5000,\n        });\n      } finally {\n        this.submitting = false;\n      }\n    },\n\n    async removeLike() {\n      if (this.submitting) {\n        return;\n      }\n\n      this.submitting = true;\n      let variables = {\n        component: this.component,\n        area: this.area,\n        instanceid: this.instanceId,\n      };\n\n      try {\n        await this.$apollo.mutate({\n          mutation: totara_reaction_graphql_remove_like__WEBPACK_IMPORTED_MODULE_9__[\"default\"],\n          variables: variables,\n          refetchAll: false,\n          refetchQueries: [\n            {\n              query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n              variables: variables,\n            },\n            {\n              query: totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n              variables: variables,\n            },\n          ],\n        });\n\n        this.$emit('removed-like');\n      } catch (e) {\n        await Object(tui_notifications__WEBPACK_IMPORTED_MODULE_5__[\"notify\"])({\n          message: this.$str('error:remove_like', 'totara_reaction'),\n          type: 'error',\n          duration: 5000,\n        });\n      } finally {\n        this.submitting = false;\n      }\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SimpleLike.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/buttons/ButtonIcon */ \"tui/components/buttons/ButtonIcon\");\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_icons_common_Like__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/icons/common/Like */ \"tui/components/icons/common/Like\");\n/* harmony import */ var tui_components_icons_common_Like__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_common_Like__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_icons_common_LikeActive__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/icons/common/LikeActive */ \"tui/components/icons/common/LikeActive\");\n/* harmony import */ var tui_components_icons_common_LikeActive__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_common_LikeActive__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_icons_common_Spinner__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/icons/common/Spinner */ \"tui/components/icons/common/Spinner\");\n/* harmony import */ var tui_components_icons_common_Spinner__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_common_Spinner__WEBPACK_IMPORTED_MODULE_3__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ButtonIcon: (tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_0___default()),\n    LikeIcon: (tui_components_icons_common_Like__WEBPACK_IMPORTED_MODULE_1___default()),\n    LikedIcon: (tui_components_icons_common_LikeActive__WEBPACK_IMPORTED_MODULE_2___default()),\n    Loading: (tui_components_icons_common_Spinner__WEBPACK_IMPORTED_MODULE_3___default()),\n  },\n\n  props: {\n    transparent: {\n      type: Boolean,\n      default: true,\n    },\n\n    small: {\n      type: Boolean,\n      default: true,\n    },\n\n    transparentNoPadding: {\n      type: Boolean,\n      default: true,\n    },\n\n    submitting: Boolean,\n    liked: Boolean,\n    iconSize: [String, Number],\n\n    ariaLabel: {\n      type: String,\n      required: true,\n    },\n\n    disabled: Boolean,\n    text: String,\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_modal_Modal__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/modal/Modal */ \"tui/components/modal/Modal\");\n/* harmony import */ var tui_components_modal_Modal__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_Modal__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_modal_ModalContent__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/modal/ModalContent */ \"tui/components/modal/ModalContent\");\n/* harmony import */ var tui_components_modal_ModalContent__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_ModalContent__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/avatar/Avatar */ \"tui/components/avatar/Avatar\");\n/* harmony import */ var tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_icons_common_Spinner__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/icons/common/Spinner */ \"tui/components/icons/common/Spinner\");\n/* harmony import */ var tui_components_icons_common_Spinner__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_common_Spinner__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_reaction/graphql/get_likes */ \"./server/totara/reaction/webapi/ajax/get_likes.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n// GraphQL\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Modal: (tui_components_modal_Modal__WEBPACK_IMPORTED_MODULE_0___default()),\n    ModalContent: (tui_components_modal_ModalContent__WEBPACK_IMPORTED_MODULE_1___default()),\n    Avatar: (tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_2___default()),\n    Spinner: (tui_components_icons_common_Spinner__WEBPACK_IMPORTED_MODULE_3___default()),\n  },\n\n  props: {\n    instanceId: {\n      type: [String, Number],\n      required: true,\n    },\n\n    component: {\n      type: String,\n      required: true,\n    },\n\n    area: {\n      type: String,\n      required: true,\n    },\n  },\n\n  apollo: {\n    like: {\n      query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_4__[\"default\"],\n      fetchPolicy: 'network-only',\n      variables() {\n        return {\n          component: this.component,\n          area: this.area,\n          instanceid: this.instanceId,\n        };\n      },\n\n      update({ count, reactions }) {\n        return { count, reactions };\n      },\n    },\n  },\n\n  data() {\n    return {\n      page: 1,\n      like: {\n        count: 0,\n        reactions: [],\n      },\n    };\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_icons_common_Spinner__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/icons/common/Spinner */ \"tui/components/icons/common/Spinner\");\n/* harmony import */ var tui_components_icons_common_Spinner__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_common_Spinner__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_reaction/graphql/get_likes */ \"./server/totara/reaction/webapi/ajax/get_likes.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n// GraphQL queries\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Spinner: (tui_components_icons_common_Spinner__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n\n  props: {\n    component: {\n      type: String,\n      required: true,\n    },\n\n    area: {\n      type: String,\n      required: true,\n    },\n\n    instanceId: {\n      type: [String, Number],\n      required: true,\n    },\n\n    /**\n     * A prop to tell apollo whether to load or the records or not.\n     * This prop is being used in skip function, which it will only affect once.\n     */\n    skipLoadingRecords: Boolean,\n  },\n\n  apollo: {\n    like: {\n      query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n      skip() {\n        return this.skipLoadingRecords;\n      },\n      variables() {\n        return {\n          component: this.component,\n          area: this.area,\n          instanceid: this.instanceId,\n        };\n      },\n\n      /**\n       *\n       * @param {Number} count\n       * @param {Array} reactions\n       * @return {{count, reactions}}\n       */\n      update({ count, reactions }) {\n        return {\n          count: count,\n          reactions: reactions,\n        };\n      },\n    },\n  },\n\n  data() {\n    return {\n      page: 1,\n      like: {\n        count: 0,\n        reactions: [],\n      },\n    };\n  },\n\n  computed: {\n    /**\n     * Only fetching the first 10 of the items.\n     * @return {Array}\n     */\n    reactions() {\n      return Array.prototype.slice.call(this.like.reactions, 0, 9);\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=template&id=698b910c&":
/*!*************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/SidePanelLike.vue?vue&type=template&id=698b910c& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-sidePanelLike\" },\n    [\n      !_vm.$apollo.loading\n        ? [\n            _c(\"ButtonIconWithLabel\", {\n              attrs: {\n                \"button-aria-label\": _vm.buttonAriaLabel,\n                \"label-text\": _vm.count,\n                disabled: _vm.disabled\n              },\n              on: {\n                \"popover-open-changed\": function($event) {\n                  _vm.showPopover = $event\n                },\n                open: function($event) {\n                  _vm.showModal = true\n                },\n                click: _vm.like\n              },\n              scopedSlots: _vm._u(\n                [\n                  {\n                    key: \"icon\",\n                    fn: function() {\n                      return [\n                        _vm.submitting\n                          ? _c(\"Loading\")\n                          : !_vm.hasLiked\n                          ? _c(\"Like\")\n                          : _c(\"LikeActive\")\n                      ]\n                    },\n                    proxy: true\n                  },\n                  {\n                    key: \"hover-label-content\",\n                    fn: function() {\n                      return [\n                        _c(\"LikeRecordsList\", {\n                          attrs: {\n                            component: _vm.component,\n                            area: _vm.area,\n                            \"instance-id\": _vm.instanceId\n                          }\n                        })\n                      ]\n                    },\n                    proxy: true\n                  }\n                ],\n                null,\n                false,\n                1618023747\n              )\n            }),\n            _vm._v(\" \"),\n            _c(\n              \"ModalPresenter\",\n              {\n                attrs: { open: _vm.showModal },\n                on: {\n                  \"request-close\": function($event) {\n                    _vm.showModal = false\n                  }\n                }\n              },\n              [\n                _c(\"LikeRecordsModal\", {\n                  attrs: {\n                    component: _vm.component,\n                    area: _vm.area,\n                    \"instance-id\": _vm.instanceId\n                  }\n                })\n              ],\n              1\n            )\n          ]\n        : _vm._e()\n    ],\n    2\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SidePanelLike.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/SimpleLike.vue?vue&type=template&id=2e39d483&":
/*!**********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/SimpleLike.vue?vue&type=template&id=2e39d483& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-simpleLike\" },\n    [\n      !_vm.$apollo.loading\n        ? [\n            _c(\"LikeButtonIcon\", {\n              attrs: {\n                \"aria-label\": _vm.buttonAriaLabel,\n                liked: _vm.hasLiked,\n                submitting: _vm.submitting,\n                disabled: _vm.disabled,\n                text: _vm.buttonText\n              },\n              on: {\n                click: function($event) {\n                  $event.preventDefault()\n                  return _vm.like($event)\n                }\n              }\n            }),\n            _vm._v(\" \"),\n            0 !== _vm.count\n              ? _c(\n                  \"Popover\",\n                  {\n                    staticClass: \"tui-simpleLike__popover\",\n                    attrs: { triggers: [\"focus\", \"hover\"] },\n                    on: {\n                      \"open-changed\": function($event) {\n                        _vm.showPopover = $event\n                      }\n                    },\n                    scopedSlots: _vm._u(\n                      [\n                        {\n                          key: \"trigger\",\n                          fn: function() {\n                            return [\n                              _c(\n                                \"a\",\n                                {\n                                  attrs: { href: \"#\" },\n                                  on: {\n                                    click: function($event) {\n                                      $event.preventDefault()\n                                      _vm.showModal = true\n                                    }\n                                  }\n                                },\n                                [\n                                  _vm._v(\n                                    \"\\n          \" +\n                                      _vm._s(\n                                        _vm.$str(\n                                          \"bracketcount\",\n                                          \"totara_reaction\",\n                                          _vm.count\n                                        )\n                                      ) +\n                                      \"\\n        \"\n                                  )\n                                ]\n                              )\n                            ]\n                          },\n                          proxy: true\n                        }\n                      ],\n                      null,\n                      false,\n                      1309043721\n                    )\n                  },\n                  [\n                    _vm._v(\" \"),\n                    _c(\"LikeRecordsList\", {\n                      attrs: {\n                        \"skip-loading-records\": !_vm.showPopover,\n                        component: _vm.component,\n                        area: _vm.area,\n                        \"instance-id\": _vm.instanceId\n                      }\n                    })\n                  ],\n                  1\n                )\n              : _vm._e()\n          ]\n        : _vm._e(),\n      _vm._v(\" \"),\n      _c(\n        \"ModalPresenter\",\n        {\n          attrs: { open: _vm.showModal },\n          on: {\n            \"request-close\": function($event) {\n              _vm.showModal = false\n            }\n          }\n        },\n        [\n          _c(\"LikeRecordsModal\", {\n            attrs: {\n              component: _vm.component,\n              \"instance-id\": _vm.instanceId,\n              area: _vm.area\n            }\n          })\n        ],\n        1\n      )\n    ],\n    2\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/SimpleLike.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?vue&type=template&id=5958f8ac&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?vue&type=template&id=5958f8ac& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"ButtonIcon\",\n    {\n      attrs: {\n        \"aria-label\": _vm.ariaLabel,\n        styleclass: {\n          transparent: _vm.transparent,\n          small: _vm.small,\n          transparentNoPadding: _vm.transparentNoPadding\n        },\n        disabled: _vm.submitting || _vm.disabled,\n        text: _vm.text\n      },\n      on: {\n        click: function($event) {\n          return _vm.$emit(\"click\", $event)\n        }\n      }\n    },\n    [\n      _vm.submitting\n        ? _c(\"Loading\", { attrs: { size: _vm.iconSize } })\n        : !_vm.liked\n        ? _c(\"LikeIcon\", { attrs: { size: _vm.iconSize } })\n        : _c(\"LikedIcon\", { attrs: { size: _vm.iconSize } })\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/buttons/LikeButtonIcon.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=template&id=17becd7e&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?vue&type=template&id=17becd7e& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"Modal\",\n    { staticClass: \"tui-likeRecordsModal\" },\n    [\n      _c(\n        \"ModalContent\",\n        {\n          attrs: { \"close-button\": true },\n          scopedSlots: _vm._u([\n            {\n              key: \"title\",\n              fn: function() {\n                return [\n                  _c(\n                    \"h2\",\n                    { staticClass: \"tui-likeRecordsModal__title\" },\n                    [\n                      _c(\"span\", [\n                        _vm._v(\n                          \"\\n          \" +\n                            _vm._s(\n                              _vm.$str(\n                                \"likesx\",\n                                \"totara_reaction\",\n                                _vm.like.count\n                              )\n                            ) +\n                            \"\\n        \"\n                        )\n                      ]),\n                      _vm._v(\" \"),\n                      _vm.$apollo.loading ? _c(\"Spinner\") : _vm._e()\n                    ],\n                    1\n                  )\n                ]\n              },\n              proxy: true\n            }\n          ])\n        },\n        [\n          _vm._v(\" \"),\n          _c(\"div\", { staticClass: \"tui-likeRecordsModal__content\" }, [\n            _c(\n              \"ul\",\n              { staticClass: \"tui-likeRecordsModal__content__records\" },\n              _vm._l(_vm.like.reactions, function(ref, index) {\n                var user = ref.user\n                return _c(\n                  \"li\",\n                  { key: index },\n                  [\n                    _c(\"Avatar\", {\n                      attrs: {\n                        src: user.profileimageurl,\n                        alt: user.profileimagealt || \"\",\n                        size: \"xsmall\"\n                      }\n                    }),\n                    _vm._v(\" \"),\n                    _c(\n                      \"a\",\n                      {\n                        attrs: {\n                          href: _vm.$url(\"/user/profile.php\", { id: user.id })\n                        }\n                      },\n                      [\n                        _vm._v(\n                          \"\\n            \" +\n                            _vm._s(user.fullname) +\n                            \"\\n          \"\n                        )\n                      ]\n                    )\n                  ],\n                  1\n                )\n              }),\n              0\n            )\n          ])\n        ]\n      )\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/modal/LikeRecordsModal.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=template&id=7a282a0f&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?vue&type=template&id=7a282a0f& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-likeRecordsList\" },\n    [\n      _vm.$apollo.loading\n        ? _c(\"Spinner\", { attrs: { size: \"200\" } })\n        : [\n            0 === _vm.like.count\n              ? _c(\"p\", [\n                  _vm._v(\n                    \"\\n      \" +\n                      _vm._s(_vm.$str(\"nolikes\", \"totara_reaction\")) +\n                      \"\\n    \"\n                  )\n                ])\n              : _c(\n                  \"ul\",\n                  { staticClass: \"tui-likeRecordsList__list\" },\n                  _vm._l(_vm.reactions, function(ref, index) {\n                    var fullname = ref.user.fullname\n                    return _c(\"li\", { key: index }, [\n                      _vm._v(\"\\n        \" + _vm._s(fullname) + \"\\n      \")\n                    ])\n                  }),\n                  0\n                ),\n            _vm._v(\" \"),\n            _vm.like.count > 10\n              ? _c(\"p\", [\n                  _vm._v(\n                    \"\\n      \" +\n                      _vm._s(\n                        _vm.$str(\n                          \"numberofmore\",\n                          \"totara_reaction\",\n                          _vm.like.count - 10\n                        )\n                      ) +\n                      \"\\n    \"\n                  )\n                ])\n              : _vm._e()\n          ]\n    ],\n    2\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/totara_reaction/components/popover_content/LikeRecordsList.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./server/totara/reaction/webapi/ajax/create_like.graphql":
/*!****************************************************************!*\
  !*** ./server/totara/reaction/webapi/ajax/create_like.graphql ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_create_like\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_component\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_area\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"reaction\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_create\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]}]}}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/reaction/webapi/ajax/create_like.graphql?");

/***/ }),

/***/ "./server/totara/reaction/webapi/ajax/get_likes.graphql":
/*!**************************************************************!*\
  !*** ./server/totara/reaction/webapi/ajax/get_likes.graphql ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_get_likes\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_component\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_area\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"page\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"count\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_total\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}}}],\"directives\":[]},{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"reactions\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_reactions\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"page\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"page\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimagealt\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimageurl\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimageurlsmall\"},\"arguments\":[],\"directives\":[]}]}}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/reaction/webapi/ajax/get_likes.graphql?");

/***/ }),

/***/ "./server/totara/reaction/webapi/ajax/liked.graphql":
/*!**********************************************************!*\
  !*** ./server/totara/reaction/webapi/ajax/liked.graphql ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_liked\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_component\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_area\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"result\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_liked\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}}}],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/reaction/webapi/ajax/liked.graphql?");

/***/ }),

/***/ "./server/totara/reaction/webapi/ajax/remove_like.graphql":
/*!****************************************************************!*\
  !*** ./server/totara/reaction/webapi/ajax/remove_like.graphql ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_remove_like\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_component\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_area\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"result\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_delete\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}}}],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/reaction/webapi/ajax/remove_like.graphql?");

/***/ }),

/***/ "totara_engage/components/icons/Loading":
/*!**************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/icons/Loading\")" ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/icons/Loading\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/icons/Loading\\%22)%22?");

/***/ }),

/***/ "totara_reaction/components/buttons/LikeButtonIcon":
/*!*************************************************************************************!*\
  !*** external "tui.require(\"totara_reaction/components/buttons/LikeButtonIcon\")" ***!
  \*************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_reaction/components/buttons/LikeButtonIcon\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_reaction/components/buttons/LikeButtonIcon\\%22)%22?");

/***/ }),

/***/ "totara_reaction/components/modal/LikeRecordsModal":
/*!*************************************************************************************!*\
  !*** external "tui.require(\"totara_reaction/components/modal/LikeRecordsModal\")" ***!
  \*************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_reaction/components/modal/LikeRecordsModal\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_reaction/components/modal/LikeRecordsModal\\%22)%22?");

/***/ }),

/***/ "totara_reaction/components/popover_content/LikeRecordsList":
/*!**********************************************************************************************!*\
  !*** external "tui.require(\"totara_reaction/components/popover_content/LikeRecordsList\")" ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_reaction/components/popover_content/LikeRecordsList\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_reaction/components/popover_content/LikeRecordsList\\%22)%22?");

/***/ }),

/***/ "tui/components/avatar/Avatar":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/avatar/Avatar\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/avatar/Avatar\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/avatar/Avatar\\%22)%22?");

/***/ }),

/***/ "tui/components/buttons/ButtonIcon":
/*!*********************************************************************!*\
  !*** external "tui.require(\"tui/components/buttons/ButtonIcon\")" ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/buttons/ButtonIcon\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/buttons/ButtonIcon\\%22)%22?");

/***/ }),

/***/ "tui/components/buttons/LabelledButtonTrigger":
/*!********************************************************************************!*\
  !*** external "tui.require(\"tui/components/buttons/LabelledButtonTrigger\")" ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/buttons/LabelledButtonTrigger\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/buttons/LabelledButtonTrigger\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/common/Like":
/*!********************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/common/Like\")" ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/common/Like\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/common/Like\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/common/LikeActive":
/*!**************************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/common/LikeActive\")" ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/common/LikeActive\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/common/LikeActive\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/common/Spinner":
/*!***********************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/common/Spinner\")" ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/common/Spinner\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/common/Spinner\\%22)%22?");

/***/ }),

/***/ "tui/components/modal/Modal":
/*!**************************************************************!*\
  !*** external "tui.require(\"tui/components/modal/Modal\")" ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/modal/Modal\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/modal/Modal\\%22)%22?");

/***/ }),

/***/ "tui/components/modal/ModalContent":
/*!*********************************************************************!*\
  !*** external "tui.require(\"tui/components/modal/ModalContent\")" ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/modal/ModalContent\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/modal/ModalContent\\%22)%22?");

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

/***/ "tui/notifications":
/*!*****************************************************!*\
  !*** external "tui.require(\"tui/notifications\")" ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/notifications\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/notifications\\%22)%22?");

/***/ })

/******/ });