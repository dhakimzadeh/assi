/*------------------------------------------------------------------------
 # SM Cart Pro - Version 2.0.0
 # Copyright (c) 2015 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

var ajaxCartPro = {
	initialize: function() {
		this.cptitle = $$('#sm_cartpro .cartpro-title')[0];
		this.cpcount = $$('#sm_cartpro .cartpro-count')[0];
		this.cpcontent = $$('#sm_cartpro .cartpro-content')[0];
		this.cploading = $$('#cartpro_process.cartpro-process')[0];
		this.cpmodal = $$('#cartpro_modal.cartpro-modal')[0];
		this.cptime = $$('#cartpro_modal .cpmodal-time')[0];
		this.cpclose = $$('#cartpro_modal .cp-close');
		this.cpmodal_mess = $$('#cartpro_modal .cpmodal-message')[0];
		this.cpmodal_iframe = $$('#cartpro_modal .cpmodal-iframe')[0];
		this.cpmodal_compare = $$('#cartpro_modal .cartpro-compare')[0];
		this.cpmodal_wishlist = $$('#cartpro_modal .cartpro-wishlist')[0];
		this.cpmodal_dis = $$('#cartpro_modal .cpmodal-display');
		this.cpmodal_viewcart = $$('#cartpro_modal .cpmodal-viewcart')[0];
		this.top_link_cls = $$('.links')[0]; /* Enter class name of Top Link */
		this.block_compare = $$('.block-compare')[0]; /* Enter class name of Block Compare */
		this.block_wishlist = $$('.block-wishlist')[0]; /* Enter class name of Block Wishlist */
		this.my_wishlist = $$('.my-account .my-wishlist')[0]; /* Enter class name of My Wishlist */
		this.header_compare = $$('.links-compare .compare-i-header')[0]; /* Enter class name of Header Compare */
		this.total_price = $$('#sm_cartpro .total-price .price')[0]; /* Enter class name of Total price for header cart */
		this.page_checkout =  $$('.cart')[0]; 
		this.cptimeout1 = this.captimeout2 = this.cptimeout3 = 0;
		this.bindEvents();
	},
	bindEvents: function() {
		var _this = this;
		if (typeof enable_ajax_cart !== 'undefined' && enable_ajax_cart) _this.addSubmitEvent();
		if (typeof enable_ajax_compare !== 'undefined' && enable_ajax_compare) {
			_this.eventForCompare();
			_this.popWinCompare();
		}	
		if (typeof enable_ajax_wishlist !== 'undefined' && enable_ajax_wishlist) _this.eventForWishlist();
		(_this.ckClsExist(_this.cpclose)) ? _this.eventForClose(_this.cpclose) : '';
	},
	eventForClose: function(el) {
		var _this = this;
		el.each(function(e) {
			e.observe('click', function(event) {
				_this.clickHideModal();
				var _cls_href = $(e).readAttribute('href');
				if (_cls_href && _cls_href !== '#') {
					window.location.href = _cls_href;
					return false;
				}
				Event.stop(event);
			});
		});
	},
	eventForCompare: function() {
		$$('a[href*="/catalog/product_compare/"]').each(function(e) {
			$(e).stopObserving();
			var _attr = $(e).getAttribute('onclick') || $(e).getAttribute('data-confirm');
			if (_attr !== null) {
				_attr = _attr.replace("return", "").replace("confirm('", "").replace("');", "");
				$(e).removeAttribute('onclick');
				$(e).setAttribute('data-confirm', _attr);
			}
			$(e).observe('click', function(event) {
				event.preventDefault();
				if (_attr !== null) {
					var _comfirm = confirm(_attr);
					if (_comfirm) setLocation($(e).readAttribute('href'));
				} else setLocation($(e).readAttribute('href'));
				Event.stop(event);
			});
		});
	},
	eventForWishlist: function() {
		var _this = this;
		$$('a[href*="/wishlist/index/"]').each(function(e) {
			var _w_href = $(e).readAttribute('href');
			if (_w_href.search('wishlist/index/configure') == -1 && _w_href.search('wishlist/index/updateItemOptions') == -1 && _w_href.search('wishlist/index/fromcart') == -1) {
				var _attr = $(e).getAttribute('onclick');
				if (_attr !== null && _attr.search('productAddToCartForm') != -1) {
					if (!isLoggedIn) {
						$(e).stopObserving();
						$(e).removeAttribute('onclick');
						$(e).observe('click', function(event) {
							event.preventDefault();
							var check_cofirm = Translator.translate(confirm("You must login first!"));
							if (check_cofirm) {
								if(window.top != window.self) {
									if (typeof window.top.ajaxCartPro !== 'undefined'){
										window.top.ajaxCartPro.resetModal();
										window.top.ajaxCartPro.hideFancyBox();
									}
									window.top.location.href = cartpro_baseurl + 'customer/account/login';
								}else{
									window.location.href = cartpro_baseurl + 'customer/account/login';
								}
								return false;
							}
						});
					}					
				} else {
					$(e).stopObserving();
					if (_attr !== null) {
						$(e).removeAttribute('onclick');
					} else _attr = null;
					$(e).observe('click', function(event) {
						event.preventDefault();
						if (!isLoggedIn) {
							if(typeof window.opener != 'undefined' && window.opener !== null){
								var check_cofirm = Translator.translate(window.opener.confirm("You must login first!"));
								if (check_cofirm) window.opener.location.href = cartpro_baseurl + 'customer/account/login';
							}else{
								var check_cofirm = Translator.translate(confirm("You must login first!"));
								if (check_cofirm) window.location.href = cartpro_baseurl + 'customer/account/login';
							}
							return false;
						} else {
							if (_attr !== null) {
								var _comfirm = Translator.translate(confirm('Are you sure you would like to remove this item from the wishlist?'));
								if (_comfirm) setLocation($(e).readAttribute('href'));
							} else setLocation($(e).readAttribute('href'));
						}
						Event.stop(event);
					});
				}
			}
		});
	},
	addSubmitEvent: function() {
		if (window.top == window.self && typeof productAddToCartForm != 'undefined') {
			var _this = this;
			productAddToCartForm.submit = function(button, url) {
				if (this.validator && this.validator.validate()) {
					var form = this.form;
					var oldUrl = form.action;
					if (url) {
						form.action = url;
					}
					url = form.action;
					if (url.search('wishlist/index/cart') != -1) 
						_this.ajaxCartSubmit(this, 'wishlist_form');
					else
						_this.ajaxCartSubmit(this, 'form');
				}
				return false;
			}.bind(productAddToCartForm);
			productAddToCartForm.submitLight = function(button, url) {
				if (!isLoggedIn) {
					var check_cofirm = Translator.translate(confirm("You must login first!"));
					if (check_cofirm) window.location.href = cartpro_baseurl + 'customer/account/login';
					return false;
				} else {
					if(this.validator) {
						var form = this.form;
						var oldUrl = form.action;
						if (url) form.setAttribute('data-action', url);
						try {
							if (url.search('wishlist/index/updateItemOptions') != -1 || url.search('wishlist/index/add') != -1)
								_this.ajaxCartSubmit(this, 'wishlist_update');
							else 
								_this.ajaxCartSubmit(this, 'form');
						} catch (e) {
						}	
						return false;
					}
					
				}
				return false;
			}.bind(productAddToCartForm);
		}
	},
	ckClsExist : function (el) {
		if (typeof el !== 'undefined' && el !== null)
			return true;
		return false;
	},
	processData: function(reponse) {
		var _this = this;
		try {
			_this.hideLoading();
			var res = reponse.responseText.evalJSON();
			if (res) {
				if (res.success) {
					if (_this.ckClsExist(_this.cpcount) && res.qty) {
						_this.cpcount.update(res.qty);
						if (res.qty > 0) {
							(_this.ckClsExist(_this.cpmodal_viewcart)) ? _this.cpmodal_viewcart.removeClassName('cartpro-hidden') :'';
							if (_this.ckClsExist(_this.cptitle) && _this.cptitle.hasClassName('cartpro-empty')) _this.cptitle.removeClassName('cartpro-empty');
						} else {
							(_this.ckClsExist(_this.cpmodal_viewcart)) ? _this.cpmodal_viewcart.addClassName('cartpro-hidden'):'';
							if (_this.ckClsExist(_this.cptitle) && !_this.cptitle.hasClassName('cartpro-empty')) _this.cptitle.addClassName('cartpro-empty');
						}
						if (_this.ckClsExist(_this.cpcontent) && res.content && typeof currencyCode !== 'undefined') {
							_this.cpcontent.update(res.content);
							var _price = _this.ckClsExist($$('#sm_cartpro .cartpro-subtotal .price')[0]) ? $$('#sm_cartpro .cartpro-subtotal .price')[0].innerHTML : 0;
							(_this.ckClsExist(_this.total_price) && _price != 0) ? (_this.total_price.innerHTML = _price ) : _this.total_price.innerHTML = currencyCode+'0.00';
						} 
						
					}
					
					if(_this.ckClsExist(_this.page_checkout) && res.checkout_content){
					
						(_this.ckClsExist(_this.page_checkout) && res.checkout_content) ? _this.page_checkout.update(res.checkout_content): '';
						var _checkout_content = _this.ckClsExist($$('.cart')[1]) ? $$('.cart')[1].innerHTML : 0;
						(_this.ckClsExist(_this.page_checkout) && _checkout_content) ? _this.page_checkout.innerHTML = _checkout_content: '';
						_this.eventForCompare();
						_this.eventForWishlist();
					}
					
					if (_this.ckClsExist(_this.cpmodal_compare) && res.is_compare) {
						if (res.nb_items > 0) 
							_this.cpmodal_compare.removeClassName('cartpro-hidden');
						else 
							_this.cpmodal_compare.addClassName('cartpro-hidden');
						
						if (res.nb_items > 1) {
							var labelcompare = "items";
						} else {
							var labelcompare = "item";
						}
						
						if (res.block_compare && _this.ckClsExist(_this.block_compare)) _this.block_compare.update(res.block_compare);
						if (_this.header_compare && _this.ckClsExist(_this.header_compare)) _this.header_compare.update('Compare ('+res.nb_items+' '+labelcompare+')');
						
						_this.eventForCompare();
					}
					if (_this.ckClsExist(_this.cpmodal_wishlist) && res.is_wishlist) {
						if (res.nb_items > 0) {
							_this.cpmodal_wishlist.removeClassName('cartpro-hidden');
						} else {
							_this.cpmodal_wishlist.addClassName('cartpro-hidden');
						}
						if (res.block_wishlist && _this.ckClsExist(_this.block_wishlist)) _this.block_wishlist.update(res.block_wishlist);
						if (res.customer_wishlist && _this.ckClsExist(_this.my_wishlist)) _this.my_wishlist.update(res.customer_wishlist);
						_this.eventForWishlist();
					}
					if (res.top_link && _this.ckClsExist(_this.top_link_cls)) _this.top_link_cls.update(res.top_link);
					if (_this.ckClsExist(_this.cpmodal_mess) && res.message) _this.cpmodal_mess.update(res.message);
					_this.showModal();
				} else {
					if (res.url && res.error) {
						_this.iframeProductOptions(res.url, res.error);
					} else {
						var _qty = (_this.ckClsExist(_this.cpcount)) ? parseInt(_this.cpcount.innerHTML) : 0;
						if (_this.ckClsExist(_this.cpmodal_viewcart)){
							(_qty && _qty > 0) ?  _this.cpmodal_viewcart.removeClassName('cartpro-hidden') : _this.cpmodal_viewcart.addClassName('cartpro-hidden');
						}
						if (_this.ckClsExist(_this.cpmodal_compare) && res.is_compare) _this.cpmodal_compare.removeClassName('cartpro-hidden');
						if (_this.ckClsExist(_this.cpmodal_wishlist) && res.is_wishlist) _this.cpmodal_wishlist.removeClassName('cartpro-hidden');
						_this.ckClsExist(_this.cpmodal) ? _this.cpmodal.removeClassName('cartpro-options') : '';
						_this.ckClsExist(_this.cpmodal_mess) ? _this.cpmodal_mess.addClassName('cp-error') : '';
						_this.ckClsExist(_this.cpmodal_mess) ? _this.cpmodal_mess.update(res.error) : '' ;
						_this.showModal();
					}
				}
			}
		} catch (e) {
			console.log("CartPro: " + e.message);
		}
	},
	ajaxCartSubmit: function(url, type) {
		var _this = this;
		try {
			switch (type) {
				case 'wishlist_update':
				case 'wishlist_form':
				case 'form':
					var _form = '';
					if (typeof url === 'object' && typeof url.form !== 'undefined') _form = url.form;
					else _form = url;
					if (type == 'wishlist_update' && _form.getAttribute('data-action') !== null) url = _form.getAttribute('data-action');
					else url = _form.action;
					if (type == 'form') url = url.replace('checkout/cart', 'cartpro/checkout_cart');
					else url = url.replace('wishlist/index', 'cartpro/wishlist');
					var data = _form.serialize();
					new Ajax.Request(url, {
						method: 'post',
						postBody: data,
						onCreate: function() {
							_this.showLoading();
						},
						onSuccess: function(response) {
							_this.processData(response);
						},
						onFailure: function(e) {
							_this.hideLoading();
							var check_cofirm = Translator.translate(confirm("Error occurred. Try to refresh page."));
							if (check_cofirm) {
								document.location.reload(true);
							}
							console.log('CartPro: ' + e.message);
						}
					});
					break;
				case 'compare':
				case 'wishlist':
				case 'url':
					if (type == 'url') url = url.replace('checkout/cart', 'cartpro/checkout_cart');
					else if (type == 'wishlist') url = url.replace('wishlist/index', 'cartpro/wishlist');
					else if (type == 'compare') url = url.replace('catalog/product_compare', 'cartpro/catalog_product_compare');
					new Ajax.Request(url, {
						method: 'post',
						onCreate: function() {
							_this.showLoading();
						},
						onComplete: function(response) {
							_this.processData(response);
						},
						onFailure: function(response) {
							_this.hideLoading();
							var check_cofirm = Translator.translate(confirm("Error occurred. Try to refresh page."));
							if (check_cofirm) {
								document.location.reload(true);
							}
							console.log('CartPro: ' + e.message);
						}
					});
					break;
				default:
					console.log(url);
			}
		} catch (e) {
			console.log(e);
			if (typeof url == 'string') window.location.href = url;
			else document.location.reload(true);
		}
	},
	hideFancyBox: function (){
		try {
			var $j = (typeof $j !== 'undefined') ? $j : jQuery.noConflict();
			if (typeof $j.fancybox === 'function' ){
				$j.fancybox.close();
			}
		}catch(e) {
			console.log('Cartpro: '+e.message);
		}
	},
	showLoading: function() {
		var _this = this;
		if (_this.ckClsExist(_this.cploading) && !_this.cploading.hasClassName('cartpro-show')) _this.cploading.addClassName('cartpro-show');
		_this.hideFancyBox();
		_this.resetModal();
	},
	hideLoading: function() {
		var _this = this;
		if (_this.ckClsExist(_this.cploading) && _this.cploading.hasClassName('cartpro-show')) _this.cploading.removeClassName('cartpro-show');
		if (_this.ckClsExist(_this.cpmodal_dis)) {
			_this.cpmodal_dis.each(function(e) {
				$(e).addClassName('cartpro-hidden');
			});
		}
	},
	showModal: function() {
		var _this = this,
			time = confirm_countdown_number;
		_this.eventForClose($$('#cartpro_modal .cp-close'));
		if (!_this.cpmodal.hasClassName('cartpro-show')) _this.cpmodal.addClassName('cartpro-show');
		_this.hideModal(time);
	},
	resetModal: function() {
		var _this = this;
		_this.hideFancyBox();
		if (_this.ckClsExist(_this.cpmodal) && !_this.cpmodal.hasClassName('cartpro-show')) {
			if (_this.cpmodal.hasClassName('cartpro-options')) _this.cpmodal.removeClassName('cartpro-options');
			if (_this.ckClsExist(_this.cpmodal_mess) && _this.cpmodal_mess.hasClassName('cp-error')) _this.cpmodal_mess.removeClassName('cp-error')
			_this.ckClsExist(_this.cpmodal_mess) ? _this.cpmodal_mess.update('') : '';
			if (_this.ckClsExist(_this.cpmodal_iframe)) _this.cpmodal_iframe.update('');
			(_this.ckClsExist(_this.cpmodal)) ? _this.cpmodal.removeClassName('cartpro-reset') : '';
		} else {
			(_this.ckClsExist(_this.cpmodal)) ? _this.cpmodal.removeClassName('cartpro-show') : '';
			if (_this.cptimeout1 == 'undefined') _this.cptimeout1 = 0;
			clearTimeout(_this.cptimeout1);
			_this.cptimeout1 = setTimeout(function() {
				_this.resetModal();
			}, 1000);
		}
	},
	hideModal: function(time) {
		var _this = this;
		try {
			if (_this.ckClsExist(_this.cpmodal) && !_this.cpmodal.hasClassName('cartpro-options')) {
				if (time > 0 && _this.cpmodal.hasClassName('cartpro-show') && _this.ckClsExist(_this.cptime)) {
					_time = time - 1;
					_this.cptime.innerHTML = _time + 1 + "  s";
					if (_this.cptimeout2 == 'undefined') _this.cptimeout2 = 0;
					clearTimeout(_this.cptimeout1);
					_this.cptimeout2 = setTimeout(function() {
						_this.hideModal(_time);
					}, 1000);
				} else {
					_this.resetModal();
				}
			}
		} catch (e) {
			console.log(e);
		}
	},
	clickHideModal: function() {
		var _this = this;
		_this.resetModal();
	},
	iframeProductOptions: function(urlpath, mes) {
		var _this = this;
		var pathbase = 'cartprooptoins/index/product/path/';
		var path = (urlpath.search(cartpro_baseurl) != -1) ? urlpath.replace(cartpro_baseurl, "") : urlpath;
		pathbase = cartpro_baseurl + pathbase + path;
		_this.showLoading();
		if (mes && _this.ckClsExist(_this.cpmodal_mess) && !_this.cpmodal_mess.hasClassName('cp-error')) {
			_this.cpmodal_mess.addClassName('cp-error')
			_this.cpmodal_mess.update(mes);
		}
		(_this.ckClsExist(_this.cpmodal)) ? _this.cpmodal.addClassName('cartpro-options') : '';
		var iframe_options = new Element('iframe', {
			id: 'cartpro_frame',
			'class': 'cartpro-frame',
			'width': '100%',
			'height': '300px',
			'scrolling': 'yes',
			'seamless': 'seamless',
			'border': 'none'
		});
		iframe_options.observe('load', function(e) {
			_this.showModal();
			_this.hideLoading();
			_this.setHeightIframe(this);
		});
		iframe_options.src = pathbase;
		(_this.ckClsExist(_this.cpmodal_iframe)) ? _this.cpmodal_iframe.insert(iframe_options) : '';
	},
	setHeightIframe: function(ifr) {
		var _this = this;
		if (typeof ifr === 'undefined' || ifr === null) return;
		var ifr_height = ifr.getAttribute('height'),
			_content = ifr.contentWindow;
		if (typeof _content === 'undefined' || _content === null || _content.document.querySelector('.cartpro-product') === null) return;
		var _content_height = _content.document.querySelector('.cartpro-product').scrollHeight;
		var _content_height1 = (window.innerHeight - _content_height <= 100) ? window.innerHeight - 100 : _content_height;
		if (typeof _content_height !== 'undefined' && ifr_height !== _content_height) {
			if (_content_height > _content_height1) ifr.setAttribute('scrolling', 'yes');
			else ifr.setAttribute('scrolling', 'no');
			ifr.height = _content_height1;
			if (_this.cptimeout3 == 'undefined') _this.cptimeout3 = 0;
			clearTimeout(_this.cptimeout3);
			_this.cptimeout3 = setTimeout(function() {
				_this.setHeightIframe(ifr);
			}, 500);
		}
	},
	popWinCompare: function () {
		window.removeItem = function(url) {
			if (typeof window.opener !== 'undefined' && window.opener !== null) {
				window.opener.ajaxCartPro.ajaxCartSubmit(url, 'compare');
				window.location.reload();
			}
		}
	}
};
var setLocation = (function() {
	return function(url) {
		if (typeof enable_ajax_cart !== 'undefined' &&  enable_ajax_cart && url.search('checkout/cart/add') != -1)
			(typeof url == 'string') ? ajaxCartPro.ajaxCartSubmit(url, 'url') : ajaxCartPro.ajaxCartSubmit(url, 'form');
		else if (typeof enable_ajax_compare !== 'undefined' &&  enable_ajax_compare && url.search('catalog/product_compare') != -1)
			(window.top !== window.self) ? window.top.ajaxCartPro.ajaxCartSubmit(url, 'compare') : ajaxCartPro.ajaxCartSubmit(url, 'compare');
		else if (typeof enable_ajax_wishlist !== 'undefined' &&  enable_ajax_wishlist && url.search('wishlist/index') != -1 && url.search('wishlist/index/fromcart') === -1)
			(window.top !== window.self) ? window.top.ajaxCartPro.ajaxCartSubmit(url, 'wishlist') : (typeof window.opener !== 'undefined' && window.opener !== null ? window.opener.ajaxCartPro.ajaxCartSubmit(url, 'wishlist') : ajaxCartPro.ajaxCartSubmit(url, 'wishlist'));
		else if (typeof enable_ajax_cart !== 'undefined' &&  enable_ajax_cart && url.search('checkout/cart/updateItemOptions') != -1) 
			ajaxCartPro.ajaxCartSubmit(url, 'form');
		else { 
			if (typeof enable_ajax_cart !== 'undefined' &&  enable_ajax_cart && typeof cartpro_baseurl !== 'undefined'  && url !== cartpro_baseurl && url.search('blog') == -1 && url.search('onepage') == -1 && url.search('sales/order/history') == -1 && url.search('limit=') == -1)
			{
				ajaxCartPro.iframeProductOptions(url, '');
			}
			else
				window.location.href = url;
		}
	};
})();
var setPLocation = (function() {
	return function(url, setFocus) {
		if (setFocus) {
			window.opener.focus();
		}
		window.opener.setLocation(url);
	}
})();
document.observe("dom:loaded", function() {
	ajaxCartPro.initialize();
});