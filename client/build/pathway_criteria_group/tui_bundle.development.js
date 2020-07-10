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
/******/ 		"pathway_criteria_group/tui_bundle.development": 0
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
/******/ 	deferredModules.push(["./client/src/pathway_criteria_group/tui.json","tui/vendors.development"]);
/******/ 	// run deferred modules when ready
/******/ 	return checkDeferredModules();
/******/ })
/************************************************************************/
/******/ ({

/***/ "./client/src/pathway_criteria_group/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\]internal[/\\\\]).)*$":
/*!*****************************************************************************************************!*\
  !*** ./client/src/pathway_criteria_group/components sync ^(?:(?!__[a-z]*__|[/\\]internal[/\\]).)*$ ***!
  \*****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("var map = {\n\t\"./achievements/AchievementDisplay\": \"./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue\",\n\t\"./achievements/AchievementDisplay.vue\": \"./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue\"\n};\n\n\nfunction webpackContext(req) {\n\tvar id = webpackContextResolve(req);\n\treturn __webpack_require__(id);\n}\nfunction webpackContextResolve(req) {\n\tif(!__webpack_require__.o(map, req)) {\n\t\tvar e = new Error(\"Cannot find module '\" + req + \"'\");\n\t\te.code = 'MODULE_NOT_FOUND';\n\t\tthrow e;\n\t}\n\treturn map[req];\n}\nwebpackContext.keys = function webpackContextKeys() {\n\treturn Object.keys(map);\n};\nwebpackContext.resolve = webpackContextResolve;\nmodule.exports = webpackContext;\nwebpackContext.id = \"./client/src/pathway_criteria_group/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\";\n\n//# sourceURL=webpack:///__%5Ba-z%5D*__%7C%5B/\\\\%5Dinternal%5B/\\\\%5D).)*$?./client/src/pathway_criteria_group/components_sync_^(?:(?");

/***/ }),

/***/ "./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue":
/*!******************************************************************************************!*\
  !*** ./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _AchievementDisplay_vue_vue_type_template_id_46757e02___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AchievementDisplay.vue?vue&type=template&id=46757e02& */ \"./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?vue&type=template&id=46757e02&\");\n/* harmony import */ var _AchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./AchievementDisplay.vue?vue&type=script&lang=js& */ \"./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ \"./node_modules/vue-loader/lib/runtime/componentNormalizer.js\");\n\n\n\n\n\n/* normalize component */\n\nvar component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(\n  _AchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__[\"default\"],\n  _AchievementDisplay_vue_vue_type_template_id_46757e02___WEBPACK_IMPORTED_MODULE_0__[\"render\"],\n  _AchievementDisplay_vue_vue_type_template_id_46757e02___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"],\n  false,\n  null,\n  null,\n  null\n  \n)\n\ncomponent.options.__hasBlocks = {\"script\":true,\"template\":true};\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = \"client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue\"\n/* harmony default export */ __webpack_exports__[\"default\"] = (component.exports);\n\n//# sourceURL=webpack:///./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************!*\
  !*** ./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_AchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./AchievementDisplay.vue?vue&type=script&lang=js& */ \"./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?vue&type=script&lang=js&\");\n/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__[\"default\"] = (_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_AchievementDisplay_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__[\"default\"]); \n\n//# sourceURL=webpack:///./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?vue&type=template&id=46757e02&":
/*!*************************************************************************************************************************!*\
  !*** ./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?vue&type=template&id=46757e02& ***!
  \*************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_AchievementDisplay_vue_vue_type_template_id_46757e02___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../tooling/webpack/tui_vue_loader.js!../../../../../node_modules/vue-loader/lib??vue-loader-options!./AchievementDisplay.vue?vue&type=template&id=46757e02& */ \"./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?vue&type=template&id=46757e02&\");\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_AchievementDisplay_vue_vue_type_template_id_46757e02___WEBPACK_IMPORTED_MODULE_0__[\"render\"]; });\n\n/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_tooling_webpack_tui_vue_loader_js_node_modules_vue_loader_lib_index_js_vue_loader_options_AchievementDisplay_vue_vue_type_template_id_46757e02___WEBPACK_IMPORTED_MODULE_0__[\"staticRenderFns\"]; });\n\n\n\n//# sourceURL=webpack:///./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?");

/***/ }),

/***/ "./client/src/pathway_criteria_group/tui.json":
/*!****************************************************!*\
  !*** ./client/src/pathway_criteria_group/tui.json ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("!function() {\n\"use strict\";\n\nif (typeof tui !== 'undefined' && tui._bundle.isLoaded(\"pathway_criteria_group\")) {\n  console.warn(\n    '[tui bundle] The bundle \"' + \"pathway_criteria_group\" +\n    '\" is already loaded, skipping initialisation.'\n  );\n  return;\n};\ntui._bundle.register(\"pathway_criteria_group\")\ntui._bundle.addModulesFromContext(\"pathway_criteria_group/components\", __webpack_require__(\"./client/src/pathway_criteria_group/components sync recursive ^(?:(?!__[a-z]*__|[/\\\\\\\\]internal[/\\\\\\\\]).)*$\"));\n}();\n\n//# sourceURL=webpack:///./client/src/pathway_criteria_group/tui.json?");

/***/ }),

/***/ "./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************************************!*\
  !*** ./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! totara_competency/components/achievements/AchievementLayout */ \"totara_competency/components/achievements/AchievementLayout\");\n/* harmony import */ var totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var tui_components_decor_AndBox__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! tui/components/decor/AndBox */ \"tui/components/decor/AndBox\");\n/* harmony import */ var tui_components_decor_AndBox__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(tui_components_decor_AndBox__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var pathway_criteria_group_graphql_achievements__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! pathway_criteria_group/graphql/achievements */ \"./server/totara/competency/pathway/criteria_group/webapi/ajax/achievements.graphql\");\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n// Components\n\n\n\n// GraphQL\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  components: {\n    AchievementLayout: (totara_competency_components_achievements_AchievementLayout__WEBPACK_IMPORTED_MODULE_0___default()),\n    AndBox: (tui_components_decor_AndBox__WEBPACK_IMPORTED_MODULE_1___default()),\n  },\n\n  inheritAttrs: false,\n\n  props: {\n    assignmentId: {\n      required: true,\n      type: Number,\n    },\n    dateAchieved: {\n      type: String,\n    },\n    instanceId: {\n      required: true,\n      type: Number,\n    },\n    userId: {\n      required: true,\n      type: Number,\n    },\n  },\n\n  data: function() {\n    return {\n      achievements: [],\n      itemsLoaded: 0,\n    };\n  },\n\n  apollo: {\n    achievements: {\n      query: pathway_criteria_group_graphql_achievements__WEBPACK_IMPORTED_MODULE_2__[\"default\"],\n      context: { batch: true },\n      variables() {\n        return {\n          instance_id: this.instanceId,\n        };\n      },\n      update({ pathway_criteria_group_achievements: achievements }) {\n        let newAchievementComponents = [];\n        achievements.forEach(achievement => {\n          let compPath = `criteria_${achievement.type}/components/achievements/AchievementDisplay`;\n\n          newAchievementComponents.push({\n            component: tui.asyncComponent(compPath),\n            props: {\n              assignmentId: this.assignmentId,\n              dateAchieved: this.dateAchieved,\n              instanceId: parseInt(achievement.instance_id),\n              userId: this.userId,\n            },\n          });\n        });\n\n        // Make sure event is fired even if there are no items\n        if (newAchievementComponents.length === 0) {\n          this.$emit('loaded');\n        }\n\n        return newAchievementComponents;\n      },\n    },\n  },\n\n  computed: {\n    /**\n     * Calculates the number of items and returns the value\n     *\n     * @return {Int}\n     */\n    numberOfItems() {\n      return this.achievements.length;\n    },\n  },\n\n  watch: {\n    /**\n     * Check if all items are loaded, emit a 'loaded' event if they are\n     *\n     * @param {Object} loadedItems\n     */\n    itemsLoaded: function(loadedItems) {\n      if (loadedItems === this.numberOfItems) {\n        this.$emit('loaded');\n      }\n    },\n  },\n\n  methods: {\n    /**\n     * Checks if current item is last and returns a bool\n     *\n     * @param {Int} id\n     * @param {Array} items\n     * @return {Boolean}\n     */\n    isLastItem(id, items) {\n      return id === items.length - 1;\n    },\n\n    /**\n     * Increments number of items loaded\n     *\n     */\n    itemLoaded() {\n      this.itemsLoaded += 1;\n    },\n  },\n});\n\n\n//# sourceURL=webpack:///./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib/index.js?!./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?vue&type=template&id=46757e02&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options!./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?vue&type=template&id=46757e02& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"render\", function() { return render; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"staticRenderFns\", function() { return staticRenderFns; });\nvar render = function() {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c(\n    \"div\",\n    { staticClass: \"tui-pathwayCriteriaGroupAchievement\" },\n    [\n      _vm._l(_vm.achievements, function(component, id) {\n        return [\n          _c(\n            \"div\",\n            {\n              key: id,\n              staticClass: \"tui-pathwayCriteriaGroupAchievement__item\"\n            },\n            [\n              _c(\n                component.component,\n                _vm._b(\n                  { tag: \"component\", on: { loaded: _vm.itemLoaded } },\n                  \"component\",\n                  component.props,\n                  false\n                )\n              )\n            ],\n            1\n          ),\n          _vm._v(\" \"),\n          !_vm.isLastItem(id, _vm.achievements)\n            ? _c(\n                \"div\",\n                {\n                  key: id + \"andseparator\",\n                  staticClass: \"tui-pathwayCriteriaGroupAchievement__separator\"\n                },\n                [\n                  _c(\"AchievementLayout\", {\n                    attrs: { \"no-borders\": true },\n                    scopedSlots: _vm._u(\n                      [\n                        {\n                          key: \"left\",\n                          fn: function() {\n                            return [_c(\"AndBox\")]\n                          },\n                          proxy: true\n                        }\n                      ],\n                      null,\n                      true\n                    )\n                  })\n                ],\n                1\n              )\n            : _vm._e()\n        ]\n      })\n    ],\n    2\n  )\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack:///./client/src/pathway_criteria_group/components/achievements/AchievementDisplay.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./client/tooling/webpack/tui_vue_loader.js!./node_modules/vue-loader/lib??vue-loader-options");

/***/ }),

/***/ "./server/totara/competency/pathway/criteria_group/webapi/ajax/achievements.graphql":
/*!******************************************************************************************!*\
  !*** ./server/totara/competency/pathway/criteria_group/webapi/ajax/achievements.graphql ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n\n    var doc = {\"kind\":\"Document\",\"definitions\":[{\"kind\":\"OperationDefinition\",\"operation\":\"query\",\"name\":{\"kind\":\"Name\",\"value\":\"pathway_criteria_group_achievements\"},\"variableDefinitions\":[{\"kind\":\"VariableDefinition\",\"variable\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instance_id\"}},\"type\":{\"kind\":\"NonNullType\",\"type\":{\"kind\":\"NamedType\",\"name\":{\"kind\":\"Name\",\"value\":\"core_id\"}}},\"directives\":[]}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"pathway_criteria_group_achievements\"},\"arguments\":[{\"kind\":\"Argument\",\"name\":{\"kind\":\"Name\",\"value\":\"instance_id\"},\"value\":{\"kind\":\"Variable\",\"name\":{\"kind\":\"Name\",\"value\":\"instance_id\"}}}],\"directives\":[],\"selectionSet\":{\"kind\":\"SelectionSet\",\"selections\":[{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"instance_id\"},\"arguments\":[],\"directives\":[]},{\"kind\":\"Field\",\"name\":{\"kind\":\"Name\",\"value\":\"type\"},\"arguments\":[],\"directives\":[]}]}}]}}]};\n    /* harmony default export */ __webpack_exports__[\"default\"] = (doc);\n  \n\n//# sourceURL=webpack:///./server/totara/competency/pathway/criteria_group/webapi/ajax/achievements.graphql?");

/***/ }),

/***/ "totara_competency/components/achievements/AchievementLayout":
/*!***********************************************************************************************!*\
  !*** external "tui.require(\"totara_competency/components/achievements/AchievementLayout\")" ***!
  \***********************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"totara_competency/components/achievements/AchievementLayout\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22totara_competency/components/achievements/AchievementLayout\\%22)%22?");

/***/ }),

/***/ "tui/components/decor/AndBox":
/*!***************************************************************!*\
  !*** external "tui.require(\"tui/components/decor/AndBox\")" ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("module.exports = tui.require(\"tui/components/decor/AndBox\");\n\n//# sourceURL=webpack:///external_%22tui.require(\\%22tui/components/decor/AndBox\\%22)%22?");

/***/ })

/******/ });