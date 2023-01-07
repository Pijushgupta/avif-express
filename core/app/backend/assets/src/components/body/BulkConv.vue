<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex flex-col justify-start" for="bulkcnvtbtn"><span>Upload directory <template v-if="totalImages != null && convertedImage != null">({{convertedImage}} / {{totalImages}})</template></span><span class="text-xs" v-if="numberOfthums != null">{{ numberOfthums }} thumbnails sizes x {{ totalImages - convertedImage }} images to be converted</span></label>
				<div class="w-1/2 flex justify-end">
					<button class=" bg-blue-600 text-white px-4 py-2 rounded-full mr-3" id="bulkcnvtbtn" v-on:click="convert">Convert</button>
					<button class=" bg-gray-600 text-white px-4 py-2 rounded-full" id="delconvimgs" v-on:click="deleteAll" >Delete</button>
				</div>
			</div>
</template>
<script setup>
import { waitingSatus } from '../../../stores/state';
import { ref } from 'vue';
import { useToast } from 'vue-toastification';
const totalImages = ref(null);
const convertedImage = ref(null);
const numberOfthums = ref(null)

const toWait = waitingSatus();
const gdstatus = gd;
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
	if (gdstatus != 1 || gdstatus != '1') return false;
	if (totalImages.value === convertedImage.value) return false;
	const toast = useToast();
	toWait.toggleWaiting();
	toast("Conversion may take time. Once its done, we will notify you!");

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
				toast("Converted all Images inside upload directory.");
				getAttachemnts();
				toWait.toggleWaiting();
			}
			if (res === false) {
				const toast = useToast();
				toast.error("Operation failed, Unable to set php execution time limit.");
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
	toast("This may take time. Once its done, we well notify you!");
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
				toast("Deleted all Avif Images inside upload directory.");
				convertedImage.value = 0;
				toWait.toggleWaiting();
			}
		})
		.catch(err => console.log(err));

}
</script>