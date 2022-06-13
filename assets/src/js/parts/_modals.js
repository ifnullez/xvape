jQuery(document).ready($ => {
    const getModalData = (action) => {
        $.post({
            url: main.url,
            data: {
                action: action
            },
            beforeSend: () => {
                Fancybox.close();
                showLoader(true)
            },
            success: (response) => {
                new Fancybox.show([
                    {
                        src: response.data,
                        type: "html",
                    },
                ]);
            },
            complete: (x) => {
                showLoader(false);
            }
        })
    }

    const getLoginForm = () => getModalData('get_login_modal');
    const getRegisterForm = () => getModalData('get_register_modal');
    // const getSearchForm = () => getModalData('get_search_modal');
    const getMiniCart = () => getModalData('get_mini_cart_modal');

    // window.getSearchForm = getSearchForm;
    window.getMiniCart = getMiniCart;
    window.getLoginForm = getLoginForm;
    window.getRegisterForm = getRegisterForm;
})