<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex flex-col justify-start" for="bulkcnvtbtn"><span>{{ t('uploadDirectory') }} <template v-if="totalImages != null && convertedImage != null">({{convertedImage}} / {{totalImages}})</template></span></label>
				<div class="w-1/2 flex justify-end">
					<button class=" bg-blue-600 text-white px-4 py-2 rounded-full mr-3" id="bulkcnvtbtn" v-on:click="convert">{{ t('convert') }}</button>
					<button class=" bg-gray-600 text-white px-4 py-2 rounded-full" id="delconvimgs" v-on:click="deleteAll" >{{ t('delete') }}</button>
				</div>
			</div>
</template>
<script setup>
import { waitingSatus } from '../../../stores/state';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toastification';
const totalImages = ref(null);
const convertedImage = ref(null);
const numberOfthums = ref(null)

const { t } = useI18n({});
const toWait = waitingSatus();
const gdstatus = gd;
const avifsupportstatus = avifsupport;
const hasImagickstatus  = hasImagick;
const isEnabledCloud = isCloudEngine;
function getAttachemnts(){
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxCountMedia');

	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			
			convertedImage.value = res[0]
			totalImages.value = res[1]
			numberOfthums.value = res[2]	
		
			
		})
		.catch(err => {console.log(err) });
}
getAttachemnts();

function convert() {
	if (avifsupportstatus != '1' && hasImagickstatus != '1' && isEnabledCloud != '1') return false;
	if (totalImages.value === convertedImage.value) return false;
	const toast = useToast();
	toWait.toggleWaiting();
	toast(t('conversionMayTakeTimeMsg'));

	function innerConvert() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxConvertRemaining');
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			if (res === true || res === null) {
				const toast = useToast();
				toast(t('convertedAllImageUploadDir'));
				getAttachemnts();
				toWait.toggleWaiting();
			}
			if (res === false) {
				const toast = useToast();
				toast.error(t("operationFailedPhpExeTime"));
				getAttachemnts();
				toWait.toggleWaiting();
			}
			if (res === 'keep-alive') {
				console.log('keep-alive');
				innerConvert();
			}
		})
		.catch(err => console.log(err));
	}

	innerConvert();
	
}
function deleteAll() {
	if (convertedImage.value < 1) return false;
	const toast = useToast();
	toast(t('thisMayTakeTime'));
	toWait.toggleWaiting();
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxDeleteAll');
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body: data
	})
		.then(res => res.json())
		.then(res => {
			if (res === true) {
				const toast = useToast();
				toast(t("deleteImageInUpload"));
				convertedImage.value = 0;
				toWait.toggleWaiting();
			}
		})
		.catch(err => console.log(err));

}
</script>