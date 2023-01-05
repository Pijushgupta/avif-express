import { defineStore } from "pinia";

export const waitingSatus = defineStore("statuschange", {
	state: () => ({ isWaiting: false }),
	getters: {},
	actions: {
		toggleWaiting() {
			this.isWaiting = !this.isWaiting;
		},
	},
});
