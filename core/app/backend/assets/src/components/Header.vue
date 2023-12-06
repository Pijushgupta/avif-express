<template>
	
		<div class="avife-header flex flex-row justify-start items-center p-4 text-white  rounded-t-xl bg-gradient-to-r  from-blue-800 to-blue-500   min-h-[200px] mt-5">
			<div class="p-3 w-40">
				<img v-bind:src="logo" />
			</div>
			<div class="flex flex-col w-full">
				<h2 class="text-2xl font-semibold text-white mb-3">{{ $t('pluginName') }}</h2>
				<div class="flex flex-col md:flex-row md:justify-between">
					<span>{{ $t('tagline') }}</span>
					<!--<a href="https://www.linkedin.com/in/pijush-gupta-php/" class="bg-white text-blue-800 rounded-full px-4 py-1 max-w-fit mt-5 md:mt-0 " target="_blank">{{$t('hireMe')}}</a>-->
				</div>
			</div>
		
	
	</div>
		
</template>
<script setup>
let logo = assetPath + 'imgs/avif.png';

</script> 