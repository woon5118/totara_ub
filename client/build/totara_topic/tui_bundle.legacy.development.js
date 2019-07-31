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
/******/ 		"totara_topic/tui_bundle.legacy.development": 0
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
/******/ 	deferredModules.push(["./client/src/totara_topic/tui.json","tui/vendors.legacy.development"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/src/totara_topic/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!*******************************************************************************************!*\
  !*** ./client/src/totara_topic/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \*******************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./form/TopicsSelector\": \"./client/src/totara_topic/components/form/TopicsSelector.vue\",\n\t\"./form/TopicsSelector.vue\": \"./client/src/totara_topic/components/form/TopicsSelector.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/src/totara_topic/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/src/totara_topic/components_sync_^(?:(?");

/***/ }),

/***/ "./client/src/totara_topic/components/form/TopicsSelector.vue":
/*!********************************************************************!*\
  !*** ./client/src/totara_topic/components/form/TopicsSelector.vue ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _TopicsSelector_vue_vue_type_template_id_0b40615c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TopicsSelector.vue?vue&type=template&id=0b40615c& */ \"./client/src/totara_topic/components/form/TopicsSelector.vue?vue&type=template&id=0b40615c&\");\n/* harmony import */ var _TopicsSelector_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./TopicsSelector.vue?vue&type=script&lang=js& */ \"./client/src/totara_topic/components/form/TopicsSelector.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _TopicsSelector_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _TopicsSelector_vue_vue_type_template_id_0b40615c___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _TopicsSelector_vue_vue_type_template_id_0b40615c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/totara_topic/components/form/TopicsSelector.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/totara_topic/components/form/TopicsSelector.vue?");

/***/ }),

/***/ "./client/src/totara_topic/components/form/TopicsSelector.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************!*\
  !*** ./client/src/totara_topic/components/form/TopicsSelector.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_TopicsSelector_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--1-0!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./TopicsSelector.vue?vue&type=script&lang=js& */ \"./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_topic/components/form/TopicsSelector.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_node_modules_babel_loader_lib_index_js_ref_1_0_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_TopicsSelector_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/totara_topic/components/form/TopicsSelector.vue?");

/***/ }),

/***/ "./client/src/totara_topic/components/form/TopicsSelector.vue?vue&type=template&id=0b40615c&":
/*!***************************************************************************************************!*\
  !*** ./client/src/totara_topic/components/form/TopicsSelector.vue?vue&type=template&id=0b40615c& ***!
  \***************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_TopicsSelector_vue_vue_type_template_id_0b40615c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./TopicsSelector.vue?vue&type=template&id=0b40615c& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_topic/components/form/TopicsSelector.vue?vue&type=template&id=0b40615c&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_TopicsSelector_vue_vue_type_template_id_0b40615c___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_TopicsSelector_vue_vue_type_template_id_0b40615c___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/totara_topic/components/form/TopicsSelector.vue?");

/***/ }),

/***/ "./client/src/totara_topic/tui.json":
/*!******************************************!*\
  !*** ./client/src/totara_topic/tui.json ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"totara_topic\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"totara_topic\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"totara_topic\")\ntui._bundle.addModulesFromContext(\"totara_topic/components\", __webpack_require__(\"./client/src/totara_topic/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/src/totara_topic/tui.json?");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_topic/components/form/TopicsSelector.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_topic/components/form/TopicsSelector.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var tui_components_tag_TagList__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! tui/components/tag/TagList */ \"tui/components/tag/TagList\");\n/* harmony import */ var tui_components_tag_TagList__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(tui_components_tag_TagList__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var totara_topic_graphql_find_topics__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! totara_topic/graphql/find_topics */ \"./server/totara/topic/webapi/ajax/find_topics.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n // GraphQL queries\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    TagList: tui_components_tag_TagList__WEBPACK_IMPORTED_MODULE_0___default.a\n  },\n  props: {\n    selectedTopics: {\n      type: Array,\n      \"default\": function _default() {\n        return [];\n      },\n      validator: function validator(prop) {\n        var items = Array.prototype.filter.call(prop, function (item) {\n          return !('value' in item) || !('id' in item);\n        });\n        return 0 === items.length;\n      }\n    }\n  },\n  apollo: {\n    topics: {\n      query: totara_topic_graphql_find_topics__WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n      variables: function variables() {\n        return {\n          search: this.searchTerm,\n          exclude: this.selectedTopicIds\n        };\n      }\n    }\n  },\n  data: function data() {\n    return {\n      searchTerm: '',\n      topics: []\n    };\n  },\n  computed: {\n    selectedTopicIds: function selectedTopicIds() {\n      if (0 === this.selectedTopics.length) {\n        return [];\n      }\n\n      return Array.prototype.map.call(this.selectedTopics, function (_ref) {\n        var id = _ref.id;\n        return id;\n      });\n    },\n    pickedTopics: function pickedTopics() {\n      return Array.prototype.map.call(this.selectedTopics, function (_ref2) {\n        var value = _ref2.value,\n            id = _ref2.id;\n        return {\n          id: id,\n          text: value\n        };\n      });\n    }\n  },\n  methods: {\n    /**\n     *\n     * @param {String} term\n     */\n    findTopics: function findTopics(term) {\n      this.searchTerm = term;\n      this.$apollo.queries.topics.refetch();\n    },\n\n    /**\n     *\n     * @param {Number}  id\n     * @param {String}  value\n     */\n    selectTopic: function selectTopic(_ref3) {\n      var id = _ref3.id,\n          value = _ref3.value;\n      var selectedTopics = Array.prototype.concat.call(this.selectedTopics, {\n        id: id,\n        value: value\n      });\n      this.$emit('change', selectedTopics); // Reset search term and refetch the query.\n\n      this.searchTerm = '';\n      this.$apollo.queries.topics.refetch();\n    },\n\n    /**\n     *\n     * @param {Number} id\n     */\n    removeTopic: function removeTopic(_ref4) {\n      var id = _ref4.id;\n      var selectedTopics = Array.prototype.filter.call(this.selectedTopics, function (item) {\n        return item.id !== id;\n      }); // Need to update the list again.\n\n      this.$emit('change', selectedTopics);\n      this.$apollo.queries.topics.refetch();\n    }\n  }\n});\n\n//# sourceURL=webpack:///./client/src/totara_topic/components/form/TopicsSelector.vue?./node_modules/babel-loader/lib??ref--1-0!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/totara_topic/components/form/TopicsSelector.vue?vue&type=template&id=0b40615c&":
/*!****************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/totara_topic/components/form/TopicsSelector.vue?vue&type=template&id=0b40615c& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\"TagList\", {\n    staticClass: \"tui-topicsSelector\",\n    attrs: {\n      filter: _vm.searchTerm,\n      items: _vm.topics,\n      tags: _vm.pickedTopics\n    },\n    on: {\n      filter: _vm.findTopics,\n      select: _vm.selectTopic,\n      remove: _vm.removeTopic\n    },\n    scopedSlots: _vm._u([\n      {\n        key: \"item\",\n        fn: function(ref) {\n          var value = ref.item.value\n          return [_vm._v(\"\\n    \" + _vm._s(value) + \"\\n  \")]\n        }\n      }\n    ])\n  })\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/totara_topic/components/form/TopicsSelector.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./server/totara/topic/webapi/ajax/find_topics.graphql":
/*!*************************************************************!*\
  !*** ./server/totara/topic/webapi/ajax/find_topics.graphql ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"totara_topic_find_topics\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"search\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_text\"}}},\"directives\":[]},{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"exclude\"}},\"type\":{\"kind\":\"ListType\",\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"param_integer\"}}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"alias\":{\"kind\":\"Name\",\"value\":\"topics\"},\"name\":{\"kind\":\"Name\",\"value\":\"totara_topic_find_topics\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"search\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"search\"}}},{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"exclude\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"exclude\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"__typename\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"value\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"format\"},\"value\":{\"kind\":\"EnumValue\",\"value\":\"PLAIN\"}}],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/topic/webapi/ajax/find_topics.graphql?");

/***/ }),

/***/ "tui/components/tag/TagList":
/*!**************************************************************!*\
  !*** external "tui.require(\"tui/components/tag/TagList\")" ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/tag/TagList\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/tag/TagList\\%22)%22?");

/***/ })

/******/ });