/******/ (function(modules) { // webpackBootstrap
/******/  // The module cache
/******/  var installedModules = {};
/******/
/******/  // The require function
/******/  function __webpack_require__(moduleId) {
/******/console.log(moduleId);
/******/    // Check if module is in cache
/******/    if(installedModules[moduleId]) {
/******/      return installedModules[moduleId].exports;
/******/    }
/******/    // Create a new module (and put it into the cache)
/******/    var module = installedModules[moduleId] = {
/******/      i: moduleId,
/******/      l: false,
/******/      exports: {}
/******/    };
/******/
/******/    // Execute the module function
/******/    modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/    // Flag the module as loaded
/******/    module.l = true;
/******/
/******/    // Return the exports of the module
/******/    return module.exports;
/******/  }
/******/
/******/
/******/  // expose the modules object (__webpack_modules__)
/******/  __webpack_require__.m = modules;
/******/
/******/  // expose the module cache
/******/  __webpack_require__.c = installedModules;
/******/
/******/  // define getter function for harmony exports
/******/  __webpack_require__.d = function(exports, name, getter) {
/******/    if(!__webpack_require__.o(exports, name)) {
/******/      Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/    }
/******/  };
/******/
/******/  // define __esModule on exports
/******/  __webpack_require__.r = function(exports) {
/******/    if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/      Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/    }
/******/    Object.defineProperty(exports, '__esModule', { value: true });
/******/  };
/******/
/******/  // create a fake namespace object
/******/  // mode & 1: value is a module id, require it
/******/  // mode & 2: merge all properties of value into the ns
/******/  // mode & 4: return value when already ns object
/******/  // mode & 8|1: behave like require
/******/  __webpack_require__.t = function(value, mode) {
/******/    if(mode & 1) value = __webpack_require__(value);
/******/    if(mode & 8) return value;
/******/    if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/    var ns = Object.create(null);
/******/    __webpack_require__.r(ns);
/******/    Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/    if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/    return ns;
/******/  };
/******/
/******/  // getDefaultExport function for compatibility with non-harmony modules
/******/  __webpack_require__.n = function(module) {
/******/    var getter = module && module.__esModule ?
/******/      function getDefault() { return module['default']; } :
/******/      function getModuleExports() { return module; };
/******/    __webpack_require__.d(getter, 'a', getter);
/******/    return getter;
/******/  };
/******/
/******/  // Object.prototype.hasOwnProperty.call
/******/  __webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/  // __webpack_public_path__
/******/  __webpack_require__.p = "";
/******/
/******/
/******/  // Load entry module and return exports
/******/  return __webpack_require__(__webpack_require__.s = "./js/src/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/src/blocks/razorpay/block.scss":
/*!***************************************!*\
  !*** ./js/src/blocks/form/block.scss ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "./js/src/blocks/razorpay/edit.js":
/*!************************************!*\
  !*** ./js/src/blocks/form/edit.js ***!
  \************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _block_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./block.scss */ "./js/src/blocks/razorpay/block.scss");
/* harmony import */ var _block_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_block_scss__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _icon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./icon */ "./js/src/blocks/razorpay/icon.js");
/**
 * WordPress dependencies
 */
const { PanelBody, Placeholder, SelectControl, ServerSideRender, TextControl, TextareaControl, ToggleControl } = wp.components;
const { InspectorControls } = wp.blockEditor;
const { Component, Fragment } = wp.element;
const { __ } = wp.i18n;

class Edit extends Component {

  constructor() {

    super(...arguments);

    // Set initial state.
    this.state = { buttonWasDeleted: false, formConstruct: '' };

    // Bind events.
    this.setButtonId = this.setButtonId.bind(this);

    // Get defined form ID.
    const { buttonId,buttontype } = this.props.attributes;

    if(buttontype == undefined){
      this.props.setAttributes({ buttontype: 'payment' });
    }
    // If form has been selected, disable preview / reset.
    if (buttonId) {
      
      // Get form object.
      const button = Edit.getButton(buttonId,buttontype);

      // If form was not found, reset block.
      if (!button) {

        // Reset form ID.
        this.props.setAttributes({ buttonId: '' });

        // Set failed state.
        this.state = { buttonWasDeleted: true };

        // If form was found and has conditional logic, disable preview.
      } else if (button) {
        this.props.setAttributes({ buttonPreview: false });
      }
    }
  }

  componentWillUnmount() {
    this.unmounting = true;
  }

  setButtonId(buttonId) {
   
    let {  buttontype }  = this.props.attributes;
    let button = Edit.getButton(buttonId,buttontype);
    this.props.setAttributes({ buttonId });
    this.setState({ buttonWasDeleted: false });

    if (button) {
      this.props.setAttributes({ buttonPreview: false });
    }
  }
  
  

  static getButton(buttonId,buttontype) {
    
    return buttontype=='payment'?razorpay.payment_buttons.find(button => button.id == buttonId):razorpay.subscription_button.find(button => button.id == buttonId);
  }


  static getButtonOptions(buttontype) {
    
    let options = [{
      label: 'Select a button',
      value: ''
    }];
    let buttonValue = buttontype=='payment'?razorpay.payment_buttons:razorpay.subscription_button;
    for (let i = 0; i < buttonValue.length; i++) {

      let button = buttonValue[i];

      options.push({
        label: button.title,
        value: button.id
      });
    }

    return options;
  }
  
  render() {

    let { buttonId, buttonContent, title, tabindex, buttonPreview , buttontype }  = this.props.attributes;

    const { setAttributes, isSelected } = this.props;

    const toggleTitle = () => setAttributes({ title: !title });
    const togglebuttonPreview = () => setAttributes({ buttonPreview: !buttonPreview });

    const updateTabindex = tabindex => setAttributes({ tabindex });

    const setButtonIdFromPlaceholder = (e) => {
      //alert(e.target.value);
      this.setButtonId(e.target.value);
      // this.setButtonContent(e.target.value);
    }
  

    const displaypayment = (e) => {
     this.props.setAttributes({ buttontype: e.target.value});
     this.setButtonId('');
     
    }
   

    const controls = [isSelected && buttontype!=undefined && React.createElement(
      InspectorControls,
      { key: 'inspector' },
      React.createElement(
        PanelBody,
        {
          title: 'Button Settings'
        },
        React.createElement(SelectControl, {
          label: 'Button',
          value: buttonId,
          options: Edit.getButtonOptions(buttontype),
          onChange: this.setButtonId
        }),
        buttonId && React.createElement(ToggleControl, {
          label: 'Payment Button Title',
          checked: title,
          onChange: toggleTitle
        })
      ),
      buttonId && React.createElement(
        PanelBody,
        {
          title: 'Preview',
          initialOpen: false,
          className: 'rzp-block__panel'
        },
        buttonId && React.createElement(ToggleControl, {
          label: 'Preview',
          checked: buttonPreview,
          onChange: togglebuttonPreview
        }),
        React.createElement(TextControl, {
          className: 'rzp-block__tabindex',
          label: 'Tabindex',
          type: 'number',
          value: tabindex,
          onChange: updateTabindex,
          placeholder: '-1'
        }),
        React.createElement(
          Fragment,
          null,
          'Button ID: ',
          buttonId
        )
      )
    )];

    const formContent = React.createElement(
        'p',
        null,
        ''
      );
    if(buttonId) {
      
      if(buttontype =='payment'){
  

        const formConstruct = React.createElement("form", null, 
        React.createElement("script", {
          src: "https://cdn.razorpay.com/static/widget/payment-button.js",
          "data-plugin": "wordpress_payment_button"+razorpay.payment_buttons_plugin_version,
          "data-payment_button_id": buttonId,
          "async": 1
        }, " "), " ");
      const updatedContent = '<form><script src="https://cdn.razorpay.com/static/widget/payment-button.js" data-plugin ="wordpress_payment_button_'+razorpay.payment_buttons_plugin_version+'" data-payment_button_id="'+ buttonId +'"> </script> </form>';
      this.props.setAttributes({ buttonContent: updatedContent });
      formContent.props.children = formConstruct;
      }else{
        const formConstruct = React.createElement("form", null, 
        React.createElement("script", {
          src: "https://cdn.razorpay.com/static/widget/subscription-button.js",
          "data-plugin": "wordpress_subscription_button"+razorpay.payment_buttons_plugin_version,
          "data-subscription_button_id": buttonId,
          "async": 1
        }, " "), " ");
      const updatedContent = '<form><script src="https://cdn.razorpay.com/static/widget/subscription-button.js" data-plugin ="wordpress_subscription_button_'+razorpay.payment_buttons_plugin_version+'" data-subscription_button_id="'+ buttonId +'"> </script> </form>';
      this.props.setAttributes({ buttonContent: updatedContent });
      formContent.props.children = formConstruct;
      }
    }
    else {
      this.props.setAttributes({ buttonContent: "" });
    }
    if (!buttonId || !buttonPreview) {

      const { buttonWasDeleted } = this.state;
      

      return [controls, buttonWasDeleted && React.createElement(
        'div',
          { className: 'rzp-block__alert rzp-block__alert-error' },
          React.createElement(
            'p',
            null,
            'The selected form has been deleted or trashed. Please select a new form.'
          )
          ), React.createElement(
          Placeholder,
          { key: 'placeholder', className: 'wp-block-embed rzp-block__placeholder'},
          React.createElement(
            'form',
          { className: 'rzp-block__placeholder-select'},
      
          React.createElement("div", {className: "rzp-radiostyle"}, 
          React.createElement(
            "input", 
            {
            type: "radio",
            id: "payment",
            name: "buttontype",
            value: "payment",
            checked: buttontype=='payment'?true:false,
            onChange: displaypayment
            }),
          React.createElement("label", null,"Payment Button")),
          React.createElement("div", {className: "rzp-radiostyle"}, 
          React.createElement(
            "input", {
            type: "radio",
            id: "subscription",
            checked: buttontype=='subscription'?true:false,
            name: "buttontype",
            value: "subscription",
            onChange:displaypayment
          }), 
          React.createElement("label",null, "Subscription Button"))
          ,
          React.createElement("div",  {
            id: "buttonlistid",
            class: "components-placeholder__fieldset"
          }, 
              React.createElement(
            'select',
            { id: "dropdownid",
              value: buttonId, onChange: setButtonIdFromPlaceholder },
            Edit.getButtonOptions(buttontype).map(button => React.createElement(
              'option',
              { key: button.value, value: button.value },
              button.label
            ))
          )

         )
        )
      ), formContent];
    }

    return [controls, React.createElement(ServerSideRender, {
      key: 'button_preview',
      block: 'razorpay/payment-button',
      attributes: this.props.attributes
    }), formContent];
  }

}

/* harmony default export */ __webpack_exports__["default"] = (Edit);

/***/ }),

/***/ "./js/src/blocks/razorpay/icon.js":
/*!************************************!*\
  !*** ./js/src/blocks/form/icon.js ***!
  \************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
const icon = React.createElement(
  'svg',
  { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 508.3 559.5', width: '100%', height: '100%',
    focusable: 'false', 'aria-hidden': 'true',
    className: 'dashicon dashicon-gravityforms' },
  React.createElement(
    'g',
    null,
    React.createElement('path', { className: 'st0',
      d: 'M468,109.8L294.4,9.6c-22.1-12.8-58.4-12.8-80.5,0L40.3,109.8C18.2,122.6,0,154,0,179.5V380\tc0,25.6,18.1,56.9,40.3,69.7l173.6,100.2c22.1,12.8,58.4,12.8,80.5,0L468,449.8c22.2-12.8,40.3-44.2,40.3-69.7V179.6\tC508.3,154,490.2,122.6,468,109.8z M399.3,244.4l-195.1,0c-11,0-19.2,3.2-25.6,10c-14.2,15.1-18.2,44.4-19.3,60.7H348v-26.4h49.9\tv76.3H111.3l-1.8-23c-0.3-3.3-5.9-80.7,32.8-121.9c16.1-17.1,37.1-25.8,62.4-25.8h194.7V244.4z'
    })
  )
);

/* harmony default export */ __webpack_exports__["default"] = (icon);

/***/ }),

/***/ "./js/src/blocks/razorpay/index.js":

/*!*************************************!*\
  !*** ./js/src/blocks/razorpay/index.js ***!
  \*************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./edit */ "./js/src/blocks/razorpay/edit.js");
/* harmony import */ var _icon__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./icon */ "./js/src/blocks/razorpay/icon.js");
/**
/ * WordPress dependencies
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

/**
 * Internal dependencies
 */




wp.blocks.registerBlockType('razorpay/payment-button', {
  title: 'Razorpay: Payment Buttons',
  description: 'Select a button below to add it to your page.',
  icon: 'button',
  category: 'widgets',
  attributes: {
    buttontype:{
      type: 'string'
    },
    buttonId: {
      type: 'string'
    },
    buttonContent: {
      type: 'string'
    },
    title: {
      type: 'boolean',
      default: true
    },
    tabindex: {
      type: 'string'
    },
    buttonPreview: {
      type: 'boolean',
      default: true
    }
  },
  edit: _edit__WEBPACK_IMPORTED_MODULE_0__["default"],
  
  save: function(props) {
    
    return wp.element.createElement( 'p', {
          dangerouslySetInnerHTML: {
              __html: props.attributes.buttonContent
          }
      } );
  }
})


/***/ }),

/***/ "./js/src/index.js":
/*!*************************!*\
  !*** ./js/src/index.js ***!
  \*************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);

/* harmony import */ var _blocks_form_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./blocks/form/index.js */ "./js/src/blocks/razorpay/index.js");


/***/ })

/******/ });
//# sourceMappingURL=blocks.js.map