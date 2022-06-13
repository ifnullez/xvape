const showLoader = (showLoader) => {
    if(showLoader){
        document.body.insertAdjacentHTML('afterbegin', `<div class="xldr">
        <div class="infinity">
        <div>
            <span></span>
        </div>
        <div>
            <span></span>
        </div>
        <div>
            <span></span>
        </div>
    </div></div>`);
    } else {
        document.querySelector('.xldr').remove();
    }
}
window.showLoader = showLoader;

window.addEventListener('DOMContentLoaded', (event) => {
    showLoader(true)
});
  
window.addEventListener('load', (event) => {
    showLoader(false)
});