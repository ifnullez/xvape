import "~/node_modules/bootstrap-icons/font/bootstrap-icons.css";

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