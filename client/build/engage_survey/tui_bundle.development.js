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
/******/ 		"engage_survey/tui_bundle.development": 0
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
/******/ 	deferredModules.push(["./client/src/engage_survey/tui.json","tui/vendors.development"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/src/engage_survey/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!********************************************************************************************!*\
  !*** ./client/src/engage_survey/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./CreateSurvey\": \"./client/src/engage_survey/components/CreateSurvey.vue\",\n\t\"./CreateSurvey.vue\": \"./client/src/engage_survey/components/CreateSurvey.vue\",\n\t\"./box/RadioBox\": \"./client/src/engage_survey/components/box/RadioBox.vue\",\n\t\"./box/RadioBox.vue\": \"./client/src/engage_survey/components/box/RadioBox.vue\",\n\t\"./box/SquareBox\": \"./client/src/engage_survey/components/box/SquareBox.vue\",\n\t\"./box/SquareBox.vue\": \"./client/src/engage_survey/components/box/SquareBox.vue\",\n\t\"./button/SurveyBackButton\": \"./client/src/engage_survey/components/button/SurveyBackButton.vue\",\n\t\"./button/SurveyBackButton.vue\": \"./client/src/engage_survey/components/button/SurveyBackButton.vue\",\n\t\"./card/SurveyCard\": \"./client/src/engage_survey/components/card/SurveyCard.vue\",\n\t\"./card/SurveyCard.vue\": \"./client/src/engage_survey/components/card/SurveyCard.vue\",\n\t\"./card/SurveyCardBody\": \"./client/src/engage_survey/components/card/SurveyCardBody.vue\",\n\t\"./card/SurveyCardBody.vue\": \"./client/src/engage_survey/components/card/SurveyCardBody.vue\",\n\t\"./card/SurveyResultBody\": \"./client/src/engage_survey/components/card/SurveyResultBody.vue\",\n\t\"./card/SurveyResultBody.vue\": \"./client/src/engage_survey/components/card/SurveyResultBody.vue\",\n\t\"./card/result/SurveyQuestionResult\": \"./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue\",\n\t\"./card/result/SurveyQuestionResult.vue\": \"./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue\",\n\t\"./content/SurveyResultContent\": \"./client/src/engage_survey/components/content/SurveyResultContent.vue\",\n\t\"./content/SurveyResultContent.vue\": \"./client/src/engage_survey/components/content/SurveyResultContent.vue\",\n\t\"./content/SurveyVoteContent\": \"./client/src/engage_survey/components/content/SurveyVoteContent.vue\",\n\t\"./content/SurveyVoteContent.vue\": \"./client/src/engage_survey/components/content/SurveyVoteContent.vue\",\n\t\"./content/SurveyVoteTitle\": \"./client/src/engage_survey/components/content/SurveyVoteTitle.vue\",\n\t\"./content/SurveyVoteTitle.vue\": \"./client/src/engage_survey/components/content/SurveyVoteTitle.vue\",\n\t\"./form/SurveyForm\": \"./client/src/engage_survey/components/form/SurveyForm.vue\",\n\t\"./form/SurveyForm.vue\": \"./client/src/engage_survey/components/form/SurveyForm.vue\",\n\t\"./info/Author\": \"./client/src/engage_survey/components/info/Author.vue\",\n\t\"./info/Author.vue\": \"./client/src/engage_survey/components/info/Author.vue\",\n\t\"./shape/SurveyBadge\": \"./client/src/engage_survey/components/shape/SurveyBadge.vue\",\n\t\"./shape/SurveyBadge.vue\": \"./client/src/engage_survey/components/shape/SurveyBadge.vue\",\n\t\"./sidepanel/SurveyBaseSidePanel\": \"./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue\",\n\t\"./sidepanel/SurveyBaseSidePanel.vue\": \"./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue\",\n\t\"./sidepanel/SurveySidePanel\": \"./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue\",\n\t\"./sidepanel/SurveySidePanel.vue\": \"./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/src/engage_survey/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/src/engage_survey/components_sync_^(?:(?");

/***/ }),

/***/ "./client/src/engage_survey/components/CreateSurvey.vue":
/*!**************************************************************!*\
  !*** ./client/src/engage_survey/components/CreateSurvey.vue ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _CreateSurvey_vue_vue_type_template_id_a3fdee92___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./CreateSurvey.vue?vue&type=template&id=a3fdee92& */ \"./client/src/engage_survey/components/CreateSurvey.vue?vue&type=template&id=a3fdee92&\");\n/* harmony import */ var _CreateSurvey_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CreateSurvey.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/CreateSurvey.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _CreateSurvey_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _CreateSurvey_vue_vue_type_template_id_a3fdee92___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _CreateSurvey_vue_vue_type_template_id_a3fdee92___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/CreateSurvey.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/CreateSurvey.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/CreateSurvey.vue?vue&type=script&lang=js&":
/*!***************************************************************************************!*\
  !*** ./client/src/engage_survey/components/CreateSurvey.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateSurvey_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./CreateSurvey.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/CreateSurvey.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateSurvey_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/CreateSurvey.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/CreateSurvey.vue?vue&type=template&id=a3fdee92&":
/*!*********************************************************************************************!*\
  !*** ./client/src/engage_survey/components/CreateSurvey.vue?vue&type=template&id=a3fdee92& ***!
  \*********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateSurvey_vue_vue_type_template_id_a3fdee92___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./CreateSurvey.vue?vue&type=template&id=a3fdee92& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/CreateSurvey.vue?vue&type=template&id=a3fdee92&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateSurvey_vue_vue_type_template_id_a3fdee92___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateSurvey_vue_vue_type_template_id_a3fdee92___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/CreateSurvey.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/box/RadioBox.vue":
/*!**************************************************************!*\
  !*** ./client/src/engage_survey/components/box/RadioBox.vue ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _RadioBox_vue_vue_type_template_id_2612b5d5___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RadioBox.vue?vue&type=template&id=2612b5d5& */ \"./client/src/engage_survey/components/box/RadioBox.vue?vue&type=template&id=2612b5d5&\");\n/* harmony import */ var _RadioBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RadioBox.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/box/RadioBox.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _RadioBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _RadioBox_vue_vue_type_template_id_2612b5d5___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _RadioBox_vue_vue_type_template_id_2612b5d5___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/box/RadioBox.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/box/RadioBox.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/box/RadioBox.vue?vue&type=script&lang=js&":
/*!***************************************************************************************!*\
  !*** ./client/src/engage_survey/components/box/RadioBox.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RadioBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./RadioBox.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/box/RadioBox.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RadioBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/box/RadioBox.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/box/RadioBox.vue?vue&type=template&id=2612b5d5&":
/*!*********************************************************************************************!*\
  !*** ./client/src/engage_survey/components/box/RadioBox.vue?vue&type=template&id=2612b5d5& ***!
  \*********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RadioBox_vue_vue_type_template_id_2612b5d5___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./RadioBox.vue?vue&type=template&id=2612b5d5& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/box/RadioBox.vue?vue&type=template&id=2612b5d5&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RadioBox_vue_vue_type_template_id_2612b5d5___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_RadioBox_vue_vue_type_template_id_2612b5d5___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/box/RadioBox.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/box/SquareBox.vue":
/*!***************************************************************!*\
  !*** ./client/src/engage_survey/components/box/SquareBox.vue ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SquareBox_vue_vue_type_template_id_410b958e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SquareBox.vue?vue&type=template&id=410b958e& */ \"./client/src/engage_survey/components/box/SquareBox.vue?vue&type=template&id=410b958e&\");\n/* harmony import */ var _SquareBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SquareBox.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/box/SquareBox.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SquareBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SquareBox_vue_vue_type_template_id_410b958e___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SquareBox_vue_vue_type_template_id_410b958e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/box/SquareBox.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/box/SquareBox.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/box/SquareBox.vue?vue&type=script&lang=js&":
/*!****************************************************************************************!*\
  !*** ./client/src/engage_survey/components/box/SquareBox.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SquareBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SquareBox.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/box/SquareBox.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SquareBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/box/SquareBox.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/box/SquareBox.vue?vue&type=template&id=410b958e&":
/*!**********************************************************************************************!*\
  !*** ./client/src/engage_survey/components/box/SquareBox.vue?vue&type=template&id=410b958e& ***!
  \**********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SquareBox_vue_vue_type_template_id_410b958e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SquareBox.vue?vue&type=template&id=410b958e& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/box/SquareBox.vue?vue&type=template&id=410b958e&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SquareBox_vue_vue_type_template_id_410b958e___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SquareBox_vue_vue_type_template_id_410b958e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/box/SquareBox.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/button/SurveyBackButton.vue":
/*!*************************************************************************!*\
  !*** ./client/src/engage_survey/components/button/SurveyBackButton.vue ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyBackButton_vue_vue_type_template_id_077cc8bf___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyBackButton.vue?vue&type=template&id=077cc8bf& */ \"./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=template&id=077cc8bf&\");\n/* harmony import */ var _SurveyBackButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyBackButton.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyBackButton_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveyBackButton.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyBackButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyBackButton_vue_vue_type_template_id_077cc8bf___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyBackButton_vue_vue_type_template_id_077cc8bf___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyBackButton_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveyBackButton_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/button/SurveyBackButton.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/button/SurveyBackButton.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!************************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBackButton_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyBackButton.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBackButton_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/button/SurveyBackButton.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBackButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyBackButton.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBackButton_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/button/SurveyBackButton.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=template&id=077cc8bf&":
/*!********************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=template&id=077cc8bf& ***!
  \********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBackButton_vue_vue_type_template_id_077cc8bf___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyBackButton.vue?vue&type=template&id=077cc8bf& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=template&id=077cc8bf&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBackButton_vue_vue_type_template_id_077cc8bf___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBackButton_vue_vue_type_template_id_077cc8bf___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/button/SurveyBackButton.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/SurveyCard.vue":
/*!*****************************************************************!*\
  !*** ./client/src/engage_survey/components/card/SurveyCard.vue ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyCard_vue_vue_type_template_id_346c79f8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyCard.vue?vue&type=template&id=346c79f8& */ \"./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=template&id=346c79f8&\");\n/* harmony import */ var _SurveyCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyCard.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyCard_vue_vue_type_template_id_346c79f8___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyCard_vue_vue_type_template_id_346c79f8___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveyCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/card/SurveyCard.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCard.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCard.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=script&lang=js&":
/*!******************************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyCard.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCard.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=template&id=346c79f8&":
/*!************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=template&id=346c79f8& ***!
  \************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_template_id_346c79f8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyCard.vue?vue&type=template&id=346c79f8& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=template&id=346c79f8&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_template_id_346c79f8___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCard_vue_vue_type_template_id_346c79f8___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCard.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/SurveyCardBody.vue":
/*!*********************************************************************!*\
  !*** ./client/src/engage_survey/components/card/SurveyCardBody.vue ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyCardBody_vue_vue_type_template_id_64a8c6ba___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyCardBody.vue?vue&type=template&id=64a8c6ba& */ \"./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=template&id=64a8c6ba&\");\n/* harmony import */ var _SurveyCardBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyCardBody.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyCardBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyCardBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyCardBody_vue_vue_type_template_id_64a8c6ba___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyCardBody_vue_vue_type_template_id_64a8c6ba___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyCardBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveyCardBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/card/SurveyCardBody.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCardBody.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!********************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \********************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCardBody.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyCardBody.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCardBody.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=template&id=64a8c6ba&":
/*!****************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=template&id=64a8c6ba& ***!
  \****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_template_id_64a8c6ba___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyCardBody.vue?vue&type=template&id=64a8c6ba& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=template&id=64a8c6ba&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_template_id_64a8c6ba___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyCardBody_vue_vue_type_template_id_64a8c6ba___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCardBody.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/SurveyResultBody.vue":
/*!***********************************************************************!*\
  !*** ./client/src/engage_survey/components/card/SurveyResultBody.vue ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyResultBody_vue_vue_type_template_id_372ae2c7___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyResultBody.vue?vue&type=template&id=372ae2c7& */ \"./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=template&id=372ae2c7&\");\n/* harmony import */ var _SurveyResultBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyResultBody.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyResultBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyResultBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyResultBody_vue_vue_type_template_id_372ae2c7___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyResultBody_vue_vue_type_template_id_372ae2c7___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyResultBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveyResultBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/card/SurveyResultBody.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyResultBody.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**********************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**********************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyResultBody.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=script&lang=js&":
/*!************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyResultBody.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyResultBody.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=template&id=372ae2c7&":
/*!******************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=template&id=372ae2c7& ***!
  \******************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_template_id_372ae2c7___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyResultBody.vue?vue&type=template&id=372ae2c7& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=template&id=372ae2c7&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_template_id_372ae2c7___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultBody_vue_vue_type_template_id_372ae2c7___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyResultBody.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue":
/*!**********************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue ***!
  \**********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyQuestionResult_vue_vue_type_template_id_7dab0b91___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyQuestionResult.vue?vue&type=template&id=7dab0b91& */ \"./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=template&id=7dab0b91&\");\n/* harmony import */ var _SurveyQuestionResult_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyQuestionResult.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyQuestionResult_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyQuestionResult_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyQuestionResult_vue_vue_type_template_id_7dab0b91___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyQuestionResult_vue_vue_type_template_id_7dab0b91___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyQuestionResult_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveyQuestionResult_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/card/result/SurveyQuestionResult.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*********************************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*********************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyQuestionResult.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=template&id=7dab0b91&":
/*!*****************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=template&id=7dab0b91& ***!
  \*****************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_template_id_7dab0b91___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyQuestionResult.vue?vue&type=template&id=7dab0b91& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=template&id=7dab0b91&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_template_id_7dab0b91___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyQuestionResult_vue_vue_type_template_id_7dab0b91___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/content/SurveyResultContent.vue":
/*!*****************************************************************************!*\
  !*** ./client/src/engage_survey/components/content/SurveyResultContent.vue ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyResultContent_vue_vue_type_template_id_3919acca___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyResultContent.vue?vue&type=template&id=3919acca& */ \"./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=template&id=3919acca&\");\n/* harmony import */ var _SurveyResultContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyResultContent.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyResultContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyResultContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyResultContent_vue_vue_type_template_id_3919acca___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyResultContent_vue_vue_type_template_id_3919acca___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyResultContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveyResultContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/content/SurveyResultContent.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyResultContent.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyResultContent.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyResultContent.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyResultContent.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=template&id=3919acca&":
/*!************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=template&id=3919acca& ***!
  \************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_template_id_3919acca___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyResultContent.vue?vue&type=template&id=3919acca& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=template&id=3919acca&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_template_id_3919acca___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyResultContent_vue_vue_type_template_id_3919acca___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyResultContent.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/content/SurveyVoteContent.vue":
/*!***************************************************************************!*\
  !*** ./client/src/engage_survey/components/content/SurveyVoteContent.vue ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyVoteContent_vue_vue_type_template_id_a88b0b24___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyVoteContent.vue?vue&type=template&id=a88b0b24& */ \"./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=template&id=a88b0b24&\");\n/* harmony import */ var _SurveyVoteContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyVoteContent.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyVoteContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyVoteContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyVoteContent_vue_vue_type_template_id_a88b0b24___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyVoteContent_vue_vue_type_template_id_a88b0b24___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyVoteContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveyVoteContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/content/SurveyVoteContent.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyVoteContent.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyVoteContent.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteContent.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyVoteContent.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=template&id=a88b0b24&":
/*!**********************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=template&id=a88b0b24& ***!
  \**********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_template_id_a88b0b24___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteContent.vue?vue&type=template&id=a88b0b24& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=template&id=a88b0b24&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_template_id_a88b0b24___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteContent_vue_vue_type_template_id_a88b0b24___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyVoteContent.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/content/SurveyVoteTitle.vue":
/*!*************************************************************************!*\
  !*** ./client/src/engage_survey/components/content/SurveyVoteTitle.vue ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyVoteTitle_vue_vue_type_template_id_6565b40d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyVoteTitle.vue?vue&type=template&id=6565b40d& */ \"./client/src/engage_survey/components/content/SurveyVoteTitle.vue?vue&type=template&id=6565b40d&\");\n/* harmony import */ var _SurveyVoteTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyVoteTitle.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/content/SurveyVoteTitle.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyVoteTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyVoteTitle_vue_vue_type_template_id_6565b40d___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyVoteTitle_vue_vue_type_template_id_6565b40d___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/content/SurveyVoteTitle.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyVoteTitle.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/content/SurveyVoteTitle.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/content/SurveyVoteTitle.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteTitle.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyVoteTitle.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyVoteTitle.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/content/SurveyVoteTitle.vue?vue&type=template&id=6565b40d&":
/*!********************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/content/SurveyVoteTitle.vue?vue&type=template&id=6565b40d& ***!
  \********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteTitle_vue_vue_type_template_id_6565b40d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteTitle.vue?vue&type=template&id=6565b40d& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyVoteTitle.vue?vue&type=template&id=6565b40d&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteTitle_vue_vue_type_template_id_6565b40d___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteTitle_vue_vue_type_template_id_6565b40d___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyVoteTitle.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/form/SurveyForm.vue":
/*!*****************************************************************!*\
  !*** ./client/src/engage_survey/components/form/SurveyForm.vue ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyForm_vue_vue_type_template_id_68406178___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyForm.vue?vue&type=template&id=68406178& */ \"./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=template&id=68406178&\");\n/* harmony import */ var _SurveyForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyForm.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyForm_vue_vue_type_template_id_68406178___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyForm_vue_vue_type_template_id_68406178___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveyForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/form/SurveyForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/form/SurveyForm.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/form/SurveyForm.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=script&lang=js&":
/*!******************************************************************************************!*\
  !*** ./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyForm.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/form/SurveyForm.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=template&id=68406178&":
/*!************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=template&id=68406178& ***!
  \************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_template_id_68406178___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyForm.vue?vue&type=template&id=68406178& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=template&id=68406178&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_template_id_68406178___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyForm_vue_vue_type_template_id_68406178___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/form/SurveyForm.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/info/Author.vue":
/*!*************************************************************!*\
  !*** ./client/src/engage_survey/components/info/Author.vue ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Author_vue_vue_type_template_id_3aabbc1b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Author.vue?vue&type=template&id=3aabbc1b& */ \"./client/src/engage_survey/components/info/Author.vue?vue&type=template&id=3aabbc1b&\");\n/* harmony import */ var _Author_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Author.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/info/Author.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _Author_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _Author_vue_vue_type_template_id_3aabbc1b___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _Author_vue_vue_type_template_id_3aabbc1b___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/info/Author.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/info/Author.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/info/Author.vue?vue&type=script&lang=js&":
/*!**************************************************************************************!*\
  !*** ./client/src/engage_survey/components/info/Author.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Author_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Author.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/info/Author.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Author_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/info/Author.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/info/Author.vue?vue&type=template&id=3aabbc1b&":
/*!********************************************************************************************!*\
  !*** ./client/src/engage_survey/components/info/Author.vue?vue&type=template&id=3aabbc1b& ***!
  \********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Author_vue_vue_type_template_id_3aabbc1b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Author.vue?vue&type=template&id=3aabbc1b& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/info/Author.vue?vue&type=template&id=3aabbc1b&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Author_vue_vue_type_template_id_3aabbc1b___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_Author_vue_vue_type_template_id_3aabbc1b___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/info/Author.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/shape/SurveyBadge.vue":
/*!*******************************************************************!*\
  !*** ./client/src/engage_survey/components/shape/SurveyBadge.vue ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyBadge_vue_vue_type_template_id_a253c7ec___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyBadge.vue?vue&type=template&id=a253c7ec& */ \"./client/src/engage_survey/components/shape/SurveyBadge.vue?vue&type=template&id=a253c7ec&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyBadge_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/engage_survey/components/shape/SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\nvar script = {}\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_1__[\"default\"])(\n  script,\n  _SurveyBadge_vue_vue_type_template_id_a253c7ec___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyBadge_vue_vue_type_template_id_a253c7ec___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyBadge_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_2__[\"default\"] === 'function') Object(_SurveyBadge_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":false,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/shape/SurveyBadge.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/shape/SurveyBadge.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/shape/SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!******************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/shape/SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \******************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBadge_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/shape/SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBadge_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/shape/SurveyBadge.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/shape/SurveyBadge.vue?vue&type=template&id=a253c7ec&":
/*!**************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/shape/SurveyBadge.vue?vue&type=template&id=a253c7ec& ***!
  \**************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBadge_vue_vue_type_template_id_a253c7ec___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyBadge.vue?vue&type=template&id=a253c7ec& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/shape/SurveyBadge.vue?vue&type=template&id=a253c7ec&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBadge_vue_vue_type_template_id_a253c7ec___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBadge_vue_vue_type_template_id_a253c7ec___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/shape/SurveyBadge.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue":
/*!*******************************************************************************!*\
  !*** ./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyBaseSidePanel_vue_vue_type_template_id_90fd8022___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyBaseSidePanel.vue?vue&type=template&id=90fd8022& */ \"./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=template&id=90fd8022&\");\n/* harmony import */ var _SurveyBaseSidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyBaseSidePanel.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyBaseSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveyBaseSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyBaseSidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyBaseSidePanel_vue_vue_type_template_id_90fd8022___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyBaseSidePanel_vue_vue_type_template_id_90fd8022___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyBaseSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveyBaseSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!******************************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \******************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBaseSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyBaseSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBaseSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBaseSidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyBaseSidePanel.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBaseSidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=template&id=90fd8022&":
/*!**************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=template&id=90fd8022& ***!
  \**************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBaseSidePanel_vue_vue_type_template_id_90fd8022___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyBaseSidePanel.vue?vue&type=template&id=90fd8022& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=template&id=90fd8022&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBaseSidePanel_vue_vue_type_template_id_90fd8022___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyBaseSidePanel_vue_vue_type_template_id_90fd8022___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue":
/*!***************************************************************************!*\
  !*** ./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveySidePanel_vue_vue_type_template_id_47f210c0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveySidePanel.vue?vue&type=template&id=47f210c0& */ \"./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=template&id=47f210c0&\");\n/* harmony import */ var _SurveySidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveySidePanel.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveySidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveySidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveySidePanel_vue_vue_type_template_id_47f210c0___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveySidePanel_vue_vue_type_template_id_47f210c0___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveySidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveySidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/components/sidepanel/SurveySidePanel.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveySidePanel.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?");

/***/ }),

/***/ "./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=template&id=47f210c0&":
/*!**********************************************************************************************************!*\
  !*** ./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=template&id=47f210c0& ***!
  \**********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_template_id_47f210c0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveySidePanel.vue?vue&type=template&id=47f210c0& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=template&id=47f210c0&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_template_id_47f210c0___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveySidePanel_vue_vue_type_template_id_47f210c0___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?");

/***/ }),

/***/ "./client/src/engage_survey/js sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!************************************************************************************!*\
  !*** ./client/src/engage_survey/js sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\".\": \"./client/src/engage_survey/js/index.js\",\n\t\"./\": \"./client/src/engage_survey/js/index.js\",\n\t\"./index\": \"./client/src/engage_survey/js/index.js\",\n\t\"./index.js\": \"./client/src/engage_survey/js/index.js\",\n\t\"./mixins/surveypage_mixin\": \"./client/src/engage_survey/js/mixins/surveypage_mixin.js\",\n\t\"./mixins/surveypage_mixin.js\": \"./client/src/engage_survey/js/mixins/surveypage_mixin.js\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/src/engage_survey/js sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/src/engage_survey/js_sync_^(?:(?");

/***/ }),

/***/ "./client/src/engage_survey/js/index.js":
/*!**********************************************!*\
  !*** ./client/src/engage_survey/js/index.js ***!
  \**********************************************/
/*! exports provided: surveyPageMixin */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_mixins_surveypage_mixin__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/mixins/surveypage_mixin */ \"engage_survey/mixins/surveypage_mixin\");\n/* harmony import */ var engage_survey_mixins_surveypage_mixin__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_mixins_surveypage_mixin__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (default from non-harmony) */ __webpack_require__.d(__webpack_exports__, \"surveyPageMixin\", function() { return engage_survey_mixins_surveypage_mixin__WEBPACK_IMPORTED_MODULE_0___default.a; });\n/**\n * This file is part of Totara Enterprise Extensions.\n *\n * Copyright (C) 2020 onwards Totara Learning Solutions LTD\n *\n * Totara Enterprise Extensions is provided only to Totara\n * Learning Solutions LTD's customers and partners, pursuant to\n * the terms and conditions of a separate agreement with Totara\n * Learning Solutions LTD or its affiliate.\n *\n * If you do not have an agreement with Totara Learning Solutions\n * LTD, you may not access, use, modify, or distribute this software.\n * Please contact [licensing@totaralearning.com] for more information.\n *\n * @author Qingyang Liu <Qingyang.liu@totaralearning.com>\n * @module engage_survey\n */\n\n\n\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/js/index.js?");

/***/ }),

/***/ "./client/src/engage_survey/js/mixins/surveypage_mixin.js":
/*!****************************************************************!*\
  !*** ./client/src/engage_survey/js/mixins/surveypage_mixin.js ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/graphql/get_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql\");\n/* harmony import */ var totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/graphql/update_bookmark */ \"./server/totara/engage/webapi/ajax/update_bookmark.graphql\");\n/**\n * This file is part of Totara Enterprise Extensions.\n *\n * Copyright (C) 2020 onwards Totara Learning Solutions LTD\n *\n * Totara Enterprise Extensions is provided only to Totara\n * Learning Solutions LTD's customers and partners, pursuant to\n * the terms and conditions of a separate agreement with Totara\n * Learning Solutions LTD or its affiliate.\n *\n * If you do not have an agreement with Totara Learning Solutions\n * LTD, you may not access, use, modify, or distribute this software.\n * Please contact [licensing@totaralearning.com] for more information.\n *\n * @author Qingyang Liu <Qingyang.liu@totaralearning.com>\n * @module engage_survey\n */\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  props: {\n    resourceId: {\n      type: Number,\n      required: true,\n    },\n  },\n\n  apollo: {\n    survey: {\n      query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_0__[\"default\"],\n      variables() {\n        return {\n          resourceid: this.resourceId,\n        };\n      },\n      result({ data: { survey } }) {\n        this.bookmarked = survey.bookmarked;\n      },\n    },\n  },\n\n  data() {\n    return {\n      survey: {},\n      bookmarked: false,\n    };\n  },\n\n  computed: {\n    /**\n     *\n     * @returns {Object}\n     */\n    firstQuestion() {\n      if (!this.survey) {\n        return {};\n      }\n\n      return Array.prototype.slice.call(this.survey.questions).shift();\n    },\n  },\n\n  methods: {\n    updateBookmark() {\n      this.bookmarked = !this.bookmarked;\n      this.$apollo.mutate({\n        mutation: totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n        refetchAll: false,\n        variables: {\n          itemid: this.resourceId,\n          component: 'engage_survey',\n          bookmarked: this.bookmarked,\n        },\n      });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/js/mixins/surveypage_mixin.js?");

/***/ }),

/***/ "./client/src/engage_survey/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!***************************************************************************************!*\
  !*** ./client/src/engage_survey/pages sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \***************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./SurveyEditView\": \"./client/src/engage_survey/pages/SurveyEditView.vue\",\n\t\"./SurveyEditView.vue\": \"./client/src/engage_survey/pages/SurveyEditView.vue\",\n\t\"./SurveyView\": \"./client/src/engage_survey/pages/SurveyView.vue\",\n\t\"./SurveyView.vue\": \"./client/src/engage_survey/pages/SurveyView.vue\",\n\t\"./SurveyVoteView\": \"./client/src/engage_survey/pages/SurveyVoteView.vue\",\n\t\"./SurveyVoteView.vue\": \"./client/src/engage_survey/pages/SurveyVoteView.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/src/engage_survey/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/src/engage_survey/pages_sync_^(?:(?");

/***/ }),

/***/ "./client/src/engage_survey/pages/SurveyEditView.vue":
/*!***********************************************************!*\
  !*** ./client/src/engage_survey/pages/SurveyEditView.vue ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyEditView_vue_vue_type_template_id_5c93f5fc___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyEditView.vue?vue&type=template&id=5c93f5fc& */ \"./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=template&id=5c93f5fc&\");\n/* harmony import */ var _SurveyEditView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyEditView.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SurveyEditView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyEditView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyEditView_vue_vue_type_template_id_5c93f5fc___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyEditView_vue_vue_type_template_id_5c93f5fc___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SurveyEditView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SurveyEditView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/pages/SurveyEditView.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyEditView.vue?");

/***/ }),

/***/ "./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**********************************************************************************************************!*\
  !*** ./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_lang_strings_loader.js!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyEditView.vue?");

/***/ }),

/***/ "./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=script&lang=js&":
/*!************************************************************************************!*\
  !*** ./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=script&lang=js& ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyEditView.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyEditView.vue?");

/***/ }),

/***/ "./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=template&id=5c93f5fc&":
/*!******************************************************************************************!*\
  !*** ./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=template&id=5c93f5fc& ***!
  \******************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_template_id_5c93f5fc___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyEditView.vue?vue&type=template&id=5c93f5fc& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=template&id=5c93f5fc&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_template_id_5c93f5fc___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyEditView_vue_vue_type_template_id_5c93f5fc___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyEditView.vue?");

/***/ }),

/***/ "./client/src/engage_survey/pages/SurveyView.vue":
/*!*******************************************************!*\
  !*** ./client/src/engage_survey/pages/SurveyView.vue ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyView_vue_vue_type_template_id_3bf2bf5c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyView.vue?vue&type=template&id=3bf2bf5c& */ \"./client/src/engage_survey/pages/SurveyView.vue?vue&type=template&id=3bf2bf5c&\");\n/* harmony import */ var _SurveyView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyView.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/pages/SurveyView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyView_vue_vue_type_template_id_3bf2bf5c___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyView_vue_vue_type_template_id_3bf2bf5c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/pages/SurveyView.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyView.vue?");

/***/ }),

/***/ "./client/src/engage_survey/pages/SurveyView.vue?vue&type=script&lang=js&":
/*!********************************************************************************!*\
  !*** ./client/src/engage_survey/pages/SurveyView.vue?vue&type=script&lang=js& ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyView.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyView.vue?");

/***/ }),

/***/ "./client/src/engage_survey/pages/SurveyView.vue?vue&type=template&id=3bf2bf5c&":
/*!**************************************************************************************!*\
  !*** ./client/src/engage_survey/pages/SurveyView.vue?vue&type=template&id=3bf2bf5c& ***!
  \**************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyView_vue_vue_type_template_id_3bf2bf5c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyView.vue?vue&type=template&id=3bf2bf5c& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyView.vue?vue&type=template&id=3bf2bf5c&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyView_vue_vue_type_template_id_3bf2bf5c___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyView_vue_vue_type_template_id_3bf2bf5c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyView.vue?");

/***/ }),

/***/ "./client/src/engage_survey/pages/SurveyVoteView.vue":
/*!***********************************************************!*\
  !*** ./client/src/engage_survey/pages/SurveyVoteView.vue ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SurveyVoteView_vue_vue_type_template_id_1458fadc___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SurveyVoteView.vue?vue&type=template&id=1458fadc& */ \"./client/src/engage_survey/pages/SurveyVoteView.vue?vue&type=template&id=1458fadc&\");\n/* harmony import */ var _SurveyVoteView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SurveyVoteView.vue?vue&type=script&lang=js& */ \"./client/src/engage_survey/pages/SurveyVoteView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SurveyVoteView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SurveyVoteView_vue_vue_type_template_id_1458fadc___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SurveyVoteView_vue_vue_type_template_id_1458fadc___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/engage_survey/pages/SurveyVoteView.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyVoteView.vue?");

/***/ }),

/***/ "./client/src/engage_survey/pages/SurveyVoteView.vue?vue&type=script&lang=js&":
/*!************************************************************************************!*\
  !*** ./client/src/engage_survey/pages/SurveyVoteView.vue?vue&type=script&lang=js& ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteView.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyVoteView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyVoteView.vue?");

/***/ }),

/***/ "./client/src/engage_survey/pages/SurveyVoteView.vue?vue&type=template&id=1458fadc&":
/*!******************************************************************************************!*\
  !*** ./client/src/engage_survey/pages/SurveyVoteView.vue?vue&type=template&id=1458fadc& ***!
  \******************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteView_vue_vue_type_template_id_1458fadc___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../tooling/webpack/tui_vue_loader.js!../../../../node_modules/vue-loader/lib??vue-loader-options!./SurveyVoteView.vue?vue&type=template&id=1458fadc& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyVoteView.vue?vue&type=template&id=1458fadc&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteView_vue_vue_type_template_id_1458fadc___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_SurveyVoteView_vue_vue_type_template_id_1458fadc___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyVoteView.vue?");

/***/ }),

/***/ "./client/src/engage_survey/tui.json":
/*!*******************************************!*\
  !*** ./client/src/engage_survey/tui.json ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"engage_survey\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"engage_survey\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"engage_survey\")\ntui._bundle.addModulesFromContext(\"engage_survey\", __webpack_require__(\"./client/src/engage_survey/js sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\ntui._bundle.addModulesFromContext(\"engage_survey/components\", __webpack_require__(\"./client/src/engage_survey/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\ntui._bundle.addModulesFromContext(\"engage_survey/pages\", __webpack_require__(\"./client/src/engage_survey/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/src/engage_survey/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"moodle\": [\n    \"back\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/button/SurveyBackButton.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*****************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*****************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"survey\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCard.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"votenow\",\n    \"editsurvey\",\n    \"noresult\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCardBody.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"votemessage\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyResultBody.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"percentage\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*****************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"close\",\n    \"participant\",\n    \"participants\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyResultContent.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"vote\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyVoteContent.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*****************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*****************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"moodle\": [\n    \"next\"\n  ],\n\n  \"engage_survey\": [\n    \"formtitle\",\n    \"formtypetitle\",\n    \"optionstitle\",\n    \"optionsingle\",\n    \"optionmultiple\",\n    \"option\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/form/SurveyForm.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/shape/SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*******************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/shape/SurveyBadge.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*******************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"survey\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/shape/SurveyBadge.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*******************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_engage\": [\n    \"overview\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"deletewarningmsg\",\n    \"likesurvey\",\n    \"removelikesurvey\",\n    \"deletesurvey\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***********************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_survey\": [\n    \"save\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyEditView.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/CreateSurvey.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/CreateSurvey.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/components/form/SurveyForm */ \"engage_survey/components/form/SurveyForm\");\n/* harmony import */ var engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/components/form/AccessForm */ \"totara_engage/components/form/AccessForm\");\n/* harmony import */ var totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_survey_graphql_create_survey__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_survey/graphql/create_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/create_survey.graphql\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/mixins/container_mixin */ \"totara_engage/mixins/container_mixin\");\n/* harmony import */ var totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n// Mixins\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SurveyForm: (engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_0___default()),\n    AccessForm: (totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1___default()),\n  },\n\n  mixins: [totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4___default.a],\n\n  data() {\n    return {\n      stage: 0,\n      maxStage: 1,\n      survey: {\n        question: '',\n        type: '',\n        options: [],\n      },\n      submitting: false,\n    };\n  },\n\n  computed: {\n    privateDisabled() {\n      return this.containerValues.access\n        ? !totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"AccessManager\"].isPrivate(this.containerValues.access)\n        : false;\n    },\n    restrictedDisabled() {\n      return this.containerValues.access\n        ? totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"AccessManager\"].isPublic(this.containerValues.access)\n        : false;\n    },\n  },\n\n  methods: {\n    /**\n     * @param {String}          question\n     * @param {Number|String}   type\n     * @param {Array}           options\n     */\n    next({ question, type, options }) {\n      if (this.stage < this.maxStage) {\n        this.stage += 1;\n      }\n\n      this.survey.question = question;\n      this.survey.type = type;\n      this.survey.options = options;\n\n      this.$emit('hide-tabs', true);\n    },\n\n    back() {\n      if (this.stage > 0) {\n        this.stage -= 1;\n      }\n\n      this.$emit('hide-tabs', false);\n    },\n\n    /**\n     * @param {String} access\n     * @param {Array} topics\n     * @param {Array} shares\n     */\n    done({ access, topics, shares }) {\n      if (this.submitting) {\n        return;\n      }\n      this.submitting = true;\n\n      this.$apollo\n        .mutate({\n          mutation: engage_survey_graphql_create_survey__WEBPACK_IMPORTED_MODULE_2__[\"default\"],\n          variables: {\n            // TODO: replace timeexpired with the time selected from the date component\n            timeexpired: null,\n            questions: [\n              {\n                value: this.survey.question,\n                answertype: this.survey.type,\n                options: this.survey.options.map(option => option.text),\n              },\n            ],\n            access: access,\n            topics: topics.map(topic => topic.id),\n            shares: shares,\n          },\n          update: (cache, { data: { survey } }) => {\n            this.$emit('done', { resourceId: survey.resource.id });\n          },\n        })\n        .then(() => this.$emit('cancel'))\n        .finally(() => (this.submitting = false));\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/CreateSurvey.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/box/RadioBox.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/box/RadioBox.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/RadioGroup */ \"tui/components/form/RadioGroup\");\n/* harmony import */ var tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/Radio */ \"tui/components/form/Radio\");\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/uniform */ \"tui/components/uniform\");\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform__WEBPACK_IMPORTED_MODULE_2__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\nconst has = Object.prototype.hasOwnProperty;\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    RadioGroup: (tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_0___default()),\n    Radio: (tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1___default()),\n    FormRowFieldset: tui_components_uniform__WEBPACK_IMPORTED_MODULE_2__[\"FormRowFieldset\"],\n  },\n\n  model: {\n    prop: 'value',\n    event: 'update-value',\n  },\n\n  props: {\n    value: {\n      // We are using this property for v-model.\n      required: false,\n      type: [Number, String],\n    },\n\n    options: {\n      required: true,\n      type: [Array, Object],\n      validator(prop) {\n        for (let i in prop) {\n          if (!has.call(prop, i)) {\n            continue;\n          }\n\n          let option = prop[i];\n          if (!has.call(option, 'id') || !has.call(option, 'value')) {\n            return false;\n          }\n        }\n\n        return true;\n      },\n    },\n  },\n\n  data() {\n    return {\n      option: null,\n    };\n  },\n\n  watch: {\n    option(value) {\n      this.$emit('update-value', value);\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/box/RadioBox.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/box/SquareBox.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/box/SquareBox.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_Checkbox__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/Checkbox */ \"tui/components/form/Checkbox\");\n/* harmony import */ var tui_components_form_Checkbox__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Checkbox__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/uniform */ \"tui/components/uniform\");\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform__WEBPACK_IMPORTED_MODULE_1__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\nconst has = Object.prototype.hasOwnProperty;\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Checkbox: (tui_components_form_Checkbox__WEBPACK_IMPORTED_MODULE_0___default()),\n    FormRowFieldset: tui_components_uniform__WEBPACK_IMPORTED_MODULE_1__[\"FormRowFieldset\"],\n  },\n\n  model: {\n    prop: 'value',\n    event: 'update-value',\n  },\n\n  props: {\n    value: {\n      // A property that is being used for v-model\n      type: Array,\n      default() {\n        return [];\n      },\n    },\n\n    options: {\n      type: [Array, Object],\n      validator(prop) {\n        for (let i in prop) {\n          if (!has.call(prop, i)) {\n            continue;\n          }\n\n          let item = prop[i];\n          if (!has.call(item, 'id') || !has.call(item, 'value')) {\n            return false;\n          }\n        }\n\n        return true;\n      },\n    },\n  },\n\n  data() {\n    return {\n      picked: [],\n    };\n  },\n\n  methods: {\n    $_handleChange(id, checked) {\n      if (!checked) {\n        this.picked = this.picked.filter(function(item) {\n          return item !== id;\n        });\n      } else if (checked && !this.picked.includes(id)) {\n        // Adding.\n        this.picked.push(id);\n      }\n\n      // We are making sure that the whole button vote will be blocked form clicked.\n      let picked = null;\n\n      if (0 < this.picked.length) {\n        picked = this.picked;\n      }\n\n      this.$emit('update-value', picked);\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/box/SquareBox.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_icons_common_BackArrow__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/icons/common/BackArrow */ \"tui/components/icons/common/BackArrow\");\n/* harmony import */ var tui_components_icons_common_BackArrow__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_common_BackArrow__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    BackArrow: (tui_components_icons_common_BackArrow__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n  props: {\n    owned: {\n      type: Boolean,\n      required: true,\n    },\n  },\n\n  computed: {\n    getUrl() {\n      return this.owned\n        ? this.$url('/totara/engage/your_resources.php')\n        : this.$url('/totara/engage/shared_with_you.php');\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/button/SurveyBackButton.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/card/Card */ \"tui/components/card/Card\");\n/* harmony import */ var tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_survey_components_card_SurveyCardBody__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_survey/components/card/SurveyCardBody */ \"engage_survey/components/card/SurveyCardBody\");\n/* harmony import */ var engage_survey_components_card_SurveyCardBody__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_card_SurveyCardBody__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var engage_survey_components_card_SurveyResultBody__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! engage_survey/components/card/SurveyResultBody */ \"engage_survey/components/card/SurveyResultBody\");\n/* harmony import */ var engage_survey_components_card_SurveyResultBody__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_card_SurveyResultBody__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/components/buttons/BookmarkButton */ \"totara_engage/components/buttons/BookmarkButton\");\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var engage_survey_graphql_vote_result__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_survey/graphql/vote_result */ \"./server/totara/engage/resources/survey/webapi/ajax/vote_result.graphql\");\n/* harmony import */ var totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! totara_engage/graphql/update_bookmark */ \"./server/totara/engage/webapi/ajax/update_bookmark.graphql\");\n/* harmony import */ var totara_engage_components_card_Footnotes__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_engage/components/card/Footnotes */ \"totara_engage/components/card/Footnotes\");\n/* harmony import */ var totara_engage_components_card_Footnotes__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_card_Footnotes__WEBPACK_IMPORTED_MODULE_7__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n// GraphQL\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    CoreCard: (tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0___default()),\n    SurveyCardBody: (engage_survey_components_card_SurveyCardBody__WEBPACK_IMPORTED_MODULE_2___default()),\n    SurveyResultBody: (engage_survey_components_card_SurveyResultBody__WEBPACK_IMPORTED_MODULE_3___default()),\n    BookmarkButton: (totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4___default()),\n    Footnotes: (totara_engage_components_card_Footnotes__WEBPACK_IMPORTED_MODULE_7___default()),\n  },\n\n  mixins: [totara_engage_index__WEBPACK_IMPORTED_MODULE_1__[\"cardMixin\"]],\n\n  data() {\n    let extraData = {},\n      questions = [];\n\n    if (this.extra) {\n      extraData = JSON.parse(this.extra);\n    }\n\n    if (extraData.questions) {\n      questions = Array.prototype.slice.call(extraData.questions);\n    }\n\n    return {\n      show: {\n        result: false,\n        editModal: false,\n      },\n\n      innerBookMarked: this.bookmarked,\n      questions: questions,\n      voted: extraData.voted || false,\n      extraData: JSON.parse(this.extra),\n    };\n  },\n\n  computed: {\n    editAble() {\n      const extra = this.extraData;\n      return extra.editable || false;\n    },\n  },\n\n  methods: {\n    /**\n     * Updating the questions of this cards.\n     */\n    handleVoted() {\n      this.$apollo\n        .query({\n          query: engage_survey_graphql_vote_result__WEBPACK_IMPORTED_MODULE_5__[\"default\"],\n          variables: {\n            resourceid: this.instanceId,\n          },\n        })\n        .then(({ data: { questions } }) => {\n          this.questions = questions;\n          this.voted = true;\n\n          // Showing the result afterward.\n          this.show.result = true;\n        });\n    },\n\n    $_hideModals() {\n      this.show.editModal = false;\n      this.show.result = false;\n    },\n\n    deleted() {\n      this.$_hideModals();\n\n      // Sent to up-stream to remove this very card from very\n      this.emitDeleted();\n    },\n\n    updated() {\n      this.$_hideModals();\n\n      // Sent to up-stream to update this very card.\n      this.emitUpdated();\n    },\n\n    updateBookmark() {\n      this.innerBookMarked = !this.innerBookMarked;\n      this.$apollo.mutate({\n        mutation: totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n        refetchAll: false,\n        refetchQueries: ['totara_engage_contribution_cards'],\n        variables: {\n          itemid: this.instanceId,\n          component: 'engage_survey',\n          bookmarked: this.innerBookMarked,\n        },\n      });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCard.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_engage/components/icons/access/computed/AccessIcon */ \"totara_engage/components/icons/access/computed/AccessIcon\");\n/* harmony import */ var totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/links/ActionLink */ \"tui/components/links/ActionLink\");\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AccessIcon: (totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_0___default()),\n    ActionLink: (tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default()),\n  },\n\n  inheritAttrs: false,\n\n  props: {\n    resourceId: {\n      type: [Number, String],\n      required: true,\n    },\n\n    name: {\n      required: true,\n      type: String,\n      default: '',\n    },\n\n    access: {\n      required: true,\n      type: String,\n    },\n\n    voted: {\n      required: true,\n      type: Boolean,\n    },\n\n    owned: {\n      required: true,\n      type: Boolean,\n    },\n\n    editAble: {\n      required: true,\n      type: Boolean,\n    },\n\n    bookmarked: {\n      type: Boolean,\n      default: false,\n    },\n\n    labelId: {\n      type: String,\n      default: '',\n    },\n  },\n  computed: {\n    showEdit() {\n      return this.owned && this.editAble;\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCardBody.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_Label__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/Label */ \"tui/components/form/Label\");\n/* harmony import */ var tui_components_form_Label__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Label__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/components/icons/access/computed/AccessIcon */ \"totara_engage/components/icons/access/computed/AccessIcon\");\n/* harmony import */ var totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_survey/components/card/result/SurveyQuestionResult */ \"engage_survey/components/card/result/SurveyQuestionResult\");\n/* harmony import */ var engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_2__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AccessIcon: (totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_1___default()),\n    Label: (tui_components_form_Label__WEBPACK_IMPORTED_MODULE_0___default()),\n    SurveyQuestionResult: (engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_2___default()),\n  },\n\n  props: {\n    name: {\n      required: true,\n      type: String,\n      default: '',\n    },\n\n    questions: {\n      type: [Object, Array],\n      required: true,\n    },\n\n    access: {\n      required: true,\n      type: String,\n    },\n\n    labelId: {\n      type: String,\n      default: '',\n    },\n\n    resourceId: {\n      required: true,\n      type: String,\n    },\n  },\n\n  computed: {\n    voteMessage() {\n      const questions = Array.prototype.slice.call(this.questions).shift();\n\n      return this.$str('votemessage', 'engage_survey', {\n        options: questions.options.length >= 3 ? 3 : 2,\n        questions: questions.options.length,\n      });\n    },\n  },\n  methods: {\n    navigateTo() {\n      window.location.href = this.$url(\n        '/totara/engage/resources/survey/survey_vote.php',\n        { id: this.resourceId }\n      );\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyResultBody.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/progress/Progress */ \"tui/components/progress/Progress\");\n/* harmony import */ var tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_1__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Progress: (tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n\n  props: {\n    questionId: {\n      type: [Number, String],\n      required: true,\n    },\n\n    options: {\n      type: [Array, Object],\n      required: true,\n    },\n\n    /**\n     * Total number of user has voted the question.\n     */\n    totalVotes: {\n      type: [Number, String],\n      required: true,\n    },\n\n    displayOptions: {\n      type: [Number, String],\n      default: 3,\n    },\n\n    resultContent: {\n      type: Boolean,\n      default: false,\n    },\n\n    answerType: {\n      type: [Number, String],\n      required: true,\n    },\n  },\n\n  computed: {\n    calulatedOptions() {\n      if (this.resultContent) return this.options;\n      return Array.prototype.slice.call(this.options, 0, this.displayOptions);\n    },\n\n    highestVote() {\n      if (this.isMultiChoice) {\n        const sortArray = Array.prototype.slice\n          .call(this.options)\n          .sort((o1, o2) => o2.votes - o1.votes);\n        return sortArray[0].votes;\n      }\n      return 0;\n    },\n\n    /**\n     *\n     * @returns {Boolean}\n     */\n    isSingleChoice() {\n      return totara_engage_index__WEBPACK_IMPORTED_MODULE_1__[\"AnswerType\"].isSingleChoice(this.answerType);\n    },\n\n    /**\n     * @returns {Boolean}\n     */\n    isMultiChoice() {\n      return totara_engage_index__WEBPACK_IMPORTED_MODULE_1__[\"AnswerType\"].isMultiChoice(this.answerType);\n    },\n  },\n  methods: {\n    /**\n     *\n     * @param {Number} votes\n     * @returns {number}\n     */\n    percentage(votes) {\n      return Math.round((votes / this.totalVotes) * 100);\n    },\n    /**\n     *\n     * @param {Number} votes\n     * @returns {number}\n     */\n    $_getVotes(votes) {\n      if (this.isMultiChoice) {\n        return (votes / this.highestVote) * this.totalVotes;\n      }\n      return votes;\n    },\n    /**\n     *\n     * @param {Number} votes\n     * @returns {number}\n     */\n    getValues(votes) {\n      if (this.isSingleChoice) {\n        return votes;\n      }\n\n      return this.highestVote === 0 && this.highestVote === votes\n        ? this.totalVotes\n        : this.$_getVotes(votes);\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/components/card/result/SurveyQuestionResult */ \"engage_survey/components/card/result/SurveyQuestionResult\");\n/* harmony import */ var engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_survey/graphql/get_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n// GraphQL queries\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SurveyQuestionResult: (engage_survey_components_card_result_SurveyQuestionResult__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n\n  props: {\n    resourceId: {\n      required: true,\n      type: [Number, String],\n    },\n  },\n\n  apollo: {\n    survey: {\n      query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_2__[\"default\"],\n      variables() {\n        return {\n          resourceid: this.resourceId,\n        };\n      },\n    },\n  },\n\n  data() {\n    return {\n      survey: {},\n    };\n  },\n\n  computed: {\n    showParticipants() {\n      const questions = Array.prototype.slice.call(this.questions),\n        { participants } = questions.shift();\n\n      if (participants === 1) {\n        return this.$str('participant', 'engage_survey');\n      }\n\n      return this.$str('participants', 'engage_survey');\n    },\n\n    showNumberOfParticipant() {\n      const { participants } = Array.prototype.slice\n        .call(this.questions)\n        .shift();\n\n      return participants;\n    },\n\n    /**\n     * @returns {Boolean}\n     */\n    isMultiChoice() {\n      return totara_engage_index__WEBPACK_IMPORTED_MODULE_1__[\"AnswerType\"].isMultiChoice(this.questions[0].answertype);\n    },\n\n    questions() {\n      const { questionresults } = this.survey;\n      return Array.prototype.slice.call(questionresults);\n    },\n  },\n\n  methods: {},\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyResultContent.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_components_box_RadioBox__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/components/box/RadioBox */ \"engage_survey/components/box/RadioBox\");\n/* harmony import */ var engage_survey_components_box_RadioBox__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_box_RadioBox__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var engage_survey_components_box_SquareBox__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! engage_survey/components/box/SquareBox */ \"engage_survey/components/box/SquareBox\");\n/* harmony import */ var engage_survey_components_box_SquareBox__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_box_SquareBox__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/buttons/Button */ \"tui/components/buttons/Button\");\n/* harmony import */ var tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/form/Form */ \"tui/components/form/Form\");\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Form__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var engage_survey_graphql_create_answer__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_survey/graphql/create_answer */ \"./server/totara/engage/resources/survey/webapi/ajax/create_answer.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n// GraphQL queries\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SquareBox: (engage_survey_components_box_SquareBox__WEBPACK_IMPORTED_MODULE_1___default()),\n    RadioBox: (engage_survey_components_box_RadioBox__WEBPACK_IMPORTED_MODULE_0___default()),\n    Button: (tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_3___default()),\n    Form: (tui_components_form_Form__WEBPACK_IMPORTED_MODULE_4___default()),\n  },\n\n  props: {\n    options: {\n      type: [Array, Object],\n      required: true,\n    },\n\n    answerType: {\n      type: [Number, String],\n      required: true,\n    },\n\n    resourceId: {\n      required: true,\n      type: [Number, String],\n    },\n\n    questionId: {\n      required: true,\n      type: [Number, String],\n    },\n\n    disabled: {\n      type: Boolean,\n      default: false,\n    },\n  },\n\n  data() {\n    return {\n      questions: [],\n      answer: null,\n    };\n  },\n\n  computed: {\n    /**\n     * @returns {Boolean}\n     */\n    isMultiChoice() {\n      return totara_engage_index__WEBPACK_IMPORTED_MODULE_2__[\"AnswerType\"].isMultiChoice(this.answerType);\n    },\n\n    /**\n     *\n     * @returns {Boolean}\n     */\n    isSingleChoice() {\n      return totara_engage_index__WEBPACK_IMPORTED_MODULE_2__[\"AnswerType\"].isSingleChoice(this.answerType);\n    },\n  },\n\n  methods: {\n    vote() {\n      if (null == this.answer) {\n        return;\n      }\n\n      let answers;\n\n      if (!Array.isArray(this.answer)) {\n        answers = [this.answer];\n      } else {\n        answers = this.answer;\n      }\n\n      this.$apollo.mutate({\n        mutation: engage_survey_graphql_create_answer__WEBPACK_IMPORTED_MODULE_5__[\"default\"],\n        variables: {\n          resourceid: this.resourceId,\n          options: answers,\n          questionid: this.questionId,\n        },\n      });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyVoteContent.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyVoteTitle.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/content/SurveyVoteTitle.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_engage/components/buttons/BookmarkButton */ \"totara_engage/components/buttons/BookmarkButton\");\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    BookmarkButton: (totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n\n  props: {\n    title: {\n      type: String,\n      required: true,\n    },\n\n    bookmarked: {\n      type: Boolean,\n      default: false,\n    },\n\n    owned: {\n      type: Boolean,\n      required: true,\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyVoteTitle.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/uniform */ \"tui/components/uniform\");\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_reform_FieldContextProvider__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/reform/FieldContextProvider */ \"tui/components/reform/FieldContextProvider\");\n/* harmony import */ var tui_components_reform_FieldContextProvider__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_reform_FieldContextProvider__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/buttons/ButtonGroup */ \"tui/components/buttons/ButtonGroup\");\n/* harmony import */ var tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/buttons/Cancel */ \"tui/components/buttons/Cancel\");\n/* harmony import */ var tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_components_buttons_LoadingButton__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/components/buttons/LoadingButton */ \"totara_engage/components/buttons/LoadingButton\");\n/* harmony import */ var totara_engage_components_buttons_LoadingButton__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_LoadingButton__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/components/form/Radio */ \"tui/components/form/Radio\");\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/components/form/Repeater */ \"tui/components/form/Repeater\");\n/* harmony import */ var tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_7__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Uniform: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"Uniform\"],\n    FieldArray: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"FieldArray\"],\n    FormRowFieldset: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"FormRowFieldset\"],\n    FormText: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"FormText\"],\n    FieldContextProvider: (tui_components_reform_FieldContextProvider__WEBPACK_IMPORTED_MODULE_1___default()),\n    FormRadioGroup: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"FormRadioGroup\"],\n    ButtonGroup: (tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_2___default()),\n    LoadingButton: (totara_engage_components_buttons_LoadingButton__WEBPACK_IMPORTED_MODULE_4___default()),\n    CancelButton: (tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_3___default()),\n    Radio: (tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_5___default()),\n    Repeater: (tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_6___default()),\n  },\n\n  props: {\n    survey: {\n      type: Object,\n      default() {\n        return {\n          question: '',\n          type: '',\n          options: [],\n          questionId: null,\n        };\n      },\n\n      validator: prop =>\n        'question' in prop && 'type' in prop && 'options' in prop,\n    },\n\n    submitting: {\n      type: Boolean,\n      default: false,\n    },\n\n    buttonContent: {\n      type: String,\n      default() {\n        return this.$str('next', 'moodle');\n      },\n    },\n\n    showButtonRight: {\n      type: Boolean,\n      default: true,\n    },\n    showButtonLeft: {\n      type: Boolean,\n      default: false,\n    },\n  },\n\n  data() {\n    const minOptions = 2;\n\n    const options = Array.isArray(this.survey.options)\n      ? this.survey.options\n      : [];\n    while (options.length < minOptions) {\n      options.push(this.newOption());\n    }\n\n    return {\n      multiChoice: String(totara_engage_index__WEBPACK_IMPORTED_MODULE_7__[\"AnswerType\"].MULTI_CHOICE),\n      singleChoice: String(totara_engage_index__WEBPACK_IMPORTED_MODULE_7__[\"AnswerType\"].SINGLE_CHOICE),\n      minOptions,\n      maxOptions: 10,\n      disabled: true,\n\n      initialValues: {\n        question: this.survey.question,\n        options,\n        optionType: this.survey.type || String(totara_engage_index__WEBPACK_IMPORTED_MODULE_7__[\"AnswerType\"].MULTI_CHOICE),\n      },\n    };\n  },\n  computed: {\n    buttonText() {\n      return this.buttonContent;\n    },\n  },\n  methods: {\n    /**\n     * @returns {object}\n     */\n    newOption() {\n      return { text: '', id: 0 };\n    },\n\n    submit(values) {\n      const params = {\n        options: values.options,\n        question: values.question,\n        type: values.optionType,\n\n        // If it is for creation, then this should be null.\n        questionId: this.survey.questionId,\n      };\n      this.$emit('next', params);\n    },\n\n    change(values) {\n      const { question, options } = values;\n      this.disabled = true;\n      if (question.length > 0) {\n        const result = Array.prototype.slice\n          .call(options, 0, 2)\n          .filter(option => option.text !== '');\n\n        if (result.length === 2) {\n          this.disabled = false;\n        }\n      }\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/form/SurveyForm.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/info/Author.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/info/Author.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/avatar/Avatar */ \"tui/components/avatar/Avatar\");\n/* harmony import */ var tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Avatar: (tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n\n  props: {\n    userId: {\n      required: true,\n      type: [Number, String],\n    },\n\n    fullname: {\n      required: true,\n      type: String,\n    },\n\n    profileImageUrl: {\n      required: true,\n      type: String,\n    },\n\n    profileImageAlt: {\n      required: true,\n      type: String,\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/info/Author.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_tabs_Tabs__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/tabs/Tabs */ \"tui/components/tabs/Tabs\");\n/* harmony import */ var tui_components_tabs_Tabs__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_tabs_Tabs__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_tabs_Tab__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/tabs/Tab */ \"tui/components/tabs/Tab\");\n/* harmony import */ var tui_components_tabs_Tab__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_tabs_Tab__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/profile/MiniProfileCard */ \"tui/components/profile/MiniProfileCard\");\n/* harmony import */ var tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var totara_engage_components_dropdown_EngageDropDown__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/components/dropdown/EngageDropDown */ \"totara_engage/components/dropdown/EngageDropDown\");\n/* harmony import */ var totara_engage_components_dropdown_EngageDropDown__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_dropdown_EngageDropDown__WEBPACK_IMPORTED_MODULE_3__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Tabs: (tui_components_tabs_Tabs__WEBPACK_IMPORTED_MODULE_0___default()),\n    Tab: (tui_components_tabs_Tab__WEBPACK_IMPORTED_MODULE_1___default()),\n    MiniProfileCard: (tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_2___default()),\n    EngageDropDown: (totara_engage_components_dropdown_EngageDropDown__WEBPACK_IMPORTED_MODULE_3___default()),\n  },\n\n  props: {\n    userFullName: {\n      type: String,\n      required: true,\n    },\n\n    userProfileImageAlt: {\n      type: String,\n      default: '',\n    },\n\n    userProfileImageUrl: {\n      type: String,\n      required: true,\n    },\n\n    userId: {\n      type: [String, Number],\n      required: true,\n    },\n\n    userEmail: {\n      type: String,\n      required: true,\n    },\n\n    owned: {\n      type: Boolean,\n      default: false,\n    },\n\n    actions: {\n      type: Array,\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_components_sidepanel_SurveyBaseSidePanel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/components/sidepanel/SurveyBaseSidePanel */ \"engage_survey/components/sidepanel/SurveyBaseSidePanel\");\n/* harmony import */ var engage_survey_components_sidepanel_SurveyBaseSidePanel__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_sidepanel_SurveyBaseSidePanel__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/components/sidepanel/access/AccessSetting */ \"totara_engage/components/sidepanel/access/AccessSetting\");\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! totara_engage/components/sidepanel/access/AccessDisplay */ \"totara_engage/components/sidepanel/access/AccessDisplay\");\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/modal/ModalPresenter */ \"tui/components/modal/ModalPresenter\");\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/components/modal/EngageWarningModal */ \"totara_engage/components/modal/EngageWarningModal\");\n/* harmony import */ var totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! totara_engage/components/sidepanel/media/MediaSetting */ \"totara_engage/components/sidepanel/media/MediaSetting\");\n/* harmony import */ var totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_apollo_client__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/apollo_client */ \"tui/apollo_client\");\n/* harmony import */ var tui_apollo_client__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_apollo_client__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! engage_survey/graphql/get_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql\");\n/* harmony import */ var engage_survey_graphql_delete_survey__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! engage_survey/graphql/delete_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/delete_survey.graphql\");\n/* harmony import */ var engage_survey_graphql_update_survey__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! engage_survey/graphql/update_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/update_survey.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n// GraphQL\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SurveyBaseSidePanel: (engage_survey_components_sidepanel_SurveyBaseSidePanel__WEBPACK_IMPORTED_MODULE_0___default()),\n    AccessDisplay: (totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_2___default()),\n    ModalPresenter: (tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_3___default()),\n    EngageWarningModal: (totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_4___default()),\n    AccessSetting: (totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_1___default()),\n    MediaSetting: (totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_5___default()),\n  },\n\n  props: {\n    resourceId: {\n      type: [Number, String],\n      required: true,\n    },\n  },\n\n  apollo: {\n    survey: {\n      query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n      fetchPolicy: 'network-only',\n      variables() {\n        return {\n          resourceid: this.resourceId,\n        };\n      },\n    },\n  },\n\n  data() {\n    return {\n      survey: {},\n      submitting: false,\n      openModalFromButtonLabel: false,\n      openModalFromAction: false,\n    };\n  },\n\n  computed: {\n    userEmail() {\n      return this.survey.resource.user.email || '';\n    },\n    actions() {\n      return [\n        {\n          label: this.$str('deletesurvey', 'engage_survey'),\n          action: this.$_requestConfirm.bind(this),\n        },\n      ];\n    },\n    sharedByCount() {\n      return this.survey.sharedByCount;\n    },\n\n    likeButtonLabel() {\n      if (this.survey.reacted) {\n        return this.$str(\n          'removelikesurvey',\n          'engage_survey',\n          this.survey.resource.name\n        );\n      }\n\n      return this.$str(\n        'likesurvey',\n        'engage_survey',\n        this.survey.resource.name\n      );\n    },\n  },\n\n  methods: {\n    $_requestConfirm() {\n      this.openModalFromAction = true;\n    },\n\n    handleDelete() {\n      this.$apollo\n        .mutate({\n          mutation: engage_survey_graphql_delete_survey__WEBPACK_IMPORTED_MODULE_8__[\"default\"],\n          variables: {\n            resourceid: this.resourceId,\n          },\n          refetchAll: false,\n        })\n        .then(({ data }) => {\n          if (data.result) {\n            this.openModalFromAction = false;\n            window.location.href = this.$url(\n              '/totara/engage/your_resources.php'\n            );\n          }\n        });\n    },\n\n    /**\n     * Updates Access for this survey\n     *\n     * @param {String} access The access level of the survey\n     * @param {Array} topics Topics that this survey should be shared with\n     * @param {Array} shares An array of group id's that this survey is shared with\n     */\n    updateAccess({ access, topics, shares }) {\n      this.submitting = true;\n      this.$apollo\n        .mutate({\n          mutation: engage_survey_graphql_update_survey__WEBPACK_IMPORTED_MODULE_9__[\"default\"],\n          refetchAll: false,\n          variables: {\n            resourceid: this.resourceId,\n            access: access,\n            topics: topics.map(({ id }) => id),\n            shares: shares,\n          },\n\n          update: (proxy, { data }) => {\n            proxy.writeQuery({\n              query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n              variables: { resourceid: this.resourceId },\n              data,\n            });\n          },\n        })\n        .finally(() => {\n          this.submitting = false;\n        });\n    },\n\n    /**\n     *\n     * @param {Boolean} status\n     */\n    updateLikeStatus(status) {\n      let { survey } = tui_apollo_client__WEBPACK_IMPORTED_MODULE_6___default.a.readQuery({\n        query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n        variables: {\n          resourceid: this.resourceId,\n        },\n      });\n\n      survey = Object.assign({}, survey);\n      survey.reacted = status;\n\n      tui_apollo_client__WEBPACK_IMPORTED_MODULE_6___default.a.writeQuery({\n        query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n        variables: { resourceid: this.resourceId },\n        data: { survey: survey },\n      });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/components/sidepanel/SurveySidePanel */ \"engage_survey/components/sidepanel/SurveySidePanel\");\n/* harmony import */ var engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! engage_survey/components/form/SurveyForm */ \"engage_survey/components/form/SurveyForm\");\n/* harmony import */ var engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/loader/Loader */ \"tui/components/loader/Loader\");\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_layouts_LayoutOneColumnWithSidePanel__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/layouts/LayoutOneColumnWithSidePanel */ \"tui/components/layouts/LayoutOneColumnWithSidePanel\");\n/* harmony import */ var tui_components_layouts_LayoutOneColumnWithSidePanel__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_layouts_LayoutOneColumnWithSidePanel__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var engage_survey_components_button_SurveyBackButton__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! engage_survey/components/button/SurveyBackButton */ \"engage_survey/components/button/SurveyBackButton\");\n/* harmony import */ var engage_survey_components_button_SurveyBackButton__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_button_SurveyBackButton__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var engage_survey_index__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_survey/index */ \"engage_survey/index\");\n/* harmony import */ var engage_survey_index__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(engage_survey_index__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! engage_survey/graphql/get_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql\");\n/* harmony import */ var engage_survey_graphql_update_survey__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! engage_survey/graphql/update_survey */ \"./server/totara/engage/resources/survey/webapi/ajax/update_survey.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n// GraphQL\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SurveySidePanel: (engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0___default()),\n    SurveyForm: (engage_survey_components_form_SurveyForm__WEBPACK_IMPORTED_MODULE_1___default()),\n    Loader: (tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_2___default()),\n    Layout: (tui_components_layouts_LayoutOneColumnWithSidePanel__WEBPACK_IMPORTED_MODULE_3___default()),\n    SurveyBackButton: (engage_survey_components_button_SurveyBackButton__WEBPACK_IMPORTED_MODULE_4___default()),\n  },\n  mixins: [engage_survey_index__WEBPACK_IMPORTED_MODULE_5__[\"surveyPageMixin\"]],\n\n  data() {\n    return {\n      submitting: false,\n    };\n  },\n\n  computed: {\n    surveyInstance() {\n      if (this.$apollo.loading) {\n        return undefined;\n      }\n\n      let { questions } = this.survey;\n      questions = Array.prototype.slice.call(questions);\n\n      const question = questions.shift();\n      let options = [];\n\n      if (question.options && Array.isArray(question.options)) {\n        options = question.options.map(({ id, value }) => {\n          return {\n            id: id,\n            text: value,\n          };\n        });\n      }\n\n      return {\n        questionId: question.id,\n        question: question.value,\n        type: question.answertype,\n        options: options,\n      };\n    },\n  },\n\n  methods: {\n    handleCancel() {\n      window.location.href = this.$url(\n        '/totara/engage/resources/survey/survey_view.php',\n        { id: this.resourceId }\n      );\n    },\n    handleSave({ question, questionId, type, options }) {\n      if (this.submitting) {\n        return;\n      }\n      this.submitting = true;\n\n      this.$apollo\n        .mutate({\n          mutation: engage_survey_graphql_update_survey__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n          refetchAll: false,\n          variables: {\n            resourceid: this.resourceId,\n            questions: [\n              {\n                value: question,\n                answertype: type,\n                options: options.map(({ text }) => text),\n                id: questionId,\n              },\n            ],\n          },\n\n          /**\n           *\n           * @param {DataProxy} proxy\n           * @param {Object}    data\n           */\n          updateQuery: (proxy, data) => {\n            proxy.writeQuery({\n              query: engage_survey_graphql_get_survey__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n              variables: {\n                resourceid: this.resourceId,\n              },\n\n              data: data,\n            });\n          },\n        })\n        .finally(() => {\n          this.submitting = false;\n          window.location.href = this.$url(\n            '/totara/engage/resources/survey/survey_view.php',\n            { id: this.resourceId }\n          );\n        });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyEditView.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyView.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/pages/SurveyView.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/components/sidepanel/SurveySidePanel */ \"engage_survey/components/sidepanel/SurveySidePanel\");\n/* harmony import */ var engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var engage_survey_components_button_SurveyBackButton__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! engage_survey/components/button/SurveyBackButton */ \"engage_survey/components/button/SurveyBackButton\");\n/* harmony import */ var engage_survey_components_button_SurveyBackButton__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_button_SurveyBackButton__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_survey/components/content/SurveyVoteTitle */ \"engage_survey/components/content/SurveyVoteTitle\");\n/* harmony import */ var engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/loader/Loader */ \"tui/components/loader/Loader\");\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_layouts_LayoutOneColumnWithSidePanel__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/layouts/LayoutOneColumnWithSidePanel */ \"tui/components/layouts/LayoutOneColumnWithSidePanel\");\n/* harmony import */ var tui_components_layouts_LayoutOneColumnWithSidePanel__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_layouts_LayoutOneColumnWithSidePanel__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_survey/components/content/SurveyVoteContent */ \"engage_survey/components/content/SurveyVoteContent\");\n/* harmony import */ var engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var engage_survey_index__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! engage_survey/index */ \"engage_survey/index\");\n/* harmony import */ var engage_survey_index__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(engage_survey_index__WEBPACK_IMPORTED_MODULE_6__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SurveySidePanel: (engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0___default()),\n    SurveyBackButton: (engage_survey_components_button_SurveyBackButton__WEBPACK_IMPORTED_MODULE_1___default()),\n    Loader: (tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_3___default()),\n    Layout: (tui_components_layouts_LayoutOneColumnWithSidePanel__WEBPACK_IMPORTED_MODULE_4___default()),\n    SurveyVoteContent: (engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_5___default()),\n    SurveyVoteTitle: (engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_2___default()),\n  },\n  mixins: [engage_survey_index__WEBPACK_IMPORTED_MODULE_6__[\"surveyPageMixin\"]],\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyView.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyVoteView.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/pages/SurveyVoteView.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_survey/components/sidepanel/SurveySidePanel */ \"engage_survey/components/sidepanel/SurveySidePanel\");\n/* harmony import */ var engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var engage_survey_components_button_SurveyBackButton__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! engage_survey/components/button/SurveyBackButton */ \"engage_survey/components/button/SurveyBackButton\");\n/* harmony import */ var engage_survey_components_button_SurveyBackButton__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_button_SurveyBackButton__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_survey/components/content/SurveyVoteTitle */ \"engage_survey/components/content/SurveyVoteTitle\");\n/* harmony import */ var engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! engage_survey/components/content/SurveyVoteContent */ \"engage_survey/components/content/SurveyVoteContent\");\n/* harmony import */ var engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/loader/Loader */ \"tui/components/loader/Loader\");\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var engage_survey_components_content_SurveyResultContent__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_survey/components/content/SurveyResultContent */ \"engage_survey/components/content/SurveyResultContent\");\n/* harmony import */ var engage_survey_components_content_SurveyResultContent__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(engage_survey_components_content_SurveyResultContent__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_components_layouts_LayoutOneColumnWithSidePanel__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/components/layouts/LayoutOneColumnWithSidePanel */ \"tui/components/layouts/LayoutOneColumnWithSidePanel\");\n/* harmony import */ var tui_components_layouts_LayoutOneColumnWithSidePanel__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_components_layouts_LayoutOneColumnWithSidePanel__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var engage_survey_index__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! engage_survey/index */ \"engage_survey/index\");\n/* harmony import */ var engage_survey_index__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(engage_survey_index__WEBPACK_IMPORTED_MODULE_7__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    SurveySidePanel: (engage_survey_components_sidepanel_SurveySidePanel__WEBPACK_IMPORTED_MODULE_0___default()),\n    SurveyBackButton: (engage_survey_components_button_SurveyBackButton__WEBPACK_IMPORTED_MODULE_1___default()),\n    Loader: (tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_4___default()),\n    SurveyVoteTitle: (engage_survey_components_content_SurveyVoteTitle__WEBPACK_IMPORTED_MODULE_2___default()),\n    SurveyVoteContent: (engage_survey_components_content_SurveyVoteContent__WEBPACK_IMPORTED_MODULE_3___default()),\n    SurveyResultContent: (engage_survey_components_content_SurveyResultContent__WEBPACK_IMPORTED_MODULE_5___default()),\n    Layout: (tui_components_layouts_LayoutOneColumnWithSidePanel__WEBPACK_IMPORTED_MODULE_6___default()),\n  },\n  mixins: [engage_survey_index__WEBPACK_IMPORTED_MODULE_7__[\"surveyPageMixin\"]],\n});\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyVoteView.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/CreateSurvey.vue?vue&type=template&id=a3fdee92&":
/*!**********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/CreateSurvey.vue?vue&type=template&id=a3fdee92& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-engageSurvey-createSurvey\" },\n    [\n      _c(\"SurveyForm\", {\n        directives: [\n          {\n            name: \"show\",\n            rawName: \"v-show\",\n            value: _vm.stage === 0,\n            expression: \"stage === 0\"\n          }\n        ],\n        attrs: { survey: _vm.survey },\n        on: {\n          next: _vm.next,\n          cancel: function($event) {\n            return _vm.$emit(\"cancel\")\n          }\n        }\n      }),\n      _vm._v(\" \"),\n      _c(\"AccessForm\", {\n        directives: [\n          {\n            name: \"show\",\n            rawName: \"v-show\",\n            value: _vm.stage === 1,\n            expression: \"stage === 1\"\n          }\n        ],\n        attrs: {\n          \"item-id\": \"0\",\n          component: \"engage_survey\",\n          \"show-back\": true,\n          submitting: _vm.submitting,\n          \"selected-access\": _vm.containerValues.access,\n          \"private-disabled\": _vm.privateDisabled,\n          \"restricted-disabled\": _vm.restrictedDisabled,\n          container: _vm.container\n        },\n        on: {\n          done: _vm.done,\n          back: _vm.back,\n          cancel: function($event) {\n            return _vm.$emit(\"cancel\")\n          }\n        }\n      })\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/CreateSurvey.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/box/RadioBox.vue?vue&type=template&id=2612b5d5&":
/*!**********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/box/RadioBox.vue?vue&type=template&id=2612b5d5& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"FormRowFieldset\",\n    { staticClass: \"tui-engageSurvey-radioBox\" },\n    [\n      _c(\n        \"RadioGroup\",\n        {\n          model: {\n            value: _vm.option,\n            callback: function($$v) {\n              _vm.option = $$v\n            },\n            expression: \"option\"\n          }\n        },\n        _vm._l(_vm.options, function(item) {\n          return _c(\n            \"Radio\",\n            {\n              key: item.id,\n              staticClass: \"tui-engageSurvey-radioBox__radio\",\n              attrs: { name: item.value, value: item.id, label: item.value }\n            },\n            [_vm._v(\"\\n      \" + _vm._s(item.value) + \"\\n    \")]\n          )\n        }),\n        1\n      )\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/box/RadioBox.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/box/SquareBox.vue?vue&type=template&id=410b958e&":
/*!***********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/box/SquareBox.vue?vue&type=template&id=410b958e& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    _vm._l(_vm.options, function(option) {\n      return _c(\n        \"FormRowFieldset\",\n        { key: option.id, staticClass: \"tui-engageSurvey-squareBox\" },\n        [\n          _c(\n            \"Checkbox\",\n            {\n              key: option.id,\n              staticClass: \"tui-engageSurvey-squareBox__checkbox\",\n              attrs: {\n                \"aria-label\": option.value,\n                name: option.value,\n                value: option.id\n              },\n              on: {\n                change: function($event) {\n                  return _vm.$_handleChange(option.id, $event)\n                }\n              }\n            },\n            [_vm._v(\"\\n      \" + _vm._s(option.value) + \"\\n    \")]\n          )\n        ],\n        1\n      )\n    }),\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/box/SquareBox.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=template&id=077cc8bf&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/button/SurveyBackButton.vue?vue&type=template&id=077cc8bf& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"a\",\n    { attrs: { href: _vm.getUrl } },\n    [\n      _c(\"BackArrow\", {\n        attrs: { size: \"200\", alt: _vm.$str(\"back\", \"moodle\") }\n      }),\n      _vm._v(\"\\n  \" + _vm._s(_vm.$str(\"back\", \"moodle\")) + \"\\n\")\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/button/SurveyBackButton.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=template&id=346c79f8&":
/*!*************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/card/SurveyCard.vue?vue&type=template&id=346c79f8& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-surveyCard\" },\n    [\n      _c(\n        \"CoreCard\",\n        {\n          staticClass: \"tui-surveyCard__cardContent\",\n          attrs: { clickable: !_vm.editAble && _vm.voted }\n        },\n        [\n          _c(\n            \"div\",\n            { staticClass: \"tui-surveyCard__cardContent__inner\" },\n            [\n              _c(\n                \"div\",\n                { staticClass: \"tui-surveyCard__cardContent__inner__header\" },\n                [\n                  _c(\n                    \"section\",\n                    {\n                      staticClass:\n                        \"tui-surveyCard__cardContent__inner__header__image\"\n                    },\n                    [\n                      _c(\"img\", {\n                        attrs: { alt: _vm.name, src: _vm.extraData.image }\n                      }),\n                      _vm._v(\" \"),\n                      _c(\n                        \"h5\",\n                        {\n                          staticClass:\n                            \"tui-surveyCard__cardContent__inner__header__title\"\n                        },\n                        [\n                          _vm._v(\n                            \"\\n            \" +\n                              _vm._s(_vm.$str(\"survey\", \"engage_survey\")) +\n                              \"\\n          \"\n                          )\n                        ]\n                      )\n                    ]\n                  ),\n                  _vm._v(\" \"),\n                  _c(\"BookmarkButton\", {\n                    directives: [\n                      {\n                        name: \"show\",\n                        rawName: \"v-show\",\n                        value: !_vm.owned && !_vm.editAble,\n                        expression: \"!owned && !editAble\"\n                      }\n                    ],\n                    staticClass:\n                      \"tui-surveyCard__cardContent__inner__header__bookmark\",\n                    attrs: {\n                      size: \"300\",\n                      bookmarked: _vm.innerBookMarked,\n                      primary: false,\n                      circle: false,\n                      small: true,\n                      transparent: true\n                    },\n                    on: { click: _vm.updateBookmark }\n                  })\n                ],\n                1\n              ),\n              _vm._v(\" \"),\n              _vm.voted && !_vm.editAble\n                ? [\n                    _c(\"SurveyResultBody\", {\n                      attrs: {\n                        name: _vm.name,\n                        \"label-id\": _vm.labelId,\n                        questions: _vm.questions,\n                        access: _vm.access,\n                        \"resource-id\": _vm.instanceId\n                      },\n                      on: {\n                        \"open-result\": function($event) {\n                          _vm.show.result = true\n                        }\n                      }\n                    })\n                  ]\n                : [\n                    _c(\"SurveyCardBody\", {\n                      attrs: {\n                        name: _vm.name,\n                        questions: _vm.questions,\n                        \"resource-id\": _vm.instanceId,\n                        bookmarked: _vm.innerBookMarked,\n                        voted: _vm.voted,\n                        topics: _vm.topics,\n                        access: _vm.access,\n                        owned: _vm.owned,\n                        \"edit-able\": _vm.editAble,\n                        \"label-id\": _vm.labelId\n                      },\n                      on: { voted: _vm.handleVoted }\n                    })\n                  ]\n            ],\n            2\n          )\n        ]\n      ),\n      _vm._v(\" \"),\n      _vm.showFootnotes\n        ? _c(\"Footnotes\", { attrs: { footnotes: _vm.footnotes } })\n        : _vm._e()\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCard.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=template&id=64a8c6ba&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/card/SurveyCardBody.vue?vue&type=template&id=64a8c6ba& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"div\", { staticClass: \"tui-surveyCardBody\" }, [\n    _c(\n      \"div\",\n      { staticClass: \"tui-surveyCardBody__title\", attrs: { id: _vm.labelId } },\n      [_vm._v(\"\\n    \" + _vm._s(_vm.name) + \"\\n  \")]\n    ),\n    _vm._v(\" \"),\n    _c(\"div\", { staticClass: \"tui-surveyCardBody__footer\" }, [\n      _vm.showEdit\n        ? _c(\"h5\", { staticClass: \"tui-surveyCardBody__text\" }, [\n            _vm._v(\n              \"\\n      \" +\n                _vm._s(_vm.$str(\"noresult\", \"engage_survey\")) +\n                \"\\n    \"\n            )\n          ])\n        : _vm._e(),\n      _vm._v(\" \"),\n      _c(\n        \"div\",\n        { staticClass: \"tui-surveyCardBody__container\" },\n        [\n          !_vm.voted\n            ? _c(\"ActionLink\", {\n                attrs: {\n                  href: _vm.$url(\n                    \"/totara/engage/resources/survey/survey_vote.php\",\n                    {\n                      id: _vm.resourceId\n                    }\n                  ),\n                  text: _vm.$str(\"votenow\", \"engage_survey\"),\n                  styleclass: { primary: true }\n                }\n              })\n            : _vm.showEdit\n            ? _c(\"ActionLink\", {\n                attrs: {\n                  href: _vm.$url(\n                    \"/totara/engage/resources/survey/survey_edit.php\",\n                    {\n                      id: _vm.resourceId\n                    }\n                  ),\n                  styleclass: { primary: true, small: true },\n                  text: _vm.$str(\"editsurvey\", \"engage_survey\")\n                }\n              })\n            : _vm._e(),\n          _vm._v(\" \"),\n          _c(\n            \"div\",\n            { staticClass: \"tui-surveyCardBody__icon\" },\n            [_c(\"AccessIcon\", { attrs: { access: _vm.access, size: \"300\" } })],\n            1\n          )\n        ],\n        1\n      )\n    ])\n  ])\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyCardBody.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=template&id=372ae2c7&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/card/SurveyResultBody.vue?vue&type=template&id=372ae2c7& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-surveyResultBody\", on: { click: _vm.navigateTo } },\n    [\n      _c(\"Label\", {\n        staticClass: \"tui-surveyResultBody__title\",\n        attrs: { id: _vm.labelId, label: _vm.name }\n      }),\n      _vm._v(\" \"),\n      _c(\n        \"div\",\n        { staticClass: \"tui-surveyResultBody__progress\" },\n        _vm._l(_vm.questions, function(ref, index) {\n          var votes = ref.votes\n          var id = ref.id\n          var options = ref.options\n          var answertype = ref.answertype\n          return _c(\"SurveyQuestionResult\", {\n            key: index,\n            attrs: {\n              options: options,\n              \"question-id\": id,\n              \"total-votes\": votes,\n              \"answer-type\": answertype\n            }\n          })\n        }),\n        1\n      ),\n      _vm._v(\" \"),\n      _c(\"div\", { staticClass: \"tui-surveyResultBody__footer\" }, [\n        _c(\"div\", { staticClass: \"tui-surveyResultBody__container\" }, [\n          _c(\"p\", { staticClass: \"tui-surveyResultBody__text\" }, [\n            _vm._v(_vm._s(_vm.voteMessage))\n          ]),\n          _vm._v(\" \"),\n          _c(\n            \"div\",\n            { staticClass: \"tui-surveyResultBody__icon\" },\n            [_c(\"AccessIcon\", { attrs: { access: _vm.access, size: \"300\" } })],\n            1\n          )\n        ])\n      ])\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/SurveyResultBody.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=template&id=7dab0b91&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?vue&type=template&id=7dab0b91& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-surveyQuestionResult\" },\n    [\n      _vm._l(_vm.calulatedOptions, function(ref, index) {\n        var votes = ref.votes\n        var value = ref.value\n        return _c(\n          \"div\",\n          { key: index, staticClass: \"tui-surveyQuestionResult__progressBar\" },\n          [\n            _vm.resultContent\n              ? [\n                  _c(\n                    \"div\",\n                    { staticClass: \"tui-surveyQuestionResult__progress\" },\n                    [\n                      _c(\n                        \"div\",\n                        { staticClass: \"tui-surveyQuestionResult__bar\" },\n                        [\n                          _c(\"Progress\", {\n                            attrs: {\n                              small: true,\n                              \"hide-value\": true,\n                              value: _vm.getValues(votes),\n                              max: _vm.totalVotes,\n                              \"hide-background\": _vm.isMultiChoice,\n                              \"show-empty-state\": _vm.isMultiChoice\n                            }\n                          })\n                        ],\n                        1\n                      ),\n                      _vm._v(\" \"),\n                      _c(\n                        \"span\",\n                        { staticClass: \"tui-surveyQuestionResult__count\" },\n                        [_vm._v(\"\\n          \" + _vm._s(votes) + \"\\n        \")]\n                      )\n                    ]\n                  )\n                ]\n              : [\n                  _vm.isMultiChoice\n                    ? [\n                        _c(\n                          \"div\",\n                          {\n                            staticClass:\n                              \"tui-surveyQuestionResult__cardProgress\"\n                          },\n                          [\n                            _c(\n                              \"div\",\n                              { staticClass: \"tui-surveyQuestionResult__bar\" },\n                              [\n                                _c(\"Progress\", {\n                                  attrs: {\n                                    small: true,\n                                    \"hide-value\": true,\n                                    value: _vm.getValues(votes),\n                                    max: _vm.totalVotes,\n                                    \"hide-background\": _vm.isMultiChoice,\n                                    \"show-empty-state\": _vm.isMultiChoice\n                                  }\n                                })\n                              ],\n                              1\n                            ),\n                            _vm._v(\" \"),\n                            _c(\n                              \"span\",\n                              {\n                                staticClass: \"tui-surveyQuestionResult__count\"\n                              },\n                              [\n                                _vm._v(\n                                  \"\\n            \" +\n                                    _vm._s(votes) +\n                                    \"\\n          \"\n                                )\n                              ]\n                            )\n                          ]\n                        )\n                      ]\n                    : [\n                        _c(\"Progress\", {\n                          attrs: {\n                            small: true,\n                            \"hide-value\": true,\n                            value: votes,\n                            max: _vm.totalVotes,\n                            \"hide-background\": _vm.isMultiChoice,\n                            \"show-empty-state\": _vm.isMultiChoice\n                          }\n                        })\n                      ]\n                ],\n            _vm._v(\" \"),\n            _vm.isSingleChoice\n              ? [\n                  _c(\n                    \"span\",\n                    { staticClass: \"tui-surveyQuestionResult__percent\" },\n                    [\n                      _vm._v(\n                        \"\\n        \" +\n                          _vm._s(\n                            _vm.$str(\n                              \"percentage\",\n                              \"engage_survey\",\n                              _vm.percentage(votes)\n                            )\n                          ) +\n                          \"\\n      \"\n                      )\n                    ]\n                  )\n                ]\n              : _vm._e(),\n            _vm._v(\" \"),\n            _c(\"span\", { staticClass: \"tui-surveyQuestionResult__answer\" }, [\n              _vm._v(\"\\n      \" + _vm._s(value) + \"\\n    \")\n            ])\n          ],\n          2\n        )\n      }),\n      _vm._v(\" \"),\n      _vm.resultContent\n        ? [\n            _c(\"div\", { staticClass: \"tui-surveyQuestionResult__votes\" }, [\n              _c(\"span\", [_vm._v(\"Total votes: \" + _vm._s(_vm.totalVotes))])\n            ])\n          ]\n        : _vm._e()\n    ],\n    2\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/card/result/SurveyQuestionResult.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=template&id=3919acca&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/content/SurveyResultContent.vue?vue&type=template&id=3919acca& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return !_vm.$apollo.loading\n    ? _c(\n        \"div\",\n        { staticClass: \"tui-surveyResultContent\" },\n        [\n          _vm._l(_vm.questions, function(ref, index) {\n            var id = ref.id\n            var votes = ref.votes\n            var options = ref.options\n            var answertype = ref.answertype\n            return _c(\"SurveyQuestionResult\", {\n              key: index,\n              attrs: {\n                \"question-id\": id,\n                \"total-votes\": votes,\n                \"answer-type\": answertype,\n                options: options,\n                \"result-content\": true\n              }\n            })\n          }),\n          _vm._v(\" \"),\n          _vm.isMultiChoice\n            ? [\n                _c(\n                  \"div\",\n                  { staticClass: \"tui-surveyResultContent__participant\" },\n                  [\n                    _c(\n                      \"span\",\n                      {\n                        staticClass:\n                          \"tui-surveyResultContent__participantnumber\"\n                      },\n                      [_vm._v(_vm._s(_vm.showNumberOfParticipant))]\n                    ),\n                    _vm._v(\"\\n      \" + _vm._s(_vm.showParticipants) + \"\\n    \")\n                  ]\n                )\n              ]\n            : _vm._e()\n        ],\n        2\n      )\n    : _vm._e()\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyResultContent.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=template&id=a88b0b24&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/content/SurveyVoteContent.vue?vue&type=template&id=a88b0b24& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-surveyVoteContent\" },\n    [\n      _c(\n        \"Form\",\n        {\n          staticClass: \"tui-surveyVoteContent__form\",\n          attrs: { vertical: true }\n        },\n        [\n          _vm.isSingleChoice\n            ? [\n                _c(\"RadioBox\", {\n                  attrs: { options: _vm.options },\n                  model: {\n                    value: _vm.answer,\n                    callback: function($$v) {\n                      _vm.answer = $$v\n                    },\n                    expression: \"answer\"\n                  }\n                })\n              ]\n            : _vm.isMultiChoice\n            ? [\n                _c(\"SquareBox\", {\n                  attrs: { options: _vm.options },\n                  model: {\n                    value: _vm.answer,\n                    callback: function($$v) {\n                      _vm.answer = $$v\n                    },\n                    expression: \"answer\"\n                  }\n                })\n              ]\n            : _vm._e(),\n          _vm._v(\" \"),\n          _c(\"Button\", {\n            staticClass: \"tui-surveyVoteContent__button\",\n            attrs: {\n              disabled: null == _vm.answer || _vm.disabled,\n              styleclass: { primary: true },\n              text: _vm.$str(\"vote\", \"engage_survey\"),\n              \"aria-label\": _vm.$str(\"vote\", \"engage_survey\")\n            },\n            on: { click: _vm.vote }\n          })\n        ],\n        2\n      )\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyVoteContent.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/content/SurveyVoteTitle.vue?vue&type=template&id=6565b40d&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/content/SurveyVoteTitle.vue?vue&type=template&id=6565b40d& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"div\", { staticClass: \"tui-surveyVoteTitle\" }, [\n    _c(\n      \"div\",\n      { staticClass: \"tui-surveyVoteTitle__head\" },\n      [\n        _c(\"h3\", { staticClass: \"tui-surveyVoteTitle__head__title\" }, [\n          _vm._v(\"\\n      \" + _vm._s(_vm.title) + \"\\n    \")\n        ]),\n        _vm._v(\" \"),\n        _c(\"BookmarkButton\", {\n          directives: [\n            {\n              name: \"show\",\n              rawName: \"v-show\",\n              value: !_vm.owned,\n              expression: \"!owned\"\n            }\n          ],\n          attrs: {\n            primary: false,\n            circle: true,\n            bookmarked: _vm.bookmarked,\n            size: \"300\"\n          },\n          on: {\n            click: function($event) {\n              return _vm.$emit(\"bookmark\", $event)\n            }\n          }\n        })\n      ],\n      1\n    )\n  ])\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/content/SurveyVoteTitle.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=template&id=68406178&":
/*!*************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/form/SurveyForm.vue?vue&type=template&id=68406178& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"Uniform\",\n    {\n      staticClass: \"tui-totaraEngage-surveyForm\",\n      attrs: { \"initial-values\": _vm.initialValues, vertical: true },\n      on: { submit: _vm.submit, change: _vm.change }\n    },\n    [\n      _c(\n        \"div\",\n        { staticClass: \"tui-totaraEngage-surveyForm__title\" },\n        [\n          _c(\n            \"FieldContextProvider\",\n            [\n              _c(\"FormText\", {\n                attrs: {\n                  name: \"question\",\n                  validations: function(v) {\n                    return [v.required()]\n                  },\n                  maxlength: 60,\n                  \"aria-label\": _vm.$str(\"formtitle\", \"engage_survey\"),\n                  placeholder: _vm.$str(\"formtitle\", \"engage_survey\"),\n                  disabled: _vm.submitting\n                }\n              })\n            ],\n            1\n          )\n        ],\n        1\n      ),\n      _vm._v(\" \"),\n      _c(\n        \"FormRowFieldset\",\n        { attrs: { label: _vm.$str(\"formtypetitle\", \"engage_survey\") } },\n        [\n          _c(\n            \"FormRadioGroup\",\n            {\n              attrs: {\n                name: \"optionType\",\n                validations: function(v) {\n                  return [v.required()]\n                },\n                horizontal: true\n              }\n            },\n            [\n              _c(\n                \"Radio\",\n                {\n                  class: [\n                    \"tui-totaraEngage-surveyForm__optionType\",\n                    \"tui-totaraEngage-surveyForm__optionType--single\"\n                  ],\n                  attrs: { name: \"optionType\", value: _vm.singleChoice }\n                },\n                [\n                  _vm._v(\n                    \"\\n        \" +\n                      _vm._s(_vm.$str(\"optionsingle\", \"engage_survey\")) +\n                      \"\\n      \"\n                  )\n                ]\n              ),\n              _vm._v(\" \"),\n              _c(\n                \"Radio\",\n                {\n                  class: [\n                    \"tui-totaraEngage-surveyForm__optionType\",\n                    \"tui-totaraEngage-surveyForm__optionType--multiple\"\n                  ],\n                  attrs: { name: \"optionType\", value: _vm.multiChoice }\n                },\n                [\n                  _vm._v(\n                    \"\\n        \" +\n                      _vm._s(_vm.$str(\"optionmultiple\", \"engage_survey\")) +\n                      \"\\n      \"\n                  )\n                ]\n              )\n            ],\n            1\n          )\n        ],\n        1\n      ),\n      _vm._v(\" \"),\n      _c(\n        \"FormRowFieldset\",\n        {\n          staticClass: \"tui-totaraEngage-surveyForm__answerTitle\",\n          attrs: { label: _vm.$str(\"optionstitle\", \"engage_survey\") }\n        },\n        [\n          _c(\"FieldArray\", {\n            attrs: { path: \"options\" },\n            scopedSlots: _vm._u([\n              {\n                key: \"default\",\n                fn: function(ref) {\n                  var items = ref.items\n                  var push = ref.push\n                  var remove = ref.remove\n                  return [\n                    _c(\"Repeater\", {\n                      staticClass: \"tui-totaraEngage-surveyForm__repeater\",\n                      attrs: {\n                        rows: items,\n                        \"min-rows\": _vm.minOptions,\n                        \"max-rows\": _vm.maxOptions,\n                        disabled: _vm.submitting,\n                        \"delete-icon\": true,\n                        \"allow-deleting-first-items\": false\n                      },\n                      on: {\n                        add: function($event) {\n                          push(_vm.newOption())\n                        },\n                        remove: function(item, i) {\n                          return remove(i)\n                        }\n                      },\n                      scopedSlots: _vm._u(\n                        [\n                          {\n                            key: \"default\",\n                            fn: function(ref) {\n                              var row = ref.row\n                              var index = ref.index\n                              return [\n                                _c(\n                                  \"div\",\n                                  {\n                                    staticClass:\n                                      \"tui-totaraEngage-surveyForm__repeater__input\"\n                                  },\n                                  [\n                                    _c(\n                                      \"FieldContextProvider\",\n                                      [\n                                        _c(\"FormText\", {\n                                          attrs: {\n                                            name: [index, \"text\"],\n                                            validations: function(v) {\n                                              return [v.required()]\n                                            },\n                                            maxlength: 80,\n                                            \"aria-label\": _vm.$str(\n                                              \"option\",\n                                              \"engage_survey\"\n                                            )\n                                          }\n                                        })\n                                      ],\n                                      1\n                                    )\n                                  ],\n                                  1\n                                )\n                              ]\n                            }\n                          }\n                        ],\n                        null,\n                        true\n                      )\n                    })\n                  ]\n                }\n              }\n            ])\n          })\n        ],\n        1\n      ),\n      _vm._v(\" \"),\n      _c(\n        \"ButtonGroup\",\n        {\n          staticClass: \"tui-totaraEngage-surveyForm__buttons\",\n          class: {\n            \"tui-totaraEngage-surveyForm__buttons--right\": _vm.showButtonRight,\n            \"tui-totaraEngage-surveyForm__buttons--left\": _vm.showButtonLeft\n          }\n        },\n        [\n          _c(\"LoadingButton\", {\n            staticClass: \"tui-totaraEngage-surveyForm__button\",\n            attrs: {\n              type: \"submit\",\n              loading: _vm.submitting,\n              primary: true,\n              disabled: _vm.disabled,\n              text: _vm.buttonText\n            }\n          }),\n          _vm._v(\" \"),\n          _c(\"CancelButton\", {\n            staticClass: \"tui-totaraEngage-surveyForm__cancelButton\",\n            attrs: { disabled: _vm.submitting },\n            on: {\n              click: function($event) {\n                return _vm.$emit(\"cancel\")\n              }\n            }\n          })\n        ],\n        1\n      )\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/form/SurveyForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/info/Author.vue?vue&type=template&id=3aabbc1b&":
/*!*********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/info/Author.vue?vue&type=template&id=3aabbc1b& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-engageSurvey-author\" },\n    [\n      _c(\"Avatar\", {\n        attrs: {\n          src: _vm.profileImageUrl,\n          alt: _vm.profileImageAlt,\n          size: \"xxsmall\"\n        }\n      }),\n      _vm._v(\" \"),\n      _c(\n        \"a\",\n        {\n          staticClass: \"tui-engageSurvey-author__userLink\",\n          attrs: { href: _vm.$url(\"/user/profile.php\", { id: _vm.userId }) }\n        },\n        [_vm._v(\"\\n    \" + _vm._s(_vm.fullname) + \"\\n  \")]\n      )\n    ],\n    1\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/info/Author.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/shape/SurveyBadge.vue?vue&type=template&id=a253c7ec&":
/*!***************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/shape/SurveyBadge.vue?vue&type=template&id=a253c7ec& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"svg\",\n    {\n      staticClass: \"tui-surveyBadge\",\n      attrs: {\n        viewBox: \"0 0 105 40\",\n        version: \"1.1\",\n        xmlns: \"http://www.w3.org/2000/svg\",\n        \"xmlns:xlink\": \"http://www.w3.org/1999/xlink\"\n      }\n    },\n    [\n      _c(\"title\", [_vm._v(_vm._s(_vm.$str(\"survey\", \"engage_survey\")))]),\n      _vm._v(\" \"),\n      _c(\"desc\", [_vm._v(_vm._s(_vm.$str(\"survey\", \"engage_survey\")))]),\n      _vm._v(\" \"),\n      _c(\"g\", { staticClass: \"tui-surveyBadge__shapeParent\" }, [\n        _c(\"g\", { attrs: { transform: \"translate(-91.000000, 0.000000)\" } }, [\n          _c(\"g\", [\n            _c(\n              \"g\",\n              { attrs: { transform: \"translate(91.000000, 0.000000)\" } },\n              [\n                _c(\"path\", {\n                  staticClass: \"tui-surveyBadge__shape\",\n                  attrs: {\n                    d:\n                      \"M1.5,33.5 L3.71008242,33.5  L101.289918,33.5 L103.5,33.5 L103.5,6 C103.5,3.51471863 101.485281,1.5 99,1.5 L6,1.5 C3.51471863,1.5 1.5,3.51471863 1.5,6 L1.5,33.5 Z\"\n                  }\n                }),\n                _vm._v(\" \"),\n                _c(\n                  \"g\",\n                  {\n                    staticClass: \"tui-surveyBadge__text\",\n                    attrs: { transform: \"translate(30.500000, 7.000000)\" }\n                  },\n                  [\n                    _c(\"g\", [\n                      _c(\"text\", [\n                        _c(\"tspan\", { attrs: { x: \"0\", y: \"13\" } }, [\n                          _vm._v(\n                            \"\\n                  \" +\n                              _vm._s(_vm.$str(\"survey\", \"engage_survey\")) +\n                              \"\\n                \"\n                          )\n                        ])\n                      ])\n                    ])\n                  ]\n                )\n              ]\n            )\n          ])\n        ])\n      ])\n    ]\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/shape/SurveyBadge.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=template&id=90fd8022&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?vue&type=template&id=90fd8022& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-surveyBaseSidePanel\" },\n    [\n      _vm._t(\"modal\"),\n      _vm._v(\" \"),\n      _c(\n        \"div\",\n        { staticClass: \"tui-surveyBaseSidePanel__header\" },\n        [\n          _c(\"MiniProfileCard\", {\n            attrs: {\n              \"no-border\": true,\n              interactive: false,\n              name: _vm.userFullName,\n              \"avatar-src\": _vm.userProfileImageUrl,\n              \"avatar-alt\": _vm.userProfileImageAlt || \"\",\n              email: _vm.userEmail,\n              mention: _vm.userFullName\n            }\n          }),\n          _vm._v(\" \"),\n          _vm.owned\n            ? _c(\"EngageDropDown\", {\n                attrs: { position: \"bottom-right\", actions: _vm.actions }\n              })\n            : _vm._e()\n        ],\n        1\n      ),\n      _vm._v(\" \"),\n      _c(\n        \"Tabs\",\n        {\n          staticClass: \"tui-surveyBaseSidePanel__tabs\",\n          attrs: { \"transparent-tabs\": true }\n        },\n        [\n          _c(\n            \"Tab\",\n            {\n              staticClass: \"tui-surveyBaseSidePanel__overviewBox\",\n              attrs: {\n                id: \"overview\",\n                name: _vm.$str(\"overview\", \"totara_engage\"),\n                disabled: true\n              }\n            },\n            [_vm._t(\"overview\")],\n            2\n          )\n        ],\n        1\n      )\n    ],\n    2\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveyBaseSidePanel.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=template&id=47f210c0&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?vue&type=template&id=47f210c0& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return !_vm.$apollo.loading\n    ? _c(\"SurveyBaseSidePanel\", {\n        staticClass: \"tui-surveySidePanel\",\n        attrs: {\n          \"user-full-name\": _vm.survey.resource.user.fullname,\n          \"user-email\": _vm.userEmail,\n          \"user-id\": _vm.survey.resource.user.id,\n          owned: _vm.survey.owned,\n          \"user-profile-image-alt\":\n            _vm.survey.resource.user.profileimagealt || \"\",\n          \"user-profile-image-url\": _vm.survey.resource.user.profileimageurl,\n          actions: _vm.actions\n        },\n        scopedSlots: _vm._u(\n          [\n            {\n              key: \"modal\",\n              fn: function() {\n                return [\n                  _c(\n                    \"ModalPresenter\",\n                    {\n                      attrs: { open: _vm.openModalFromAction },\n                      on: {\n                        \"request-close\": function($event) {\n                          _vm.openModalFromAction = false\n                        }\n                      }\n                    },\n                    [\n                      _c(\"EngageWarningModal\", {\n                        attrs: {\n                          \"message-content\": _vm.$str(\n                            \"deletewarningmsg\",\n                            \"engage_survey\"\n                          )\n                        },\n                        on: { delete: _vm.handleDelete }\n                      })\n                    ],\n                    1\n                  )\n                ]\n              },\n              proxy: true\n            },\n            {\n              key: \"overview\",\n              fn: function() {\n                return [\n                  _c(\n                    \"p\",\n                    { staticClass: \"tui-surveySidePanel__timeDescription\" },\n                    [\n                      _vm._v(\n                        \"\\n      \" +\n                          _vm._s(_vm.survey.timedescription) +\n                          \"\\n    \"\n                      )\n                    ]\n                  ),\n                  _vm._v(\" \"),\n                  _vm.survey.owned\n                    ? _c(\"AccessSetting\", {\n                        attrs: {\n                          \"item-id\": _vm.resourceId,\n                          component: \"engage_survey\",\n                          \"access-value\": _vm.survey.resource.access,\n                          topics: _vm.survey.topics,\n                          submitting: false,\n                          \"open-modal\": _vm.openModalFromButtonLabel,\n                          \"enable-time-view\": false\n                        },\n                        on: {\n                          \"close-modal\": function($event) {\n                            _vm.openModalFromButtonLabel = false\n                          },\n                          \"access-update\": _vm.updateAccess\n                        }\n                      })\n                    : _c(\"AccessDisplay\", {\n                        attrs: {\n                          \"access-value\": _vm.survey.resource.access,\n                          topics: _vm.survey.topics,\n                          \"show-button\": false\n                        }\n                      }),\n                  _vm._v(\" \"),\n                  _c(\"MediaSetting\", {\n                    attrs: {\n                      owned: _vm.survey.owned,\n                      \"access-value\": _vm.survey.resource.access,\n                      \"instance-id\": _vm.resourceId,\n                      \"shared-by-count\": _vm.survey.sharedbycount,\n                      \"like-button-aria-label\": _vm.likeButtonLabel,\n                      liked: _vm.survey.reacted,\n                      \"component-name\": \"engage_survey\"\n                    },\n                    on: {\n                      \"access-update\": _vm.updateAccess,\n                      \"access-modal\": function($event) {\n                        _vm.openModalFromButtonLabel = true\n                      },\n                      \"update-like-status\": _vm.updateLikeStatus\n                    }\n                  })\n                ]\n              },\n              proxy: true\n            }\n          ],\n          null,\n          false,\n          695796090\n        )\n      })\n    : _vm._e()\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/components/sidepanel/SurveySidePanel.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=template&id=5c93f5fc&":
/*!*******************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/pages/SurveyEditView.vue?vue&type=template&id=5c93f5fc& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"Layout\", {\n    staticClass: \"tui-surveyEditView\",\n    scopedSlots: _vm._u([\n      {\n        key: \"column\",\n        fn: function() {\n          return [\n            _c(\"Loader\", {\n              attrs: { loading: _vm.$apollo.loading, fullpage: true }\n            }),\n            _vm._v(\" \"),\n            !_vm.$apollo.loading\n              ? [\n                  _c(\"SurveyBackButton\", {\n                    staticClass: \"tui-surveyView__backButton\",\n                    attrs: { owned: _vm.survey.owned }\n                  })\n                ]\n              : _vm._e(),\n            _vm._v(\" \"),\n            !_vm.$apollo.loading\n              ? _c(\n                  \"div\",\n                  { staticClass: \"tui-surveyEditView__layout\" },\n                  [\n                    _c(\"SurveyForm\", {\n                      staticClass: \"tui-surveyEditView__layout__content\",\n                      attrs: {\n                        survey: _vm.surveyInstance,\n                        \"button-content\": _vm.$str(\"save\", \"engage_survey\"),\n                        submitting: _vm.submitting,\n                        \"show-button-right\": false\n                      },\n                      on: { next: _vm.handleSave, cancel: _vm.handleCancel }\n                    })\n                  ],\n                  1\n                )\n              : _vm._e()\n          ]\n        },\n        proxy: true\n      },\n      {\n        key: \"sidepanel\",\n        fn: function() {\n          return [\n            _c(\"SurveySidePanel\", { attrs: { \"resource-id\": _vm.resourceId } })\n          ]\n        },\n        proxy: true\n      }\n    ])\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyEditView.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyView.vue?vue&type=template&id=3bf2bf5c&":
/*!***************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/pages/SurveyView.vue?vue&type=template&id=3bf2bf5c& ***!
  \***************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"Layout\", {\n    staticClass: \"tui-surveyView\",\n    scopedSlots: _vm._u([\n      {\n        key: \"column\",\n        fn: function() {\n          return [\n            _c(\"Loader\", {\n              attrs: { loading: _vm.$apollo.loading, fullpage: true }\n            }),\n            _vm._v(\" \"),\n            !_vm.$apollo.loading\n              ? [\n                  _c(\"SurveyBackButton\", {\n                    staticClass: \"tui-surveyView__backButton\",\n                    attrs: { owned: _vm.survey.owned }\n                  })\n                ]\n              : _vm._e(),\n            _vm._v(\" \"),\n            !_vm.$apollo.loading\n              ? _c(\"div\", { staticClass: \"tui-surveyView__layout\" }, [\n                  _c(\n                    \"div\",\n                    { staticClass: \"tui-surveyView__layout__content\" },\n                    [\n                      _c(\"SurveyVoteTitle\", {\n                        staticClass: \"tui-surveyView__layout__content__title\",\n                        attrs: {\n                          title: _vm.firstQuestion.value,\n                          owned: _vm.survey.owned\n                        }\n                      }),\n                      _vm._v(\" \"),\n                      _c(\"SurveyVoteContent\", {\n                        attrs: {\n                          \"answer-type\": _vm.firstQuestion.answertype,\n                          options: _vm.firstQuestion.options,\n                          \"question-id\": _vm.firstQuestion.id,\n                          \"resource-id\": _vm.resourceId,\n                          disabled: true\n                        }\n                      })\n                    ],\n                    1\n                  )\n                ])\n              : _vm._e()\n          ]\n        },\n        proxy: true\n      },\n      {\n        key: \"sidepanel\",\n        fn: function() {\n          return [\n            _c(\"SurveySidePanel\", { attrs: { \"resource-id\": _vm.resourceId } })\n          ]\n        },\n        proxy: true\n      }\n    ])\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyView.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/engage_survey/pages/SurveyVoteView.vue?vue&type=template&id=1458fadc&":
/*!*******************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/engage_survey/pages/SurveyVoteView.vue?vue&type=template&id=1458fadc& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"Layout\", {\n    staticClass: \"tui-surveyVoteView\",\n    scopedSlots: _vm._u([\n      {\n        key: \"column\",\n        fn: function() {\n          return [\n            _c(\"Loader\", {\n              attrs: { loading: _vm.$apollo.loading, fullpage: true }\n            }),\n            _vm._v(\" \"),\n            !_vm.$apollo.loading\n              ? [\n                  _c(\"SurveyBackButton\", {\n                    staticClass: \"tui-surveyView__backButton\",\n                    attrs: { owned: _vm.survey.owned }\n                  })\n                ]\n              : _vm._e(),\n            _vm._v(\" \"),\n            !_vm.$apollo.loading\n              ? _c(\"div\", { staticClass: \"tui-surveyVoteView__layout\" }, [\n                  _c(\n                    \"div\",\n                    { staticClass: \"tui-surveyVoteView__layout__content\" },\n                    [\n                      _c(\"SurveyVoteTitle\", {\n                        staticClass:\n                          \"tui-surveyVoteView__layout__content__title\",\n                        attrs: {\n                          title: _vm.firstQuestion.value,\n                          bookmarked: _vm.bookmarked,\n                          owned: _vm.survey.owned\n                        },\n                        on: { bookmark: _vm.updateBookmark }\n                      }),\n                      _vm._v(\" \"),\n                      !_vm.survey.voted && !_vm.survey.owned\n                        ? _c(\"SurveyVoteContent\", {\n                            attrs: {\n                              \"answer-type\": _vm.firstQuestion.answertype,\n                              options: _vm.firstQuestion.options,\n                              \"question-id\": _vm.firstQuestion.id,\n                              \"resource-id\": _vm.resourceId\n                            }\n                          })\n                        : _c(\"SurveyResultContent\", {\n                            attrs: { \"resource-id\": _vm.resourceId }\n                          })\n                    ],\n                    1\n                  )\n                ])\n              : _vm._e()\n          ]\n        },\n        proxy: true\n      },\n      {\n        key: \"sidepanel\",\n        fn: function() {\n          return [\n            _c(\"SurveySidePanel\", { attrs: { \"resource-id\": _vm.resourceId } })\n          ]\n        },\n        proxy: true\n      }\n    ])\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/engage_survey/pages/SurveyVoteView.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

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
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_get_survey\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"survey\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_survey_instance\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timeexpired\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"resource\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimagealt\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimageurl\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"email\"},\"arguments\":[],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questions\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"answertype\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"options\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questionid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"questionresults\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"answertype\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"votes\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"participants\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"options\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"votes\"},\"arguments\":[],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timedescription\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"owned\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"voted\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"sharedbycount\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"reacted\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/survey/webapi/ajax/get_survey.graphql?");

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

/***/ "engage_survey/components/button/SurveyBackButton":
/*!************************************************************************************!*\
  !*** external "tui.require(\"engage_survey/components/button/SurveyBackButton\")" ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/components/button/SurveyBackButton\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/components/button/SurveyBackButton\\%22)%22?");

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

/***/ "engage_survey/components/sidepanel/SurveyBaseSidePanel":
/*!******************************************************************************************!*\
  !*** external "tui.require(\"engage_survey/components/sidepanel/SurveyBaseSidePanel\")" ***!
  \******************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_survey/components/sidepanel/SurveyBaseSidePanel\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_survey/components/sidepanel/SurveyBaseSidePanel\\%22)%22?");

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

/***/ "totara_engage/components/dropdown/EngageDropDown":
/*!************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/dropdown/EngageDropDown\")" ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/dropdown/EngageDropDown\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/dropdown/EngageDropDown\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/form/AccessForm":
/*!****************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/form/AccessForm\")" ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/form/AccessForm\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/form/AccessForm\\%22)%22?");

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

/***/ "tui/components/form/Checkbox":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/form/Checkbox\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/Checkbox\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/Checkbox\\%22)%22?");

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

/***/ "tui/components/icons/common/BackArrow":
/*!*************************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/common/BackArrow\")" ***!
  \*************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/common/BackArrow\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/common/BackArrow\\%22)%22?");

/***/ }),

/***/ "tui/components/layouts/LayoutOneColumnWithSidePanel":
/*!***************************************************************************************!*\
  !*** external "tui.require(\"tui/components/layouts/LayoutOneColumnWithSidePanel\")" ***!
  \***************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/layouts/LayoutOneColumnWithSidePanel\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/layouts/LayoutOneColumnWithSidePanel\\%22)%22?");

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

/***/ })

/******/ });