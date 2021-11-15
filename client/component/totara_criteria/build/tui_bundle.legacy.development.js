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
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./client/component/totara_criteria/src/tui.json");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/component/totara_criteria/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!********************************************************************************************************!*\
  !*** ./client/component/totara_criteria/src/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./achievements/CompetencyAchievementDisplay\": \"./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue\",\n\t\"./achievements/CompetencyAchievementDisplay.vue\": \"./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue\",\n\t\"./achievements/CourseAchievementDisplay\": \"./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue\",\n\t\"./achievements/CourseAchievementDisplay.vue\": \"./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/totara_criteria/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/totara_criteria/src/components_sync_^(?:(?");

/***/ }),

/***/ "./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue":
/*!*******************************************************************************************************!*\
  !*** ./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue ***!
  \*******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _CompetencyAchievementDisplay_vue_vue_type_template_id_00c6bf4d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./CompetencyAchievementDisplay.vue?vue&type=template&id=00c6bf4d& */ \"./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=template&id=00c6bf4d&\");\n/* harmony import */ var _CompetencyAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CompetencyAchievementDisplay.vue?vue&type=script&lang=js& */ \"./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _CompetencyAchievementDisplay_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./CompetencyAchievementDisplay.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _CompetencyAchievementDisplay_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_CompetencyAchievementDisplay_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _CompetencyAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _CompetencyAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _CompetencyAchievementDisplay_vue_vue_type_template_id_00c6bf4d___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _CompetencyAchievementDisplay_vue_vue_type_template_id_00c6bf4d___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _CompetencyAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_CompetencyAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?");

/***/ }),

/***/ "./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!******************************************************************************************************************************************************!*\
  !*** ./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \******************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CompetencyAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js??ref--7-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CompetencyAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?");

/***/ }),

/***/ "./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************!*\
  !*** ./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_CompetencyAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./CompetencyAchievementDisplay.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_CompetencyAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?");

/***/ }),

/***/ "./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=style&index=0&lang=scss&":
/*!*****************************************************************************************************************************************!*\
  !*** ./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=style&index=0&lang=scss& ***!
  \*****************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?");

/***/ }),

/***/ "./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=template&id=00c6bf4d&":
/*!**************************************************************************************************************************************!*\
  !*** ./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=template&id=00c6bf4d& ***!
  \**************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CompetencyAchievementDisplay_vue_vue_type_template_id_00c6bf4d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./CompetencyAchievementDisplay.vue?vue&type=template&id=00c6bf4d& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=template&id=00c6bf4d&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CompetencyAchievementDisplay_vue_vue_type_template_id_00c6bf4d___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CompetencyAchievementDisplay_vue_vue_type_template_id_00c6bf4d___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?");

/***/ }),

/***/ "./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue":
/*!***************************************************************************************************!*\
  !*** ./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _CourseAchievementDisplay_vue_vue_type_template_id_2b90c0ea___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./CourseAchievementDisplay.vue?vue&type=template&id=2b90c0ea& */ \"./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=template&id=2b90c0ea&\");\n/* harmony import */ var _CourseAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CourseAchievementDisplay.vue?vue&type=script&lang=js& */ \"./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _CourseAchievementDisplay_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./CourseAchievementDisplay.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _CourseAchievementDisplay_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_CourseAchievementDisplay_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _CourseAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _CourseAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _CourseAchievementDisplay_vue_vue_type_template_id_2b90c0ea___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _CourseAchievementDisplay_vue_vue_type_template_id_2b90c0ea___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _CourseAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_CourseAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?");

/***/ }),

/***/ "./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************************************!*\
  !*** ./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CourseAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js??ref--7-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CourseAchievementDisplay_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?");

/***/ }),

/***/ "./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************!*\
  !*** ./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_CourseAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./CourseAchievementDisplay.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_CourseAchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?");

/***/ }),

/***/ "./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=style&index=0&lang=scss&":
/*!*************************************************************************************************************************************!*\
  !*** ./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=style&index=0&lang=scss& ***!
  \*************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?");

/***/ }),

/***/ "./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=template&id=2b90c0ea&":
/*!**********************************************************************************************************************************!*\
  !*** ./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=template&id=2b90c0ea& ***!
  \**********************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CourseAchievementDisplay_vue_vue_type_template_id_2b90c0ea___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./CourseAchievementDisplay.vue?vue&type=template&id=2b90c0ea& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=template&id=2b90c0ea&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CourseAchievementDisplay_vue_vue_type_template_id_2b90c0ea___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CourseAchievementDisplay_vue_vue_type_template_id_2b90c0ea___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?");

/***/ }),

/***/ "./client/component/totara_criteria/src/tui.json":
/*!*******************************************************!*\
  !*** ./client/component/totara_criteria/src/tui.json ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"totara_criteria\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"totara_criteria\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"totara_criteria\")\ntui._bundle.addModulesFromContext(\"totara_criteria/components\", __webpack_require__(\"./client/component/totara_criteria/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"criteria_childcompetency\": [\n    \"error_no_children\"\n  ],\n  \"criteria_othercompetency\": [\n    \"error_no_competencies\"\n  ],\n  \"totara_criteria\": [\n    \"achieve_proficiency_in_child_competencies\",\n    \"achieve_proficiency_in_other_competencies\",\n    \"assign_competency\",\n    \"competencies\",\n    \"complete\",\n    \"completion\",\n    \"confirm_assign_competency_body_by_other\",\n    \"confirm_assign_competency_body_by_self\",\n    \"confirm_assign_competency_title\",\n    \"error_competency_assignment\",\n    \"network_error\",\n    \"not_available\",\n    \"not_complete\",\n    \"achievement_level\",\n    \"self_assign_competency\",\n    \"view_competency\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_criteria\": [\n    \"complete\",\n    \"completion\",\n    \"complete_courses\",\n    \"courses\",\n    \"go_to_course\",\n    \"hidden_course\",\n    \"no_courses\",\n    \"not_available\",\n    \"not_complete\",\n    \"progress\"\n  ]\n}\n\n;\n    });\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_competency/components/achievements/AchievementLayout */ \"totara_competency/components/achievements/AchievementLayout\");\n/* harmony import */ var totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/links/ActionLink */ \"tui/components/links/ActionLink\");\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/buttons/Button */ \"tui/components/buttons/Button\");\n/* harmony import */ var tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/datatable/Cell */ \"tui/components/datatable/Cell\");\n/* harmony import */ var tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_icons_CheckSuccess__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/icons/CheckSuccess */ \"tui/components/icons/CheckSuccess\");\n/* harmony import */ var tui_components_icons_CheckSuccess__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_CheckSuccess__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_components_modal_ConfirmationModal__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/components/modal/ConfirmationModal */ \"tui/components/modal/ConfirmationModal\");\n/* harmony import */ var tui_components_modal_ConfirmationModal__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_ConfirmationModal__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/components/datatable/ExpandCell */ \"tui/components/datatable/ExpandCell\");\n/* harmony import */ var tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_competency/components/achievements/ProgressCircle */ \"totara_competency/components/achievements/ProgressCircle\");\n/* harmony import */ var totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! tui/components/datatable/Table */ \"tui/components/datatable/Table\");\n/* harmony import */ var tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! tui/notifications */ \"tui/notifications\");\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(tui_notifications__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var totara_competency_graphql_create_user_assignments__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! totara_competency/graphql/create_user_assignments */ \"./server/totara/competency/webapi/ajax/create_user_assignments.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n// Components\n\n\n\n\n\n\n\n\n\n // GraphQL\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AchievementLayout: totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0___default.a,\n    ActionLink: tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default.a,\n    Button: tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_2___default.a,\n    Cell: tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_3___default.a,\n    CheckIcon: tui_components_icons_CheckSuccess__WEBPACK_IMPORTED_MODULE_4___default.a,\n    ConfirmationModal: tui_components_modal_ConfirmationModal__WEBPACK_IMPORTED_MODULE_5___default.a,\n    ExpandCell: tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_6___default.a,\n    ProgressCircle: totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_7___default.a,\n    Table: tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_8___default.a\n  },\n  props: {\n    achievements: {\n      required: true,\n      type: Object\n    },\n    type: {\n      required: true,\n      type: String\n    },\n    userId: {\n      required: true,\n      type: Number\n    }\n  },\n  data: function data() {\n    return {\n      modalOpen: false\n    };\n  },\n  computed: {\n    /**\n     * Return int for number of completed competencies\n     *\n     * @return {Integer}\n     */\n    achievedCompetencies: function achievedCompetencies() {\n      return this.achievements.items.reduce(function (total, current) {\n        return current.value && current.value.proficient ? total += 1 : total;\n      }, 0);\n    },\n\n    /**\n     * Check if the criteria has been completed\n     *\n     * @return {Boolean}\n     */\n    criteriaComplete: function criteriaComplete() {\n      return this.isValid && this.numberOfRequiredCompetencies > 0 && this.achievedCompetencies >= this.numberOfRequiredCompetencies;\n    },\n\n    /**\n     * Return criteria header strings based on competency type\n     *\n     * @return {String}\n     */\n    criteriaHeading: function criteriaHeading() {\n      if (this.type === 'otherCompetency') {\n        return this.$str('achieve_proficiency_in_other_competencies', 'totara_criteria');\n      }\n\n      return this.$str('achieve_proficiency_in_child_competencies', 'totara_criteria');\n    },\n\n    /**\n     * Return no competency strings based on competency type\n     *\n     * @return {String}\n     */\n    noCompetenciesString: function noCompetenciesString() {\n      if (this.type === 'otherCompetency') {\n        return this.$str('error_no_competencies', 'criteria_othercompetency');\n      }\n\n      return this.$str('error_no_children', 'criteria_childcompetency');\n    },\n\n    /**\n     * Return int for required number of competencies completed to fulfill criteria\n     *\n     * @return {Integer}\n     */\n    numberOfRequiredCompetencies: function numberOfRequiredCompetencies() {\n      if (this.achievements.aggregation_method === 1) {\n        return this.achievements.items.length;\n      }\n\n      return this.achievements.required_items;\n    },\n\n    /**\n     * Returns true if it is possible for the achievement path to be competed. Returns false if it is not possible\n     * (e.g. an other/child competency item has a course completion not being tracked).\n     *\n     * @return {Boolean}\n     */\n    isValid: function isValid() {\n      return this.achievements.is_valid;\n    }\n  },\n  methods: {\n    /**\n     * Trigger a mutation to assign selected competency\n     *\n     */\n    assignCompetency: function assignCompetency(competency) {\n      var _this = this;\n\n      this.$apollo.mutate({\n        // Query\n        mutation: totara_competency_graphql_create_user_assignments__WEBPACK_IMPORTED_MODULE_10__[\"default\"],\n        // Parameters\n        variables: {\n          competency_ids: [competency.id],\n          user_id: this.userId\n        }\n      }).then(function (_ref) {\n        var data = _ref.data;\n\n        if (data && data.totara_competency_create_user_assignments) {\n          var result = data.totara_competency_create_user_assignments; // Due to this being a batch api designed to tolerate partial success,\n          // single assignment can silently fail, indicated by no results being returned.\n\n          if (result.length > 0) {\n            _this.$emit('self-assigned');\n          } else {\n            _this.triggerErrorNotification(_this.$str('error_competency_assignment', 'totara_criteria'));\n          }\n        }\n      })[\"catch\"](function (error) {\n        console.error(error);\n\n        _this.triggerErrorNotification(_this.$str('error_competency_assignment', 'totara_criteria'));\n      })[\"finally\"](function () {\n        return _this.closeModal();\n      });\n    },\n\n    /**\n     * Display error messages when competency assignment fails\n     *\n     */\n    triggerErrorNotification: function triggerErrorNotification(message) {\n      Object(tui_notifications__WEBPACK_IMPORTED_MODULE_9__[\"notify\"])({\n        message: message,\n        type: 'error'\n      });\n    },\n\n    /**\n     * Show assign competency modal\n     *\n     */\n    showModal: function showModal() {\n      this.modalOpen = true;\n    },\n\n    /**\n     * Close assign competency modal\n     *\n     */\n    closeModal: function closeModal() {\n      this.modalOpen = false;\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_competency/components/achievements/AchievementLayout */ \"totara_competency/components/achievements/AchievementLayout\");\n/* harmony import */ var totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/links/ActionLink */ \"tui/components/links/ActionLink\");\n/* harmony import */ var tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/datatable/Cell */ \"tui/components/datatable/Cell\");\n/* harmony import */ var tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_icons_CheckSuccess__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/icons/CheckSuccess */ \"tui/components/icons/CheckSuccess\");\n/* harmony import */ var tui_components_icons_CheckSuccess__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_CheckSuccess__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/datatable/ExpandCell */ \"tui/components/datatable/ExpandCell\");\n/* harmony import */ var tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/components/progress/Progress */ \"tui/components/progress/Progress\");\n/* harmony import */ var tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! totara_competency/components/achievements/ProgressCircle */ \"totara_competency/components/achievements/ProgressCircle\");\n/* harmony import */ var totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! tui/components/datatable/Table */ \"tui/components/datatable/Table\");\n/* harmony import */ var tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_7__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AchievementLayout: totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0___default.a,\n    ActionLink: tui_components_links_ActionLink__WEBPACK_IMPORTED_MODULE_1___default.a,\n    Cell: tui_components_datatable_Cell__WEBPACK_IMPORTED_MODULE_2___default.a,\n    CheckIcon: tui_components_icons_CheckSuccess__WEBPACK_IMPORTED_MODULE_3___default.a,\n    ExpandCell: tui_components_datatable_ExpandCell__WEBPACK_IMPORTED_MODULE_4___default.a,\n    Progress: tui_components_progress_Progress__WEBPACK_IMPORTED_MODULE_5___default.a,\n    ProgressCircle: totara_competency_components_achievements_ProgressCircle__WEBPACK_IMPORTED_MODULE_6___default.a,\n    Table: tui_components_datatable_Table__WEBPACK_IMPORTED_MODULE_7___default.a\n  },\n  props: {\n    achievements: {\n      required: true,\n      type: Object\n    },\n    displayed: Boolean\n  },\n  computed: {\n    /**\n     * Return bool for criteria fulfilled\n     *\n     * @return {Boolean}\n     */\n    criteriaFulfilled: function criteriaFulfilled() {\n      return this.isValid && this.targetNumberOfCourses > 0 && this.completedNumberOfCourses >= this.targetNumberOfCourses;\n    },\n\n    /**\n     * Return int for number of courses\n     *\n     * @return {Integer}\n     */\n    numberOfCourses: function numberOfCourses() {\n      return this.achievements.items ? this.achievements.items.length : 0;\n    },\n\n    /**\n     * Return int for number of courses completed\n     *\n     * @return {Integer}\n     */\n    completedNumberOfCourses: function completedNumberOfCourses() {\n      var complete = 0;\n\n      if (!this.numberOfCourses) {\n        return complete;\n      }\n\n      this.achievements.items.forEach(function (item) {\n        if (item.course && item.course.progress === 100) {\n          complete++;\n        }\n      });\n      return complete;\n    },\n\n    /**\n     * Return int for required number of courses completed to fulfil criteria\n     *\n     * @return {Integer}\n     */\n    targetNumberOfCourses: function targetNumberOfCourses() {\n      // If aggregation_method is set to achieve ALL courses\n      if (this.achievements.aggregation_method === 1) {\n        return this.numberOfCourses;\n      }\n\n      return this.achievements.required_items;\n    },\n\n    /**\n     * Returns true if it is possible for the achievement path to be competed. Returns false if it is not possible (e.g. course completion not being tracked).\n     *\n     * @return {Boolean}\n     */\n    isValid: function isValid() {\n      return this.achievements.is_valid;\n    }\n  },\n  methods: {\n    /**\n     * Return course name or unavailable to user string\n     *\n     * @return {String}\n     */\n    getCourseName: function getCourseName(row) {\n      return row.course ? row.course.fullname : this.$str('hidden_course', 'totara_criteria');\n    },\n\n    /**\n     * Return bool based on progress data\n     *\n     * @return {Boolean}\n     */\n    hasProgress: function hasProgress(row) {\n      return row.course && row.course.progress > 0;\n    },\n\n    /**\n     * Return bool based on course completion\n     *\n     * @return {Boolean}\n     */\n    isComplete: function isComplete(row) {\n      return row.course && row.course.progress === 100;\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=template&id=00c6bf4d&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?vue&type=template&id=00c6bf4d& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-criteriaCompetencyAchievement\"},[_c('AchievementLayout',{scopedSlots:_vm._u([{key:\"left\",fn:function(){return [_c('div',{staticClass:\"tui-criteriaCompetencyAchievement__goal\"},[_c('h5',{staticClass:\"tui-criteriaCompetencyAchievement__title\"},[_vm._v(\"\\n          \"+_vm._s(_vm.criteriaHeading)+\"\\n        \")]),_vm._v(\" \"),_c('ProgressCircle',{attrs:{\"complete\":_vm.criteriaComplete,\"completed\":_vm.criteriaComplete\n              ? _vm.numberOfRequiredCompetencies\n              : _vm.achievedCompetencies,\"target\":_vm.numberOfRequiredCompetencies}})],1)]},proxy:true},{key:\"right\",fn:function(){return [_c('Table',{attrs:{\"data\":_vm.achievements.items,\"expandable-rows\":true,\"no-items-text\":_vm.noCompetenciesString},scopedSlots:_vm._u([{key:\"row\",fn:function(ref){\n              var row = ref.row;\n              var expand = ref.expand;\n              var expandState = ref.expandState;\nreturn [_c('ExpandCell',{attrs:{\"aria-label\":row.competency.fullname,\"size\":\"1\",\"expand-state\":expandState},on:{\"click\":function($event){return expand()}}}),_vm._v(\" \"),_c('Cell',{attrs:{\"size\":\"9\",\"column-header\":_vm.$str('competencies', 'totara_criteria')}},[_vm._v(\"\\n            \"+_vm._s(row.competency.fullname)+\"\\n          \")]),_vm._v(\" \"),_c('Cell',{class:'tui-criteriaCompetencyAchievement__level',attrs:{\"size\":\"3\",\"column-header\":_vm.$str('achievement_level', 'totara_criteria')}},[(row.value)?[_vm._v(\"\\n              \"+_vm._s(row.value.name)+\"\\n            \")]:[_c('span',{staticClass:\"tui-criteriaCompetencyAchievement__level-notAvailable\"},[_vm._v(\"\\n                \"+_vm._s(_vm.$str('not_available', 'totara_criteria'))+\"\\n              \")])]],2),_vm._v(\" \"),_c('Cell',{attrs:{\"size\":\"3\",\"column-header\":_vm.$str('completion', 'totara_criteria'),\"align\":\"end\"}},[(row.value && row.value.proficient)?_c('div',{staticClass:\"tui-criteriaCompetencyAchievement__completion-complete\"},[_c('CheckIcon',{attrs:{\"size\":\"200\"}}),_vm._v(\"\\n              \"+_vm._s(_vm.$str('complete', 'totara_criteria'))+\"\\n            \")],1):_c('div',{staticClass:\"tui-criteriaCompetencyAchievement__completion-notComplete\"},[_vm._v(\"\\n              \"+_vm._s(_vm.$str('not_complete', 'totara_criteria'))+\"\\n            \")])])]}},{key:\"expand-content\",fn:function(ref){\n              var row = ref.row;\nreturn [_c('div',{staticClass:\"tui-criteriaCompetencyAchievement__summary\"},[_c('h6',{staticClass:\"tui-criteriaCompetencyAchievement__summary-header\"},[_vm._v(\"\\n              \"+_vm._s(row.competency.fullname)+\"\\n            \")]),_vm._v(\" \"),_c('div',{staticClass:\"tui-criteriaCompetencyAchievement__summary-body\",domProps:{\"innerHTML\":_vm._s(row.competency.description)}}),_vm._v(\" \"),(row.assigned)?_c('ActionLink',{class:'tui-criteriaCompetencyAchievement__summary-button',attrs:{\"href\":_vm.$url('/totara/competency/profile/details/index.php', {\n                  competency_id: row.competency.id,\n                  user_id: _vm.userId,\n                }),\"text\":_vm.$str('view_competency', 'totara_criteria'),\"styleclass\":{\n                primary: true,\n                small: true,\n              }}}):(row.self_assignable)?_c('div',[_c('Button',{class:'tui-criteriaCompetencyAchievement__summary-button',attrs:{\"text\":_vm.$str(\n                    _vm.achievements.current_user\n                      ? 'self_assign_competency'\n                      : 'assign_competency',\n                    'totara_criteria'\n                  ),\"styleclass\":{\n                  primary: true,\n                  small: true,\n                }},on:{\"click\":function($event){return _vm.showModal(row.competency)}}})],1):_vm._e()],1),_vm._v(\" \"),_c('ConfirmationModal',{attrs:{\"open\":_vm.modalOpen,\"title\":_vm.$str('confirm_assign_competency_title', 'totara_criteria')},on:{\"confirm\":function($event){return _vm.assignCompetency(row.competency)},\"cancel\":_vm.closeModal}},[_vm._v(\"\\n            \"+_vm._s(_vm.$str(\n                _vm.achievements.current_user\n                  ? 'confirm_assign_competency_body_by_self'\n                  : 'confirm_assign_competency_body_by_other',\n                'totara_criteria',\n                row.competency.fullname\n              ))+\"\\n          \")])]}}])})]},proxy:true}])})],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CompetencyAchievementDisplay.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=template&id=2b90c0ea&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?vue&type=template&id=2b90c0ea& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-criteriaCourseAchievement\"},[_c('AchievementLayout',{scopedSlots:_vm._u([{key:\"left\",fn:function(){return [_c('div',{staticClass:\"tui-criteriaCourseAchievement__goal\"},[_c('h4',{staticClass:\"tui-criteriaCourseAchievement__title\"},[_vm._v(\"\\n          \"+_vm._s(_vm.$str('complete_courses', 'totara_criteria'))+\"\\n        \")]),_vm._v(\" \"),_c('ProgressCircle',{attrs:{\"complete\":_vm.criteriaFulfilled,\"completed\":_vm.completedNumberOfCourses >= _vm.targetNumberOfCourses\n              ? _vm.targetNumberOfCourses\n              : _vm.completedNumberOfCourses,\"target\":_vm.targetNumberOfCourses}})],1)]},proxy:true},{key:\"right\",fn:function(){return [_c('Table',{attrs:{\"data\":_vm.achievements.items,\"expandable-rows\":true,\"no-items-text\":_vm.$str('no_courses', 'totara_criteria')},scopedSlots:_vm._u([{key:\"row\",fn:function(ref){\n              var row = ref.row;\n              var expand = ref.expand;\n              var expandState = ref.expandState;\nreturn [_c('ExpandCell',{attrs:{\"aria-label\":_vm.getCourseName(row),\"expand-state\":expandState},on:{\"click\":function($event){return expand()}}}),_vm._v(\" \"),_c('Cell',{attrs:{\"size\":\"9\",\"column-header\":_vm.$str('courses', 'totara_criteria')}},[_vm._v(\"\\n            \"+_vm._s(_vm.getCourseName(row))+\"\\n          \")]),_vm._v(\" \"),_c('Cell',{class:'tui-criteriaCourseAchievement__progress',attrs:{\"size\":\"3\",\"column-header\":_vm.$str('progress', 'totara_criteria')}},[(_vm.hasProgress(row))?_c('div',{staticClass:\"tui-criteriaCourseAchievement__progress-bar\"},[(_vm.displayed)?_c('Progress',{attrs:{\"value\":row.course.progress}}):_vm._e()],1):_c('div',{staticClass:\"tui-criteriaCourseAchievement__progress-empty\"},[_vm._v(\"\\n              \"+_vm._s(_vm.$str('not_available', 'totara_criteria'))+\"\\n            \")])]),_vm._v(\" \"),_c('Cell',{attrs:{\"size\":\"3\",\"column-header\":_vm.$str('completion', 'totara_criteria'),\"align\":\"end\"}},[(_vm.isComplete(row))?_c('div',{staticClass:\"tui-criteriaCourseAchievement__completion-complete\"},[_c('CheckIcon',{attrs:{\"size\":\"200\"}}),_vm._v(\"\\n              \"+_vm._s(_vm.$str('complete', 'totara_criteria'))+\"\\n            \")],1):_c('div',{staticClass:\"tui-criteriaCourseAchievement__completion-notComplete\"},[_vm._v(\"\\n              \"+_vm._s(_vm.$str('not_complete', 'totara_criteria'))+\"\\n            \")])])]}},{key:\"expand-content\",fn:function(ref){\n              var row = ref.row;\nreturn [_c('div',{staticClass:\"tui-criteriaCourseAchievement__summary\"},[_c('h6',{staticClass:\"tui-criteriaCourseAchievement__summary-header\"},[_vm._v(\"\\n              \"+_vm._s(row.course.fullname)+\"\\n            \")]),_vm._v(\" \"),_c('div',{staticClass:\"tui-criteriaCourseAchievement__summary-body\",domProps:{\"innerHTML\":_vm._s(row.course.description)}}),_vm._v(\" \"),_c('ActionLink',{class:'tui-criteriaCourseAchievement__summary-button',attrs:{\"href\":row.course.url_view,\"text\":_vm.$str('go_to_course', 'totara_criteria'),\"styleclass\":{\n                primary: true,\n                small: true,\n              }}})],1)]}}])})]},proxy:true}])})],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/totara_criteria/src/components/achievements/CourseAchievementDisplay.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js":
/*!********************************************************************!*\
  !*** ./node_modules/vue-loader/lib/runtime/componentNormalizer.js ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return normalizeComponent; });\n/* globals __VUE_SSR_CONTEXT__ */\n\n// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).\n// This module is a runtime utility for cleaner component module output and will\n// be included in the final webpack user bundle.\n\nfunction normalizeComponent (\n  scriptExports,\n  render,\n  staticRenderFns,\n  functionalTemplate,\n  injectStyles,\n  scopeId,\n  moduleIdentifier, /* server only */\n  shadowMode /* vue-cli only */\n) {\n  // Vue.extend constructor export interop\n  var options = typeof scriptExports === 'function'\n    ? scriptExports.options\n    : scriptExports\n\n  // render functions\n  if (render) {\n    options.render = render\n    options.staticRenderFns = staticRenderFns\n    options._compiled = true\n  }\n\n  // functional template\n  if (functionalTemplate) {\n    options.functional = true\n  }\n\n  // scopedId\n  if (scopeId) {\n    options._scopeId = 'data-v-' + scopeId\n  }\n\n  var hook\n  if (moduleIdentifier) { // server build\n    hook = function (context) {\n      // 2.3 injection\n      context =\n        context || // cached call\n        (this.$vnode && this.$vnode.ssrContext) || // stateful\n        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional\n      // 2.2 with runInNewContext: true\n      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {\n        context = __VUE_SSR_CONTEXT__\n      }\n      // inject component styles\n      if (injectStyles) {\n        injectStyles.call(this, context)\n      }\n      // register component module identifier for async chunk inferrence\n      if (context && context._registeredComponents) {\n        context._registeredComponents.add(moduleIdentifier)\n      }\n    }\n    // used by ssr in case component is cached and beforeCreate\n    // never gets called\n    options._ssrRegister = hook\n  } else if (injectStyles) {\n    hook = shadowMode\n      ? function () {\n        injectStyles.call(\n          this,\n          (options.functional ? this.parent : this).$root.$options.shadowRoot\n        )\n      }\n      : injectStyles\n  }\n\n  if (hook) {\n    if (options.functional) {\n      // for template-only hot-reload because in that case the render fn doesn't\n      // go through the normalizer\n      options._injectStyles = hook\n      // register for functional component in vue file\n      var originalRender = options.render\n      options.render = function renderWithStyleInjection (h, context) {\n        hook.call(context)\n        return originalRender(h, context)\n      }\n    } else {\n      // inject component registration as beforeCreate hook\n      var existing = options.beforeCreate\n      options.beforeCreate = existing\n        ? [].concat(existing, hook)\n        : [hook]\n    }\n  }\n\n  return {\n    exports: scriptExports,\n    options: options\n  }\n}\n\n\n//# sourceURL=webpack:///./node_modules/vue-loader/lib/runtime/componentNormalizer.js?");

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

/***/ "tui/components/icons/CheckSuccess":
/*!*********************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/CheckSuccess\")" ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/CheckSuccess\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/CheckSuccess\\%22)%22?");

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