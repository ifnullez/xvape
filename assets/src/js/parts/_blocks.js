import {tns} from "~/node_modules/tiny-slider/src/tiny-slider";

const newerSliderContainer = document.querySelector('.newest-carousel-block');
const mostCommentedContainer = document.querySelector('.most-commented-carousel-block');

if(newerSliderContainer){
    const newestProductsBlock = tns({
        container: newerSliderContainer,
        items: 4,
        autoplay: false,
        slideBy: 1,
        gutter: 15,
        nav: false,
        touch: true,
        prevButton: '#newest-prev',
        nextButton: '#newest-next',
        responsive: {
            "1400": {
              "items": 4
            },
            "1358": {
                "items": 3
            },
            "992": {
                "items": 2
            },
            "640": {
                "items": 2
            },
            "320": {
                "items": 1
            }
          }
    });
}
if(mostCommentedContainer){
    const mostCommentedBlock = tns({
        container: mostCommentedContainer,
        items: 4,
        autoplay: false,
        slideBy: 1,
        nav: false,
        touch: true,
        gutter: 15,
        prevButton: '#most-commented-prev',
        nextButton: '#most-commented-next',
        responsive: {
            "1400": {
              "items": 4
            },
            "1358": {
                "items": 3
            },
            "992": {
                "items": 2
            },
            "640": {
                "items": 2
            },
            "320": {
                "items": 1
            }
          }
    });
}