<template>
	<div class="w-full flex md:flex-row flex-col justify-between items-center p-4 border-b">
				<label class="w-full md:w-1/2 flex justify-start mb-2 md:mb-0" for="fallbackmode">{{$t("fallbackText")}}</label>
				<div class="w-full md:w-1/2 flex justify-start md:justify-end">
                    <select 
                    id="fallbackmode" class="w-full md:w-auto" v-model="fallbackMode" v-on:change="setFallbackMode">
                        <option value="original">{{$t('original')}}</option>
                        <option value="webp">{{$t('webp')}}</option>
                    </select>
                </div>
			</div>
</template>
<script setup>
import { ref } from 'vue';
const fallbackMode = ref();

function getFallbackMode() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetFallbackMode');
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			fallbackMode.value = res
			console.log()
		})
		.catch(err => console.log(err));
}
getFallbackMode();

function setFallbackMode() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxSetFallbackMode');
	data.append('fallbackMode', fallbackMode.value);
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