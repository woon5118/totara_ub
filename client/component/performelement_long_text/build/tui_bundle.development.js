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
/******/ 	return __webpack_require__(__webpack_require__.s = "./client/component/performelement_long_text/src/tui.json");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/component/performelement_long_text/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!*****************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \*****************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./LongTextAdminEdit\": \"./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue\",\n\t\"./LongTextAdminEdit.vue\": \"./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue\",\n\t\"./LongTextAdminSummary\": \"./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue\",\n\t\"./LongTextAdminSummary.vue\": \"./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue\",\n\t\"./LongTextAdminView\": \"./client/component/performelement_long_text/src/components/LongTextAdminView.vue\",\n\t\"./LongTextAdminView.vue\": \"./client/component/performelement_long_text/src/components/LongTextAdminView.vue\",\n\t\"./LongTextParticipantForm\": \"./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue\",\n\t\"./LongTextParticipantForm.vue\": \"./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue\",\n\t\"./LongTextParticipantPrint\": \"./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue\",\n\t\"./LongTextParticipantPrint.vue\": \"./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue\",\n\t\"./WekaWrapper\": \"./client/component/performelement_long_text/src/components/WekaWrapper.js\",\n\t\"./WekaWrapper.js\": \"./client/component/performelement_long_text/src/components/WekaWrapper.js\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/performelement_long_text/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/performelement_long_text/src/components_sync_^(?:(?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue":
/*!****************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue ***!
  \****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _LongTextAdminEdit_vue_vue_type_template_id_e1e24e86___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LongTextAdminEdit.vue?vue&type=template&id=e1e24e86& */ \"./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=template&id=e1e24e86&\");\n/* harmony import */ var _LongTextAdminEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LongTextAdminEdit.vue?vue&type=script&lang=js& */ \"./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _LongTextAdminEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _LongTextAdminEdit_vue_vue_type_template_id_e1e24e86___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _LongTextAdminEdit_vue_vue_type_template_id_e1e24e86___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_long_text/src/components/LongTextAdminEdit.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextAdminEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LongTextAdminEdit.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextAdminEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=template&id=e1e24e86&":
/*!***********************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=template&id=e1e24e86& ***!
  \***********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminEdit_vue_vue_type_template_id_e1e24e86___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LongTextAdminEdit.vue?vue&type=template&id=e1e24e86& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=template&id=e1e24e86&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminEdit_vue_vue_type_template_id_e1e24e86___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminEdit_vue_vue_type_template_id_e1e24e86___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue":
/*!*******************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue ***!
  \*******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _LongTextAdminSummary_vue_vue_type_template_id_2b2f7043___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LongTextAdminSummary.vue?vue&type=template&id=2b2f7043& */ \"./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=template&id=2b2f7043&\");\n/* harmony import */ var _LongTextAdminSummary_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LongTextAdminSummary.vue?vue&type=script&lang=js& */ \"./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _LongTextAdminSummary_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _LongTextAdminSummary_vue_vue_type_template_id_2b2f7043___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _LongTextAdminSummary_vue_vue_type_template_id_2b2f7043___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_long_text/src/components/LongTextAdminSummary.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextAdminSummary_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LongTextAdminSummary.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextAdminSummary_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=template&id=2b2f7043&":
/*!**************************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=template&id=2b2f7043& ***!
  \**************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminSummary_vue_vue_type_template_id_2b2f7043___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LongTextAdminSummary.vue?vue&type=template&id=2b2f7043& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=template&id=2b2f7043&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminSummary_vue_vue_type_template_id_2b2f7043___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminSummary_vue_vue_type_template_id_2b2f7043___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextAdminView.vue":
/*!****************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextAdminView.vue ***!
  \****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _LongTextAdminView_vue_vue_type_template_id_76d8dfd8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LongTextAdminView.vue?vue&type=template&id=76d8dfd8& */ \"./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=template&id=76d8dfd8&\");\n/* harmony import */ var _LongTextAdminView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LongTextAdminView.vue?vue&type=script&lang=js& */ \"./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _LongTextAdminView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./LongTextAdminView.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _LongTextAdminView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _LongTextAdminView_vue_vue_type_template_id_76d8dfd8___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _LongTextAdminView_vue_vue_type_template_id_76d8dfd8___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_long_text/src/components/LongTextAdminView.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminView.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextAdminView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LongTextAdminView.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextAdminView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminView.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=style&index=0&lang=scss&":
/*!**************************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=style&index=0&lang=scss& ***!
  \**************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??ref--4-0!../../../../tooling/webpack/css_raw_loader.js??ref--4-1!../../../../../node_modules/postcss-loader/src??ref--4-2!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LongTextAdminView.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js?!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if([\"default\"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminView.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=template&id=76d8dfd8&":
/*!***********************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=template&id=76d8dfd8& ***!
  \***********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminView_vue_vue_type_template_id_76d8dfd8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LongTextAdminView.vue?vue&type=template&id=76d8dfd8& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=template&id=76d8dfd8&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminView_vue_vue_type_template_id_76d8dfd8___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextAdminView_vue_vue_type_template_id_76d8dfd8___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminView.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue":
/*!**********************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue ***!
  \**********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _LongTextParticipantForm_vue_vue_type_template_id_19a2215b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LongTextParticipantForm.vue?vue&type=template&id=19a2215b& */ \"./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=template&id=19a2215b&\");\n/* harmony import */ var _LongTextParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LongTextParticipantForm.vue?vue&type=script&lang=js& */ \"./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _LongTextParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _LongTextParticipantForm_vue_vue_type_template_id_19a2215b___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _LongTextParticipantForm_vue_vue_type_template_id_19a2215b___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_long_text/src/components/LongTextParticipantForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LongTextParticipantForm.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=template&id=19a2215b&":
/*!*****************************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=template&id=19a2215b& ***!
  \*****************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextParticipantForm_vue_vue_type_template_id_19a2215b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LongTextParticipantForm.vue?vue&type=template&id=19a2215b& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=template&id=19a2215b&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextParticipantForm_vue_vue_type_template_id_19a2215b___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextParticipantForm_vue_vue_type_template_id_19a2215b___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue":
/*!***********************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue ***!
  \***********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _LongTextParticipantPrint_vue_vue_type_template_id_464862a6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LongTextParticipantPrint.vue?vue&type=template&id=464862a6& */ \"./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=template&id=464862a6&\");\n/* harmony import */ var _LongTextParticipantPrint_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LongTextParticipantPrint.vue?vue&type=script&lang=js& */ \"./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _LongTextParticipantPrint_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _LongTextParticipantPrint_vue_vue_type_template_id_464862a6___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _LongTextParticipantPrint_vue_vue_type_template_id_464862a6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextParticipantPrint_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LongTextParticipantPrint.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextParticipantPrint_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=template&id=464862a6&":
/*!******************************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=template&id=464862a6& ***!
  \******************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextParticipantPrint_vue_vue_type_template_id_464862a6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./LongTextParticipantPrint.vue?vue&type=template&id=464862a6& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=template&id=464862a6&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextParticipantPrint_vue_vue_type_template_id_464862a6___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LongTextParticipantPrint_vue_vue_type_template_id_464862a6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/WekaWrapper.js":
/*!*********************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/WekaWrapper.js ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! editor_weka/WekaValue */ \"editor_weka/WekaValue\");\n/* harmony import */ var editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_0__);\n/**\n * This file is part of Totara Enterprise Extensions.\n *\n * Copyright (C) 2020 onwards Totara Learning Solutions LTD\n *\n * Totara Enterprise Extensions is provided only to Totara\n * Learning Solutions LTD's customers and partners, pursuant to\n * the terms and conditions of a separate agreement with Totara\n * Learning Solutions LTD or its affiliate.\n *\n * If you do not have an agreement with Totara Learning Solutions\n * LTD, you may not access, use, modify, or distribute this software.\n * Please contact [licensing@totaralearning.com] for more information.\n *\n * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>\n * @module performelement_long_text\n */\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  props: {\n    value: {\n      type: Object,\n      required: false,\n    },\n  },\n\n  data() {\n    return {\n      content: this.value ? editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_0___default.a.fromDoc(this.value) : editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_0___default.a.empty(),\n    };\n  },\n\n  methods: {\n    /**\n     * @param {WekaValue} value\n     */\n    update(value) {\n      if (value.isEmpty) {\n        this.$emit('update', null);\n      }\n      this.$emit('update', value.getDoc());\n    },\n  },\n\n  render() {\n    return this.$scopedSlots.default({\n      value: this.content,\n      update: this.update,\n    });\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/WekaWrapper.js?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/tui.json":
/*!****************************************************************!*\
  !*** ./client/component/performelement_long_text/src/tui.json ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"performelement_long_text\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"performelement_long_text\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"performelement_long_text\")\ntui._bundle.addModulesFromContext(\"performelement_long_text/components\", __webpack_require__(\"./client/component/performelement_long_text/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var mod_perform_components_element_PerformAdminCustomElementEdit__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! mod_perform/components/element/PerformAdminCustomElementEdit */ \"mod_perform/components/element/PerformAdminCustomElementEdit\");\n/* harmony import */ var mod_perform_components_element_PerformAdminCustomElementEdit__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_PerformAdminCustomElementEdit__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    PerformAdminCustomElementEdit: (mod_perform_components_element_PerformAdminCustomElementEdit__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n\n  inheritAttrs: false,\n\n  props: {\n    identifier: String,\n    isRequired: Boolean,\n    rawTitle: String,\n    settings: Object,\n  },\n\n  data() {\n    return {\n      initialValues: {\n        rawTitle: this.rawTitle,\n        identifier: this.identifier,\n        responseRequired: this.isRequired,\n      },\n    };\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var mod_perform_components_element_PerformAdminCustomElementSummary__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! mod_perform/components/element/PerformAdminCustomElementSummary */ \"mod_perform/components/element/PerformAdminCustomElementSummary\");\n/* harmony import */ var mod_perform_components_element_PerformAdminCustomElementSummary__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_PerformAdminCustomElementSummary__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    PerformAdminCustomElementSummary: (mod_perform_components_element_PerformAdminCustomElementSummary__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n\n  inheritAttrs: false,\n\n  props: {\n    identifier: String,\n    isRequired: Boolean,\n    settings: Object,\n    title: String,\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/Form */ \"tui/components/form/Form\");\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/FormRow */ \"tui/components/form/FormRow\");\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! editor_weka/components/Weka */ \"editor_weka/components/Weka\");\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! editor_weka/WekaValue */ \"editor_weka/WekaValue\");\n/* harmony import */ var editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_3__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Form: (tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default()),\n    FormRow: (tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1___default()),\n    Weka: (editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_2___default()),\n  },\n\n  inheritAttrs: false,\n\n  data() {\n    return {\n      emptyValue: editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_3___default.a.empty(),\n    };\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminView.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/uniform */ \"tui/components/uniform\");\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/reform/FormScope */ \"tui/components/reform/FormScope\");\n/* harmony import */ var tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! editor_weka/components/Weka */ \"editor_weka/components/Weka\");\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var performelement_long_text_components_WekaWrapper__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! performelement_long_text/components/WekaWrapper */ \"performelement_long_text/components/WekaWrapper\");\n/* harmony import */ var performelement_long_text_components_WekaWrapper__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(performelement_long_text_components_WekaWrapper__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_validation__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/validation */ \"tui/validation\");\n/* harmony import */ var tui_validation__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_validation__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var performelement_long_text_graphql_prepare_draft_area__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! performelement_long_text/graphql/prepare_draft_area */ \"./server/mod/perform/element/long_text/webapi/ajax/prepare_draft_area.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n// GraphQL queries\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    FormField: tui_components_uniform__WEBPACK_IMPORTED_MODULE_0__[\"FormField\"],\n    FormScope: (tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_1___default()),\n    Weka: (editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_2___default()),\n    WekaWrapper: (performelement_long_text_components_WekaWrapper__WEBPACK_IMPORTED_MODULE_3___default()),\n  },\n  props: {\n    path: {\n      type: [String, Array],\n      default: '',\n    },\n    error: String,\n    isDraft: Boolean,\n    element: Object,\n    isExternalParticipant: Boolean,\n    sectionElementId: {\n      type: [String, Number],\n      required: true,\n    },\n    participantInstanceId: {\n      type: [String, Number],\n      required: true,\n    },\n  },\n\n  data() {\n    return {\n      draftFileId: 0,\n    };\n  },\n\n  computed: {\n    /**\n     * Have the required queries been loaded?\n     * @return {Boolean}\n     */\n    loaded() {\n      return this.draftFileId || this.isExternalParticipant;\n    },\n\n    /**\n     * An array of validation rules for the element.\n     * The rules returned depend on if we are saving as draft or if a response is required or not.\n     *\n     * @return {(function|object)[]}\n     */\n    validations() {\n      if (!this.isDraft && this.element && this.element.is_required) {\n        return [tui_validation__WEBPACK_IMPORTED_MODULE_4__[\"v\"].required()];\n      }\n\n      return [];\n    },\n  },\n\n  async mounted() {\n    if (this.isExternalParticipant) {\n      // File upload is problematic for external participants.\n      return;\n    }\n    await this.$_loadDraftId();\n  },\n\n  methods: {\n    /**\n     * Get a Draft ID to use for temporarily storing uploading files.\n     */\n    async $_loadDraftId() {\n      const {\n        data: { draft_id: draftFileId },\n      } = await this.$apollo.mutate({\n        mutation: performelement_long_text_graphql_prepare_draft_area__WEBPACK_IMPORTED_MODULE_5__[\"default\"],\n        variables: {\n          section_element_id: this.sectionElementId,\n          participant_instance_id: this.participantInstanceId,\n        },\n      });\n      this.draftFileId = draftFileId;\n    },\n\n    /**\n     * Process the form values.\n     *\n     * @param {Object} value\n     * @return {Object|null}\n     */\n    process(value) {\n      if (!value || !value.response) {\n        return null;\n      }\n\n      return {\n        draft_id: this.draftFileId,\n        weka: value.response,\n      };\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_NotepadLines__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/NotepadLines */ \"tui/components/form/NotepadLines\");\n/* harmony import */ var tui_components_form_NotepadLines__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_NotepadLines__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    NotepadLines: (tui_components_form_NotepadLines__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n  props: {\n    data: String,\n  },\n  computed: {\n    /**\n     * Has this question been answered.\n     *\n     * @return {boolean}\n     */\n    hasBeenAnswered() {\n      return this.data && this.data.length > 0;\n    },\n  },\n\n  mounted() {\n    this.$_scan();\n  },\n\n  updated() {\n    this.$_scan();\n  },\n\n  methods: {\n    /**\n     * Required to handle Weka HTML.\n     */\n    $_scan() {\n      this.$nextTick().then(() => {\n        let content = this.$refs.content;\n        if (!content) {\n          return;\n        }\n\n        tui.scan(content);\n      });\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js?!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=style&index=0&lang=scss&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js??ref--4-0!./client/tooling/webpack/css_raw_loader.js??ref--4-1!./node_modules/postcss-loader/src??ref--4-2!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=style&index=0&lang=scss& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminView.vue?./node_modules/mini-css-extract-plugin/dist/loader.js??ref--4-0!./client/tooling/webpack/css_raw_loader.js??ref--4-1!./node_modules/postcss-loader/src??ref--4-2!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=template&id=e1e24e86&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=template&id=e1e24e86& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-longTextAdminEdit\"},[_c('PerformAdminCustomElementEdit',{attrs:{\"initial-values\":_vm.initialValues,\"settings\":_vm.settings},on:{\"cancel\":function($event){return _vm.$emit('display')},\"update\":function($event){return _vm.$emit('update', $event)}}})],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=template&id=2b2f7043&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=template&id=2b2f7043& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-longTextAdminSummary\"},[_c('PerformAdminCustomElementSummary',{attrs:{\"identifier\":_vm.identifier,\"is-required\":_vm.isRequired,\"settings\":_vm.settings,\"title\":_vm.title},on:{\"display\":function($event){return _vm.$emit('display')}}})],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=template&id=76d8dfd8&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=template&id=76d8dfd8& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-longTextAdminView\"},[_c('Form',{attrs:{\"input-width\":\"full\",\"vertical\":true}},[_c('FormRow',[_c('Weka',{attrs:{\"value\":_vm.emptyValue,\"usage-identifier\":{\n          component: 'performelement_long_text',\n          area: 'response',\n        },\"variant\":\"description\",\"file-item-id\":1}})],1)],1)],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminView.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=template&id=19a2215b&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=template&id=19a2215b& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return (_vm.loaded)?_c('FormScope',{attrs:{\"path\":_vm.path,\"process\":_vm.process}},[_c('FormField',{attrs:{\"name\":\"response\",\"validations\":_vm.validations,\"char-length\":50,\"error\":_vm.error},scopedSlots:_vm._u([{key:\"default\",fn:function(ref){\nvar formValue = ref.value;\nvar formUpdate = ref.update;\nreturn [_c('WekaWrapper',{attrs:{\"value\":formValue},on:{\"update\":formUpdate},scopedSlots:_vm._u([{key:\"default\",fn:function(ref){\nvar value = ref.value;\nvar update = ref.update;\nreturn [_c('Weka',{attrs:{\"value\":value,\"usage-identifier\":{\n          component: 'performelement_long_text',\n          area: 'response',\n          instanceId: _vm.sectionElementId,\n        },\"variant\":\"description\",\"file-item-id\":_vm.draftFileId,\"is-logged-in\":!_vm.isExternalParticipant},on:{\"input\":update}})]}}],null,true)})]}}],null,false,2896829445)})],1):_vm._e()}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=template&id=464862a6&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=template&id=464862a6& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-longTextParticipantPrint\"},[(_vm.hasBeenAnswered)?_c('div',{ref:\"content\",staticClass:\"tui-longTextParticipantPrint__wekaContent\",domProps:{\"innerHTML\":_vm._s(_vm.data)}}):_c('NotepadLines',{attrs:{\"lines\":6}})],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

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

/***/ "./server/mod/perform/element/long_text/webapi/ajax/prepare_draft_area.graphql":
/*!*************************************************************************************!*\
  !*** ./server/mod/perform/element/long_text/webapi/ajax/prepare_draft_area.graphql ***!
  \*************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"performelement_long_text_prepare_draft_area\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"section_element_id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"core_id\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"participant_instance_id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"core_id\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"draft_id\"},\"name\":{\"kind\":\"Name\",\"value\":\"performelement_long_text_prepare_draft_area\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"section_element_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"section_element_id\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"participant_instance_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"participant_instance_id\"}}}],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/mod/perform/element/long_text/webapi/ajax/prepare_draft_area.graphql?");

/***/ }),

/***/ "editor_weka/WekaValue":
/*!*********************************************************!*\
  !*** external "tui.require(\"editor_weka/WekaValue\")" ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"editor_weka/WekaValue\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22editor_weka/WekaValue\\%22)%22?");

/***/ }),

/***/ "editor_weka/components/Weka":
/*!***************************************************************!*\
  !*** external "tui.require(\"editor_weka/components/Weka\")" ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"editor_weka/components/Weka\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22editor_weka/components/Weka\\%22)%22?");

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

/***/ "performelement_long_text/components/WekaWrapper":
/*!***********************************************************************************!*\
  !*** external "tui.require(\"performelement_long_text/components/WekaWrapper\")" ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"performelement_long_text/components/WekaWrapper\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22performelement_long_text/components/WekaWrapper\\%22)%22?");

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

/***/ "tui/components/form/NotepadLines":
/*!********************************************************************!*\
  !*** external "tui.require(\"tui/components/form/NotepadLines\")" ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/NotepadLines\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/NotepadLines\\%22)%22?");

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

/***/ "tui/validation":
/*!**************************************************!*\
  !*** external "tui.require(\"tui/validation\")" ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/validation\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/validation\\%22)%22?");

/***/ })

/******/ });