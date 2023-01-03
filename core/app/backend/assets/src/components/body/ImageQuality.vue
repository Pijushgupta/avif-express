<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
		<label class="w-1/2 flex flex-col justify-start" for="imgqal">
			<span class="mb-1">Image Quality</span>
			<span class="text-xs">0 - Worst, 100 - Best. Higher quality will increase the file size.</span>
		</label>
		<div class="w-1/2 flex justify-end">
			<input id="imgqal" type="range" min="0" max="110" name="imgqal" v-model="imageQuality" class="w-full" v-on:mouseup="setQuality"/> <span class="w-1/12 flex justify-end">{{ imageQuality }}</span>
		</div>
	</div>
</template>
<script setup>
import { ref } from 'vue';
const imageQuality = ref(0);

function getQuality() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetImgQuality');
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body: data
	})
		.then(res => res.json())
		.then(res => {
			imageQuality.value = res;
			
		})
		.catch (err => console.log(err));
}
getQuality();

function setQuality() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('quality', imageQuality.value);
	data.append('action', 'ajaxSetImgQuality');
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body: data
	})
		.then(res => res.json())
		.then(res => {
			
		})
		.catch (err => console.log(err));
}
</script>