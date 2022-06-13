import { Toast } from "bootstrap";
const showToast = (text, title, bootstrapIconHtml) => {
    document.querySelector('.toast-container').insertAdjacentHTML('afterbegin', `<div class="toast fade shadow-md" id="notify">
    <div class="toast-header">
        <strong class="me-auto">${bootstrapIconHtml} ${title}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        ${text}
    </div>
</div>`);
  new Toast('#notify').show()
}
  
  window.showToast = showToast;