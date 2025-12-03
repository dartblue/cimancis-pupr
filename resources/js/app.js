import "./bootstrap";
import Alpine from "alpinejs";
import flatpickr from "flatpickr";
import Chart from "chart.js/auto";

// import "flatpickr/dist/flatpickr.min.css";

window.flatpickr = flatpickr;

window.Chart = Chart;

window.Alpine = Alpine;

Alpine.start();
