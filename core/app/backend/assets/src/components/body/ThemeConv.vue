<template>
<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex justify-start" for="bulkcnvtbtn">{{ t('themeDirectory') }} ({{ conveted_files }} / {{ total_files }})</label>
				<div class="w-1/2 flex justify-end">
					<button class=" bg-blue-600 text-white px-4 py-2 rounded-full mr-3" v-on:click="convert">{{ t('convert') }}</button>
					<button class=" bg-gray-600 text-white px-4 py-2 rounded-full"  v-on:click="deleteImg">{{ t('delete') }}</button>
				</div>
</div>	
</template>
<script setup>
import { waitingSatus } from '../../../stores/state';
import { useToast } from 'vue-toastification';
import { useI18n } from 'vue-i18n';

import { ref } from 'vue';
const theme_name = ref();
const is_child = ref();
const conveted_files = ref();
const total_files = ref();
const { t } = useI18n({});
const toWait = waitingSatus();
const gdstatus = gd;
const avifsupportstatus = avifsupport;
const hasImagickstatus  = hasImagick;
const isEnabledCloud = isCloudEngine;
function getTheme() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetCurrentTheme');
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			
			theme_name.value = res[0].theme_name;
			is_child.value = res[0].is_child;
			conveted_files.value = res[0].files.converted;
			total_files.value = res[0].files.total;
		})
		.catch(err => console.log(err));
}
getTheme();

function convert() {
	
	if (avifsupportstatus != '1' && hasImagickstatus != '1' && isEnabledCloud != '1') return false;
	if (conveted_files.value == total_files.value || total_files.value <= 0) {
		return false;
	} 
	const toast = useToast();
	toast(t('conversionMayTakeTimeMsg'));
	toWait.toggleWaiting();
	function innerConvert() {
		const data = new FormData();
		data.append('avife_nonce', avife_nonce);
		data.append('action', 'ajaxThemeFilesConvert');
		fetch(avife_ajax_path, {
			method: 'POST',
			credentials: 'same-origin',
			body:data
		})
		.then(res => res.json())
		.then(res => {
			
			if (res === true || res === null) {
				getTheme();
				const toast = useToast();
				toast(t('convertedAllImageThemeDir'));
				toWait.toggleWaiting();
			}

			if (res === false) {
				getTheme();
				const toast = useToast();
				toast.error(t("operationFailedPhpExeTime"));
				toWait.toggleWaiting();
			}

			if (res === 'keep-alive') {
				console.log('keep-alive');
				innerConvert()
			}
			
		})
		.catch(err => console.log(err));
	}

	innerConvert();
	

}

function deleteImg(){
	if (conveted_files.value == 0) {
		return false;
	} 
	const toast = useToast();
	toast(t("thisMayTakeTime"));
	toWait.toggleWaiting();
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxThemeFilesDelete');
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			if (res == true) {
				getTheme();
				const toast = useToast();
				toast(t("deleteImageInTheme"));
				toWait.toggleWaiting();
			}
			
			
		})
		.catch(err => console.log(err));
}



</script>