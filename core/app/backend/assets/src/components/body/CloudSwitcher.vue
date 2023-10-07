<template>
<div class="w-full flex md:flex-row flex-col justify-between items-center p-4 border-b">	
	<label class="w-full md:w-1/2 flex justify-start mb-2 md:mb-0" for="opengine">Conversion Engine</label>
	<div class="w-full md:w-1/2 flex justify-start md:justify-end">
		<select id="opengine" class="w-full md:w-auto" v-model="conversionEngine" v-on:change="setConversionEngine">
			<option value="cloud">Cloud</option>
			<option value="local">Local</option>
		</select>
	</div>
</div>
</template>
<script setup>
import {ref} from 'vue';
const conversionEngine = ref();

const getConversionEngine = () =>{
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetConversionEngine');
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			conversionEngine.value = res
			
		})
		.catch(err => console.log(err));
}
getConversionEngine();

const setConversionEngine = () =>{
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxSetConversionEngine');
	data.append('engine', conversionEngine.value);
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			
		})
		.catch(err => console.log(err));
}
</script>