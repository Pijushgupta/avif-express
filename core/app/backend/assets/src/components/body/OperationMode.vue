<template>
	<div class="w-full flex md:flex-row flex-col justify-between items-center p-4 border-b">
				<label class="w-full md:w-1/2 flex justify-start mb-2 md:mb-0" for="opmode">{{$t("rendering")}}</label>
				<div class="w-full md:w-1/2 flex justify-start md:justify-end"><select id="opmode" class="w-full md:w-auto" v-model="operationModeStatus" v-on:change="setOperationMode"><option value="active">{{$t('active')}}</option><option value="inactive">{{$t('inactive')}}</option></select></div>
			</div>
</template>
<script setup>
import { ref } from 'vue';
const operationModeStatus = ref();

function getOperationMode() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetOperationMode');
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			operationModeStatus.value = res
			
		})
		.catch(err => console.log(err));
}
getOperationMode();

function setOperationMode() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxSetOperationMode');
	data.append('mode', operationModeStatus.value);
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