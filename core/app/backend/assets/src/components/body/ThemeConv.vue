<template>
<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex justify-start" for="bulkcnvtbtn">Theme directory ({{ conveted_files }} / {{ total_files }})</label>
				<div class="w-1/2 flex justify-end">
					<button class=" bg-blue-600 text-white px-4 py-2 rounded-full mr-3" v-on:click="convert">Convert</button>
					<button class=" bg-gray-600 text-white px-4 py-2 rounded-full"  v-on:click="deleteImg">Delete</button>
				</div>
</div>	
</template>
<script setup>
import { waitingSatus } from '../../../stores/state';
import { useToast } from 'vue-toastification';
import { ref } from 'vue';
const theme_name = ref();
const is_child = ref();
const conveted_files = ref();
const total_files = ref();

const toWait = waitingSatus();
const gdstatus = gd;
const avifsupportstatus = avifsupport;
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
	if (gdstatus != '1' || avifsupportstatus != '1') return false;
	if (conveted_files.value == total_files.value || total_files.value <= 0) {
		return false;
	} 
	const toast = useToast();
	toast("Conversion may take time. Once its done, we well notify you!");
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
				toast("Converted all Images inside theme directory.");
				toWait.toggleWaiting();
			}

			if (res === false) {
				getTheme();
				const toast = useToast();
				toast.error("Operation failed, Unable to set php execution time limit.");
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
	toast("This may take time. Once its done, we will notify you!");
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
				toast("Deleted all Avif Images inside the Theme directory.");
				toWait.toggleWaiting();
			}
			
			
		})
		.catch(err => console.log(err));
}



</script>