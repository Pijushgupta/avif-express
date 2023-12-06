<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex justify-start" for="avifonthefly">View Log</label>
				<div class="w-1/2 flex justify-end"><button class="bg-blue-600 text-white px-4 py-2 rounded-full">View</button></div>
			</div>
</template>
<script setup>
import { ref } from 'vue';
const enableLogging = ref(false);
function getEnableLogging(){
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetEnableLogging');

	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			enableLogging.value = res;
			
		})
		.catch(err => {console.log(err) });

}
//getEnableLogging();

function setEnableLogging() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxSetEnableLogging');
	
	
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