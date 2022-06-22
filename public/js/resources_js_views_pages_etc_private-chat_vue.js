"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_views_pages_etc_private-chat_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/pages/etc/private-chat.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/pages/etc/private-chat.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../store */ "./resources/js/store/index.js");
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../api */ "./resources/js/api/index.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  data: function data() {
    return {
      user: _store__WEBPACK_IMPORTED_MODULE_0__["default"].getters.getLoggedUser,
      message: null,
      activeFriend: null,
      allMessages: [],
      users: []
    };
  },
  watch: {
    activeFriend: function activeFriend(val) {
      this.fetchMessages();
    }
  },
  methods: {
    sendMessage: function sendMessage() {
      var _this = this;

      //check if there message
      if (!this.message) {
        return alert('Please enter message');
      }

      if (!this.activeFriend) {
        return alert('Please select friend');
      }

      axios.post('/private-messages/' + this.activeFriend, {
        message: this.message
      }).then(function (response) {
        _this.message = null;

        _this.allMessages.push(response.data.message);

        setTimeout(_this.scrollToEnd, 100);
      });
    },
    fetchMessages: function fetchMessages() {
      var _this2 = this;

      if (!this.activeFriend) {
        return alert('Please select friend');
      }

      axios.get('/private-messages/' + this.activeFriend).then(function (response) {
        _this2.allMessages = response.data;
      });
    },
    fetchUsers: function fetchUsers() {
      var _this3 = this;

      axios.get('/users').then(function (response) {
        _this3.users = response.data;
      });
    },
    scrollToEnd: function scrollToEnd() {
      document.getElementById('privateMessageBox').scrollTo(0, 99999);
    }
  },
  mounted: function mounted() {},
  created: function created() {
    var _this4 = this;

    this.fetchUsers();
    Echo["private"]('privatechat.' + this.user.id).listen('PrivateMessageSent', function (e) {
      _this4.activeFriend = e.message.user_id;

      _this4.allMessages.push(e.message);

      setTimeout(_this4.scrollToEnd, 100);
    });
  }
});

/***/ }),

/***/ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/pages/etc/private-chat.vue?vue&type=style&index=0&id=3fbb30a7&scoped=true&lang=css&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/pages/etc/private-chat.vue?vue&type=style&index=0&id=3fbb30a7&scoped=true&lang=css& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../../node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js */ "./node_modules/laravel-mix/node_modules/css-loader/dist/runtime/api.js");
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0__);
// Imports

var ___CSS_LOADER_EXPORT___ = _node_modules_laravel_mix_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_0___default()(function(i){return i[1]});
// Module
___CSS_LOADER_EXPORT___.push([module.id, "\n.online-users[data-v-3fbb30a7],.messages[data-v-3fbb30a7]{\n  overflow-y:scroll;\n  height:100vh;\n}\n\n", ""]);
// Exports
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (___CSS_LOADER_EXPORT___);


/***/ }),

/***/ "./node_modules/style-loader/dist/cjs.js!./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/pages/etc/private-chat.vue?vue&type=style&index=0&id=3fbb30a7&scoped=true&lang=css&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader/dist/cjs.js!./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/pages/etc/private-chat.vue?vue&type=style&index=0&id=3fbb30a7&scoped=true&lang=css& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../../../../../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_8_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_8_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_private_chat_vue_vue_type_style_index_0_id_3fbb30a7_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../../../../../node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./private-chat.vue?vue&type=style&index=0&id=3fbb30a7&scoped=true&lang=css& */ "./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/pages/etc/private-chat.vue?vue&type=style&index=0&id=3fbb30a7&scoped=true&lang=css&");

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()(_node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_8_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_8_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_private_chat_vue_vue_type_style_index_0_id_3fbb30a7_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_1__["default"], options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_8_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_8_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_private_chat_vue_vue_type_style_index_0_id_3fbb30a7_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_1__["default"].locals || {});

/***/ }),

/***/ "./resources/js/views/pages/etc/private-chat.vue":
/*!*******************************************************!*\
  !*** ./resources/js/views/pages/etc/private-chat.vue ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _private_chat_vue_vue_type_template_id_3fbb30a7_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./private-chat.vue?vue&type=template&id=3fbb30a7&scoped=true& */ "./resources/js/views/pages/etc/private-chat.vue?vue&type=template&id=3fbb30a7&scoped=true&");
/* harmony import */ var _private_chat_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./private-chat.vue?vue&type=script&lang=js& */ "./resources/js/views/pages/etc/private-chat.vue?vue&type=script&lang=js&");
/* harmony import */ var _private_chat_vue_vue_type_style_index_0_id_3fbb30a7_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./private-chat.vue?vue&type=style&index=0&id=3fbb30a7&scoped=true&lang=css& */ "./resources/js/views/pages/etc/private-chat.vue?vue&type=style&index=0&id=3fbb30a7&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");



;


/* normalize component */

var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _private_chat_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _private_chat_vue_vue_type_template_id_3fbb30a7_scoped_true___WEBPACK_IMPORTED_MODULE_0__.render,
  _private_chat_vue_vue_type_template_id_3fbb30a7_scoped_true___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  "3fbb30a7",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/views/pages/etc/private-chat.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/views/pages/etc/private-chat.vue?vue&type=script&lang=js&":
/*!********************************************************************************!*\
  !*** ./resources/js/views/pages/etc/private-chat.vue?vue&type=script&lang=js& ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_private_chat_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./private-chat.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5.use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/pages/etc/private-chat.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_private_chat_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/views/pages/etc/private-chat.vue?vue&type=style&index=0&id=3fbb30a7&scoped=true&lang=css&":
/*!****************************************************************************************************************!*\
  !*** ./resources/js/views/pages/etc/private-chat.vue?vue&type=style&index=0&id=3fbb30a7&scoped=true&lang=css& ***!
  \****************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_dist_cjs_js_node_modules_laravel_mix_node_modules_css_loader_dist_cjs_js_clonedRuleSet_8_use_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_dist_cjs_js_clonedRuleSet_8_use_2_node_modules_vue_loader_lib_index_js_vue_loader_options_private_chat_vue_vue_type_style_index_0_id_3fbb30a7_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/style-loader/dist/cjs.js!../../../../../node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./private-chat.vue?vue&type=style&index=0&id=3fbb30a7&scoped=true&lang=css& */ "./node_modules/style-loader/dist/cjs.js!./node_modules/laravel-mix/node_modules/css-loader/dist/cjs.js??clonedRuleSet-8.use[1]!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-8.use[2]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/pages/etc/private-chat.vue?vue&type=style&index=0&id=3fbb30a7&scoped=true&lang=css&");


/***/ }),

/***/ "./resources/js/views/pages/etc/private-chat.vue?vue&type=template&id=3fbb30a7&scoped=true&":
/*!**************************************************************************************************!*\
  !*** ./resources/js/views/pages/etc/private-chat.vue?vue&type=template&id=3fbb30a7&scoped=true& ***!
  \**************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_private_chat_vue_vue_type_template_id_3fbb30a7_scoped_true___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_private_chat_vue_vue_type_template_id_3fbb30a7_scoped_true___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_private_chat_vue_vue_type_template_id_3fbb30a7_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./private-chat.vue?vue&type=template&id=3fbb30a7&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/pages/etc/private-chat.vue?vue&type=template&id=3fbb30a7&scoped=true&");


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/pages/etc/private-chat.vue?vue&type=template&id=3fbb30a7&scoped=true&":
/*!*****************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/views/pages/etc/private-chat.vue?vue&type=template&id=3fbb30a7&scoped=true& ***!
  \*****************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "v-layout",
    { attrs: { row: "" } },
    [
      _c(
        "v-flex",
        { staticClass: "online-users", attrs: { xs3: "" } },
        [
          _c(
            "v-list",
            _vm._l(_vm.users, function (friend) {
              return _vm.user.id != friend.id
                ? _c(
                    "v-list-tile",
                    {
                      key: friend.id,
                      attrs: {
                        color: friend.id == _vm.activeFriend ? "green" : "",
                      },
                      on: {
                        click: function ($event) {
                          _vm.activeFriend = friend.id
                        },
                      },
                    },
                    [
                      _c(
                        "v-list-tile-action",
                        [
                          _c("v-icon", { attrs: { color: "green" } }, [
                            _vm._v("account_circle"),
                          ]),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "v-list-tile-content",
                        [
                          _c("v-list-tile-title", [
                            _vm._v(_vm._s(friend.name)),
                          ]),
                        ],
                        1
                      ),
                    ],
                    1
                  )
                : _vm._e()
            }),
            1
          ),
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "v-flex",
        {
          staticClass: "messages mb-5",
          attrs: { id: "privateMessageBox", xs9: "" },
        },
        [
          _c(
            "v-list",
            _vm._l(_vm.allMessages, function (message, index) {
              return _c(
                "v-list-tile",
                { key: index, staticClass: "p-3" },
                [
                  _c(
                    "v-layout",
                    {
                      attrs: {
                        "align-end": _vm.user.id !== message.user.id,
                        column: "",
                      },
                    },
                    [
                      _c(
                        "v-flex",
                        [
                          _c(
                            "v-layout",
                            { attrs: { column: "" } },
                            [
                              _c("v-flex", [
                                _c(
                                  "span",
                                  { staticClass: "small font-italic" },
                                  [_vm._v(_vm._s(message.user.name))]
                                ),
                              ]),
                              _vm._v(" "),
                              _c(
                                "v-flex",
                                [
                                  _c(
                                    "v-chip",
                                    {
                                      attrs: {
                                        color:
                                          _vm.user.id !== message.user.id
                                            ? "red"
                                            : "green",
                                        "text-color": "white",
                                      },
                                    },
                                    [
                                      _c("v-list-tile-content", [
                                        _vm._v(
                                          "\n                      " +
                                            _vm._s(message.message) +
                                            "\n                    "
                                        ),
                                      ]),
                                    ],
                                    1
                                  ),
                                ],
                                1
                              ),
                              _vm._v(" "),
                              _c(
                                "v-flex",
                                { staticClass: "caption font-italic" },
                                [
                                  _vm._v(
                                    "\n                  " +
                                      _vm._s(message.created_at) +
                                      "\n                "
                                  ),
                                ]
                              ),
                            ],
                            1
                          ),
                        ],
                        1
                      ),
                    ],
                    1
                  ),
                ],
                1
              )
            }),
            1
          ),
          _vm._v(" "),
          _c(
            "v-footer",
            { attrs: { height: "auto", fixed: "", color: "grey" } },
            [
              _c(
                "v-layout",
                { attrs: { row: "" } },
                [
                  _c(
                    "v-flex",
                    {
                      attrs: {
                        xs6: "",
                        "offset-xs3": "",
                        "justify-center": "",
                        "align-center": "",
                      },
                    },
                    [
                      _c("v-text-field", {
                        attrs: {
                          rows: "2",
                          label: "Enter Message",
                          "single-line": "",
                        },
                        on: {
                          keyup: function ($event) {
                            if (
                              !$event.type.indexOf("key") &&
                              _vm._k(
                                $event.keyCode,
                                "enter",
                                13,
                                $event.key,
                                "Enter"
                              )
                            ) {
                              return null
                            }
                            return _vm.sendMessage.apply(null, arguments)
                          },
                        },
                        model: {
                          value: _vm.message,
                          callback: function ($$v) {
                            _vm.message = $$v
                          },
                          expression: "message",
                        },
                      }),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "v-flex",
                    { attrs: { xs2: "" } },
                    [
                      _c(
                        "v-btn",
                        {
                          staticClass: "mt-3 ml-2 white--text",
                          attrs: { dark: "", small: "", color: "green" },
                          on: { click: _vm.sendMessage },
                        },
                        [_vm._v("send")]
                      ),
                    ],
                    1
                  ),
                ],
                1
              ),
            ],
            1
          ),
        ],
        1
      ),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ })

}]);