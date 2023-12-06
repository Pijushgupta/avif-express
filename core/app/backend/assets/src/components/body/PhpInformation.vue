<template>
	<div class="w-full flex flex-col justify-between items-center p-4 border-b">
				<label class="w-full flex justify-start mb-2" for="">Php Information</label>
				<div class="w-full bg-gray-100 p-2 rounded-lg">
					<div class="w-full flex flex-col justify-end mt-2" v-if="gdInfo != false">
						<div class="flex flex-row justify-between items-center" v-if="'GD Version' in gdInfo">
							<span>GD Version</span>
							<span>{{ gdInfo['GD Version'] }}</span>
						</div>
						<div class="flex flex-row justify-between items-center" v-if="'AVIF Support' in gdInfo">
							<span>AVIF Support</span>
							<span>{{ gdInfo['AVIF Support'] == false ? "No":"Yes" }}</span>
						</div>
						<div class="flex flex-row justify-between items-center" v-if="'WBMP Support' in gdInfo">
							<span>WEBP Support</span>
							<span>{{ gdInfo['WBMP Support'] == false ? "No":"Yes" }}</span>
						</div>

					</div>
					<div class="w-full flex flex-col justify-end mt-2" v-if="imagickInfo != false">
						<div class="flex flex-row justify-between items-center" v-if="imagickInfo['version']['versionString']">
							<span>Imagick Version</span>
							<span>{{ imagickInfo['version']['versionString'] }}</span>
						</div>
						<div class="flex flex-row justify-between items-center" >
							<span>AVIF Support</span>
							<span>{{ imagickInfo['formats'].indexOf('AVIF') !== -1 ? 'Yes':'No' }}</span>
						</div>
						<div class="flex flex-row justify-between items-center" v-if="'WBMP Support' in gdInfo">
							<span>WEBP Support</span>
							<span>{{ imagickInfo['formats'].indexOf('WEBP') !== -1 ? 'Yes':'No' }}</span>
						</div>

					</div>
					<div class="w-full flex flex-col justify-end mt-2" v-if="phpInfo != false">
						<div class="flex flex-row justify-between items-center" v-if="phpInfo['version']">
							<span>Php Version</span>
							<span>{{ phpInfo['version'] }}</span>
						</div>
						<div class="flex flex-row justify-between items-center" v-if="phpInfo['curl'] != false">
							<span>cUrl Version</span>
							<span>{{ phpInfo['curl']['version'] ? phpInfo['curl']['version']:'Unable to find version' }}</span>
						</div>
						

					</div>
				</div>
				
			</div>
</template>
<script setup>
import { ref } from 'vue';

const phpInfo = ref(false);
const gdInfo = ref(false);
const imagickInfo = ref(false);

const tabId = ref(0);


function getGetGdInfo(){
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetGdInfo');

	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			if(res == false || res == 'false'){
				gdInfo.value = false;
				return;
			}
			
			gdInfo.value = res;
			
		})
		.catch(err => {console.log(err) });
	
	const data2 = new FormData();
	data2.append('avife_nonce', avife_nonce);
	data2.append('action', 'ajaxGetImagickInfo');

	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data2
	})
		.then(res => res.json())
		.then(res => {
			if(res == false || res == 'false'){
				imagickInfo.value = false;
				return;
			}
			
			imagickInfo.value = res;
			
		})
		.catch(err => {console.log(err) });
	
	const data3 = new FormData();
	data3.append('avife_nonce', avife_nonce);
	data3.append('action', 'ajaxGetPhpInfo');

	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data3
	})
		.then(res => res.json())
		.then(res => {
			if(res == false || res == 'false'){
				phpInfo.value = false;
				return;
			}
			
			phpInfo.value = res;
			
		})
		.catch(err => {console.log(err) });

}
getGetGdInfo();


</script>
<style scoped>


</style>
