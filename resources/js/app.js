import "./bootstrap";
import './stisla';
import $ from 'jquery';
import Alpine from "alpinejs";
import { persist } from "@alpinejs/persist";
import 'datatables.net';
import 'datatables.net-responsive';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5'

window.Alpine = Alpine;
window.$ = window.jQuery = $

Alpine.plugin(persist);
Alpine.start();