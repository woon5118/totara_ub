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
/******/ 		"totara_criteria/tui_bundle.legacy.development": 0
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
/******/ 	deferredModules.push(["./client/src/totara_criteria/tui.json","tui/vendors.legacy.development"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/src/totara_criteria/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!**********************************************************************************************!*\
  !*** ./client/src/totara_criteria/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./achievements/CompetencyAchievementDisplay\": \"./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue\",\n\t\"./achievements/CompetencyAchievementDisplay.vue\": \"./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue\",\n\t\"./achievements/CourseAchievementDisplay\": \"./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue\",\n\t\"./achievements/CourseAchievementDisplay.vue\": \"./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/src/totara_criteria/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/src/totara_criteria/components_sync_^(?:(?");

/***/ }),

/***/ "./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue":
/*!*********************************************************************************************!*\
  !*** ./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _CompetencyAchievementDisplay_vue_vue_type_template_id_bded420a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./CompetencyAchievementDisplay.vue?vue&type=template&id=bded420a& */ \"./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=template&id=bded420a&\");\n/* harmony import */ var _CompetencyAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CompetencyAchievementDisplay.vue?vue&type=script&lang=js& */ \"./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _CompetencyAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _CompetencyAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _CompetencyAchievementDisplay_vue_vue_type_template_id_bded420a___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _CompetencyAchievementDisplay_vue_vue_type_template_id_bded420a___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _CompetencyAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_CompetencyAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!********************************************************************************************************************************************!*\
  !*** ./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \********************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CompetencyAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CompetencyAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************!*\
  !*** ./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CompetencyAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CompetencyAchievementDisplay.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CompetencyAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=template&id=bded420a&":
/*!****************************************************************************************************************************!*\
  !*** ./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=template&id=bded420a& ***!
  \****************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CompetencyAchievementDisplay_vue_vue_type_template_id_bded420a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CompetencyAchievementDisplay.vue?vue&type=template&id=bded420a& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=template&id=bded420a&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CompetencyAchievementDisplay_vue_vue_type_template_id_bded420a___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CompetencyAchievementDisplay_vue_vue_type_template_id_bded420a___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue":
/*!*****************************************************************************************!*\
  !*** ./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue ***!
  \*****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _CourseAchievementDisplay_vue_vue_type_template_id_83ad738e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./CourseAchievementDisplay.vue?vue&type=template&id=83ad738e& */ \"./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=template&id=83ad738e&\");\n/* harmony import */ var _CourseAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CourseAchievementDisplay.vue?vue&type=script&lang=js& */ \"./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _CourseAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _CourseAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _CourseAchievementDisplay_vue_vue_type_template_id_83ad738e___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _CourseAchievementDisplay_vue_vue_type_template_id_83ad738e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _CourseAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_CourseAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************************************!*\
  !*** ./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CourseAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CourseAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************!*\
  !*** ./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CourseAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CourseAchievementDisplay.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CourseAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=template&id=83ad738e&":
/*!************************************************************************************************************************!*\
  !*** ./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=template&id=83ad738e& ***!
  \************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CourseAchievementDisplay_vue_vue_type_template_id_83ad738e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CourseAchievementDisplay.vue?vue&type=template&id=83ad738e& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=template&id=83ad738e&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CourseAchievementDisplay_vue_vue_type_template_id_83ad738e___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CourseAchievementDisplay_vue_vue_type_template_id_83ad738e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/totara_criteria/tui.json":
/*!*********************************************!*\
  !*** ./client/src/totara_criteria/tui.json ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"totara_criteria\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"totara_criteria\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"totara_criteria\")\ntui._bundle.addModulesFromContext(\"totara_criteria/components\", __webpack_require__(\"./client/src/totara_criteria/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/src/totara_criteria/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"criteria_childcompetency\": [\n    \"no_competencies\"\n  ],\n  \"criteria_othercompetency\": [\n    \"no_competencies\"\n  ],\n  \"totara_criteria\": [\n    \"achieve_proficiency_in_child_competencies\",\n    \"achieve_proficiency_in_other_competencies\",\n    \"assign_competency\",\n    \"competencies\",\n    \"complete\",\n    \"completion\",\n    \"confirm_assign_competency_body_by_other\",\n    \"confirm_assign_competency_body_by_self\",\n    \"confirm_assign_competency_title\",\n    \"error_competency_assignment\",\n    \"network_error\",\n    \"not_available\",\n    \"not_complete\",\n    \"achievement_level\",\n    \"self_assign_competency\",\n    \"view_competency\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_criteria\": [\n    \"complete\",\n    \"completion\",\n    \"complete_courses\",\n    \"course_link\",\n    \"courses\",\n    \"hidden_course\",\n    \"no_courses\",\n    \"not_available\",\n    \"not_complete\",\n    \"progress\"\n  ]\n}\n\n;\n    });\n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_competency/components/achievements/AchievementLayout */ \"totara_competency/components/achievements/AchievementLayout\");\n/* harmony import */ var totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/links/ActionLink */ \"tui/components/links/ActionLink\");\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/buttons/Button */ \"tui/components/buttons/Button\");\n/* harmony import */ var tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/datatable/Cell */ \"tui/components/datatable/Cell\");\n/* harmony import */ var tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_icons_common_CheckSuccess__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/icons/common/CheckSuccess */ \"tui/components/icons/common/CheckSuccess\");\n/* harmony import */ var tui_components_icons_common_CheckSuccess__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_common_CheckSuccess__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_components_modal_ConfirmationModal__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/components/modal/ConfirmationModal */ \"tui/components/modal/ConfirmationModal\");\n/* harmony import */ var tui_components_modal_ConfirmationModal__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_ConfirmationModal__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/components/datatable/ExpandCell */ \"tui/components/datatable/ExpandCell\");\n/* harmony import */ var tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_competency/components/achievements/ProgressCircle */ \"totara_competency/components/achievements/ProgressCircle\");\n/* harmony import */ var totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! tui/components/datatable/Table */ \"tui/components/datatable/Table\");\n/* harmony import */ var tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! tui/notifications */ \"tui/notifications\");\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(tui_notifications__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var totara_competency_graphql_create_user_assignments__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! totara_competency/graphql/create_user_assignments */ \"./server/totara/competency/webapi/ajax/create_user_assignments.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n// Components\n\n\n\n\n\n\n\n\n\n // GraphQL\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AchievementLayout: totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0___default.a,\n    ActionLink: tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default.a,\n    Button: tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_2___default.a,\n    Cell: tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_3___default.a,\n    CheckIcon: tui_components_icons_common_CheckSuccess__WEBPACK_IMPORTED_MODULE_4___default.a,\n    ConfirmationModal: tui_components_modal_ConfirmationModal__WEBPACK_IMPORTED_MODULE_5___default.a,\n    ExpandCell: tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_6___default.a,\n    ProgressCircle: totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_7___default.a,\n    Table: tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_8___default.a\n  },\n  props: {\n    achievements: {\n      required: true,\n      type: Object\n    },\n    type: {\n      required: true,\n      type: String\n    },\n    userId: {\n      required: true,\n      type: Number\n    }\n  },\n  data: function data() {\n    return {\n      modalOpen: false\n    };\n  },\n  computed: {\n    /**\n     * Return int for number of completed competencies\n     *\n     * @return {Integer}\n     */\n    achievedCompetencies: function achievedCompetencies() {\n      return this.achievements.items.reduce(function (total, current) {\n        return current.value && current.value.proficient ? total += 1 : total;\n      }, 0);\n    },\n\n    /**\n     * Check if the criteria has been completed\n     *\n     * @return {Boolean}\n     */\n    criteriaComplete: function criteriaComplete() {\n      return this.achievedCompetencies >= this.numberOfRequiredCompetencies;\n    },\n\n    /**\n     * Return criteria header strings based on competency type\n     *\n     * @return {String}\n     */\n    criteriaHeading: function criteriaHeading() {\n      if (this.type === 'otherCompetency') {\n        return this.$str('achieve_proficiency_in_other_competencies', 'totara_criteria');\n      }\n\n      return this.$str('achieve_proficiency_in_child_competencies', 'totara_criteria');\n    },\n\n    /**\n     * Return no competency strings based on competency type\n     *\n     * @return {String}\n     */\n    noCompetenciesString: function noCompetenciesString() {\n      if (this.type === 'otherCompetency') {\n        return this.$str('no_competencies', 'criteria_othercompetency');\n      }\n\n      return this.$str('no_competencies', 'criteria_childcompetency');\n    },\n\n    /**\n     * Return int for required number of competencies completed to fulfill criteria\n     *\n     * @return {Integer}\n     */\n    numberOfRequiredCompetencies: function numberOfRequiredCompetencies() {\n      if (this.achievements.aggregation_method === 1) {\n        return this.achievements.items.length;\n      }\n\n      return this.achievements.required_items;\n    }\n  },\n  methods: {\n    /**\n     * Trigger a mutation to assign selected competency\n     *\n     */\n    assignCompetency: function assignCompetency(competency) {\n      var _this = this;\n\n      this.$apollo.mutate({\n        // Query\n        mutation: totara_competency_graphql_create_user_assignments__WEBPACK_IMPORTED_MODULE_10__[\"default\"],\n        // Parameters\n        variables: {\n          competency_ids: [competency.id],\n          user_id: this.userId\n        }\n      }).then(function (_ref) {\n        var data = _ref.data;\n\n        if (data && data.totara_competency_create_user_assignments) {\n          var result = data.totara_competency_create_user_assignments; // Due to this being a batch api designed to tolerate partial success,\n          // single assignment can silently fail, indicated by no results being returned.\n\n          if (result.length > 0) {\n            _this.$emit('self-assigned');\n          } else {\n            _this.triggerErrorNotification(_this.$str('error_competency_assignment', 'totara_criteria'));\n          }\n        }\n      })[\"catch\"](function (error) {\n        console.error(error);\n\n        _this.triggerErrorNotification(_this.$str('error_competency_assignment', 'totara_criteria'));\n      })[\"finally\"](function () {\n        return _this.closeModal();\n      });\n    },\n\n    /**\n     * Display error messages when competency assignment fails\n     *\n     */\n    triggerErrorNotification: function triggerErrorNotification(message) {\n      Object(tui_notifications__WEBPACK_IMPORTED_MODULE_9__[\"notify\"])({\n        message: message,\n        type: 'error'\n      });\n    },\n\n    /**\n     * Show assign competency modal\n     *\n     */\n    showModal: function showModal() {\n      this.modalOpen = true;\n    },\n\n    /**\n     * Close assign competency modal\n     *\n     */\n    closeModal: function closeModal() {\n      this.modalOpen = false;\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_competency/components/achievements/AchievementLayout */ \"totara_competency/components/achievements/AchievementLayout\");\n/* harmony import */ var totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/links/ActionLink */ \"tui/components/links/ActionLink\");\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/datatable/Cell */ \"tui/components/datatable/Cell\");\n/* harmony import */ var tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_icons_common_CheckSuccess__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/icons/common/CheckSuccess */ \"tui/components/icons/common/CheckSuccess\");\n/* harmony import */ var tui_components_icons_common_CheckSuccess__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_common_CheckSuccess__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/datatable/ExpandCell */ \"tui/components/datatable/ExpandCell\");\n/* harmony import */ var tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/components/progress/Progress */ \"tui/components/progress/Progress\");\n/* harmony import */ var tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! totara_competency/components/achievements/ProgressCircle */ \"totara_competency/components/achievements/ProgressCircle\");\n/* harmony import */ var totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! tui/components/datatable/Table */ \"tui/components/datatable/Table\");\n/* harmony import */ var tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_7__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AchievementLayout: totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0___default.a,\n    ActionLink: tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default.a,\n    Cell: tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_2___default.a,\n    CheckIcon: tui_components_icons_common_CheckSuccess__WEBPACK_IMPORTED_MODULE_3___default.a,\n    ExpandCell: tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_4___default.a,\n    Progress: tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_5___default.a,\n    ProgressCircle: totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_6___default.a,\n    Table: tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_7___default.a\n  },\n  props: {\n    achievements: {\n      required: true,\n      type: Object\n    }\n  },\n  computed: {\n    /**\n     * Return bool for criteria fulfilled\n     *\n     * @return {Boolean}\n     */\n    criteriaFulfilled: function criteriaFulfilled() {\n      return this.completedNumberOfCourses >= this.targetNumberOfCourses;\n    },\n\n    /**\n     * Return int for number of courses\n     *\n     * @return {Integer}\n     */\n    numberOfCourses: function numberOfCourses() {\n      return this.achievements.items ? this.achievements.items.length : 0;\n    },\n\n    /**\n     * Return int for number of courses completed\n     *\n     * @return {Integer}\n     */\n    completedNumberOfCourses: function completedNumberOfCourses() {\n      var complete = 0;\n\n      if (!this.numberOfCourses) {\n        return complete;\n      }\n\n      this.achievements.items.forEach(function (item) {\n        if (item.course && item.course.progress === 100) {\n          complete++;\n        }\n      });\n      return complete;\n    },\n\n    /**\n     * Return int for required number of courses completed to fulfil criteria\n     *\n     * @return {Integer}\n     */\n    targetNumberOfCourses: function targetNumberOfCourses() {\n      // If aggregation_method is set to achieve ALL courses\n      if (this.achievements.aggregation_method === 1) {\n        return this.numberOfCourses;\n      }\n\n      return this.achievements.required_items;\n    }\n  },\n  methods: {\n    /**\n     * Return course name or unavailable to user string\n     *\n     * @return {String}\n     */\n    getCourseName: function getCourseName(row) {\n      return row.course ? row.course.fullname : this.$str('hidden_course', 'totara_criteria');\n    },\n\n    /**\n     * Return bool based on progress data\n     *\n     * @return {Boolean}\n     */\n    hasProgress: function hasProgress(row) {\n      return row.course && row.course.progress > 0;\n    },\n\n    /**\n     * Return bool based on course completion\n     *\n     * @return {Boolean}\n     */\n    isComplete: function isComplete(row) {\n      return row.course && row.course.progress === 100;\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=template&id=bded420a&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?vue&type=template&id=bded420a& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-criteriaCompetencyAchievement\" },\n    [\n      _c(\"AchievementLayout\", {\n        scopedSlots: _vm._u([\n          {\n            key: \"left\",\n            fn: function() {\n              return [\n                _c(\n                  \"div\",\n                  { staticClass: \"tui-criteriaCompetencyAchievement__goal\" },\n                  [\n                    _c(\n                      \"h5\",\n                      {\n                        staticClass: \"tui-criteriaCompetencyAchievement__title\"\n                      },\n                      [\n                        _vm._v(\n                          \"\\n          \" +\n                            _vm._s(_vm.criteriaHeading) +\n                            \"\\n        \"\n                        )\n                      ]\n                    ),\n                    _vm._v(\" \"),\n                    _c(\"ProgressCircle\", {\n                      attrs: {\n                        complete: _vm.criteriaComplete,\n                        completed: _vm.criteriaComplete\n                          ? _vm.numberOfRequiredCompetencies\n                          : _vm.achievedCompetencies,\n                        target: _vm.numberOfRequiredCompetencies\n                      }\n                    })\n                  ],\n                  1\n                )\n              ]\n            },\n            proxy: true\n          },\n          {\n            key: \"right\",\n            fn: function() {\n              return [\n                _c(\"Table\", {\n                  attrs: {\n                    data: _vm.achievements.items,\n                    \"expandable-rows\": true,\n                    \"no-items-text\": _vm.noCompetenciesString\n                  },\n                  scopedSlots: _vm._u([\n                    {\n                      key: \"row\",\n                      fn: function(ref) {\n                        var row = ref.row\n                        var expand = ref.expand\n                        var expandState = ref.expandState\n                        return [\n                          _c(\"ExpandCell\", {\n                            attrs: { size: \"1\", \"expand-state\": expandState },\n                            on: {\n                              click: function($event) {\n                                return expand()\n                              }\n                            }\n                          }),\n                          _vm._v(\" \"),\n                          _c(\n                            \"Cell\",\n                            {\n                              attrs: {\n                                size: \"9\",\n                                \"column-header\": _vm.$str(\n                                  \"competencies\",\n                                  \"totara_criteria\"\n                                )\n                              }\n                            },\n                            [\n                              _vm._v(\n                                \"\\n            \" +\n                                  _vm._s(row.competency.fullname) +\n                                  \"\\n          \"\n                              )\n                            ]\n                          ),\n                          _vm._v(\" \"),\n                          _c(\n                            \"Cell\",\n                            {\n                              class: \"tui-criteriaCompetencyAchievement__level\",\n                              attrs: {\n                                size: \"3\",\n                                \"column-header\": _vm.$str(\n                                  \"achievement_level\",\n                                  \"totara_criteria\"\n                                )\n                              }\n                            },\n                            [\n                              row.value\n                                ? [\n                                    _vm._v(\n                                      \"\\n              \" +\n                                        _vm._s(row.value.name) +\n                                        \"\\n            \"\n                                    )\n                                  ]\n                                : [\n                                    _c(\n                                      \"span\",\n                                      {\n                                        staticClass:\n                                          \"tui-criteriaCompetencyAchievement__level-notAvailable\"\n                                      },\n                                      [\n                                        _vm._v(\n                                          \"\\n                \" +\n                                            _vm._s(\n                                              _vm.$str(\n                                                \"not_available\",\n                                                \"totara_criteria\"\n                                              )\n                                            ) +\n                                            \"\\n              \"\n                                        )\n                                      ]\n                                    )\n                                  ]\n                            ],\n                            2\n                          ),\n                          _vm._v(\" \"),\n                          _c(\n                            \"Cell\",\n                            {\n                              attrs: {\n                                size: \"3\",\n                                \"column-header\": _vm.$str(\n                                  \"completion\",\n                                  \"totara_criteria\"\n                                ),\n                                align: \"end\"\n                              }\n                            },\n                            [\n                              row.value && row.value.proficient\n                                ? _c(\n                                    \"div\",\n                                    {\n                                      staticClass:\n                                        \"tui-criteriaCompetencyAchievement__completion-complete\"\n                                    },\n                                    [\n                                      _c(\"CheckIcon\", {\n                                        attrs: { size: \"200\" }\n                                      }),\n                                      _vm._v(\n                                        \"\\n              \" +\n                                          _vm._s(\n                                            _vm.$str(\n                                              \"complete\",\n                                              \"totara_criteria\"\n                                            )\n                                          ) +\n                                          \"\\n            \"\n                                      )\n                                    ],\n                                    1\n                                  )\n                                : _c(\n                                    \"div\",\n                                    {\n                                      staticClass:\n                                        \"tui-criteriaCompetencyAchievement__completion-notComplete\"\n                                    },\n                                    [\n                                      _vm._v(\n                                        \"\\n              \" +\n                                          _vm._s(\n                                            _vm.$str(\n                                              \"not_complete\",\n                                              \"totara_criteria\"\n                                            )\n                                          ) +\n                                          \"\\n            \"\n                                      )\n                                    ]\n                                  )\n                            ]\n                          )\n                        ]\n                      }\n                    },\n                    {\n                      key: \"expand-content\",\n                      fn: function(ref) {\n                        var row = ref.row\n                        return [\n                          _c(\n                            \"div\",\n                            {\n                              staticClass:\n                                \"tui-criteriaCompetencyAchievement__summary\"\n                            },\n                            [\n                              _c(\n                                \"h6\",\n                                {\n                                  staticClass:\n                                    \"tui-criteriaCompetencyAchievement__summary-header\"\n                                },\n                                [\n                                  _vm._v(\n                                    \"\\n              \" +\n                                      _vm._s(row.competency.fullname) +\n                                      \"\\n            \"\n                                  )\n                                ]\n                              ),\n                              _vm._v(\" \"),\n                              _c(\"div\", {\n                                staticClass:\n                                  \"tui-criteriaCompetencyAchievement__summary-body\",\n                                domProps: {\n                                  innerHTML: _vm._s(row.competency.description)\n                                }\n                              }),\n                              _vm._v(\" \"),\n                              row.assigned\n                                ? _c(\"ActionLink\", {\n                                    class:\n                                      \"tui-criteriaCompetencyAchievement__summary-button\",\n                                    attrs: {\n                                      href: _vm.$url(\n                                        \"/totara/competency/profile/details/index.php\",\n                                        {\n                                          competency_id: row.competency.id,\n                                          user_id: _vm.userId\n                                        }\n                                      ),\n                                      text: _vm.$str(\n                                        \"view_competency\",\n                                        \"totara_criteria\"\n                                      ),\n                                      styleclass: {\n                                        primary: true,\n                                        small: true\n                                      }\n                                    }\n                                  })\n                                : row.self_assignable\n                                ? _c(\n                                    \"div\",\n                                    [\n                                      _c(\"Button\", {\n                                        class:\n                                          \"tui-criteriaCompetencyAchievement__summary-button\",\n                                        attrs: {\n                                          text: _vm.$str(\n                                            _vm.achievements.current_user\n                                              ? \"self_assign_competency\"\n                                              : \"assign_competency\",\n                                            \"totara_criteria\"\n                                          ),\n                                          styleclass: {\n                                            primary: true,\n                                            small: true\n                                          }\n                                        },\n                                        on: {\n                                          click: function($event) {\n                                            return _vm.showModal(row.competency)\n                                          }\n                                        }\n                                      })\n                                    ],\n                                    1\n                                  )\n                                : _vm._e()\n                            ],\n                            1\n                          ),\n                          _vm._v(\" \"),\n                          _c(\n                            \"ConfirmationModal\",\n                            {\n                              attrs: {\n                                open: _vm.modalOpen,\n                                title: _vm.$str(\n                                  \"confirm_assign_competency_title\",\n                                  \"totara_criteria\"\n                                )\n                              },\n                              on: {\n                                confirm: function($event) {\n                                  return _vm.assignCompetency(row.competency)\n                                },\n                                cancel: _vm.closeModal\n                              }\n                            },\n                            [\n                              _vm._v(\n                                \"\\n            \" +\n                                  _vm._s(\n                                    _vm.$str(\n                                      _vm.achievements.current_user\n                                        ? \"confirm_assign_competency_body_by_self\"\n                                        : \"confirm_assign_competency_body_by_other\",\n                                      \"totara_criteria\",\n                                      row.competency.fullname\n                                    )\n                                  ) +\n                                  \"\\n          \"\n                              )\n                            ]\n                          )\n                        ]\n                      }\n                    }\n                  ])\n                })\n              ]\n            },\n            proxy: true\n          }\n        ])\n      })\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CompetencyAchievementDisplay.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=template&id=83ad738e&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?vue&type=template&id=83ad738e& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-criteriaCourseAchievement\" },\n    [\n      _c(\"AchievementLayout\", {\n        scopedSlots: _vm._u([\n          {\n            key: \"left\",\n            fn: function() {\n              return [\n                _c(\n                  \"div\",\n                  { staticClass: \"tui-criteriaCourseAchievement__goal\" },\n                  [\n                    _c(\n                      \"h4\",\n                      { staticClass: \"tui-criteriaCourseAchievement__title\" },\n                      [\n                        _vm._v(\n                          \"\\n          \" +\n                            _vm._s(\n                              _vm.$str(\"complete_courses\", \"totara_criteria\")\n                            ) +\n                            \"\\n        \"\n                        )\n                      ]\n                    ),\n                    _vm._v(\" \"),\n                    _c(\"ProgressCircle\", {\n                      attrs: {\n                        complete: _vm.criteriaFulfilled,\n                        completed:\n                          _vm.completedNumberOfCourses >=\n                          _vm.targetNumberOfCourses\n                            ? _vm.targetNumberOfCourses\n                            : _vm.completedNumberOfCourses,\n                        target: _vm.targetNumberOfCourses\n                      }\n                    })\n                  ],\n                  1\n                )\n              ]\n            },\n            proxy: true\n          },\n          {\n            key: \"right\",\n            fn: function() {\n              return [\n                _c(\"Table\", {\n                  attrs: {\n                    data: _vm.achievements.items,\n                    \"expandable-rows\": true,\n                    \"no-items-text\": _vm.$str(\"no_courses\", \"totara_criteria\")\n                  },\n                  scopedSlots: _vm._u([\n                    {\n                      key: \"row\",\n                      fn: function(ref) {\n                        var row = ref.row\n                        var expand = ref.expand\n                        var expandState = ref.expandState\n                        return [\n                          _c(\"ExpandCell\", {\n                            attrs: { \"expand-state\": expandState },\n                            on: {\n                              click: function($event) {\n                                return expand()\n                              }\n                            }\n                          }),\n                          _vm._v(\" \"),\n                          _c(\n                            \"Cell\",\n                            {\n                              attrs: {\n                                size: \"9\",\n                                \"column-header\": _vm.$str(\n                                  \"courses\",\n                                  \"totara_criteria\"\n                                )\n                              }\n                            },\n                            [\n                              _vm._v(\n                                \"\\n            \" +\n                                  _vm._s(_vm.getCourseName(row)) +\n                                  \"\\n          \"\n                              )\n                            ]\n                          ),\n                          _vm._v(\" \"),\n                          _c(\n                            \"Cell\",\n                            {\n                              class: \"tui-criteriaCourseAchievement__progress\",\n                              attrs: {\n                                size: \"3\",\n                                \"column-header\": _vm.$str(\n                                  \"progress\",\n                                  \"totara_criteria\"\n                                )\n                              }\n                            },\n                            [\n                              _vm.hasProgress(row)\n                                ? _c(\n                                    \"div\",\n                                    {\n                                      staticClass:\n                                        \"tui-criteriaCourseAchievement__progress-bar\"\n                                    },\n                                    [\n                                      _c(\"Progress\", {\n                                        attrs: { value: row.course.progress }\n                                      })\n                                    ],\n                                    1\n                                  )\n                                : _c(\n                                    \"div\",\n                                    {\n                                      staticClass:\n                                        \"tui-criteriaCourseAchievement__progress-empty\"\n                                    },\n                                    [\n                                      _vm._v(\n                                        \"\\n              \" +\n                                          _vm._s(\n                                            _vm.$str(\n                                              \"not_available\",\n                                              \"totara_criteria\"\n                                            )\n                                          ) +\n                                          \"\\n            \"\n                                      )\n                                    ]\n                                  )\n                            ]\n                          ),\n                          _vm._v(\" \"),\n                          _c(\n                            \"Cell\",\n                            {\n                              attrs: {\n                                size: \"3\",\n                                \"column-header\": _vm.$str(\n                                  \"completion\",\n                                  \"totara_criteria\"\n                                ),\n                                align: \"end\"\n                              }\n                            },\n                            [\n                              _vm.isComplete(row)\n                                ? _c(\n                                    \"div\",\n                                    {\n                                      staticClass:\n                                        \"tui-criteriaCourseAchievement__completion-complete\"\n                                    },\n                                    [\n                                      _c(\"CheckIcon\", {\n                                        attrs: { size: \"200\" }\n                                      }),\n                                      _vm._v(\n                                        \"\\n              \" +\n                                          _vm._s(\n                                            _vm.$str(\n                                              \"complete\",\n                                              \"totara_criteria\"\n                                            )\n                                          ) +\n                                          \"\\n            \"\n                                      )\n                                    ],\n                                    1\n                                  )\n                                : _c(\n                                    \"div\",\n                                    {\n                                      staticClass:\n                                        \"tui-criteriaCourseAchievement__completion-notComplete\"\n                                    },\n                                    [\n                                      _vm._v(\n                                        \"\\n              \" +\n                                          _vm._s(\n                                            _vm.$str(\n                                              \"not_complete\",\n                                              \"totara_criteria\"\n                                            )\n                                          ) +\n                                          \"\\n            \"\n                                      )\n                                    ]\n                                  )\n                            ]\n                          )\n                        ]\n                      }\n                    },\n                    {\n                      key: \"expand-content\",\n                      fn: function(ref) {\n                        var row = ref.row\n                        return [\n                          _c(\n                            \"div\",\n                            {\n                              staticClass:\n                                \"tui-criteriaCourseAchievement__summary\"\n                            },\n                            [\n                              _c(\n                                \"h6\",\n                                {\n                                  staticClass:\n                                    \"tui-criteriaCourseAchievement__summary-header\"\n                                },\n                                [\n                                  _vm._v(\n                                    \"\\n              \" +\n                                      _vm._s(row.course.fullname) +\n                                      \"\\n            \"\n                                  )\n                                ]\n                              ),\n                              _vm._v(\" \"),\n                              _c(\"div\", {\n                                staticClass:\n                                  \"tui-criteriaCourseAchievement__summary-body\",\n                                domProps: {\n                                  innerHTML: _vm._s(row.course.description)\n                                }\n                              }),\n                              _vm._v(\" \"),\n                              _c(\"ActionLink\", {\n                                class:\n                                  \"tui-criteriaCourseAchievement__summary-button\",\n                                attrs: {\n                                  href: row.course.url_view,\n                                  text: _vm.$str(\n                                    \"course_link\",\n                                    \"totara_criteria\"\n                                  ),\n                                  styleclass: {\n                                    primary: true,\n                                    small: true\n                                  }\n                                }\n                              })\n                            ],\n                            1\n                          )\n                        ]\n                      }\n                    }\n                  ])\n                })\n              ]\n            },\n            proxy: true\n          }\n        ])\n      })\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/totara_criteria/components/achievements/CourseAchievementDisplay.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./server/totara/competency/webapi/ajax/create_user_assignments.graphql":
/*!******************************************************************************!*\
  !*** ./server/totara/competency/webapi/ajax/create_user_assignments.graphql ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_competency_create_user_assignments\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"user_id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"core_id\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"competency_ids\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"core_id\"}}}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_competency_create_user_assignments\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"user_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"user_id\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"competency_ids\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"competency_ids\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/competency/webapi/ajax/create_user_assignments.graphql?");

/***/ }),

/***/ "totara_competency/components/achievements/AchievementLayout":
/*!***********************************************************************************************!*\
  !*** external "tui.require(\"totara_competency/components/achievements/AchievementLayout\")" ***!
  \***********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_competency/components/achievements/AchievementLayout\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_competency/components/achievements/AchievementLayout\\%22)%22?");

/***/ }),

/***/ "totara_competency/components/achievements/ProgressCircle":
/*!********************************************************************************************!*\
  !*** external "tui.require(\"totara_competency/components/achievements/ProgressCircle\")" ***!
  \********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_competency/components/achievements/ProgressCircle\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_competency/components/achievements/ProgressCircle\\%22)%22?");

/***/ }),

/***/ "tui/components/buttons/Button":
/*!*****************************************************************!*\
  !*** external "tui.require(\"tui/components/buttons/Button\")" ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/buttons/Button\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/buttons/Button\\%22)%22?");

/***/ }),

/***/ "tui/components/datatable/Cell":
/*!*****************************************************************!*\
  !*** external "tui.require(\"tui/components/datatable/Cell\")" ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/datatable/Cell\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/datatable/Cell\\%22)%22?");

/***/ }),

/***/ "tui/components/datatable/ExpandCell":
/*!***********************************************************************!*\
  !*** external "tui.require(\"tui/components/datatable/ExpandCell\")" ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/datatable/ExpandCell\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/datatable/ExpandCell\\%22)%22?");

/***/ }),

/***/ "tui/components/datatable/Table":
/*!******************************************************************!*\
  !*** external "tui.require(\"tui/components/datatable/Table\")" ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/datatable/Table\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/datatable/Table\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/common/CheckSuccess":
/*!****************************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/common/CheckSuccess\")" ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/common/CheckSuccess\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/common/CheckSuccess\\%22)%22?");

/***/ }),

/***/ "tui/components/links/ActionLink":
/*!*******************************************************************!*\
  !*** external "tui.require(\"tui/components/links/ActionLink\")" ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/links/ActionLink\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/links/ActionLink\\%22)%22?");

/***/ }),

/***/ "tui/components/modal/ConfirmationModal":
/*!**************************************************************************!*\
  !*** external "tui.require(\"tui/components/modal/ConfirmationModal\")" ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/modal/ConfirmationModal\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/modal/ConfirmationModal\\%22)%22?");

/***/ }),

/***/ "tui/components/progress/Progress":
/*!********************************************************************!*\
  !*** external "tui.require(\"tui/components/progress/Progress\")" ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/progress/Progress\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/progress/Progress\\%22)%22?");

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