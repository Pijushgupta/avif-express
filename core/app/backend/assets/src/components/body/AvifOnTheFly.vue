<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex justify-start" for="avifonthefly">{{$t("disableOnTheFlyLabel")}}</label>
				<div class="w-1/2 flex justify-end"><input id="avifonthefly" v-model="onTheFlyAvif" v-on:click="setonTheFlyAvif" type="checkbox" name="avifonthefly"/></div>
			</div>
</template>
<script setup>
import { ref } from 'vue';
const onTheFlyAvif = ref(false);
function getonTheFlyAvif(){
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetOnTheFlyAvif');

	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			onTheFlyAvif.value = res;
			
		})
		.catch(err => {console.log(err) });

}
getonTheFlyAvif();

function setonTheFlyAvif() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxSetOnTheFlyAvif');
	
	
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			
			//console.log(res);
		})
		.catch(err => {console.log(err) });
}
</script>