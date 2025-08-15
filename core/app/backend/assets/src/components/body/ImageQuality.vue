<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
		<label class="w-1/2 flex flex-col justify-start" for="imgqal">
			<span class="mb-1">{{$t('iamgeQuality')}}</span>
			<span class="text-xs">{{ $t('qualityWarning') }}</span>
		</label>
		<div class="w-1/2 flex justify-end">
			<input id="imgqal" type="range" min="0" max="110" name="imgqal" v-model.number="imageQuality" class="w-full" v-on:mouseup="setQuality"/> <span class="w-1/12 flex justify-end">{{ imageQuality.toFixed(0) }}</span>
		</div>
	</div>
</template>
<script setup>

import { useAnimateNumber } from '../../composables/useAnimateNumber';

const { value: imageQuality, animateTo } = useAnimateNumber();
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
			 animateTo(parseFloat(res));
			//imageQuality.value = res;
			
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