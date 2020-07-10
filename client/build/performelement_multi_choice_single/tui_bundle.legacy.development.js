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
/******/ 		"performelement_multi_choice_single/tui_bundle.legacy.development": 0
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
/******/ 	deferredModules.push(["./client/src/performelement_multi_choice_single/tui.json","tui/vendors.legacy.development"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/src/performelement_multi_choice_single/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!*****************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \*****************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./MultiChoiceSingleElementAdminDisplay\": \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue\",\n\t\"./MultiChoiceSingleElementAdminDisplay.vue\": \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue\",\n\t\"./MultiChoiceSingleElementAdminForm\": \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue\",\n\t\"./MultiChoiceSingleElementAdminForm.vue\": \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue\",\n\t\"./MultiChoiceSingleElementAdminReadOnlyDisplay\": \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue\",\n\t\"./MultiChoiceSingleElementAdminReadOnlyDisplay.vue\": \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue\",\n\t\"./MultiChoiceSingleElementParticipantForm\": \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue\",\n\t\"./MultiChoiceSingleElementParticipantForm.vue\": \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue\",\n\t\"./MultiChoiceSingleElementParticipantResponse\": \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue\",\n\t\"./MultiChoiceSingleElementParticipantResponse.vue\": \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/src/performelement_multi_choice_single/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/src/performelement_multi_choice_single/components_sync_^(?:(?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue":
/*!***********************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue ***!
  \***********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _MultiChoiceSingleElementAdminDisplay_vue_vue_type_template_id_1b33b604___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./MultiChoiceSingleElementAdminDisplay.vue?vue&type=template&id=1b33b604& */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?vue&type=template&id=1b33b604&\");\n/* harmony import */ var _MultiChoiceSingleElementAdminDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./MultiChoiceSingleElementAdminDisplay.vue?vue&type=script&lang=js& */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _MultiChoiceSingleElementAdminDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _MultiChoiceSingleElementAdminDisplay_vue_vue_type_template_id_1b33b604___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _MultiChoiceSingleElementAdminDisplay_vue_vue_type_template_id_1b33b604___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--1-0!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementAdminDisplay.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?vue&type=template&id=1b33b604&":
/*!******************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?vue&type=template&id=1b33b604& ***!
  \******************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminDisplay_vue_vue_type_template_id_1b33b604___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementAdminDisplay.vue?vue&type=template&id=1b33b604& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?vue&type=template&id=1b33b604&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminDisplay_vue_vue_type_template_id_1b33b604___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminDisplay_vue_vue_type_template_id_1b33b604___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue":
/*!********************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue ***!
  \********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _MultiChoiceSingleElementAdminForm_vue_vue_type_template_id_0e8051f2___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./MultiChoiceSingleElementAdminForm.vue?vue&type=template&id=0e8051f2& */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=template&id=0e8051f2&\");\n/* harmony import */ var _MultiChoiceSingleElementAdminForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./MultiChoiceSingleElementAdminForm.vue?vue&type=script&lang=js& */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _MultiChoiceSingleElementAdminForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./MultiChoiceSingleElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _MultiChoiceSingleElementAdminForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _MultiChoiceSingleElementAdminForm_vue_vue_type_template_id_0e8051f2___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _MultiChoiceSingleElementAdminForm_vue_vue_type_template_id_0e8051f2___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _MultiChoiceSingleElementAdminForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_MultiChoiceSingleElementAdminForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*******************************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*******************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_lang_strings_loader.js!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--1-0!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementAdminForm.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=template&id=0e8051f2&":
/*!***************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=template&id=0e8051f2& ***!
  \***************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminForm_vue_vue_type_template_id_0e8051f2___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementAdminForm.vue?vue&type=template&id=0e8051f2& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=template&id=0e8051f2&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminForm_vue_vue_type_template_id_0e8051f2___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminForm_vue_vue_type_template_id_0e8051f2___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue":
/*!*******************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue ***!
  \*******************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_template_id_de6c1f7c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=template&id=de6c1f7c& */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=template&id=de6c1f7c&\");\n/* harmony import */ var _MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js& */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_template_id_de6c1f7c___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_template_id_de6c1f7c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!******************************************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \******************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_lang_strings_loader.js!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--1-0!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=template&id=de6c1f7c&":
/*!**************************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=template&id=de6c1f7c& ***!
  \**************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_template_id_de6c1f7c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=template&id=de6c1f7c& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=template&id=de6c1f7c&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_template_id_de6c1f7c___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementAdminReadOnlyDisplay_vue_vue_type_template_id_de6c1f7c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue":
/*!**************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue ***!
  \**************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _MultiChoiceSingleElementParticipantForm_vue_vue_type_template_id_1cfbbed4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./MultiChoiceSingleElementParticipantForm.vue?vue&type=template&id=1cfbbed4& */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=template&id=1cfbbed4&\");\n/* harmony import */ var _MultiChoiceSingleElementParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./MultiChoiceSingleElementParticipantForm.vue?vue&type=script&lang=js& */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _MultiChoiceSingleElementParticipantForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./MultiChoiceSingleElementParticipantForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _MultiChoiceSingleElementParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _MultiChoiceSingleElementParticipantForm_vue_vue_type_template_id_1cfbbed4___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _MultiChoiceSingleElementParticipantForm_vue_vue_type_template_id_1cfbbed4___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _MultiChoiceSingleElementParticipantForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_MultiChoiceSingleElementParticipantForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_lang_strings_loader.js!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementParticipantForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--1-0!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementParticipantForm.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=template&id=1cfbbed4&":
/*!*********************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=template&id=1cfbbed4& ***!
  \*********************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantForm_vue_vue_type_template_id_1cfbbed4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementParticipantForm.vue?vue&type=template&id=1cfbbed4& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=template&id=1cfbbed4&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantForm_vue_vue_type_template_id_1cfbbed4___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantForm_vue_vue_type_template_id_1cfbbed4___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue":
/*!******************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue ***!
  \******************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _MultiChoiceSingleElementParticipantResponse_vue_vue_type_template_id_1dd6f493___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./MultiChoiceSingleElementParticipantResponse.vue?vue&type=template&id=1dd6f493& */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=template&id=1dd6f493&\");\n/* harmony import */ var _MultiChoiceSingleElementParticipantResponse_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./MultiChoiceSingleElementParticipantResponse.vue?vue&type=script&lang=js& */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _MultiChoiceSingleElementParticipantResponse_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./MultiChoiceSingleElementParticipantResponse.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _MultiChoiceSingleElementParticipantResponse_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _MultiChoiceSingleElementParticipantResponse_vue_vue_type_template_id_1dd6f493___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _MultiChoiceSingleElementParticipantResponse_vue_vue_type_template_id_1dd6f493___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _MultiChoiceSingleElementParticipantResponse_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_MultiChoiceSingleElementParticipantResponse_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*****************************************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*****************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantResponse_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_lang_strings_loader.js!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementParticipantResponse.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantResponse_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantResponse_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--1-0!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementParticipantResponse.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantResponse_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=template&id=1dd6f493&":
/*!*************************************************************************************************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=template&id=1dd6f493& ***!
  \*************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantResponse_vue_vue_type_template_id_1dd6f493___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleElementParticipantResponse.vue?vue&type=template&id=1dd6f493& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=template&id=1dd6f493&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantResponse_vue_vue_type_template_id_1dd6f493___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleElementParticipantResponse_vue_vue_type_template_id_1dd6f493___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?");

/***/ }),

/***/ "./client/src/performelement_multi_choice_single/tui.json":
/*!****************************************************************!*\
  !*** ./client/src/performelement_multi_choice_single/tui.json ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"performelement_multi_choice_single\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"performelement_multi_choice_single\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"performelement_multi_choice_single\")\ntui._bundle.addModulesFromContext(\"performelement_multi_choice_single/components\", __webpack_require__(\"./client/src/performelement_multi_choice_single/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"performelement_multi_choice_single\": [\n      \"error_question_required\",\n      \"question_title\",\n      \"answer_text\",\n      \"single_select_options\"\n  ],\n  \"mod_perform\": [\n      \"section_element_response_required\"\n  ],\n  \"moodle\": [\n    \"add\",\n    \"delete\"\n   ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n\"performelement_multi_choice_multi\": [\n  \"multi_select_options\"\n]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"performelement_multi_choice_single\": [\n    \"error_you_must_answer_this_question\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"performelement_multi_choice_single\": [\n    \"no_response_submitted\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var mod_perform_components_element_ElementAdminDisplay__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! mod_perform/components/element/ElementAdminDisplay */ \"mod_perform/components/element/ElementAdminDisplay\");\n/* harmony import */ var mod_perform_components_element_ElementAdminDisplay__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_ElementAdminDisplay__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/Radio */ \"tui/components/form/Radio\");\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/form/RadioGroup */ \"tui/components/form/RadioGroup\");\n/* harmony import */ var tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_2__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ElementAdminDisplay: mod_perform_components_element_ElementAdminDisplay__WEBPACK_IMPORTED_MODULE_0___default.a,\n    Radio: tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1___default.a,\n    RadioGroup: tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_2___default.a\n  },\n  props: {\n    title: String,\n    identifier: String,\n    type: Object,\n    data: Object,\n    isRequired: Boolean,\n    activityState: {\n      type: Object,\n      required: true\n    },\n    error: String\n  }\n});\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_icons_common_Add__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/icons/common/Add */ \"tui/components/icons/common/Add\");\n/* harmony import */ var tui_components_icons_common_Add__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_common_Add__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var mod_perform_components_element_admin_form_AdminFormMixin__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! mod_perform/components/element/admin_form/AdminFormMixin */ \"mod_perform/components/element/admin_form/AdminFormMixin\");\n/* harmony import */ var mod_perform_components_element_admin_form_AdminFormMixin__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_admin_form_AdminFormMixin__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/buttons/ButtonIcon */ \"tui/components/buttons/ButtonIcon\");\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_form_Checkbox__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/form/Checkbox */ \"tui/components/form/Checkbox\");\n/* harmony import */ var tui_components_form_Checkbox__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Checkbox__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var mod_perform_components_element_ElementAdminForm__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! mod_perform/components/element/ElementAdminForm */ \"mod_perform/components/element/ElementAdminForm\");\n/* harmony import */ var mod_perform_components_element_ElementAdminForm__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_ElementAdminForm__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var mod_perform_components_element_admin_form_ActionButtons__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! mod_perform/components/element/admin_form/ActionButtons */ \"mod_perform/components/element/admin_form/ActionButtons\");\n/* harmony import */ var mod_perform_components_element_admin_form_ActionButtons__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_admin_form_ActionButtons__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_components_uniform_FormText__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/components/uniform/FormText */ \"tui/components/uniform/FormText\");\n/* harmony import */ var tui_components_uniform_FormText__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform_FormText__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var mod_perform_components_element_admin_form_IdentifierInput__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! mod_perform/components/element/admin_form/IdentifierInput */ \"mod_perform/components/element/admin_form/IdentifierInput\");\n/* harmony import */ var mod_perform_components_element_admin_form_IdentifierInput__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_admin_form_IdentifierInput__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! tui/components/form/Repeater */ \"tui/components/form/Repeater\");\n/* harmony import */ var tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! tui/components/uniform */ \"tui/components/uniform\");\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform__WEBPACK_IMPORTED_MODULE_9__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n\nvar MIN_OPTIONS = 2;\nvar OPTION_PREFIX = 'option_';\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AddIcon: tui_components_icons_common_Add__WEBPACK_IMPORTED_MODULE_0___default.a,\n    ButtonIcon: tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_2___default.a,\n    Checkbox: tui_components_form_Checkbox__WEBPACK_IMPORTED_MODULE_3___default.a,\n    ElementAdminForm: mod_perform_components_element_ElementAdminForm__WEBPACK_IMPORTED_MODULE_4___default.a,\n    FieldArray: tui_components_uniform__WEBPACK_IMPORTED_MODULE_9__[\"FieldArray\"],\n    FormActionButtons: mod_perform_components_element_admin_form_ActionButtons__WEBPACK_IMPORTED_MODULE_5___default.a,\n    FormRow: tui_components_uniform__WEBPACK_IMPORTED_MODULE_9__[\"FormRow\"],\n    FormText: tui_components_uniform_FormText__WEBPACK_IMPORTED_MODULE_6___default.a,\n    IdentifierInput: mod_perform_components_element_admin_form_IdentifierInput__WEBPACK_IMPORTED_MODULE_7___default.a,\n    Repeater: tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_8___default.a,\n    Uniform: tui_components_uniform__WEBPACK_IMPORTED_MODULE_9__[\"Uniform\"]\n  },\n  mixins: [mod_perform_components_element_admin_form_AdminFormMixin__WEBPACK_IMPORTED_MODULE_1___default.a],\n  props: {\n    type: Object,\n    title: String,\n    rawTitle: String,\n    identifier: String,\n    isRequired: {\n      type: Boolean,\n      \"default\": false\n    },\n    activityState: {\n      type: Object,\n      required: true\n    },\n    data: Object,\n    rawData: Object,\n    error: String\n  },\n  data: function data() {\n    var initialValues = {\n      title: this.title,\n      rawTitle: this.rawTitle,\n      identifier: this.identifier,\n      responseRequired: this.isRequired,\n      answers: []\n    };\n\n    if (Object.keys(this.rawData).length == 0) {\n      initialValues.answers = ['', ''];\n    } else {\n      this.rawData.options.forEach(function (item) {\n        initialValues.answers.push(item.value);\n      });\n    }\n\n    return {\n      initialValues: initialValues,\n      minRows: MIN_OPTIONS,\n      responseRequired: this.isRequired\n    };\n  },\n  methods: {\n    /**\n     * Handle multi choice single element submit data\n     * @param values\n     */\n    handleSubmit: function handleSubmit(values) {\n      var optionList = [];\n      values.answers.forEach(function (item, index) {\n        optionList.push({\n          name: OPTION_PREFIX + index,\n          value: item\n        });\n      });\n      this.$emit('update', {\n        title: values.rawTitle,\n        identifier: values.identifier,\n        data: {\n          options: optionList\n        },\n        is_required: this.responseRequired\n      });\n    },\n\n    /**\n     * Cancel edit form\n     */\n    cancel: function cancel() {\n      this.$emit('display');\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var mod_perform_components_element_ElementAdminReadOnlyDisplay__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! mod_perform/components/element/ElementAdminReadOnlyDisplay */ \"mod_perform/components/element/ElementAdminReadOnlyDisplay\");\n/* harmony import */ var mod_perform_components_element_ElementAdminReadOnlyDisplay__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_ElementAdminReadOnlyDisplay__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/FormRow */ \"tui/components/form/FormRow\");\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    FormRow: tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1___default.a,\n    ElementAdminReadOnlyDisplay: mod_perform_components_element_ElementAdminReadOnlyDisplay__WEBPACK_IMPORTED_MODULE_0___default.a\n  },\n  props: {\n    title: String,\n    identifier: String,\n    type: Object,\n    data: Object,\n    isRequired: Boolean,\n    error: String,\n    activityState: {\n      type: Object,\n      required: true\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/reform/FormScope */ \"tui/components/reform/FormScope\");\n/* harmony import */ var tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/Radio */ \"tui/components/form/Radio\");\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_uniform_FormRadioGroup__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/uniform/FormRadioGroup */ \"tui/components/uniform/FormRadioGroup\");\n/* harmony import */ var tui_components_uniform_FormRadioGroup__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform_FormRadioGroup__WEBPACK_IMPORTED_MODULE_2__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    FormScope: tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_0___default.a,\n    Radio: tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1___default.a,\n    FormRadioGroup: tui_components_uniform_FormRadioGroup__WEBPACK_IMPORTED_MODULE_2___default.a\n  },\n  props: {\n    path: [String, Array],\n    error: String,\n    element: Object\n  },\n  methods: {\n    /**\n     * answer validator based on element config\n     *\n     * @return {function[]}\n     */\n    answerValidator: function answerValidator(val) {\n      if (this.element.is_required) {\n        var isEmpty = !val || typeof val === 'string' && val.trim().length === 0;\n        if (isEmpty) return this.$str('error_you_must_answer_this_question', 'performelement_multi_choice_single');\n      }\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  props: {\n    data: Object,\n    element: Object\n  },\n  computed: {\n    answerOption: {\n      get: function get() {\n        var _this = this;\n\n        var optionValue = '';\n\n        if (this.data) {\n          this.element.data.options.forEach(function (item) {\n            if (item.name == _this.data.answer_option) {\n              optionValue = item.value;\n            }\n          });\n        }\n\n        return optionValue;\n      },\n      set: function set(newValue) {\n        if (!this.data) {\n          this.data = {};\n        }\n\n        this.data.answer_option = newValue;\n      }\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?vue&type=template&id=1b33b604&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?vue&type=template&id=1b33b604& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"ElementAdminDisplay\", {\n    attrs: {\n      type: _vm.type,\n      title: _vm.title,\n      identifier: _vm.identifier,\n      error: _vm.error,\n      \"is-required\": _vm.isRequired,\n      \"activity-state\": _vm.activityState\n    },\n    on: {\n      edit: function($event) {\n        return _vm.$emit(\"edit\")\n      },\n      remove: function($event) {\n        return _vm.$emit(\"remove\")\n      },\n      \"display-read\": function($event) {\n        return _vm.$emit(\"display-read\")\n      }\n    },\n    scopedSlots: _vm._u([\n      {\n        key: \"content\",\n        fn: function() {\n          return [\n            _c(\n              \"RadioGroup\",\n              { attrs: { disabled: true } },\n              _vm._l(_vm.data.options, function(item) {\n                return _c(\n                  \"Radio\",\n                  {\n                    key: item.name,\n                    attrs: { name: item.name, value: \"item.value\" }\n                  },\n                  [_vm._v(_vm._s(item.value))]\n                )\n              }),\n              1\n            )\n          ]\n        },\n        proxy: true\n      }\n    ])\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminDisplay.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=template&id=0e8051f2&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?vue&type=template&id=0e8051f2& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"ElementAdminForm\", {\n    attrs: {\n      type: _vm.type,\n      error: _vm.error,\n      \"activity-state\": _vm.activityState\n    },\n    on: {\n      remove: function($event) {\n        return _vm.$emit(\"remove\")\n      }\n    },\n    scopedSlots: _vm._u([\n      {\n        key: \"content\",\n        fn: function() {\n          return [\n            _c(\n              \"div\",\n              { staticClass: \"tui-elementEditMultiChoiceSingle\" },\n              [\n                _vm.initialValues\n                  ? _c(\"Uniform\", {\n                      attrs: {\n                        \"initial-values\": _vm.initialValues,\n                        vertical: true,\n                        \"input-width\": \"full\"\n                      },\n                      on: { submit: _vm.handleSubmit },\n                      scopedSlots: _vm._u(\n                        [\n                          {\n                            key: \"default\",\n                            fn: function(ref) {\n                              var getSubmitting = ref.getSubmitting\n                              return [\n                                _c(\n                                  \"FormRow\",\n                                  {\n                                    attrs: {\n                                      label: _vm.$str(\n                                        \"question_title\",\n                                        \"performelement_multi_choice_single\"\n                                      )\n                                    }\n                                  },\n                                  [\n                                    _c(\"FormText\", {\n                                      attrs: {\n                                        name: \"rawTitle\",\n                                        validations: function(v) {\n                                          return [\n                                            v.required(),\n                                            v.maxLength(1024)\n                                          ]\n                                        }\n                                      }\n                                    })\n                                  ],\n                                  1\n                                ),\n                                _vm._v(\" \"),\n                                _c(\n                                  \"FormRow\",\n                                  {\n                                    attrs: {\n                                      label: _vm.$str(\n                                        \"single_select_options\",\n                                        \"performelement_multi_choice_single\"\n                                      )\n                                    }\n                                  },\n                                  [\n                                    _c(\"FieldArray\", {\n                                      attrs: { path: \"answers\" },\n                                      scopedSlots: _vm._u(\n                                        [\n                                          {\n                                            key: \"default\",\n                                            fn: function(ref) {\n                                              var items = ref.items\n                                              var push = ref.push\n                                              var remove = ref.remove\n                                              return [\n                                                _c(\"Repeater\", {\n                                                  attrs: {\n                                                    rows: items,\n                                                    \"min-rows\": _vm.minRows,\n                                                    \"delete-icon\": true,\n                                                    \"allow-deleting-first-items\": false\n                                                  },\n                                                  on: {\n                                                    add: function($event) {\n                                                      return push()\n                                                    },\n                                                    remove: function(item, i) {\n                                                      return remove(i)\n                                                    }\n                                                  },\n                                                  scopedSlots: _vm._u(\n                                                    [\n                                                      {\n                                                        key: \"default\",\n                                                        fn: function(ref) {\n                                                          var row = ref.row\n                                                          var index = ref.index\n                                                          return [\n                                                            _c(\n                                                              \"div\",\n                                                              {\n                                                                staticClass:\n                                                                  \"tui-elementEditMultiChoiceSingle__option\"\n                                                              },\n                                                              [\n                                                                _c(\"FormText\", {\n                                                                  attrs: {\n                                                                    name: [\n                                                                      index\n                                                                    ],\n                                                                    validations: function(\n                                                                      v\n                                                                    ) {\n                                                                      return [\n                                                                        v.required()\n                                                                      ]\n                                                                    },\n                                                                    \"aria-label\": _vm.$str(\n                                                                      \"answer_text\",\n                                                                      \"performelement_multi_choice_single\",\n                                                                      index + 1\n                                                                    )\n                                                                  }\n                                                                })\n                                                              ],\n                                                              1\n                                                            )\n                                                          ]\n                                                        }\n                                                      },\n                                                      {\n                                                        key: \"add\",\n                                                        fn: function() {\n                                                          return [\n                                                            _c(\n                                                              \"ButtonIcon\",\n                                                              {\n                                                                staticClass:\n                                                                  \"tui-elementEditMultiChoiceSingle__add-option\",\n                                                                attrs: {\n                                                                  \"aria-label\": _vm.$str(\n                                                                    \"add\",\n                                                                    \"moodle\"\n                                                                  ),\n                                                                  styleclass: {\n                                                                    small: true\n                                                                  }\n                                                                },\n                                                                on: {\n                                                                  click: function(\n                                                                    $event\n                                                                  ) {\n                                                                    return push()\n                                                                  }\n                                                                }\n                                                              },\n                                                              [_c(\"AddIcon\")],\n                                                              1\n                                                            )\n                                                          ]\n                                                        },\n                                                        proxy: true\n                                                      }\n                                                    ],\n                                                    null,\n                                                    true\n                                                  )\n                                                })\n                                              ]\n                                            }\n                                          }\n                                        ],\n                                        null,\n                                        true\n                                      )\n                                    })\n                                  ],\n                                  1\n                                ),\n                                _vm._v(\" \"),\n                                _c(\n                                  \"FormRow\",\n                                  [\n                                    _c(\n                                      \"Checkbox\",\n                                      {\n                                        attrs: { name: \"responseRequired\" },\n                                        model: {\n                                          value: _vm.responseRequired,\n                                          callback: function($$v) {\n                                            _vm.responseRequired = $$v\n                                          },\n                                          expression: \"responseRequired\"\n                                        }\n                                      },\n                                      [\n                                        _vm._v(\n                                          \"\\n            \" +\n                                            _vm._s(\n                                              _vm.$str(\n                                                \"section_element_response_required\",\n                                                \"mod_perform\"\n                                              )\n                                            ) +\n                                            \"\\n          \"\n                                        )\n                                      ]\n                                    )\n                                  ],\n                                  1\n                                ),\n                                _vm._v(\" \"),\n                                _c(\"IdentifierInput\"),\n                                _vm._v(\" \"),\n                                _c(\"FormRow\", [\n                                  _c(\n                                    \"div\",\n                                    {\n                                      staticClass:\n                                        \"tui-elementEditMultiChoiceSingle__action-buttons\"\n                                    },\n                                    [\n                                      _c(\"FormActionButtons\", {\n                                        attrs: { submitting: getSubmitting() },\n                                        on: { cancel: _vm.cancel }\n                                      })\n                                    ],\n                                    1\n                                  )\n                                ])\n                              ]\n                            }\n                          }\n                        ],\n                        null,\n                        false,\n                        2583066663\n                      )\n                    })\n                  : _vm._e()\n              ],\n              1\n            )\n          ]\n        },\n        proxy: true\n      }\n    ])\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=template&id=de6c1f7c&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?vue&type=template&id=de6c1f7c& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"ElementAdminReadOnlyDisplay\", {\n    attrs: {\n      type: _vm.type,\n      title: _vm.title,\n      identifier: _vm.identifier,\n      \"is-required\": _vm.isRequired,\n      \"activity-state\": _vm.activityState\n    },\n    on: {\n      display: function($event) {\n        return _vm.$emit(\"display\")\n      }\n    },\n    scopedSlots: _vm._u([\n      {\n        key: \"content\",\n        fn: function() {\n          return [\n            _c(\n              \"FormRow\",\n              {\n                attrs: {\n                  label: _vm.$str(\n                    \"multi_select_options\",\n                    \"performelement_multi_choice_multi\"\n                  )\n                }\n              },\n              [\n                _c(\n                  \"div\",\n                  {\n                    staticClass:\n                      \"tui-multiChoiceSingleElementAdminReadOnlyDisplay__options\"\n                  },\n                  _vm._l(_vm.data.options, function(item) {\n                    return _c(\n                      \"div\",\n                      {\n                        key: item.name,\n                        staticClass:\n                          \"tui-multiChoiceSingleElementAdminReadOnlyDisplay__options-item\"\n                      },\n                      [\n                        _vm._v(\n                          \"\\n          \" + _vm._s(item.value) + \"\\n        \"\n                        )\n                      ]\n                    )\n                  }),\n                  0\n                )\n              ]\n            )\n          ]\n        },\n        proxy: true\n      }\n    ])\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementAdminReadOnlyDisplay.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=template&id=1cfbbed4&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?vue&type=template&id=1cfbbed4& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"FormScope\",\n    { attrs: { path: _vm.path } },\n    [\n      _c(\n        \"FormRadioGroup\",\n        { attrs: { validate: _vm.answerValidator, name: \"answer_option\" } },\n        _vm._l(_vm.element.data.options, function(item) {\n          return _c(\"Radio\", { key: item.name, attrs: { value: item.name } }, [\n            _vm._v(_vm._s(item.value))\n          ])\n        }),\n        1\n      )\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=template&id=1dd6f493&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?vue&type=template&id=1dd6f493& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-elementEditMultiChoiceSingleParticipantResponse\" },\n    [\n      _vm.answerOption\n        ? _c(\n            \"div\",\n            {\n              staticClass:\n                \"tui-elementEditMultiChoiceSingleParticipantResponse__answer\"\n            },\n            [_vm._v(\"\\n    \" + _vm._s(_vm.answerOption) + \"\\n  \")]\n          )\n        : _c(\n            \"div\",\n            {\n              staticClass:\n                \"tui-elementEditMultiChoiceSingleParticipantResponse__noResponse\"\n            },\n            [\n              _vm._v(\n                \"\\n    \" +\n                  _vm._s(\n                    _vm.$str(\n                      \"no_response_submitted\",\n                      \"performelement_multi_choice_single\"\n                    )\n                  ) +\n                  \"\\n  \"\n              )\n            ]\n          )\n    ]\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/performelement_multi_choice_single/components/MultiChoiceSingleElementParticipantResponse.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "mod_perform/components/element/ElementAdminDisplay":
/*!**************************************************************************************!*\
  !*** external "tui.require(\"mod_perform/components/element/ElementAdminDisplay\")" ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"mod_perform/components/element/ElementAdminDisplay\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22mod_perform/components/element/ElementAdminDisplay\\%22)%22?");

/***/ }),

/***/ "mod_perform/components/element/ElementAdminForm":
/*!***********************************************************************************!*\
  !*** external "tui.require(\"mod_perform/components/element/ElementAdminForm\")" ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"mod_perform/components/element/ElementAdminForm\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22mod_perform/components/element/ElementAdminForm\\%22)%22?");

/***/ }),

/***/ "mod_perform/components/element/ElementAdminReadOnlyDisplay":
/*!**********************************************************************************************!*\
  !*** external "tui.require(\"mod_perform/components/element/ElementAdminReadOnlyDisplay\")" ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"mod_perform/components/element/ElementAdminReadOnlyDisplay\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22mod_perform/components/element/ElementAdminReadOnlyDisplay\\%22)%22?");

/***/ }),

/***/ "mod_perform/components/element/admin_form/ActionButtons":
/*!*******************************************************************************************!*\
  !*** external "tui.require(\"mod_perform/components/element/admin_form/ActionButtons\")" ***!
  \*******************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"mod_perform/components/element/admin_form/ActionButtons\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22mod_perform/components/element/admin_form/ActionButtons\\%22)%22?");

/***/ }),

/***/ "mod_perform/components/element/admin_form/AdminFormMixin":
/*!********************************************************************************************!*\
  !*** external "tui.require(\"mod_perform/components/element/admin_form/AdminFormMixin\")" ***!
  \********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"mod_perform/components/element/admin_form/AdminFormMixin\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22mod_perform/components/element/admin_form/AdminFormMixin\\%22)%22?");

/***/ }),

/***/ "mod_perform/components/element/admin_form/IdentifierInput":
/*!*********************************************************************************************!*\
  !*** external "tui.require(\"mod_perform/components/element/admin_form/IdentifierInput\")" ***!
  \*********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"mod_perform/components/element/admin_form/IdentifierInput\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22mod_perform/components/element/admin_form/IdentifierInput\\%22)%22?");

/***/ }),

/***/ "tui/components/buttons/ButtonIcon":
/*!*********************************************************************!*\
  !*** external "tui.require(\"tui/components/buttons/ButtonIcon\")" ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/buttons/ButtonIcon\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/buttons/ButtonIcon\\%22)%22?");

/***/ }),

/***/ "tui/components/form/Checkbox":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/form/Checkbox\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/Checkbox\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/Checkbox\\%22)%22?");

/***/ }),

/***/ "tui/components/form/FormRow":
/*!***************************************************************!*\
  !*** external "tui.require(\"tui/components/form/FormRow\")" ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/FormRow\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/FormRow\\%22)%22?");

/***/ }),

/***/ "tui/components/form/Radio":
/*!*************************************************************!*\
  !*** external "tui.require(\"tui/components/form/Radio\")" ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/Radio\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/Radio\\%22)%22?");

/***/ }),

/***/ "tui/components/form/RadioGroup":
/*!******************************************************************!*\
  !*** external "tui.require(\"tui/components/form/RadioGroup\")" ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/RadioGroup\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/RadioGroup\\%22)%22?");

/***/ }),

/***/ "tui/components/form/Repeater":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/form/Repeater\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/Repeater\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/Repeater\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/common/Add":
/*!*******************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/common/Add\")" ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/common/Add\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/common/Add\\%22)%22?");

/***/ }),

/***/ "tui/components/reform/FormScope":
/*!*******************************************************************!*\
  !*** external "tui.require(\"tui/components/reform/FormScope\")" ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/reform/FormScope\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/reform/FormScope\\%22)%22?");

/***/ }),

/***/ "tui/components/uniform":
/*!**********************************************************!*\
  !*** external "tui.require(\"tui/components/uniform\")" ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/uniform\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/uniform\\%22)%22?");

/***/ }),

/***/ "tui/components/uniform/FormRadioGroup":
/*!*************************************************************************!*\
  !*** external "tui.require(\"tui/components/uniform/FormRadioGroup\")" ***!
  \*************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/uniform/FormRadioGroup\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/uniform/FormRadioGroup\\%22)%22?");

/***/ }),

/***/ "tui/components/uniform/FormText":
/*!*******************************************************************!*\
  !*** external "tui.require(\"tui/components/uniform/FormText\")" ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/uniform/FormText\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/uniform/FormText\\%22)%22?");

/***/ })

/******/ });