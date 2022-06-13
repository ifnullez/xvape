import { Fancybox } from "@fancyapps/ui";
import * as bootstrap from 'bootstrap';
import 'nouislider/dist/nouislider.css';

// parts
import "../scss/main.scss"
import "./parts/_woocommerce";
import "./parts/_modals";
import "./parts/_checkout";
import "./parts/_header";
import "./parts/_custom_blocks";
import "./parts/_notifications";
import "./parts/_loader";
import "./parts/_blocks";

// make available global
window.Fancybox = Fancybox;

// configure fancybox v4
Fancybox.bind("[data-fancybox]", {
    Image: {
      zoom: true,
    },
    Toolbar: {
        display: [
          { id: "prev", position: "left" },
          { id: "counter", position: "left" },
          { id: "next", position: "left" },
          { id: "close", position: "left" },
          { id: "slideshow", position: "left" },
          { id: "fullscreen", position: "left" },
          { id: "thumbs", position: "left" },
          { id: "zoom", position: "left" },
          { id: "next", position: "left" },
        ],
    },
});