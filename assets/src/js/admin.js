import "~/node_modules/bootstrap-icons/font/bootstrap-icons.css";
import "../js/parts/_loader";

jQuery(document).ready( $ => {
    $('#insert_local_based_attrs').on('click', e => {
        $.post({
            url: adm.url,
            data: {
              action: 'insert_local_based_attrs'
            },
            beforeSend: () => {
                
            },
            success: (response) => {
                console.log(response)
            },
            complete: (x) => {
            }
        })
    })
})

jQuery(document).ready( $ => {
    $('#set_all_products_variable_type').on('click', e => {
        $.post({
            url: adm.url,
            data: {
              action: 'set_all_products_variable_type'
            },
            beforeSend: () => {
                showLoader(true)
            },
            success: (response) => {
                showLoader(false)
                console.log(response)
            },
            complete: (x) => {
            }
        })
    })
})