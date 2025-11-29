import "./bootstrap";
import flatpickr from "flatpickr";
import Alpine from "alpinejs";
import Chart from "chart.js/auto";

import "flatpickr/dist/flatpickr.min.css";

window.Alpine = Alpine;

Alpine.start();

window.flatpickr = flatpickr;

window.Chart = Chart;
