<template>
	<div class="w-full flex flex-col justify-between items-center  border-b">
		<div class="w-full flex flex-row justify-between items-center p-4">
			<label class="w-full flex justify-start  " for="phpinfoarea">{{t('phpInformation')}}</label>
			<button id="phpinfoarea" @click="showPhpInfo = !showPhpInfo">
				<svg v-show="showPhpInfo == false" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"> <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /> </svg>
				<svg v-show="showPhpInfo == true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"> <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /> </svg>

			</button>
		</div>
		<div class="w-full p-4 pt-0" v-show="showPhpInfo == true">
			<div class="w-full flex flex-col justify-end mt-2" v-if="gdInfo != false">
				<div class="flex flex-row justify-between items-center" v-if="'GD Version' in gdInfo">
					<span>GD Version</span>
					<span>{{ gdInfo['GD Version'] }}</span>
				</div>
				<div class="flex flex-row justify-between items-center" v-if="'AVIF Support' in gdInfo">
					<span>AVIF Support</span>
					<span>{{ gdInfo['AVIF Support'] == false ? t('no'):t('yes') }}</span>
				</div>
				<div class="flex flex-row justify-between items-center" v-if="'WBMP Support' in gdInfo">
					<span>WEBP Support</span>
					<span>{{ gdInfo['WBMP Support'] == false ? t('no'):t('yes') }}</span>
				</div>

			</div>
			<div class="w-full flex flex-col justify-end mt-2" v-if="imagickInfo != false">
				<div class="flex flex-row justify-between items-center" v-if="imagickInfo['version']['versionString']">
					<span>Imagick Version</span>
					<span>{{ imagickInfo['version']['versionString'] }}</span>
				</div>
				<div class="flex flex-row justify-between items-center" >
					<span>AVIF Support</span>
					<span>{{ imagickInfo['formats'].indexOf('AVIF') !== -1 ? t('yes'):t('no') }}</span>
				</div>
				<div class="flex flex-row justify-between items-center" v-if="'WBMP Support' in gdInfo">
					<span>WEBP Support</span>
					<span>{{ imagickInfo['formats'].indexOf('WEBP') !== -1 ? t('yes'):t('no') }}</span>
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
import { useI18n } from 'vue-i18n';

const { t } = useI18n({});
const phpInfo = ref(false);
const gdInfo = ref(false);
const imagickInfo = ref(false);
const showPhpInfo = ref(false);

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

