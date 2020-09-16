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
/******/ 	return __webpack_require__(__webpack_require__.s = "./client/component/engage_article/src/tui.json");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/component/engage_article/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!*******************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \*******************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./CreateArticle\": \"./client/component/engage_article/src/components/CreateArticle.vue\",\n\t\"./CreateArticle.vue\": \"./client/component/engage_article/src/components/CreateArticle.vue\",\n\t\"./card/ArticleCard\": \"./client/component/engage_article/src/components/card/ArticleCard.vue\",\n\t\"./card/ArticleCard.vue\": \"./client/component/engage_article/src/components/card/ArticleCard.vue\",\n\t\"./card/RelatedCard\": \"./client/component/engage_article/src/components/card/RelatedCard.vue\",\n\t\"./card/RelatedCard.vue\": \"./client/component/engage_article/src/components/card/RelatedCard.vue\",\n\t\"./content/ArticleContent\": \"./client/component/engage_article/src/components/content/ArticleContent.vue\",\n\t\"./content/ArticleContent.vue\": \"./client/component/engage_article/src/components/content/ArticleContent.vue\",\n\t\"./content/ArticleTitle\": \"./client/component/engage_article/src/components/content/ArticleTitle.vue\",\n\t\"./content/ArticleTitle.vue\": \"./client/component/engage_article/src/components/content/ArticleTitle.vue\",\n\t\"./form/ArticleForm\": \"./client/component/engage_article/src/components/form/ArticleForm.vue\",\n\t\"./form/ArticleForm.vue\": \"./client/component/engage_article/src/components/form/ArticleForm.vue\",\n\t\"./form/EditArticleContentForm\": \"./client/component/engage_article/src/components/form/EditArticleContentForm.vue\",\n\t\"./form/EditArticleContentForm.vue\": \"./client/component/engage_article/src/components/form/EditArticleContentForm.vue\",\n\t\"./form/EditArticleTitleForm\": \"./client/component/engage_article/src/components/form/EditArticleTitleForm.vue\",\n\t\"./form/EditArticleTitleForm.vue\": \"./client/component/engage_article/src/components/form/EditArticleTitleForm.vue\",\n\t\"./separator/ArticleSeparator\": \"./client/component/engage_article/src/components/separator/ArticleSeparator.vue\",\n\t\"./separator/ArticleSeparator.vue\": \"./client/component/engage_article/src/components/separator/ArticleSeparator.vue\",\n\t\"./sidepanel/ArticleSidePanel\": \"./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue\",\n\t\"./sidepanel/ArticleSidePanel.vue\": \"./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue\",\n\t\"./sidepanel/Related\": \"./client/component/engage_article/src/components/sidepanel/Related.vue\",\n\t\"./sidepanel/Related.vue\": \"./client/component/engage_article/src/components/sidepanel/Related.vue\",\n\t\"./sidepanel/content/ArticlePlaylistBox\": \"./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue\",\n\t\"./sidepanel/content/ArticlePlaylistBox.vue\": \"./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/engage_article/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/engage_article/src/components_sync_^(?:(?");

/***/ }),

/***/ "./client/component/engage_article/src/components/CreateArticle.vue":
/*!**************************************************************************!*\
  !*** ./client/component/engage_article/src/components/CreateArticle.vue ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _CreateArticle_vue_vue_type_template_id_20bcfe1b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./CreateArticle.vue?vue&type=template&id=20bcfe1b& */ \"./client/component/engage_article/src/components/CreateArticle.vue?vue&type=template&id=20bcfe1b&\");\n/* harmony import */ var _CreateArticle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CreateArticle.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/CreateArticle.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _CreateArticle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./CreateArticle.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/CreateArticle.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _CreateArticle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_CreateArticle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _CreateArticle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _CreateArticle_vue_vue_type_template_id_20bcfe1b___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _CreateArticle_vue_vue_type_template_id_20bcfe1b___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/CreateArticle.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/CreateArticle.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/CreateArticle.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CreateArticle.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/CreateArticle.vue?vue&type=style&index=0&lang=scss&":
/*!************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/CreateArticle.vue?vue&type=style&index=0&lang=scss& ***!
  \************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/CreateArticle.vue?vue&type=template&id=20bcfe1b&":
/*!*********************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/CreateArticle.vue?vue&type=template&id=20bcfe1b& ***!
  \*********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_template_id_20bcfe1b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./CreateArticle.vue?vue&type=template&id=20bcfe1b& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=template&id=20bcfe1b&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_template_id_20bcfe1b___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CreateArticle_vue_vue_type_template_id_20bcfe1b___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/ArticleCard.vue":
/*!*****************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/ArticleCard.vue ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleCard_vue_vue_type_template_id_18b826b6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleCard.vue?vue&type=template&id=18b826b6& */ \"./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=template&id=18b826b6&\");\n/* harmony import */ var _ArticleCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleCard.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticleCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticleCard.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _ArticleCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_ArticleCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _ArticleCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticleCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticleCard_vue_vue_type_template_id_18b826b6___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleCard_vue_vue_type_template_id_18b826b6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _ArticleCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_ArticleCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/card/ArticleCard.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js??ref--6-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleCard.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=style&index=0&lang=scss&":
/*!***************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=style&index=0&lang=scss& ***!
  \***************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=template&id=18b826b6&":
/*!************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=template&id=18b826b6& ***!
  \************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_template_id_18b826b6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleCard.vue?vue&type=template&id=18b826b6& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=template&id=18b826b6&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_template_id_18b826b6___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleCard_vue_vue_type_template_id_18b826b6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/RelatedCard.vue":
/*!*****************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/RelatedCard.vue ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _RelatedCard_vue_vue_type_template_id_f16a1e2a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RelatedCard.vue?vue&type=template&id=f16a1e2a& */ \"./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=template&id=f16a1e2a&\");\n/* harmony import */ var _RelatedCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./RelatedCard.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _RelatedCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./RelatedCard.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _RelatedCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_RelatedCard_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _RelatedCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _RelatedCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _RelatedCard_vue_vue_type_template_id_f16a1e2a___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _RelatedCard_vue_vue_type_template_id_f16a1e2a___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _RelatedCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_RelatedCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/card/RelatedCard.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js??ref--6-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./RelatedCard.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=style&index=0&lang=scss&":
/*!***************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=style&index=0&lang=scss& ***!
  \***************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=template&id=f16a1e2a&":
/*!************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=template&id=f16a1e2a& ***!
  \************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_template_id_f16a1e2a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./RelatedCard.vue?vue&type=template&id=f16a1e2a& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=template&id=f16a1e2a&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_template_id_f16a1e2a___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_RelatedCard_vue_vue_type_template_id_f16a1e2a___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleContent.vue":
/*!***********************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleContent.vue ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleContent_vue_vue_type_template_id_7827ff08___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleContent.vue?vue&type=template&id=7827ff08& */ \"./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=template&id=7827ff08&\");\n/* harmony import */ var _ArticleContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleContent.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticleContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticleContent.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _ArticleContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_ArticleContent_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticleContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticleContent_vue_vue_type_template_id_7827ff08___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleContent_vue_vue_type_template_id_7827ff08___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/content/ArticleContent.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleContent.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=style&index=0&lang=scss&":
/*!*********************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=style&index=0&lang=scss& ***!
  \*********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=template&id=7827ff08&":
/*!******************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=template&id=7827ff08& ***!
  \******************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_template_id_7827ff08___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleContent.vue?vue&type=template&id=7827ff08& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=template&id=7827ff08&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_template_id_7827ff08___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleContent_vue_vue_type_template_id_7827ff08___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleTitle.vue":
/*!*********************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleTitle.vue ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleTitle_vue_vue_type_template_id_be80d1b2___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleTitle.vue?vue&type=template&id=be80d1b2& */ \"./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=template&id=be80d1b2&\");\n/* harmony import */ var _ArticleTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleTitle.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticleTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticleTitle.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _ArticleTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_ArticleTitle_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticleTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticleTitle_vue_vue_type_template_id_be80d1b2___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleTitle_vue_vue_type_template_id_be80d1b2___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/content/ArticleTitle.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleTitle.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=template&id=be80d1b2&":
/*!****************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=template&id=be80d1b2& ***!
  \****************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_template_id_be80d1b2___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleTitle.vue?vue&type=template&id=be80d1b2& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=template&id=be80d1b2&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_template_id_be80d1b2___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleTitle_vue_vue_type_template_id_be80d1b2___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/ArticleForm.vue":
/*!*****************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/ArticleForm.vue ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleForm_vue_vue_type_template_id_01a1681e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleForm.vue?vue&type=template&id=01a1681e& */ \"./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=template&id=01a1681e&\");\n/* harmony import */ var _ArticleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleForm.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticleForm.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _ArticleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_ArticleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _ArticleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticleForm_vue_vue_type_template_id_01a1681e___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleForm_vue_vue_type_template_id_01a1681e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _ArticleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_ArticleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/form/ArticleForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!****************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \****************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js??ref--6-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleForm.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=style&index=0&lang=scss&":
/*!***************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=style&index=0&lang=scss& ***!
  \***************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=template&id=01a1681e&":
/*!************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=template&id=01a1681e& ***!
  \************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_template_id_01a1681e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleForm.vue?vue&type=template&id=01a1681e& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=template&id=01a1681e&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_template_id_01a1681e___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleForm_vue_vue_type_template_id_01a1681e___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleContentForm.vue":
/*!****************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleContentForm.vue ***!
  \****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _EditArticleContentForm_vue_vue_type_template_id_93461ec6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./EditArticleContentForm.vue?vue&type=template&id=93461ec6& */ \"./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=template&id=93461ec6&\");\n/* harmony import */ var _EditArticleContentForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./EditArticleContentForm.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _EditArticleContentForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _EditArticleContentForm_vue_vue_type_template_id_93461ec6___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _EditArticleContentForm_vue_vue_type_template_id_93461ec6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/form/EditArticleContentForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleContentForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleContentForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./EditArticleContentForm.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleContentForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleContentForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=template&id=93461ec6&":
/*!***********************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=template&id=93461ec6& ***!
  \***********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleContentForm_vue_vue_type_template_id_93461ec6___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./EditArticleContentForm.vue?vue&type=template&id=93461ec6& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=template&id=93461ec6&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleContentForm_vue_vue_type_template_id_93461ec6___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleContentForm_vue_vue_type_template_id_93461ec6___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleContentForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleTitleForm.vue":
/*!**************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleTitleForm.vue ***!
  \**************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _EditArticleTitleForm_vue_vue_type_template_id_11c34d08___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./EditArticleTitleForm.vue?vue&type=template&id=11c34d08& */ \"./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=template&id=11c34d08&\");\n/* harmony import */ var _EditArticleTitleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./EditArticleTitleForm.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _EditArticleTitleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./EditArticleTitleForm.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _EditArticleTitleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_EditArticleTitleForm_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _EditArticleTitleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _EditArticleTitleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _EditArticleTitleForm_vue_vue_type_template_id_11c34d08___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _EditArticleTitleForm_vue_vue_type_template_id_11c34d08___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _EditArticleTitleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_EditArticleTitleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/form/EditArticleTitleForm.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js??ref--6-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./EditArticleTitleForm.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=style&index=0&lang=scss&":
/*!************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=style&index=0&lang=scss& ***!
  \************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=template&id=11c34d08&":
/*!*********************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=template&id=11c34d08& ***!
  \*********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_template_id_11c34d08___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./EditArticleTitleForm.vue?vue&type=template&id=11c34d08& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=template&id=11c34d08&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_template_id_11c34d08___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EditArticleTitleForm_vue_vue_type_template_id_11c34d08___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/separator/ArticleSeparator.vue":
/*!***************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/separator/ArticleSeparator.vue ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleSeparator_vue_vue_type_template_id_01d50df0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleSeparator.vue?vue&type=template&id=01d50df0& */ \"./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=template&id=01d50df0&\");\n/* harmony import */ var _ArticleSeparator_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleSeparator.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _ArticleSeparator_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_ArticleSeparator_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\nvar script = {}\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  script,\n  _ArticleSeparator_vue_vue_type_template_id_01d50df0___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleSeparator_vue_vue_type_template_id_01d50df0___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":false,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/separator/ArticleSeparator.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/separator/ArticleSeparator.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=style&index=0&lang=scss&":
/*!*************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=style&index=0&lang=scss& ***!
  \*************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/separator/ArticleSeparator.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=template&id=01d50df0&":
/*!**********************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=template&id=01d50df0& ***!
  \**********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSeparator_vue_vue_type_template_id_01d50df0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleSeparator.vue?vue&type=template&id=01d50df0& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=template&id=01d50df0&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSeparator_vue_vue_type_template_id_01d50df0___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSeparator_vue_vue_type_template_id_01d50df0___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/separator/ArticleSeparator.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue":
/*!***************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleSidePanel_vue_vue_type_template_id_3c516de8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleSidePanel.vue?vue&type=template&id=3c516de8& */ \"./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=template&id=3c516de8&\");\n/* harmony import */ var _ArticleSidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleSidePanel.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticleSidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticleSidePanel.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _ArticleSidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_ArticleSidePanel_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _ArticleSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticleSidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticleSidePanel_vue_vue_type_template_id_3c516de8___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleSidePanel_vue_vue_type_template_id_3c516de8___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _ArticleSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_ArticleSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**************************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../tooling/webpack/tui_lang_strings_loader.js??ref--6-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleSidePanel.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=style&index=0&lang=scss&":
/*!*************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=style&index=0&lang=scss& ***!
  \*************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=template&id=3c516de8&":
/*!**********************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=template&id=3c516de8& ***!
  \**********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_template_id_3c516de8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleSidePanel.vue?vue&type=template&id=3c516de8& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=template&id=3c516de8&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_template_id_3c516de8___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleSidePanel_vue_vue_type_template_id_3c516de8___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/Related.vue":
/*!******************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/Related.vue ***!
  \******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _Related_vue_vue_type_template_id_44b6de2c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Related.vue?vue&type=template&id=44b6de2c& */ \"./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=template&id=44b6de2c&\");\n/* harmony import */ var _Related_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Related.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _Related_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Related.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _Related_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_Related_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _Related_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _Related_vue_vue_type_template_id_44b6de2c___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _Related_vue_vue_type_template_id_44b6de2c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/sidepanel/Related.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Related.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=style&index=0&lang=scss&":
/*!****************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=style&index=0&lang=scss& ***!
  \****************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=template&id=44b6de2c&":
/*!*************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=template&id=44b6de2c& ***!
  \*************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_template_id_44b6de2c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Related.vue?vue&type=template&id=44b6de2c& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=template&id=44b6de2c&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_template_id_44b6de2c___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Related_vue_vue_type_template_id_44b6de2c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue":
/*!*************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue ***!
  \*************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticlePlaylistBox_vue_vue_type_template_id_2c100fac___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticlePlaylistBox.vue?vue&type=template&id=2c100fac& */ \"./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=template&id=2c100fac&\");\n/* harmony import */ var _ArticlePlaylistBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticlePlaylistBox.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticlePlaylistBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticlePlaylistBox.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _ArticlePlaylistBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_ArticlePlaylistBox_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _ArticlePlaylistBox_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticlePlaylistBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticlePlaylistBox_vue_vue_type_template_id_2c100fac___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticlePlaylistBox_vue_vue_type_template_id_2c100fac___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _ArticlePlaylistBox_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_ArticlePlaylistBox_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!************************************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../tooling/webpack/tui_lang_strings_loader.js??ref--6-0!../../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticlePlaylistBox.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=style&index=0&lang=scss&":
/*!***********************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=style&index=0&lang=scss& ***!
  \***********************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=template&id=2c100fac&":
/*!********************************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=template&id=2c100fac& ***!
  \********************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_template_id_2c100fac___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticlePlaylistBox.vue?vue&type=template&id=2c100fac& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=template&id=2c100fac&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_template_id_2c100fac___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticlePlaylistBox_vue_vue_type_template_id_2c100fac___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!**************************************************************************************************!*\
  !*** ./client/component/engage_article/src/pages sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \**************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./ArticleView\": \"./client/component/engage_article/src/pages/ArticleView.vue\",\n\t\"./ArticleView.vue\": \"./client/component/engage_article/src/pages/ArticleView.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/engage_article/src/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/engage_article/src/pages_sync_^(?:(?");

/***/ }),

/***/ "./client/component/engage_article/src/pages/ArticleView.vue":
/*!*******************************************************************!*\
  !*** ./client/component/engage_article/src/pages/ArticleView.vue ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ArticleView_vue_vue_type_template_id_34cec6b4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ArticleView.vue?vue&type=template&id=34cec6b4& */ \"./client/component/engage_article/src/pages/ArticleView.vue?vue&type=template&id=34cec6b4&\");\n/* harmony import */ var _ArticleView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ArticleView.vue?vue&type=script&lang=js& */ \"./client/component/engage_article/src/pages/ArticleView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _ArticleView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ArticleView.vue?vue&type=style&index=0&lang=scss& */ \"./client/component/engage_article/src/pages/ArticleView.vue?vue&type=style&index=0&lang=scss&\");\n/* harmony import */ var _ArticleView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_ArticleView_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n/* harmony import */ var _ArticleView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/component/engage_article/src/pages/ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings\");\n\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__[\"default\"])(\n  _ArticleView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _ArticleView_vue_vue_type_template_id_34cec6b4___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _ArticleView_vue_vue_type_template_id_34cec6b4___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\n/* custom blocks */\n\nif (typeof _ArticleView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"] === 'function') Object(_ArticleView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_4__[\"default\"])(component)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/engage_article/src/pages/ArticleView.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/pages/ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!******************************************************************************************************************!*\
  !*** ./client/component/engage_article/src/pages/ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \******************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_lang_strings_loader.js??ref--6-0!../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings */ \"./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_lang_strings_loader_js_ref_6_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_custom_index_0_blockType_lang_strings__WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/pages/ArticleView.vue?vue&type=script&lang=js&":
/*!********************************************************************************************!*\
  !*** ./client/component/engage_article/src/pages/ArticleView.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleView.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/pages/ArticleView.vue?vue&type=style&index=0&lang=scss&":
/*!*****************************************************************************************************!*\
  !*** ./client/component/engage_article/src/pages/ArticleView.vue?vue&type=style&index=0&lang=scss& ***!
  \*****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("\n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/pages/ArticleView.vue?vue&type=template&id=34cec6b4&":
/*!**************************************************************************************************!*\
  !*** ./client/component/engage_article/src/pages/ArticleView.vue?vue&type=template&id=34cec6b4& ***!
  \**************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_template_id_34cec6b4___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--2-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./ArticleView.vue?vue&type=template&id=34cec6b4& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=template&id=34cec6b4&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_template_id_34cec6b4___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_2_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ArticleView_vue_vue_type_template_id_34cec6b4___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?");

/***/ }),

/***/ "./client/component/engage_article/src/tui.json":
/*!******************************************************!*\
  !*** ./client/component/engage_article/src/tui.json ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"engage_article\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"engage_article\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"engage_article\")\ntui._bundle.addModulesFromContext(\"engage_article/components\", __webpack_require__(\"./client/component/engage_article/src/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\ntui._bundle.addModulesFromContext(\"engage_article/pages\", __webpack_require__(\"./client/component/engage_article/src/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/component/engage_article/src/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_article\": [\n    \"addtoplaylist\",\n    \"numberwithinplaylist\",\n    \"timelessthanfive\",\n    \"timefivetoten\",\n    \"timemorethanten\"\n  ],\n  \"totara_engage\": [\n    \"more\",\n    \"share\",\n    \"numberofcomments\",\n    \"numberoflikes\",\n    \"numberofshares\",\n    \"time\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"totara_engage\": [\n    \"time\",\n    \"like\"\n  ],\n  \"engage_article\": [\n    \"timelessthanfive\",\n    \"timefivetoten\",\n    \"timemorethanten\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"moodle\": [\n    \"next\",\n    \"info\"\n  ],\n  \"totara_core\": [\n    \"save\"\n  ],\n  \"engage_article\": [\n    \"entertitle\",\n    \"entercontent\",\n    \"articletitle\",\n    \"content\",\n    \"createarticleshort\"\n  ],\n  \"totara_engage\": [\n    \"contributetip\",\n    \"contributetip_help\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_article\": [\n    \"articletitle\",\n    \"entertitle\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_article\": [\n    \"deletewarningmsg\",\n    \"deletewarningtitle\",\n    \"timelessthanfive\",\n    \"timefivetoten\",\n    \"timemorethanten\",\n    \"likearticle\",\n    \"removelikearticle\",\n    \"reportresource\",\n    \"error:reportresource\"\n  ],\n  \"moodle\": [\n    \"delete\"\n  ],\n  \"totara_reportedcontent\": [\n    \"reported\",\n    \"reported_failed\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_article\": [\n    \"appears_in\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./client/tooling/webpack/tui_lang_strings_loader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings":
/*!***************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=custom&index=0&blockType=lang-strings ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony default export */ __webpack_exports__[\"default\"] = (function (component) {\n        component.options.__langStrings = \n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n{\n  \"engage_article\": [\n    \"entercontent\",\n    \"entertitle\"\n  ]\n}\n;\n    });\n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?./client/tooling/webpack/tui_lang_strings_loader.js??ref--6-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

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

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_article_components_form_ArticleForm__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_article/components/form/ArticleForm */ \"engage_article/components/form/ArticleForm\");\n/* harmony import */ var engage_article_components_form_ArticleForm__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_form_ArticleForm__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/components/form/AccessForm */ \"totara_engage/components/form/AccessForm\");\n/* harmony import */ var totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_article_graphql_create_article__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_article/graphql/create_article */ \"./server/totara/engage/resources/article/webapi/ajax/create_article.graphql\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/mixins/container_mixin */ \"totara_engage/mixins/container_mixin\");\n/* harmony import */ var totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n // Graphql queries\n\n\n // Mixins\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ArticleForm: engage_article_components_form_ArticleForm__WEBPACK_IMPORTED_MODULE_0___default.a,\n    AccessForm: totara_engage_components_form_AccessForm__WEBPACK_IMPORTED_MODULE_1___default.a\n  },\n  mixins: [totara_engage_mixins_container_mixin__WEBPACK_IMPORTED_MODULE_4___default.a],\n  data: function data() {\n    return {\n      stage: 0,\n      maxStage: 1,\n      article: {\n        name: '',\n        content: null,\n        itemId: null\n      },\n      submitting: false\n    };\n  },\n  computed: {\n    privateDisabled: function privateDisabled() {\n      return this.containerValues.access ? !totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"AccessManager\"].isPrivate(this.containerValues.access) : false;\n    },\n    restrictedDisabled: function restrictedDisabled() {\n      return this.containerValues.access ? totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"AccessManager\"].isPublic(this.containerValues.access) : false;\n    }\n  },\n  methods: {\n    /**\n     * @param {String} content\n     * @param {String} name\n     * @param {String|Number} itemId\n     */\n    next: function next(_ref) {\n      var content = _ref.content,\n          name = _ref.name,\n          itemId = _ref.itemId;\n\n      if (this.stage < this.maxStage) {\n        this.stage += 1;\n      }\n\n      this.article.content = content;\n      this.article.name = name;\n      this.article.itemId = itemId;\n      this.$emit('change-title', this.stage);\n    },\n    back: function back() {\n      if (this.stage > 0) {\n        this.stage -= 1;\n      }\n\n      this.$emit('change-title', this.stage);\n    },\n\n    /**\n     * @param {String} access\n     * @param {Array} topics\n     * @param {Array} shares\n     * @param {String|null} timeView\n     */\n    done: function done(_ref2) {\n      var _this = this;\n\n      var access = _ref2.access,\n          topics = _ref2.topics,\n          timeView = _ref2.timeView,\n          shares = _ref2.shares;\n      this.submitting = true;\n      var params = {\n        content: this.article.content,\n        name: this.article.name,\n        access: access,\n        topics: topics.map(function (topic) {\n          return topic.id;\n        }),\n        shares: shares,\n        draft_id: this.article.itemId\n      };\n\n      if (timeView) {\n        params.timeview = timeView;\n      }\n\n      this.$apollo.mutate({\n        mutation: engage_article_graphql_create_article__WEBPACK_IMPORTED_MODULE_2__[\"default\"],\n        refetchQueries: ['totara_engage_contribution_cards', 'container_workspace_contribution_cards', 'container_workspace_shared_cards'],\n        variables: params,\n        update: function update(cache, _ref3) {\n          var id = _ref3.data.article.resource.id;\n\n          _this.$emit('done', {\n            resourceId: id\n          });\n        }\n      }).then(function () {\n        _this.$emit('cancel');\n      })[\"finally\"](function () {\n        _this.submitting = false;\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_engage_components_card_BaseCard__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_engage/components/card/BaseCard */ \"totara_engage/components/card/BaseCard\");\n/* harmony import */ var totara_engage_components_card_BaseCard__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_card_BaseCard__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_components_card_ImageHeader__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/components/card/ImageHeader */ \"totara_engage/components/card/ImageHeader\");\n/* harmony import */ var totara_engage_components_card_ImageHeader__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_card_ImageHeader__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var totara_engage_components_icons_StatIcon__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! totara_engage/components/icons/StatIcon */ \"totara_engage/components/icons/StatIcon\");\n/* harmony import */ var totara_engage_components_icons_StatIcon__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_icons_StatIcon__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var totara_engage_components_card_CardHeader__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/components/card/CardHeader */ \"totara_engage/components/card/CardHeader\");\n/* harmony import */ var totara_engage_components_card_CardHeader__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_card_CardHeader__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/buttons/ButtonIcon */ \"tui/components/buttons/ButtonIcon\");\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_components_icons_Share__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/components/icons/Share */ \"tui/components/icons/Share\");\n/* harmony import */ var tui_components_icons_Share__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Share__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_components_icons_AddToList__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/components/icons/AddToList */ \"tui/components/icons/AddToList\");\n/* harmony import */ var tui_components_icons_AddToList__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_AddToList__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! tui/components/icons/Like */ \"tui/components/icons/Like\");\n/* harmony import */ var tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var tui_components_icons_Comment__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! tui/components/icons/Comment */ \"tui/components/icons/Comment\");\n/* harmony import */ var tui_components_icons_Comment__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Comment__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var tui_components_icons_More__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! tui/components/icons/More */ \"tui/components/icons/More\");\n/* harmony import */ var tui_components_icons_More__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_More__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_10__);\n/* harmony import */ var totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! totara_engage/components/icons/access/computed/AccessIcon */ \"totara_engage/components/icons/access/computed/AccessIcon\");\n/* harmony import */ var totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_11__);\n/* harmony import */ var tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! tui/components/icons/Time */ \"tui/components/icons/Time\");\n/* harmony import */ var tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_12__);\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! totara_engage/components/buttons/BookmarkButton */ \"totara_engage/components/buttons/BookmarkButton\");\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_13__);\n/* harmony import */ var totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! totara_engage/graphql/update_bookmark */ \"./server/totara/engage/webapi/ajax/update_bookmark.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n\n\n\n\n // GraphQL\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ButtonIcon: tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_4___default.a,\n    BaseCard: totara_engage_components_card_BaseCard__WEBPACK_IMPORTED_MODULE_0___default.a,\n    ImageHeader: totara_engage_components_card_ImageHeader__WEBPACK_IMPORTED_MODULE_1___default.a,\n    StatIcon: totara_engage_components_icons_StatIcon__WEBPACK_IMPORTED_MODULE_2___default.a,\n    CardHeader: totara_engage_components_card_CardHeader__WEBPACK_IMPORTED_MODULE_3___default.a,\n    ShareIcon: tui_components_icons_Share__WEBPACK_IMPORTED_MODULE_5___default.a,\n    AddToListIcon: tui_components_icons_AddToList__WEBPACK_IMPORTED_MODULE_6___default.a,\n    MoreIcon: tui_components_icons_More__WEBPACK_IMPORTED_MODULE_9___default.a,\n    AccessIcon: totara_engage_components_icons_access_computed_AccessIcon__WEBPACK_IMPORTED_MODULE_11___default.a,\n    TimeIcon: tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_12___default.a,\n    BookmarkButton: totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_13___default.a\n  },\n  mixins: [totara_engage_index__WEBPACK_IMPORTED_MODULE_10__[\"cardMixin\"]],\n  data: function data() {\n    return {\n      // Assign the value to the inner child, as we do not want to mutate the prop.\n      innerBookmarked: this.bookmarked,\n      hovered: false,\n      statIcons: [{\n        type: 'reaction',\n        title: this.$str('numberoflikes', 'totara_engage', this.totalReactions),\n        icon: tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_7___default.a,\n        statNumber: this.totalReactions\n      }, {\n        type: 'comment',\n        title: this.$str('numberofcomments', 'totara_engage', this.totalComments),\n        icon: tui_components_icons_Comment__WEBPACK_IMPORTED_MODULE_8___default.a,\n        statNumber: this.totalComments\n      }],\n      actions: [{\n        alt: this.$str('addtoplaylist', 'engage_article'),\n        component: 'AddToListIcon'\n      }, {\n        alt: this.$str('share', 'totara_engage'),\n        component: 'ShareIcon'\n      }, {\n        alt: this.$str('more', 'totara_engage'),\n        component: 'MoreIcon'\n      }],\n      extraData: JSON.parse(this.extra)\n    };\n  },\n  computed: {\n    getTimeView: function getTimeView() {\n      if (totara_engage_index__WEBPACK_IMPORTED_MODULE_10__[\"TimeViewType\"].isLessThanFive(this.extraData.timeview)) {\n        return this.$str('timelessthanfive', 'engage_article');\n      } else if (totara_engage_index__WEBPACK_IMPORTED_MODULE_10__[\"TimeViewType\"].isFiveToTen(this.extraData.timeview)) {\n        return this.$str('timefivetoten', 'engage_article');\n      } else if (totara_engage_index__WEBPACK_IMPORTED_MODULE_10__[\"TimeViewType\"].isMoreThanTen(this.extraData.timeview)) {\n        return this.$str('timemorethanten', 'engage_article');\n      }\n\n      return null;\n    }\n  },\n  created: function created() {\n    // Add more stat icons depending on the visibility status of the card\n    if (totara_engage_index__WEBPACK_IMPORTED_MODULE_10__[\"AccessManager\"].isPublic(this.access)) {\n      this.statIcons = this.statIcons.concat([{\n        type: 'share',\n        title: this.$str('numberofshares', 'totara_engage', this.sharedbycount),\n        icon: tui_components_icons_Share__WEBPACK_IMPORTED_MODULE_5___default.a,\n        statNumber: this.sharedbycount\n      }, {\n        type: 'playlistUsage',\n        title: this.$str('numberwithinplaylist', 'engage_article', this.extraData.usage),\n        icon: tui_components_icons_AddToList__WEBPACK_IMPORTED_MODULE_6___default.a,\n        statNumber: this.extraData.usage\n      }]);\n    }\n  },\n  methods: {\n    $_handleHovered: function $_handleHovered(hovered) {\n      this.hovered = hovered;\n    },\n    updateBookmark: function updateBookmark() {\n      this.innerBookmarked = !this.innerBookmarked;\n      this.$apollo.mutate({\n        mutation: totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_14__[\"default\"],\n        refetchAll: false,\n        refetchQueries: ['totara_engage_contribution_cards'],\n        variables: {\n          itemid: this.instanceId,\n          component: 'engage_article',\n          bookmarked: this.innerBookmarked\n        }\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/card/Card */ \"tui/components/card/Card\");\n/* harmony import */ var tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/icons/Time */ \"tui/components/icons/Time\");\n/* harmony import */ var tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/icons/Like */ \"tui/components/icons/Like\");\n/* harmony import */ var tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! totara_engage/components/buttons/BookmarkButton */ \"totara_engage/components/buttons/BookmarkButton\");\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    BookmarkButton: totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_4___default.a,\n    Card: tui_components_card_Card__WEBPACK_IMPORTED_MODULE_0___default.a,\n    TimeIcon: tui_components_icons_Time__WEBPACK_IMPORTED_MODULE_1___default.a,\n    Like: tui_components_icons_Like__WEBPACK_IMPORTED_MODULE_2___default.a\n  },\n  props: {\n    resourceId: {\n      type: [Number, String],\n      required: true\n    },\n    name: {\n      type: String,\n      required: true\n    },\n    bookmarked: {\n      type: Boolean,\n      \"default\": false\n    },\n    image: {\n      type: String,\n      required: true\n    },\n    reactions: {\n      type: [Number, String],\n      required: true\n    },\n    timeview: {\n      type: String,\n      \"default\": ''\n    },\n    url: {\n      type: String,\n      required: true\n    }\n  },\n  data: function data() {\n    return {\n      innerBookmarked: this.bookmarked\n    };\n  },\n  computed: {\n    timeviewString: function timeviewString() {\n      if (totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"TimeViewType\"].isLessThanFive(this.timeview)) {\n        return this.$str('timelessthanfive', 'engage_article');\n      }\n\n      if (totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"TimeViewType\"].isFiveToTen(this.timeview)) {\n        return this.$str('timefivetoten', 'engage_article');\n      }\n\n      if (totara_engage_index__WEBPACK_IMPORTED_MODULE_3__[\"TimeViewType\"].isMoreThanTen(this.timeview)) {\n        return this.$str('timemorethanten', 'engage_article');\n      }\n\n      return '';\n    }\n  },\n  methods: {\n    handleClickBookmark: function handleClickBookmark() {\n      this.innerBookmarked = !this.innerBookmarked;\n      this.$emit('update', this.resourceId, this.innerBookmarked);\n    },\n    handleClickCard: function handleClickCard() {\n      window.location.href = this.url;\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_engage/components/form/InlineEditing */ \"totara_engage/components/form/InlineEditing\");\n/* harmony import */ var totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var engage_article_components_form_EditArticleContentForm__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! engage_article/components/form/EditArticleContentForm */ \"engage_article/components/form/EditArticleContentForm\");\n/* harmony import */ var engage_article_components_form_EditArticleContentForm__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_form_EditArticleContentForm__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_tui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/tui */ \"tui/tui\");\n/* harmony import */ var tui_tui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_tui__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var engage_article_graphql_update_article__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! engage_article/graphql/update_article */ \"./server/totara/engage/resources/article/webapi/ajax/update_article.graphql\");\n/* harmony import */ var engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! engage_article/graphql/get_article */ \"./server/totara/engage/resources/article/webapi/ajax/get_article.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n // GraphQL queries\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    InlineEditing: totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0___default.a,\n    EditArticleForm: engage_article_components_form_EditArticleContentForm__WEBPACK_IMPORTED_MODULE_1___default.a\n  },\n  props: {\n    /**\n     * For fetching the draft content of article.\n     */\n    resourceId: {\n      type: [String, Number],\n      required: true\n    },\n    content: {\n      type: String,\n      required: true\n    },\n    updateAble: {\n      type: Boolean,\n      required: true\n    }\n  },\n  data: function data() {\n    return {\n      editing: false,\n      submitting: false\n    };\n  },\n  mounted: function mounted() {\n    this.$_scan();\n  },\n  updated: function updated() {\n    this.$_scan();\n  },\n  methods: {\n    $_scan: function $_scan() {\n      var _this = this;\n\n      this.$nextTick().then(function () {\n        var content = _this.$refs.content;\n\n        if (!content) {\n          return;\n        }\n\n        tui_tui__WEBPACK_IMPORTED_MODULE_2___default.a.scan(content);\n      });\n    },\n\n    /**\n     *\n     * @param {String} content\n     * @param {Number} format\n     */\n    updateArticle: function updateArticle(_ref) {\n      var _this2 = this;\n\n      var content = _ref.content,\n          format = _ref.format,\n          itemId = _ref.itemId;\n      this.submitting = true;\n      this.$apollo.mutate({\n        mutation: engage_article_graphql_update_article__WEBPACK_IMPORTED_MODULE_3__[\"default\"],\n        variables: {\n          resourceid: this.resourceId,\n          content: content,\n          format: format,\n          draft_id: itemId\n        },\n\n        /**\n         *\n         * @param {DataProxy} proxy\n         * @param {Object} data\n         */\n        updateQuery: function updateQuery(proxy, data) {\n          proxy.writeQuery({\n            query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_4__[\"default\"],\n            variables: {\n              resourceid: _this2.resourceId\n            },\n            data: data\n          });\n        }\n      })[\"finally\"](function () {\n        _this2.editing = false;\n        _this2.submitting = false;\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_engage/components/form/InlineEditing */ \"totara_engage/components/form/InlineEditing\");\n/* harmony import */ var totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/components/buttons/BookmarkButton */ \"totara_engage/components/buttons/BookmarkButton\");\n/* harmony import */ var totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_article_components_form_EditArticleTitleForm__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_article/components/form/EditArticleTitleForm */ \"engage_article/components/form/EditArticleTitleForm\");\n/* harmony import */ var engage_article_components_form_EditArticleTitleForm__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_form_EditArticleTitleForm__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var engage_article_components_separator_ArticleSeparator__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! engage_article/components/separator/ArticleSeparator */ \"engage_article/components/separator/ArticleSeparator\");\n/* harmony import */ var engage_article_components_separator_ArticleSeparator__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_separator_ArticleSeparator__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var engage_article_graphql_update_article__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! engage_article/graphql/update_article */ \"./server/totara/engage/resources/article/webapi/ajax/update_article.graphql\");\n/* harmony import */ var engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_article/graphql/get_article */ \"./server/totara/engage/resources/article/webapi/ajax/get_article.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n // GraphQL queries\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    EditArticleTitleForm: engage_article_components_form_EditArticleTitleForm__WEBPACK_IMPORTED_MODULE_2___default.a,\n    InlineEditing: totara_engage_components_form_InlineEditing__WEBPACK_IMPORTED_MODULE_0___default.a,\n    BookmarkButton: totara_engage_components_buttons_BookmarkButton__WEBPACK_IMPORTED_MODULE_1___default.a,\n    ArticleSeparator: engage_article_components_separator_ArticleSeparator__WEBPACK_IMPORTED_MODULE_3___default.a\n  },\n  props: {\n    title: {\n      type: String,\n      required: true\n    },\n    updateAble: {\n      type: Boolean,\n      required: true\n    },\n    bookmarked: {\n      type: Boolean,\n      required: true\n    },\n    resourceId: {\n      type: [Number, String],\n      required: true\n    },\n    owned: {\n      type: Boolean,\n      required: true\n    }\n  },\n  data: function data() {\n    return {\n      editing: false,\n      submitting: false\n    };\n  },\n  methods: {\n    /**\n     *\n     * @param {String} title\n     */\n    updateTitle: function updateTitle(title) {\n      var _this = this;\n\n      this.submitting = true;\n      this.$apollo.mutate({\n        mutation: engage_article_graphql_update_article__WEBPACK_IMPORTED_MODULE_4__[\"default\"],\n        refetchAll: false,\n        variables: {\n          resourceid: this.resourceId,\n          name: title\n        },\n\n        /**\n         *\n         * @param {DataProxy} proxy\n         * @param {Object} data\n         */\n        updateQuery: function updateQuery(proxy, data) {\n          proxy.writeQuery({\n            query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_5__[\"default\"],\n            variables: {\n              resourceid: _this.resourceId\n            },\n            data: data\n          });\n        }\n      })[\"finally\"](function () {\n        _this.submitting = false;\n        _this.editing = false;\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ \"./node_modules/@babel/runtime/regenerator/index.js\");\n/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ \"./node_modules/@babel/runtime/helpers/asyncToGenerator.js\");\n/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/components/form/InputText */ \"tui/components/form/InputText\");\n/* harmony import */ var tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/buttons/ButtonGroup */ \"tui/components/buttons/ButtonGroup\");\n/* harmony import */ var tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/buttons/ButtonIcon */ \"tui/components/buttons/ButtonIcon\");\n/* harmony import */ var tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! tui/components/buttons/Cancel */ \"tui/components/buttons/Cancel\");\n/* harmony import */ var tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! tui/components/buttons/Button */ \"tui/components/buttons/Button\");\n/* harmony import */ var tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! tui/components/popover/Popover */ \"tui/components/popover/Popover\");\n/* harmony import */ var tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var tui_util__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! tui/util */ \"tui/util\");\n/* harmony import */ var tui_util__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(tui_util__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! editor_weka/components/Weka */ \"editor_weka/components/Weka\");\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! tui/components/form/Form */ \"tui/components/form/Form\");\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Form__WEBPACK_IMPORTED_MODULE_10__);\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! tui/components/form/FormRow */ \"tui/components/form/FormRow\");\n/* harmony import */ var tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_11__);\n/* harmony import */ var tui_components_icons_Info__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! tui/components/icons/Info */ \"tui/components/icons/Info\");\n/* harmony import */ var tui_components_icons_Info__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Info__WEBPACK_IMPORTED_MODULE_12__);\n/* harmony import */ var core_graphql_file_unused_draft_item_id__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! core/graphql/file_unused_draft_item_id */ \"./server/lib/webapi/ajax/file_unused_draft_item_id.graphql\");\n\n\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n\n // GraphQL queries\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ButtonIcon: tui_components_buttons_ButtonIcon__WEBPACK_IMPORTED_MODULE_4___default.a,\n    InputText: tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_2___default.a,\n    ButtonGroup: tui_components_buttons_ButtonGroup__WEBPACK_IMPORTED_MODULE_3___default.a,\n    Button: tui_components_buttons_Button__WEBPACK_IMPORTED_MODULE_6___default.a,\n    CancelButton: tui_components_buttons_Cancel__WEBPACK_IMPORTED_MODULE_5___default.a,\n    Popover: tui_components_popover_Popover__WEBPACK_IMPORTED_MODULE_7___default.a,\n    Weka: editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_9___default.a,\n    Form: tui_components_form_Form__WEBPACK_IMPORTED_MODULE_10___default.a,\n    FormRow: tui_components_form_FormRow__WEBPACK_IMPORTED_MODULE_11___default.a,\n    InfoIcon: tui_components_icons_Info__WEBPACK_IMPORTED_MODULE_12___default.a\n  },\n  props: {\n    articleContent: {\n      type: String,\n      \"default\": ''\n    },\n    articleName: {\n      type: String,\n      required: true\n    }\n  },\n  data: function data() {\n    return {\n      // Caching the name separately\n      name: this.articleName,\n      content: {\n        // Default state of editor\n        doc: null,\n        isEmpty: true\n      },\n      draftId: null,\n      submitting: false\n    };\n  },\n  computed: {\n    disabled: function disabled() {\n      return this.name.length === 0 || this.content.isEmpty;\n    }\n  },\n  watch: {\n    articleContent: {\n      immediate: true,\n\n      /**\n       *\n       * @param {String} value\n       */\n      handler: function handler(value) {\n        if (!value) {\n          return;\n        }\n\n        try {\n          this.content.doc = JSON.parse(value);\n        } catch (e) {\n          // Silenced any invalid json string.\n          this.content.doc = null;\n        }\n      }\n    }\n  },\n  mounted: function mounted() {\n    var _this = this;\n\n    return _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default()( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {\n      return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {\n        while (1) {\n          switch (_context.prev = _context.next) {\n            case 0:\n              _context.next = 2;\n              return _this.$_loadDraftId();\n\n            case 2:\n            case \"end\":\n              return _context.stop();\n          }\n        }\n      }, _callee);\n    }))();\n  },\n  methods: {\n    /**\n     *\n     * @param {Object} opt\n     */\n    handleUpdate: function handleUpdate(opt) {\n      this.$_readJson(opt);\n    },\n    $_loadDraftId: function $_loadDraftId() {\n      var _this2 = this;\n\n      return _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default()( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee2() {\n        var _yield$_this2$$apollo, item_id;\n\n        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee2$(_context2) {\n          while (1) {\n            switch (_context2.prev = _context2.next) {\n              case 0:\n                _context2.next = 2;\n                return _this2.$apollo.mutate({\n                  mutation: core_graphql_file_unused_draft_item_id__WEBPACK_IMPORTED_MODULE_13__[\"default\"]\n                });\n\n              case 2:\n                _yield$_this2$$apollo = _context2.sent;\n                item_id = _yield$_this2$$apollo.data.item_id;\n                _this2.draftId = item_id;\n\n              case 5:\n              case \"end\":\n                return _context2.stop();\n            }\n          }\n        }, _callee2);\n      }))();\n    },\n    $_readJson: Object(tui_util__WEBPACK_IMPORTED_MODULE_8__[\"debounce\"])(\n    /**\n     *\n     * @param {Object} opt\n     */\n    function (opt) {\n      this.content.doc = opt.getJSON();\n      this.content.isEmpty = opt.isEmpty();\n    }, 250, {\n      perArgs: false\n    }),\n    submit: function submit() {\n      var params = {\n        name: this.name,\n        content: JSON.stringify(this.content.doc),\n        itemId: this.draftId\n      };\n      this.$emit('next', params);\n      this.$_loadDraftId();\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/Form */ \"tui/components/form/Form\");\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! editor_weka/components/Weka */ \"editor_weka/components/Weka\");\n/* harmony import */ var editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_util__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/util */ \"tui/util\");\n/* harmony import */ var tui_util__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_util__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/components/buttons/DoneCancelGroup */ \"totara_engage/components/buttons/DoneCancelGroup\");\n/* harmony import */ var totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/loader/Loader */ \"tui/components/loader/Loader\");\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var engage_article_graphql_draft_item__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! engage_article/graphql/draft_item */ \"./server/totara/engage/resources/article/webapi/ajax/draft_item.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n // GraphQL queries\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    Form: tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default.a,\n    Weka: editor_weka_components_Weka__WEBPACK_IMPORTED_MODULE_1___default.a,\n    DoneCancelGroup: totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_3___default.a,\n    Loader: tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_4___default.a\n  },\n  props: {\n    resourceId: {\n      type: [String, Number],\n      required: true\n    },\n    submitting: Boolean\n  },\n  apollo: {\n    draft: {\n      query: engage_article_graphql_draft_item__WEBPACK_IMPORTED_MODULE_5__[\"default\"],\n      fetchPolicy: 'network-only',\n      variables: function variables() {\n        return {\n          resourceid: this.resourceId\n        };\n      },\n      result: function result(_ref) {\n        var content = _ref.data.draft.content;\n\n        if (content) {\n          this.content.doc = JSON.parse(content);\n          this.content.empty = false;\n        }\n      }\n    }\n  },\n  data: function data() {\n    return {\n      draft: {},\n      editorMounted: false,\n      content: {\n        doc: null,\n        empty: true\n      }\n    };\n  },\n  methods: {\n    $_readJSON: Object(tui_util__WEBPACK_IMPORTED_MODULE_2__[\"debounce\"])(\n    /**\n     * @param {{\n     *   getJSON: Function,\n     *   isEmpty: Function,\n     *   getFileStorageItemId: Function,\n     * }} option\n     */\n    function (option) {\n      this.content.doc = option.getJSON();\n      this.content.empty = option.isEmpty();\n      this.content.itemId = option.getFileStorageItemId();\n    }, 100),\n\n    /**\n     *\n     * @param {Object} option\n     */\n    handleUpdate: function handleUpdate(option) {\n      this.$_readJSON(option);\n    },\n    submit: function submit() {\n      var params = {\n        resourceId: this.resourceId,\n        content: JSON.stringify(this.content.doc),\n        // This seems to be redundant, but lets keep it here, who know in the future, we\n        format: this.draft.format,\n        itemId: this.content.itemId\n      };\n      this.$emit('submit', params);\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleContentForm.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/form/Form */ \"tui/components/form/Form\");\n/* harmony import */ var tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/form/InputText */ \"tui/components/form/InputText\");\n/* harmony import */ var tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! totara_engage/components/buttons/DoneCancelGroup */ \"totara_engage/components/buttons/DoneCancelGroup\");\n/* harmony import */ var totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_2__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    DoneCancelGroup: totara_engage_components_buttons_DoneCancelGroup__WEBPACK_IMPORTED_MODULE_2___default.a,\n    Form: tui_components_form_Form__WEBPACK_IMPORTED_MODULE_0___default.a,\n    InputText: tui_components_form_InputText__WEBPACK_IMPORTED_MODULE_1___default.a\n  },\n  props: {\n    submitting: {\n      type: Boolean,\n      \"default\": false\n    },\n    title: {\n      type: String,\n      required: true\n    }\n  },\n  data: function data() {\n    return {\n      // Caching the inner title, as we will emit the event to update it.\n      innerTitle: this.title\n    };\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ \"./node_modules/@babel/runtime/regenerator/index.js\");\n/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ \"./node_modules/@babel/runtime/helpers/asyncToGenerator.js\");\n/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var tui_apollo_client__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! tui/apollo_client */ \"tui/apollo_client\");\n/* harmony import */ var tui_apollo_client__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(tui_apollo_client__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! tui/components/loader/Loader */ \"tui/components/loader/Loader\");\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! tui/components/modal/ModalPresenter */ \"tui/components/modal/ModalPresenter\");\n/* harmony import */ var tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var totara_comment_components_box_SidePanelCommentBox__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! totara_comment/components/box/SidePanelCommentBox */ \"totara_comment/components/box/SidePanelCommentBox\");\n/* harmony import */ var totara_comment_components_box_SidePanelCommentBox__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(totara_comment_components_box_SidePanelCommentBox__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! totara_engage/components/sidepanel/access/AccessDisplay */ \"totara_engage/components/sidepanel/access/AccessDisplay\");\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_6__);\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_engage/components/sidepanel/access/AccessSetting */ \"totara_engage/components/sidepanel/access/AccessSetting\");\n/* harmony import */ var totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var totara_engage_components_sidepanel_EngageSidePanel__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! totara_engage/components/sidepanel/EngageSidePanel */ \"totara_engage/components/sidepanel/EngageSidePanel\");\n/* harmony import */ var totara_engage_components_sidepanel_EngageSidePanel__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_EngageSidePanel__WEBPACK_IMPORTED_MODULE_8__);\n/* harmony import */ var totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! totara_engage/components/modal/EngageWarningModal */ \"totara_engage/components/modal/EngageWarningModal\");\n/* harmony import */ var totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_9__);\n/* harmony import */ var totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! totara_engage/components/sidepanel/media/MediaSetting */ \"totara_engage/components/sidepanel/media/MediaSetting\");\n/* harmony import */ var totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_10__);\n/* harmony import */ var tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! tui/components/profile/MiniProfileCard */ \"tui/components/profile/MiniProfileCard\");\n/* harmony import */ var tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_11__);\n/* harmony import */ var tui_components_dropdown_DropdownItem__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! tui/components/dropdown/DropdownItem */ \"tui/components/dropdown/DropdownItem\");\n/* harmony import */ var tui_components_dropdown_DropdownItem__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(tui_components_dropdown_DropdownItem__WEBPACK_IMPORTED_MODULE_12__);\n/* harmony import */ var engage_article_components_sidepanel_content_ArticlePlaylistBox__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! engage_article/components/sidepanel/content/ArticlePlaylistBox */ \"engage_article/components/sidepanel/content/ArticlePlaylistBox\");\n/* harmony import */ var engage_article_components_sidepanel_content_ArticlePlaylistBox__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_sidepanel_content_ArticlePlaylistBox__WEBPACK_IMPORTED_MODULE_13__);\n/* harmony import */ var engage_article_components_sidepanel_Related__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! engage_article/components/sidepanel/Related */ \"engage_article/components/sidepanel/Related\");\n/* harmony import */ var engage_article_components_sidepanel_Related__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_sidepanel_Related__WEBPACK_IMPORTED_MODULE_14__);\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! tui/notifications */ \"tui/notifications\");\n/* harmony import */ var tui_notifications__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(tui_notifications__WEBPACK_IMPORTED_MODULE_15__);\n/* harmony import */ var engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! engage_article/graphql/get_article */ \"./server/totara/engage/resources/article/webapi/ajax/get_article.graphql\");\n/* harmony import */ var engage_article_graphql_update_article__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! engage_article/graphql/update_article */ \"./server/totara/engage/resources/article/webapi/ajax/update_article.graphql\");\n/* harmony import */ var engage_article_graphql_delete_article__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! engage_article/graphql/delete_article */ \"./server/totara/engage/resources/article/webapi/ajax/delete_article.graphql\");\n/* harmony import */ var totara_engage_graphql_advanced_features__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! totara_engage/graphql/advanced_features */ \"./server/totara/engage/webapi/ajax/advanced_features.graphql\");\n/* harmony import */ var totara_reportedcontent_graphql_create_review__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! totara_reportedcontent/graphql/create_review */ \"./server/totara/reportedcontent/webapi/ajax/create_review.graphql\");\n\n\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n\n\n\n\n\n\n\n\n // GraphQL queries\n\n\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AccessDisplay: totara_engage_components_sidepanel_access_AccessDisplay__WEBPACK_IMPORTED_MODULE_6___default.a,\n    AccessSetting: totara_engage_components_sidepanel_access_AccessSetting__WEBPACK_IMPORTED_MODULE_7___default.a,\n    ArticlePlaylistBox: engage_article_components_sidepanel_content_ArticlePlaylistBox__WEBPACK_IMPORTED_MODULE_13___default.a,\n    EngageSidePanel: totara_engage_components_sidepanel_EngageSidePanel__WEBPACK_IMPORTED_MODULE_8___default.a,\n    EngageWarningModal: totara_engage_components_modal_EngageWarningModal__WEBPACK_IMPORTED_MODULE_9___default.a,\n    Loader: tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_3___default.a,\n    MediaSetting: totara_engage_components_sidepanel_media_MediaSetting__WEBPACK_IMPORTED_MODULE_10___default.a,\n    ModalPresenter: tui_components_modal_ModalPresenter__WEBPACK_IMPORTED_MODULE_4___default.a,\n    Related: engage_article_components_sidepanel_Related__WEBPACK_IMPORTED_MODULE_14___default.a,\n    SidePanelCommentBox: totara_comment_components_box_SidePanelCommentBox__WEBPACK_IMPORTED_MODULE_5___default.a,\n    MiniProfileCard: tui_components_profile_MiniProfileCard__WEBPACK_IMPORTED_MODULE_11___default.a,\n    DropdownItem: tui_components_dropdown_DropdownItem__WEBPACK_IMPORTED_MODULE_12___default.a\n  },\n  props: {\n    resourceId: {\n      type: [Number, String],\n      required: true\n    }\n  },\n  apollo: {\n    article: {\n      query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_16__[\"default\"],\n      variables: function variables() {\n        return {\n          id: this.resourceId\n        };\n      }\n    },\n    features: {\n      query: totara_engage_graphql_advanced_features__WEBPACK_IMPORTED_MODULE_19__[\"default\"]\n    }\n  },\n  data: function data() {\n    return {\n      article: {},\n      submitting: false,\n      openModalFromButtonLabel: false,\n      openModalFromAction: false,\n      features: {}\n    };\n  },\n  computed: {\n    user: function user() {\n      if (!this.article.resource || !this.article.resource.user) {\n        return {};\n      }\n\n      return this.article.resource.user;\n    },\n    sharedByCount: function sharedByCount() {\n      return this.article.sharedByCount;\n    },\n    likeButtonLabel: function likeButtonLabel() {\n      if (this.article.reacted) {\n        return this.$str('removelikearticle', 'engage_article', this.article.resource.name);\n      }\n\n      return this.$str('likearticle', 'engage_article', this.article.resource.name);\n    },\n    featureRecommenders: function featureRecommenders() {\n      return this.features && this.features.recommenders;\n    }\n  },\n  methods: {\n    /**\n     * Updates Access for this article\n     *\n     * @param {String} access The access level of the article\n     * @param {Array} topics Topics that this article should be shared with\n     * @param {Array} shares An array of group id's that this article is shared with\n     */\n    updateAccess: function updateAccess(_ref) {\n      var _this = this;\n\n      var access = _ref.access,\n          topics = _ref.topics,\n          shares = _ref.shares,\n          timeView = _ref.timeView;\n      this.submitting = true;\n      this.$apollo.mutate({\n        mutation: engage_article_graphql_update_article__WEBPACK_IMPORTED_MODULE_17__[\"default\"],\n        refetchAll: false,\n        variables: {\n          resourceid: this.resourceId,\n          access: access,\n          topics: topics.map(function (_ref2) {\n            var id = _ref2.id;\n            return id;\n          }),\n          shares: shares,\n          timeview: timeView\n        },\n        update: function update(proxy, _ref3) {\n          var data = _ref3.data;\n          proxy.writeQuery({\n            query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_16__[\"default\"],\n            variables: {\n              id: _this.resourceId\n            },\n            data: data\n          });\n        }\n      })[\"finally\"](function () {\n        _this.submitting = false;\n      });\n    },\n    handleDelete: function handleDelete() {\n      var _this2 = this;\n\n      this.$apollo.mutate({\n        mutation: engage_article_graphql_delete_article__WEBPACK_IMPORTED_MODULE_18__[\"default\"],\n        variables: {\n          resourceid: this.resourceId\n        },\n        refetchAll: false\n      }).then(function (_ref4) {\n        var data = _ref4.data;\n\n        if (data.result) {\n          _this2.$children.openModal = false;\n          window.location.href = _this2.$url('/totara/engage/your_resources.php');\n        }\n      });\n    },\n\n    /**\n     *\n     * @param {Boolean} status\n     */\n    updateLikeStatus: function updateLikeStatus(status) {\n      var _apolloClient$readQue = tui_apollo_client__WEBPACK_IMPORTED_MODULE_2___default.a.readQuery({\n        query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_16__[\"default\"],\n        variables: {\n          id: this.resourceId\n        }\n      }),\n          article = _apolloClient$readQue.article;\n\n      article = Object.assign({}, article);\n      article.reacted = status;\n      tui_apollo_client__WEBPACK_IMPORTED_MODULE_2___default.a.writeQuery({\n        query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_16__[\"default\"],\n        variables: {\n          id: this.resourceId\n        },\n        data: {\n          article: article\n        }\n      });\n    },\n\n    /**\n     * Report the attached resource\n     * @returns {Promise<void>}\n     */\n    reportResource: function reportResource() {\n      var _this3 = this;\n\n      return _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1___default()( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {\n        var response;\n        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {\n          while (1) {\n            switch (_context.prev = _context.next) {\n              case 0:\n                if (!_this3.submitting) {\n                  _context.next = 2;\n                  break;\n                }\n\n                return _context.abrupt(\"return\");\n\n              case 2:\n                _this3.submitting = true;\n                _context.prev = 3;\n                _context.next = 6;\n                return _this3.$apollo.mutate({\n                  mutation: totara_reportedcontent_graphql_create_review__WEBPACK_IMPORTED_MODULE_20__[\"default\"],\n                  refetchAll: false,\n                  variables: {\n                    component: 'engage_article',\n                    area: '',\n                    item_id: _this3.resourceId,\n                    url: window.location.href\n                  }\n                }).then(function (response) {\n                  return response.data.review;\n                });\n\n              case 6:\n                response = _context.sent;\n\n                if (!response.success) {\n                  _context.next = 12;\n                  break;\n                }\n\n                _context.next = 10;\n                return Object(tui_notifications__WEBPACK_IMPORTED_MODULE_15__[\"notify\"])({\n                  message: _this3.$str('reported', 'totara_reportedcontent'),\n                  type: 'success'\n                });\n\n              case 10:\n                _context.next = 14;\n                break;\n\n              case 12:\n                _context.next = 14;\n                return Object(tui_notifications__WEBPACK_IMPORTED_MODULE_15__[\"notify\"])({\n                  message: _this3.$str('reported_failed', 'totara_reportedcontent'),\n                  type: 'error'\n                });\n\n              case 14:\n                _context.next = 20;\n                break;\n\n              case 16:\n                _context.prev = 16;\n                _context.t0 = _context[\"catch\"](3);\n                _context.next = 20;\n                return Object(tui_notifications__WEBPACK_IMPORTED_MODULE_15__[\"notify\"])({\n                  message: _this3.$str('error:reportresource', 'engage_article'),\n                  type: 'error'\n                });\n\n              case 20:\n                _context.prev = 20;\n                _this3.submitting = false;\n                return _context.finish(20);\n\n              case 23:\n              case \"end\":\n                return _context.stop();\n            }\n          }\n        }, _callee, null, [[3, 16, 20, 23]]);\n      }))();\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var engage_article_components_card_RelatedCard__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! engage_article/components/card/RelatedCard */ \"engage_article/components/card/RelatedCard\");\n/* harmony import */ var engage_article_components_card_RelatedCard__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_card_RelatedCard__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var ml_recommender_graphql_get_recommended_articles__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ml_recommender/graphql/get_recommended_articles */ \"./server/ml/recommender/webapi/ajax/get_recommended_articles.graphql\");\n/* harmony import */ var totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! totara_engage/graphql/update_bookmark */ \"./server/totara/engage/webapi/ajax/update_bookmark.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    RelatedCard: engage_article_components_card_RelatedCard__WEBPACK_IMPORTED_MODULE_0___default.a\n  },\n  props: {\n    resourceId: {\n      type: [Number, String],\n      required: true\n    }\n  },\n  data: function data() {\n    return {\n      articles: []\n    };\n  },\n  mounted: function mounted() {\n    this.getRecommendations();\n  },\n  methods: {\n    getRecommendations: function getRecommendations() {\n      var _this = this;\n\n      this.$apollo.query({\n        query: ml_recommender_graphql_get_recommended_articles__WEBPACK_IMPORTED_MODULE_2__[\"default\"],\n        refetchAll: false,\n        variables: {\n          article_id: this.resourceId,\n          source: totara_engage_index__WEBPACK_IMPORTED_MODULE_1__[\"UrlSourceType\"].article(this.resourceId)\n        }\n      }).then(function (_ref) {\n        var data = _ref.data;\n        _this.articles = data.articles.map(function (item) {\n          var bookmarked = item.bookmarked,\n              extra = item.extra,\n              name = item.name,\n              instanceid = item.instanceid,\n              reactions = item.reactions,\n              url = item.url;\n\n          var _JSON$parse = JSON.parse(extra),\n              image = _JSON$parse.image,\n              timeview = _JSON$parse.timeview;\n\n          return {\n            bookmarked: bookmarked,\n            instanceid: instanceid,\n            image: image,\n            name: name,\n            reactions: reactions,\n            timeview: timeview,\n            url: url\n          };\n        });\n      });\n    },\n    update: function update(resourceId, bookmarked) {\n      this.$apollo.mutate({\n        mutation: totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_3__[\"default\"],\n        refetchAll: false,\n        variables: {\n          itemid: resourceId,\n          component: 'engage_article',\n          bookmarked: bookmarked\n        }\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_playlist_components_box_ResourcePlaylistBox__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_playlist/components/box/ResourcePlaylistBox */ \"totara_playlist/components/box/ResourcePlaylistBox\");\n/* harmony import */ var totara_playlist_components_box_ResourcePlaylistBox__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_playlist_components_box_ResourcePlaylistBox__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/icons/Loading */ \"tui/components/icons/Loading\");\n/* harmony import */ var tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! totara_engage/index */ \"totara_engage/index\");\n/* harmony import */ var totara_engage_index__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(totara_engage_index__WEBPACK_IMPORTED_MODULE_2__);\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ResourcePlaylistBox: totara_playlist_components_box_ResourcePlaylistBox__WEBPACK_IMPORTED_MODULE_0___default.a,\n    Loading: tui_components_icons_Loading__WEBPACK_IMPORTED_MODULE_1___default.a\n  },\n  props: {\n    resourceId: {\n      type: [String, Number],\n      required: true\n    }\n  },\n  data: function data() {\n    return {\n      loading: false,\n      show: true\n    };\n  },\n  computed: {\n    urlSource: function urlSource() {\n      return totara_engage_index__WEBPACK_IMPORTED_MODULE_2__[\"UrlSourceType\"].article(this.resourceId);\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/layouts/LayoutOneColumnContentWithSidePanel */ \"tui/components/layouts/LayoutOneColumnContentWithSidePanel\");\n/* harmony import */ var tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/loader/Loader */ \"tui/components/loader/Loader\");\n/* harmony import */ var tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var engage_article_components_sidepanel_ArticleSidePanel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! engage_article/components/sidepanel/ArticleSidePanel */ \"engage_article/components/sidepanel/ArticleSidePanel\");\n/* harmony import */ var engage_article_components_sidepanel_ArticleSidePanel__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_sidepanel_ArticleSidePanel__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var engage_article_components_content_ArticleContent__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! engage_article/components/content/ArticleContent */ \"engage_article/components/content/ArticleContent\");\n/* harmony import */ var engage_article_components_content_ArticleContent__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_content_ArticleContent__WEBPACK_IMPORTED_MODULE_3__);\n/* harmony import */ var engage_article_components_content_ArticleTitle__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! engage_article/components/content/ArticleTitle */ \"engage_article/components/content/ArticleTitle\");\n/* harmony import */ var engage_article_components_content_ArticleTitle__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(engage_article_components_content_ArticleTitle__WEBPACK_IMPORTED_MODULE_4__);\n/* harmony import */ var totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! totara_engage/components/header/ResourceNavigationBar */ \"totara_engage/components/header/ResourceNavigationBar\");\n/* harmony import */ var totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! engage_article/graphql/get_article */ \"./server/totara/engage/resources/article/webapi/ajax/get_article.graphql\");\n/* harmony import */ var totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! totara_engage/graphql/update_bookmark */ \"./server/totara/engage/webapi/ajax/update_bookmark.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n\n\n\n // GraphQL\n\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    ArticleTitle: engage_article_components_content_ArticleTitle__WEBPACK_IMPORTED_MODULE_4___default.a,\n    ArticleSidePanel: engage_article_components_sidepanel_ArticleSidePanel__WEBPACK_IMPORTED_MODULE_2___default.a,\n    ArticleContent: engage_article_components_content_ArticleContent__WEBPACK_IMPORTED_MODULE_3___default.a,\n    Layout: tui_components_layouts_LayoutOneColumnContentWithSidePanel__WEBPACK_IMPORTED_MODULE_0___default.a,\n    Loader: tui_components_loader_Loader__WEBPACK_IMPORTED_MODULE_1___default.a,\n    ResourceNavigationBar: totara_engage_components_header_ResourceNavigationBar__WEBPACK_IMPORTED_MODULE_5___default.a\n  },\n  props: {\n    resourceId: {\n      type: Number,\n      required: true\n    },\n    backButton: {\n      type: Object,\n      required: false\n    },\n    navigationButtons: {\n      type: Object,\n      required: false\n    }\n  },\n  data: function data() {\n    return {\n      article: {},\n      bookmarked: false\n    };\n  },\n  computed: {\n    articleName: function articleName() {\n      if (!this.article.resource || !this.article.resource.name) {\n        return '';\n      }\n\n      return this.article.resource.name;\n    }\n  },\n  apollo: {\n    article: {\n      query: engage_article_graphql_get_article__WEBPACK_IMPORTED_MODULE_6__[\"default\"],\n      variables: function variables() {\n        return {\n          id: this.resourceId\n        };\n      },\n      result: function result(_ref) {\n        var article = _ref.data.article;\n        this.bookmarked = article.bookmarked;\n      }\n    }\n  },\n  methods: {\n    updateBookmark: function updateBookmark() {\n      this.bookmarked = !this.bookmarked;\n      this.$apollo.mutate({\n        mutation: totara_engage_graphql_update_bookmark__WEBPACK_IMPORTED_MODULE_7__[\"default\"],\n        refetchAll: false,\n        variables: {\n          itemid: this.resourceId,\n          component: 'engage_article',\n          bookmarked: this.bookmarked\n        }\n      });\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/regenerator-runtime/runtime.js":
/*!*****************************************************!*\
  !*** ./node_modules/regenerator-runtime/runtime.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("/**\n * Copyright (c) 2014-present, Facebook, Inc.\n *\n * This source code is licensed under the MIT license found in the\n * LICENSE file in the root directory of this source tree.\n */\n\nvar runtime = (function (exports) {\n  \"use strict\";\n\n  var Op = Object.prototype;\n  var hasOwn = Op.hasOwnProperty;\n  var undefined; // More compressible than void 0.\n  var $Symbol = typeof Symbol === \"function\" ? Symbol : {};\n  var iteratorSymbol = $Symbol.iterator || \"@@iterator\";\n  var asyncIteratorSymbol = $Symbol.asyncIterator || \"@@asyncIterator\";\n  var toStringTagSymbol = $Symbol.toStringTag || \"@@toStringTag\";\n\n  function wrap(innerFn, outerFn, self, tryLocsList) {\n    // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.\n    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;\n    var generator = Object.create(protoGenerator.prototype);\n    var context = new Context(tryLocsList || []);\n\n    // The ._invoke method unifies the implementations of the .next,\n    // .throw, and .return methods.\n    generator._invoke = makeInvokeMethod(innerFn, self, context);\n\n    return generator;\n  }\n  exports.wrap = wrap;\n\n  // Try/catch helper to minimize deoptimizations. Returns a completion\n  // record like context.tryEntries[i].completion. This interface could\n  // have been (and was previously) designed to take a closure to be\n  // invoked without arguments, but in all the cases we care about we\n  // already have an existing method we want to call, so there's no need\n  // to create a new function object. We can even get away with assuming\n  // the method takes exactly one argument, since that happens to be true\n  // in every case, so we don't have to touch the arguments object. The\n  // only additional allocation required is the completion record, which\n  // has a stable shape and so hopefully should be cheap to allocate.\n  function tryCatch(fn, obj, arg) {\n    try {\n      return { type: \"normal\", arg: fn.call(obj, arg) };\n    } catch (err) {\n      return { type: \"throw\", arg: err };\n    }\n  }\n\n  var GenStateSuspendedStart = \"suspendedStart\";\n  var GenStateSuspendedYield = \"suspendedYield\";\n  var GenStateExecuting = \"executing\";\n  var GenStateCompleted = \"completed\";\n\n  // Returning this object from the innerFn has the same effect as\n  // breaking out of the dispatch switch statement.\n  var ContinueSentinel = {};\n\n  // Dummy constructor functions that we use as the .constructor and\n  // .constructor.prototype properties for functions that return Generator\n  // objects. For full spec compliance, you may wish to configure your\n  // minifier not to mangle the names of these two functions.\n  function Generator() {}\n  function GeneratorFunction() {}\n  function GeneratorFunctionPrototype() {}\n\n  // This is a polyfill for %IteratorPrototype% for environments that\n  // don't natively support it.\n  var IteratorPrototype = {};\n  IteratorPrototype[iteratorSymbol] = function () {\n    return this;\n  };\n\n  var getProto = Object.getPrototypeOf;\n  var NativeIteratorPrototype = getProto && getProto(getProto(values([])));\n  if (NativeIteratorPrototype &&\n      NativeIteratorPrototype !== Op &&\n      hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) {\n    // This environment has a native %IteratorPrototype%; use it instead\n    // of the polyfill.\n    IteratorPrototype = NativeIteratorPrototype;\n  }\n\n  var Gp = GeneratorFunctionPrototype.prototype =\n    Generator.prototype = Object.create(IteratorPrototype);\n  GeneratorFunction.prototype = Gp.constructor = GeneratorFunctionPrototype;\n  GeneratorFunctionPrototype.constructor = GeneratorFunction;\n  GeneratorFunctionPrototype[toStringTagSymbol] =\n    GeneratorFunction.displayName = \"GeneratorFunction\";\n\n  // Helper for defining the .next, .throw, and .return methods of the\n  // Iterator interface in terms of a single ._invoke method.\n  function defineIteratorMethods(prototype) {\n    [\"next\", \"throw\", \"return\"].forEach(function(method) {\n      prototype[method] = function(arg) {\n        return this._invoke(method, arg);\n      };\n    });\n  }\n\n  exports.isGeneratorFunction = function(genFun) {\n    var ctor = typeof genFun === \"function\" && genFun.constructor;\n    return ctor\n      ? ctor === GeneratorFunction ||\n        // For the native GeneratorFunction constructor, the best we can\n        // do is to check its .name property.\n        (ctor.displayName || ctor.name) === \"GeneratorFunction\"\n      : false;\n  };\n\n  exports.mark = function(genFun) {\n    if (Object.setPrototypeOf) {\n      Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);\n    } else {\n      genFun.__proto__ = GeneratorFunctionPrototype;\n      if (!(toStringTagSymbol in genFun)) {\n        genFun[toStringTagSymbol] = \"GeneratorFunction\";\n      }\n    }\n    genFun.prototype = Object.create(Gp);\n    return genFun;\n  };\n\n  // Within the body of any async function, `await x` is transformed to\n  // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test\n  // `hasOwn.call(value, \"__await\")` to determine if the yielded value is\n  // meant to be awaited.\n  exports.awrap = function(arg) {\n    return { __await: arg };\n  };\n\n  function AsyncIterator(generator, PromiseImpl) {\n    function invoke(method, arg, resolve, reject) {\n      var record = tryCatch(generator[method], generator, arg);\n      if (record.type === \"throw\") {\n        reject(record.arg);\n      } else {\n        var result = record.arg;\n        var value = result.value;\n        if (value &&\n            typeof value === \"object\" &&\n            hasOwn.call(value, \"__await\")) {\n          return PromiseImpl.resolve(value.__await).then(function(value) {\n            invoke(\"next\", value, resolve, reject);\n          }, function(err) {\n            invoke(\"throw\", err, resolve, reject);\n          });\n        }\n\n        return PromiseImpl.resolve(value).then(function(unwrapped) {\n          // When a yielded Promise is resolved, its final value becomes\n          // the .value of the Promise<{value,done}> result for the\n          // current iteration.\n          result.value = unwrapped;\n          resolve(result);\n        }, function(error) {\n          // If a rejected Promise was yielded, throw the rejection back\n          // into the async generator function so it can be handled there.\n          return invoke(\"throw\", error, resolve, reject);\n        });\n      }\n    }\n\n    var previousPromise;\n\n    function enqueue(method, arg) {\n      function callInvokeWithMethodAndArg() {\n        return new PromiseImpl(function(resolve, reject) {\n          invoke(method, arg, resolve, reject);\n        });\n      }\n\n      return previousPromise =\n        // If enqueue has been called before, then we want to wait until\n        // all previous Promises have been resolved before calling invoke,\n        // so that results are always delivered in the correct order. If\n        // enqueue has not been called before, then it is important to\n        // call invoke immediately, without waiting on a callback to fire,\n        // so that the async generator function has the opportunity to do\n        // any necessary setup in a predictable way. This predictability\n        // is why the Promise constructor synchronously invokes its\n        // executor callback, and why async functions synchronously\n        // execute code before the first await. Since we implement simple\n        // async functions in terms of async generators, it is especially\n        // important to get this right, even though it requires care.\n        previousPromise ? previousPromise.then(\n          callInvokeWithMethodAndArg,\n          // Avoid propagating failures to Promises returned by later\n          // invocations of the iterator.\n          callInvokeWithMethodAndArg\n        ) : callInvokeWithMethodAndArg();\n    }\n\n    // Define the unified helper method that is used to implement .next,\n    // .throw, and .return (see defineIteratorMethods).\n    this._invoke = enqueue;\n  }\n\n  defineIteratorMethods(AsyncIterator.prototype);\n  AsyncIterator.prototype[asyncIteratorSymbol] = function () {\n    return this;\n  };\n  exports.AsyncIterator = AsyncIterator;\n\n  // Note that simple async functions are implemented on top of\n  // AsyncIterator objects; they just return a Promise for the value of\n  // the final result produced by the iterator.\n  exports.async = function(innerFn, outerFn, self, tryLocsList, PromiseImpl) {\n    if (PromiseImpl === void 0) PromiseImpl = Promise;\n\n    var iter = new AsyncIterator(\n      wrap(innerFn, outerFn, self, tryLocsList),\n      PromiseImpl\n    );\n\n    return exports.isGeneratorFunction(outerFn)\n      ? iter // If outerFn is a generator, return the full iterator.\n      : iter.next().then(function(result) {\n          return result.done ? result.value : iter.next();\n        });\n  };\n\n  function makeInvokeMethod(innerFn, self, context) {\n    var state = GenStateSuspendedStart;\n\n    return function invoke(method, arg) {\n      if (state === GenStateExecuting) {\n        throw new Error(\"Generator is already running\");\n      }\n\n      if (state === GenStateCompleted) {\n        if (method === \"throw\") {\n          throw arg;\n        }\n\n        // Be forgiving, per 25.3.3.3.3 of the spec:\n        // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume\n        return doneResult();\n      }\n\n      context.method = method;\n      context.arg = arg;\n\n      while (true) {\n        var delegate = context.delegate;\n        if (delegate) {\n          var delegateResult = maybeInvokeDelegate(delegate, context);\n          if (delegateResult) {\n            if (delegateResult === ContinueSentinel) continue;\n            return delegateResult;\n          }\n        }\n\n        if (context.method === \"next\") {\n          // Setting context._sent for legacy support of Babel's\n          // function.sent implementation.\n          context.sent = context._sent = context.arg;\n\n        } else if (context.method === \"throw\") {\n          if (state === GenStateSuspendedStart) {\n            state = GenStateCompleted;\n            throw context.arg;\n          }\n\n          context.dispatchException(context.arg);\n\n        } else if (context.method === \"return\") {\n          context.abrupt(\"return\", context.arg);\n        }\n\n        state = GenStateExecuting;\n\n        var record = tryCatch(innerFn, self, context);\n        if (record.type === \"normal\") {\n          // If an exception is thrown from innerFn, we leave state ===\n          // GenStateExecuting and loop back for another invocation.\n          state = context.done\n            ? GenStateCompleted\n            : GenStateSuspendedYield;\n\n          if (record.arg === ContinueSentinel) {\n            continue;\n          }\n\n          return {\n            value: record.arg,\n            done: context.done\n          };\n\n        } else if (record.type === \"throw\") {\n          state = GenStateCompleted;\n          // Dispatch the exception by looping back around to the\n          // context.dispatchException(context.arg) call above.\n          context.method = \"throw\";\n          context.arg = record.arg;\n        }\n      }\n    };\n  }\n\n  // Call delegate.iterator[context.method](context.arg) and handle the\n  // result, either by returning a { value, done } result from the\n  // delegate iterator, or by modifying context.method and context.arg,\n  // setting context.delegate to null, and returning the ContinueSentinel.\n  function maybeInvokeDelegate(delegate, context) {\n    var method = delegate.iterator[context.method];\n    if (method === undefined) {\n      // A .throw or .return when the delegate iterator has no .throw\n      // method always terminates the yield* loop.\n      context.delegate = null;\n\n      if (context.method === \"throw\") {\n        // Note: [\"return\"] must be used for ES3 parsing compatibility.\n        if (delegate.iterator[\"return\"]) {\n          // If the delegate iterator has a return method, give it a\n          // chance to clean up.\n          context.method = \"return\";\n          context.arg = undefined;\n          maybeInvokeDelegate(delegate, context);\n\n          if (context.method === \"throw\") {\n            // If maybeInvokeDelegate(context) changed context.method from\n            // \"return\" to \"throw\", let that override the TypeError below.\n            return ContinueSentinel;\n          }\n        }\n\n        context.method = \"throw\";\n        context.arg = new TypeError(\n          \"The iterator does not provide a 'throw' method\");\n      }\n\n      return ContinueSentinel;\n    }\n\n    var record = tryCatch(method, delegate.iterator, context.arg);\n\n    if (record.type === \"throw\") {\n      context.method = \"throw\";\n      context.arg = record.arg;\n      context.delegate = null;\n      return ContinueSentinel;\n    }\n\n    var info = record.arg;\n\n    if (! info) {\n      context.method = \"throw\";\n      context.arg = new TypeError(\"iterator result is not an object\");\n      context.delegate = null;\n      return ContinueSentinel;\n    }\n\n    if (info.done) {\n      // Assign the result of the finished delegate to the temporary\n      // variable specified by delegate.resultName (see delegateYield).\n      context[delegate.resultName] = info.value;\n\n      // Resume execution at the desired location (see delegateYield).\n      context.next = delegate.nextLoc;\n\n      // If context.method was \"throw\" but the delegate handled the\n      // exception, let the outer generator proceed normally. If\n      // context.method was \"next\", forget context.arg since it has been\n      // \"consumed\" by the delegate iterator. If context.method was\n      // \"return\", allow the original .return call to continue in the\n      // outer generator.\n      if (context.method !== \"return\") {\n        context.method = \"next\";\n        context.arg = undefined;\n      }\n\n    } else {\n      // Re-yield the result returned by the delegate method.\n      return info;\n    }\n\n    // The delegate iterator is finished, so forget it and continue with\n    // the outer generator.\n    context.delegate = null;\n    return ContinueSentinel;\n  }\n\n  // Define Generator.prototype.{next,throw,return} in terms of the\n  // unified ._invoke helper method.\n  defineIteratorMethods(Gp);\n\n  Gp[toStringTagSymbol] = \"Generator\";\n\n  // A Generator should always return itself as the iterator object when the\n  // @@iterator function is called on it. Some browsers' implementations of the\n  // iterator prototype chain incorrectly implement this, causing the Generator\n  // object to not be returned from this call. This ensures that doesn't happen.\n  // See https://github.com/facebook/regenerator/issues/274 for more details.\n  Gp[iteratorSymbol] = function() {\n    return this;\n  };\n\n  Gp.toString = function() {\n    return \"[object Generator]\";\n  };\n\n  function pushTryEntry(locs) {\n    var entry = { tryLoc: locs[0] };\n\n    if (1 in locs) {\n      entry.catchLoc = locs[1];\n    }\n\n    if (2 in locs) {\n      entry.finallyLoc = locs[2];\n      entry.afterLoc = locs[3];\n    }\n\n    this.tryEntries.push(entry);\n  }\n\n  function resetTryEntry(entry) {\n    var record = entry.completion || {};\n    record.type = \"normal\";\n    delete record.arg;\n    entry.completion = record;\n  }\n\n  function Context(tryLocsList) {\n    // The root entry object (effectively a try statement without a catch\n    // or a finally block) gives us a place to store values thrown from\n    // locations where there is no enclosing try statement.\n    this.tryEntries = [{ tryLoc: \"root\" }];\n    tryLocsList.forEach(pushTryEntry, this);\n    this.reset(true);\n  }\n\n  exports.keys = function(object) {\n    var keys = [];\n    for (var key in object) {\n      keys.push(key);\n    }\n    keys.reverse();\n\n    // Rather than returning an object with a next method, we keep\n    // things simple and return the next function itself.\n    return function next() {\n      while (keys.length) {\n        var key = keys.pop();\n        if (key in object) {\n          next.value = key;\n          next.done = false;\n          return next;\n        }\n      }\n\n      // To avoid creating an additional object, we just hang the .value\n      // and .done properties off the next function object itself. This\n      // also ensures that the minifier will not anonymize the function.\n      next.done = true;\n      return next;\n    };\n  };\n\n  function values(iterable) {\n    if (iterable) {\n      var iteratorMethod = iterable[iteratorSymbol];\n      if (iteratorMethod) {\n        return iteratorMethod.call(iterable);\n      }\n\n      if (typeof iterable.next === \"function\") {\n        return iterable;\n      }\n\n      if (!isNaN(iterable.length)) {\n        var i = -1, next = function next() {\n          while (++i < iterable.length) {\n            if (hasOwn.call(iterable, i)) {\n              next.value = iterable[i];\n              next.done = false;\n              return next;\n            }\n          }\n\n          next.value = undefined;\n          next.done = true;\n\n          return next;\n        };\n\n        return next.next = next;\n      }\n    }\n\n    // Return an iterator with no values.\n    return { next: doneResult };\n  }\n  exports.values = values;\n\n  function doneResult() {\n    return { value: undefined, done: true };\n  }\n\n  Context.prototype = {\n    constructor: Context,\n\n    reset: function(skipTempReset) {\n      this.prev = 0;\n      this.next = 0;\n      // Resetting context._sent for legacy support of Babel's\n      // function.sent implementation.\n      this.sent = this._sent = undefined;\n      this.done = false;\n      this.delegate = null;\n\n      this.method = \"next\";\n      this.arg = undefined;\n\n      this.tryEntries.forEach(resetTryEntry);\n\n      if (!skipTempReset) {\n        for (var name in this) {\n          // Not sure about the optimal order of these conditions:\n          if (name.charAt(0) === \"t\" &&\n              hasOwn.call(this, name) &&\n              !isNaN(+name.slice(1))) {\n            this[name] = undefined;\n          }\n        }\n      }\n    },\n\n    stop: function() {\n      this.done = true;\n\n      var rootEntry = this.tryEntries[0];\n      var rootRecord = rootEntry.completion;\n      if (rootRecord.type === \"throw\") {\n        throw rootRecord.arg;\n      }\n\n      return this.rval;\n    },\n\n    dispatchException: function(exception) {\n      if (this.done) {\n        throw exception;\n      }\n\n      var context = this;\n      function handle(loc, caught) {\n        record.type = \"throw\";\n        record.arg = exception;\n        context.next = loc;\n\n        if (caught) {\n          // If the dispatched exception was caught by a catch block,\n          // then let that catch block handle the exception normally.\n          context.method = \"next\";\n          context.arg = undefined;\n        }\n\n        return !! caught;\n      }\n\n      for (var i = this.tryEntries.length - 1; i >= 0; --i) {\n        var entry = this.tryEntries[i];\n        var record = entry.completion;\n\n        if (entry.tryLoc === \"root\") {\n          // Exception thrown outside of any try block that could handle\n          // it, so set the completion value of the entire function to\n          // throw the exception.\n          return handle(\"end\");\n        }\n\n        if (entry.tryLoc <= this.prev) {\n          var hasCatch = hasOwn.call(entry, \"catchLoc\");\n          var hasFinally = hasOwn.call(entry, \"finallyLoc\");\n\n          if (hasCatch && hasFinally) {\n            if (this.prev < entry.catchLoc) {\n              return handle(entry.catchLoc, true);\n            } else if (this.prev < entry.finallyLoc) {\n              return handle(entry.finallyLoc);\n            }\n\n          } else if (hasCatch) {\n            if (this.prev < entry.catchLoc) {\n              return handle(entry.catchLoc, true);\n            }\n\n          } else if (hasFinally) {\n            if (this.prev < entry.finallyLoc) {\n              return handle(entry.finallyLoc);\n            }\n\n          } else {\n            throw new Error(\"try statement without catch or finally\");\n          }\n        }\n      }\n    },\n\n    abrupt: function(type, arg) {\n      for (var i = this.tryEntries.length - 1; i >= 0; --i) {\n        var entry = this.tryEntries[i];\n        if (entry.tryLoc <= this.prev &&\n            hasOwn.call(entry, \"finallyLoc\") &&\n            this.prev < entry.finallyLoc) {\n          var finallyEntry = entry;\n          break;\n        }\n      }\n\n      if (finallyEntry &&\n          (type === \"break\" ||\n           type === \"continue\") &&\n          finallyEntry.tryLoc <= arg &&\n          arg <= finallyEntry.finallyLoc) {\n        // Ignore the finally entry if control is not jumping to a\n        // location outside the try/catch block.\n        finallyEntry = null;\n      }\n\n      var record = finallyEntry ? finallyEntry.completion : {};\n      record.type = type;\n      record.arg = arg;\n\n      if (finallyEntry) {\n        this.method = \"next\";\n        this.next = finallyEntry.finallyLoc;\n        return ContinueSentinel;\n      }\n\n      return this.complete(record);\n    },\n\n    complete: function(record, afterLoc) {\n      if (record.type === \"throw\") {\n        throw record.arg;\n      }\n\n      if (record.type === \"break\" ||\n          record.type === \"continue\") {\n        this.next = record.arg;\n      } else if (record.type === \"return\") {\n        this.rval = this.arg = record.arg;\n        this.method = \"return\";\n        this.next = \"end\";\n      } else if (record.type === \"normal\" && afterLoc) {\n        this.next = afterLoc;\n      }\n\n      return ContinueSentinel;\n    },\n\n    finish: function(finallyLoc) {\n      for (var i = this.tryEntries.length - 1; i >= 0; --i) {\n        var entry = this.tryEntries[i];\n        if (entry.finallyLoc === finallyLoc) {\n          this.complete(entry.completion, entry.afterLoc);\n          resetTryEntry(entry);\n          return ContinueSentinel;\n        }\n      }\n    },\n\n    \"catch\": function(tryLoc) {\n      for (var i = this.tryEntries.length - 1; i >= 0; --i) {\n        var entry = this.tryEntries[i];\n        if (entry.tryLoc === tryLoc) {\n          var record = entry.completion;\n          if (record.type === \"throw\") {\n            var thrown = record.arg;\n            resetTryEntry(entry);\n          }\n          return thrown;\n        }\n      }\n\n      // The context.catch method must only be called with a location\n      // argument that corresponds to a known catch block.\n      throw new Error(\"illegal catch attempt\");\n    },\n\n    delegateYield: function(iterable, resultName, nextLoc) {\n      this.delegate = {\n        iterator: values(iterable),\n        resultName: resultName,\n        nextLoc: nextLoc\n      };\n\n      if (this.method === \"next\") {\n        // Deliberately forget the last sent value so that we don't\n        // accidentally pass it on to the delegate.\n        this.arg = undefined;\n      }\n\n      return ContinueSentinel;\n    }\n  };\n\n  // Regardless of whether this script is executing as a CommonJS module\n  // or not, return the runtime object so that we can declare the variable\n  // regeneratorRuntime in the outer scope, which allows this module to be\n  // injected easily by `bin/regenerator --include-runtime script.js`.\n  return exports;\n\n}(\n  // If this script is executing as a CommonJS module, use module.exports\n  // as the regeneratorRuntime namespace. Otherwise create a new empty\n  // object. Either way, the resulting object will be used to initialize\n  // the regeneratorRuntime variable at the top of this file.\n   true ? module.exports : undefined\n));\n\ntry {\n  regeneratorRuntime = runtime;\n} catch (accidentalStrictMode) {\n  // This module should not be running in strict mode, so the above\n  // assignment should always work unless something is misconfigured. Just\n  // in case runtime.js accidentally runs in strict mode, we can escape\n  // strict mode using a global Function call. This could conceivably fail\n  // if a Content Security Policy forbids using Function, but in that case\n  // the proper solution is to fix the accidental strict mode problem. If\n  // you've misconfigured your bundler to force strict mode and applied a\n  // CSP to forbid Function, and you're not willing to fix either of those\n  // problems, please detail your unique predicament in a GitHub issue.\n  Function(\"r\", \"regeneratorRuntime = r\")(runtime);\n}\n\n\n//# sourceURL=webpack:///./node_modules/regenerator-runtime/runtime.js?");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=template&id=20bcfe1b&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/CreateArticle.vue?vue&type=template&id=20bcfe1b& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-engageArticle-createArticle\"},[_c('ArticleForm',{directives:[{name:\"show\",rawName:\"v-show\",value:(_vm.stage === 0),expression:\"stage === 0\"}],attrs:{\"article-name\":_vm.article.name,\"article-content\":_vm.article.content},on:{\"next\":_vm.next,\"cancel\":function($event){return _vm.$emit('cancel')}}}),_vm._v(\" \"),_c('AccessForm',{directives:[{name:\"show\",rawName:\"v-show\",value:(_vm.stage === 1),expression:\"stage === 1\"}],attrs:{\"item-id\":\"0\",\"component\":\"engage_article\",\"show-back\":true,\"submitting\":_vm.submitting,\"selected-access\":_vm.containerValues.access,\"private-disabled\":_vm.privateDisabled,\"restricted-disabled\":_vm.restrictedDisabled,\"container\":_vm.container,\"enable-time-view\":true},on:{\"done\":_vm.done,\"back\":_vm.back,\"cancel\":function($event){return _vm.$emit('cancel')}}})],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/CreateArticle.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=template&id=18b826b6&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/ArticleCard.vue?vue&type=template&id=18b826b6& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('BaseCard',{staticClass:\"tui-engageArticle-articleCard\",attrs:{\"data-card-unique\":_vm.instanceId,\"href\":_vm.url,\"show-footnotes\":_vm.showFootnotes,\"footnotes\":_vm.footnotes},on:{\"mouseover\":function($event){return _vm.$_handleHovered(true)},\"mouseleave\":function($event){return _vm.$_handleHovered(false)}}},[_c('ImageHeader',{staticClass:\"tui-engageArticle-articleCard__imageheader\",attrs:{\"slot\":\"header-image\",\"show-cover\":_vm.hovered},slot:\"header-image\"},[_c('img',{staticClass:\"tui-engageArticle-articleCard__image\",attrs:{\"slot\":\"image\",\"alt\":_vm.name,\"src\":_vm.extraData.image},slot:\"image\"}),_vm._v(\" \"),_c('div',{staticClass:\"tui-engageArticle-articleCard__icons\",attrs:{\"slot\":\"actions\"},slot:\"actions\"},_vm._l((_vm.actions),function(action,i){return _c('ButtonIcon',{key:i,attrs:{\"aria-label\":action.alt,\"styleclass\":{ primary: false, circle: true }}},[_c(action.component,{tag:\"component\"})],1)}),1)]),_vm._v(\" \"),_c('CardHeader',{staticClass:\"tui-engageArticle-articleCard__header\",attrs:{\"slot\":\"header\"},slot:\"header\"},[_c('BookmarkButton',{directives:[{name:\"show\",rawName:\"v-show\",value:(!_vm.owned),expression:\"!owned\"}],staticClass:\"tui-engageArticle-articleCard__bookmark\",attrs:{\"slot\":\"first\",\"size\":\"300\",\"bookmarked\":_vm.innerBookmarked,\"primary\":false,\"circle\":false,\"small\":true,\"transparent\":true},on:{\"click\":_vm.updateBookmark},slot:\"first\"}),_vm._v(\" \"),_c('h3',{staticClass:\"tui-engageArticle-articleCard__title\",attrs:{\"slot\":\"second\",\"id\":_vm.labelId},slot:\"second\"},[_vm._v(\"\\n      \"+_vm._s(_vm.name)+\"\\n    \")]),_vm._v(\" \"),(_vm.extraData.timeview)?_c('div',{staticClass:\"tui-engageArticle-articleCard__subTitle\",attrs:{\"slot\":\"third\"},slot:\"third\"},[_c('TimeIcon',{attrs:{\"size\":\"200\",\"alt\":_vm.$str('time', 'totara_engage'),\"custom-class\":\"tui-icon--dimmed\"}}),_vm._v(\" \"),_c('span',{staticClass:\"tui-engageArticle-articleCard__subTitle-text\"},[_vm._v(_vm._s(_vm.getTimeView))])],1):_vm._e()],1),_vm._v(\" \"),_c('div',{staticClass:\"tui-engageArticle-articleCard__footer\",attrs:{\"slot\":\"footer\"},slot:\"footer\"},[_vm._l((_vm.statIcons),function(statIcon){return _c('StatIcon',{key:statIcon.type,attrs:{\"title\":statIcon.title,\"stat-number\":statIcon.statNumber}},[_c(statIcon.icon,{tag:\"component\",attrs:{\"title\":statIcon.title}})],1)}),_vm._v(\" \"),_c('AccessIcon',{attrs:{\"access\":_vm.access,\"size\":\"300\",\"custom-class\":\"tui-engageArticle-articleCard__visibilityIcon\"}})],2)],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/ArticleCard.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=template&id=f16a1e2a&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/card/RelatedCard.vue?vue&type=template&id=f16a1e2a& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('Card',{staticClass:\"tui-articleRelatedCard\",attrs:{\"clickable\":true},on:{\"click\":_vm.handleClickCard}},[_c('img',{staticClass:\"tui-articleRelatedCard__img\",attrs:{\"src\":_vm.image,\"alt\":_vm.name}}),_vm._v(\" \"),_c('section',{staticClass:\"tui-articleRelatedCard__content\"},[_c('a',{attrs:{\"href\":_vm.url}},[_vm._v(\"\\n      \"+_vm._s(_vm.name)+\"\\n    \")]),_vm._v(\" \"),_c('p',[(_vm.timeviewString)?_c('span',{staticClass:\"tui-articleRelatedCard__timeview\"},[_c('TimeIcon',{attrs:{\"size\":\"200\",\"alt\":_vm.$str('time', 'totara_engage'),\"custom-class\":\"tui-articleRelatedCard--dimmed\"}}),_vm._v(\"\\n        \"+_vm._s(_vm.timeviewString)+\"\\n      \")],1):_vm._e(),_vm._v(\" \"),_c('Like',{attrs:{\"size\":\"200\",\"alt\":_vm.$str('like', 'totara_engage'),\"custom-class\":\"tui-articleRelatedCard--dimmed\"}}),_vm._v(\" \"),_c('span',[_vm._v(_vm._s(_vm.reactions))])],1)]),_vm._v(\" \"),_c('BookmarkButton',{staticClass:\"tui-articleRelatedCard__bookmark\",attrs:{\"size\":\"300\",\"bookmarked\":_vm.innerBookmarked,\"primary\":false,\"circle\":false,\"small\":true,\"transparent\":true},on:{\"click\":_vm.handleClickBookmark}})],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/card/RelatedCard.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=template&id=7827ff08&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/content/ArticleContent.vue?vue&type=template&id=7827ff08& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-articleContent\"},[_c('InlineEditing',{directives:[{name:\"show\",rawName:\"v-show\",value:(!_vm.editing),expression:\"!editing\"}],attrs:{\"full-width\":true,\"restricted-mode\":true,\"update-able\":_vm.updateAble},on:{\"click\":function($event){_vm.editing = true}}},[_c('div',{ref:\"content\",staticClass:\"tui-articleContent__content\",attrs:{\"slot\":\"content\"},domProps:{\"innerHTML\":_vm._s(_vm.content)},slot:\"content\"})]),_vm._v(\" \"),(_vm.editing)?_c('EditArticleForm',{attrs:{\"resource-id\":_vm.resourceId,\"submitting\":_vm.submitting},on:{\"submit\":_vm.updateArticle,\"cancel\":function($event){_vm.editing = false}}}):_vm._e()],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleContent.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=template&id=be80d1b2&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/content/ArticleTitle.vue?vue&type=template&id=be80d1b2& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-articleTitle\"},[_c('div',{staticClass:\"tui-articleTitle__head\"},[_c('InlineEditing',{directives:[{name:\"show\",rawName:\"v-show\",value:(!_vm.editing),expression:\"!editing\"}],attrs:{\"update-able\":_vm.updateAble,\"full-width\":true},on:{\"click\":function($event){_vm.editing = true}}},[_c('h3',{staticClass:\"tui-articleTitle__head__title\",attrs:{\"slot\":\"content\"},slot:\"content\"},[_vm._v(\"\\n        \"+_vm._s(_vm.title)+\"\\n      \")])]),_vm._v(\" \"),(_vm.editing)?_c('EditArticleTitleForm',{attrs:{\"title\":_vm.title,\"submitting\":_vm.submitting},on:{\"cancel\":function($event){_vm.editing = false},\"submit\":_vm.updateTitle}}):_vm._e(),_vm._v(\" \"),(!_vm.owned)?_c('BookmarkButton',{attrs:{\"primary\":false,\"circle\":true,\"bookmarked\":_vm.bookmarked,\"size\":\"300\"},on:{\"click\":function($event){return _vm.$emit('bookmark', $event)}}}):_vm._e()],1),_vm._v(\" \"),_c('ArticleSeparator')],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/content/ArticleTitle.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=template&id=01a1681e&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/ArticleForm.vue?vue&type=template&id=01a1681e& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('Form',{staticClass:\"tui-articleForm\",attrs:{\"vertical\":true,\"input-width\":\"full\"}},[_c('FormRow',{staticClass:\"tui-articleForm__title\",attrs:{\"hidden\":true,\"label\":_vm.$str('articletitle', 'engage_article'),\"required\":true},scopedSlots:_vm._u([{key:\"default\",fn:function(ref){\nvar id = ref.id;\nreturn [_c('InputText',{attrs:{\"id\":id,\"name\":\"article-title\",\"maxlength\":75,\"placeholder\":_vm.$str('entertitle', 'engage_article'),\"disabled\":_vm.submitting,\"required\":true},model:{value:(_vm.name),callback:function ($$v) {_vm.name=$$v},expression:\"name\"}})]}}])}),_vm._v(\" \"),_c('div',{staticClass:\"tui-articleForm__description\"},[_c('FormRow',{staticClass:\"tui-articleForm__description__formRow\",attrs:{\"hidden\":true,\"label\":_vm.$str('content', 'engage_article'),\"required\":true,\"is-stacked\":false},scopedSlots:_vm._u([{key:\"default\",fn:function(ref){\nvar id = ref.id;\nreturn [(_vm.draftId)?_c('Weka',{attrs:{\"id\":id,\"component\":\"engage_article\",\"area\":\"content\",\"doc\":_vm.content.doc,\"file-item-id\":_vm.draftId,\"placeholder\":_vm.$str('entercontent', 'engage_article')},on:{\"update\":_vm.handleUpdate}}):_vm._e()]}}])}),_vm._v(\" \"),_c('div',{staticClass:\"tui-articleForm__description__tip\"},[_c('p',[_vm._v(_vm._s(_vm.$str('contributetip', 'totara_engage')))]),_vm._v(\" \"),_c('Popover',{attrs:{\"position\":\"right\"},scopedSlots:_vm._u([{key:\"trigger\",fn:function(ref){\nvar isOpen = ref.isOpen;\nreturn [_c('ButtonIcon',{staticClass:\"tui-articleForm__description__iconButton\",attrs:{\"aria-expanded\":isOpen.toString(),\"aria-label\":_vm.$str('info', 'moodle'),\"styleclass\":{\n              primary: true,\n              small: true,\n              transparentNoPadding: true,\n            }}},[_c('InfoIcon')],1)]}}])},[_vm._v(\" \"),_c('p',{staticClass:\"tui-articleForm__description__tip__content\"},[_vm._v(\"\\n          \"+_vm._s(_vm.$str('contributetip_help', 'totara_engage'))+\"\\n        \")])])],1)],1),_vm._v(\" \"),_c('ButtonGroup',{staticClass:\"tui-articleForm__buttons\"},[_c('Button',{attrs:{\"loading\":_vm.submitting,\"styleclass\":{ primary: 'true' },\"disabled\":_vm.disabled,\"aria-label\":_vm.$str('createarticleshort', 'engage_article'),\"text\":_vm.$str('next', 'moodle')},on:{\"click\":_vm.submit}}),_vm._v(\" \"),_c('CancelButton',{attrs:{\"disabled\":_vm.submitting},on:{\"click\":function($event){return _vm.$emit('cancel')}}})],1)],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/ArticleForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=template&id=93461ec6&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/EditArticleContentForm.vue?vue&type=template&id=93461ec6& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('Form',{staticClass:\"tui-editArticleContentForm\"},[_c('Loader',{attrs:{\"fullpage\":true,\"loading\":!_vm.editorMounted}}),_vm._v(\" \"),(!_vm.$apollo.loading)?_c('Weka',{staticClass:\"tui-editArticleContentForm__editor\",attrs:{\"component\":\"engage_article\",\"area\":\"content\",\"instance-id\":_vm.resourceId,\"doc\":_vm.content.doc,\"file-item-id\":_vm.draft.file_item_id},on:{\"editor-mounted\":function($event){_vm.editorMounted = true},\"update\":_vm.handleUpdate}}):_vm._e(),_vm._v(\" \"),_c('DoneCancelGroup',{attrs:{\"loading\":_vm.submitting,\"disabled\":_vm.content.empty || _vm.submitting},on:{\"done\":_vm.submit,\"cancel\":function($event){return _vm.$emit('cancel')}}})],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleContentForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=template&id=11c34d08&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?vue&type=template&id=11c34d08& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('Form',{staticClass:\"tui-editArticleTitleForm\"},[_c('InputText',{staticClass:\"tui-editArticleTitleForm__input\",attrs:{\"name\":\"title\",\"disabled\":_vm.submitting,\"maxlength\":60,\"placeholder\":_vm.$str('entertitle', 'engage_article'),\"aria-label\":_vm.$str('articletitle', 'engage_article')},on:{\"submit\":function($event){return _vm.$emit('submit', _vm.innerTitle)}},model:{value:(_vm.innerTitle),callback:function ($$v) {_vm.innerTitle=$$v},expression:\"innerTitle\"}}),_vm._v(\" \"),_c('DoneCancelGroup',{attrs:{\"loading\":_vm.submitting,\"disabled\":_vm.submitting || !_vm.innerTitle},on:{\"done\":function($event){return _vm.$emit('submit', _vm.innerTitle)},\"cancel\":function($event){return _vm.$emit('cancel')}}})],1)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/form/EditArticleTitleForm.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=template&id=01d50df0&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/separator/ArticleSeparator.vue?vue&type=template&id=01d50df0& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-articleSeparator\"})}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/separator/ArticleSeparator.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=template&id=3c516de8&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?vue&type=template&id=3c516de8& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return (!_vm.$apollo.loading)?_c('EngageSidePanel',{staticClass:\"tui-articleSidePanel\",scopedSlots:_vm._u([{key:\"modal\",fn:function(){return [_c('ModalPresenter',{attrs:{\"open\":_vm.openModalFromAction},on:{\"request-close\":function($event){_vm.openModalFromAction = false}}},[_c('EngageWarningModal',{attrs:{\"title\":_vm.$str('deletewarningtitle', 'engage_article'),\"message-content\":_vm.$str('deletewarningmsg', 'engage_article')},on:{\"delete\":_vm.handleDelete}})],1)]},proxy:true},{key:\"overview\",fn:function(){return [_c('Loader',{attrs:{\"fullpage\":true,\"loading\":_vm.submitting}}),_vm._v(\" \"),_c('p',{staticClass:\"tui-articleSidePanel__timeDescription\"},[_vm._v(\"\\n      \"+_vm._s(_vm.article.timedescription)+\"\\n    \")]),_vm._v(\" \"),(_vm.article.owned || _vm.article.updateable)?_c('AccessSetting',{attrs:{\"item-id\":_vm.resourceId,\"component\":\"engage_article\",\"access-value\":_vm.article.resource.access,\"topics\":_vm.article.topics,\"submitting\":false,\"open-modal\":_vm.openModalFromButtonLabel,\"selected-time-view\":_vm.article.timeview,\"enable-time-view\":true},on:{\"access-update\":_vm.updateAccess,\"close-modal\":function($event){_vm.openModalFromButtonLabel = false}}}):_c('AccessDisplay',{attrs:{\"access-value\":_vm.article.resource.access,\"time-view\":_vm.article.timeview,\"topics\":_vm.article.topics,\"show-button\":false}}),_vm._v(\" \"),_c('MediaSetting',{attrs:{\"owned\":_vm.article.owned,\"access-value\":_vm.article.resource.access,\"instance-id\":_vm.resourceId,\"shared-by-count\":_vm.article.sharedbycount,\"like-button-aria-label\":_vm.likeButtonLabel,\"liked\":_vm.article.reacted,\"component-name\":\"engage_article\"},on:{\"access-update\":_vm.updateAccess,\"access-modal\":function($event){_vm.openModalFromButtonLabel = true},\"update-like-status\":_vm.updateLikeStatus}}),_vm._v(\" \"),_c('ArticlePlaylistBox',{staticClass:\"tui-articleSidePanel__playlistBox\",attrs:{\"resource-id\":_vm.resourceId}})]},proxy:true},{key:\"comments\",fn:function(){return [_c('SidePanelCommentBox',{attrs:{\"component\":\"engage_article\",\"area\":\"comment\",\"instance-id\":_vm.resourceId}})]},proxy:true},(_vm.featureRecommenders)?{key:\"related\",fn:function(){return [_c('Related',{attrs:{\"component\":\"engage_article\",\"area\":\"related\",\"resource-id\":_vm.resourceId}})]},proxy:true}:null],null,true)},[_c('MiniProfileCard',{attrs:{\"slot\":\"author-profile\",\"display\":_vm.user.card_display,\"no-border\":true},slot:\"author-profile\",scopedSlots:_vm._u([{key:\"drop-down-items\",fn:function(){return [(_vm.article.owned || _vm.article.updateable)?_c('DropdownItem',{on:{\"click\":function($event){_vm.openModalFromAction = true}}},[_vm._v(\"\\n        \"+_vm._s(_vm.$str('delete', 'moodle'))+\"\\n      \")]):_vm._e(),_vm._v(\" \"),(!_vm.article.owned)?_c('DropdownItem',{on:{\"click\":_vm.reportResource}},[_vm._v(\"\\n        \"+_vm._s(_vm.$str('reportresource', 'engage_article'))+\"\\n      \")]):_vm._e()]},proxy:true}],null,false,3654931436)})],1):_vm._e()}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/ArticleSidePanel.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=template&id=44b6de2c&":
/*!************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/Related.vue?vue&type=template&id=44b6de2c& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-articleRelated\"},_vm._l((_vm.articles),function(ref){\n    var bookmarked = ref.bookmarked;\n    var instanceid = ref.instanceid;\n    var image = ref.image;\n    var name = ref.name;\n    var reactions = ref.reactions;\n    var timeview = ref.timeview;\n    var url = ref.url;\nreturn _c('article',{key:instanceid},[_c('RelatedCard',{attrs:{\"resource-id\":instanceid,\"bookmarked\":bookmarked,\"image\":image,\"name\":name,\"reactions\":reactions,\"timeview\":timeview,\"url\":url},on:{\"update\":_vm.update}})],1)}),0)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/Related.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=template&id=2c100fac&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?vue&type=template&id=2c100fac& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',{staticClass:\"tui-articlePlaylistBox\"},[(_vm.show)?[_c('p',{staticClass:\"tui-articlePlaylistBox__label\"},[_c('span',[_vm._v(\"\\n        \"+_vm._s(_vm.$str('appears_in', 'engage_article'))+\"\\n      \")]),_vm._v(\" \"),(_vm.loading)?_c('Loading'):_vm._e()],1),_vm._v(\" \"),_c('ResourcePlaylistBox',{staticClass:\"tui-articlePlaylistBox__playlistsBox\",attrs:{\"resource-id\":_vm.resourceId,\"url-source\":_vm.urlSource},on:{\"update-has-playlists\":function($event){_vm.show = $event},\"load-records\":function($event){_vm.loading = $event}}})]:_vm._e()],2)}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/components/sidepanel/content/ArticlePlaylistBox.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=template&id=34cec6b4&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/engage_article/src/pages/ArticleView.vue?vue&type=template&id=34cec6b4& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('Layout',{staticClass:\"tui-articleView\",scopedSlots:_vm._u([(_vm.backButton || _vm.navigationButtons)?{key:\"header\",fn:function(){return [_c('ResourceNavigationBar',{attrs:{\"back-button\":_vm.backButton,\"navigation-buttons\":_vm.navigationButtons}})]},proxy:true}:null,{key:\"column\",fn:function(){return [_c('Loader',{attrs:{\"loading\":_vm.$apollo.loading,\"fullpage\":true}}),_vm._v(\" \"),(!_vm.$apollo.loading)?_c('div',{staticClass:\"tui-articleView__layout\"},[_c('ArticleTitle',{attrs:{\"title\":_vm.articleName,\"resource-id\":_vm.resourceId,\"owned\":_vm.article.owned,\"bookmarked\":_vm.bookmarked,\"update-able\":_vm.article.updateable},on:{\"bookmark\":_vm.updateBookmark}}),_vm._v(\" \"),_c('ArticleContent',{attrs:{\"update-able\":_vm.article.updateable,\"content\":_vm.article.content,\"resource-id\":_vm.resourceId}})],1):_vm._e()]},proxy:true},{key:\"sidepanel\",fn:function(){return [_c('ArticleSidePanel',{attrs:{\"resource-id\":_vm.resourceId}})]},proxy:true}],null,true)})}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/engage_article/src/pages/ArticleView.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--2-0!./node_modules/vue-loader/lib??vue-loader-options");

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

/***/ "./server/lib/webapi/ajax/file_unused_draft_item_id.graphql":
/*!******************************************************************!*\
  !*** ./server/lib/webapi/ajax/file_unused_draft_item_id.graphql ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"core_file_unused_draft_item_id\"},\"variableDefinitions\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"item_id\"},\"name\":{\"kind\":\"Name\",\"value\":\"core_file_unused_draft_item_id\"},\"arguments\":[],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/lib/webapi/ajax/file_unused_draft_item_id.graphql?");

/***/ }),

/***/ "./server/ml/recommender/webapi/ajax/get_recommended_articles.graphql":
/*!****************************************************************************!*\
  !*** ./server/ml/recommender/webapi/ajax/get_recommended_articles.graphql ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"ml_recommender_get_recommended_articles\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"article_id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"cursor\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_text\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"source\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"String\"}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"cursor\"},\"name\":{\"kind\":\"Name\",\"value\":\"ml_recommender_recommended_articles_cursor\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"article_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"article_id\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"cursor\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"cursor\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"total\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"next\"},\"arguments\":[],\"directives\":[]}]}},{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"articles\"},\"name\":{\"kind\":\"Name\",\"value\":\"ml_recommender_recommended_articles\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"article_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"article_id\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"cursor\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"cursor\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"instanceid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"summary\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"component\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"tuicomponent\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"comments\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"reactions\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timecreated\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"sharedbycount\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"extra\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"owned\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimageurl\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimagealt\"},\"arguments\":[],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"url\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"source\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"source\"}}}],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/ml/recommender/webapi/ajax/get_recommended_articles.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/article/webapi/ajax/create_article.graphql":
/*!***********************************************************************************!*\
  !*** ./server/totara/engage/resources/article/webapi/ajax/create_article.graphql ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_create_article\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"String\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_text\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_access\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_timeview\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_recipient_in\"}}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"draft_id\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"article\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_create\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"draft_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"draft_id\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"resource\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"time\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"fullname\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimagealt\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profileimageurl\"},\"arguments\":[],\"directives\":[]}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"HTML\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"image\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"owned\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"sharedbycount\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"reacted\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/article/webapi/ajax/create_article.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/article/webapi/ajax/delete_article.graphql":
/*!***********************************************************************************!*\
  !*** ./server/totara/engage/resources/article/webapi/ajax/delete_article.graphql ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_delete_article\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"result\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_delete\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}}}],\"directives\":[]}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/article/webapi/ajax/delete_article.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/article/webapi/ajax/draft_item.graphql":
/*!*******************************************************************************!*\
  !*** ./server/totara/engage/resources/article/webapi/ajax/draft_item.graphql ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_draft_item\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"draft\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_draft_item\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"file_item_id\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/article/webapi/ajax/draft_item.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/article/webapi/ajax/get_article.graphql":
/*!********************************************************************************!*\
  !*** ./server/totara/engage/resources/article/webapi/ajax/get_article.graphql ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_get_article\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"article\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_get_article\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"resource\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"time\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"card_display\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_picture_alt\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_picture_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"display_fields\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"label\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"associate_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"is_custom\"},\"arguments\":[],\"directives\":[]}]}}]}}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"HTML\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"image\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"updateable\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"owned\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timedescription\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"sharedbycount\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"reacted\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/article/webapi/ajax/get_article.graphql?");

/***/ }),

/***/ "./server/totara/engage/resources/article/webapi/ajax/update_article.graphql":
/*!***********************************************************************************!*\
  !*** ./server/totara/engage/resources/article/webapi/ajax/update_article.graphql ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"mutation\",\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_update_article\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_text\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"String\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_access\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_timeview\"}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_recipient_in\"}}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"draft_id\"}},\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"article\"},\"name\":{\"kind\":\"Name\",\"value\":\"engage_article_update\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"resourceid\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"shares\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"draft_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"draft_id\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"resource\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"name\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"access\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"time\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"user\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"card_display\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_picture_alt\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_picture_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"profile_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"display_fields\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"label\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"associate_url\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"is_custom\"},\"arguments\":[],\"directives\":[]}]}}]}}]}}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"content\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"HTML\"}}],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"image\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"updateable\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"owned\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"topics\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timedescription\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timeview\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"sharedbycount\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"bookmarked\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"reacted\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/resources/article/webapi/ajax/update_article.graphql?");

/***/ }),

/***/ "./server/totara/engage/webapi/ajax/advanced_features.graphql":
/*!********************************************************************!*\
  !*** ./server/totara/engage/webapi/ajax/advanced_features.graphql ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_advanced_features\"},\"variableDefinitions\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"features\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_engage_advanced_features\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"library\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"recommenders\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"workspaces\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/engage/webapi/ajax/advanced_features.graphql?");

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

/***/ "editor_weka/components/Weka":
/*!***************************************************************!*\
  !*** external "tui.require(\"editor_weka/components/Weka\")" ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"editor_weka/components/Weka\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22editor_weka/components/Weka\\%22)%22?");

/***/ }),

/***/ "engage_article/components/card/RelatedCard":
/*!******************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/card/RelatedCard\")" ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/card/RelatedCard\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/card/RelatedCard\\%22)%22?");

/***/ }),

/***/ "engage_article/components/content/ArticleContent":
/*!************************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/content/ArticleContent\")" ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/content/ArticleContent\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/content/ArticleContent\\%22)%22?");

/***/ }),

/***/ "engage_article/components/content/ArticleTitle":
/*!**********************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/content/ArticleTitle\")" ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/content/ArticleTitle\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/content/ArticleTitle\\%22)%22?");

/***/ }),

/***/ "engage_article/components/form/ArticleForm":
/*!******************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/form/ArticleForm\")" ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/form/ArticleForm\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/form/ArticleForm\\%22)%22?");

/***/ }),

/***/ "engage_article/components/form/EditArticleContentForm":
/*!*****************************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/form/EditArticleContentForm\")" ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/form/EditArticleContentForm\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/form/EditArticleContentForm\\%22)%22?");

/***/ }),

/***/ "engage_article/components/form/EditArticleTitleForm":
/*!***************************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/form/EditArticleTitleForm\")" ***!
  \***************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/form/EditArticleTitleForm\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/form/EditArticleTitleForm\\%22)%22?");

/***/ }),

/***/ "engage_article/components/separator/ArticleSeparator":
/*!****************************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/separator/ArticleSeparator\")" ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/separator/ArticleSeparator\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/separator/ArticleSeparator\\%22)%22?");

/***/ }),

/***/ "engage_article/components/sidepanel/ArticleSidePanel":
/*!****************************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/sidepanel/ArticleSidePanel\")" ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/sidepanel/ArticleSidePanel\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/sidepanel/ArticleSidePanel\\%22)%22?");

/***/ }),

/***/ "engage_article/components/sidepanel/Related":
/*!*******************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/sidepanel/Related\")" ***!
  \*******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/sidepanel/Related\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/sidepanel/Related\\%22)%22?");

/***/ }),

/***/ "engage_article/components/sidepanel/content/ArticlePlaylistBox":
/*!**************************************************************************************************!*\
  !*** external "tui.require(\"engage_article/components/sidepanel/content/ArticlePlaylistBox\")" ***!
  \**************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"engage_article/components/sidepanel/content/ArticlePlaylistBox\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22engage_article/components/sidepanel/content/ArticlePlaylistBox\\%22)%22?");

/***/ }),

/***/ "totara_comment/components/box/SidePanelCommentBox":
/*!*************************************************************************************!*\
  !*** external "tui.require(\"totara_comment/components/box/SidePanelCommentBox\")" ***!
  \*************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_comment/components/box/SidePanelCommentBox\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_comment/components/box/SidePanelCommentBox\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/buttons/BookmarkButton":
/*!***********************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/buttons/BookmarkButton\")" ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/buttons/BookmarkButton\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/buttons/BookmarkButton\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/buttons/DoneCancelGroup":
/*!************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/buttons/DoneCancelGroup\")" ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/buttons/DoneCancelGroup\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/buttons/DoneCancelGroup\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/card/BaseCard":
/*!**************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/card/BaseCard\")" ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/card/BaseCard\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/card/BaseCard\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/card/CardHeader":
/*!****************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/card/CardHeader\")" ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/card/CardHeader\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/card/CardHeader\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/card/ImageHeader":
/*!*****************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/card/ImageHeader\")" ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/card/ImageHeader\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/card/ImageHeader\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/form/AccessForm":
/*!****************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/form/AccessForm\")" ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/form/AccessForm\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/form/AccessForm\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/form/InlineEditing":
/*!*******************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/form/InlineEditing\")" ***!
  \*******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/form/InlineEditing\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/form/InlineEditing\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/header/ResourceNavigationBar":
/*!*****************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/header/ResourceNavigationBar\")" ***!
  \*****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/header/ResourceNavigationBar\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/header/ResourceNavigationBar\\%22)%22?");

/***/ }),

/***/ "totara_engage/components/icons/StatIcon":
/*!***************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/icons/StatIcon\")" ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/icons/StatIcon\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/icons/StatIcon\\%22)%22?");

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

/***/ "totara_engage/components/sidepanel/EngageSidePanel":
/*!**************************************************************************************!*\
  !*** external "tui.require(\"totara_engage/components/sidepanel/EngageSidePanel\")" ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_engage/components/sidepanel/EngageSidePanel\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_engage/components/sidepanel/EngageSidePanel\\%22)%22?");

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

/***/ "totara_playlist/components/box/ResourcePlaylistBox":
/*!**************************************************************************************!*\
  !*** external "tui.require(\"totara_playlist/components/box/ResourcePlaylistBox\")" ***!
  \**************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_playlist/components/box/ResourcePlaylistBox\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_playlist/components/box/ResourcePlaylistBox\\%22)%22?");

/***/ }),

/***/ "tui/apollo_client":
/*!*****************************************************!*\
  !*** external "tui.require(\"tui/apollo_client\")" ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/apollo_client\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/apollo_client\\%22)%22?");

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

/***/ "tui/components/buttons/ButtonIcon":
/*!*********************************************************************!*\
  !*** external "tui.require(\"tui/components/buttons/ButtonIcon\")" ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/buttons/ButtonIcon\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/buttons/ButtonIcon\\%22)%22?");

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

/***/ "tui/components/form/InputText":
/*!*****************************************************************!*\
  !*** external "tui.require(\"tui/components/form/InputText\")" ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/form/InputText\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/form/InputText\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/AddToList":
/*!******************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/AddToList\")" ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/AddToList\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/AddToList\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Comment":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Comment\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Comment\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Comment\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Info":
/*!*************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Info\")" ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Info\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Info\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Like":
/*!*************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Like\")" ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Like\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Like\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Loading":
/*!****************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Loading\")" ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Loading\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Loading\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/More":
/*!*************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/More\")" ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/More\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/More\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Share":
/*!**************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Share\")" ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Share\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Share\\%22)%22?");

/***/ }),

/***/ "tui/components/icons/Time":
/*!*************************************************************!*\
  !*** external "tui.require(\"tui/components/icons/Time\")" ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/icons/Time\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/icons/Time\\%22)%22?");

/***/ }),

/***/ "tui/components/layouts/LayoutOneColumnContentWithSidePanel":
/*!**********************************************************************************************!*\
  !*** external "tui.require(\"tui/components/layouts/LayoutOneColumnContentWithSidePanel\")" ***!
  \**********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/layouts/LayoutOneColumnContentWithSidePanel\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/layouts/LayoutOneColumnContentWithSidePanel\\%22)%22?");

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

/***/ "tui/components/popover/Popover":
/*!******************************************************************!*\
  !*** external "tui.require(\"tui/components/popover/Popover\")" ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/popover/Popover\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/popover/Popover\\%22)%22?");

/***/ }),

/***/ "tui/components/profile/MiniProfileCard":
/*!**************************************************************************!*\
  !*** external "tui.require(\"tui/components/profile/MiniProfileCard\")" ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/profile/MiniProfileCard\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/profile/MiniProfileCard\\%22)%22?");

/***/ }),

/***/ "tui/notifications":
/*!*****************************************************!*\
  !*** external "tui.require(\"tui/notifications\")" ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/notifications\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/notifications\\%22)%22?");

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