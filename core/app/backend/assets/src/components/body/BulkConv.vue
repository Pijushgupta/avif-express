<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex justify-start" for="bulkcnvtbtn">Upload Directory <template v-if="totalImages != null && convertedImage != null">({{convertedImage}}  /{{totalImages}})</template></label>
				<div class="w-1/2 flex justify-end">
					<button class=" bg-blue-600 text-white px-4 py-2 rounded-full mr-3" id="bulkcnvtbtn" v-on:click="convert">Convert</button>
					<button class=" bg-gray-600 text-white px-4 py-2 rounded-full" id="delconvimgs" v-on:click="deleteAll" >Delete</button>
				</div>
			</div>
</template>
<script setup>
import { ref } from 'vue';
import { useToast } from 'vue-toastification';
const totalImages = ref(null);
const convertedImage = ref(null);
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
				
			
			
		})
		.catch(err => {console.log(err) });
}
getAttachemnts();

function convert() {
	if (totalImages.value === convertedImage.value) return false;
	const toast = useToast();
	toast("Conversion may take time. Once its done, we well notify you!");
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
			if (res === 'done') {
				const toast = useToast();
				toast("Converted all Images inside upload directory.");
				getAttachemnts();
			}
		})
		.catch(err => console.log(err));
}
function deleteAll() {
	if (convertedImage.value < 1) return false;
	const toast = useToast();
	toast("This may take time. Once its done, we well notify you!");
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
			if (res == true) {
				const toast = useToast();
				toast("Deleted all Avif Images inside upload directory.");
			}
		})
		.catch(err => console.log(err));

}
</script>