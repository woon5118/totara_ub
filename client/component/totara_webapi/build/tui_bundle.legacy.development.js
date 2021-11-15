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
/******/ 	return __webpack_require__(__webpack_require__.s = "./client/component/totara_webapi/src/tui.json");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/component/totara_webapi/src/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!*************************************************************************************************!*\
  !*** ./client/component/totara_webapi/src/pages sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \*************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./TestSession\": \"./client/component/totara_webapi/src/pages/TestSession.vue\",\n\t\"./TestSession.vue\": \"./client/component/totara_webapi/src/pages/TestSession.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/component/totara_webapi/src/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/component/totara_webapi/src/pages_sync_^(?:(?");

/***/ }),

/***/ "./client/component/totara_webapi/src/pages/TestSession.vue":
/*!******************************************************************!*\
  !*** ./client/component/totara_webapi/src/pages/TestSession.vue ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _TestSession_vue_vue_type_template_id_b8b4e836___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TestSession.vue?vue&type=template&id=b8b4e836& */ \"./client/component/totara_webapi/src/pages/TestSession.vue?vue&type=template&id=b8b4e836&\");\n/* harmony import */ var _TestSession_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./TestSession.vue?vue&type=script&lang=js& */ \"./client/component/totara_webapi/src/pages/TestSession.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _TestSession_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _TestSession_vue_vue_type_template_id_b8b4e836___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _TestSession_vue_vue_type_template_id_b8b4e836___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/component/totara_webapi/src/pages/TestSession.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/component/totara_webapi/src/pages/TestSession.vue?");

/***/ }),

/***/ "./client/component/totara_webapi/src/pages/TestSession.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************!*\
  !*** ./client/component/totara_webapi/src/pages/TestSession.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_TestSession_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!../../../../../node_modules/source-map-loader/dist/cjs.js??ref--2-0!./TestSession.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_webapi/src/pages/TestSession.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_node_modules_source_map_loader_dist_cjs_js_ref_2_0_TestSession_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/component/totara_webapi/src/pages/TestSession.vue?");

/***/ }),

/***/ "./client/component/totara_webapi/src/pages/TestSession.vue?vue&type=template&id=b8b4e836&":
/*!*************************************************************************************************!*\
  !*** ./client/component/totara_webapi/src/pages/TestSession.vue?vue&type=template&id=b8b4e836& ***!
  \*************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TestSession_vue_vue_type_template_id_b8b4e836___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js??ref--3-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./TestSession.vue?vue&type=template&id=b8b4e836& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_webapi/src/pages/TestSession.vue?vue&type=template&id=b8b4e836&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TestSession_vue_vue_type_template_id_b8b4e836___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_ref_3_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TestSession_vue_vue_type_template_id_b8b4e836___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/component/totara_webapi/src/pages/TestSession.vue?");

/***/ }),

/***/ "./client/component/totara_webapi/src/tui.json":
/*!*****************************************************!*\
  !*** ./client/component/totara_webapi/src/tui.json ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"totara_webapi\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"totara_webapi\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"totara_webapi\")\ntui._bundle.addModulesFromContext(\"totara_webapi/pages\", __webpack_require__(\"./client/component/totara_webapi/src/pages sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/component/totara_webapi/src/tui.json?");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./node_modules/source-map-loader/dist/cjs.js?!./client/component/totara_webapi/src/pages/TestSession.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0!./client/component/totara_webapi/src/pages/TestSession.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_webapi_graphql_status_nosession__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_webapi/graphql/status_nosession */ \"./server/totara/webapi/webapi/ajax/status_nosession.graphql\");\n/* harmony import */ var totara_webapi_graphql_status__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_webapi/graphql/status */ \"./server/totara/webapi/webapi/ajax/status.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {},\n  props: {},\n  data: function data() {\n    return {\n      sessionButtonText: 'Send session query',\n      nosessionButtonText: 'Send nosession query',\n      nosession: '',\n      session: '',\n      sessionError: '',\n      nosessionError: ''\n    };\n  },\n  methods: {\n    sendSessionQuery: function sendSessionQuery() {\n      this.$apollo.queries.session.skip = false;\n      this.$apollo.queries.session.refetch();\n    },\n    sendNosessionQuery: function sendNosessionQuery() {\n      this.$apollo.queries.nosession.skip = false;\n      this.$apollo.queries.nosession.refetch();\n    }\n  },\n  apollo: {\n    nosession: {\n      query: totara_webapi_graphql_status_nosession__WEBPACK_IMPORTED_MODULE_0__[\"default\"],\n      update: function update(data) {\n        return data.totara_webapi_status;\n      },\n      error: function error(_error) {\n        this.nosessionError = _error.graphQLErrors.length > 0 ? _error.graphQLErrors : _error.networkError;\n        return false;\n      },\n      skip: true,\n      fetchPolicy: 'no-cache'\n    },\n    session: {\n      query: totara_webapi_graphql_status__WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n      update: function update(data) {\n        return data.totara_webapi_status;\n      },\n      error: function error(_error2) {\n        this.sessionError = _error2.graphQLErrors.length > 0 ? _error2.graphQLErrors : _error2.networkError;\n        return false;\n      },\n      skip: true,\n      fetchPolicy: 'no-cache'\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/component/totara_webapi/src/pages/TestSession.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./node_modules/source-map-loader/dist/cjs.js??ref--2-0");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js?!./node_modules/vue-loader/lib/index.js?!./client/component/totara_webapi/src/pages/TestSession.vue?vue&type=template&id=b8b4e836&":
/*!************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options!./client/component/totara_webapi/src/pages/TestSession.vue?vue&type=template&id=b8b4e836& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_c('p',[_vm._v(\"\\n    This is a test page to test queries using sessions and queries using\\n    nosession.\\n  \")]),_vm._v(\" \"),_c('div',[_c('button',{attrs:{\"type\":\"button\"},on:{\"click\":function($event){return _vm.sendSessionQuery()}}},[_vm._v(\"\\n      \"+_vm._s(_vm.sessionButtonText)+\"\\n    \")]),_vm._v(\" \"),(_vm.session)?_c('h4',[_vm._v(\"Result:\")]):_vm._e(),_vm._v(\" \"),(_vm.session)?_c('div',[_c('pre',[_vm._v(_vm._s(_vm.session))])]):_vm._e(),_vm._v(\" \"),(_vm.sessionError)?_c('h4',[_vm._v(\"Error:\")]):_vm._e(),_vm._v(\" \"),(_vm.sessionError)?_c('div',[_c('pre',[_vm._v(_vm._s(_vm.sessionError))])]):_vm._e()]),_vm._v(\" \"),_vm._m(0),_vm._v(\" \"),_c('div',[_c('button',{attrs:{\"type\":\"button\"},on:{\"click\":function($event){return _vm.sendNosessionQuery()}}},[_vm._v(\"\\n      \"+_vm._s(_vm.nosessionButtonText)+\"\\n    \")]),_vm._v(\" \"),(_vm.nosession)?_c('h3',[_vm._v(\"Result:\")]):_vm._e(),_vm._v(\" \"),(_vm.nosession)?_c('div',[_c('pre',[_vm._v(_vm._s(_vm.nosession))])]):_vm._e(),_vm._v(\" \"),(_vm.nosessionError)?_c('h4',[_vm._v(\"Error:\")]):_vm._e(),_vm._v(\" \"),(_vm.nosessionError)?_c('div',[_c('pre',[_vm._v(_vm._s(_vm.nosessionError))])]):_vm._e()])])}\nvar staticRenderFns = [function () {var _vm=this;var _h=_vm.$createElement;var _c=_vm._self._c||_h;return _c('div',[_c('hr')])}]\nrender._withStripped = true\n\n\n//# sourceURL=webpack:///./client/component/totara_webapi/src/pages/TestSession.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js??ref--3-0!./node_modules/vue-loader/lib??vue-loader-options");

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

/***/ "./server/totara/webapi/webapi/ajax/status.graphql":
/*!*********************************************************!*\
  !*** ./server/totara/webapi/webapi/ajax/status.graphql ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_webapi_status\"},\"variableDefinitions\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_webapi_status\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"status\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timestamp\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/webapi/webapi/ajax/status.graphql?");

/***/ }),

/***/ "./server/totara/webapi/webapi/ajax/status_nosession.graphql":
/*!*******************************************************************!*\
  !*** ./server/totara/webapi/webapi/ajax/status_nosession.graphql ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_webapi_status_nosession\"},\"variableDefinitions\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_webapi_status\"},\"arguments\":[],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"status\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"timestamp\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/webapi/webapi/ajax/status_nosession.graphql?");

/***/ })

/******/ });