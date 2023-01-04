<template>
<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex justify-start" for="bulkcnvtbtn">Theme: {{theme_name}} ({{ conveted_files }} / {{ total_files }})</label>
				<div class="w-1/2 flex justify-end">
					<button class=" bg-blue-600 text-white px-4 py-2 rounded-full mr-3" v-on:click="convert">Convert</button>
					<button class=" bg-gray-600 text-white px-4 py-2 rounded-full"  >Delete</button>
				</div>
</div>	
</template>
<script setup>
import { ref } from 'vue';
const theme_name = ref();
const is_child = ref();
const conveted_files = ref();
const total_files = ref();
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
			console.log(res);
			theme_name.value = res[0].theme_name;
			is_child.value = res[0].is_child;
			conveted_files.value = res[0].files.converted;
			total_files.value = res[0].files.total;
		})
		.catch(err => console.log(err));
}
getTheme();

function convert() {
	if (conveted_files.value == total_files.value || total_files.value <= 0) {
		return false;
	} 
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
			console.log(res);
			
		})
		.catch(err => console.log(err));

}

function deleteImg(){
	if (conveted_files.value == 0) {
		return false;
	} 
}



</script>