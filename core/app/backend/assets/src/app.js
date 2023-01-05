import { createApp } from "vue";
import { createPinia } from "pinia";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import Main from "./main.vue";
const toastOption = {
	transition: "Vue-Toastification__fade",
	maxToasts: 20,
	timeout: false,
	hideProgressBar: true,
	newestOnTop: true,
	position: "bottom-right",
	toastClassName: "awraq-toast",
};
const app = createApp(Main);
const pinia = createPinia();
app.use(pinia);
app.use(Toast, toastOption);
app.mount("#avife-root");
