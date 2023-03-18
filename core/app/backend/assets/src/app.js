/**
 * importing createApp from VueJs
 */
import { createApp } from "vue";
/**
 * Importing pinia from state management
 */
import { createPinia } from "pinia";
/**
 * Internationalization of texts using vue-i18n
 */
import i18n from "./locales/i18n";
/**
 * Importing vue-toast for notification system
 */
import Toast from "vue-toastification";
/**
 * Importing css for vue-toast for notification
 */
import "vue-toastification/dist/index.css";
/**
 * Importing main component file
 */
import Main from "./main.vue";

/**
 * Setting up option vue-toast for notification
 */
const toastOption = {
	transition: "Vue-Toastification__fade",
	maxToasts: 20,
	timeout: false,
	hideProgressBar: true,
	newestOnTop: true,
	position: "bottom-right",
	toastClassName: "awraq-toast",
};
/**
 * creating the main vue app with imported Main component
 */
const app = createApp(Main);
/**
 * creating pinia
 */
const pinia = createPinia();
/**
 * using pinia in vue app
 */
app.use(pinia);
/**
 * using i18n in vue app
 */
app.use(i18n);
/**
 * using vue-toast in vue app
 */
app.use(Toast, toastOption);
/**
 * mounting the app on real dom with id
 */
app.mount("#avife-root");
