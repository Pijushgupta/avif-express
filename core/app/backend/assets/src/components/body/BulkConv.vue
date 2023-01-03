<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex justify-start" for="bulkcnvtbtn">Bulk Conversion <template v-if="totalImages != false">({{totalImages}})</template></label>
				<div class="w-1/2 flex justify-end"><button class=" bg-blue-600 text-white px-4 py-2 rounded-full" id="bulkcnvtbtn">Convert</button></div>
			</div>
</template>
<script setup>
import { ref } from 'vue';
const totalImages = ref(false);
function getAttachemnts(){
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxCountMedia');

	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			if (typeof res == 'object') {

				totalImages.value = Object.keys(res).length;
				
			}
		})
		.catch(err => {console.log(err) });
}
getAttachemnts();
</script>