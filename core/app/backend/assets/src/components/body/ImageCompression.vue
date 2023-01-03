<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex flex-col justify-start" for="imgComSpeed">
					<span class="mb-1">Compression Speed</span>
					<span class="text-xs">0 - Slow, smaller file. 10 - Fast, larger file.</span>
				</label>
				<div class="w-1/2 flex justify-end">
					<input id="imgComSpeed" type="range" min="0" max="10" name="imgComSpeed" v-model="compressionSpeed" class="w-full" v-on:mouseup="setComSpeed"/> <span class="w-1/12 flex justify-end">{{ compressionSpeed }}</span>
				</div>
			</div>
</template>
<script setup>
import { ref } from 'vue';
const compressionSpeed = ref(0);
function getComSpeed() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetComSpeed');
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body: data
	})
		.then(res => res.json())
		.then(res => {
			compressionSpeed.value = res;
			
		})
		.catch (err => console.log(err));
}
getComSpeed();

function setComSpeed() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('speed', compressionSpeed.value);
	data.append('action', 'ajaxSetComSpeed');
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