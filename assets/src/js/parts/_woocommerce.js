import noUiSlider from 'nouislider';

jQuery(document).ready($ => {
  const priceSlider = document.getElementById('price_filter_slider');
  const priceSliderPips = document.getElementById('price_filter_pips');

  if (priceSlider) {
    const range = {
      'min': [parseInt(main.price_filter.min_price)],
      // '10%': [ parseInt( ( main.price_filter.max_price * 10) / 100),  parseInt( ( main.price_filter.max_price * 10) / 100) ],
      // '20%': [ parseInt( ( main.price_filter.max_price * 20) / 100),  parseInt( ( main.price_filter.max_price * 20) / 100) ],
      // '30%': [ parseInt( ( main.price_filter.max_price * 30) / 100),  parseInt( ( main.price_filter.max_price * 30) / 100) ],
      // '40%': [ parseInt( ( main.price_filter.max_price * 40) / 100),  parseInt( ( main.price_filter.max_price * 40) / 100) ],
      // '50%': [ parseInt( ( main.price_filter.max_price * 50) / 100),  parseInt( ( main.price_filter.max_price * 50) / 100) ],
      // '60%': [ parseInt( ( main.price_filter.max_price * 60) / 100),  parseInt( ( main.price_filter.max_price * 60) / 100) ],
      // '70%': [ parseInt( ( main.price_filter.max_price * 70) / 100),  parseInt( ( main.price_filter.max_price * 70) / 100) ],
      // '80%': [ parseInt( ( main.price_filter.max_price * 80) / 100),  parseInt( ( main.price_filter.max_price * 80) / 100) ],
      // '90%': [ parseInt( ( main.price_filter.max_price * 90) / 100),  parseInt( ( main.price_filter.max_price * 90) / 100) ],
      'max': [parseInt(main.price_filter.max_price)]
    };

    noUiSlider.create(priceSlider, {
      start: [parseInt(main.selected_min), parseInt(main.selected_max)],
      step: 1,
      behaviour: 'tap-drag',
      connect: true,
      range: range,
      pips: {
        mode: 'range',
        density: 5
      }
    });
    priceSlider.noUiSlider.on('update', values => {
      $('input#max_price').val(values[1]);
      $('input#min_price').val(values[0]);
      $('.selected_prices span.min').text(values[0]);
      $('.selected_prices span.max').text(values[1]);
    })
  }

  const wooNotices = $('.woocommerce-notices-wrapper');

  const updateCartData = e => {
    $.post({
      url: main.url,
      data: {
        action: "mini_cart_count"
      },
      success: function (response) {
        $('.cart_count').text(response.data.cart_items_count)
      }
    })
  }

  const toggleFieldsClasses = (value) => {
    $('#pickup_point_field').toggleClass('hide', value != 'local_pickup:5');
  }

  // Once DOM is loaded
  toggleFieldsClasses($('#shipping_method input[type="radio"]:checked').val());

  $(document).on('wc_fragments_loaded update_checkout updated_wc_div', (e) => {
    updateCartData()
  })
  $('.woocommerce-notices-wrapper').on('DOMSubtreeModified', e => {
    $(wooNotices).slideDown();
    setTimeout(() => {
      $(wooNotices).slideUp();
    }, 4000);
    clearTimeout()
  });

  // On change
  $(document.body).on('change', '#shipping_method input[type="radio"]', function () {
    toggleFieldsClasses(this.value);
  });

  $(document).on('change', '#product_quantity', e => {
    $(document).find('button[data-quantity]').attr('data-quantity', e.target.value);
  })

  $(document).on('click', '#to_cart', (e) => {
    $.post({
      url: main.url,
      data: {
        action: "to_cart",
        product_id: $(e.currentTarget).attr('data-product_id'),
        quantity: $(e.currentTarget).attr('data-quantity'),
        variation: $('#variation-selector-form').serializeArray(),
        variation_id: $('input[name="variation_id"]').val()
      },
      beforeSend: r => {
        $(e.currentTarget).addClass('button--loading');
      },
      success: (r) => {
        if (r.data.added) {
          $(document.body).trigger('wc_fragments_loaded');
          $(document.body).trigger('update_checkout');
          $(document.body).trigger('updated_wc_div');
          $(document.body).trigger('wc_fragments_refreshed');
          $(document.body).trigger('wc_fragment_refresh');
          $(document.body).trigger('updated_wc_div');
        }
      },
      complete: x => {
        let productCardTitle = $(e.currentTarget).parent().parent().find('.card-title').text();
        let singleProductTitle = $('.custom_summary__product_controls_title').text();
        let productTitle = productCardTitle !== '' ? productCardTitle : singleProductTitle;
        if (x.responseJSON.data.added) {
          showToast(`${productTitle} ${main.added_to_cart}`, main.cart_title, '<i class="bi bi-bag-check"></i>');
        }
        $(e.currentTarget).removeClass('button--loading');
      }
    })
  })
  $(document).on('change', '#variation-selector-form select', (e) => {
    $.post({
      url: main.url,
      data: {
        action: "find_variation",
        product_id: $('#to_cart').attr('data-product_id'),
        variation: $('#variation-selector-form').serializeArray()
      },
      beforeSend: rq => {
        showLoader(true)
      },
      success: (response) => {
        $('.custom_summary__product_controls_add .product-price, span.sku').html('');
        // pass variation id
        if (typeof response.data.variation_id !== 'undefined') {
          $('input[name="variation_id"]').val(response.data.variation_id);
        } else {
          $('input[name="variation_id"]').val('');
        }
        // price
        if (typeof response.data.display_price !== 'undefined') {
          $('.custom_summary__product_controls_add .product-price').text(`${response.data.display_price} ${main.currency_symbol}`);
        } else {
          $('.custom_summary__product_controls_add .product-price').text(`${response.data.price} ${main.currency_symbol}`);
        }
        // stock status
        // || ( typeof response.data.stock_status !== 'undefined' && response.data.stock_status == 'instock'  --> allow buy base product
        if ((response.data.is_in_stock && typeof response.data.is_in_stock !== 'undefined')) {
          $('#to_cart').removeClass('disabled');
        } else {
          $('.custom_summary__product_controls_add .product-price > span').remove();
          $('#to_cart').addClass('disabled');
          $('.custom_summary__product_controls_add .product-price').append(` | <span class="text-red">${main.out_of_stock}</span>`);
        }
        // sku
        if (response.data.sku !== '') {
          $('span.sku').text(response.data.sku);
        } else {
          $('span.sku').text('N/A');
        }
      },
      complete: rs => {
        showLoader(false)
      }
    })
  })
  // $('.filter_button_wrapper').on('click', e => {
  //   $('#archive_product_filter').toggleClass('active');
  //   $('#menu_filter').toggleClass('open');
  //   $('#main_nav_overlay').toggleClass('filter_opened');
  // })
  $('#clear_filters').on('click', e => {
    const filterForm = $("#filter-form")[0];
    filterForm.reset();
    window.location = location.href.split('?')[0];
  })
  $('#submit_filters, #clear_filters').on('click', e => {
    $(e.currentTarget).addClass('button--loading');
  });

  // $(document.body).on('click', e => {
  //   if (!$(e.target).closest('#menu_filter').length && !$(e.target).closest('.filter_button_wrapper').length) {
  //     $('#archive_product_filter').removeClass('active');
  //     $('#menu_filter').removeClass('open');
  //   }
  // });
}) // document.ready end