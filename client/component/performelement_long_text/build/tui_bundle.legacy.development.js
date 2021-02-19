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
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextAdminEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LongTextAdminEdit.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextAdminEdit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?");

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
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextAdminSummary_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LongTextAdminSummary.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextAdminSummary_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?");

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
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _LongTextAdminView_vue_vue_type_template_id_76d8dfd8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LongTextAdminView.vue?vue&type=template&id=76d8dfd8& */ \"./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=template&id=76d8dfd8&\");\n/* harmony import */ var _LongTextAdminView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LongTextAdminView.vue?vue&type=script&lang=js& */ \"./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _LongTextAdminView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./LongTextAdminView.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _LongTextAdminView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_LongTextAdminView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _LongTextAdminView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _LongTextAdminView_vue_vue_type_template_id_76d8dfd8___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _LongTextAdminView_vue_vue_type_template_id_76d8dfd8___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/performelement_long_text/src/components/LongTextAdminView.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminView.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextAdminView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LongTextAdminView.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextAdminView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminView.vue?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=style&index=0&lang=scss&":
/*!**************************************************************************************************************************!*\
  !*** ./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=style&index=0&lang=scss& ***!
  \**************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminView.vue?");

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
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LongTextParticipantForm.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextParticipantForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?");

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
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextParticipantPrint_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LongTextParticipantPrint.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LongTextParticipantPrint_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?");

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
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! editor_weka/WekaValue */ \"editor_weka/WekaValue\");\n/* harmony import */ var editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_0__);\n/**\n * This file is part of Totara Enterprise Extensions.\n *\n * Copyright (C) 2020 onwards Totara Learning Solutions LTD\n *\n * Totara Enterprise Extensions is provided only to Totara\n * Learning Solutions LTD's customers and partners, pursuant to\n * the terms and conditions of a separate agreement with Totara\n * Learning Solutions LTD or its affiliate.\n *\n * If you do not have an agreement with Totara Learning Solutions\n * LTD, you may not access, use, modify, or distribute this software.\n * Please contact [licensing@totaralearning.com] for more information.\n *\n * @author Mark Metcalfe <mark.metcalfe@totaralearning.com>\n * @module performelement_long_text\n */\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  props: {\n    value: {\n      type: Object,\n      required: false\n    }\n  },\n  data: function data() {\n    return {\n      content: this.value ? editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_0___default.a.fromDoc(this.value) : editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_0___default.a.empty()\n    };\n  },\n  methods: {\n    /**\n     * @param {WekaValue} value\n     */\n    update: function update(value) {\n      if (value.isEmpty) {\n        this.$emit('update', null);\n      }\n\n      this.$emit('update', value.getDoc());\n    }\n  },\n  render: function render() {\n    return this.$scopedSlots[\"default\"]({\n      value: this.content,\n      update: this.update\n    });\n  }\n});\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/WekaWrapper.js?");

/***/ }),

/***/ "./client/component/performelement_long_text/src/tui.json":
/*!****************************************************************!*\
  !*** ./client/component/performelement_long_text/src/tui.json ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"performelement_long_text\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"performelement_long_text\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"performelement_long_text\")\ntui._bundle.addModulesFromContext(\"performelement_long_text/components\", __webpack_require__(\"./client/component/performelement_long_text/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/tui.json?");

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/asyncToGenerator.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/asyncToGenerator.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {\n  try {\n    var info = gen[key](arg);\n    var value = info.value;\n  } catch (error) {\n    reject(error);\n    return;\n  }\n\n  if (info.done) {\n    resolve(value);\n  } else {\n    Promise.resolve(value).then(_next, _throw);\n  }\n}\n\nfunction _asyncToGenerator(fn) {\n  return function () {\n    var self = this,\n        args = arguments;\n    return new Promise(function (resolve, reject) {\n      var gen = fn.apply(self, args);\n\n      function _next(value) {\n        asyncGeneratorStep(gen, resolve, reject, _next, _throw, \"next\", value);\n      }\n\n      function _throw(err) {\n        asyncGeneratorStep(gen, resolve, reject, _next, _throw, \"throw\", err);\n      }\n\n      _next(undefined);\n    });\n  };\n}\n\nmodule.exports = _asyncToGenerator;\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/helpers/asyncToGenerator.js?");

/***/ }),

/***/ "./node_modules/@babel/runtime/regenerator/index.js":
/*!**********************************************************!*\
  !*** ./node_modules/@babel/runtime/regenerator/index.js ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__(/*! regenerator-runtime */ \"./node_modules/regenerator-runtime/runtime.js\");\n\n\n//# sourceURL=webpack:///./node_modules/@babel/runtime/regenerator/index.js?");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var mod_perform_components_element_PerformAdminCustomElementEdit__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! mod_perform/components/element/PerformAdminCustomElementEdit */ \"mod_perform/components/element/PerformAdminCustomElementEdit\");\n/* harmony import */ var mod_perform_components_element_PerformAdminCustomElementEdit__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_PerformAdminCustomElementEdit__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    PerformAdminCustomElementEdit: mod_perform_components_element_PerformAdminCustomElementEdit__WEBPACK_IMPORTED_MODULE_0___default.a\n  },\n  inheritAttrs: false,\n  props: {\n    identifier: String,\n    isRequired: Boolean,\n    rawTitle: String,\n    settings: Object\n  },\n  data: function data() {\n    return {\n      initialValues: {\n        rawTitle: this.rawTitle,\n        identifier: this.identifier,\n        responseRequired: this.isRequired\n      }\n    };\n  }\n});\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminEdit.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var mod_perform_components_element_PerformAdminCustomElementSummary__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! mod_perform/components/element/PerformAdminCustomElementSummary */ \"mod_perform/components/element/PerformAdminCustomElementSummary\");\n/* harmony import */ var mod_perform_components_element_PerformAdminCustomElementSummary__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(mod_perform_components_element_PerformAdminCustomElementSummary__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    PerformAdminCustomElementSummary: mod_perform_components_element_PerformAdminCustomElementSummary__WEBPACK_IMPORTED_MODULE_0___default.a\n  },\n  inheritAttrs: false,\n  props: {\n    identifier: String,\n    isRequired: Boolean,\n    settings: Object,\n    title: String\n  }\n});\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminSummary.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_long_text/src/components/LongTextAdminView.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/Form */ \"tui/components/form/Form\");\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/FormRow */ \"tui/components/form/FormRow\");\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! editor_weka/components/Weka */ \"editor_weka/components/Weka\");\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! editor_weka/WekaValue */ \"editor_weka/WekaValue\");\n/* harmony import */ var editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_3__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Form: tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default.a,\n    FormRow: tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_1___default.a,\n    Weka: editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_2___default.a\n  },\n  inheritAttrs: false,\n  data: function data() {\n    return {\n      emptyValue: editor_weka_WekaValue__WEBPACK_IMPORTED_MODULE_3___default.a.empty()\n    };\n  }\n});\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextAdminView.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ \"./node_modules/@babel/runtime/regenerator/index.js\");\n/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ \"./node_modules/@babel/runtime/helpers/asyncToGenerator.js\");\n/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/uniform */ \"tui/components/uniform\");\n/* harmony import */ var tui_components_uniform__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_uniform__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/reform/FormScope */ \"tui/components/reform/FormScope\");\n/* harmony import */ var tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! editor_weka/components/Weka */ \"editor_weka/components/Weka\");\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var performelement_long_text_components_WekaWrapper__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! performelement_long_text/components/WekaWrapper */ \"performelement_long_text/components/WekaWrapper\");\n/* harmony import */ var performelement_long_text_components_WekaWrapper__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(performelement_long_text_components_WekaWrapper__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_validation__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/validation */ \"tui/validation\");\n/* harmony import */ var tui_validation__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_validation__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var performelement_long_text_graphql_prepare_draft_area__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! performelement_long_text/graphql/prepare_draft_area */ \"./server/mod/perform/element/long_text/webapi/ajax/prepare_draft_area.graphql\");\n\n\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n // GraphQL queries\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    FormField: tui_components_uniform__WEBPACK_IMPORTED_MODULE_2__[\"FormField\"],\n    FormScope: tui_components_reform_FormScope__WEBPACK_IMPORTED_MODULE_3___default.a,\n    Weka: editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_4___default.a,\n    WekaWrapper: performelement_long_text_components_WekaWrapper__WEBPACK_IMPORTED_MODULE_5___default.a\n  },\n  props: {\n    path: {\n      type: [String, Array],\n      \"default\": ''\n    },\n    error: String,\n    isDraft: Boolean,\n    element: Object,\n    isExternalParticipant: Boolean,\n    sectionElementId: {\n      type: [String, Number],\n      required: true\n    },\n    participantInstanceId: {\n      type: [String, Number],\n      required: true\n    }\n  },\n  data: function data() {\n    return {\n      draftFileId: 0\n    };\n  },\n  computed: {\n    /**\n     * Have the required queries been loaded?\n     * @return {Boolean}\n     */\n    loaded: function loaded() {\n      return this.draftFileId || this.isExternalParticipant;\n    },\n\n    /**\n     * An array of validation rules for the element.\n     * The rules returned depend on if we are saving as draft or if a response is required or not.\n     *\n     * @return {(function|object)[]}\n     */\n    validations: function validations() {\n      if (!this.isDraft && this.element && this.element.is_required) {\n        return [tui_validation__WEBPACK_IMPORTED_MODULE_6__[\"v\"].required()];\n      }\n\n      return [];\n    }\n  },\n  mounted: function mounted() {\n    var _this = this;\n\n    return _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default()( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {\n      return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {\n        while (1) {\n          switch (_context.prev = _context.next) {\n            case 0:\n              if (!_this.isExternalParticipant) {\n                _context.next = 2;\n                break;\n              }\n\n              return _context.abrupt(\"return\");\n\n            case 2:\n              _context.next = 4;\n              return _this.$_loadDraftId();\n\n            case 4:\n            case \"end\":\n              return _context.stop();\n          }\n        }\n      }, _callee);\n    }))();\n  },\n  methods: {\n    /**\n     * Get a Draft ID to use for temporarily storing uploading files.\n     */\n    $_loadDraftId: function $_loadDraftId() {\n      var _this2 = this;\n\n      return _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default()( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee2() {\n        var _yield$_this2$$apollo, draftFileId;\n\n        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee2$(_context2) {\n          while (1) {\n            switch (_context2.prev = _context2.next) {\n              case 0:\n                _context2.next = 2;\n                return _this2.$apollo.mutate({\n                  mutation: performelement_long_text_graphql_prepare_draft_area__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n                  variables: {\n                    section_element_id: _this2.sectionElementId,\n                    participant_instance_id: _this2.participantInstanceId\n                  }\n                });\n\n              case 2:\n                _yield$_this2$$apollo = _context2.sent;\n                draftFileId = _yield$_this2$$apollo.data.draft_id;\n                _this2.draftFileId = draftFileId;\n\n              case 5:\n              case \"end\":\n                return _context2.stop();\n            }\n          }\n        }, _callee2);\n      }))();\n    },\n\n    /**\n     * Process the form values.\n     *\n     * @param {Object} value\n     * @return {Object|null}\n     */\n    process: function process(value) {\n      if (!value || !value.response) {\n        return null;\n      }\n\n      return {\n        draft_id: this.draftFileId,\n        weka: value.response\n      };\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantForm.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_NotepadLines__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/NotepadLines */ \"tui/components/form/NotepadLines\");\n/* harmony import */ var tui_components_form_NotepadLines__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_NotepadLines__WEBPACK_IMPORTED_MODULE_0__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    NotepadLines: tui_components_form_NotepadLines__WEBPACK_IMPORTED_MODULE_0___default.a\n  },\n  props: {\n    data: String\n  },\n  computed: {\n    /**\n     * Has this question been answered.\n     *\n     * @return {boolean}\n     */\n    hasBeenAnswered: function hasBeenAnswered() {\n      return this.data && this.data.length > 0;\n    }\n  },\n  mounted: function mounted() {\n    this.$_scan();\n  },\n  updated: function updated() {\n    this.$_scan();\n  },\n  methods: {\n    /**\n     * Required to handle Weka HTML.\n     */\n    $_scan: function $_scan() {\n      var _this = this;\n\n      this.$nextTick().then(function () {\n        var content = _this.$refs.content;\n\n        if (!content) {\n          return;\n        }\n\n        tui.scan(content);\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/performelement_long_text/src/components/LongTextParticipantPrint.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./node_modules/regenerator-runtime/runtime.js":
/*!*****************************************************!*\
  !*** ./node_modules/regenerator-runtime/runtime.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("/**\n * Copyright (c) 2014-present, Facebook, Inc.\n *\n * This source code is licensed under the MIT license found in the\n * LICENSE file in the root directory of this source tree.\n */\n\nvar runtime = (function (exports) {\n  \"use strict\";\n\n  var Op = Object.prototype;\n  var hasOwn = Op.hasOwnProperty;\n  var undefined; // More compressible than void 0.\n  var $Symbol = typeof Symbol === \"function\" ? Symbol : {};\n  var iteratorSymbol = $Symbol.iterator || \"@@iterator\";\n  var asyncIteratorSymbol = $Symbol.asyncIterator || \"@@asyncIterator\";\n  var toStringTagSymbol = $Symbol.toStringTag || \"@@toStringTag\";\n\n  function wrap(innerFn, outerFn, self, tryLocsList) {\n    // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.\n    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;\n    var generator = Object.create(protoGenerator.prototype);\n    var context = new Context(tryLocsList || []);\n\n    // The ._invoke method unifies the implementations of the .next,\n    // .throw, and .return methods.\n    generator._invoke = makeInvokeMethod(innerFn, self, context);\n\n    return generator;\n  }\n  exports.wrap = wrap;\n\n  // Try/catch helper to minimize deoptimizations. Returns a completion\n  // record like context.tryEntries[i].completion. This interface could\n  // have been (and was previously) designed to take a closure to be\n  // invoked without arguments, but in all the cases we care about we\n  // already have an existing method we want to call, so there's no need\n  // to create a new function object. We can even get away with assuming\n  // the method takes exactly one argument, since that happens to be true\n  // in every case, so we don't have to touch the arguments object. The\n  // only additional allocation required is the completion record, which\n  // has a stable shape and so hopefully should be cheap to allocate.\n  function tryCatch(fn, obj, arg) {\n    try {\n      return { type: \"normal\", arg: fn.call(obj, arg) };\n    } catch (err) {\n      return { type: \"throw\", arg: err };\n    }\n  }\n\n  var GenStateSuspendedStart = \"suspendedStart\";\n  var GenStateSuspendedYield = \"suspendedYield\";\n  var GenStateExecuting = \"executing\";\n  var GenStateCompleted = \"completed\";\n\n  // Returning this object from the innerFn has the same effect as\n  // breaking out of the dispatch switch statement.\n  var ContinueSentinel = {};\n\n  // Dummy constructor functions that we use as the .constructor and\n  // .constructor.prototype properties for functions that return Generator\n  // objects. For full spec compliance, you may wish to configure your\n  // minifier not to mangle the names of these two functions.\n  function Generator() {}\n  function GeneratorFunction() {}\n  function GeneratorFunctionPrototype() {}\n\n  // This is a polyfill for %IteratorPrototype% for environments that\n  // don't natively support it.\n  var IteratorPrototype = {};\n  IteratorPrototype[iteratorSymbol] = function () {\n    return this;\n  };\n\n  var getProto = Object.getPrototypeOf;\n  var NativeIteratorPrototype = getProto && getProto(getProto(values([])));\n  if (NativeIteratorPrototype &&\n      NativeIteratorPrototype !== Op &&\n      hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) {\n    // This environment has a native %IteratorPrototype%; use it instead\n    // of the polyfill.\n    IteratorPrototype = NativeIteratorPrototype;\n  }\n\n  var Gp = GeneratorFunctionPrototype.prototype =\n    Generator.prototype = Object.create(IteratorPrototype);\n  GeneratorFunction.prototype = Gp.constructor = GeneratorFunctionPrototype;\n  GeneratorFunctionPrototype.constructor = GeneratorFunction;\n  GeneratorFunctionPrototype[toStringTagSymbol] =\n    GeneratorFunction.displayName = \"GeneratorFunction\";\n\n  // Helper for defining the .next, .throw, and .return methods of the\n  // Iterator interface in terms of a single ._invoke method.\n  function defineIteratorMethods(prototype) {\n    [\"next\", \"throw\", \"return\"].forEach(function(method) {\n      prototype[method] = function(arg) {\n        return this._invoke(method, arg);\n      };\n    });\n  }\n\n  exports.isGeneratorFunction = function(genFun) {\n    var ctor = typeof genFun === \"function\" && genFun.constructor;\n    return ctor\n      ? ctor === GeneratorFunction ||\n        // For the native GeneratorFunction constructor, the best we can\n        // do is to check its .name property.\n        (ctor.displayName || ctor.name) === \"GeneratorFunction\"\n      : false;\n  };\n\n  exports.mark = function(genFun) {\n    if (Object.setPrototypeOf) {\n      Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);\n    } else {\n      genFun.__proto__ = GeneratorFunctionPrototype;\n      if (!(toStringTagSymbol in genFun)) {\n        genFun[toStringTagSymbol] = \"GeneratorFunction\";\n      }\n    }\n    genFun.prototype = Object.create(Gp);\n    return genFun;\n  };\n\n  // Within the body of any async function, `await x` is transformed to\n  // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test\n  // `hasOwn.call(value, \"__await\")` to determine if the yielded value is\n  // meant to be awaited.\n  exports.awrap = function(arg) {\n    return { __await: arg };\n  };\n\n  function AsyncIterator(generator, PromiseImpl) {\n    function invoke(method, arg, resolve, reject) {\n      var record = tryCatch(generator[method], generator, arg);\n      if (record.type === \"throw\") {\n        reject(record.arg);\n      } else {\n        var result = record.arg;\n        var value = result.value;\n        if (value &&\n            typeof value === \"object\" &&\n            hasOwn.call(value, \"__await\")) {\n          return PromiseImpl.resolve(value.__await).then(function(value) {\n            invoke(\"next\", value, resolve, reject);\n          }, function(err) {\n            invoke(\"throw\", err, resolve, reject);\n          });\n        }\n\n        return PromiseImpl.resolve(value).then(function(unwrapped) {\n          // When a yielded Promise is resolved, its final value becomes\n          // the .value of the Promise<{value,done}> result for the\n          // current iteration.\n          result.value = unwrapped;\n          resolve(result);\n        }, function(error) {\n          // If a rejected Promise was yielded, throw the rejection back\n          // into the async generator function so it can be handled there.\n          return invoke(\"throw\", error, resolve, reject);\n        });\n      }\n    }\n\n    var previousPromise;\n\n    function enqueue(method, arg) {\n      function callInvokeWithMethodAndArg() {\n        return new PromiseImpl(function(resolve, reject) {\n          invoke(method, arg, resolve, reject);\n        });\n      }\n\n      return previousPromise =\n        // If enqueue has been called before, then we want to wait until\n        // all previous Promises have been resolved before calling invoke,\n        // so that results are always delivered in the correct order. If\n        // enqueue has not been called before, then it is important to\n        // call invoke immediately, without waiting on a callback to fire,\n        // so that the async generator function has the opportunity to do\n        // any necessary setup in a predictable way. This predictability\n        // is why the Promise constructor synchronously invokes its\n        // executor callback, and why async functions synchronously\n        // execute code before the first await. Since we implement simple\n        // async functions in terms of async generators, it is especially\n        // important to get this right, even though it requires care.\n        previousPromise ? previousPromise.then(\n          callInvokeWithMethodAndArg,\n          // Avoid propagating failures to Promises returned by later\n          // invocations of the iterator.\n          callInvokeWithMethodAndArg\n        ) : callInvokeWithMethodAndArg();\n    }\n\n    // Define the unified helper method that is used to implement .next,\n    // .throw, and .return (see defineIteratorMethods).\n    this._invoke = enqueue;\n  }\n\n  defineIteratorMethods(AsyncIterator.prototype);\n  AsyncIterator.prototype[asyncIteratorSymbol] = function () {\n    return this;\n  };\n  exports.AsyncIterator = AsyncIterator;\n\n  // Note that simple async functions are implemented on top of\n  // AsyncIterator objects; they just return a Promise for the value of\n  // the final result produced by the iterator.\n  exports.async = function(innerFn, outerFn, self, tryLocsList, PromiseImpl) {\n    if (PromiseImpl === void 0) PromiseImpl = Promise;\n\n    var iter = new AsyncIterator(\n      wrap(innerFn, outerFn, self, tryLocsList),\n      PromiseImpl\n    );\n\n    return exports.isGeneratorFunction(outerFn)\n      ? iter // If outerFn is a generator, return the full iterator.\n      : iter.next().then(function(result) {\n          return result.done ? result.value : iter.next();\n        });\n  };\n\n  function makeInvokeMethod(innerFn, self, context) {\n    var state = GenStateSuspendedStart;\n\n    return function invoke(method, arg) {\n      if (state === GenStateExecuting) {\n        throw new Error(\"Generator is already running\");\n      }\n\n      if (state === GenStateCompleted) {\n        if (method === \"throw\") {\n          throw arg;\n        }\n\n        // Be forgiving, per 25.3.3.3.3 of the spec:\n        // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume\n        return doneResult();\n      }\n\n      context.method = method;\n      context.arg = arg;\n\n      while (true) {\n        var delegate = context.delegate;\n        if (delegate) {\n          var delegateResult = maybeInvokeDelegate(delegate, context);\n          if (delegateResult) {\n            if (delegateResult === ContinueSentinel) continue;\n            return delegateResult;\n          }\n        }\n\n        if (context.method === \"next\") {\n          // Setting context._sent for legacy support of Babel's\n          // function.sent implementation.\n          context.sent = context._sent = context.arg;\n\n        } else if (context.method === \"throw\") {\n          if (state === GenStateSuspendedStart) {\n            state = GenStateCompleted;\n            throw context.arg;\n          }\n\n          context.dispatchException(context.arg);\n\n        } else if (context.method === \"return\") {\n          context.abrupt(\"return\", context.arg);\n        }\n\n        state = GenStateExecuting;\n\n        var record = tryCatch(innerFn, self, context);\n        if (record.type === \"normal\") {\n          // If an exception is thrown from innerFn, we leave state ===\n          // GenStateExecuting and loop back for another invocation.\n          state = context.done\n            ? GenStateCompleted\n            : GenStateSuspendedYield;\n\n          if (record.arg === ContinueSentinel) {\n            continue;\n          }\n\n          return {\n            value: record.arg,\n            done: context.done\n          };\n\n        } else if (record.type === \"throw\") {\n          state = GenStateCompleted;\n          // Dispatch the exception by looping back around to the\n          // context.dispatchException(context.arg) call above.\n          context.method = \"throw\";\n          context.arg = record.arg;\n        }\n      }\n    };\n  }\n\n  // Call delegate.iterator[context.method](context.arg) and handle the\n  // result, either by returning a { value, done } result from the\n  // delegate iterator, or by modifying context.method and context.arg,\n  // setting context.delegate to null, and returning the ContinueSentinel.\n  function maybeInvokeDelegate(delegate, context) {\n    var method = delegate.iterator[context.method];\n    if (method === undefined) {\n      // A .throw or .return when the delegate iterator has no .throw\n      // method always terminates the yield* loop.\n      context.delegate = null;\n\n      if (context.method === \"throw\") {\n        // Note: [\"return\"] must be used for ES3 parsing compatibility.\n        if (delegate.iterator[\"return\"]) {\n          // If the delegate iterator has a return method, give it a\n          // chance to clean up.\n          context.method = \"return\";\n          context.arg = undefined;\n          maybeInvokeDelegate(delegate, context);\n\n          if (context.method === \"throw\") {\n            // If maybeInvokeDelegate(context) changed context.method from\n            // \"return\" to \"throw\", let that override the TypeError below.\n            return ContinueSentinel;\n          }\n        }\n\n        context.method = \"throw\";\n        context.arg = new TypeError(\n          \"The iterator does not provide a 'throw' method\");\n      }\n\n      return ContinueSentinel;\n    }\n\n    var record = tryCatch(method, delegate.iterator, context.arg);\n\n    if (record.type === \"throw\") {\n      context.method = \"throw\";\n      context.arg = record.arg;\n      context.delegate = null;\n      return ContinueSentinel;\n    }\n\n    var info = record.arg;\n\n    if (! info) {\n      context.method = \"throw\";\n      context.arg = new TypeError(\"iterator result is not an object\");\n      context.delegate = null;\n      return ContinueSentinel;\n    }\n\n    if (info.done) {\n      // Assign the result of the finished delegate to the temporary\n      // variable specified by delegate.resultName (see delegateYield).\n      context[delegate.resultName] = info.value;\n\n      // Resume execution at the desired location (see delegateYield).\n      context.next = delegate.nextLoc;\n\n      // If context.method was \"throw\" but the delegate handled the\n      // exception, let the outer generator proceed normally. If\n      // context.method was \"next\", forget context.arg since it has been\n      // \"consumed\" by the delegate iterator. If context.method was\n      // \"return\", allow the original .return call to continue in the\n      // outer generator.\n      if (context.method !== \"return\") {\n        context.method = \"next\";\n        context.arg = undefined;\n      }\n\n    } else {\n      // Re-yield the result returned by the delegate method.\n      return info;\n    }\n\n    // The delegate iterator is finished, so forget it and continue with\n    // the outer generator.\n    context.delegate = null;\n    return ContinueSentinel;\n  }\n\n  // Define Generator.prototype.{next,throw,return} in terms of the\n  // unified ._invoke helper method.\n  defineIteratorMethods(Gp);\n\n  Gp[toStringTagSymbol] = \"Generator\";\n\n  // A Generator should always return itself as the iterator object when the\n  // @@iterator function is called on it. Some browsers' implementations of the\n  // iterator prototype chain incorrectly implement this, causing the Generator\n  // object to not be returned from this call. This ensures that doesn't happen.\n  // See https://github.com/facebook/regenerator/issues/274 for more details.\n  Gp[iteratorSymbol] = function() {\n    return this;\n  };\n\n  Gp.toString = function() {\n    return \"[object Generator]\";\n  };\n\n  function pushTryEntry(locs) {\n    var entry = { tryLoc: locs[0] };\n\n    if (1 in locs) {\n      entry.catchLoc = locs[1];\n    }\n\n    if (2 in locs) {\n      entry.finallyLoc = locs[2];\n      entry.afterLoc = locs[3];\n    }\n\n    this.tryEntries.push(entry);\n  }\n\n  function resetTryEntry(entry) {\n    var record = entry.completion || {};\n    record.type = \"normal\";\n    delete record.arg;\n    entry.completion = record;\n  }\n\n  function Context(tryLocsList) {\n    // The root entry object (effectively a try statement without a catch\n    // or a finally block) gives us a place to store values thrown from\n    // locations where there is no enclosing try statement.\n    this.tryEntries = [{ tryLoc: \"root\" }];\n    tryLocsList.forEach(pushTryEntry, this);\n    this.reset(true);\n  }\n\n  exports.keys = function(object) {\n    var keys = [];\n    for (var key in object) {\n      keys.push(key);\n    }\n    keys.reverse();\n\n    // Rather than returning an object with a next method, we keep\n    // things simple and return the next function itself.\n    return function next() {\n      while (keys.length) {\n        var key = keys.pop();\n        if (key in object) {\n          next.value = key;\n          next.done = false;\n          return next;\n        }\n      }\n\n      // To avoid creating an additional object, we just hang the .value\n      // and .done properties off the next function object itself. This\n      // also ensures that the minifier will not anonymize the function.\n      next.done = true;\n      return next;\n    };\n  };\n\n  function values(iterable) {\n    if (iterable) {\n      var iteratorMethod = iterable[iteratorSymbol];\n      if (iteratorMethod) {\n        return iteratorMethod.call(iterable);\n      }\n\n      if (typeof iterable.next === \"function\") {\n        return iterable;\n      }\n\n      if (!isNaN(iterable.length)) {\n        var i = -1, next = function next() {\n          while (++i < iterable.length) {\n            if (hasOwn.call(iterable, i)) {\n              next.value = iterable[i];\n              next.done = false;\n              return next;\n            }\n          }\n\n          next.value = undefined;\n          next.done = true;\n\n          return next;\n        };\n\n        return next.next = next;\n      }\n    }\n\n    // Return an iterator with no values.\n    return { next: doneResult };\n  }\n  exports.values = values;\n\n  function doneResult() {\n    return { value: undefined, done: true };\n  }\n\n  Context.prototype = {\n    constructor: Context,\n\n    reset: function(skipTempReset) {\n      this.prev = 0;\n      this.next = 0;\n      // Resetting context._sent for legacy support of Babel's\n      // function.sent implementation.\n      this.sent = this._sent = undefined;\n      this.done = false;\n      this.delegate = null;\n\n      this.method = \"next\";\n      this.arg = undefined;\n\n      this.tryEntries.forEach(resetTryEntry);\n\n      if (!skipTempReset) {\n        for (var name in this) {\n          // Not sure about the optimal order of these conditions:\n          if (name.charAt(0) === \"t\" &&\n              hasOwn.call(this, name) &&\n              !isNaN(+name.slice(1))) {\n            this[name] = undefined;\n          }\n        }\n      }\n    },\n\n    stop: function() {\n      this.done = true;\n\n      var rootEntry = this.tryEntries[0];\n      var rootRecord = rootEntry.completion;\n      if (rootRecord.type === \"throw\") {\n        throw rootRecord.arg;\n      }\n\n      return this.rval;\n    },\n\n    dispatchException: function(exception) {\n      if (this.done) {\n        throw exception;\n      }\n\n      var context = this;\n      function handle(loc, caught) {\n        record.type = \"throw\";\n        record.arg = exception;\n        context.next = loc;\n\n        if (caught) {\n          // If the dispatched exception was caught by a catch block,\n          // then let that catch block handle the exception normally.\n          context.method = \"next\";\n          context.arg = undefined;\n        }\n\n        return !! caught;\n      }\n\n      for (var i = this.tryEntries.length - 1; i >= 0; --i) {\n        var entry = this.tryEntries[i];\n        var record = entry.completion;\n\n        if (entry.tryLoc === \"root\") {\n          // Exception thrown outside of any try block that could handle\n          // it, so set the completion value of the entire function to\n          // throw the exception.\n          return handle(\"end\");\n        }\n\n        if (entry.tryLoc <= this.prev) {\n          var hasCatch = hasOwn.call(entry, \"catchLoc\");\n          var hasFinally = hasOwn.call(entry, \"finallyLoc\");\n\n          if (hasCatch && hasFinally) {\n            if (this.prev < entry.catchLoc) {\n              return handle(entry.catchLoc, true);\n            } else if (this.prev < entry.finallyLoc) {\n              return handle(entry.finallyLoc);\n            }\n\n          } else if (hasCatch) {\n            if (this.prev < entry.catchLoc) {\n              return handle(entry.catchLoc, true);\n            }\n\n          } else if (hasFinally) {\n            if (this.prev < entry.finallyLoc) {\n              return handle(entry.finallyLoc);\n            }\n\n          } else {\n            throw new Error(\"try statement without catch or finally\");\n          }\n        }\n      }\n    },\n\n    abrupt: function(type, arg) {\n      for (var i = this.tryEntries.length - 1; i >= 0; --i) {\n        var entry = this.tryEntries[i];\n        if (entry.tryLoc <= this.prev &&\n            hasOwn.call(entry, \"finallyLoc\") &&\n            this.prev < entry.finallyLoc) {\n          var finallyEntry = entry;\n          break;\n        }\n      }\n\n      if (finallyEntry &&\n          (type === \"break\" ||\n           type === \"continue\") &&\n          finallyEntry.tryLoc <= arg &&\n          arg <= finallyEntry.finallyLoc) {\n        // Ignore the finally entry if control is not jumping to a\n        // location outside the try/catch block.\n        finallyEntry = null;\n      }\n\n      var record = finallyEntry ? finallyEntry.completion : {};\n      record.type = type;\n      record.arg = arg;\n\n      if (finallyEntry) {\n        this.method = \"next\";\n        this.next = finallyEntry.finallyLoc;\n        return ContinueSentinel;\n      }\n\n      return this.complete(record);\n    },\n\n    complete: function(record, afterLoc) {\n      if (record.type === \"throw\") {\n        throw record.arg;\n      }\n\n      if (record.type === \"break\" ||\n          record.type === \"continue\") {\n        this.next = record.arg;\n      } else if (record.type === \"return\") {\n        this.rval = this.arg = record.arg;\n        this.method = \"return\";\n        this.next = \"end\";\n      } else if (record.type === \"normal\" && afterLoc) {\n        this.next = afterLoc;\n      }\n\n      return ContinueSentinel;\n    },\n\n    finish: function(finallyLoc) {\n      for (var i = this.tryEntries.length - 1; i >= 0; --i) {\n        var entry = this.tryEntries[i];\n        if (entry.finallyLoc === finallyLoc) {\n          this.complete(entry.completion, entry.afterLoc);\n          resetTryEntry(entry);\n          return ContinueSentinel;\n        }\n      }\n    },\n\n    \"catch\": function(tryLoc) {\n      for (var i = this.tryEntries.length - 1; i >= 0; --i) {\n        var entry = this.tryEntries[i];\n        if (entry.tryLoc === tryLoc) {\n          var record = entry.completion;\n          if (record.type === \"throw\") {\n            var thrown = record.arg;\n            resetTryEntry(entry);\n          }\n          return thrown;\n        }\n      }\n\n      // The context.catch method must only be called with a location\n      // argument that corresponds to a known catch block.\n      throw new Error(\"illegal catch attempt\");\n    },\n\n    delegateYield: function(iterable, resultName, nextLoc) {\n      this.delegate = {\n        iterator: values(iterable),\n        resultName: resultName,\n        nextLoc: nextLoc\n      };\n\n      if (this.method === \"next\") {\n        // Deliberately forget the last sent value so that we don't\n        // accidentally pass it on to the delegate.\n        this.arg = undefined;\n      }\n\n      return ContinueSentinel;\n    }\n  };\n\n  // Regardless of whether this script is executing as a CommonJS module\n  // or not, return the runtime object so that we can declare the variable\n  // regeneratorRuntime in the outer scope, which allows this module to be\n  // injected easily by `bin/regenerator --include-runtime script.js`.\n  return exports;\n\n}(\n  // If this script is executing as a CommonJS module, use module.exports\n  // as the regeneratorRuntime namespace. Otherwise create a new empty\n  // object. Either way, the resulting object will be used to initialize\n  // the regeneratorRuntime variable at the top of this file.\n   true ? module.exports : undefined\n));\n\ntry {\n  regeneratorRuntime = runtime;\n} catch (accidentalStrictMode) {\n  // This module should not be running in strict mode, so the above\n  // assignment should always work unless something is misconfigured. Just\n  // in case runtime.js accidentally runs in strict mode, we can escape\n  // strict mode using a global Function call. This could conceivably fail\n  // if a Content Security Policy forbids using Function, but in that case\n  // the proper solution is to fix the accidental strict mode problem. If\n  // you've misconfigured your bundler to force strict mode and applied a\n  // CSP to forbid Function, and you're not willing to fix either of those\n  // problems, please detail your unique predicament in a GitHub issue.\n  Function(\"r\", \"regeneratorRuntime = r\")(runtime);\n}\n\n\n//# sourceURL=webpack:///./node_modules/regenerator-runtime/runtime.js?");

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