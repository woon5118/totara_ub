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
/******/ 		"pathway_learning_plan/tui_bundle.legacy.development": 0
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
/******/ 	deferredModules.push(["./client/src/pathway_learning_plan/tui.json","tui/vendors.legacy.development"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/src/pathway_learning_plan/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!****************************************************************************************************!*\
  !*** ./client/src/pathway_learning_plan/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./achievements/AchievementDisplay\": \"./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue\",\n\t\"./achievements/AchievementDisplay.vue\": \"./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/src/pathway_learning_plan/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/src/pathway_learning_plan/components_sync_^(?:(?");

/***/ }),

/***/ "./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue":
/*!*****************************************************************************************!*\
  !*** ./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue ***!
  \*****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _AchievementDisplay_vue_vue_type_template_id_37191570___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AchievementDisplay.vue?vue&type=template&id=37191570& */ \"./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=template&id=37191570&\");\n/* harmony import */ var _AchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./AchievementDisplay.vue?vue&type=script&lang=js& */ \"./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _AchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./AchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _AchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _AchievementDisplay_vue_vue_type_template_id_37191570___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _AchievementDisplay_vue_vue_type_template_id_37191570___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _AchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_AchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************************************!*\
  !*** ./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_AchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./AchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_AchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************!*\
  !*** ./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_AchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./AchievementDisplay.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_AchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=template&id=37191570&":
/*!************************************************************************************************************************!*\
  !*** ./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=template&id=37191570& ***!
  \************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_AchievementDisplay_vue_vue_type_template_id_37191570___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./AchievementDisplay.vue?vue&type=template&id=37191570& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=template&id=37191570&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_AchievementDisplay_vue_vue_type_template_id_37191570___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_AchievementDisplay_vue_vue_type_template_id_37191570___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/pathway_learning_plan/tui.json":
/*!***************************************************!*\
  !*** ./client/src/pathway_learning_plan/tui.json ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"pathway_learning_plan\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"pathway_learning_plan\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"pathway_learning_plan\")\ntui._bundle.addModulesFromContext(\"pathway_learning_plan/components\", __webpack_require__(\"./client/src/pathway_learning_plan/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/src/pathway_learning_plan/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"pathway_learning_plan\" : [\n    \"achievement_via_learning_plan\",\n    \"name\",\n    \"no_available_learning_plans\",\n    \"no_rating_set\",\n    \"no_permission_view_plan\",\n    \"set_on\",\n    \"view_plan\",\n    \"work_towards_level\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?./client/tooling/webpack/tui_lang_strings_loader.js!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_competency/components/achievements/AchievementLayout */ \"totara_competency/components/achievements/AchievementLayout\");\n/* harmony import */ var totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/links/ActionLink */ \"tui/components/links/ActionLink\");\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/datatable/Cell */ \"tui/components/datatable/Cell\");\n/* harmony import */ var tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/datatable/ExpandCell */ \"tui/components/datatable/ExpandCell\");\n/* harmony import */ var tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/datatable/Table */ \"tui/components/datatable/Table\");\n/* harmony import */ var tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var pathway_learning_plan_graphql_competency_plans__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! pathway_learning_plan/graphql/competency_plans */ \"./server/totara/competency/pathway/learning_plan/webapi/ajax/competency_plans.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n// Components\n\n\n\n\n // GraphQL\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AchievementLayout: totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0___default.a,\n    ActionLink: tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default.a,\n    Cell: tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_2___default.a,\n    ExpandCell: tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_3___default.a,\n    Table: tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_4___default.a\n  },\n  inheritAttrs: false,\n  props: {\n    assignmentId: {\n      required: true,\n      type: Number\n    },\n    userId: {\n      required: true,\n      type: Number\n    }\n  },\n  data: function data() {\n    return {\n      plans: []\n    };\n  },\n  apollo: {\n    plans: {\n      query: pathway_learning_plan_graphql_competency_plans__WEBPACK_IMPORTED_MODULE_5__[\"default\"],\n      context: {\n        batch: true\n      },\n      variables: function variables() {\n        return {\n          assignment_id: this.assignmentId,\n          user_id: this.userId\n        };\n      },\n      update: function update(_ref) {\n        var plans = _ref.pathway_learning_plan_competency_plans;\n        this.$emit('loaded');\n        return plans;\n      }\n    }\n  },\n  computed: {\n    /**\n     * Check if data contains learning plan\n     *\n     * @return {Boolean}\n     */\n    hasPlans: function hasPlans() {\n      return this.plans.learning_plans;\n    },\n\n    /**\n     * Check if a scale value has been set\n     *\n     * @return {Boolean}\n     */\n    hasValue: function hasValue() {\n      return this.hasPlans && this.plans.scale_value != null;\n    }\n  },\n  methods: {\n    /**\n     * Return URL for plan\n     *\n     * @param {Integer} planId\n     * @return {String}\n     */\n    getPlanUrl: function getPlanUrl(planId) {\n      return this.$url('/totara/plan/component.php', {\n        c: 'competency',\n        id: planId\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=template&id=37191570&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?vue&type=template&id=37191570& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"div\", { staticClass: \"tui-pathwayLearningPlanAchievement\" }, [\n    !_vm.hasPlans\n      ? _c(\n          \"div\",\n          { staticClass: \"tui-pathwayLearningPlanAchievement__empty\" },\n          [\n            _vm._v(\n              \"\\n    \" +\n                _vm._s(\n                  _vm.$str(\n                    \"no_available_learning_plans\",\n                    \"pathway_learning_plan\"\n                  )\n                ) +\n                \"\\n  \"\n            )\n          ]\n        )\n      : _c(\n          \"div\",\n          { staticClass: \"tui-pathwayLearningPlanAchievement__content\" },\n          [\n            _c(\"AchievementLayout\", {\n              scopedSlots: _vm._u([\n                {\n                  key: \"left\",\n                  fn: function() {\n                    return [\n                      _c(\n                        \"div\",\n                        {\n                          staticClass:\n                            \"tui-pathwayLearningPlanAchievement__overview\"\n                        },\n                        [\n                          _c(\n                            \"h5\",\n                            {\n                              staticClass:\n                                \"tui-pathwayLearningPlanAchievement__title\"\n                            },\n                            [\n                              _vm._v(\n                                \"\\n            \" +\n                                  _vm._s(\n                                    _vm.$str(\n                                      \"achievement_via_learning_plan\",\n                                      \"pathway_learning_plan\"\n                                    )\n                                  ) +\n                                  \"\\n          \"\n                              )\n                            ]\n                          ),\n                          _vm._v(\" \"),\n                          _vm.hasValue\n                            ? [\n                                _c(\n                                  \"div\",\n                                  {\n                                    staticClass:\n                                      \"tui-pathwayLearningPlanAchievement__value\"\n                                  },\n                                  [\n                                    _c(\n                                      \"span\",\n                                      {\n                                        staticClass:\n                                          \"tui-pathwayLearningPlanAchievement__value-title\"\n                                      },\n                                      [\n                                        _vm._v(\n                                          \"\\n                \" +\n                                            _vm._s(_vm.plans.scale_value.name) +\n                                            \"\\n              \"\n                                        )\n                                      ]\n                                    ),\n                                    _vm._v(\n                                      \"\\n              \" +\n                                        _vm._s(\n                                          _vm.$str(\n                                            \"set_on\",\n                                            \"pathway_learning_plan\",\n                                            _vm.plans.date\n                                          )\n                                        ) +\n                                        \"\\n            \"\n                                    )\n                                  ]\n                                )\n                              ]\n                            : [\n                                _c(\n                                  \"div\",\n                                  {\n                                    staticClass:\n                                      \"tui-pathwayLearningPlanAchievement__noValue\"\n                                  },\n                                  [\n                                    _vm._v(\n                                      \"\\n              \" +\n                                        _vm._s(\n                                          _vm.$str(\n                                            \"no_rating_set\",\n                                            \"pathway_learning_plan\"\n                                          )\n                                        ) +\n                                        \"\\n            \"\n                                    )\n                                  ]\n                                )\n                              ]\n                        ],\n                        2\n                      )\n                    ]\n                  },\n                  proxy: true\n                },\n                {\n                  key: \"right\",\n                  fn: function() {\n                    return [\n                      _c(\"Table\", {\n                        staticClass: \"tui-pathwayLearningPlanAchievement__list\",\n                        attrs: {\n                          data: _vm.plans.learning_plans,\n                          \"expandable-rows\": true\n                        },\n                        scopedSlots: _vm._u([\n                          {\n                            key: \"row\",\n                            fn: function(ref) {\n                              var row = ref.row\n                              var expand = ref.expand\n                              var expandState = ref.expandState\n                              return [\n                                !row.can_view\n                                  ? [\n                                      _c(\"ExpandCell\", {\n                                        attrs: { header: true }\n                                      }),\n                                      _vm._v(\" \"),\n                                      _c(\"Cell\", { attrs: { size: \"11\" } }, [\n                                        _vm._v(\n                                          \"\\n                \" +\n                                            _vm._s(\n                                              _vm.$str(\n                                                \"no_permission_view_plan\",\n                                                \"pathway_learning_plan\"\n                                              )\n                                            ) +\n                                            \"\\n              \"\n                                        )\n                                      ])\n                                    ]\n                                  : [\n                                      row.can_view\n                                        ? _c(\"ExpandCell\", {\n                                            attrs: {\n                                              \"expand-state\": expandState\n                                            },\n                                            on: {\n                                              click: function($event) {\n                                                return expand()\n                                              }\n                                            }\n                                          })\n                                        : _vm._e(),\n                                      _vm._v(\" \"),\n                                      _c(\n                                        \"Cell\",\n                                        {\n                                          attrs: {\n                                            size: \"11\",\n                                            \"column-header\": _vm.$str(\n                                              \"name\",\n                                              \"pathway_learning_plan\"\n                                            )\n                                          }\n                                        },\n                                        [\n                                          _vm._v(\n                                            \"\\n                \" +\n                                              _vm._s(row.name) +\n                                              \"\\n              \"\n                                          )\n                                        ]\n                                      )\n                                    ]\n                              ]\n                            }\n                          },\n                          {\n                            key: \"expand-content\",\n                            fn: function(ref) {\n                              var row = ref.row\n                              return [\n                                _c(\n                                  \"div\",\n                                  {\n                                    staticClass:\n                                      \"tui-pathwayLearningPlanAchievement__summary\"\n                                  },\n                                  [\n                                    _c(\n                                      \"h6\",\n                                      {\n                                        staticClass:\n                                          \"tui-pathwayLearningPlanAchievement__summary-header\"\n                                      },\n                                      [\n                                        _vm._v(\n                                          \"\\n                \" +\n                                            _vm._s(row.name) +\n                                            \"\\n              \"\n                                        )\n                                      ]\n                                    ),\n                                    _vm._v(\" \"),\n                                    row.description\n                                      ? _c(\"div\", {\n                                          staticClass:\n                                            \"tui-pathwayLearningPlanAchievement__summary-body\",\n                                          domProps: {\n                                            innerHTML: _vm._s(row.description)\n                                          }\n                                        })\n                                      : _vm._e(),\n                                    _vm._v(\" \"),\n                                    _c(\"ActionLink\", {\n                                      class:\n                                        \"tui-pathwayLearningPlanAchievement__summary-button\",\n                                      attrs: {\n                                        href: _vm.getPlanUrl(row.id),\n                                        text: _vm.$str(\n                                          \"view_plan\",\n                                          \"pathway_learning_plan\"\n                                        ),\n                                        styleclass: {\n                                          primary: true,\n                                          small: true\n                                        }\n                                      }\n                                    })\n                                  ],\n                                  1\n                                )\n                              ]\n                            }\n                          }\n                        ])\n                      })\n                    ]\n                  },\n                  proxy: true\n                }\n              ])\n            })\n          ],\n          1\n        )\n  ])\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/pathway_learning_plan/components/achievements/AchievementDisplay.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./server/totara/competency/pathway/learning_plan/webapi/ajax/competency_plans.graphql":
/*!*********************************************************************************************!*\
  !*** ./server/totara/competency/pathway/learning_plan/webapi/ajax/competency_plans.graphql ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"pathway_learning_plan_competency_plans\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"user_id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"core_id\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"assignment_id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"core_id\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"pathway_learning_plan_competency_plans\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"user_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"user_id\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"assignment_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"assignment_id\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"learning_plans\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"can_view\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"description\"},\"arguments\":[],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"scale_value\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"arguments\":[],\"directives\":[]}]}},{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"date\"},\"name\":{\"kind\":\"Name\",\"value\":\"date_assigned\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"DATE\"}}],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/competency/pathway/learning_plan/webapi/ajax/competency_plans.graphql?");

/***/ }),

/***/ "totara_competency/components/achievements/AchievementLayout":
/*!***********************************************************************************************!*\
  !*** external "tui.require(\"totara_competency/components/achievements/AchievementLayout\")" ***!
  \***********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_competency/components/achievements/AchievementLayout\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_competency/components/achievements/AchievementLayout\\%22)%22?");

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

/***/ "tui/components/links/ActionLink":
/*!*******************************************************************!*\
  !*** external "tui.require(\"tui/components/links/ActionLink\")" ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/links/ActionLink\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/links/ActionLink\\%22)%22?");

/***/ })

/******/ });