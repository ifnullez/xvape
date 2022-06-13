jQuery(document).ready($ => {
    const burgerButton = $('#burger_button');
    const mainNaw = $('#main_nav');
    const mainNavOverlay = $('#main_nav_overlay');
    const hederMainWidget = $('#header_controll_widget');
    const siteHeader = $('#site-header');

    const toggleMenu = () => {
        Fancybox.close();
        $(burgerButton).toggleClass('show');
        // $(mainNaw).toggleClass('open');
        // $("main.site-main").toggleClass('menu_opened');
        // $(mainNavOverlay).toggleClass('menu_opened');
        // $(hederMainWidget).toggleClass('menu_opened');
        $('body').toggleClass('disable_scroll');
        $('html').toggleClass('disable_scroll');
    }
    $(burgerButton).on('click', e => {
        toggleMenu();
    })
    $('.offcanvas-close').on('click', e => {
        toggleMenu();
    })
    $("#primary_menu .menu-item-has-children i").on('click', e => {
        e.preventDefault();
        let dropdownContent = $(e.currentTarget).parent('a').next(); //e.currentTarget.nextElementSibling;
        $(e.currentTarget).parent('a').toggleClass('menu_item_opened');
        $(e.currentTarget).toggleClass('menu_item_opened');
        $(dropdownContent).toggleClass('open');
        $(dropdownContent).slideToggle();
    })
    // call search form
    // $('.btn-search').on('click', (e) => {
    //     getSearchForm();
    // });
    // call mini cart
    // $('.shopping-cart-btn').on('click', (e) => {
    //     getMiniCart();
    // });
    // call login form
    $('#login').on('click', (e) => {
        getLoginForm();
    });

    $('#register').on('click', e => {
        getRegisterForm();
    });
    // submit login/register
    $(document).on('submit', 'form#login', e => {
        e.preventDefault();
        $.post({
            url: main.url,
            data: {
                action: 'login',
                login_data: $('form#login').serializeArray()
            },
            beforeSend: () => {
                $('#login_messages').html();
                showLoader(true)
            },
            success: (response) => {
                if (response.data.loggedin) {
                    showLoader(false);
                    Fancybox.close();
                    window.location.reload();
                } else {
                    if (response.data.message) {
                        $('#login_messages').html(response.data.message);
                    }
                }
            },
            complete: (x) => {
                showLoader(false);
            }
        })
    })

    $(document).on('submit', 'form#register', e => {
        e.preventDefault();
        $.post({
            url: main.url,
            data: {
                action: 'register',
                register_data: $('form#register').serializeArray()
            },
            beforeSend: () => {
                $('#register_messages').html();
                showLoader(true)
            },
            success: (response) => {
                if (response.data.registered) {
                    showLoader(false);
                    Fancybox.close();
                    window.location.reload();
                } else {
                    if (response.data.message) {
                        $('#register_messages').html(response.data.message);
                    }
                }
            },
            complete: (x) => {
                showLoader(false);
            }
        })
    })
    $(document.body).on('scroll', e => {
        let header = $(document.body).scrollTop();
        let headerHeight = siteHeader.outerHeight();

        if (header >= headerHeight) {
            $(siteHeader).addClass("sticky");
        } else {
            $(siteHeader).removeClass("sticky");
        }
    });
})