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
/******/ 	return __webpack_require__(__webpack_require__.s = "./client/component/totara_reaction/src/tui.json");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/component/totara_reaction/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!********************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./SidePanelLike\": \"./client/component/totara_reaction/src/components/SidePanelLike.vue\",\n\t\"./SidePanelLike.vue\": \"./client/component/totara_reaction/src/components/SidePanelLike.vue\",\n\t\"./SimpleLike\": \"./client/component/totara_reaction/src/components/SimpleLike.vue\",\n\t\"./SimpleLike.vue\": \"./client/component/totara_reaction/src/components/SimpleLike.vue\",\n\t\"./buttons/LikeButtonIcon\": \"./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue\",\n\t\"./buttons/LikeButtonIcon.vue\": \"./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue\",\n\t\"./modal/LikeRecordsModal\": \"./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue\",\n\t\"./modal/LikeRecordsModal.vue\": \"./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue\",\n\t\"./popover_content/LikeRecordsList\": \"./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue\",\n\t\"./popover_content/LikeRecordsList.vue\": \"./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/totara_reaction/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/totara_reaction/src/components_sync_^(?:(?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/SidePanelLike.vue":
/*!***************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/SidePanelLike.vue ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SidePanelLike_vue_vue_type_template_id_61eb3bcc___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SidePanelLike.vue?vue&type=template&id=61eb3bcc& */ \"./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=template&id=61eb3bcc&\");\n/* harmony import */ var _SidePanelLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SidePanelLike.vue?vue&type=script&lang=js& */ \"./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SidePanelLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _SidePanelLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SidePanelLike_vue_vue_type_template_id_61eb3bcc___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SidePanelLike_vue_vue_type_template_id_61eb3bcc___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SidePanelLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"] === 'function') Object(_SidePanelLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/totara_reaction/src/components/SidePanelLike.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SidePanelLike.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidePanelLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js??ref--7-0!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidePanelLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SidePanelLike.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_SidePanelLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./SidePanelLike.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_SidePanelLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SidePanelLike.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=template&id=61eb3bcc&":
/*!**********************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=template&id=61eb3bcc& ***!
  \**********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidePanelLike_vue_vue_type_template_id_61eb3bcc___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SidePanelLike.vue?vue&type=template&id=61eb3bcc& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=template&id=61eb3bcc&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidePanelLike_vue_vue_type_template_id_61eb3bcc___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SidePanelLike_vue_vue_type_template_id_61eb3bcc___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SidePanelLike.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/SimpleLike.vue":
/*!************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/SimpleLike.vue ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _SimpleLike_vue_vue_type_template_id_3374fd1e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SimpleLike.vue?vue&type=template&id=3374fd1e& */ \"./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=template&id=3374fd1e&\");\n/* harmony import */ var _SimpleLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SimpleLike.vue?vue&type=script&lang=js& */ \"./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _SimpleLike_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./SimpleLike.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _SimpleLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _SimpleLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _SimpleLike_vue_vue_type_template_id_3374fd1e___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _SimpleLike_vue_vue_type_template_id_3374fd1e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _SimpleLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_SimpleLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/totara_reaction/src/components/SimpleLike.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SimpleLike.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***********************************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***********************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js??ref--7-0!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SimpleLike.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_SimpleLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./SimpleLike.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_SimpleLike_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SimpleLike.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=style&index=0&lang=scss&":
/*!**********************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=style&index=0&lang=scss& ***!
  \**********************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??ref--4-0!../../../../tooling/webpack/css_raw_loader.js??ref--4-1!../../../../../node_modules/postcss-loader/src??ref--4-2!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SimpleLike.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js?!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if([\"default\"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SimpleLike.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=template&id=3374fd1e&":
/*!*******************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=template&id=3374fd1e& ***!
  \*******************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_template_id_3374fd1e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./SimpleLike.vue?vue&type=template&id=3374fd1e& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=template&id=3374fd1e&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_template_id_3374fd1e___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SimpleLike_vue_vue_type_template_id_3374fd1e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SimpleLike.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue":
/*!************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _LikeButtonIcon_vue_vue_type_template_id_cde588d0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LikeButtonIcon.vue?vue&type=template&id=cde588d0& */ \"./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?vue&type=template&id=cde588d0&\");\n/* harmony import */ var _LikeButtonIcon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LikeButtonIcon.vue?vue&type=script&lang=js& */ \"./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _LikeButtonIcon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _LikeButtonIcon_vue_vue_type_template_id_cde588d0___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _LikeButtonIcon_vue_vue_type_template_id_cde588d0___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LikeButtonIcon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LikeButtonIcon.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LikeButtonIcon_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?vue&type=template&id=cde588d0&":
/*!*******************************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?vue&type=template&id=cde588d0& ***!
  \*******************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeButtonIcon_vue_vue_type_template_id_cde588d0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeButtonIcon.vue?vue&type=template&id=cde588d0& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?vue&type=template&id=cde588d0&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeButtonIcon_vue_vue_type_template_id_cde588d0___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeButtonIcon_vue_vue_type_template_id_cde588d0___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue":
/*!************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _LikeRecordsModal_vue_vue_type_template_id_450ef528___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LikeRecordsModal.vue?vue&type=template&id=450ef528& */ \"./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=template&id=450ef528&\");\n/* harmony import */ var _LikeRecordsModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LikeRecordsModal.vue?vue&type=script&lang=js& */ \"./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _LikeRecordsModal_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./LikeRecordsModal.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _LikeRecordsModal_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _LikeRecordsModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _LikeRecordsModal_vue_vue_type_template_id_450ef528___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _LikeRecordsModal_vue_vue_type_template_id_450ef528___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _LikeRecordsModal_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_LikeRecordsModal_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***********************************************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***********************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js??ref--7-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LikeRecordsModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LikeRecordsModal.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LikeRecordsModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=style&index=0&lang=scss&":
/*!**********************************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=style&index=0&lang=scss& ***!
  \**********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??ref--4-0!../../../../../tooling/webpack/css_raw_loader.js??ref--4-1!../../../../../../node_modules/postcss-loader/src??ref--4-2!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeRecordsModal.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js?!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if([\"default\"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=template&id=450ef528&":
/*!*******************************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=template&id=450ef528& ***!
  \*******************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_template_id_450ef528___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeRecordsModal.vue?vue&type=template&id=450ef528& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=template&id=450ef528&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_template_id_450ef528___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsModal_vue_vue_type_template_id_450ef528___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue":
/*!*********************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _LikeRecordsList_vue_vue_type_template_id_9e99043e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LikeRecordsList.vue?vue&type=template&id=9e99043e& */ \"./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=template&id=9e99043e&\");\n/* harmony import */ var _LikeRecordsList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LikeRecordsList.vue?vue&type=script&lang=js& */ \"./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _LikeRecordsList_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./LikeRecordsList.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _LikeRecordsList_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _LikeRecordsList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _LikeRecordsList_vue_vue_type_template_id_9e99043e___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _LikeRecordsList_vue_vue_type_template_id_9e99043e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _LikeRecordsList_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_LikeRecordsList_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!********************************************************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \********************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js??ref--7-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_7_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LikeRecordsList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./LikeRecordsList.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_LikeRecordsList_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/mini-css-extract-plugin/dist/loader.js??ref--4-0!../../../../../tooling/webpack/css_raw_loader.js??ref--4-1!../../../../../../node_modules/postcss-loader/src??ref--4-2!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeRecordsList.vue?vue&type=style&index=0&lang=scss& */ \"./node_modules/mini-css-extract-plugin/dist/loader.js?!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);\n/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if([\"default\"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));\n /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_mini_css_extract_plugin_dist_loader_js_ref_4_0_tooling_webpack_css_raw_loader_js_ref_4_1_node_modules_postcss_loader_src_index_js_ref_4_2_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default.a); \n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=template&id=9e99043e&":
/*!****************************************************************************************************************************!*\
  !*** ./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=template&id=9e99043e& ***!
  \****************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_template_id_9e99043e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LikeRecordsList.vue?vue&type=template&id=9e99043e& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=template&id=9e99043e&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_template_id_9e99043e___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LikeRecordsList_vue_vue_type_template_id_9e99043e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?");

/***/ }),

/***/ "./client/component/totara_reaction/src/tui.json":
/*!*******************************************************!*\
  !*** ./client/component/totara_reaction/src/tui.json ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"totara_reaction\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"totara_reaction\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"totara_reaction\")\ntui._bundle.addModulesFromContext(\"totara_reaction/components\", __webpack_require__(\"./client/component/totara_reaction/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_reaction\": [\n    \"nolikes\",\n    \"numberoflikes\",\n    \"error:create_like\",\n    \"error:remove_like\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SidePanelLike.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_reaction\": [\n    \"bracketcount\",\n    \"error:create_like\",\n    \"error:remove_like\",\n    \"like\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SimpleLike.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_reaction\": [\n    \"likesx\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_reaction\": [\n    \"nolikes\",\n    \"numberofmore\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--7-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_buttons_LabelledButtonTrigger__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/buttons/LabelledButtonTrigger */ \"tui/components/buttons/LabelledButtonTrigger\");\n/* harmony import */ var tui_components_buttons_LabelledButtonTrigger__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_LabelledButtonTrigger__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_reaction/components/popover_content/LikeRecordsList */ \"totara_reaction/components/popover_content/LikeRecordsList\");\n/* harmony import */ var totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! totara_reaction/components/modal/LikeRecordsModal */ \"totara_reaction/components/modal/LikeRecordsModal\");\n/* harmony import */ var totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/icons/Like */ \"tui/components/icons/Like\");\n/* harmony import */ var tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_icons_LikeActive__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/icons/LikeActive */ \"tui/components/icons/LikeActive\");\n/* harmony import */ var tui_components_icons_LikeActive__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_LikeActive__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/components/icons/Loading */ \"tui/components/icons/Loading\");\n/* harmony import */ var tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/components/modal/ModalPresenter */ \"tui/components/modal/ModalPresenter\");\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_reaction/graphql/get_likes */ \"./server/totara/reaction/webapi/ajax/get_likes.graphql\");\n/* harmony import */ var totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! totara_reaction/graphql/liked */ \"./server/totara/reaction/webapi/ajax/liked.graphql\");\n/* harmony import */ var totara_reaction_graphql_create_like__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! totara_reaction/graphql/create_like */ \"./server/totara/reaction/webapi/ajax/create_like.graphql\");\n/* harmony import */ var totara_reaction_graphql_remove_like__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! totara_reaction/graphql/remove_like */ \"./server/totara/reaction/webapi/ajax/remove_like.graphql\");\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! tui/notifications */ \"tui/notifications\");\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(tui_notifications__WEBPACK_IMPORTED_MODULE_11__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n// GraphQL queries\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ModalPresenter: (tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_6___default()),\n    ButtonIconWithLabel: (tui_components_buttons_LabelledButtonTrigger__WEBPACK_IMPORTED_MODULE_0___default()),\n    LikeRecordsList: (totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_1___default()),\n    LikeRecordsModal: (totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_2___default()),\n    Like: (tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_3___default()),\n    LikeActive: (tui_components_icons_LikeActive__WEBPACK_IMPORTED_MODULE_4___default()),\n    Loading: (tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_5___default()),\n  },\n\n  props: {\n    component: {\n      type: String,\n      required: true,\n    },\n\n    area: {\n      type: String,\n      required: true,\n    },\n\n    instanceId: {\n      type: [String, Number],\n      required: true,\n    },\n\n    liked: {\n      type: Boolean,\n      default: null,\n    },\n\n    totalLikes: {\n      type: Boolean,\n      default: null,\n    },\n\n    iconSize: [String, Number],\n\n    buttonAriaLabel: {\n      type: String,\n      required: true,\n    },\n\n    disabled: Boolean,\n  },\n\n  apollo: {\n    count: {\n      query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n      variables() {\n        return {\n          component: this.component,\n          area: this.area,\n          instanceid: this.instanceId,\n        };\n      },\n\n      /**\n       *\n       * @param {Number} count\n       * @return {Number}\n       */\n      update({ count }) {\n        return count;\n      },\n\n      skip() {\n        // Do not load from server if the property's value is provided.\n        return (\n          'undefined' !== typeof this.totalLikes && null !== this.totalLikes\n        );\n      },\n    },\n\n    hasLiked: {\n      query: totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_8__[\"default\"],\n      variables() {\n        return {\n          component: this.component,\n          area: this.area,\n          instanceid: this.instanceId,\n        };\n      },\n\n      /**\n       *\n       * @param {Boolean} result\n       * @return {Boolean}\n       */\n      update({ result }) {\n        return result;\n      },\n\n      skip() {\n        // Do not load from server when the property's value is provided.\n        return 'undefined' !== typeof this.liked && null !== this.liked;\n      },\n    },\n  },\n\n  data() {\n    return {\n      hasLiked: this.liked,\n      count: this.totalLikes,\n      showPopover: false,\n      showModal: false,\n      submitting: false,\n    };\n  },\n\n  computed: {\n    labelAriaLabel() {\n      if (this.count === 0) {\n        return this.$str('nolikes', 'totara_reaction');\n      } else {\n        return this.$str('numberoflikes', 'totara_reaction', this.count);\n      }\n    },\n  },\n\n  watch: {\n    /**\n     *\n     * @param {Boolean} value\n     */\n    liked(value) {\n      if (value === this.hasLiked) {\n        return;\n      }\n\n      this.hasLiked = value;\n    },\n\n    /**\n     *\n     * @param {Number} value\n     */\n    totalLikes(value) {\n      if (value == this.count) {\n        return;\n      }\n\n      this.count = value;\n    },\n  },\n\n  methods: {\n    async like() {\n      if (this.hasLiked) {\n        await this.removeLike();\n      } else {\n        await this.createLike();\n      }\n    },\n\n    async createLike() {\n      if (this.submitting) {\n        return;\n      }\n\n      this.submitting = true;\n      let variables = {\n        component: this.component,\n        area: this.area,\n        instanceid: this.instanceId,\n      };\n\n      try {\n        await this.$apollo.mutate({\n          mutation: totara_reaction_graphql_create_like__WEBPACK_IMPORTED_MODULE_9__[\"default\"],\n          variables: variables,\n          refetchAll: false,\n          refetchQueries: [\n            {\n              query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n              variables: variables,\n            },\n            {\n              query: totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_8__[\"default\"],\n              variables: variables,\n            },\n          ],\n        });\n\n        this.$emit('created-like');\n      } catch (e) {\n        await Object(tui_notifications__WEBPACK_IMPORTED_MODULE_11__[\"notify\"])({\n          message: this.$str('error:create_like', 'totara_reaction'),\n          type: 'error',\n        });\n      } finally {\n        this.submitting = false;\n      }\n    },\n\n    async removeLike() {\n      if (this.submitting) {\n        return;\n      }\n\n      this.submitting = true;\n      let variables = {\n        component: this.component,\n        area: this.area,\n        instanceid: this.instanceId,\n      };\n\n      try {\n        await this.$apollo.mutate({\n          mutation: totara_reaction_graphql_remove_like__WEBPACK_IMPORTED_MODULE_10__[\"default\"],\n          variables: variables,\n          refetchAll: false,\n          refetchQueries: [\n            {\n              query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n              variables: variables,\n            },\n            {\n              query: totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_8__[\"default\"],\n              variables: variables,\n            },\n          ],\n        });\n\n        this.$emit('removed-like');\n      } catch (e) {\n        await Object(tui_notifications__WEBPACK_IMPORTED_MODULE_11__[\"notify\"])({\n          message: this.$str('error:remove_like', 'totara_reaction'),\n          type: 'error',\n        });\n      } finally {\n        this.submitting = false;\n      }\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SidePanelLike.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_reaction/components/modal/LikeRecordsModal */ \"totara_reaction/components/modal/LikeRecordsModal\");\n/* harmony import */ var totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/modal/ModalPresenter */ \"tui/components/modal/ModalPresenter\");\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/popover/Popover */ \"tui/components/popover/Popover\");\n/* harmony import */ var tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var totara_reaction_components_buttons_LikeButtonIcon__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_reaction/components/buttons/LikeButtonIcon */ \"totara_reaction/components/buttons/LikeButtonIcon\");\n/* harmony import */ var totara_reaction_components_buttons_LikeButtonIcon__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_reaction_components_buttons_LikeButtonIcon__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_reaction/components/popover_content/LikeRecordsList */ \"totara_reaction/components/popover_content/LikeRecordsList\");\n/* harmony import */ var totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/notifications */ \"tui/notifications\");\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_notifications__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! totara_reaction/graphql/get_likes */ \"./server/totara/reaction/webapi/ajax/get_likes.graphql\");\n/* harmony import */ var totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_reaction/graphql/liked */ \"./server/totara/reaction/webapi/ajax/liked.graphql\");\n/* harmony import */ var totara_reaction_graphql_create_like__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! totara_reaction/graphql/create_like */ \"./server/totara/reaction/webapi/ajax/create_like.graphql\");\n/* harmony import */ var totara_reaction_graphql_remove_like__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! totara_reaction/graphql/remove_like */ \"./server/totara/reaction/webapi/ajax/remove_like.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n// GraphQL\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    LikeRecordsModal: (totara_reaction_components_modal_LikeRecordsModal__WEBPACK_IMPORTED_MODULE_0___default()),\n    ModalPresenter: (tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_1___default()),\n    Popover: (tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_2___default()),\n    LikeButtonIcon: (totara_reaction_components_buttons_LikeButtonIcon__WEBPACK_IMPORTED_MODULE_3___default()),\n    LikeRecordsList: (totara_reaction_components_popover_content_LikeRecordsList__WEBPACK_IMPORTED_MODULE_4___default()),\n  },\n\n  props: {\n    component: {\n      type: String,\n      required: true,\n    },\n    area: {\n      type: String,\n      required: true,\n    },\n    instanceId: {\n      type: [String, Number],\n      required: true,\n    },\n    disabled: Boolean,\n    /**\n     * Passing this prop to tell whether user has liked record or not. So that this component\n     * will not try to fire a request to the server.\n     */\n    liked: {\n      type: Boolean,\n      default: null,\n    },\n    /**\n     * Passing this prop with a valid value to prevent firing request to the server.\n     * We cant use zero as default, because it might not trigger the query.\n     */\n    totalLikes: {\n      type: [String, Number],\n      default: null,\n    },\n\n    buttonAriaLabel: {\n      type: String,\n      required: true,\n    },\n\n    showText: Boolean,\n  },\n\n  apollo: {\n    count: {\n      query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n      variables() {\n        return {\n          component: this.component,\n          area: this.area,\n          instanceid: this.instanceId,\n        };\n      },\n\n      /**\n       *\n       * @param {Number|String} count\n       * @return {Number}\n       */\n      update({ count }) {\n        return parseInt(count, 9);\n      },\n\n      skip() {\n        // Only start fetching, when the data is not provided.\n        return (\n          'undefined' !== typeof this.totalLikes && null !== this.totalLikes\n        );\n      },\n    },\n\n    hasLiked: {\n      query: totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n      variables() {\n        return {\n          component: this.component,\n          area: this.area,\n          instanceid: this.instanceId,\n        };\n      },\n\n      /**\n       *\n       * @param {Boolean} result\n       * @return {Boolean}\n       */\n      update({ result }) {\n        return result;\n      },\n\n      skip() {\n        // Only start fetching when the data is not provided.\n        return 'undefined' !== typeof this.liked && null !== this.liked;\n      },\n\n      result({ data: { result } }) {\n        this.$emit('update-like-status', result);\n      },\n    },\n  },\n\n  data() {\n    return {\n      hasLiked: this.liked,\n      count: this.totalLikes,\n      showModal: false,\n      showPopover: false,\n      submitting: false,\n    };\n  },\n\n  computed: {\n    buttonText() {\n      if (!this.showText) {\n        return '';\n      }\n\n      return this.$str('like', 'totara_reaction');\n    },\n  },\n\n  watch: {\n    /**\n     *\n     * @param {Boolean} value\n     */\n    liked(value) {\n      if (value === this.hasLiked) {\n        return;\n      }\n\n      this.hasLiked = value;\n    },\n\n    /**\n     *\n     * @param {Number} value\n     */\n    totalLikes(value) {\n      if (value == this.count) {\n        return;\n      }\n\n      this.count = value;\n    },\n  },\n\n  methods: {\n    async like() {\n      if (this.hasLiked) {\n        await this.removeLike();\n      } else {\n        await this.createLike();\n      }\n    },\n\n    async createLike() {\n      if (this.submitting) {\n        return;\n      }\n\n      this.submitting = true;\n      let variables = {\n        component: this.component,\n        area: this.area,\n        instanceid: this.instanceId,\n      };\n\n      try {\n        await this.$apollo.mutate({\n          mutation: totara_reaction_graphql_create_like__WEBPACK_IMPORTED_MODULE_8__[\"default\"],\n          variables: variables,\n          refetchAll: false,\n          refetchQueries: [\n            {\n              query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n              variables: variables,\n            },\n            {\n              query: totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n              variables: variables,\n            },\n          ],\n        });\n\n        this.$emit('created-like');\n      } catch (e) {\n        await Object(tui_notifications__WEBPACK_IMPORTED_MODULE_5__[\"notify\"])({\n          message: this.$str('error:create_like', 'totara_reaction'),\n          type: 'error',\n        });\n      } finally {\n        this.submitting = false;\n      }\n    },\n\n    async removeLike() {\n      if (this.submitting) {\n        return;\n      }\n\n      this.submitting = true;\n      let variables = {\n        component: this.component,\n        area: this.area,\n        instanceid: this.instanceId,\n      };\n\n      try {\n        await this.$apollo.mutate({\n          mutation: totara_reaction_graphql_remove_like__WEBPACK_IMPORTED_MODULE_9__[\"default\"],\n          variables: variables,\n          refetchAll: false,\n          refetchQueries: [\n            {\n              query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n              variables: variables,\n            },\n            {\n              query: totara_reaction_graphql_liked__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n              variables: variables,\n            },\n          ],\n        });\n\n        this.$emit('removed-like');\n      } catch (e) {\n        await Object(tui_notifications__WEBPACK_IMPORTED_MODULE_5__[\"notify\"])({\n          message: this.$str('error:remove_like', 'totara_reaction'),\n          type: 'error',\n        });\n      } finally {\n        this.submitting = false;\n      }\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SimpleLike.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/buttons/ButtonIcon */ \"tui/components/buttons/ButtonIcon\");\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/icons/Like */ \"tui/components/icons/Like\");\n/* harmony import */ var tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_icons_LikeActive__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/icons/LikeActive */ \"tui/components/icons/LikeActive\");\n/* harmony import */ var tui_components_icons_LikeActive__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_LikeActive__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/icons/Loading */ \"tui/components/icons/Loading\");\n/* harmony import */ var tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_3__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ButtonIcon: (tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_0___default()),\n    LikeIcon: (tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_1___default()),\n    LikedIcon: (tui_components_icons_LikeActive__WEBPACK_IMPORTED_MODULE_2___default()),\n    Loading: (tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_3___default()),\n  },\n\n  props: {\n    transparent: {\n      type: Boolean,\n      default: true,\n    },\n\n    small: {\n      type: Boolean,\n      default: true,\n    },\n\n    transparentNoPadding: {\n      type: Boolean,\n      default: true,\n    },\n\n    submitting: Boolean,\n    liked: Boolean,\n    iconSize: [String, Number],\n\n    ariaLabel: {\n      type: String,\n      required: true,\n    },\n\n    disabled: Boolean,\n    text: String,\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_modal_Modal__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/modal/Modal */ \"tui/components/modal/Modal\");\n/* harmony import */ var tui_components_modal_Modal__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_Modal__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_modal_ModalContent__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/modal/ModalContent */ \"tui/components/modal/ModalContent\");\n/* harmony import */ var tui_components_modal_ModalContent__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_ModalContent__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/avatar/Avatar */ \"tui/components/avatar/Avatar\");\n/* harmony import */ var tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/icons/Loading */ \"tui/components/icons/Loading\");\n/* harmony import */ var tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_reaction/graphql/get_likes */ \"./server/totara/reaction/webapi/ajax/get_likes.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n// GraphQL\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Modal: (tui_components_modal_Modal__WEBPACK_IMPORTED_MODULE_0___default()),\n    ModalContent: (tui_components_modal_ModalContent__WEBPACK_IMPORTED_MODULE_1___default()),\n    Avatar: (tui_components_avatar_Avatar__WEBPACK_IMPORTED_MODULE_2___default()),\n    Loading: (tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_3___default()),\n  },\n\n  props: {\n    instanceId: {\n      type: [String, Number],\n      required: true,\n    },\n\n    component: {\n      type: String,\n      required: true,\n    },\n\n    area: {\n      type: String,\n      required: true,\n    },\n  },\n\n  apollo: {\n    like: {\n      query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_4__[\"default\"],\n      fetchPolicy: 'network-only',\n      variables() {\n        return {\n          component: this.component,\n          area: this.area,\n          instanceid: this.instanceId,\n        };\n      },\n\n      update({ count, reactions }) {\n        return { count, reactions };\n      },\n    },\n  },\n\n  data() {\n    return {\n      page: 1,\n      like: {\n        count: 0,\n        reactions: [],\n      },\n    };\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/icons/Loading */ \"tui/components/icons/Loading\");\n/* harmony import */ var tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_reaction/graphql/get_likes */ \"./server/totara/reaction/webapi/ajax/get_likes.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n// GraphQL queries\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Loading: (tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_0___default()),\n  },\n\n  props: {\n    component: {\n      type: String,\n      required: true,\n    },\n\n    area: {\n      type: String,\n      required: true,\n    },\n\n    instanceId: {\n      type: [String, Number],\n      required: true,\n    },\n\n    /**\n     * A prop to tell apollo whether to load or the records or not.\n     * This prop is being used in skip function, which it will only affect once.\n     */\n    skipLoadingRecords: Boolean,\n  },\n\n  apollo: {\n    like: {\n      query: totara_reaction_graphql_get_likes__WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n      skip() {\n        return this.skipLoadingRecords;\n      },\n      variables() {\n        return {\n          component: this.component,\n          area: this.area,\n          instanceid: this.instanceId,\n        };\n      },\n\n      /**\n       *\n       * @param {Number} count\n       * @param {Array} reactions\n       * @return {{count, reactions}}\n       */\n      update({ count, reactions }) {\n        return {\n          count: count,\n          reactions: reactions,\n        };\n      },\n    },\n  },\n\n  data() {\n    return {\n      page: 1,\n      like: {\n        count: 0,\n        reactions: [],\n      },\n    };\n  },\n\n  computed: {\n    /**\n     * Only fetching the first 10 of the items.\n     * @return {Array}\n     */\n    reactions() {\n      return Array.prototype.slice.call(this.like.reactions, 0, 9);\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js?!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=style&index=0&lang=scss&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js??ref--4-0!./client/tooling/webpack/css_raw_loader.js??ref--4-1!./node_modules/postcss-loader/src??ref--4-2!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=style&index=0&lang=scss& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SimpleLike.vue?./node_modules/mini-css-extract-plugin/dist/loader.js??ref--4-0!./client/tooling/webpack/css_raw_loader.js??ref--4-1!./node_modules/postcss-loader/src??ref--4-2!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js?!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=style&index=0&lang=scss&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js??ref--4-0!./client/tooling/webpack/css_raw_loader.js??ref--4-1!./node_modules/postcss-loader/src??ref--4-2!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=style&index=0&lang=scss& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?./node_modules/mini-css-extract-plugin/dist/loader.js??ref--4-0!./client/tooling/webpack/css_raw_loader.js??ref--4-1!./node_modules/postcss-loader/src??ref--4-2!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js?!./client/tooling/webpack/css_raw_loader.js?!./node_modules/postcss-loader/src/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js??ref--4-0!./client/tooling/webpack/css_raw_loader.js??ref--4-1!./node_modules/postcss-loader/src??ref--4-2!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?./node_modules/mini-css-extract-plugin/dist/loader.js??ref--4-0!./client/tooling/webpack/css_raw_loader.js??ref--4-1!./node_modules/postcss-loader/src??ref--4-2!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=template&id=61eb3bcc&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_reaction/src/components/SidePanelLike.vue?vue&type=template&id=61eb3bcc& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-sidePanelLike\"},[(!_vm.$apollo.loading)?[_c('ButtonIconWithLabel',{attrs:{\"button-aria-label\":_vm.buttonAriaLabel,\"label-aria-label\":_vm.labelAriaLabel,\"label-text\":_vm.count,\"disabled\":_vm.disabled,\"closeable-popover\":false},on:{\"popover-open-changed\":function($event){_vm.showPopover = $event},\"open\":function($event){_vm.showModal = true},\"click\":_vm.like},scopedSlots:_vm._u([{key:\"icon\",fn:function(){return [(_vm.submitting)?_c('Loading'):(!_vm.hasLiked)?_c('Like'):_c('LikeActive')]},proxy:true},{key:\"hover-label-content\",fn:function(){return [_c('LikeRecordsList',{attrs:{\"component\":_vm.component,\"area\":_vm.area,\"instance-id\":_vm.instanceId}})]},proxy:true}],null,false,1618023747)}),_vm._v(\" \"),_c('ModalPresenter',{attrs:{\"open\":_vm.showModal},on:{\"request-close\":function($event){_vm.showModal = false}}},[_c('LikeRecordsModal',{attrs:{\"component\":_vm.component,\"area\":_vm.area,\"instance-id\":_vm.instanceId}})],1)]:_vm._e()],2)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SidePanelLike.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=template&id=3374fd1e&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_reaction/src/components/SimpleLike.vue?vue&type=template&id=3374fd1e& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-simpleLike\"},[(!_vm.$apollo.loading)?[_c('LikeButtonIcon',{attrs:{\"aria-label\":_vm.buttonAriaLabel,\"liked\":_vm.hasLiked,\"submitting\":_vm.submitting,\"disabled\":_vm.disabled,\"text\":_vm.buttonText},on:{\"click\":function($event){$event.preventDefault();return _vm.like($event)}}}),_vm._v(\" \"),(0 !== _vm.count)?_c('Popover',{staticClass:\"tui-simpleLike__popover\",attrs:{\"triggers\":['focus', 'hover']},on:{\"open-changed\":function($event){_vm.showPopover = $event}},scopedSlots:_vm._u([{key:\"trigger\",fn:function(){return [_c('a',{attrs:{\"href\":\"#\"},on:{\"click\":function($event){$event.preventDefault();_vm.showModal = true}}},[_vm._v(\"\\n          \"+_vm._s(_vm.$str('bracketcount', 'totara_reaction', _vm.count))+\"\\n        \")])]},proxy:true}],null,false,1309043721)},[_vm._v(\" \"),_c('LikeRecordsList',{attrs:{\"skip-loading-records\":!_vm.showPopover,\"component\":_vm.component,\"area\":_vm.area,\"instance-id\":_vm.instanceId}})],1):_vm._e()]:_vm._e(),_vm._v(\" \"),_c('ModalPresenter',{attrs:{\"open\":_vm.showModal},on:{\"request-close\":function($event){_vm.showModal = false}}},[_c('LikeRecordsModal',{attrs:{\"component\":_vm.component,\"instance-id\":_vm.instanceId,\"area\":_vm.area}})],1)],2)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/SimpleLike.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?vue&type=template&id=cde588d0&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?vue&type=template&id=cde588d0& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('ButtonIcon',{attrs:{\"aria-label\":_vm.ariaLabel,\"styleclass\":{ transparent: _vm.transparent, small: _vm.small, transparentNoPadding: _vm.transparentNoPadding },\"disabled\":_vm.disabled,\"text\":_vm.text},on:{\"click\":function($event){return _vm.$emit('click', $event)}}},[(_vm.submitting)?_c('Loading',{attrs:{\"size\":_vm.iconSize}}):(!_vm.liked)?_c('LikeIcon',{attrs:{\"size\":_vm.iconSize}}):_c('LikedIcon',{attrs:{\"size\":_vm.iconSize}})],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/buttons/LikeButtonIcon.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=template&id=450ef528&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?vue&type=template&id=450ef528& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('Modal',{staticClass:\"tui-likeRecordsModal\"},[_c('ModalContent',{attrs:{\"close-button\":true},scopedSlots:_vm._u([{key:\"title\",fn:function(){return [_c('h2',{staticClass:\"tui-likeRecordsModal__title\"},[_c('span',[_vm._v(\"\\n          \"+_vm._s(_vm.$str('likesx', 'totara_reaction', _vm.like.count))+\"\\n        \")]),_vm._v(\" \"),(_vm.$apollo.loading)?_c('Loading'):_vm._e()],1)]},proxy:true}])},[_vm._v(\" \"),_c('div',{staticClass:\"tui-likeRecordsModal__content\"},[_c('ul',{staticClass:\"tui-likeRecordsModal__records\"},_vm._l((_vm.like.reactions),function(ref,index){\nvar user = ref.user;\nreturn _c('li',{key:index},[_c('Avatar',{attrs:{\"src\":user.profileimageurl,\"alt\":user.profileimagealt || '',\"size\":\"xsmall\"}}),_vm._v(\" \"),_c('a',{attrs:{\"href\":_vm.$url('/user/profile.php', { id: user.id })}},[_vm._v(\"\\n            \"+_vm._s(user.fullname)+\"\\n          \")])],1)}),0)])])],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/modal/LikeRecordsModal.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=template&id=9e99043e&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?vue&type=template&id=9e99043e& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-likeRecordsList\"},[(_vm.$apollo.loading)?_c('Loading',{attrs:{\"size\":\"200\"}}):[(0 === _vm.like.count)?_c('p',[_vm._v(\"\\n      \"+_vm._s(_vm.$str('nolikes', 'totara_reaction'))+\"\\n    \")]):_c('ul',{staticClass:\"tui-likeRecordsList__list\"},_vm._l((_vm.reactions),function(ref,index){\nvar fullname = ref.user.fullname;\nreturn _c('li',{key:index},[_vm._v(\"\\n        \"+_vm._s(fullname)+\"\\n      \")])}),0),_vm._v(\" \"),(_vm.like.count > 10)?_c('p',[_vm._v(\"\\n      \"+_vm._s(_vm.$str('numberofmore', 'totara_reaction', _vm.like.count - 10))+\"\\n    \")]):_vm._e()]],2)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/totara_reaction/src/components/popover_content/LikeRecordsList.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

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

/***/ "./server/totara/reaction/webapi/ajax/create_like.graphql":
/*!****************************************************************!*\
  !*** ./server/totara/reaction/webapi/ajax/create_like.graphql ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_create_like\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_component\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_area\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"reaction\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_create\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]}]}}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/reaction/webapi/ajax/create_like.graphql?");

/***/ }),

/***/ "./server/totara/reaction/webapi/ajax/get_likes.graphql":
/*!**************************************************************!*\
  !*** ./server/totara/reaction/webapi/ajax/get_likes.graphql ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_get_likes\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_component\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_area\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"page\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"count\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_total\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}}}],\"directives\":[]},{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"reactions\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_reactions\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"page\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"page\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimagealt\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimageurl\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimageurlsmall\"},\"arguments\":[],\"directives\":[]}]}}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/reaction/webapi/ajax/get_likes.graphql?");

/***/ }),

/***/ "./server/totara/reaction/webapi/ajax/liked.graphql":
/*!**********************************************************!*\
  !*** ./server/totara/reaction/webapi/ajax/liked.graphql ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_liked\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_component\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_area\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"result\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_liked\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}}}],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/reaction/webapi/ajax/liked.graphql?");

/***/ }),

/***/ "./server/totara/reaction/webapi/ajax/remove_like.graphql":
/*!****************************************************************!*\
  !*** ./server/totara/reaction/webapi/ajax/remove_like.graphql ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_remove_like\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_component\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_area\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"result\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_reaction_delete\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"area\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"}}}],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/reaction/webapi/ajax/remove_like.graphql?");

/***/ }),

/***/ "totara_reaction/components/buttons/LikeButtonIcon":
/*!*************************************************************************************!*\
  !*** external "tui.require(\"totara_reaction/components/buttons/LikeButtonIcon\")" ***!
  \*************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_reaction/components/buttons/LikeButtonIcon\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_reaction/components/buttons/LikeButtonIcon\\%22)%22?");

/***/ }),

/***/ "totara_reaction/components/modal/LikeRecordsModal":
/*!*************************************************************************************!*\
  !*** external "tui.require(\"totara_reaction/components/modal/LikeRecordsModal\")" ***!
  \*************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_reaction/components/modal/LikeRecordsModal\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_reaction/components/modal/LikeRecordsModal\\%22)%22?");

/***/ }),

/***/ "totara_reaction/components/popover_content/LikeRecordsList":
/*!**********************************************************************************************!*\
  !*** external "tui.require(\"totara_reaction/components/popover_content/LikeRecordsList\")" ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_reaction/components/popover_content/LikeRecordsList\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_reaction/components/popover_content/LikeRecordsList\\%22)%22?");

/***/ }),

/***/ "tui/components/avatar/Avatar":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/avatar/Avatar\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/avatar/Avatar\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/avatar/Avatar\\%22)%22?");

/***/ }),

/***/ "tui/components/buttons/ButtonIcon":
/*!*********************************************************************!*\
  !*** external "tui.require(\"tui/components/buttons/ButtonIcon\")" ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/buttons/ButtonIcon\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/buttons/ButtonIcon\\%22)%22?");

/***/ }),

/***/ "tui/components/buttons/LabelledButtonTrigger":
/*!********************************************************************************!*\
  !*** external "tui.require(\"tui/components/buttons/LabelledButtonTrigger\")" ***!
  \********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/buttons/LabelledButtonTrigger\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/buttons/LabelledButtonTrigger\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Like":
/*!*************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Like\")" ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Like\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Like\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/LikeActive":
/*!*******************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/LikeActive\")" ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/LikeActive\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/LikeActive\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Loading":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Loading\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Loading\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Loading\\%22)%22?");

/***/ }),

/***/ "tui/components/modal/Modal":
/*!**************************************************************!*\
  !*** external "tui.require(\"tui/components/modal/Modal\")" ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/modal/Modal\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/modal/Modal\\%22)%22?");

/***/ }),

/***/ "tui/components/modal/ModalContent":
/*!*********************************************************************!*\
  !*** external "tui.require(\"tui/components/modal/ModalContent\")" ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/modal/ModalContent\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/modal/ModalContent\\%22)%22?");

/***/ }),

/***/ "tui/components/modal/ModalPresenter":
/*!***********************************************************************!*\
  !*** external "tui.require(\"tui/components/modal/ModalPresenter\")" ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/modal/ModalPresenter\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/modal/ModalPresenter\\%22)%22?");

/***/ }),

/***/ "tui/components/popover/Popover":
/*!******************************************************************!*\
  !*** external "tui.require(\"tui/components/popover/Popover\")" ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/popover/Popover\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/popover/Popover\\%22)%22?");

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