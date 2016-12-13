/*------------------------------------------------------------------------
 # SM Cart Pro - Version 2.0.0
 # Copyright (c) 2015 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

$j = (typeof $j !== 'undefined') ? $j : jQuery.noConflict();

function ajaxCartProUpdate(options) {
	this.formKey = options.formKey;
	this.previousVal = null;
	this.defaultErrorMessage = 'Error occurred. Try to refresh page.';
	this.selectors = {
		itemRemove: '#sm_cartpro .remove, #shopping-cart-table .btn-remove',
		container: '#sm_cartpro .cartpro-content',
		inputQty: '#sm_cartpro .cart-item-quantity, #shopping-cart-table input.qty',
		qty: '#sm_cartpro .cartpro-count',
		overlay: '#sm_cartpro .cartpro-content',
		error: '#sm_cartpro .cartpro-error ',
		success: '#sm_cartpro .cartpro-success',
		quantityButtonPrefix: '#sm_cartpro #cpqbutton-',
		quantityInputPrefix: '#sm_cartpro #cpqinput-',
		quantityButtonClass: '#sm_cartpro .quantity-button',
		ckquantityInputPrefix: '#shopping-cart-table input.qty',
		ckquantityButtonClass: '#shopping-cart-table .product-cart-actions .btn-update',
		cartTitle: '#sm_cartpro .cartpro-title',
		checkout: '.cart',  /* Enter class name of Checkout Cart */
		toplink: '.links', /* Enter class name of Top Links */
		totalPrice:'#sm_cartpro .total-price .price' /* Enter class name of Total price for header cart */
	};
	if (options.selectors) {
		$j.extend(this.selectors, options.selectors);
	}
}
ajaxCartProUpdate.prototype = {
	init: function() {
		var cart = this;
		$j(this.selectors.itemRemove).unbind('click.minicart').bind('click.minicart', function(e) {
			e.preventDefault();
			cart.removeItem($j(this));
		});
		$j(this.selectors.inputQty).unbind('blur.minicart').unbind('focus.minicart').bind('focus.minicart', function() {
			cart.previousVal = $j(this).val();
			cart.displayQuantityButton($j(this))
		}).bind('blur.minicart', function() {
			cart.revertInvalidValue(this);
		});
		$j(this.selectors.quantityButtonClass).unbind('click.quantity').bind('click.quantity', function() {
			cart.processUpdateQuantity(this);
		});
		$j(this.selectors.ckquantityButtonClass).unbind('click.quantityck').bind('click.quantityck', function(e) {
			e.preventDefault();
			cart.processUpdateQuantityCK(this);
		});
	},
	removeItem: function(el) {
		var cart = this,
			_comfirm = el.data('confirm') ? el.data('confirm') : 'Are you sure you would like to remove this item from the shopping cart?';
		if (confirm(_comfirm)) {
			cart.hideMessage();
			cart.showOverlay();
			var url = el.attr('href');
			url = url.replace('checkout/cart', 'cartpro/checkout_cart'), url = url.replace('cartpro/checkout_cart/delete', 'cartpro/checkout_cart/ajaxDelete');
			$j.ajax({
				type: 'POST',
				dataType: 'json',
				data: {
					form_key: cart.formKey
				},
				url: url
			}).done(function(result) {
				if (result.success) {
					cart.updateCartQty(result.qty);
					if (result.qty <= 0) $j(cart.selectors.cartTitle).addClass('cartpro-empty');
					else $j(cart.selectors.cartTitle).removeClass('cartpro-empty');
					cart.updateContentOnRemove(result, el.closest('li'));
				} else {
					cart.showMessage(result);
				}
			}).error(function() {
				cart.hideOverlay();
				var check_cofirm = Translator.translate(confirm(cart.defaultErrorMessage));
				if (check_cofirm) {
					document.location.reload(true);
				}
			});
		}
	},
	revertInvalidValue: function(el) {
		if (!this.isValidQty($j(el).val()) || $j(el).val() == this.previousVal) {
			$j(el).val(this.previousVal);
			this.hideQuantityButton(el);
		}
	},
	displayQuantityButton: function(el) {
		if ($j(el).siblings('.btn-update')) {
			$j(el).siblings('.btn-update').css('display', 'inline-block');
		}
		var buttonId = this.selectors.quantityButtonPrefix + $j(el).data('item-id');
		$j(buttonId).addClass('visible').attr('disabled', null);
	},
	hideQuantityButton: function(el) {
		var buttonId = this.selectors.quantityButtonPrefix + $j(el).data('item-id');
		$j(buttonId).removeClass('visible').attr('disabled', 'disabled');
	},
	processUpdateQuantity: function(el) {
		var input = $j(this.selectors.quantityInputPrefix + $j(el).data('item-id'));
		if (this.isValidQty(input.val()) && input.val() != this.previousVal) {
			this.updateItem(el);
		} else {
			this.revertInvalidValue(input);
		}
	},
	processUpdateQuantityCK: function(el) {
		var input = $j(el).siblings('.qty');
		if(typeof input !== 'undefined' &&  input.length) {
			var	item_id = input.attr('name');
			item_id = item_id.replace('cart[', '').replace('][qty]', '');
			var data_link = $j(this.selectors.quantityInputPrefix + item_id).data('link');
			input.attr('data-link', data_link);
			input.attr('data-qtyvalue', $j(this.selectors.quantityInputPrefix + item_id).val());
			if (this.isValidQty(input.val()) && input.val() != this.previousVal) {
				this.updateItem(el);
			} else {
				this.revertInvalidValue(input);
			}
		}
		return true;
	},
	updateItem: function(el) {
		var cart = this;
		var input = $j(this.selectors.quantityInputPrefix + $j(el).data('item-id'));
		input = input.length ? input : $j(el).siblings('.qty');
		var quantity = parseInt(input.val(), 10);
		var cur_quantity = parseInt(input.data('qtyvalue'), 10);
		cart.hideMessage();
		cart.showOverlay();
		var url = input.data('link');
		url = url = url.replace('checkout/cart', 'cartpro/checkout_cart');
		$j.ajax({
			type: 'POST',
			dataType: 'json',
			url: url,
			data: {
				qty: quantity,
				form_key: cart.formKey
			}
		}).done(function(result) {
			if (result.success) {
				cart.updateCartQty(result.qty);
				if (quantity !== 0) {
					cart.updateContentOnUpdate(result);
				} else {
					cart.updateContentOnRemove(result, input.closest('li'));
				}
			} else {
				input.val(cur_quantity);
				cart.showMessage(result);
			}
		}).error(function() {
			cart.hideOverlay();
			var check_cofirm = Translator.translate(confirm(cart.defaultErrorMessage));
			if (check_cofirm) {
				document.location.reload(true);
			}
		});
		return false;
	},
	updateContentOnRemove: function(result, el) {
		var cart = this;
		if (el.length) {
			el.hide('slow', function() {
				$j(cart.selectors.container).html(result.content);
				(cart._ckClsExist(cart.selectors.checkout) && result.checkout_content) ? $j(result.checkout_content).replaceAll(cart.selectors.checkout) : '';
				(cart._ckClsExist(cart.selectors.toplink) && result.top_link) ? $j(result.top_link).replaceAll(cart.selectors.toplink) : '';
				cart._updatePriceHeader();
				cart.showMessage(result);
			});
		} else {
			$j(cart.selectors.container).html(result.content);
			(cart._ckClsExist(cart.selectors.checkout) && result.checkout_content) ? $j(cart.selectors.checkout).html($j($j(result.checkout_content)).html()) : '';
			(cart._ckClsExist(cart.selectors.toplink) && result.top_link) ? $j(result.top_link).replaceAll(cart.selectors.toplink) : '';
			cart._updatePriceHeader();
			cart.showMessage(result);
		}
		if (typeof ajaxCartPro !== 'undefined') {
			ajaxCartPro.eventForCompare();
			ajaxCartPro.eventForWishlist();
		}	
	},
	_updatePriceHeader: function(){
		var cart = this;
		var _price = cart._ckClsExist($j('#sm_cartpro .cartpro-subtotal .price')) ? $j('#sm_cartpro .cartpro-subtotal .price').text() : 0;
		(cart._ckClsExist($j(cart.selectors.totalPrice)) && _price != 0) ? ($j(cart.selectors.totalPrice).text(_price)) :$j(cart.selectors.totalPrice).text(currencyCode+'0.00') ;
	},
	_ckClsExist: function (el) {
		if (typeof el !== 'undefined' && el !== null)
			return true;
		return false;
	},
	updateContentOnUpdate: function(result) {
		var cart = this;
		$j(cart.selectors.container).html(result.content);
		(cart._ckClsExist(cart.selectors.checkout) && result.checkout_content) ? $j(cart.selectors.checkout).html($j($j(result.checkout_content)).html()) : '';
		(cart._ckClsExist(cart.selectors.toplink) && result.top_link) ? $j(result.top_link).replaceAll(cart.selectors.toplink) : '';
		cart._updatePriceHeader();
		cart.showMessage(result);
		if (typeof ajaxCartPro !== 'undefined') {
			ajaxCartPro.eventForCompare();
			ajaxCartPro.eventForWishlist();
		}				
	},
	updateCartQty: function(qty) {
		if (typeof qty != 'undefined') {
			$j(this.selectors.qty).text(qty);
		}
	},
	isValidQty: function(val) {
		return (val.length > 0) && (val - 0 == val) && (val - 0 > 0);
	},
	showOverlay: function() {
		if (typeof ajaxCartPro !== 'undefined') ajaxCartPro.showLoading();
	},
	hideOverlay: function() {
		if (typeof ajaxCartPro !== 'undefined') ajaxCartPro.hideLoading();
	},
	showMessage: function(result) {
		var cart = this;
		cart.hideOverlay();
		if ($j('#cartpro_modal .cpmodal-display') && $j('#cartpro_modal .cpmodal-display').length) {
			$j('#cartpro_modal .cpmodal-display').addClass('cartpro-hidden');
			var _qty = parseInt($j(cart.selectors.qty).text());
			if (_qty) {
				$j('#cartpro_modal .cpmodal-viewcart').removeClass('cartpro-hidden');
			}
		}
		if (typeof result.notice != 'undefined') {
			this.showError(result.notice);
		} else if (typeof result.error != 'undefined') {
			this.showError(result.error);
		} else if (typeof result.message != 'undefined') {
			this.showSuccess(result.message);
		}
		cart.init();
	},
	hideMessage: function() {
		$j(this.selectors.error).fadeOut('slow');
		$j(this.selectors.success).fadeOut('slow');
	},
	showError: function(message) {
		$j(this.selectors.error).text(message).fadeIn('slow');
		if (typeof ajaxCartPro !== 'undefined') {
			if ($j('#cartpro_modal .cpmodal-message').length) {
				$j('#cartpro_modal .cpmodal-message').addClass('cp-error');
				$j('#cartpro_modal .cpmodal-message').text(message);
			}
			ajaxCartPro.showModal();
		}
	},
	showSuccess: function(message) {
		$j(this.selectors.success).text(message).fadeIn('slow');
		if (typeof ajaxCartPro !== 'undefined') {
			if ($j('#cartpro_modal .cpmodal-message').length) {
				$j('#cartpro_modal .cpmodal-message').removeClass('cp-error');
				$j('#cartpro_modal .cpmodal-message').html('<strong>' + message + '</strong>');
			}
			ajaxCartPro.showModal();
		}
	}
};
$j(document).ready(function() {
	function _isMobile() {
		try {
			document.createEvent("TouchEvent");
			return true;
		} catch (e) {
			return false;
		}
	}
	if (typeof effect !== 'undefined') {
		if (_isMobile()) effect = 'device';
		switch (effect) {
			case 'hover':
				$j('#sm_cartpro').hover(function(e) {
					'use strict';
					$j(this).toggleClass('cartpro-hover');
					e.preventDefault();
					if ($j('.skip-active').length) $j('.skip-active').removeClass('skip-active');
				});
				break;
			case 'click':
				$j('#sm_cartpro .cartpro-title').click(function(e) {
					'use strict';
					e.preventDefault();
					$j('#sm_cartpro').toggleClass('cartpro-hover');
					if ($j('.skip-active').length) $j('.skip-active').removeClass('skip-active');
				});
				break;
			case 'device':
				$j('#sm_cartpro .cartpro-title').on("touchstart", function(e) {
					'use strict';
					$j('#sm_cartpro').toggleClass('cartpro-hover');
					e.preventDefault();
					if ($j('.skip-active').length) $j('.skip-active').removeClass('skip-active');
				});
				break;
			default:
		}
		if ($j('.skip-link').length) {
			$j('.skip-link').click(function(e) {
				e.preventDefault();
				$j('.cartpro-hover').removeClass('cartpro-hover');
			});
		}
	}
})