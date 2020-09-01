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
/******/ 		"engage_survey.legacy.development": 0
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
/******/ 	deferredModules.push(["./client/component/engage_survey/src/tui.json","tui/build/vendors.legacy.development"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/component/engage_survey/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!******************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \******************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./CreateSurvey\": \"./client/component/engage_survey/src/components/CreateSurvey.vue\",\n\t\"./CreateSurvey.vue\": \"./client/component/engage_survey/src/components/CreateSurvey.vue\",\n\t\"./box/RadioBox\": \"./client/component/engage_survey/src/components/box/RadioBox.vue\",\n\t\"./box/RadioBox.vue\": \"./client/component/engage_survey/src/components/box/RadioBox.vue\",\n\t\"./box/SquareBox\": \"./client/component/engage_survey/src/components/box/SquareBox.vue\",\n\t\"./box/SquareBox.vue\": \"./client/component/engage_survey/src/components/box/SquareBox.vue\",\n\t\"./card/SurveyCard\": \"./client/component/engage_survey/src/components/card/SurveyCard.vue\",\n\t\"./card/SurveyCard.vue\": \"./client/component/engage_survey/src/components/card/SurveyCard.vue\",\n\t\"./card/SurveyCardBody\": \"./client/component/engage_survey/src/components/card/SurveyCardBody.vue\",\n\t\"./card/SurveyCardBody.vue\": \"./client/component/engage_survey/src/components/card/SurveyCardBody.vue\",\n\t\"./card/SurveyResultBody\": \"./client/component/engage_survey/src/components/card/SurveyResultBody.vue\",\n\t\"./card/SurveyResultBody.vue\": \"./client/component/engage_survey/src/components/card/SurveyResultBody.vue\",\n\t\"./card/result/SurveyQuestionResult\": \"./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue\",\n\t\"./card/result/SurveyQuestionResult.vue\": \"./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue\",\n\t\"./content/SurveyResultContent\": \"./client/component/engage_survey/src/components/content/SurveyResultContent.vue\",\n\t\"./content/SurveyResultContent.vue\": \"./client/component/engage_survey/src/components/content/SurveyResultContent.vue\",\n\t\"./content/SurveyVoteContent\": \"./client/component/engage_survey/src/components/content/SurveyVoteContent.vue\",\n\t\"./content/SurveyVoteContent.vue\": \"./client/component/engage_survey/src/components/content/SurveyVoteContent.vue\",\n\t\"./content/SurveyVoteTitle\": \"./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue\",\n\t\"./content/SurveyVoteTitle.vue\": \"./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue\",\n\t\"./form/SurveyForm\": \"./client/component/engage_survey/src/components/form/SurveyForm.vue\",\n\t\"./form/SurveyForm.vue\": \"./client/component/engage_survey/src/components/form/SurveyForm.vue\",\n\t\"./info/Author\": \"./client/component/engage_survey/src/components/info/Author.vue\",\n\t\"./info/Author.vue\": \"./client/component/engage_survey/src/components/info/Author.vue\",\n\t\"./shape/SurveyBadge\": \"./client/component/engage_survey/src/components/shape/SurveyBadge.vue\",\n\t\"./shape/SurveyBadge.vue\": \"./client/component/engage_survey/src/components/shape/SurveyBadge.vue\",\n\t\"./sidepanel/SurveySidePanel\": \"./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue\",\n\t\"./sidepanel/SurveySidePanel.vue\": \"./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/engage_survey/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/engage_survey/src/components_sync_^(?:(?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/CreateSurvey.vue":
/*!************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/CreateSurvey.vue ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _CreateSurvey_vue_vue_type_template_id_a9924e36___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./CreateSurvey.vue?vue&type=template&id=a9924e36& */ \"./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=template&id=a9924e36&\");\n/* harmony import */ var _CreateSurvey_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CreateSurvey.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _CreateSurvey_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./CreateSurvey.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _CreateSurvey_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_CreateSurvey_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _CreateSurvey_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _CreateSurvey_vue_vue_type_template_id_a9924e36___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _CreateSurvey_vue_vue_type_template_id_a9924e36___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/CreateSurvey.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/CreateSurvey.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateSurvey_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CreateSurvey.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateSurvey_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/CreateSurvey.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=style&index=0&lang=scss&":
/*!**********************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=style&index=0&lang=scss& ***!
  \**********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/CreateSurvey.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=template&id=a9924e36&":
/*!*******************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=template&id=a9924e36& ***!
  \*******************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateSurvey_vue_vue_type_template_id_a9924e36___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CreateSurvey.vue?vue&type=template&id=a9924e36& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=template&id=a9924e36&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateSurvey_vue_vue_type_template_id_a9924e36___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateSurvey_vue_vue_type_template_id_a9924e36___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/CreateSurvey.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/box/RadioBox.vue":
/*!************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/box/RadioBox.vue ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _RadioBox_vue_vue_type_template_id_23488603___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RadioBox.vue?vue&type=template&id=23488603& */ \"./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=template&id=23488603&\");\n/* harmony import */ var _RadioBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RadioBox.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _RadioBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./RadioBox.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _RadioBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_RadioBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _RadioBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _RadioBox_vue_vue_type_template_id_23488603___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _RadioBox_vue_vue_type_template_id_23488603___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/box/RadioBox.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/box/RadioBox.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RadioBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./RadioBox.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RadioBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/box/RadioBox.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=style&index=0&lang=scss&":
/*!**********************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=style&index=0&lang=scss& ***!
  \**********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/box/RadioBox.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=template&id=23488603&":
/*!*******************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=template&id=23488603& ***!
  \*******************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RadioBox_vue_vue_type_template_id_23488603___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./RadioBox.vue?vue&type=template&id=23488603& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=template&id=23488603&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RadioBox_vue_vue_type_template_id_23488603___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RadioBox_vue_vue_type_template_id_23488603___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/box/RadioBox.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/box/SquareBox.vue":
/*!*************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/box/SquareBox.vue ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SquareBox_vue_vue_type_template_id_ee032a6a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SquareBox.vue?vue&type=template&id=ee032a6a& */ \"./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=template&id=ee032a6a&\");\n/* harmony import */ var _SquareBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SquareBox.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SquareBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SquareBox.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SquareBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_SquareBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SquareBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SquareBox_vue_vue_type_template_id_ee032a6a___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SquareBox_vue_vue_type_template_id_ee032a6a___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/box/SquareBox.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/box/SquareBox.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SquareBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SquareBox.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SquareBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/box/SquareBox.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=style&index=0&lang=scss&":
/*!***********************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=style&index=0&lang=scss& ***!
  \***********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/box/SquareBox.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=template&id=ee032a6a&":
/*!********************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=template&id=ee032a6a& ***!
  \********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SquareBox_vue_vue_type_template_id_ee032a6a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SquareBox.vue?vue&type=template&id=ee032a6a& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=template&id=ee032a6a&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SquareBox_vue_vue_type_template_id_ee032a6a___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SquareBox_vue_vue_type_template_id_ee032a6a___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/box/SquareBox.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyCard.vue":
/*!***************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyCard.vue ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyCard_vue_vue_type_template_id_e48cd9ec___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyCard.vue?vue&type=template&id=e48cd9ec& */ \"./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=template&id=e48cd9ec&\");\n/* harmony import */ var _SurveyCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyCard.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SurveyCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SurveyCard.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SurveyCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_SurveyCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SurveyCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyCard_vue_vue_type_template_id_e48cd9ec___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyCard_vue_vue_type_template_id_e48cd9ec___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_SurveyCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/card/SurveyCard.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCard.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCard.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyCard.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCard.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=style&index=0&lang=scss&":
/*!*************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=style&index=0&lang=scss& ***!
  \*************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCard.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=template&id=e48cd9ec&":
/*!**********************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=template&id=e48cd9ec& ***!
  \**********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_template_id_e48cd9ec___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyCard.vue?vue&type=template&id=e48cd9ec& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=template&id=e48cd9ec&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_template_id_e48cd9ec___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_template_id_e48cd9ec___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCard.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyCardBody.vue":
/*!*******************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyCardBody.vue ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyCardBody_vue_vue_type_template_id_9e81f268___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyCardBody.vue?vue&type=template&id=9e81f268& */ \"./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=template&id=9e81f268&\");\n/* harmony import */ var _SurveyCardBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyCardBody.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SurveyCardBody_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SurveyCardBody.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SurveyCardBody_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_SurveyCardBody_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyCardBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SurveyCardBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyCardBody_vue_vue_type_template_id_9e81f268___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyCardBody_vue_vue_type_template_id_9e81f268___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyCardBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_SurveyCardBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/card/SurveyCardBody.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCardBody.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!******************************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \******************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCardBody.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyCardBody.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCardBody.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=style&index=0&lang=scss&":
/*!*****************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=style&index=0&lang=scss& ***!
  \*****************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCardBody.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=template&id=9e81f268&":
/*!**************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=template&id=9e81f268& ***!
  \**************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_template_id_9e81f268___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyCardBody.vue?vue&type=template&id=9e81f268& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=template&id=9e81f268&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_template_id_9e81f268___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_template_id_9e81f268___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCardBody.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyResultBody.vue":
/*!*********************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyResultBody.vue ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyResultBody_vue_vue_type_template_id_529d334e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyResultBody.vue?vue&type=template&id=529d334e& */ \"./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=template&id=529d334e&\");\n/* harmony import */ var _SurveyResultBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyResultBody.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SurveyResultBody_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SurveyResultBody.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SurveyResultBody_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_SurveyResultBody_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyResultBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SurveyResultBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyResultBody_vue_vue_type_template_id_529d334e___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyResultBody_vue_vue_type_template_id_529d334e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyResultBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_SurveyResultBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/card/SurveyResultBody.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyResultBody.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!********************************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \********************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyResultBody.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyResultBody.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyResultBody.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyResultBody.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=template&id=529d334e&":
/*!****************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=template&id=529d334e& ***!
  \****************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_template_id_529d334e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyResultBody.vue?vue&type=template&id=529d334e& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=template&id=529d334e&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_template_id_529d334e___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_template_id_529d334e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyResultBody.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue":
/*!********************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue ***!
  \********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyQuestionResult_vue_vue_type_template_id_5954f8bf___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyQuestionResult.vue?vue&type=template&id=5954f8bf& */ \"./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=template&id=5954f8bf&\");\n/* harmony import */ var _SurveyQuestionResult_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyQuestionResult.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SurveyQuestionResult_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SurveyQuestionResult.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SurveyQuestionResult_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_SurveyQuestionResult_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyQuestionResult_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SurveyQuestionResult_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyQuestionResult_vue_vue_type_template_id_5954f8bf___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyQuestionResult_vue_vue_type_template_id_5954f8bf___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyQuestionResult_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_SurveyQuestionResult_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*******************************************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*******************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyQuestionResult.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=style&index=0&lang=scss&":
/*!******************************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=style&index=0&lang=scss& ***!
  \******************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=template&id=5954f8bf&":
/*!***************************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=template&id=5954f8bf& ***!
  \***************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_template_id_5954f8bf___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyQuestionResult.vue?vue&type=template&id=5954f8bf& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=template&id=5954f8bf&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_template_id_5954f8bf___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_template_id_5954f8bf___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyResultContent.vue":
/*!***************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyResultContent.vue ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyResultContent_vue_vue_type_template_id_421937ad___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyResultContent.vue?vue&type=template&id=421937ad& */ \"./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=template&id=421937ad&\");\n/* harmony import */ var _SurveyResultContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyResultContent.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SurveyResultContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SurveyResultContent.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SurveyResultContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_SurveyResultContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyResultContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SurveyResultContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyResultContent_vue_vue_type_template_id_421937ad___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyResultContent_vue_vue_type_template_id_421937ad___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyResultContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_SurveyResultContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/content/SurveyResultContent.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyResultContent.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyResultContent.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyResultContent.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyResultContent.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=style&index=0&lang=scss&":
/*!*************************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=style&index=0&lang=scss& ***!
  \*************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyResultContent.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=template&id=421937ad&":
/*!**********************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=template&id=421937ad& ***!
  \**********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_template_id_421937ad___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyResultContent.vue?vue&type=template&id=421937ad& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=template&id=421937ad&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_template_id_421937ad___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_template_id_421937ad___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyResultContent.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyVoteContent.vue":
/*!*************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyVoteContent.vue ***!
  \*************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyVoteContent_vue_vue_type_template_id_5650e500___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyVoteContent.vue?vue&type=template&id=5650e500& */ \"./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=template&id=5650e500&\");\n/* harmony import */ var _SurveyVoteContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyVoteContent.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyVoteContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyVoteContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyVoteContent_vue_vue_type_template_id_5650e500___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyVoteContent_vue_vue_type_template_id_5650e500___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyVoteContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveyVoteContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/content/SurveyVoteContent.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!************************************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteContent.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=template&id=5650e500&":
/*!********************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=template&id=5650e500& ***!
  \********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_template_id_5650e500___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteContent.vue?vue&type=template&id=5650e500& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=template&id=5650e500&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_template_id_5650e500___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_template_id_5650e500___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue":
/*!***********************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyVoteTitle_vue_vue_type_template_id_3d599b1f___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyVoteTitle.vue?vue&type=template&id=3d599b1f& */ \"./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=template&id=3d599b1f&\");\n/* harmony import */ var _SurveyVoteTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyVoteTitle.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SurveyVoteTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SurveyVoteTitle.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SurveyVoteTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_SurveyVoteTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SurveyVoteTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyVoteTitle_vue_vue_type_template_id_3d599b1f___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyVoteTitle_vue_vue_type_template_id_3d599b1f___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/content/SurveyVoteTitle.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteTitle.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=style&index=0&lang=scss&":
/*!*********************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=style&index=0&lang=scss& ***!
  \*********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=template&id=3d599b1f&":
/*!******************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=template&id=3d599b1f& ***!
  \******************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteTitle_vue_vue_type_template_id_3d599b1f___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteTitle.vue?vue&type=template&id=3d599b1f& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=template&id=3d599b1f&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteTitle_vue_vue_type_template_id_3d599b1f___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteTitle_vue_vue_type_template_id_3d599b1f___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/form/SurveyForm.vue":
/*!***************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/form/SurveyForm.vue ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyForm_vue_vue_type_template_id_7ce50aec___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyForm.vue?vue&type=template&id=7ce50aec& */ \"./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=template&id=7ce50aec&\");\n/* harmony import */ var _SurveyForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyForm.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SurveyForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SurveyForm.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SurveyForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_SurveyForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SurveyForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyForm_vue_vue_type_template_id_7ce50aec___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyForm_vue_vue_type_template_id_7ce50aec___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_SurveyForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/form/SurveyForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/form/SurveyForm.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/form/SurveyForm.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyForm.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/form/SurveyForm.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=style&index=0&lang=scss&":
/*!*************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=style&index=0&lang=scss& ***!
  \*************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/form/SurveyForm.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=template&id=7ce50aec&":
/*!**********************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=template&id=7ce50aec& ***!
  \**********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_template_id_7ce50aec___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyForm.vue?vue&type=template&id=7ce50aec& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=template&id=7ce50aec&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_template_id_7ce50aec___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_template_id_7ce50aec___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/form/SurveyForm.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/info/Author.vue":
/*!***********************************************************************!*\
  !*** ./client/component/engage_survey/src/components/info/Author.vue ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Author_vue_vue_type_template_id_0ef7a3a6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Author.vue?vue&type=template&id=0ef7a3a6& */ \"./client/component/engage_survey/src/components/info/Author.vue?vue&type=template&id=0ef7a3a6&\");\n/* harmony import */ var _Author_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Author.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/info/Author.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _Author_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Author.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/info/Author.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _Author_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_Author_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _Author_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _Author_vue_vue_type_template_id_0ef7a3a6___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _Author_vue_vue_type_template_id_0ef7a3a6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/info/Author.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/info/Author.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/info/Author.vue?vue&type=script&lang=js&":
/*!************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/info/Author.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Author_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Author.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/info/Author.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Author_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/info/Author.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/info/Author.vue?vue&type=style&index=0&lang=scss&":
/*!*********************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/info/Author.vue?vue&type=style&index=0&lang=scss& ***!
  \*********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/info/Author.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/info/Author.vue?vue&type=template&id=0ef7a3a6&":
/*!******************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/info/Author.vue?vue&type=template&id=0ef7a3a6& ***!
  \******************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Author_vue_vue_type_template_id_0ef7a3a6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Author.vue?vue&type=template&id=0ef7a3a6& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/info/Author.vue?vue&type=template&id=0ef7a3a6&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Author_vue_vue_type_template_id_0ef7a3a6___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Author_vue_vue_type_template_id_0ef7a3a6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/info/Author.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/shape/SurveyBadge.vue":
/*!*****************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/shape/SurveyBadge.vue ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyBadge_vue_vue_type_template_id_2d7d8ec8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyBadge.vue?vue&type=template&id=2d7d8ec8& */ \"./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=template&id=2d7d8ec8&\");\n/* harmony import */ var _SurveyBadge_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyBadge.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SurveyBadge_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_SurveyBadge_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyBadge_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\nvar script = {}\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  script,\n  _SurveyBadge_vue_vue_type_template_id_2d7d8ec8___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyBadge_vue_vue_type_template_id_2d7d8ec8___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyBadge_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveyBadge_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":false,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/shape/SurveyBadge.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/shape/SurveyBadge.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBadge_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBadge_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/shape/SurveyBadge.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=style&index=0&lang=scss&":
/*!***************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=style&index=0&lang=scss& ***!
  \***************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/shape/SurveyBadge.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=template&id=2d7d8ec8&":
/*!************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=template&id=2d7d8ec8& ***!
  \************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBadge_vue_vue_type_template_id_2d7d8ec8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyBadge.vue?vue&type=template&id=2d7d8ec8& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=template&id=2d7d8ec8&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBadge_vue_vue_type_template_id_2d7d8ec8___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBadge_vue_vue_type_template_id_2d7d8ec8___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/shape/SurveyBadge.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue":
/*!*************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue ***!
  \*************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveySidePanel_vue_vue_type_template_id_1aef095c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveySidePanel.vue?vue&type=template&id=1aef095c& */ \"./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=template&id=1aef095c&\");\n/* harmony import */ var _SurveySidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveySidePanel.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SurveySidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SurveySidePanel.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SurveySidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_SurveySidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveySidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SurveySidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveySidePanel_vue_vue_type_template_id_1aef095c___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveySidePanel_vue_vue_type_template_id_1aef095c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveySidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_SurveySidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!************************************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveySidePanel.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=style&index=0&lang=scss&":
/*!***********************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=style&index=0&lang=scss& ***!
  \***********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=template&id=1aef095c&":
/*!********************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=template&id=1aef095c& ***!
  \********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_template_id_1aef095c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveySidePanel.vue?vue&type=template&id=1aef095c& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=template&id=1aef095c&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_template_id_1aef095c___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_template_id_1aef095c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/js sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!**********************************************************************************************!*\
  !*** ./client/component/engage_survey/src/js sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\".\": \"./client/component/engage_survey/src/js/index.js\",\n\t\"./\": \"./client/component/engage_survey/src/js/index.js\",\n\t\"./index\": \"./client/component/engage_survey/src/js/index.js\",\n\t\"./index.js\": \"./client/component/engage_survey/src/js/index.js\",\n\t\"./mixins/surveypage_mixin\": \"./client/component/engage_survey/src/js/mixins/surveypage_mixin.js\",\n\t\"./mixins/surveypage_mixin.js\": \"./client/component/engage_survey/src/js/mixins/surveypage_mixin.js\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/engage_survey/src/js sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/engage_survey/src/js_sync_^(?:(?");

/***/ }),

/***/ "./client/component/engage_survey/src/js/index.js":
/*!********************************************************!*\
  !*** ./client/component/engage_survey/src/js/index.js ***!
  \********************************************************/
/*! exports provided: surveyPageMixin */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_mixins_surveypage_mixin__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/mixins/surveypage_mixin */ \"engage_survey/mixins/surveypage_mixin\");\n/* harmony import */ var engage_survey_mixins_surveypage_mixin__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_mixins_surveypage_mixin__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (default from non-harmony) */ __webpack_require__.d(__webpack_exports__, \"surveyPageMixin\", function() { return engage_survey_mixins_surveypage_mixin__WEBPACK_IMPORTED_MODULE_0___default.a; });\n/**\n * This file is part of Totara Enterprise Extensions.\n *\n * Copyright (C) 2020 onwards Totara Learning Solutions LTD\n *\n * Totara Enterprise Extensions is provided only to Totara\n * Learning Solutions LTD's customers and partners, pursuant to\n * the terms and conditions of a separate agreement with Totara\n * Learning Solutions LTD or its affiliate.\n *\n * If you do not have an agreement with Totara Learning Solutions\n * LTD, you may not access, use, modify, or distribute this software.\n * Please contact [licensing@totaralearning.com] for more information.\n *\n * @author Qingyang Liu <Qingyang.liu@totaralearning.com>\n * @module engage_survey\n */\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/js/index.js?");

/***/ }),

/***/ "./client/component/engage_survey/src/js/mixins/surveypage_mixin.js":
/*!**************************************************************************!*\
  !*** ./client/component/engage_survey/src/js/mixins/surveypage_mixin.js ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/graphql/get_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql\");\n/* harmony import */ var totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/graphql/update_bookmark */ \"./server/totara/engage/webapi/ajax/update_bookmark.graphql\");\n/**\n * This file is part of Totara Enterprise Extensions.\n *\n * Copyright (C) 2020 onwards Totara Learning Solutions LTD\n *\n * Totara Enterprise Extensions is provided only to Totara\n * Learning Solutions LTD's customers and partners, pursuant to\n * the terms and conditions of a separate agreement with Totara\n * Learning Solutions LTD or its affiliate.\n *\n * If you do not have an agreement with Totara Learning Solutions\n * LTD, you may not access, use, modify, or distribute this software.\n * Please contact [licensing@totaralearning.com] for more information.\n *\n * @author Qingyang Liu <Qingyang.liu@totaralearning.com>\n * @module engage_survey\n */\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  props: {\n    resourceId: {\n      type: Number,\n      required: true\n    },\n    backButton: {\n      type: Object,\n      required: false\n    },\n    navigationButtons: {\n      type: Object,\n      required: false\n    }\n  },\n  apollo: {\n    survey: {\n      query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_0__[\"default\"],\n      variables: function variables() {\n        return {\n          resourceid: this.resourceId\n        };\n      },\n      result: function result(_ref) {\n        var survey = _ref.data.survey;\n        this.bookmarked = survey.bookmarked;\n      }\n    }\n  },\n  data: function data() {\n    return {\n      survey: {},\n      bookmarked: false\n    };\n  },\n  computed: {\n    /**\n     *\n     * @returns {Object}\n     */\n    firstQuestion: function firstQuestion() {\n      if (!this.survey) {\n        return {};\n      }\n\n      return Array.prototype.slice.call(this.survey.questions).shift();\n    }\n  },\n  methods: {\n    updateBookmark: function updateBookmark() {\n      this.bookmarked = !this.bookmarked;\n      this.$apollo.mutate({\n        mutation: totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n        refetchAll: false,\n        variables: {\n          itemid: this.resourceId,\n          component: 'engage_survey',\n          bookmarked: this.bookmarked\n        }\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/js/mixins/surveypage_mixin.js?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!*************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/pages sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \*************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./SurveyEditView\": \"./client/component/engage_survey/src/pages/SurveyEditView.vue\",\n\t\"./SurveyEditView.vue\": \"./client/component/engage_survey/src/pages/SurveyEditView.vue\",\n\t\"./SurveyView\": \"./client/component/engage_survey/src/pages/SurveyView.vue\",\n\t\"./SurveyView.vue\": \"./client/component/engage_survey/src/pages/SurveyView.vue\",\n\t\"./SurveyVoteView\": \"./client/component/engage_survey/src/pages/SurveyVoteView.vue\",\n\t\"./SurveyVoteView.vue\": \"./client/component/engage_survey/src/pages/SurveyVoteView.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/engage_survey/src/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/engage_survey/src/pages_sync_^(?:(?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyEditView.vue":
/*!*********************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyEditView.vue ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyEditView_vue_vue_type_template_id_d0b476e4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyEditView.vue?vue&type=template&id=d0b476e4& */ \"./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=template&id=d0b476e4&\");\n/* harmony import */ var _SurveyEditView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyEditView.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SurveyEditView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SurveyEditView.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SurveyEditView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_SurveyEditView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyEditView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SurveyEditView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyEditView_vue_vue_type_template_id_d0b476e4___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyEditView_vue_vue_type_template_id_d0b476e4___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyEditView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_SurveyEditView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/pages/SurveyEditView.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyEditView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!********************************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \********************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyEditView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyEditView.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyEditView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyEditView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=template&id=d0b476e4&":
/*!****************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=template&id=d0b476e4& ***!
  \****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_template_id_d0b476e4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyEditView.vue?vue&type=template&id=d0b476e4& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=template&id=d0b476e4&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_template_id_d0b476e4___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_template_id_d0b476e4___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyEditView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyView.vue":
/*!*****************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyView.vue ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyView_vue_vue_type_template_id_3f5a87e4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyView.vue?vue&type=template&id=3f5a87e4& */ \"./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=template&id=3f5a87e4&\");\n/* harmony import */ var _SurveyView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyView.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SurveyView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SurveyView.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SurveyView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_SurveyView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SurveyView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyView_vue_vue_type_template_id_3f5a87e4___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyView_vue_vue_type_template_id_3f5a87e4___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/pages/SurveyView.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=script&lang=js&":
/*!******************************************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyView.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=style&index=0&lang=scss&":
/*!***************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=style&index=0&lang=scss& ***!
  \***************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=template&id=3f5a87e4&":
/*!************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=template&id=3f5a87e4& ***!
  \************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyView_vue_vue_type_template_id_3f5a87e4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyView.vue?vue&type=template&id=3f5a87e4& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=template&id=3f5a87e4&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyView_vue_vue_type_template_id_3f5a87e4___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyView_vue_vue_type_template_id_3f5a87e4___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyVoteView.vue":
/*!*********************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyVoteView.vue ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyVoteView_vue_vue_type_template_id_4f6ac96e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyVoteView.vue?vue&type=template&id=4f6ac96e& */ \"./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=template&id=4f6ac96e&\");\n/* harmony import */ var _SurveyVoteView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyVoteView.vue?vue&type=script&lang=js& */ \"./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SurveyVoteView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SurveyVoteView.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _SurveyVoteView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_SurveyVoteView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SurveyVoteView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyVoteView_vue_vue_type_template_id_4f6ac96e___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyVoteView_vue_vue_type_template_id_4f6ac96e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_survey/src/pages/SurveyVoteView.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyVoteView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteView.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyVoteView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyVoteView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=template&id=4f6ac96e&":
/*!****************************************************************************************************!*\
  !*** ./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=template&id=4f6ac96e& ***!
  \****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteView_vue_vue_type_template_id_4f6ac96e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteView.vue?vue&type=template&id=4f6ac96e& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=template&id=4f6ac96e&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteView_vue_vue_type_template_id_4f6ac96e___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteView_vue_vue_type_template_id_4f6ac96e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyVoteView.vue?");

/***/ }),

/***/ "./client/component/engage_survey/src/tui.json":
/*!*****************************************************!*\
  !*** ./client/component/engage_survey/src/tui.json ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"engage_survey\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"engage_survey\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"engage_survey\")\ntui._bundle.addModulesFromContext(\"engage_survey\", __webpack_require__(\"./client/component/engage_survey/src/js sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\ntui._bundle.addModulesFromContext(\"engage_survey/components\", __webpack_require__(\"./client/component/engage_survey/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\ntui._bundle.addModulesFromContext(\"engage_survey/pages\", __webpack_require__(\"./client/component/engage_survey/src/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"survey\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCard.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*******************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"votenow\",\n    \"editsurvey\",\n    \"editsurveyaccessiblename\",\n    \"noresult\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCardBody.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"votemessage\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyResultBody.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"percentage\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"close\",\n    \"participant\",\n    \"participants\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyResultContent.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"vote\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"moodle\": [\n    \"next\"\n  ],\n\n  \"engage_survey\": [\n    \"formtitle\",\n    \"formtypetitle\",\n    \"optionstitle\",\n    \"optionsingle\",\n    \"optionmultiple\",\n    \"option\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/form/SurveyForm.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*****************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"survey\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/shape/SurveyBadge.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"deletewarningmsg\",\n    \"likesurvey\",\n    \"removelikesurvey\",\n    \"deletesurvey\",\n    \"reportsurvey\",\n    \"error:reportsurvey\"\n  ],\n  \"totara_engage\": [\n    \"overview\"\n  ],\n  \"totara_reportedcontent\": [\n    \"reported\",\n    \"reported_failed\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"save\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyEditView.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/components/form/SurveyForm */ \"engage_survey/components/form/SurveyForm\");\n/* harmony import */ var engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/components/form/AccessForm */ \"totara_engage/components/form/AccessForm\");\n/* harmony import */ var totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_survey_graphql_create_survey__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_survey/graphql/create_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/create_survey.graphql\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/mixins/container_mixin */ \"totara_engage/mixins/container_mixin\");\n/* harmony import */ var totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n // Mixins\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SurveyForm: engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_0___default.a,\n    AccessForm: totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1___default.a\n  },\n  mixins: [totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4___default.a],\n  data: function data() {\n    return {\n      stage: 0,\n      maxStage: 1,\n      survey: {\n        question: '',\n        type: '',\n        options: []\n      },\n      submitting: false\n    };\n  },\n  computed: {\n    privateDisabled: function privateDisabled() {\n      return this.containerValues.access ? !totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"AccessManager\"].isPrivate(this.containerValues.access) : false;\n    },\n    restrictedDisabled: function restrictedDisabled() {\n      return this.containerValues.access ? totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"AccessManager\"].isPublic(this.containerValues.access) : false;\n    }\n  },\n  methods: {\n    /**\n     * @param {String}          question\n     * @param {Number|String}   type\n     * @param {Array}           options\n     */\n    next: function next(_ref) {\n      var question = _ref.question,\n          type = _ref.type,\n          options = _ref.options;\n\n      if (this.stage < this.maxStage) {\n        this.stage += 1;\n      }\n\n      this.survey.question = question;\n      this.survey.type = type;\n      this.survey.options = options;\n      this.$emit('change-title', this.stage);\n    },\n    back: function back() {\n      if (this.stage > 0) {\n        this.stage -= 1;\n      }\n\n      this.$emit('change-title', this.stage);\n    },\n\n    /**\n     * @param {String} access\n     * @param {Array} topics\n     * @param {Array} shares\n     */\n    done: function done(_ref2) {\n      var _this = this;\n\n      var access = _ref2.access,\n          topics = _ref2.topics,\n          shares = _ref2.shares;\n\n      if (this.submitting) {\n        return;\n      }\n\n      this.submitting = true;\n      this.$apollo.mutate({\n        mutation: engage_survey_graphql_create_survey__WEBPACK_IMPORTED_MODULE_2__[\"default\"],\n        refetchQueries: ['totara_engage_contribution_cards', 'container_workspace_contribution_cards', 'container_workspace_shared_cards'],\n        variables: {\n          // TODO: replace timeexpired with the time selected from the date component\n          timeexpired: null,\n          questions: [{\n            value: this.survey.question,\n            answertype: this.survey.type,\n            options: this.survey.options.map(function (option) {\n              return option.text;\n            })\n          }],\n          access: access,\n          topics: topics.map(function (topic) {\n            return topic.id;\n          }),\n          shares: shares\n        },\n        update: function update(cache, _ref3) {\n          var survey = _ref3.data.survey;\n\n          _this.$emit('done', {\n            resourceId: survey.resource.id\n          });\n        }\n      }).then(function () {\n        return _this.$emit('cancel');\n      })[\"finally\"](function () {\n        return _this.submitting = false;\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/CreateSurvey.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/RadioGroup */ \"tui/components/form/RadioGroup\");\n/* harmony import */ var tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/Radio */ \"tui/components/form/Radio\");\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/uniform */ \"tui/components/uniform\");\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform__WEBPACK_IMPORTED_MODULE_2__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\nvar has = Object.prototype.hasOwnProperty;\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    RadioGroup: tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_0___default.a,\n    Radio: tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1___default.a,\n    FormRow: tui_components_uniform__WEBPACK_IMPORTED_MODULE_2__[\"FormRow\"]\n  },\n  model: {\n    prop: 'value',\n    event: 'update-value'\n  },\n  props: {\n    value: {\n      // We are using this property for v-model.\n      required: false,\n      type: [Number, String]\n    },\n    options: {\n      required: true,\n      type: [Array, Object],\n      validator: function validator(prop) {\n        for (var i in prop) {\n          if (!has.call(prop, i)) {\n            continue;\n          }\n\n          var option = prop[i];\n\n          if (!has.call(option, 'id') || !has.call(option, 'value')) {\n            return false;\n          }\n        }\n\n        return true;\n      }\n    },\n    label: String\n  },\n  data: function data() {\n    return {\n      option: null\n    };\n  },\n  watch: {\n    option: function option(value) {\n      this.$emit('update-value', value);\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/box/RadioBox.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_Checkbox__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/Checkbox */ \"tui/components/form/Checkbox\");\n/* harmony import */ var tui_components_form_Checkbox__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Checkbox__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_CheckboxGroup__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/CheckboxGroup */ \"tui/components/form/CheckboxGroup\");\n/* harmony import */ var tui_components_form_CheckboxGroup__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_CheckboxGroup__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/uniform */ \"tui/components/uniform\");\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform__WEBPACK_IMPORTED_MODULE_2__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\nvar has = Object.prototype.hasOwnProperty;\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Checkbox: tui_components_form_Checkbox__WEBPACK_IMPORTED_MODULE_0___default.a,\n    CheckboxGroup: tui_components_form_CheckboxGroup__WEBPACK_IMPORTED_MODULE_1___default.a,\n    FormRow: tui_components_uniform__WEBPACK_IMPORTED_MODULE_2__[\"FormRow\"]\n  },\n  model: {\n    prop: 'value',\n    event: 'update-value'\n  },\n  props: {\n    value: {\n      // A property that is being used for v-model\n      type: Array,\n      \"default\": function _default() {\n        return [];\n      }\n    },\n    options: {\n      type: [Array, Object],\n      validator: function validator(prop) {\n        for (var i in prop) {\n          if (!has.call(prop, i)) {\n            continue;\n          }\n\n          var item = prop[i];\n\n          if (!has.call(item, 'id') || !has.call(item, 'value')) {\n            return false;\n          }\n        }\n\n        return true;\n      }\n    },\n    label: String\n  },\n  data: function data() {\n    return {\n      picked: []\n    };\n  },\n  methods: {\n    $_handleChange: function $_handleChange(id, checked) {\n      if (!checked) {\n        this.picked = this.picked.filter(function (item) {\n          return item !== id;\n        });\n      } else if (checked && !this.picked.includes(id)) {\n        // Adding.\n        this.picked.push(id);\n      } // We are making sure that the whole button vote will be blocked form clicked.\n\n\n      var picked = null;\n\n      if (0 < this.picked.length) {\n        picked = this.picked;\n      }\n\n      this.$emit('update-value', picked);\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/box/SquareBox.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/card/Card */ \"tui/components/card/Card\");\n/* harmony import */ var tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_survey_components_card_SurveyCardBody__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_survey/components/card/SurveyCardBody */ \"engage_survey/components/card/SurveyCardBody\");\n/* harmony import */ var engage_survey_components_card_SurveyCardBody__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_card_SurveyCardBody__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var engage_survey_components_card_SurveyResultBody__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! engage_survey/components/card/SurveyResultBody */ \"engage_survey/components/card/SurveyResultBody\");\n/* harmony import */ var engage_survey_components_card_SurveyResultBody__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_card_SurveyResultBody__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/components/buttons/BookmarkButton */ \"totara_engage/components/buttons/BookmarkButton\");\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var engage_survey_graphql_vote_result__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_survey/graphql/vote_result */ \"./server/totara/engage/resources/survey/webapi/ajax/vote_result.graphql\");\n/* harmony import */ var totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! totara_engage/graphql/update_bookmark */ \"./server/totara/engage/webapi/ajax/update_bookmark.graphql\");\n/* harmony import */ var totara_engage_components_card_Footnotes__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_engage/components/card/Footnotes */ \"totara_engage/components/card/Footnotes\");\n/* harmony import */ var totara_engage_components_card_Footnotes__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_card_Footnotes__WEBPACK_IMPORTED_MODULE_7__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n // GraphQL\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    CoreCard: tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0___default.a,\n    SurveyCardBody: engage_survey_components_card_SurveyCardBody__WEBPACK_IMPORTED_MODULE_2___default.a,\n    SurveyResultBody: engage_survey_components_card_SurveyResultBody__WEBPACK_IMPORTED_MODULE_3___default.a,\n    BookmarkButton: totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4___default.a,\n    Footnotes: totara_engage_components_card_Footnotes__WEBPACK_IMPORTED_MODULE_7___default.a\n  },\n  mixins: [totara_engage_index__WEBPACK_IMPORTED_MODULE_1__[\"cardMixin\"]],\n  data: function data() {\n    var extraData = {},\n        questions = [];\n\n    if (this.extra) {\n      extraData = JSON.parse(this.extra);\n    }\n\n    if (extraData.questions) {\n      questions = Array.prototype.slice.call(extraData.questions);\n    }\n\n    return {\n      show: {\n        result: false,\n        editModal: false\n      },\n      innerBookMarked: this.bookmarked,\n      questions: questions,\n      voted: extraData.voted || false,\n      extraData: JSON.parse(this.extra)\n    };\n  },\n  computed: {\n    editAble: function editAble() {\n      var extra = this.extraData;\n      return extra.editable || false;\n    }\n  },\n  methods: {\n    /**\n     * Updating the questions of this cards.\n     */\n    handleVoted: function handleVoted() {\n      var _this = this;\n\n      this.$apollo.query({\n        query: engage_survey_graphql_vote_result__WEBPACK_IMPORTED_MODULE_5__[\"default\"],\n        variables: {\n          resourceid: this.instanceId\n        }\n      }).then(function (_ref) {\n        var questions = _ref.data.questions;\n        _this.questions = questions;\n        _this.voted = true; // Showing the result afterward.\n\n        _this.show.result = true;\n      });\n    },\n    $_hideModals: function $_hideModals() {\n      this.show.editModal = false;\n      this.show.result = false;\n    },\n    deleted: function deleted() {\n      this.$_hideModals(); // Sent to up-stream to remove this very card from very\n\n      this.emitDeleted();\n    },\n    updated: function updated() {\n      this.$_hideModals(); // Sent to up-stream to update this very card.\n\n      this.emitUpdated();\n    },\n    updateBookmark: function updateBookmark() {\n      this.innerBookMarked = !this.innerBookMarked;\n      this.$apollo.mutate({\n        mutation: totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n        refetchAll: false,\n        refetchQueries: ['totara_engage_contribution_cards'],\n        variables: {\n          itemid: this.instanceId,\n          component: 'engage_survey',\n          bookmarked: this.innerBookMarked\n        }\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCard.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_engage/components/icons/access/computed/AccessIcon */ \"totara_engage/components/icons/access/computed/AccessIcon\");\n/* harmony import */ var totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/links/ActionLink */ \"tui/components/links/ActionLink\");\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AccessIcon: totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_0___default.a,\n    ActionLink: tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default.a\n  },\n  inheritAttrs: false,\n  props: {\n    resourceId: {\n      type: [Number, String],\n      required: true\n    },\n    name: {\n      required: true,\n      type: String,\n      \"default\": ''\n    },\n    access: {\n      required: true,\n      type: String\n    },\n    voted: {\n      required: true,\n      type: Boolean\n    },\n    owned: {\n      required: true,\n      type: Boolean\n    },\n    editAble: {\n      required: true,\n      type: Boolean\n    },\n    bookmarked: {\n      type: Boolean,\n      \"default\": false\n    },\n    labelId: {\n      type: String,\n      \"default\": ''\n    },\n    url: {\n      type: String,\n      \"default\": '/totara/engage/resources/survey/index.php'\n    }\n  },\n  computed: {\n    showEdit: function showEdit() {\n      return this.owned && this.editAble;\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCardBody.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_Label__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/Label */ \"tui/components/form/Label\");\n/* harmony import */ var tui_components_form_Label__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Label__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/components/icons/access/computed/AccessIcon */ \"totara_engage/components/icons/access/computed/AccessIcon\");\n/* harmony import */ var totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_survey/components/card/result/SurveyQuestionResult */ \"engage_survey/components/card/result/SurveyQuestionResult\");\n/* harmony import */ var engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_2__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AccessIcon: totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_1___default.a,\n    Label: tui_components_form_Label__WEBPACK_IMPORTED_MODULE_0___default.a,\n    SurveyQuestionResult: engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_2___default.a\n  },\n  props: {\n    name: {\n      required: true,\n      type: String,\n      \"default\": ''\n    },\n    questions: {\n      type: [Object, Array],\n      required: true\n    },\n    access: {\n      required: true,\n      type: String\n    },\n    labelId: {\n      type: String,\n      \"default\": ''\n    },\n    resourceId: {\n      required: true,\n      type: String\n    },\n    url: {\n      required: true,\n      type: String\n    }\n  },\n  computed: {\n    voteMessage: function voteMessage() {\n      var questions = Array.prototype.slice.call(this.questions).shift();\n      return this.$str('votemessage', 'engage_survey', {\n        options: questions.options.length >= 3 ? 3 : 2,\n        questions: questions.options.length\n      });\n    }\n  },\n  methods: {\n    navigateTo: function navigateTo() {\n      window.location.href = this.$url(this.url, {\n        page: 'vote'\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyResultBody.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/progress/Progress */ \"tui/components/progress/Progress\");\n/* harmony import */ var tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_1__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Progress: tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_0___default.a\n  },\n  props: {\n    questionId: {\n      type: [Number, String],\n      required: true\n    },\n    options: {\n      type: [Array, Object],\n      required: true\n    },\n\n    /**\n     * Total number of user has voted the question.\n     */\n    totalVotes: {\n      type: [Number, String],\n      required: true\n    },\n    displayOptions: {\n      type: [Number, String],\n      \"default\": 3\n    },\n    resultContent: {\n      type: Boolean,\n      \"default\": false\n    },\n    answerType: {\n      type: [Number, String],\n      required: true\n    }\n  },\n  computed: {\n    calulatedOptions: function calulatedOptions() {\n      if (this.resultContent) return this.options;\n      return Array.prototype.slice.call(this.options, 0, this.displayOptions);\n    },\n    highestVote: function highestVote() {\n      if (this.isMultiChoice) {\n        var sortArray = Array.prototype.slice.call(this.options).sort(function (o1, o2) {\n          return o2.votes - o1.votes;\n        });\n        return sortArray[0].votes;\n      }\n\n      return 0;\n    },\n\n    /**\n     *\n     * @returns {Boolean}\n     */\n    isSingleChoice: function isSingleChoice() {\n      return totara_engage_index__WEBPACK_IMPORTED_MODULE_1__[\"AnswerType\"].isSingleChoice(this.answerType);\n    },\n\n    /**\n     * @returns {Boolean}\n     */\n    isMultiChoice: function isMultiChoice() {\n      return totara_engage_index__WEBPACK_IMPORTED_MODULE_1__[\"AnswerType\"].isMultiChoice(this.answerType);\n    }\n  },\n  methods: {\n    /**\n     *\n     * @param {Number} votes\n     * @returns {number}\n     */\n    percentage: function percentage(votes) {\n      return Math.round(votes / this.totalVotes * 100);\n    },\n\n    /**\n     *\n     * @param {Number} votes\n     * @returns {number}\n     */\n    $_getVotes: function $_getVotes(votes) {\n      if (this.isMultiChoice) {\n        return votes / this.highestVote * this.totalVotes;\n      }\n\n      return votes;\n    },\n\n    /**\n     *\n     * @param {Number} votes\n     * @returns {number}\n     */\n    getValues: function getValues(votes) {\n      if (this.isSingleChoice) {\n        return votes;\n      }\n\n      return this.highestVote === 0 && this.highestVote === votes ? this.totalVotes : this.$_getVotes(votes);\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/components/card/result/SurveyQuestionResult */ \"engage_survey/components/card/result/SurveyQuestionResult\");\n/* harmony import */ var engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_survey/graphql/get_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n // GraphQL queries\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SurveyQuestionResult: engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_0___default.a\n  },\n  props: {\n    resourceId: {\n      required: true,\n      type: [Number, String]\n    }\n  },\n  apollo: {\n    survey: {\n      query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_2__[\"default\"],\n      variables: function variables() {\n        return {\n          resourceid: this.resourceId\n        };\n      }\n    }\n  },\n  data: function data() {\n    return {\n      survey: {}\n    };\n  },\n  computed: {\n    showParticipants: function showParticipants() {\n      var questions = Array.prototype.slice.call(this.questions),\n          _questions$shift = questions.shift(),\n          participants = _questions$shift.participants;\n\n      if (participants === 1) {\n        return this.$str('participant', 'engage_survey');\n      }\n\n      return this.$str('participants', 'engage_survey');\n    },\n    showNumberOfParticipant: function showNumberOfParticipant() {\n      var _Array$prototype$slic = Array.prototype.slice.call(this.questions).shift(),\n          participants = _Array$prototype$slic.participants;\n\n      return participants;\n    },\n\n    /**\n     * @returns {Boolean}\n     */\n    isMultiChoice: function isMultiChoice() {\n      return totara_engage_index__WEBPACK_IMPORTED_MODULE_1__[\"AnswerType\"].isMultiChoice(this.questions[0].answertype);\n    },\n    questions: function questions() {\n      var questionresults = this.survey.questionresults;\n      return Array.prototype.slice.call(questionresults);\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyResultContent.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_components_box_RadioBox__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/components/box/RadioBox */ \"engage_survey/components/box/RadioBox\");\n/* harmony import */ var engage_survey_components_box_RadioBox__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_box_RadioBox__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var engage_survey_components_box_SquareBox__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! engage_survey/components/box/SquareBox */ \"engage_survey/components/box/SquareBox\");\n/* harmony import */ var engage_survey_components_box_SquareBox__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_box_SquareBox__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/buttons/Button */ \"tui/components/buttons/Button\");\n/* harmony import */ var tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/form/Form */ \"tui/components/form/Form\");\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Form__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var engage_survey_graphql_create_answer__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_survey/graphql/create_answer */ \"./server/totara/engage/resources/survey/webapi/ajax/create_answer.graphql\");\n/* harmony import */ var engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! engage_survey/graphql/get_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n // GraphQL queries\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SquareBox: engage_survey_components_box_SquareBox__WEBPACK_IMPORTED_MODULE_1___default.a,\n    RadioBox: engage_survey_components_box_RadioBox__WEBPACK_IMPORTED_MODULE_0___default.a,\n    Button: tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_3___default.a,\n    Form: tui_components_form_Form__WEBPACK_IMPORTED_MODULE_4___default.a\n  },\n  props: {\n    options: {\n      type: [Array, Object],\n      required: true\n    },\n    answerType: {\n      type: [Number, String],\n      required: true\n    },\n    resourceId: {\n      required: true,\n      type: [Number, String]\n    },\n    questionId: {\n      required: true,\n      type: [Number, String]\n    },\n    disabled: {\n      type: Boolean,\n      \"default\": false\n    },\n    label: {\n      type: String,\n      required: true\n    }\n  },\n  data: function data() {\n    return {\n      questions: [],\n      answer: null\n    };\n  },\n  computed: {\n    /**\n     * @returns {Boolean}\n     */\n    isMultiChoice: function isMultiChoice() {\n      return totara_engage_index__WEBPACK_IMPORTED_MODULE_2__[\"AnswerType\"].isMultiChoice(this.answerType);\n    },\n\n    /**\n     *\n     * @returns {Boolean}\n     */\n    isSingleChoice: function isSingleChoice() {\n      return totara_engage_index__WEBPACK_IMPORTED_MODULE_2__[\"AnswerType\"].isSingleChoice(this.answerType);\n    }\n  },\n  methods: {\n    vote: function vote() {\n      if (null == this.answer) {\n        return;\n      }\n\n      var answers;\n\n      if (!Array.isArray(this.answer)) {\n        answers = [this.answer];\n      } else {\n        answers = this.answer;\n      }\n\n      this.$apollo.mutate({\n        mutation: engage_survey_graphql_create_answer__WEBPACK_IMPORTED_MODULE_5__[\"default\"],\n        refetchQueries: [{\n          query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n          variables: {\n            resourceid: this.resourceId\n          }\n        }],\n        variables: {\n          resourceid: this.resourceId,\n          options: answers,\n          questionid: this.questionId\n        }\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_engage/components/buttons/BookmarkButton */ \"totara_engage/components/buttons/BookmarkButton\");\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    BookmarkButton: totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_0___default.a\n  },\n  props: {\n    title: {\n      type: String,\n      required: true\n    },\n    bookmarked: {\n      type: Boolean,\n      \"default\": false\n    },\n    owned: {\n      type: Boolean,\n      required: true\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/uniform */ \"tui/components/uniform\");\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_reform_FieldContextProvider__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/reform/FieldContextProvider */ \"tui/components/reform/FieldContextProvider\");\n/* harmony import */ var tui_components_reform_FieldContextProvider__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_reform_FieldContextProvider__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/buttons/ButtonGroup */ \"tui/components/buttons/ButtonGroup\");\n/* harmony import */ var tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/buttons/Cancel */ \"tui/components/buttons/Cancel\");\n/* harmony import */ var tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_components_buttons_LoadingButton__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/components/buttons/LoadingButton */ \"totara_engage/components/buttons/LoadingButton\");\n/* harmony import */ var totara_engage_components_buttons_LoadingButton__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_LoadingButton__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/components/form/Radio */ \"tui/components/form/Radio\");\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/components/form/Repeater */ \"tui/components/form/Repeater\");\n/* harmony import */ var tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_7__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Uniform: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"Uniform\"],\n    FieldArray: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"FieldArray\"],\n    FormRow: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"FormRow\"],\n    FormText: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"FormText\"],\n    FieldContextProvider: tui_components_reform_FieldContextProvider__WEBPACK_IMPORTED_MODULE_1___default.a,\n    FormRadioGroup: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"FormRadioGroup\"],\n    ButtonGroup: tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_2___default.a,\n    LoadingButton: totara_engage_components_buttons_LoadingButton__WEBPACK_IMPORTED_MODULE_4___default.a,\n    CancelButton: tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_3___default.a,\n    Radio: tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_5___default.a,\n    Repeater: tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_6___default.a\n  },\n  props: {\n    survey: {\n      type: Object,\n      \"default\": function _default() {\n        return {\n          question: '',\n          type: '',\n          options: [],\n          questionId: null\n        };\n      },\n      validator: function validator(prop) {\n        return 'question' in prop && 'type' in prop && 'options' in prop;\n      }\n    },\n    submitting: {\n      type: Boolean,\n      \"default\": false\n    },\n    buttonContent: {\n      type: String,\n      \"default\": function _default() {\n        return this.$str('next', 'moodle');\n      }\n    },\n    showButtonRight: {\n      type: Boolean,\n      \"default\": true\n    },\n    showButtonLeft: {\n      type: Boolean,\n      \"default\": false\n    }\n  },\n  data: function data() {\n    var minOptions = 2;\n    var options = Array.isArray(this.survey.options) ? this.survey.options : [];\n\n    while (options.length < minOptions) {\n      options.push(this.newOption());\n    }\n\n    return {\n      multiChoice: String(totara_engage_index__WEBPACK_IMPORTED_MODULE_7__[\"AnswerType\"].MULTI_CHOICE),\n      singleChoice: String(totara_engage_index__WEBPACK_IMPORTED_MODULE_7__[\"AnswerType\"].SINGLE_CHOICE),\n      minOptions: minOptions,\n      maxOptions: 10,\n      disabled: true,\n      initialValues: {\n        question: this.survey.question,\n        options: options,\n        optionType: this.survey.type || String(totara_engage_index__WEBPACK_IMPORTED_MODULE_7__[\"AnswerType\"].MULTI_CHOICE)\n      }\n    };\n  },\n  computed: {\n    buttonText: function buttonText() {\n      return this.buttonContent;\n    }\n  },\n  methods: {\n    /**\n     * @returns {object}\n     */\n    newOption: function newOption() {\n      return {\n        text: '',\n        id: 0\n      };\n    },\n    submit: function submit(values) {\n      var params = {\n        options: values.options,\n        question: values.question,\n        type: values.optionType,\n        // If it is for creation, then this should be null.\n        questionId: this.survey.questionId\n      };\n      this.$emit('next', params);\n    },\n    change: function change(values) {\n      var question = values.question,\n          options = values.options;\n      this.disabled = true;\n\n      if (question.length > 0) {\n        var result = Array.prototype.slice.call(options, 0, 2).filter(function (option) {\n          return option.text !== '';\n        });\n\n        if (result.length === 2) {\n          this.disabled = false;\n        }\n      }\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/form/SurveyForm.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/info/Author.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/info/Author.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/avatar/Avatar */ \"tui/components/avatar/Avatar\");\n/* harmony import */ var tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Avatar: tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_0___default.a\n  },\n  props: {\n    userId: {\n      required: true,\n      type: [Number, String]\n    },\n    fullname: {\n      required: true,\n      type: String\n    },\n    profileImageUrl: {\n      required: true,\n      type: String\n    },\n    profileImageAlt: {\n      required: true,\n      type: String\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/info/Author.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ \"./node_modules/@babel/runtime/regenerator/index.js\");\n/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ \"./node_modules/@babel/runtime/helpers/asyncToGenerator.js\");\n/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! totara_engage/components/sidepanel/access/AccessSetting */ \"totara_engage/components/sidepanel/access/AccessSetting\");\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/components/sidepanel/access/AccessDisplay */ \"totara_engage/components/sidepanel/access/AccessDisplay\");\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/modal/ModalPresenter */ \"tui/components/modal/ModalPresenter\");\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! totara_engage/components/modal/EngageWarningModal */ \"totara_engage/components/modal/EngageWarningModal\");\n/* harmony import */ var totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! totara_engage/components/sidepanel/media/MediaSetting */ \"totara_engage/components/sidepanel/media/MediaSetting\");\n/* harmony import */ var totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var tui_apollo_client__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! tui/apollo_client */ \"tui/apollo_client\");\n/* harmony import */ var tui_apollo_client__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(tui_apollo_client__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! tui/components/profile/MiniProfileCard */ \"tui/components/profile/MiniProfileCard\");\n/* harmony import */ var tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var tui_components_tabs_Tabs__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! tui/components/tabs/Tabs */ \"tui/components/tabs/Tabs\");\n/* harmony import */ var tui_components_tabs_Tabs__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(tui_components_tabs_Tabs__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var tui_components_tabs_Tab__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! tui/components/tabs/Tab */ \"tui/components/tabs/Tab\");\n/* harmony import */ var tui_components_tabs_Tab__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(tui_components_tabs_Tab__WEBPACK_IMPORTED_MODULE_10__);\n/* harmony import */ var tui_components_dropdown_DropdownItem__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! tui/components/dropdown/DropdownItem */ \"tui/components/dropdown/DropdownItem\");\n/* harmony import */ var tui_components_dropdown_DropdownItem__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(tui_components_dropdown_DropdownItem__WEBPACK_IMPORTED_MODULE_11__);\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! tui/notifications */ \"tui/notifications\");\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(tui_notifications__WEBPACK_IMPORTED_MODULE_12__);\n/* harmony import */ var engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! engage_survey/graphql/get_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql\");\n/* harmony import */ var engage_survey_graphql_delete_survey__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! engage_survey/graphql/delete_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/delete_survey.graphql\");\n/* harmony import */ var engage_survey_graphql_update_survey__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! engage_survey/graphql/update_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/update_survey.graphql\");\n/* harmony import */ var totara_reportedcontent_graphql_create_review__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! totara_reportedcontent/graphql/create_review */ \"./server/totara/reportedcontent/webapi/ajax/create_review.graphql\");\n\n\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n\n // GraphQL\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AccessDisplay: totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_3___default.a,\n    ModalPresenter: tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_4___default.a,\n    EngageWarningModal: totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_5___default.a,\n    AccessSetting: totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_2___default.a,\n    MediaSetting: totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_6___default.a,\n    MiniProfileCard: tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_8___default.a,\n    Tabs: tui_components_tabs_Tabs__WEBPACK_IMPORTED_MODULE_9___default.a,\n    Tab: tui_components_tabs_Tab__WEBPACK_IMPORTED_MODULE_10___default.a,\n    DropdownItem: tui_components_dropdown_DropdownItem__WEBPACK_IMPORTED_MODULE_11___default.a\n  },\n  props: {\n    resourceId: {\n      type: [Number, String],\n      required: true\n    }\n  },\n  apollo: {\n    survey: {\n      query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_13__[\"default\"],\n      fetchPolicy: 'network-only',\n      variables: function variables() {\n        return {\n          resourceid: this.resourceId\n        };\n      }\n    }\n  },\n  data: function data() {\n    return {\n      survey: {},\n      submitting: false,\n      openModalFromButtonLabel: false,\n      openModalFromAction: false\n    };\n  },\n  computed: {\n    userEmail: function userEmail() {\n      return this.survey.resource.user.email || '';\n    },\n    sharedByCount: function sharedByCount() {\n      return this.survey.sharedByCount;\n    },\n    likeButtonLabel: function likeButtonLabel() {\n      if (this.survey.reacted) {\n        return this.$str('removelikesurvey', 'engage_survey', this.survey.resource.name);\n      }\n\n      return this.$str('likesurvey', 'engage_survey', this.survey.resource.name);\n    }\n  },\n  methods: {\n    handleDelete: function handleDelete() {\n      var _this = this;\n\n      this.$apollo.mutate({\n        mutation: engage_survey_graphql_delete_survey__WEBPACK_IMPORTED_MODULE_14__[\"default\"],\n        variables: {\n          resourceid: this.resourceId\n        },\n        refetchAll: false\n      }).then(function (_ref) {\n        var data = _ref.data;\n\n        if (data.result) {\n          _this.openModalFromAction = false;\n          window.location.href = _this.$url('/totara/engage/your_resources.php');\n        }\n      });\n    },\n\n    /**\n     * Updates Access for this survey\n     *\n     * @param {String} access The access level of the survey\n     * @param {Array} topics Topics that this survey should be shared with\n     * @param {Array} shares An array of group id's that this survey is shared with\n     */\n    updateAccess: function updateAccess(_ref2) {\n      var _this2 = this;\n\n      var access = _ref2.access,\n          topics = _ref2.topics,\n          shares = _ref2.shares;\n      this.submitting = true;\n      this.$apollo.mutate({\n        mutation: engage_survey_graphql_update_survey__WEBPACK_IMPORTED_MODULE_15__[\"default\"],\n        refetchAll: false,\n        variables: {\n          resourceid: this.resourceId,\n          access: access,\n          topics: topics.map(function (_ref3) {\n            var id = _ref3.id;\n            return id;\n          }),\n          shares: shares\n        },\n        update: function update(proxy, _ref4) {\n          var data = _ref4.data;\n          proxy.writeQuery({\n            query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_13__[\"default\"],\n            variables: {\n              resourceid: _this2.resourceId\n            },\n            data: data\n          });\n        }\n      })[\"finally\"](function () {\n        _this2.submitting = false;\n      });\n    },\n\n    /**\n     *\n     * @param {Boolean} status\n     */\n    updateLikeStatus: function updateLikeStatus(status) {\n      var _apolloClient$readQue = tui_apollo_client__WEBPACK_IMPORTED_MODULE_7___default.a.readQuery({\n        query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_13__[\"default\"],\n        variables: {\n          resourceid: this.resourceId\n        }\n      }),\n          survey = _apolloClient$readQue.survey;\n\n      survey = Object.assign({}, survey);\n      survey.reacted = status;\n      tui_apollo_client__WEBPACK_IMPORTED_MODULE_7___default.a.writeQuery({\n        query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_13__[\"default\"],\n        variables: {\n          resourceid: this.resourceId\n        },\n        data: {\n          survey: survey\n        }\n      });\n    },\n\n    /**\n     * Report the attached survey\n     * @returns {Promise<void>}\n     */\n    reportSurvey: function reportSurvey() {\n      var _this3 = this;\n\n      return _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default()( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {\n        var response;\n        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {\n          while (1) {\n            switch (_context.prev = _context.next) {\n              case 0:\n                if (!_this3.submitting) {\n                  _context.next = 2;\n                  break;\n                }\n\n                return _context.abrupt(\"return\");\n\n              case 2:\n                _this3.submitting = true;\n                _context.prev = 3;\n                _context.next = 6;\n                return _this3.$apollo.mutate({\n                  mutation: totara_reportedcontent_graphql_create_review__WEBPACK_IMPORTED_MODULE_16__[\"default\"],\n                  refetchAll: false,\n                  variables: {\n                    component: 'engage_survey',\n                    area: '',\n                    item_id: _this3.resourceId,\n                    url: window.location.href\n                  }\n                }).then(function (response) {\n                  return response.data.review;\n                });\n\n              case 6:\n                response = _context.sent;\n\n                if (!response.success) {\n                  _context.next = 12;\n                  break;\n                }\n\n                _context.next = 10;\n                return Object(tui_notifications__WEBPACK_IMPORTED_MODULE_12__[\"notify\"])({\n                  message: _this3.$str('reported', 'totara_reportedcontent'),\n                  type: 'success'\n                });\n\n              case 10:\n                _context.next = 14;\n                break;\n\n              case 12:\n                _context.next = 14;\n                return Object(tui_notifications__WEBPACK_IMPORTED_MODULE_12__[\"notify\"])({\n                  message: _this3.$str('reported_failed', 'totara_reportedcontent'),\n                  type: 'error'\n                });\n\n              case 14:\n                _context.next = 20;\n                break;\n\n              case 16:\n                _context.prev = 16;\n                _context.t0 = _context[\"catch\"](3);\n                _context.next = 20;\n                return Object(tui_notifications__WEBPACK_IMPORTED_MODULE_12__[\"notify\"])({\n                  message: _this3.$str('error:reportsurvey', 'engage_survey'),\n                  type: 'error'\n                });\n\n              case 20:\n                _context.prev = 20;\n                _this3.submitting = false;\n                return _context.finish(20);\n\n              case 23:\n              case \"end\":\n                return _context.stop();\n            }\n          }\n        }, _callee, null, [[3, 16, 20, 23]]);\n      }))();\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/components/sidepanel/SurveySidePanel */ \"engage_survey/components/sidepanel/SurveySidePanel\");\n/* harmony import */ var engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! engage_survey/components/form/SurveyForm */ \"engage_survey/components/form/SurveyForm\");\n/* harmony import */ var engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/loader/Loader */ \"tui/components/loader/Loader\");\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/layouts/LayoutOneColumnContentWithSidePanel */ \"tui/components/layouts/LayoutOneColumnContentWithSidePanel\");\n/* harmony import */ var tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/components/header/ResourceNavigationBar */ \"totara_engage/components/header/ResourceNavigationBar\");\n/* harmony import */ var totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var engage_survey_index__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_survey/index */ \"engage_survey/index\");\n/* harmony import */ var engage_survey_index__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(engage_survey_index__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! engage_survey/graphql/get_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql\");\n/* harmony import */ var engage_survey_graphql_update_survey__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! engage_survey/graphql/update_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/update_survey.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n // GraphQL\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SurveySidePanel: engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0___default.a,\n    SurveyForm: engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_1___default.a,\n    Loader: tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_2___default.a,\n    Layout: tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_3___default.a,\n    ResourceNavigationBar: totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_4___default.a\n  },\n  mixins: [engage_survey_index__WEBPACK_IMPORTED_MODULE_5__[\"surveyPageMixin\"]],\n  data: function data() {\n    return {\n      submitting: false\n    };\n  },\n  computed: {\n    surveyInstance: function surveyInstance() {\n      if (this.$apollo.loading) {\n        return undefined;\n      }\n\n      var questions = this.survey.questions;\n      questions = Array.prototype.slice.call(questions);\n      var question = questions.shift();\n      var options = [];\n\n      if (question.options && Array.isArray(question.options)) {\n        options = question.options.map(function (_ref) {\n          var id = _ref.id,\n              value = _ref.value;\n          return {\n            id: id,\n            text: value\n          };\n        });\n      }\n\n      return {\n        questionId: question.id,\n        question: question.value,\n        type: question.answertype,\n        options: options\n      };\n    }\n  },\n  methods: {\n    handleCancel: function handleCancel() {\n      window.location.href = this.$url('/totara/engage/resources/survey/survey_view.php', {\n        id: this.resourceId\n      });\n    },\n    handleSave: function handleSave(_ref2) {\n      var _this = this;\n\n      var question = _ref2.question,\n          questionId = _ref2.questionId,\n          type = _ref2.type,\n          options = _ref2.options;\n\n      if (this.submitting) {\n        return;\n      }\n\n      this.submitting = true;\n      this.$apollo.mutate({\n        mutation: engage_survey_graphql_update_survey__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n        refetchAll: false,\n        variables: {\n          resourceid: this.resourceId,\n          questions: [{\n            value: question,\n            answertype: type,\n            options: options.map(function (_ref3) {\n              var text = _ref3.text;\n              return text;\n            }),\n            id: questionId\n          }]\n        },\n\n        /**\n         *\n         * @param {DataProxy} proxy\n         * @param {Object}    data\n         */\n        updateQuery: function updateQuery(proxy, data) {\n          proxy.writeQuery({\n            query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n            variables: {\n              resourceid: _this.resourceId\n            },\n            data: data\n          });\n        }\n      })[\"finally\"](function () {\n        _this.submitting = false;\n        window.location.href = _this.$url('/totara/engage/resources/survey/survey_view.php', {\n          id: _this.resourceId\n        });\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyEditView.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/components/sidepanel/SurveySidePanel */ \"engage_survey/components/sidepanel/SurveySidePanel\");\n/* harmony import */ var engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/components/header/ResourceNavigationBar */ \"totara_engage/components/header/ResourceNavigationBar\");\n/* harmony import */ var totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_survey/components/content/SurveyVoteTitle */ \"engage_survey/components/content/SurveyVoteTitle\");\n/* harmony import */ var engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/loader/Loader */ \"tui/components/loader/Loader\");\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/layouts/LayoutOneColumnContentWithSidePanel */ \"tui/components/layouts/LayoutOneColumnContentWithSidePanel\");\n/* harmony import */ var tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_survey/components/content/SurveyVoteContent */ \"engage_survey/components/content/SurveyVoteContent\");\n/* harmony import */ var engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var engage_survey_index__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! engage_survey/index */ \"engage_survey/index\");\n/* harmony import */ var engage_survey_index__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(engage_survey_index__WEBPACK_IMPORTED_MODULE_6__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SurveySidePanel: engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0___default.a,\n    ResourceNavigationBar: totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_1___default.a,\n    Loader: tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_3___default.a,\n    Layout: tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_4___default.a,\n    SurveyVoteContent: engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_5___default.a,\n    SurveyVoteTitle: engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_2___default.a\n  },\n  mixins: [engage_survey_index__WEBPACK_IMPORTED_MODULE_6__[\"surveyPageMixin\"]]\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyView.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/loader/Loader */ \"tui/components/loader/Loader\");\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/layouts/LayoutOneColumnContentWithSidePanel */ \"tui/components/layouts/LayoutOneColumnContentWithSidePanel\");\n/* harmony import */ var tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_survey/components/sidepanel/SurveySidePanel */ \"engage_survey/components/sidepanel/SurveySidePanel\");\n/* harmony import */ var engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/components/header/ResourceNavigationBar */ \"totara_engage/components/header/ResourceNavigationBar\");\n/* harmony import */ var totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! engage_survey/components/content/SurveyVoteTitle */ \"engage_survey/components/content/SurveyVoteTitle\");\n/* harmony import */ var engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_survey/components/content/SurveyVoteContent */ \"engage_survey/components/content/SurveyVoteContent\");\n/* harmony import */ var engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var engage_survey_components_content_SurveyResultContent__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! engage_survey/components/content/SurveyResultContent */ \"engage_survey/components/content/SurveyResultContent\");\n/* harmony import */ var engage_survey_components_content_SurveyResultContent__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_content_SurveyResultContent__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var engage_survey_index__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! engage_survey/index */ \"engage_survey/index\");\n/* harmony import */ var engage_survey_index__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(engage_survey_index__WEBPACK_IMPORTED_MODULE_7__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SurveySidePanel: engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_2___default.a,\n    ResourceNavigationBar: totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_3___default.a,\n    Loader: tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_0___default.a,\n    SurveyVoteTitle: engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_4___default.a,\n    SurveyVoteContent: engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_5___default.a,\n    SurveyResultContent: engage_survey_components_content_SurveyResultContent__WEBPACK_IMPORTED_MODULE_6___default.a,\n    Layout: tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_1___default.a\n  },\n  mixins: [engage_survey_index__WEBPACK_IMPORTED_MODULE_7__[\"surveyPageMixin\"]]\n});\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyVoteView.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=template&id=a9924e36&":
/*!********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/CreateSurvey.vue?vue&type=template&id=a9924e36& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-engageSurvey-createSurvey\" },\n    [\n      _c(\"SurveyForm\", {\n        directives: [\n          {\n            name: \"show\",\n            rawName: \"v-show\",\n            value: _vm.stage === 0,\n            expression: \"stage === 0\"\n          }\n        ],\n        attrs: { survey: _vm.survey },\n        on: {\n          next: _vm.next,\n          cancel: function($event) {\n            return _vm.$emit(\"cancel\")\n          }\n        }\n      }),\n      _vm._v(\" \"),\n      _c(\"AccessForm\", {\n        directives: [\n          {\n            name: \"show\",\n            rawName: \"v-show\",\n            value: _vm.stage === 1,\n            expression: \"stage === 1\"\n          }\n        ],\n        attrs: {\n          \"item-id\": \"0\",\n          component: \"engage_survey\",\n          \"show-back\": true,\n          submitting: _vm.submitting,\n          \"selected-access\": _vm.containerValues.access,\n          \"private-disabled\": _vm.privateDisabled,\n          \"restricted-disabled\": _vm.restrictedDisabled,\n          container: _vm.container\n        },\n        on: {\n          done: _vm.done,\n          back: _vm.back,\n          cancel: function($event) {\n            return _vm.$emit(\"cancel\")\n          }\n        }\n      })\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/CreateSurvey.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=template&id=23488603&":
/*!********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/box/RadioBox.vue?vue&type=template&id=23488603& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"FormRow\", {\n    staticClass: \"tui-engageSurvey-radioBox\",\n    attrs: { label: _vm.label, hidden: \"\" },\n    scopedSlots: _vm._u([\n      {\n        key: \"default\",\n        fn: function(ref) {\n          var labelId = ref.labelId\n          return [\n            _c(\n              \"RadioGroup\",\n              {\n                attrs: { \"aria-labelledby\": labelId },\n                model: {\n                  value: _vm.option,\n                  callback: function($$v) {\n                    _vm.option = $$v\n                  },\n                  expression: \"option\"\n                }\n              },\n              _vm._l(_vm.options, function(item) {\n                return _c(\n                  \"Radio\",\n                  {\n                    key: item.id,\n                    staticClass: \"tui-engageSurvey-radioBox__radio\",\n                    attrs: {\n                      name: \"engagesurvey-radiobox\",\n                      value: item.id,\n                      label: item.value\n                    }\n                  },\n                  [_vm._v(\"\\n      \" + _vm._s(item.value) + \"\\n    \")]\n                )\n              }),\n              1\n            )\n          ]\n        }\n      }\n    ])\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/box/RadioBox.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=template&id=ee032a6a&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/box/SquareBox.vue?vue&type=template&id=ee032a6a& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"FormRow\", {\n    staticClass: \"tui-engageSurvey-squareBox\",\n    attrs: { label: _vm.label, hidden: \"\" },\n    scopedSlots: _vm._u([\n      {\n        key: \"default\",\n        fn: function(ref) {\n          var labelId = ref.labelId\n          return [\n            _c(\n              \"CheckboxGroup\",\n              { attrs: { \"aria-labelledby\": labelId } },\n              _vm._l(_vm.options, function(option) {\n                return _c(\n                  \"FormRow\",\n                  { key: option.id },\n                  [\n                    _c(\n                      \"Checkbox\",\n                      {\n                        key: option.id,\n                        staticClass: \"tui-engageSurvey-squareBox__checkbox\",\n                        attrs: {\n                          name: \"engagesurvey-checkbox\",\n                          value: option.id\n                        },\n                        on: {\n                          change: function($event) {\n                            return _vm.$_handleChange(option.id, $event)\n                          }\n                        }\n                      },\n                      [_vm._v(\"\\n        \" + _vm._s(option.value) + \"\\n      \")]\n                    )\n                  ],\n                  1\n                )\n              }),\n              1\n            )\n          ]\n        }\n      }\n    ])\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/box/SquareBox.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=template&id=e48cd9ec&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/card/SurveyCard.vue?vue&type=template&id=e48cd9ec& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-surveyCard\" },\n    [\n      _c(\n        \"CoreCard\",\n        {\n          staticClass: \"tui-surveyCard__cardContent\",\n          class: {\n            \"tui-surveyCard__cardContent__calcHeight\": _vm.showFootnotes,\n            \"tui-surveyCard__cardContent__height\": !_vm.showFootnotes\n          },\n          attrs: { clickable: !_vm.editAble && _vm.voted }\n        },\n        [\n          _c(\n            \"div\",\n            { staticClass: \"tui-surveyCard__cardContent__inner\" },\n            [\n              _c(\n                \"div\",\n                { staticClass: \"tui-surveyCard__cardContent__inner__header\" },\n                [\n                  _c(\n                    \"section\",\n                    {\n                      staticClass:\n                        \"tui-surveyCard__cardContent__inner__header__image\"\n                    },\n                    [\n                      _c(\"img\", {\n                        attrs: { alt: _vm.name, src: _vm.extraData.image }\n                      }),\n                      _vm._v(\" \"),\n                      _c(\n                        \"h3\",\n                        {\n                          staticClass:\n                            \"tui-surveyCard__cardContent__inner__header__title\"\n                        },\n                        [\n                          _vm._v(\n                            \"\\n            \" +\n                              _vm._s(_vm.$str(\"survey\", \"engage_survey\")) +\n                              \"\\n          \"\n                          )\n                        ]\n                      )\n                    ]\n                  ),\n                  _vm._v(\" \"),\n                  _c(\"BookmarkButton\", {\n                    directives: [\n                      {\n                        name: \"show\",\n                        rawName: \"v-show\",\n                        value: !_vm.owned && !_vm.editAble,\n                        expression: \"!owned && !editAble\"\n                      }\n                    ],\n                    staticClass:\n                      \"tui-surveyCard__cardContent__inner__header__bookmark\",\n                    attrs: {\n                      size: \"300\",\n                      bookmarked: _vm.innerBookMarked,\n                      primary: false,\n                      circle: false,\n                      small: true,\n                      transparent: true\n                    },\n                    on: { click: _vm.updateBookmark }\n                  })\n                ],\n                1\n              ),\n              _vm._v(\" \"),\n              _vm.voted && !_vm.editAble\n                ? [\n                    _c(\"SurveyResultBody\", {\n                      attrs: {\n                        name: _vm.name,\n                        \"label-id\": _vm.labelId,\n                        questions: _vm.questions,\n                        access: _vm.access,\n                        \"resource-id\": _vm.instanceId,\n                        url: _vm.url\n                      },\n                      on: {\n                        \"open-result\": function($event) {\n                          _vm.show.result = true\n                        }\n                      }\n                    })\n                  ]\n                : [\n                    _c(\"SurveyCardBody\", {\n                      attrs: {\n                        name: _vm.name,\n                        questions: _vm.questions,\n                        \"resource-id\": _vm.instanceId,\n                        bookmarked: _vm.innerBookMarked,\n                        voted: _vm.voted,\n                        topics: _vm.topics,\n                        access: _vm.access,\n                        owned: _vm.owned,\n                        \"edit-able\": _vm.editAble,\n                        \"label-id\": _vm.labelId,\n                        url: _vm.url\n                      },\n                      on: { voted: _vm.handleVoted }\n                    })\n                  ]\n            ],\n            2\n          )\n        ]\n      ),\n      _vm._v(\" \"),\n      _vm.showFootnotes\n        ? _c(\"Footnotes\", { attrs: { footnotes: _vm.footnotes } })\n        : _vm._e()\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCard.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=template&id=9e81f268&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/card/SurveyCardBody.vue?vue&type=template&id=9e81f268& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"div\", { staticClass: \"tui-surveyCardBody\" }, [\n    _c(\n      \"div\",\n      { staticClass: \"tui-surveyCardBody__title\", attrs: { id: _vm.labelId } },\n      [_vm._v(\"\\n    \" + _vm._s(_vm.name) + \"\\n  \")]\n    ),\n    _vm._v(\" \"),\n    _c(\"div\", { staticClass: \"tui-surveyCardBody__footer\" }, [\n      _vm.showEdit\n        ? _c(\"p\", { staticClass: \"tui-surveyCardBody__text\" }, [\n            _vm._v(\n              \"\\n      \" +\n                _vm._s(_vm.$str(\"noresult\", \"engage_survey\")) +\n                \"\\n    \"\n            )\n          ])\n        : _vm._e(),\n      _vm._v(\" \"),\n      _c(\n        \"div\",\n        { staticClass: \"tui-surveyCardBody__container\" },\n        [\n          !_vm.voted\n            ? _c(\"ActionLink\", {\n                attrs: {\n                  href: _vm.$url(_vm.url, {\n                    page: \"vote\"\n                  }),\n                  text: _vm.$str(\"votenow\", \"engage_survey\"),\n                  styleclass: { primary: true }\n                }\n              })\n            : _vm.showEdit\n            ? _c(\"ActionLink\", {\n                attrs: {\n                  href: _vm.$url(_vm.url, {\n                    page: \"edit\"\n                  }),\n                  styleclass: { primary: true, small: true },\n                  text: _vm.$str(\"editsurvey\", \"engage_survey\"),\n                  \"aria-label\": _vm.$str(\n                    \"editsurveyaccessiblename\",\n                    \"engage_survey\",\n                    _vm.name\n                  )\n                }\n              })\n            : _vm._e(),\n          _vm._v(\" \"),\n          _c(\n            \"div\",\n            { staticClass: \"tui-surveyCardBody__icon\" },\n            [_c(\"AccessIcon\", { attrs: { access: _vm.access, size: \"300\" } })],\n            1\n          )\n        ],\n        1\n      )\n    ])\n  ])\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyCardBody.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=template&id=529d334e&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/card/SurveyResultBody.vue?vue&type=template&id=529d334e& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-surveyResultBody\", on: { click: _vm.navigateTo } },\n    [\n      _c(\"Label\", {\n        staticClass: \"tui-surveyResultBody__title\",\n        attrs: { id: _vm.labelId, label: _vm.name }\n      }),\n      _vm._v(\" \"),\n      _c(\n        \"div\",\n        { staticClass: \"tui-surveyResultBody__progress\" },\n        _vm._l(_vm.questions, function(ref, index) {\n          var votes = ref.votes\n          var id = ref.id\n          var options = ref.options\n          var answertype = ref.answertype\n          return _c(\"SurveyQuestionResult\", {\n            key: index,\n            attrs: {\n              options: options,\n              \"question-id\": id,\n              \"total-votes\": votes,\n              \"answer-type\": answertype\n            }\n          })\n        }),\n        1\n      ),\n      _vm._v(\" \"),\n      _c(\"div\", { staticClass: \"tui-surveyResultBody__footer\" }, [\n        _c(\"div\", { staticClass: \"tui-surveyResultBody__container\" }, [\n          _c(\"p\", { staticClass: \"tui-surveyResultBody__text\" }, [\n            _vm._v(_vm._s(_vm.voteMessage))\n          ]),\n          _vm._v(\" \"),\n          _c(\n            \"div\",\n            { staticClass: \"tui-surveyResultBody__icon\" },\n            [_c(\"AccessIcon\", { attrs: { access: _vm.access, size: \"300\" } })],\n            1\n          )\n        ])\n      ])\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/SurveyResultBody.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=template&id=5954f8bf&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?vue&type=template&id=5954f8bf& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-surveyQuestionResult\" },\n    [\n      _vm._l(_vm.calulatedOptions, function(ref, index) {\n        var votes = ref.votes\n        var value = ref.value\n        return _c(\n          \"div\",\n          { key: index, staticClass: \"tui-surveyQuestionResult__progressBar\" },\n          [\n            _vm.resultContent\n              ? [\n                  _c(\n                    \"div\",\n                    { staticClass: \"tui-surveyQuestionResult__progress\" },\n                    [\n                      _c(\n                        \"div\",\n                        { staticClass: \"tui-surveyQuestionResult__bar\" },\n                        [\n                          _c(\"Progress\", {\n                            attrs: {\n                              small: true,\n                              \"hide-value\": true,\n                              value: _vm.getValues(votes),\n                              max: _vm.totalVotes,\n                              \"hide-background\": _vm.isMultiChoice,\n                              \"show-empty-state\": _vm.isMultiChoice\n                            }\n                          })\n                        ],\n                        1\n                      ),\n                      _vm._v(\" \"),\n                      _c(\n                        \"span\",\n                        { staticClass: \"tui-surveyQuestionResult__count\" },\n                        [_vm._v(\"\\n          \" + _vm._s(votes) + \"\\n        \")]\n                      )\n                    ]\n                  )\n                ]\n              : [\n                  _vm.isMultiChoice\n                    ? [\n                        _c(\n                          \"div\",\n                          {\n                            staticClass:\n                              \"tui-surveyQuestionResult__cardProgress\"\n                          },\n                          [\n                            _c(\n                              \"div\",\n                              { staticClass: \"tui-surveyQuestionResult__bar\" },\n                              [\n                                _c(\"Progress\", {\n                                  attrs: {\n                                    small: true,\n                                    \"hide-value\": true,\n                                    value: _vm.getValues(votes),\n                                    max: _vm.totalVotes,\n                                    \"hide-background\": _vm.isMultiChoice,\n                                    \"show-empty-state\": _vm.isMultiChoice\n                                  }\n                                })\n                              ],\n                              1\n                            ),\n                            _vm._v(\" \"),\n                            _c(\n                              \"span\",\n                              {\n                                staticClass: \"tui-surveyQuestionResult__count\"\n                              },\n                              [\n                                _vm._v(\n                                  \"\\n            \" +\n                                    _vm._s(votes) +\n                                    \"\\n          \"\n                                )\n                              ]\n                            )\n                          ]\n                        )\n                      ]\n                    : [\n                        _c(\"Progress\", {\n                          attrs: {\n                            small: true,\n                            \"hide-value\": true,\n                            value: votes,\n                            max: _vm.totalVotes,\n                            \"hide-background\": _vm.isMultiChoice,\n                            \"show-empty-state\": _vm.isMultiChoice\n                          }\n                        })\n                      ]\n                ],\n            _vm._v(\" \"),\n            _vm.isSingleChoice\n              ? [\n                  _c(\n                    \"span\",\n                    { staticClass: \"tui-surveyQuestionResult__percent\" },\n                    [\n                      _vm._v(\n                        \"\\n        \" +\n                          _vm._s(\n                            _vm.$str(\n                              \"percentage\",\n                              \"engage_survey\",\n                              _vm.percentage(votes)\n                            )\n                          ) +\n                          \"\\n      \"\n                      )\n                    ]\n                  )\n                ]\n              : _vm._e(),\n            _vm._v(\" \"),\n            _c(\"span\", { staticClass: \"tui-surveyQuestionResult__answer\" }, [\n              _vm._v(\"\\n      \" + _vm._s(value) + \"\\n    \")\n            ])\n          ],\n          2\n        )\n      }),\n      _vm._v(\" \"),\n      _vm.resultContent\n        ? [\n            _c(\"div\", { staticClass: \"tui-surveyQuestionResult__votes\" }, [\n              _c(\"span\", [_vm._v(\"Total votes: \" + _vm._s(_vm.totalVotes))])\n            ])\n          ]\n        : _vm._e()\n    ],\n    2\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/card/result/SurveyQuestionResult.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=template&id=421937ad&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/content/SurveyResultContent.vue?vue&type=template&id=421937ad& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return !_vm.$apollo.loading\n    ? _c(\n        \"div\",\n        { staticClass: \"tui-surveyResultContent\" },\n        [\n          _vm._l(_vm.questions, function(ref, index) {\n            var id = ref.id\n            var votes = ref.votes\n            var options = ref.options\n            var answertype = ref.answertype\n            return _c(\"SurveyQuestionResult\", {\n              key: index,\n              attrs: {\n                \"question-id\": id,\n                \"total-votes\": votes,\n                \"answer-type\": answertype,\n                options: options,\n                \"result-content\": true\n              }\n            })\n          }),\n          _vm._v(\" \"),\n          _vm.isMultiChoice\n            ? [\n                _c(\n                  \"div\",\n                  { staticClass: \"tui-surveyResultContent__participant\" },\n                  [\n                    _c(\n                      \"span\",\n                      {\n                        staticClass:\n                          \"tui-surveyResultContent__participantnumber\"\n                      },\n                      [\n                        _vm._v(\n                          \"\\n        \" +\n                            _vm._s(_vm.showNumberOfParticipant) +\n                            \"\\n      \"\n                        )\n                      ]\n                    ),\n                    _vm._v(\"\\n      \" + _vm._s(_vm.showParticipants) + \"\\n    \")\n                  ]\n                )\n              ]\n            : _vm._e()\n        ],\n        2\n      )\n    : _vm._e()\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyResultContent.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=template&id=5650e500&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?vue&type=template&id=5650e500& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-surveyVoteContent\" },\n    [\n      _c(\n        \"Form\",\n        {\n          staticClass: \"tui-surveyVoteContent__form\",\n          attrs: { vertical: true }\n        },\n        [\n          _vm.isSingleChoice\n            ? [\n                _c(\"RadioBox\", {\n                  attrs: { options: _vm.options, label: _vm.label },\n                  model: {\n                    value: _vm.answer,\n                    callback: function($$v) {\n                      _vm.answer = $$v\n                    },\n                    expression: \"answer\"\n                  }\n                })\n              ]\n            : _vm.isMultiChoice\n            ? [\n                _c(\"SquareBox\", {\n                  attrs: { options: _vm.options, label: _vm.label },\n                  model: {\n                    value: _vm.answer,\n                    callback: function($$v) {\n                      _vm.answer = $$v\n                    },\n                    expression: \"answer\"\n                  }\n                })\n              ]\n            : _vm._e(),\n          _vm._v(\" \"),\n          _c(\"Button\", {\n            staticClass: \"tui-surveyVoteContent__button\",\n            attrs: {\n              disabled: null == _vm.answer || _vm.disabled,\n              styleclass: { primary: true },\n              text: _vm.$str(\"vote\", \"engage_survey\"),\n              \"aria-label\": _vm.$str(\"vote\", \"engage_survey\")\n            },\n            on: { click: _vm.vote }\n          })\n        ],\n        2\n      )\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteContent.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=template&id=3d599b1f&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?vue&type=template&id=3d599b1f& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"div\", { staticClass: \"tui-surveyVoteTitle\" }, [\n    _c(\n      \"div\",\n      { staticClass: \"tui-surveyVoteTitle__head\" },\n      [\n        _c(\"h3\", { staticClass: \"tui-surveyVoteTitle__head__title\" }, [\n          _vm._v(\"\\n      \" + _vm._s(_vm.title) + \"\\n    \")\n        ]),\n        _vm._v(\" \"),\n        _c(\"BookmarkButton\", {\n          directives: [\n            {\n              name: \"show\",\n              rawName: \"v-show\",\n              value: !_vm.owned,\n              expression: \"!owned\"\n            }\n          ],\n          attrs: {\n            primary: false,\n            circle: true,\n            bookmarked: _vm.bookmarked,\n            size: \"300\"\n          },\n          on: {\n            click: function($event) {\n              return _vm.$emit(\"bookmark\", $event)\n            }\n          }\n        })\n      ],\n      1\n    )\n  ])\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/content/SurveyVoteTitle.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=template&id=7ce50aec&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/form/SurveyForm.vue?vue&type=template&id=7ce50aec& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"Uniform\",\n    {\n      staticClass: \"tui-totaraEngage-surveyForm\",\n      attrs: {\n        \"initial-values\": _vm.initialValues,\n        vertical: true,\n        \"input-width\": \"full\"\n      },\n      on: { submit: _vm.submit, change: _vm.change }\n    },\n    [\n      _c(\n        \"div\",\n        { staticClass: \"tui-totaraEngage-surveyForm__title\" },\n        [\n          _c(\n            \"FieldContextProvider\",\n            [\n              _c(\"FormText\", {\n                attrs: {\n                  name: \"question\",\n                  validations: function(v) {\n                    return [v.required()]\n                  },\n                  maxlength: 60,\n                  \"aria-label\": _vm.$str(\"formtitle\", \"engage_survey\"),\n                  placeholder: _vm.$str(\"formtitle\", \"engage_survey\"),\n                  disabled: _vm.submitting\n                }\n              })\n            ],\n            1\n          )\n        ],\n        1\n      ),\n      _vm._v(\" \"),\n      _c(\n        \"FormRow\",\n        { attrs: { label: _vm.$str(\"formtypetitle\", \"engage_survey\") } },\n        [\n          _c(\n            \"FormRadioGroup\",\n            {\n              attrs: {\n                name: \"optionType\",\n                validations: function(v) {\n                  return [v.required()]\n                },\n                horizontal: true\n              }\n            },\n            [\n              _c(\n                \"Radio\",\n                {\n                  class: [\n                    \"tui-totaraEngage-surveyForm__optionType\",\n                    \"tui-totaraEngage-surveyForm__optionType--single\"\n                  ],\n                  attrs: { name: \"optionType\", value: _vm.singleChoice }\n                },\n                [\n                  _vm._v(\n                    \"\\n        \" +\n                      _vm._s(_vm.$str(\"optionsingle\", \"engage_survey\")) +\n                      \"\\n      \"\n                  )\n                ]\n              ),\n              _vm._v(\" \"),\n              _c(\n                \"Radio\",\n                {\n                  class: [\n                    \"tui-totaraEngage-surveyForm__optionType\",\n                    \"tui-totaraEngage-surveyForm__optionType--multiple\"\n                  ],\n                  attrs: { name: \"optionType\", value: _vm.multiChoice }\n                },\n                [\n                  _vm._v(\n                    \"\\n        \" +\n                      _vm._s(_vm.$str(\"optionmultiple\", \"engage_survey\")) +\n                      \"\\n      \"\n                  )\n                ]\n              )\n            ],\n            1\n          )\n        ],\n        1\n      ),\n      _vm._v(\" \"),\n      _c(\n        \"FormRow\",\n        {\n          staticClass: \"tui-totaraEngage-surveyForm__answerTitle\",\n          attrs: { label: _vm.$str(\"optionstitle\", \"engage_survey\") }\n        },\n        [\n          _c(\"FieldArray\", {\n            attrs: { path: \"options\" },\n            scopedSlots: _vm._u([\n              {\n                key: \"default\",\n                fn: function(ref) {\n                  var items = ref.items\n                  var push = ref.push\n                  var remove = ref.remove\n                  return [\n                    _c(\"Repeater\", {\n                      staticClass: \"tui-totaraEngage-surveyForm__repeater\",\n                      attrs: {\n                        rows: items,\n                        \"min-rows\": _vm.minOptions,\n                        \"max-rows\": _vm.maxOptions,\n                        disabled: _vm.submitting,\n                        \"delete-icon\": true,\n                        \"allow-deleting-first-items\": false\n                      },\n                      on: {\n                        add: function($event) {\n                          push(_vm.newOption())\n                        },\n                        remove: function(item, i) {\n                          return remove(i)\n                        }\n                      },\n                      scopedSlots: _vm._u(\n                        [\n                          {\n                            key: \"default\",\n                            fn: function(ref) {\n                              var row = ref.row\n                              var index = ref.index\n                              return [\n                                _c(\n                                  \"div\",\n                                  {\n                                    staticClass:\n                                      \"tui-totaraEngage-surveyForm__repeater__input\"\n                                  },\n                                  [\n                                    _c(\n                                      \"FieldContextProvider\",\n                                      [\n                                        _c(\"FormText\", {\n                                          attrs: {\n                                            name: [index, \"text\"],\n                                            validations: function(v) {\n                                              return [v.required()]\n                                            },\n                                            maxlength: 80,\n                                            \"aria-label\": _vm.$str(\n                                              \"option\",\n                                              \"engage_survey\"\n                                            )\n                                          }\n                                        })\n                                      ],\n                                      1\n                                    )\n                                  ],\n                                  1\n                                )\n                              ]\n                            }\n                          }\n                        ],\n                        null,\n                        true\n                      )\n                    })\n                  ]\n                }\n              }\n            ])\n          })\n        ],\n        1\n      ),\n      _vm._v(\" \"),\n      _c(\n        \"ButtonGroup\",\n        {\n          staticClass: \"tui-totaraEngage-surveyForm__buttons\",\n          class: {\n            \"tui-totaraEngage-surveyForm__buttons--right\": _vm.showButtonRight,\n            \"tui-totaraEngage-surveyForm__buttons--left\": _vm.showButtonLeft\n          }\n        },\n        [\n          _c(\"LoadingButton\", {\n            staticClass: \"tui-totaraEngage-surveyForm__button\",\n            attrs: {\n              type: \"submit\",\n              loading: _vm.submitting,\n              primary: true,\n              disabled: _vm.disabled,\n              text: _vm.buttonText\n            }\n          }),\n          _vm._v(\" \"),\n          _c(\"CancelButton\", {\n            staticClass: \"tui-totaraEngage-surveyForm__cancelButton\",\n            attrs: { disabled: _vm.submitting },\n            on: {\n              click: function($event) {\n                return _vm.$emit(\"cancel\")\n              }\n            }\n          })\n        ],\n        1\n      )\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/form/SurveyForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/info/Author.vue?vue&type=template&id=0ef7a3a6&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/info/Author.vue?vue&type=template&id=0ef7a3a6& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-engageSurvey-author\" },\n    [\n      _c(\"Avatar\", {\n        attrs: {\n          src: _vm.profileImageUrl,\n          alt: _vm.profileImageAlt,\n          size: \"xxsmall\"\n        }\n      }),\n      _vm._v(\" \"),\n      _c(\n        \"a\",\n        {\n          staticClass: \"tui-engageSurvey-author__userLink\",\n          attrs: { href: _vm.$url(\"/user/profile.php\", { id: _vm.userId }) }\n        },\n        [_vm._v(\"\\n    \" + _vm._s(_vm.fullname) + \"\\n  \")]\n      )\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/info/Author.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=template&id=2d7d8ec8&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/shape/SurveyBadge.vue?vue&type=template&id=2d7d8ec8& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"svg\",\n    {\n      staticClass: \"tui-surveyBadge\",\n      attrs: {\n        viewBox: \"0 0 105 40\",\n        version: \"1.1\",\n        xmlns: \"http://www.w3.org/2000/svg\",\n        \"xmlns:xlink\": \"http://www.w3.org/1999/xlink\"\n      }\n    },\n    [\n      _c(\"title\", [_vm._v(_vm._s(_vm.$str(\"survey\", \"engage_survey\")))]),\n      _vm._v(\" \"),\n      _c(\"desc\", [_vm._v(_vm._s(_vm.$str(\"survey\", \"engage_survey\")))]),\n      _vm._v(\" \"),\n      _c(\"g\", { staticClass: \"tui-surveyBadge__shapeParent\" }, [\n        _c(\"g\", { attrs: { transform: \"translate(-91.000000, 0.000000)\" } }, [\n          _c(\"g\", [\n            _c(\n              \"g\",\n              { attrs: { transform: \"translate(91.000000, 0.000000)\" } },\n              [\n                _c(\"path\", {\n                  staticClass: \"tui-surveyBadge__shape\",\n                  attrs: {\n                    d:\n                      \"M1.5,33.5 L3.71008242,33.5  L101.289918,33.5 L103.5,33.5 L103.5,6 C103.5,3.51471863 101.485281,1.5 99,1.5 L6,1.5 C3.51471863,1.5 1.5,3.51471863 1.5,6 L1.5,33.5 Z\"\n                  }\n                }),\n                _vm._v(\" \"),\n                _c(\n                  \"g\",\n                  {\n                    staticClass: \"tui-surveyBadge__text\",\n                    attrs: { transform: \"translate(30.500000, 7.000000)\" }\n                  },\n                  [\n                    _c(\"g\", [\n                      _c(\"text\", [\n                        _c(\"tspan\", { attrs: { x: \"0\", y: \"13\" } }, [\n                          _vm._v(\n                            \"\\n                  \" +\n                              _vm._s(_vm.$str(\"survey\", \"engage_survey\")) +\n                              \"\\n                \"\n                          )\n                        ])\n                      ])\n                    ])\n                  ]\n                )\n              ]\n            )\n          ])\n        ])\n      ])\n    ]\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/shape/SurveyBadge.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=template&id=1aef095c&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?vue&type=template&id=1aef095c& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-surveySidePanel\" },\n    [\n      !_vm.$apollo.loading\n        ? [\n            _c(\n              \"ModalPresenter\",\n              {\n                attrs: { open: _vm.openModalFromAction },\n                on: {\n                  \"request-close\": function($event) {\n                    _vm.openModalFromAction = false\n                  }\n                }\n              },\n              [\n                _c(\"EngageWarningModal\", {\n                  attrs: {\n                    \"message-content\": _vm.$str(\n                      \"deletewarningmsg\",\n                      \"engage_survey\"\n                    )\n                  },\n                  on: { delete: _vm.handleDelete }\n                })\n              ],\n              1\n            ),\n            _vm._v(\" \"),\n            _c(\"MiniProfileCard\", {\n              staticClass: \"tui-surveySidePanel__profile\",\n              attrs: {\n                \"no-border\": true,\n                display: _vm.survey.resource.user.card_display\n              },\n              scopedSlots: _vm._u(\n                [\n                  {\n                    key: \"drop-down-items\",\n                    fn: function() {\n                      return [\n                        _vm.survey.owned\n                          ? _c(\n                              \"DropdownItem\",\n                              {\n                                on: {\n                                  click: function($event) {\n                                    _vm.openModalFromAction = true\n                                  }\n                                }\n                              },\n                              [\n                                _vm._v(\n                                  \"\\n          \" +\n                                    _vm._s(\n                                      _vm.$str(\"deletesurvey\", \"engage_survey\")\n                                    ) +\n                                    \"\\n        \"\n                                )\n                              ]\n                            )\n                          : _c(\n                              \"DropdownItem\",\n                              { on: { click: _vm.reportSurvey } },\n                              [\n                                _vm._v(\n                                  \"\\n          \" +\n                                    _vm._s(\n                                      _vm.$str(\"reportsurvey\", \"engage_survey\")\n                                    ) +\n                                    \"\\n        \"\n                                )\n                              ]\n                            )\n                      ]\n                    },\n                    proxy: true\n                  }\n                ],\n                null,\n                false,\n                1328572221\n              )\n            }),\n            _vm._v(\" \"),\n            _c(\n              \"Tabs\",\n              {\n                staticClass: \"tui-surveySidePanel__tabs\",\n                attrs: { \"transparent-tabs\": true }\n              },\n              [\n                _c(\n                  \"Tab\",\n                  {\n                    staticClass: \"tui-surveySidePanel__tabs__overview\",\n                    attrs: {\n                      id: \"overview\",\n                      name: _vm.$str(\"overview\", \"totara_engage\"),\n                      disabled: true\n                    }\n                  },\n                  [\n                    _c(\n                      \"p\",\n                      {\n                        staticClass:\n                          \"tui-surveySidePanel__tabs__overview__timeDescription\"\n                      },\n                      [\n                        _vm._v(\n                          \"\\n          \" +\n                            _vm._s(_vm.survey.timedescription) +\n                            \"\\n        \"\n                        )\n                      ]\n                    ),\n                    _vm._v(\" \"),\n                    _vm.survey.owned\n                      ? _c(\"AccessSetting\", {\n                          attrs: {\n                            \"item-id\": _vm.resourceId,\n                            component: \"engage_survey\",\n                            \"access-value\": _vm.survey.resource.access,\n                            topics: _vm.survey.topics,\n                            submitting: false,\n                            \"open-modal\": _vm.openModalFromButtonLabel,\n                            \"enable-time-view\": false\n                          },\n                          on: {\n                            \"close-modal\": function($event) {\n                              _vm.openModalFromButtonLabel = false\n                            },\n                            \"access-update\": _vm.updateAccess\n                          }\n                        })\n                      : _c(\"AccessDisplay\", {\n                          attrs: {\n                            \"access-value\": _vm.survey.resource.access,\n                            topics: _vm.survey.topics,\n                            \"show-button\": false\n                          }\n                        }),\n                    _vm._v(\" \"),\n                    _c(\"MediaSetting\", {\n                      attrs: {\n                        owned: _vm.survey.owned,\n                        \"access-value\": _vm.survey.resource.access,\n                        \"instance-id\": _vm.resourceId,\n                        \"shared-by-count\": _vm.survey.sharedbycount,\n                        \"like-button-aria-label\": _vm.likeButtonLabel,\n                        liked: _vm.survey.reacted,\n                        \"component-name\": \"engage_survey\"\n                      },\n                      on: {\n                        \"access-update\": _vm.updateAccess,\n                        \"access-modal\": function($event) {\n                          _vm.openModalFromButtonLabel = true\n                        },\n                        \"update-like-status\": _vm.updateLikeStatus\n                      }\n                    })\n                  ],\n                  1\n                )\n              ],\n              1\n            )\n          ]\n        : _vm._e()\n    ],\n    2\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/components/sidepanel/SurveySidePanel.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=template&id=d0b476e4&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/pages/SurveyEditView.vue?vue&type=template&id=d0b476e4& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"Layout\", {\n    staticClass: \"tui-surveyEditView\",\n    scopedSlots: _vm._u(\n      [\n        _vm.backButton || _vm.navigationButtons\n          ? {\n              key: \"header\",\n              fn: function() {\n                return [\n                  _c(\"ResourceNavigationBar\", {\n                    attrs: {\n                      \"back-button\": _vm.backButton,\n                      \"navigation-buttons\": _vm.navigationButtons\n                    }\n                  })\n                ]\n              },\n              proxy: true\n            }\n          : null,\n        {\n          key: \"column\",\n          fn: function() {\n            return [\n              _c(\"Loader\", {\n                attrs: { loading: _vm.$apollo.loading, fullpage: true }\n              }),\n              _vm._v(\" \"),\n              !_vm.$apollo.loading\n                ? _c(\n                    \"div\",\n                    { staticClass: \"tui-surveyEditView__layout\" },\n                    [\n                      _c(\"SurveyForm\", {\n                        staticClass: \"tui-surveyEditView__layout__content\",\n                        attrs: {\n                          survey: _vm.surveyInstance,\n                          \"button-content\": _vm.$str(\"save\", \"engage_survey\"),\n                          submitting: _vm.submitting,\n                          \"show-button-right\": false\n                        },\n                        on: { next: _vm.handleSave, cancel: _vm.handleCancel }\n                      })\n                    ],\n                    1\n                  )\n                : _vm._e()\n            ]\n          },\n          proxy: true\n        },\n        {\n          key: \"sidepanel\",\n          fn: function() {\n            return [\n              _c(\"SurveySidePanel\", {\n                attrs: { \"resource-id\": _vm.resourceId }\n              })\n            ]\n          },\n          proxy: true\n        }\n      ],\n      null,\n      true\n    )\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyEditView.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=template&id=3f5a87e4&":
/*!*************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/pages/SurveyView.vue?vue&type=template&id=3f5a87e4& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"Layout\", {\n    staticClass: \"tui-surveyView\",\n    scopedSlots: _vm._u(\n      [\n        _vm.backButton || _vm.navigationButtons\n          ? {\n              key: \"header\",\n              fn: function() {\n                return [\n                  _c(\"ResourceNavigationBar\", {\n                    attrs: {\n                      \"back-button\": _vm.backButton,\n                      \"navigation-buttons\": _vm.navigationButtons\n                    }\n                  })\n                ]\n              },\n              proxy: true\n            }\n          : null,\n        {\n          key: \"column\",\n          fn: function() {\n            return [\n              _c(\"Loader\", {\n                attrs: { loading: _vm.$apollo.loading, fullpage: true }\n              }),\n              _vm._v(\" \"),\n              !_vm.$apollo.loading\n                ? _c(\"div\", { staticClass: \"tui-surveyView__layout\" }, [\n                    _c(\n                      \"div\",\n                      { staticClass: \"tui-surveyView__layout__content\" },\n                      [\n                        _c(\"SurveyVoteTitle\", {\n                          staticClass: \"tui-surveyView__layout__content__title\",\n                          attrs: {\n                            title: _vm.firstQuestion.value,\n                            owned: _vm.survey.owned\n                          }\n                        }),\n                        _vm._v(\" \"),\n                        _c(\"SurveyVoteContent\", {\n                          attrs: {\n                            \"answer-type\": _vm.firstQuestion.answertype,\n                            options: _vm.firstQuestion.options,\n                            \"question-id\": _vm.firstQuestion.id,\n                            \"resource-id\": _vm.resourceId,\n                            disabled: true,\n                            label: _vm.firstQuestion.value\n                          }\n                        })\n                      ],\n                      1\n                    )\n                  ])\n                : _vm._e()\n            ]\n          },\n          proxy: true\n        },\n        {\n          key: \"sidepanel\",\n          fn: function() {\n            return [\n              _c(\"SurveySidePanel\", {\n                attrs: { \"resource-id\": _vm.resourceId }\n              })\n            ]\n          },\n          proxy: true\n        }\n      ],\n      null,\n      true\n    )\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyView.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=template&id=4f6ac96e&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_survey/src/pages/SurveyVoteView.vue?vue&type=template&id=4f6ac96e& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"Layout\", {\n    staticClass: \"tui-surveyVoteView\",\n    scopedSlots: _vm._u(\n      [\n        _vm.backButton || _vm.navigationButtons\n          ? {\n              key: \"header\",\n              fn: function() {\n                return [\n                  _c(\"ResourceNavigationBar\", {\n                    attrs: {\n                      \"back-button\": _vm.backButton,\n                      \"navigation-buttons\": _vm.navigationButtons\n                    }\n                  })\n                ]\n              },\n              proxy: true\n            }\n          : null,\n        {\n          key: \"column\",\n          fn: function() {\n            return [\n              _c(\"Loader\", {\n                attrs: { loading: _vm.$apollo.loading, fullpage: true }\n              }),\n              _vm._v(\" \"),\n              !_vm.$apollo.loading\n                ? _c(\"div\", { staticClass: \"tui-surveyVoteView__layout\" }, [\n                    _c(\n                      \"div\",\n                      { staticClass: \"tui-surveyVoteView__layout__content\" },\n                      [\n                        _c(\"SurveyVoteTitle\", {\n                          staticClass:\n                            \"tui-surveyVoteView__layout__content__title\",\n                          attrs: {\n                            title: _vm.firstQuestion.value,\n                            bookmarked: _vm.bookmarked,\n                            owned: _vm.survey.owned\n                          },\n                          on: { bookmark: _vm.updateBookmark }\n                        }),\n                        _vm._v(\" \"),\n                        !_vm.survey.voted && !_vm.survey.owned\n                          ? _c(\"SurveyVoteContent\", {\n                              attrs: {\n                                \"answer-type\": _vm.firstQuestion.answertype,\n                                options: _vm.firstQuestion.options,\n                                \"question-id\": _vm.firstQuestion.id,\n                                \"resource-id\": _vm.resourceId,\n                                label: _vm.firstQuestion.value\n                              }\n                            })\n                          : _c(\"SurveyResultContent\", {\n                              attrs: { \"resource-id\": _vm.resourceId }\n                            })\n                      ],\n                      1\n                    )\n                  ])\n                : _vm._e()\n            ]\n          },\n          proxy: true\n        },\n        {\n          key: \"sidepanel\",\n          fn: function() {\n            return [\n              _c(\"SurveySidePanel\", {\n                attrs: { \"resource-id\": _vm.resourceId }\n              })\n            ]\n          },\n          proxy: true\n        }\n      ],\n      null,\n      true\n    )\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/component/engage_survey/src/pages/SurveyVoteView.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./server/totara/engage/resources/survey/webapi/ajax/create_answer.graphql":
/*!*********************************************************************************!*\
  !*** ./server/totara/engage/resources/survey/webapi/ajax/create_answer.graphql ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_create_answer\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"questionid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"options\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"result\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_create_answer\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"questionid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"questionid\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"options\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"options\"}}}],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/survey/webapi/ajax/create_answer.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/survey/webapi/ajax/create_survey.graphql":
/*!*********************************************************************************!*\
  !*** ./server/totara/engage/resources/survey/webapi/ajax/create_survey.graphql ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_create_survey\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"timeexpired\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"Int\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_access\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"questions\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_question_parameter\"}}}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_recipient_in\"}}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"survey\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_create\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"timeexpired\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"timeexpired\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"questions\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"questions\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timeexpired\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"resource\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimagealt\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimageurl\"},\"arguments\":[],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questions\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"answertype\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"options\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questionid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questionresults\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"answertype\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"votes\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"participants\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"options\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"votes\"},\"arguments\":[],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timedescription\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"owned\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"voted\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"sharedbycount\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"reacted\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/survey/webapi/ajax/create_survey.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/survey/webapi/ajax/delete_survey.graphql":
/*!*********************************************************************************!*\
  !*** ./server/totara/engage/resources/survey/webapi/ajax/delete_survey.graphql ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_delete_survey\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"result\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_delete\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}}}],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/survey/webapi/ajax/delete_survey.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql":
/*!******************************************************************************!*\
  !*** ./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_get_survey\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"survey\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_instance\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timeexpired\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"resource\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"card_display\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_picture_alt\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_picture_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"display_fields\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"associate_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"label\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"is_custom\"},\"arguments\":[],\"directives\":[]}]}}]}}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questions\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"answertype\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"options\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questionid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questionresults\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"answertype\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"votes\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"participants\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"options\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"votes\"},\"arguments\":[],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timedescription\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"owned\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"voted\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"sharedbycount\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"reacted\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/survey/webapi/ajax/update_survey.graphql":
/*!*********************************************************************************!*\
  !*** ./server/totara/engage/resources/survey/webapi/ajax/update_survey.graphql ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_update_survey\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"timeexpired\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"Int\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_access\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"questions\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_question_parameter\"}}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_recipient_in\"}}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"survey\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_update\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"timeexpired\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"timeexpired\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"questions\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"questions\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timeexpired\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"resource\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimagealt\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimageurl\"},\"arguments\":[],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questions\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"answertype\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"options\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questionid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questionresults\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"answertype\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"votes\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"participants\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"options\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"votes\"},\"arguments\":[],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timedescription\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"owned\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"voted\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"sharedbycount\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"reacted\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/survey/webapi/ajax/update_survey.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/survey/webapi/ajax/vote_result.graphql":
/*!*******************************************************************************!*\
  !*** ./server/totara/engage/resources/survey/webapi/ajax/vote_result.graphql ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_vote_result\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"questions\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_vote_result\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"votes\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"answertype\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"participants\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"options\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questionid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"votes\"},\"arguments\":[],\"directives\":[]}]}}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/survey/webapi/ajax/vote_result.graphql?");

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

/***/ "engage_survey/components/box/RadioBox":
/*!*************************************************************************!*\
  !*** external "tui.require(\"engage_survey/components/box/RadioBox\")" ***!
  \*************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/components/box/RadioBox\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/components/box/RadioBox\\%22)%22?");

/***/ }),

/***/ "engage_survey/components/box/SquareBox":
/*!**************************************************************************!*\
  !*** external "tui.require(\"engage_survey/components/box/SquareBox\")" ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/components/box/SquareBox\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/components/box/SquareBox\\%22)%22?");

/***/ }),

/***/ "engage_survey/components/card/SurveyCardBody":
/*!********************************************************************************!*\
  !*** external "tui.require(\"engage_survey/components/card/SurveyCardBody\")" ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/components/card/SurveyCardBody\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/components/card/SurveyCardBody\\%22)%22?");

/***/ }),

/***/ "engage_survey/components/card/SurveyResultBody":
/*!**********************************************************************************!*\
  !*** external "tui.require(\"engage_survey/components/card/SurveyResultBody\")" ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/components/card/SurveyResultBody\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/components/card/SurveyResultBody\\%22)%22?");

/***/ }),

/***/ "engage_survey/components/card/result/SurveyQuestionResult":
/*!*********************************************************************************************!*\
  !*** external "tui.require(\"engage_survey/components/card/result/SurveyQuestionResult\")" ***!
  \*********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/components/card/result/SurveyQuestionResult\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/components/card/result/SurveyQuestionResult\\%22)%22?");

/***/ }),

/***/ "engage_survey/components/content/SurveyResultContent":
/*!****************************************************************************************!*\
  !*** external "tui.require(\"engage_survey/components/content/SurveyResultContent\")" ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/components/content/SurveyResultContent\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/components/content/SurveyResultContent\\%22)%22?");

/***/ }),

/***/ "engage_survey/components/content/SurveyVoteContent":
/*!**************************************************************************************!*\
  !*** external "tui.require(\"engage_survey/components/content/SurveyVoteContent\")" ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/components/content/SurveyVoteContent\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/components/content/SurveyVoteContent\\%22)%22?");

/***/ }),

/***/ "engage_survey/components/content/SurveyVoteTitle":
/*!************************************************************************************!*\
  !*** external "tui.require(\"engage_survey/components/content/SurveyVoteTitle\")" ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/components/content/SurveyVoteTitle\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/components/content/SurveyVoteTitle\\%22)%22?");

/***/ }),

/***/ "engage_survey/components/form/SurveyForm":
/*!****************************************************************************!*\
  !*** external "tui.require(\"engage_survey/components/form/SurveyForm\")" ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/components/form/SurveyForm\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/components/form/SurveyForm\\%22)%22?");

/***/ }),

/***/ "engage_survey/components/sidepanel/SurveySidePanel":
/*!**************************************************************************************!*\
  !*** external "tui.require(\"engage_survey/components/sidepanel/SurveySidePanel\")" ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/components/sidepanel/SurveySidePanel\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/components/sidepanel/SurveySidePanel\\%22)%22?");

/***/ }),

/***/ "engage_survey/index":
/*!*******************************************************!*\
  !*** external "tui.require(\"engage_survey/index\")" ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/index\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/index\\%22)%22?");

/***/ }),

/***/ "engage_survey/mixins/surveypage_mixin":
/*!*************************************************************************!*\
  !*** external "tui.require(\"engage_survey/mixins/surveypage_mixin\")" ***!
  \*************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/mixins/surveypage_mixin\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/mixins/surveypage_mixin\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/buttons/BookmarkButton":
/*!***********************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/buttons/BookmarkButton\")" ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/buttons/BookmarkButton\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/buttons/BookmarkButton\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/buttons/LoadingButton":
/*!**********************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/buttons/LoadingButton\")" ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/buttons/LoadingButton\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/buttons/LoadingButton\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/card/Footnotes":
/*!***************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/card/Footnotes\")" ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/card/Footnotes\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/card/Footnotes\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/form/AccessForm":
/*!****************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/form/AccessForm\")" ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/form/AccessForm\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/form/AccessForm\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/header/ResourceNavigationBar":
/*!*****************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/header/ResourceNavigationBar\")" ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/header/ResourceNavigationBar\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/header/ResourceNavigationBar\\%22)%22?");

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

/***/ "tui/apollo_client":
/*!*****************************************************!*\
  !*** external "tui.require(\"tui/apollo_client\")" ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/apollo_client\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/apollo_client\\%22)%22?");

/***/ }),

/***/ "tui/components/avatar/Avatar":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/avatar/Avatar\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/avatar/Avatar\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/avatar/Avatar\\%22)%22?");

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

/***/ "tui/components/form/Checkbox":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/form/Checkbox\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/Checkbox\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/Checkbox\\%22)%22?");

/***/ }),

/***/ "tui/components/form/CheckboxGroup":
/*!*********************************************************************!*\
  !*** external "tui.require(\"tui/components/form/CheckboxGroup\")" ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/CheckboxGroup\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/CheckboxGroup\\%22)%22?");

/***/ }),

/***/ "tui/components/form/Form":
/*!************************************************************!*\
  !*** external "tui.require(\"tui/components/form/Form\")" ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/Form\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/Form\\%22)%22?");

/***/ }),

/***/ "tui/components/form/Label":
/*!*************************************************************!*\
  !*** external "tui.require(\"tui/components/form/Label\")" ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/Label\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/Label\\%22)%22?");

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

/***/ "tui/components/layouts/LayoutOneColumnContentWithSidePanel":
/*!**********************************************************************************************!*\
  !*** external "tui.require(\"tui/components/layouts/LayoutOneColumnContentWithSidePanel\")" ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/layouts/LayoutOneColumnContentWithSidePanel\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/layouts/LayoutOneColumnContentWithSidePanel\\%22)%22?");

/***/ }),

/***/ "tui/components/links/ActionLink":
/*!*******************************************************************!*\
  !*** external "tui.require(\"tui/components/links/ActionLink\")" ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/links/ActionLink\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/links/ActionLink\\%22)%22?");

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

/***/ "tui/components/profile/MiniProfileCard":
/*!**************************************************************************!*\
  !*** external "tui.require(\"tui/components/profile/MiniProfileCard\")" ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/profile/MiniProfileCard\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/profile/MiniProfileCard\\%22)%22?");

/***/ }),

/***/ "tui/components/progress/Progress":
/*!********************************************************************!*\
  !*** external "tui.require(\"tui/components/progress/Progress\")" ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/progress/Progress\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/progress/Progress\\%22)%22?");

/***/ }),

/***/ "tui/components/reform/FieldContextProvider":
/*!******************************************************************************!*\
  !*** external "tui.require(\"tui/components/reform/FieldContextProvider\")" ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/reform/FieldContextProvider\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/reform/FieldContextProvider\\%22)%22?");

/***/ }),

/***/ "tui/components/tabs/Tab":
/*!***********************************************************!*\
  !*** external "tui.require(\"tui/components/tabs/Tab\")" ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/tabs/Tab\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/tabs/Tab\\%22)%22?");

/***/ }),

/***/ "tui/components/tabs/Tabs":
/*!************************************************************!*\
  !*** external "tui.require(\"tui/components/tabs/Tabs\")" ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/tabs/Tabs\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/tabs/Tabs\\%22)%22?");

/***/ }),

/***/ "tui/components/uniform":
/*!**********************************************************!*\
  !*** external "tui.require(\"tui/components/uniform\")" ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/uniform\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/uniform\\%22)%22?");

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