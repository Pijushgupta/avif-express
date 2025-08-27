<template>
	<div class="w-full flex md:flex-row flex-col justify-between items-center p-4 border-b" :class="backgroundConv !== 'off' ? '!border-b-0' : ''">
		<label class="w-full md:w-1/2 flex justify-start mb-2 md:mb-0" for="cronjobdirectory">Automatic image Processing in
			Background</label>
		<div class="w-full md:w-1/2 flex justify-start md:justify-end">
			<select id="cronjobdirectory" class="w-full md:w-auto" v-model="backgroundConv" v-on:change="setBackgroundConv">
				<option value="off">Inactive</option>
				<option value="theme">Theme Directory</option>
				<option value="upload">Upload Directory</option>
				<option value="themeandupload">Theme & Upload Directory</option>
			</select>
		</div>
	</div>
	<div class="w-full flex flex-col items-start p-4 pt-0 border-b" v-if="backgroundConv !== 'off'">
		<div class=" bg-gray-50 rounded  w-full">
			<TimeSelect/>
		</div>
	</div>
</template>
<script setup>
import { ref } from 'vue';
const backgroundConv = ref('off');
import { useI18n } from 'vue-i18n';
import TimeSelect from './autobackgroundconv/TimeSelect.vue';
const { t } = useI18n({});

const getBackgroundConv = () => {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetBackgroudConv');
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body: data
	})
		.then(res => res.json())
		.then(res => {
			backgroundConv.value = res

		})
		.catch(err => console.log(err));
}
getBackgroundConv();

const setBackgroundConv = () => {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxSetBackgroudConv');
	data.append('avifbackgroundConv', backgroundConv.value);
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body: data
	})
		.then(res => res.json())
		.then(res => {

		})
		.catch(err => console.log(err));
}
</script>