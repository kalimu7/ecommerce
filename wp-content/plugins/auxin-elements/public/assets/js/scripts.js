;(function($, window, document, undefined){
    var AuxinRemoveCartContent = function() {
        // Remove cart content
        $(document).on( 'click', '.aux-remove-cart-content', function(e) {
            e.preventDefault();
            
            var $thisbutton = $(this);
            var product_id   = $(this).data("product_id");
            var cart_item_key= $(this).data("cart_item_key");
            var verify_nonce = $(this).data("verify_nonce");
            var $cartBoxEl   = $(this).closest('.aux-cart-wrapper').addClass('aux-cart-remove-in-progress');

            if ( typeof auxin_cart_options === 'undefined' ) {
                auxin_cart_options = '';
            }

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: auxin.ajax_url,
                data: {
                    action: "auxels_remove_from_cart",
                    product_id: product_id,
                    cart_item_key: cart_item_key,
                    verify_nonce: verify_nonce,
                    args        : auxin_cart_options,
                },
                success: function( response ){
                    // Remove old notification
                    $(".woocommerce-message, .woocommerce-error").remove();
                    // Start Notifications
                    if( response.success ) {
                        $('.aux-hidden-blocks').append( response.data.notif );

                        if( parseInt(response.data.total) === 0 ) {
                            $('.aux-card-dropdown').html(response.data.empty);
                            $('.aux-cart-contents').find('span').remove();
                        } else {
                            $('.aux-card-item').filter(function(){
                                return $(this).data('cart_item_key') == cart_item_key;
                            }).remove();
                            $('.aux-cart-contents').find('span').text(response.data.count);
                        }
                        $(".aux-cart-subtotal").each(function() {
							$(this).find('.woocommerce-Price-amount').html(response.data.total);
                        });
                        $cartBoxEl.removeClass('aux-cart-remove-in-progress');

                        $( document.body ).trigger( 'removed_from_cart', [ response.data.fragments, response.data.cart_hash, $thisbutton, response.data.items ] );
                    } else {
                        $('.aux-hidden-blocks').append( response.data );
                    }
                    // check to be sure offcanvas cart style is always visible in mobile
                    $('#offcart .aux-cart-wrapper .aux-card-dropdown').removeClass('aux-phone-off');
                }
            });

        });
    };

    var AuxinAjaxAddToCart = function() {
        // Add Content to Cart
        $(document).on( 'click', '.aux-ajax-add-to-cart', function(e) {
            var $thisbutton = $(this);
            var productType  = $(this).data("product-type");

            if ( productType !== 'simple' ) {
                return;
            }

            $thisbutton.removeClass( 'added' );
			$thisbutton.addClass( 'loading' );

            if ( typeof auxin_cart_options === 'undefined' ) {
                auxin_cart_options = '';
            }

            e.preventDefault();

            var product_id   = $(this).data("product_id");
            var quantity     = $(this).data("quantity");
            var verify_nonce = $(this).data("verify_nonce");
            var $cartBoxEl   = $('.aux-cart-wrapper');
            var hasAnimation = $cartBoxEl.hasClass('aux-basket-animation') ? true : false;

            $cartBoxEl.trigger('AuxCartInProgress');

            if ( $(this).parents('.aux-shop-quicklook-modal') ) {
                quantity = $(this).parents('.aux-shop-quicklook-modal').find('.quantity input').val();
            }

            var data = {};

            $.each( $thisbutton.data(), function( key, value ) {
				data[ key ] = value;
			});

            // Fetch data attributes in $thisbutton. Give preference to data-attributes because they can be directly modified by javascript
			// while `.data` are jquery specific memory stores.
			$.each( $thisbutton[0].dataset, function( key, value ) {
				data[ key ] = value;
			});
            
            // Trigger event.
			$( document.body ).trigger( 'adding_to_cart', [ $thisbutton, data ] );

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: auxin.ajax_url,
                data: {
                    action      : "auxels_add_to_cart",
                    args        : auxin_cart_options,
                    product_id  : product_id,
                    quantity    : quantity,
                    verify_nonce: verify_nonce
                },
                success: function( response ){
                    // Remove old notification
                    $(".woocommerce-message, .woocommerce-error").remove();
                    // Start Notifications
                    if( response.success ) {
                        $('.aux-hidden-blocks').append( response.data.notif );

                        setTimeout( function(){
                            if ( hasAnimation ) {
                                // $cartBoxEl.on('AuxCartProgressAnimationDone', function(e) {
                                    $cartBoxEl.find('.aux-card-dropdown').html( response.data.items );
                                    $cartBoxEl.find('.aux-shopping-basket').html( response.data.total );
                                    $cartBoxEl.trigger('AuxCartUpdated');
                                    $( document.body ).trigger( 'added_to_cart', [ response.data.fragments, response.data.cart_hash, $thisbutton ] );
                                // });
                            } else {
                                $cartBoxEl.find('.aux-card-dropdown').html( response.data.items );
                                $cartBoxEl.find('.aux-shopping-basket').html( response.data.total );
                                $cartBoxEl.trigger('AuxCartUpdated');
                                $( document.body ).trigger( 'added_to_cart', [ response.data.fragments, response.data.cart_hash, $thisbutton, response.data.items ] );
                            }
                            // check to be sure offcanvas cart style is always visible in mobile
                            $('#offcart .aux-cart-wrapper .aux-card-dropdown').removeClass('aux-phone-off');
                        }, 150);
                    } else {
                        $('.aux-hidden-blocks').append( response.data );
                    }

                }

            });

        });

        $(document.body).on( 'wc_fragments_refreshed', function(){

            var $cartBoxEl   = $('.aux-cart-wrapper');
            var hasAnimation = $cartBoxEl.hasClass('aux-basket-animation') ? true : false;

            if ( typeof auxin_cart_options === 'undefined' ) {
                auxin_cart_options = '';
            }

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: auxin.ajax_url,
                data: {
                    action      : "auxels_get_refreshed_fragments",
                    args        : auxin_cart_options,
                },
                success: function( response ){
                    // Remove old notification
                    $(".woocommerce-message, .woocommerce-error").remove();
                    // Start Notifications
                    if( response.success ) {

                        setTimeout( function(){
                            if ( hasAnimation ) {
                                $cartBoxEl.find('.aux-card-dropdown').html( response.data.items );
                                $cartBoxEl.find('.aux-shopping-basket').html( response.data.total );
                                $cartBoxEl.trigger('AuxCartUpdated');
                            } else {
                                $cartBoxEl.find('.aux-card-dropdown').html( response.data.items );
                                $cartBoxEl.find('.aux-shopping-basket').html( response.data.total );
                                $cartBoxEl.trigger('AuxCartUpdated');
                            }
                        }, 150);
                    } else {
                        $('.aux-hidden-blocks').append( response.data );
                    }

                }

            });

        });

    };

    var AjaxRecentProductsPagination = function () {
        $('a.page-numbers').on('click', function (e) { 
            if ( !$(this).parents('.aux-widget-recent-products').length) {
                return;
            }
            e.preventDefault();
            var $widgetWrapper = $(this).parents('.elementor-widget-container'),
                widgetData = $widgetWrapper.find('.aux-recent-products-wrapper').data('widget-data'),
                paged = $(this).data('paged');

            $widgetWrapper.find('.aux-widget-recent-products').addClass('loading');
            $widgetWrapper.find('.aux-isotope-animated .aux-items-loading').removeClass('aux-loading-hide');
            $.ajax({
                url: auxin.ajax_url,
                type: 'GET',
                data: {
                    action: 'aux_the_recent_products',
                    data: widgetData,
                    paged: paged,
                    auxinNonce: auxin.nonce
                }
            }).done(function (response) {
                if (response) {
                    $widgetWrapper.empty();
                    $widgetWrapper.append(response);
                    $(document).trigger('auxin_the_recent_products_widget_updated');
                }
            });
        });
    }

    var AuxinSelectEelement = function () {

        $('.aux-select-element').each(function () {
            var $element = $(this);
            var action = $element.data('action');
            if (action == 'click') {
                $element.find('.current').on('click', function () {
                    $(this).siblings('.list').toggleClass('show');
                })

            } else {
                $element.hover(function () {
                        $element.find('.list').addClass('show');
                    }, function () {
                        $element.find('.list').removeClass('show');
                    }
                );
            }

            $element.find('.option').on('click', function () {
                var markup = $(this).html();
                $element.find('.current .selected').html(markup);
                $element.find('.list').toggleClass('show');
            });

            $(document).on('click', function (e) {
                if (!$(e.target).parents('.aux-select-element').length) {
                    $element.find('.list').removeClass('show');
                }
            });
        })
    }

    $(document).ready(function(){
        AuxinRemoveCartContent();
        AuxinAjaxAddToCart();
        AjaxRecentProductsPagination();
        $(document).on('auxin_the_recent_products_widget_updated', function () {
            AjaxRecentProductsPagination();
        });

        AuxinSelectEelement();
    });

     $.fn.AuxinCartAnimationHandler = function() {
        $headerCartWrapper = $(this).find('.aux-cart-wrapper');
        $headerCartWrapper.trigger('AuxCartProgressAnimationDone');

        if ( ! $headerCartWrapper.hasClass('aux-basket-animation') ) {
            return
        }

        $headerCartWrapper.on('AuxCartInProgress', function(e) {
            $headerCartWrapper.addClass('aux-cart-in-progress');
        });

        $headerCartWrapper.on('animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd', function(e) {
            if ( e.originalEvent.animationName === 'FillBasket') {
                $headerCartWrapper.removeClass('aux-cart-in-progress');
                $headerCartWrapper.trigger('AuxCartProgressAnimationDone');
            }
        });

        $headerCartWrapper.on('AuxCartUpdated', function(e) {
            $headerCartWrapper.addClass('aux-cart-updated-animation');
        });
    }

    $('body').AuxinCartAnimationHandler();

    $(document.body).on( 'wc_cart_emptied', function(){
        $('.aux-shopping-basket .aux-cart-contents span').html('0');
    });

})(jQuery,window,document);