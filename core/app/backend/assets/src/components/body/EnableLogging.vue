<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex justify-start" for="enableLogging">{{t('enableLogging')}}</label>
				<div class="w-1/2 flex justify-end"><input id="enableLogging" v-model="enableLogging" v-on:click="setEnableLogging" type="checkbox" name="enableLogging"/></div>
			</div>
</template>
<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n({});
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
getEnableLogging();

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