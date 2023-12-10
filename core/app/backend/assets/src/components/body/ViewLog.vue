<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex justify-start" for="avifonthefly">{{t('viewLog')}}</label>
				<div class="w-1/2 flex justify-end">
					<button @click="deleteLogFile" class="bg-red-600 text-white px-4 py-2 rounded-full mr-2 disabled:bg-red-300" v-bind:disabled="log == false">{{t('delete')}}</button>
					<button @click="viewLogFile" class="bg-blue-600 text-white px-4 py-2 rounded-full disabled:bg-blue-300" v-bind:disabled="log == false">{{t('view')}}</button>
				</div>
			</div>
</template>
<script setup>
import { useToast } from 'vue-toastification';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';

const { t } = useI18n({});
const log = ref(false);

function isLogFileExists() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxIsLogFileExists');
	
	
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			
			if(res.status == 'success') {
				log.value = true;
			}
		})
		.catch(err => {console.log(err) });
}
isLogFileExists();

/**
 * Delete log file
 */
function deleteLogFile() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxDeleteLogFile');
	
	
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			
			/**
			 * if server response is success then show toast
			 */
			if(res.status == 'success') {
				log.value = false;
				const toast = useToast();
				toast(t('logFileDeleted'));
			}
		})
		.catch(err => {console.log(err) });
}

function viewLogFile() {
	const logFileRel = avifLogFile;
	console.log(logFileRel);	
	window.open(logFileRel, '_blank');
}	

</script>