<template>
	<div class="bg-white border border-t-0 rounded-b-2xl">
		<div class="flex flex-col  relative">
			<div v-if="hasImagickstatus != '1' && avifsupportstatus != '1' " class="p-4 bg-red-700 text-white flex flex-col md:flex-row justify-between items-center">
				<span class="text-center md:text-left">Php GD with libavif or Imagick(ver >= 7.0.25) is not installed/enabled. Please contact the server admin!</span>
				<a href="https://www.google.com/search?client=firefox-b-d&q=php+gd+extension" target="_blank" class="border border-2 border-white rounded-full px-4 py-2 hover:text-white visited:text-white focus:shadow-none mt-3 md:mt-0" >Know More</a>
			</div>
			<div class="absolute inset-0" v-if="toWait.isWaiting == true"  >
				<div class="flex w-full h-full justify-center items-center backdrop-blur-sm rounded-b-2xl">
					<svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="64px" height="64px" viewBox="0 0 128 128" xml:space="preserve"><g><path d="M75.4 126.63a11.43 11.43 0 0 1-2.1-22.65 40.9 40.9 0 0 0 30.5-30.6 11.4 11.4 0 1 1 22.27 4.87h.02a63.77 63.77 0 0 1-47.8 48.05v-.02a11.38 11.38 0 0 1-2.93.37z" fill="#387cef"/><animateTransform attributeName="transform" type="rotate" from="0 64 64" to="360 64 64" dur="1800ms" repeatCount="indefinite"></animateTransform></g></svg>
				</div>
			</div>
			<CloudSwitcher/>
			<AutoConvStatus/>
			<OperationMode/>
			<ImageQuality />
			<ImageCompression/>
			<BulkConv/>
			<ThemeConvVue/>
			
			
		</div>
	</div>
	
</template>
<script setup>
import { waitingSatus } from '../../stores/state';
import AutoConvStatus from './body/AutoConvStatus.vue';
import OperationMode from './body/OperationMode.vue';
import ImageQuality from './body/ImageQuality.vue';
import ImageCompression from './body/ImageCompression.vue';
import BulkConv from './body/BulkConv.vue';
import ThemeConvVue from './body/ThemeConv.vue';
import CloudSwitcher from './body/CloudSwitcher.vue';

const toWait = waitingSatus();


const gdstatus = gd;
const avifsupportstatus = avifsupport;
const hasImagickstatus  = hasImagick;

</script>