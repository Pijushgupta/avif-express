<template>
	<div class="w-full flex flex-row justify-between items-center p-4">
				<label class="w-1/2 flex justify-start" for="lazyloadbackgroundimage">Lazy Background</label>
				<div class="w-1/2 flex justify-end"><input id="lazyloadbackgroundimage" v-model="lazyBackground" v-on:click="setLazyBackground" type="checkbox" name=""/></div>
			</div>
</template>
<script setup>
import { ref } from 'vue';
const lazyBackground = ref(false);
function getLazyBackground(){
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetLazyBackground');

	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			lazyBackground.value = res;
			
		})
		.catch(err => {console.log(err) });

}
getLazyBackground();

function setLazyBackground() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxSetLazyBackground');
	
	
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