<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex justify-start" for="autoconvertChk">Automatically convert images to AVIF format on upload.</label>
				<div class="w-1/2 flex justify-end"><input id="autoconvertChk" v-model="autoConvStatus" v-on:click="setAutoConvStatus" type="checkbox" name="autoconvertChk"/></div>
			</div>
</template>
<script setup>
import { ref } from 'vue';
const autoConvStatus = ref(false);
function getAutoConvStatus(){
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetAutoConvtStatus');

	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			autoConvStatus.value = res;
			
		})
		.catch(err => {console.log(err) });

}
getAutoConvStatus();

function setAutoConvStatus() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxSetAutoConvtStatus');
	
	
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