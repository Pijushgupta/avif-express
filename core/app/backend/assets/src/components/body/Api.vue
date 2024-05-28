<template>
	<div class="w-full flex flex-col justify-between items-center  border-b">
		<div class="w-full flex flex-row justify-between items-center p-4">
			<label class="w-full flex justify-start  " for="apiContentArea">{{t('api')}}</label>
			<button id="apiContentArea" @click="showApiContent = !showApiContent">
				
				<svg v-show="showApiContent == false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"> <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /> </svg>
				<svg v-show="showApiContent == true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"> <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /> </svg>


			</button>
		</div>
		<div class="w-full p-4 flex flex-col pt-0" v-show="showApiContent == true">
			<div class="flex flex-col mb-2">
				<label for="siteurl" class="mb-1">Site name</label>
				<input  id="siteurl" type="text" v-bind:value="siteName" disabled>
			</div>
			<div class="flex flex-col mb-2">
				<label for="apikey" class="mb-1">API key</label>
				<input  id="apikey" type="text" v-model="apiKey">
			</div>
			<div class="flex flex-row justify-end">
				<button class="bg-blue-600 text-white px-4 py-2 rounded-full mr-2" >Get API key</button>
				<button class="bg-blue-600 text-white px-4 py-2 rounded-full" @click="setApiKey">Update</button>
			</div>
		</div>
				
	</div>
</template>
<script setup>
import { useToast } from 'vue-toastification';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';

const { t } = useI18n({});
const showApiContent = ref(false);

const siteName = siteUrl;
const apiKey = ref('');

//get api key
function getApiKey(){
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetApiKey');

	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			
			if(res !== false){
				apiKey.value = res;
			}
		})
		.catch(err => {console.log(err) });
}
getApiKey();

// set api key
function setApiKey(){
	const toast = useToast();
	if(apiKey.value == ''){
		
		toast.error('Please enter api key');
		return;
	}
	console.log(apiKey.value);
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxSetApiKey');
	data.append('apiKey', apiKey.value);

	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			
			if(res === true){
				
				toast(t('Api key updated successfully'));
				
			}
		})
		.catch(err => {console.log(err) });
}

</script>