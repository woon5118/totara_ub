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
/******/ 		"performelement_static_content.development": 0
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
/******/ 	deferredModules.push(["./client/component/performelement_static_content/src/tui.json","tui/build/vendors.development"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/component/performelement_static_content/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!**********************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \**********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./StaticContentElementAdminDisplay\": \"./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue\",\n\t\"./StaticContentElementAdminDisplay.vue\": \"./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue\",\n\t\"./StaticContentElementAdminForm\": \"./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue\",\n\t\"./StaticContentElementAdminForm.vue\": \"./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue\",\n\t\"./StaticContentElementAdminReadOnlyDisplay\": \"./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue\",\n\t\"./StaticContentElementAdminReadOnlyDisplay.vue\": \"./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue\",\n\t\"./StaticContentElementParticipant\": \"./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue\",\n\t\"./StaticContentElementParticipant.vue\": \"./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/performelement_static_content/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/performelement_static_content/src/components_sync_^(?:(?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue":
/*!************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue ***!
  \************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _StaticContentElementAdminDisplay_vue_vue_type_template_id_4100ec47___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./StaticContentElementAdminDisplay.vue?vue&type=template&id=4100ec47& */ \"./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?vue&type=template&id=4100ec47&\");\n/* harmony import */ var _StaticContentElementAdminDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./StaticContentElementAdminDisplay.vue?vue&type=script&lang=js& */ \"./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _StaticContentElementAdminDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _StaticContentElementAdminDisplay_vue_vue_type_template_id_4100ec47___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _StaticContentElementAdminDisplay_vue_vue_type_template_id_4100ec47___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./StaticContentElementAdminDisplay.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?vue&type=template&id=4100ec47&":
/*!*******************************************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?vue&type=template&id=4100ec47& ***!
  \*******************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminDisplay_vue_vue_type_template_id_4100ec47___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./StaticContentElementAdminDisplay.vue?vue&type=template&id=4100ec47& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?vue&type=template&id=4100ec47&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminDisplay_vue_vue_type_template_id_4100ec47___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminDisplay_vue_vue_type_template_id_4100ec47___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue":
/*!*********************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue ***!
  \*********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _StaticContentElementAdminForm_vue_vue_type_template_id_daed0ae2___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./StaticContentElementAdminForm.vue?vue&type=template&id=daed0ae2& */ \"./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=template&id=daed0ae2&\");\n/* harmony import */ var _StaticContentElementAdminForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./StaticContentElementAdminForm.vue?vue&type=script&lang=js& */ \"./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _StaticContentElementAdminForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./StaticContentElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _StaticContentElementAdminForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _StaticContentElementAdminForm_vue_vue_type_template_id_daed0ae2___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _StaticContentElementAdminForm_vue_vue_type_template_id_daed0ae2___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _StaticContentElementAdminForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_StaticContentElementAdminForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!********************************************************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \********************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./StaticContentElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./StaticContentElementAdminForm.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=template&id=daed0ae2&":
/*!****************************************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=template&id=daed0ae2& ***!
  \****************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminForm_vue_vue_type_template_id_daed0ae2___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./StaticContentElementAdminForm.vue?vue&type=template&id=daed0ae2& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=template&id=daed0ae2&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminForm_vue_vue_type_template_id_daed0ae2___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminForm_vue_vue_type_template_id_daed0ae2___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue":
/*!********************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue ***!
  \********************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _StaticContentElementAdminReadOnlyDisplay_vue_vue_type_template_id_522b98f6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./StaticContentElementAdminReadOnlyDisplay.vue?vue&type=template&id=522b98f6& */ \"./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=template&id=522b98f6&\");\n/* harmony import */ var _StaticContentElementAdminReadOnlyDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./StaticContentElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js& */ \"./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _StaticContentElementAdminReadOnlyDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./StaticContentElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _StaticContentElementAdminReadOnlyDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _StaticContentElementAdminReadOnlyDisplay_vue_vue_type_template_id_522b98f6___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _StaticContentElementAdminReadOnlyDisplay_vue_vue_type_template_id_522b98f6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _StaticContentElementAdminReadOnlyDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_StaticContentElementAdminReadOnlyDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*******************************************************************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*******************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminReadOnlyDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./StaticContentElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminReadOnlyDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminReadOnlyDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./StaticContentElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminReadOnlyDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=template&id=522b98f6&":
/*!***************************************************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=template&id=522b98f6& ***!
  \***************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminReadOnlyDisplay_vue_vue_type_template_id_522b98f6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./StaticContentElementAdminReadOnlyDisplay.vue?vue&type=template&id=522b98f6& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=template&id=522b98f6&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminReadOnlyDisplay_vue_vue_type_template_id_522b98f6___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementAdminReadOnlyDisplay_vue_vue_type_template_id_522b98f6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue":
/*!***********************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue ***!
  \***********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _StaticContentElementParticipant_vue_vue_type_template_id_1e34c40f___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./StaticContentElementParticipant.vue?vue&type=template&id=1e34c40f& */ \"./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?vue&type=template&id=1e34c40f&\");\n/* harmony import */ var _StaticContentElementParticipant_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./StaticContentElementParticipant.vue?vue&type=script&lang=js& */ \"./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _StaticContentElementParticipant_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _StaticContentElementParticipant_vue_vue_type_template_id_1e34c40f___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _StaticContentElementParticipant_vue_vue_type_template_id_1e34c40f___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementParticipant_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./StaticContentElementParticipant.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementParticipant_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?vue&type=template&id=1e34c40f&":
/*!******************************************************************************************************************************************!*\
  !*** ./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?vue&type=template&id=1e34c40f& ***!
  \******************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementParticipant_vue_vue_type_template_id_1e34c40f___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./StaticContentElementParticipant.vue?vue&type=template&id=1e34c40f& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?vue&type=template&id=1e34c40f&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementParticipant_vue_vue_type_template_id_1e34c40f___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_StaticContentElementParticipant_vue_vue_type_template_id_1e34c40f___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?");

/***/ }),

/***/ "./client/component/performelement_static_content/src/tui.json":
/*!*********************************************************************!*\
  !*** ./client/component/performelement_static_content/src/tui.json ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"performelement_static_content\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"performelement_static_content\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"performelement_static_content\")\ntui._bundle.addModulesFromContext(\"performelement_static_content/components\", __webpack_require__(\"./client/component/performelement_static_content/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"performelement_static_content\": [\n    \"title\",\n    \"static_content_placeholder\",\n    \"weka_enter_content\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n\"performelement_static_content\": [\n  \"static_content_placeholder\"\n]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var mod_perform_components_element_ElementAdminDisplay__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! mod_perform/components/element/ElementAdminDisplay */ \"mod_perform/components/element/ElementAdminDisplay\");\n/* harmony import */ var mod_perform_components_element_ElementAdminDisplay__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_ElementAdminDisplay__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_tui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/tui */ \"tui/tui\");\n/* harmony import */ var tui_tui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_tui__WEBPACK_IMPORTED_MODULE_1__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n// Utils\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ElementAdminDisplay: (mod_perform_components_element_ElementAdminDisplay__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n\n  props: {\n    title: String,\n    type: Object,\n    data: Object,\n    error: String,\n    activityState: {\n      type: Object,\n      required: true,\n    },\n  },\n\n  mounted() {\n    this.$_scan();\n  },\n\n  updated() {\n    this.$_scan();\n  },\n\n  methods: {\n    $_scan() {\n      this.$nextTick().then(() => {\n        let content = this.$refs.content;\n        if (!content) {\n          return;\n        }\n\n        tui_tui__WEBPACK_IMPORTED_MODULE_1___default.a.scan(content);\n      });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/uniform */ \"tui/components/uniform\");\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var mod_perform_components_element_ElementAdminForm__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! mod_perform/components/element/ElementAdminForm */ \"mod_perform/components/element/ElementAdminForm\");\n/* harmony import */ var mod_perform_components_element_ElementAdminForm__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_ElementAdminForm__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var mod_perform_components_element_admin_form_ActionButtons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! mod_perform/components/element/admin_form/ActionButtons */ \"mod_perform/components/element/admin_form/ActionButtons\");\n/* harmony import */ var mod_perform_components_element_admin_form_ActionButtons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_admin_form_ActionButtons__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var mod_perform_components_element_admin_form_AdminFormMixin__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! mod_perform/components/element/admin_form/AdminFormMixin */ \"mod_perform/components/element/admin_form/AdminFormMixin\");\n/* harmony import */ var mod_perform_components_element_admin_form_AdminFormMixin__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_admin_form_AdminFormMixin__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! editor_weka/components/Weka */ \"editor_weka/components/Weka\");\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_util__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/util */ \"tui/util\");\n/* harmony import */ var tui_util__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_util__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var core_graphql_file_unused_draft_item_id__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core/graphql/file_unused_draft_item_id */ \"./server/lib/webapi/ajax/file_unused_draft_item_id.graphql\");\n/* harmony import */ var performelement_static_content_graphql_prepare_draft_area__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! performelement_static_content/graphql/prepare_draft_area */ \"./server/mod/perform/element/static_content/webapi/ajax/prepare_draft_area.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n// Utils\n\n\n// GraphQL queries\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ElementAdminForm: (mod_perform_components_element_ElementAdminForm__WEBPACK_IMPORTED_MODULE_1___default()),\n    Uniform: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"Uniform\"],\n    FormRow: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"FormRow\"],\n    FormText: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"FormText\"],\n    FormActionButtons: (mod_perform_components_element_admin_form_ActionButtons__WEBPACK_IMPORTED_MODULE_2___default()),\n    Weka: (editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_4___default()),\n  },\n\n  mixins: [mod_perform_components_element_admin_form_AdminFormMixin__WEBPACK_IMPORTED_MODULE_3___default.a],\n\n  props: {\n    sectionId: [Number, String],\n    elementId: [Number, String],\n    type: Object,\n    title: String,\n    rawTitle: String,\n    data: Object,\n    rawData: Object,\n    error: String,\n    activityState: {\n      type: Object,\n      required: true,\n    },\n  },\n\n  data() {\n    const initialValues = {\n      title: this.title,\n      rawTitle: this.rawTitle,\n      data: this.data,\n    };\n    return {\n      initialValues: initialValues,\n      wekaDoc: null,\n      draftId: null,\n    };\n  },\n\n  async mounted() {\n    if (this.rawData && this.rawData.wekaDoc) {\n      this.wekaDoc = JSON.parse(this.rawData.wekaDoc);\n    }\n    if (this.sectionId && this.elementId) {\n      await this.$_loadExistingDraftId();\n    } else {\n      await this.$_loadNewDraftId();\n    }\n  },\n\n  methods: {\n    /**\n     *\n     * @param {Object} opt\n     */\n    handleUpdate(opt) {\n      this.$_readJson(opt);\n    },\n\n    async $_loadNewDraftId() {\n      const {\n        data: { item_id },\n      } = await this.$apollo.mutate({ mutation: core_graphql_file_unused_draft_item_id__WEBPACK_IMPORTED_MODULE_6__[\"default\"] });\n      this.draftId = item_id;\n    },\n\n    async $_loadExistingDraftId() {\n      const {\n        data: { draft_id },\n      } = await this.$apollo.mutate({\n        mutation: performelement_static_content_graphql_prepare_draft_area__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n        variables: {\n          section_id: parseInt(this.sectionId),\n          element_id: parseInt(this.elementId),\n        },\n      });\n      this.draftId = draft_id;\n    },\n\n    $_readJson: Object(tui_util__WEBPACK_IMPORTED_MODULE_5__[\"debounce\"])(\n      /**\n       *\n       * @param {Object} opt\n       */\n      function(opt) {\n        this.wekaDoc = opt.getJSON();\n      },\n      250,\n      { perArgs: false }\n    ),\n\n    handleSubmit(values) {\n      this.$emit('update', {\n        title: values.rawTitle,\n        data: {\n          wekaDoc: this.wekaDoc ? JSON.stringify(this.wekaDoc) : null,\n          draftId: this.draftId,\n          format: 'HTML',\n          docFormat: 'FORMAT_JSON_EDITOR',\n        },\n      });\n    },\n\n    cancel() {\n      this.$emit('display');\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var mod_perform_components_element_ElementAdminReadOnlyDisplay__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! mod_perform/components/element/ElementAdminReadOnlyDisplay */ \"mod_perform/components/element/ElementAdminReadOnlyDisplay\");\n/* harmony import */ var mod_perform_components_element_ElementAdminReadOnlyDisplay__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_ElementAdminReadOnlyDisplay__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/FormRow */ \"tui/components/form/FormRow\");\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_tui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/tui */ \"tui/tui\");\n/* harmony import */ var tui_tui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_tui__WEBPACK_IMPORTED_MODULE_2__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n// Utils\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ElementAdminReadOnlyDisplay: (mod_perform_components_element_ElementAdminReadOnlyDisplay__WEBPACK_IMPORTED_MODULE_0___default()),\n    FormRow: (tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1___default()),\n  },\n\n  props: {\n    title: String,\n    identifier: String,\n    type: Object,\n    data: Object,\n    isRequired: Boolean,\n    activityState: {\n      type: Object,\n      required: true,\n    },\n    error: String,\n  },\n\n  mounted() {\n    this.$_scan();\n  },\n\n  updated() {\n    this.$_scan();\n  },\n\n  methods: {\n    $_scan() {\n      this.$nextTick().then(() => {\n        let content = this.$refs.content;\n        if (!content) {\n          return;\n        }\n\n        tui_tui__WEBPACK_IMPORTED_MODULE_2___default.a.scan(content);\n      });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_tui__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/tui */ \"tui/tui\");\n/* harmony import */ var tui_tui__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_tui__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n// Utils\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  props: {\n    element: Object,\n  },\n\n  mounted() {\n    this.$_scan();\n  },\n\n  updated() {\n    this.$_scan();\n  },\n\n  methods: {\n    $_scan() {\n      this.$nextTick().then(() => {\n        let content = this.$refs.content;\n        if (!content) {\n          return;\n        }\n\n        tui_tui__WEBPACK_IMPORTED_MODULE_0___default.a.scan(content);\n      });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?vue&type=template&id=4100ec47&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?vue&type=template&id=4100ec47& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"ElementAdminDisplay\", {\n    attrs: {\n      type: _vm.type,\n      title: _vm.title,\n      error: _vm.error,\n      \"activity-state\": _vm.activityState\n    },\n    on: {\n      edit: function($event) {\n        return _vm.$emit(\"edit\")\n      },\n      remove: function($event) {\n        return _vm.$emit(\"remove\")\n      },\n      \"display-read\": function($event) {\n        return _vm.$emit(\"display-read\")\n      }\n    },\n    scopedSlots: _vm._u([\n      {\n        key: \"content\",\n        fn: function() {\n          return [\n            _c(\"div\", {\n              ref: \"content\",\n              staticClass: \"tui-staticContentElementAdminDisplay\",\n              domProps: { innerHTML: _vm._s(_vm.data.content) }\n            })\n          ]\n        },\n        proxy: true\n      }\n    ])\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminDisplay.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=template&id=daed0ae2&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?vue&type=template&id=daed0ae2& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"ElementAdminForm\", {\n    attrs: {\n      type: _vm.type,\n      error: _vm.error,\n      \"activity-state\": _vm.activityState\n    },\n    on: {\n      remove: function($event) {\n        return _vm.$emit(\"remove\")\n      }\n    },\n    scopedSlots: _vm._u([\n      {\n        key: \"content\",\n        fn: function() {\n          return [\n            _c(\n              \"div\",\n              { staticClass: \"tui-elementEditStaticContent\" },\n              [\n                _c(\"Uniform\", {\n                  attrs: {\n                    \"initial-values\": _vm.initialValues,\n                    vertical: true,\n                    \"validation-mode\": \"submit\",\n                    \"input-width\": \"full\"\n                  },\n                  on: { submit: _vm.handleSubmit },\n                  scopedSlots: _vm._u([\n                    {\n                      key: \"default\",\n                      fn: function(ref) {\n                        var getSubmitting = ref.getSubmitting\n                        return [\n                          _c(\n                            \"FormRow\",\n                            {\n                              attrs: {\n                                label: _vm.$str(\n                                  \"title\",\n                                  \"performelement_static_content\"\n                                )\n                              }\n                            },\n                            [\n                              _c(\"FormText\", {\n                                attrs: {\n                                  name: \"rawTitle\",\n                                  validations: function(v) {\n                                    return [v.maxLength(1024)]\n                                  }\n                                }\n                              })\n                            ],\n                            1\n                          ),\n                          _vm._v(\" \"),\n                          _c(\"FormRow\", {\n                            attrs: {\n                              label: _vm.$str(\n                                \"static_content_placeholder\",\n                                \"performelement_static_content\"\n                              ),\n                              required: true\n                            },\n                            scopedSlots: _vm._u(\n                              [\n                                {\n                                  key: \"default\",\n                                  fn: function(ref) {\n                                    var id = ref.id\n                                    return [\n                                      _vm.draftId\n                                        ? _c(\"Weka\", {\n                                            attrs: {\n                                              id: id,\n                                              component:\n                                                \"performelement_static_content\",\n                                              area: \"content\",\n                                              doc: _vm.wekaDoc,\n                                              \"file-item-id\": _vm.draftId,\n                                              placeholder: _vm.$str(\n                                                \"weka_enter_content\",\n                                                \"performelement_static_content\"\n                                              )\n                                            },\n                                            on: { update: _vm.handleUpdate }\n                                          })\n                                        : _vm._e()\n                                    ]\n                                  }\n                                }\n                              ],\n                              null,\n                              true\n                            )\n                          }),\n                          _vm._v(\" \"),\n                          _c(\"FormRow\", [\n                            _c(\n                              \"div\",\n                              {\n                                staticClass:\n                                  \"tui-elementEditStaticContent__action-buttons\"\n                              },\n                              [\n                                _c(\"FormActionButtons\", {\n                                  attrs: { submitting: getSubmitting() },\n                                  on: { cancel: _vm.cancel }\n                                })\n                              ],\n                              1\n                            )\n                          ])\n                        ]\n                      }\n                    }\n                  ])\n                })\n              ],\n              1\n            )\n          ]\n        },\n        proxy: true\n      }\n    ])\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=template&id=522b98f6&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?vue&type=template&id=522b98f6& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"ElementAdminReadOnlyDisplay\", {\n    attrs: {\n      type: _vm.type,\n      title: _vm.title,\n      \"activity-state\": _vm.activityState,\n      \"is-static\": true\n    },\n    on: {\n      display: function($event) {\n        return _vm.$emit(\"display\")\n      }\n    },\n    scopedSlots: _vm._u([\n      {\n        key: \"content\",\n        fn: function() {\n          return [\n            _c(\n              \"FormRow\",\n              {\n                attrs: {\n                  label: _vm.$str(\n                    \"static_content_placeholder\",\n                    \"performelement_static_content\"\n                  )\n                }\n              },\n              [\n                _c(\"div\", {\n                  ref: \"content\",\n                  domProps: { innerHTML: _vm._s(_vm.data.content) }\n                })\n              ]\n            )\n          ]\n        },\n        proxy: true\n      }\n    ])\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementAdminReadOnlyDisplay.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?vue&type=template&id=1e34c40f&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?vue&type=template&id=1e34c40f& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"div\", { staticClass: \"tui-staticContentElementParticipantForm\" }, [\n    _c(\"div\", {\n      ref: \"content\",\n      domProps: { innerHTML: _vm._s(_vm.element.data.content) }\n    })\n  ])\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_static_content/src/components/StaticContentElementParticipant.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

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

/***/ "./server/mod/perform/element/static_content/webapi/ajax/prepare_draft_area.graphql":
/*!******************************************************************************************!*\
  !*** ./server/mod/perform/element/static_content/webapi/ajax/prepare_draft_area.graphql ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"performelement_static_content_prepare_draft_area\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"section_id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"core_id\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"element_id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"core_id\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"draft_id\"},\"name\":{\"kind\":\"Name\",\"value\":\"performelement_static_content_prepare_draft_area\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"section_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"section_id\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"element_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"element_id\"}}}],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/mod/perform/element/static_content/webapi/ajax/prepare_draft_area.graphql?");

/***/ }),

/***/ "editor_weka/components/Weka":
/*!***************************************************************!*\
  !*** external "tui.require(\"editor_weka/components/Weka\")" ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"editor_weka/components/Weka\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22editor_weka/components/Weka\\%22)%22?");

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

/***/ "tui/components/form/FormRow":
/*!***************************************************************!*\
  !*** external "tui.require(\"tui/components/form/FormRow\")" ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/FormRow\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/FormRow\\%22)%22?");

/***/ }),

/***/ "tui/components/uniform":
/*!**********************************************************!*\
  !*** external "tui.require(\"tui/components/uniform\")" ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/uniform\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/uniform\\%22)%22?");

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