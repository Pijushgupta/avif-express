<template>
	<div class="bg-white border border-t-0 rounded-b-2xl">
		<div class="flex flex-col  ">
			<div v-if="gdstatus != '1' && gdstatus != 1 " class="p-4 bg-red-700 text-white flex flex-col md:flex-row justify-between items-center">
				<span class="text-center md:text-left">Php GD Extention is not installed/enabled. Please contact the server admin!</span>
				<a href="https://www.google.com/search?client=firefox-b-d&q=php+gd+extension" target="_blank" class="border border-2 border-white rounded-full px-4 py-2 hover:text-white visited:text-white focus:shadow-none mt-3 md:mt-0" >Know More</a>
			</div>
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
import AutoConvStatus from './body/AutoConvStatus.vue';
import OperationMode from './body/OperationMode.vue';
import ImageQuality from './body/ImageQuality.vue';
import ImageCompression from './body/ImageCompression.vue';
import BulkConv from './body/BulkConv.vue';
import ThemeConvVue from './body/ThemeConv.vue';


const gdstatus = gd;
console.log(gdstatus);
</script>