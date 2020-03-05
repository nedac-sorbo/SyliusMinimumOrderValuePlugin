'use strict';

class NedacSyliusMinimumOrderValuePlugin {
  constructor() {
    this.syliusChannelBaseCurrencySelect = null;
    this.toggle = null;
  }
  init() {
    this.syliusChannelBaseCurrencySelect = document.getElementById('sylius_channel_baseCurrency');
    const currencyCode = this.syliusChannelBaseCurrencySelect.options[this.syliusChannelBaseCurrencySelect.selectedIndex].value;

    if (currencyCode !== undefined) {
      const form = document.getElementsByName('sylius_channel')[0];

      let data = {};
      data[this.syliusChannelBaseCurrencySelect.name] = currencyCode;
      this.minimumOrderValueInput = document.getElementById('sylius_channel_minimumOrderValue');
      if (null !== this.minimumOrderValueInput) {
        const value = this.minimumOrderValueInput.value;
        if ('' !== value) {
          data[this.minimumOrderValueInput.name] = value;
        }
      }

      let method = form.method;
      const hiddenMethodElements = document.getElementsByName('_method');
      if (hiddenMethodElements.length > 0) {
        const hiddenMethodValue = hiddenMethodElements[0].value;
        if ('' !== hiddenMethodValue) {
          method = hiddenMethodValue;
        }
      }

      $.ajax({
        url : form.action,
        type: method,
        data : data,
        success: (html) => {
          const segment = $(html).find('#nedac-sylius-minimum-order-value-plugin-admin-segment');
          if (segment !== null) {
            const minimumOrderValueSegment = $('#nedac-sylius-minimum-order-value-plugin-admin-segment');
            if (minimumOrderValueSegment.length === 0) {
              // Element has not been inserted before so it needs to be inserted
              const beforeElement = $('#nedac-sylius-minimum-order-value-plugin-admin-before');
              if (beforeElement !== null) {
                segment.insertAfter(beforeElement);
                this.initToggle();
              } else {
                console.error('Element with id: "nedac-sylius-minimum-order-value-plugin-admin-before" not found!');
              }
            } else {
              minimumOrderValueSegment.replaceWith(segment);
              this.initToggle();
            }
          }
        }
      });
    } else {
      console.error('Could not find currencyCode!');
    }
    this.addEventListeners();
  }
  addEventListeners() {
    this.syliusChannelBaseCurrencySelect.addEventListener('change', (event) => {
      this.init();
    });
  }
  initToggle() {
    this.toggle = document.getElementById('nedac-sylius-minimum-order-value-plugin-admin-toggle');
    if (this.toggle === null) {
      console.error('Could not find element with id: "nedac-sylius-minimum-order-value-plugin-admin-toggle"!');
      return;
    }

    this.minimumOrderValueInput = document.getElementById('sylius_channel_minimumOrderValue');
    if (null === this.minimumOrderValueInput) {
      console.error('Could not find element with id: "sylius_channel_minimumOrderValue"!');
      return;
    }

    this.toggle.addEventListener('change', (event) => {
      if (event.target.checked) {
        this.minimumOrderValueInput.disabled = false;
      } else {
        this.minimumOrderValueInput.disabled = true;
        this.minimumOrderValueInput.value = '';
      }
    });

    if ('' === this.minimumOrderValueInput.value) {
      this.minimumOrderValueInput.disabled = true;
      this.toggle.checked = false;
    } else {
      this.minimumOrderValueInput.removeAttribute('disabled');
      this.toggle.checked = true;
    }
  }
}

const nedacSyliusMinimumOrderValuePlugin = new NedacSyliusMinimumOrderValuePlugin();
nedacSyliusMinimumOrderValuePlugin.init();
