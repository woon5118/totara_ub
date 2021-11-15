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
/******/ 	return __webpack_require__(__webpack_require__.s = "./client/component/performelement_multi_choice_single/src/tui.json");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/component/performelement_multi_choice_single/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!***************************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \***************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./MultiChoiceSingleAdminEdit\": \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue\",\n\t\"./MultiChoiceSingleAdminEdit.vue\": \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue\",\n\t\"./MultiChoiceSingleAdminSummary\": \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue\",\n\t\"./MultiChoiceSingleAdminSummary.vue\": \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue\",\n\t\"./MultiChoiceSingleAdminView\": \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue\",\n\t\"./MultiChoiceSingleAdminView.vue\": \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue\",\n\t\"./MultiChoiceSingleParticipantForm\": \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue\",\n\t\"./MultiChoiceSingleParticipantForm.vue\": \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/performelement_multi_choice_single/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/performelement_multi_choice_single/src/components_sync_^(?:(?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue":
/*!***********************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue ***!
  \***********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _MultiChoiceSingleAdminEdit_vue_vue_type_template_id_974cdc6c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./MultiChoiceSingleAdminEdit.vue?vue&type=template&id=974cdc6c& */ \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=template&id=974cdc6c&\");\n/* harmony import */ var _MultiChoiceSingleAdminEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./MultiChoiceSingleAdminEdit.vue?vue&type=script&lang=js& */ \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _MultiChoiceSingleAdminEdit_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./MultiChoiceSingleAdminEdit.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _MultiChoiceSingleAdminEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _MultiChoiceSingleAdminEdit_vue_vue_type_template_id_974cdc6c___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _MultiChoiceSingleAdminEdit_vue_vue_type_template_id_974cdc6c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _MultiChoiceSingleAdminEdit_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_MultiChoiceSingleAdminEdit_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**********************************************************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**********************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminEdit_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js??ref--7-0!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleAdminEdit.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminEdit_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_MultiChoiceSingleAdminEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./MultiChoiceSingleAdminEdit.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_MultiChoiceSingleAdminEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=template&id=974cdc6c&":
/*!******************************************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=template&id=974cdc6c& ***!
  \******************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminEdit_vue_vue_type_template_id_974cdc6c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleAdminEdit.vue?vue&type=template&id=974cdc6c& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=template&id=974cdc6c&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminEdit_vue_vue_type_template_id_974cdc6c___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminEdit_vue_vue_type_template_id_974cdc6c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue":
/*!**************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue ***!
  \**************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _MultiChoiceSingleAdminSummary_vue_vue_type_template_id_4477ffd4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./MultiChoiceSingleAdminSummary.vue?vue&type=template&id=4477ffd4& */ \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=template&id=4477ffd4&\");\n/* harmony import */ var _MultiChoiceSingleAdminSummary_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./MultiChoiceSingleAdminSummary.vue?vue&type=script&lang=js& */ \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _MultiChoiceSingleAdminSummary_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./MultiChoiceSingleAdminSummary.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _MultiChoiceSingleAdminSummary_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _MultiChoiceSingleAdminSummary_vue_vue_type_template_id_4477ffd4___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _MultiChoiceSingleAdminSummary_vue_vue_type_template_id_4477ffd4___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _MultiChoiceSingleAdminSummary_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_MultiChoiceSingleAdminSummary_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminSummary_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js??ref--7-0!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleAdminSummary.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminSummary_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_MultiChoiceSingleAdminSummary_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./MultiChoiceSingleAdminSummary.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_MultiChoiceSingleAdminSummary_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=template&id=4477ffd4&":
/*!*********************************************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=template&id=4477ffd4& ***!
  \*********************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminSummary_vue_vue_type_template_id_4477ffd4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleAdminSummary.vue?vue&type=template&id=4477ffd4& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=template&id=4477ffd4&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminSummary_vue_vue_type_template_id_4477ffd4___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminSummary_vue_vue_type_template_id_4477ffd4___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue":
/*!***********************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue ***!
  \***********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _MultiChoiceSingleAdminView_vue_vue_type_template_id_c7b8ce36___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./MultiChoiceSingleAdminView.vue?vue&type=template&id=c7b8ce36& */ \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?vue&type=template&id=c7b8ce36&\");\n/* harmony import */ var _MultiChoiceSingleAdminView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./MultiChoiceSingleAdminView.vue?vue&type=script&lang=js& */ \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _MultiChoiceSingleAdminView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _MultiChoiceSingleAdminView_vue_vue_type_template_id_c7b8ce36___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _MultiChoiceSingleAdminView_vue_vue_type_template_id_c7b8ce36___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_MultiChoiceSingleAdminView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./MultiChoiceSingleAdminView.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_MultiChoiceSingleAdminView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?vue&type=template&id=c7b8ce36&":
/*!******************************************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?vue&type=template&id=c7b8ce36& ***!
  \******************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminView_vue_vue_type_template_id_c7b8ce36___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleAdminView.vue?vue&type=template&id=c7b8ce36& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?vue&type=template&id=c7b8ce36&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminView_vue_vue_type_template_id_c7b8ce36___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleAdminView_vue_vue_type_template_id_c7b8ce36___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue":
/*!*****************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue ***!
  \*****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _MultiChoiceSingleParticipantForm_vue_vue_type_template_id_a021c6b0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./MultiChoiceSingleParticipantForm.vue?vue&type=template&id=a021c6b0& */ \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?vue&type=template&id=a021c6b0&\");\n/* harmony import */ var _MultiChoiceSingleParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./MultiChoiceSingleParticipantForm.vue?vue&type=script&lang=js& */ \"./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _MultiChoiceSingleParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _MultiChoiceSingleParticipantForm_vue_vue_type_template_id_a021c6b0___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _MultiChoiceSingleParticipantForm_vue_vue_type_template_id_a021c6b0___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_MultiChoiceSingleParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./MultiChoiceSingleParticipantForm.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_MultiChoiceSingleParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?vue&type=template&id=a021c6b0&":
/*!************************************************************************************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?vue&type=template&id=a021c6b0& ***!
  \************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleParticipantForm_vue_vue_type_template_id_a021c6b0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./MultiChoiceSingleParticipantForm.vue?vue&type=template&id=a021c6b0& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?vue&type=template&id=a021c6b0&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleParticipantForm_vue_vue_type_template_id_a021c6b0___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_MultiChoiceSingleParticipantForm_vue_vue_type_template_id_a021c6b0___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?");

/***/ }),

/***/ "./client/component/performelement_multi_choice_single/src/tui.json":
/*!**************************************************************************!*\
  !*** ./client/component/performelement_multi_choice_single/src/tui.json ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"performelement_multi_choice_single\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"performelement_multi_choice_single\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"performelement_multi_choice_single\")\ntui._bundle.addModulesFromContext(\"performelement_multi_choice_single/components\", __webpack_require__(\"./client/component/performelement_multi_choice_single/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"core\": [\n    \"add\"\n  ],\n  \"performelement_multi_choice_single\": [\n    \"answer_text\",\n    \"single_select_options\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"performelement_multi_choice_single\": [\n    \"options\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_icons_Add__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/icons/Add */ \"tui/components/icons/Add\");\n/* harmony import */ var tui_components_icons_Add__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Add__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/buttons/ButtonIcon */ \"tui/components/buttons/ButtonIcon\");\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_uniform_FormText__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/uniform/FormText */ \"tui/components/uniform/FormText\");\n/* harmony import */ var tui_components_uniform_FormText__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform_FormText__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var mod_perform_components_element_PerformAdminCustomElementEdit__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! mod_perform/components/element/PerformAdminCustomElementEdit */ \"mod_perform/components/element/PerformAdminCustomElementEdit\");\n/* harmony import */ var mod_perform_components_element_PerformAdminCustomElementEdit__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_PerformAdminCustomElementEdit__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/form/Repeater */ \"tui/components/form/Repeater\");\n/* harmony import */ var tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/components/uniform */ \"tui/components/uniform\");\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform__WEBPACK_IMPORTED_MODULE_5__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AddIcon: (tui_components_icons_Add__WEBPACK_IMPORTED_MODULE_0___default()),\n    ButtonIcon: (tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_1___default()),\n    FieldArray: tui_components_uniform__WEBPACK_IMPORTED_MODULE_5__[\"FieldArray\"],\n    FormRow: tui_components_uniform__WEBPACK_IMPORTED_MODULE_5__[\"FormRow\"],\n    FormText: (tui_components_uniform_FormText__WEBPACK_IMPORTED_MODULE_2___default()),\n    PerformAdminCustomElementEdit: (mod_perform_components_element_PerformAdminCustomElementEdit__WEBPACK_IMPORTED_MODULE_3___default()),\n    Repeater: (tui_components_form_Repeater__WEBPACK_IMPORTED_MODULE_4___default()),\n  },\n\n  inheritAttrs: false,\n\n  props: {\n    data: Object,\n    identifier: String,\n    isRequired: Boolean,\n    rawData: Object,\n    rawTitle: String,\n    settings: Object,\n  },\n\n  data() {\n    return {\n      initialValues: {\n        identifier: this.identifier,\n        options: [],\n        rawTitle: this.rawTitle,\n        responseRequired: this.isRequired,\n      },\n      minRows: 2,\n      ready: false,\n    };\n  },\n\n  mounted() {\n    // If no existing data\n    if (!this.rawData.options) {\n      this.initialValues.options.push(this.createField(), this.createField());\n    } else {\n      this.initialValues.options = this.rawData.options;\n    }\n\n    this.ready = 'true';\n  },\n\n  methods: {\n    /**\n     * Provide unique name for new repeater options\n     *\n     * @returns {Object}\n     */\n    createField() {\n      const randomInt = Math.floor(Math.random() * Math.floor(10000000));\n      return { name: 'option_' + randomInt, value: undefined };\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var mod_perform_components_element_PerformAdminCustomElementSummary__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! mod_perform/components/element/PerformAdminCustomElementSummary */ \"mod_perform/components/element/PerformAdminCustomElementSummary\");\n/* harmony import */ var mod_perform_components_element_PerformAdminCustomElementSummary__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_PerformAdminCustomElementSummary__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    PerformAdminCustomElementSummary: (mod_perform_components_element_PerformAdminCustomElementSummary__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n\n  inheritAttrs: false,\n\n  props: {\n    data: Object,\n    identifier: String,\n    isRequired: Boolean,\n    settings: Object,\n    title: String,\n    type: Object,\n  },\n\n  data() {\n    return {\n      extraFields: [],\n    };\n  },\n\n  mounted() {\n    let options = [];\n    this.data.options.forEach(option => {\n      options.push({\n        value: option.value,\n      });\n    });\n\n    this.extraFields = [\n      {\n        title: this.$str('options', 'performelement_multi_choice_single'),\n        options: options,\n      },\n    ];\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/Form */ \"tui/components/form/Form\");\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/FormRow */ \"tui/components/form/FormRow\");\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/form/Radio */ \"tui/components/form/Radio\");\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/form/RadioGroup */ \"tui/components/form/RadioGroup\");\n/* harmony import */ var tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_3__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Form: (tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default()),\n    FormRow: (tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1___default()),\n    Radio: (tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_2___default()),\n    RadioGroup: (tui_components_form_RadioGroup__WEBPACK_IMPORTED_MODULE_3___default()),\n  },\n\n  inheritAttrs: false,\n\n  props: {\n    data: Object,\n    title: String,\n  },\n\n  data() {\n    return {\n      tempVal: false,\n    };\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/reform/FormScope */ \"tui/components/reform/FormScope\");\n/* harmony import */ var tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/Radio */ \"tui/components/form/Radio\");\n/* harmony import */ var tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_uniform_FormRadioGroup__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/uniform/FormRadioGroup */ \"tui/components/uniform/FormRadioGroup\");\n/* harmony import */ var tui_components_uniform_FormRadioGroup__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform_FormRadioGroup__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_validation__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/validation */ \"tui/validation\");\n/* harmony import */ var tui_validation__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_validation__WEBPACK_IMPORTED_MODULE_3__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    FormScope: (tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_0___default()),\n    Radio: (tui_components_form_Radio__WEBPACK_IMPORTED_MODULE_1___default()),\n    FormRadioGroup: (tui_components_uniform_FormRadioGroup__WEBPACK_IMPORTED_MODULE_2___default()),\n  },\n\n  props: {\n    path: [String, Array],\n    error: String,\n    isDraft: Boolean,\n    element: Object,\n  },\n  computed: {\n    /**\n     * An array of validation rules for the element.\n     * The rules returned depend on if we are saving as draft or if a response is required or not.\n     *\n     * @return {(function|object)[]}\n     */\n    validations() {\n      if (!this.isDraft && this.element && this.element.is_required) {\n        return [tui_validation__WEBPACK_IMPORTED_MODULE_3__[\"v\"].required()];\n      }\n\n      return [];\n    },\n  },\n  methods: {\n    /**\n     * Process the form values.\n     *\n     * @param value\n     * @return {null|string}\n     */\n    process(value) {\n      if (!value || !value.response) {\n        return null;\n      }\n\n      return value.response;\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=template&id=974cdc6c&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?vue&type=template&id=974cdc6c& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-multiChoiceSingleAdminEdit\"},[(_vm.ready)?_c('PerformAdminCustomElementEdit',{attrs:{\"initial-values\":_vm.initialValues,\"settings\":_vm.settings},on:{\"cancel\":function($event){return _vm.$emit('display')},\"update\":function($event){return _vm.$emit('update', $event)}}},[_c('FormRow',{attrs:{\"label\":_vm.$str('single_select_options', 'performelement_multi_choice_single'),\"required\":true},scopedSlots:_vm._u([{key:\"default\",fn:function(ref){\nvar labelId = ref.labelId;\nreturn [_c('FieldArray',{attrs:{\"path\":\"options\"},scopedSlots:_vm._u([{key:\"default\",fn:function(ref){\nvar items = ref.items;\nvar push = ref.push;\nvar remove = ref.remove;\nreturn [_c('Repeater',{attrs:{\"rows\":items,\"min-rows\":_vm.minRows,\"delete-icon\":true,\"allow-deleting-first-items\":false,\"aria-labelledby\":labelId},on:{\"add\":function($event){return push()},\"remove\":function (item, i) { return remove(i); }},scopedSlots:_vm._u([{key:\"default\",fn:function(ref){\nvar index = ref.index;\nreturn [_c('div',{staticClass:\"tui-multiChoiceSingleAdminEdit__option\"},[_c('FormText',{attrs:{\"name\":[index, 'value'],\"validations\":function (v) { return [v.required()]; },\"aria-label\":_vm.$str(\n                    'answer_text',\n                    'performelement_multi_choice_single',\n                    index + 1\n                  )}})],1)]}},{key:\"add\",fn:function(){return [_c('ButtonIcon',{staticClass:\"tui-multiChoiceSingleAdminEdit__addOption\",attrs:{\"aria-label\":_vm.$str('add', 'core'),\"styleclass\":{ small: true }},on:{\"click\":function($event){push(_vm.createField())}}},[_c('AddIcon')],1)]},proxy:true}],null,true)})]}}],null,true)})]}}],null,false,1560917256)})],1):_vm._e()],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminEdit.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=template&id=4477ffd4&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?vue&type=template&id=4477ffd4& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-multiChoiceSingleAdminSummary\"},[_c('PerformAdminCustomElementSummary',{attrs:{\"extra-fields\":_vm.extraFields,\"identifier\":_vm.identifier,\"is-required\":_vm.isRequired,\"settings\":_vm.settings,\"title\":_vm.title},on:{\"display\":function($event){return _vm.$emit('display')}}})],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminSummary.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?vue&type=template&id=c7b8ce36&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?vue&type=template&id=c7b8ce36& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-multiChoiceSingleAdminView\"},[_c('Form',{attrs:{\"input-width\":\"full\",\"vertical\":true}},[_c('FormRow',[_c('RadioGroup',{attrs:{\"aria-label\":_vm.title},model:{value:(_vm.tempVal),callback:function ($$v) {_vm.tempVal=$$v},expression:\"tempVal\"}},_vm._l((_vm.data.options),function(item){return _c('Radio',{key:item.name,attrs:{\"name\":item.name,\"value\":item.value}},[_vm._v(\"\\n          \"+_vm._s(item.value)+\"\\n        \")])}),1)],1)],1)],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleAdminView.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?vue&type=template&id=a021c6b0&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?vue&type=template&id=a021c6b0& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('FormScope',{attrs:{\"path\":_vm.path,\"process\":_vm.process}},[_c('FormRadioGroup',{staticClass:\"tui-multiChoiceSingleParticipantForm\",attrs:{\"validations\":_vm.validations,\"name\":\"response\"}},_vm._l((_vm.element.data.options),function(item){return _c('Radio',{key:item.name,attrs:{\"value\":item.name}},[_vm._v(\"\\n      \"+_vm._s(item.value)+\"\\n    \")])}),1)],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/performelement_multi_choice_single/src/components/MultiChoiceSingleParticipantForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

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

/***/ "mod_perform/components/element/PerformAdminCustomElementEdit":
/*!************************************************************************************************!*\
  !*** external "tui.require(\"mod_perform/components/element/PerformAdminCustomElementEdit\")" ***!
  \************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"mod_perform/components/element/PerformAdminCustomElementEdit\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22mod_perform/components/element/PerformAdminCustomElementEdit\\%22)%22?");

/***/ }),

/***/ "mod_perform/components/element/PerformAdminCustomElementSummary":
/*!***************************************************************************************************!*\
  !*** external "tui.require(\"mod_perform/components/element/PerformAdminCustomElementSummary\")" ***!
  \***************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"mod_perform/components/element/PerformAdminCustomElementSummary\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22mod_perform/components/element/PerformAdminCustomElementSummary\\%22)%22?");

/***/ }),

/***/ "tui/components/buttons/ButtonIcon":
/*!*********************************************************************!*\
  !*** external "tui.require(\"tui/components/buttons/ButtonIcon\")" ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/buttons/ButtonIcon\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/buttons/ButtonIcon\\%22)%22?");

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

/***/ "tui/components/icons/Add":
/*!************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Add\")" ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Add\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Add\\%22)%22?");

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

/***/ }),

/***/ "tui/validation":
/*!**************************************************!*\
  !*** external "tui.require(\"tui/validation\")" ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/validation\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/validation\\%22)%22?");

/***/ })

/******/ });