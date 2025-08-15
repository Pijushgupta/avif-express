<template>
	<div class="bg-white border border-t-0 rounded-b-2xl overflow-hidden avif-body">
		<div class="flex flex-col  relative">
			<div v-if="hasImagickstatus != '1' && avifsupportstatus != '1' && isEnabledCloud !='1'" class="p-4 bg-gradient-to-r from-red-800 via-red-500 to-red-700 text-white flex flex-col  justify-between   ">
        <span class="text-left">The PHP GD extension with libavif support or the Imagick extension with AVIF support is not installed or enabled for <strong>Local Conversion</strong>.</span>
        <div class="group relative ">
        <span class="text-left">
          <a class="hover:text-white underline" href="https://rapidapi.com/aavoya-aavoya-default/api/avif-express" target="_blank"><b>Consider using our cloud engine, which offers 500 free image conversions each month.</b></a>
        </span>
			   <div class="invisible group-hover:visible absolute z-10 backdrop-blur-md  p-2 rounded-md shadow-md text-xs w-full">
            <div class=" flex md:flex-row flex-col">
              <div class="md:w-1/4 w-full">
                <div class="shadow rounded-md p-4 m-2 text-gray-500 backdrop-blur-md">
                <h2 class="font-medium">Basic</h2>
                <span class="text-lg mb-2">$0.00 <span class="text-sm">/mo</span></span>
                <span class="block mb-1 font-medium">Request</span>
                <span class="flex flex-row justify-between mb-2">
                  <span>500/Month</span>
                  <span>Hard Limit</span>
                </span>
                <div class="flex flex-row justify-center items-center">
                  <a href="https://rapidapi.com/aavoya-aavoya-default/api/avif-express/pricing" target="_blank" class="bg-blue-600 rounded-full px-4 py-1 !text-white">Choose</a>

                </div>
                </div>
              </div>
              <div class="md:w-1/4 w-full">
                <div class="shadow rounded-md p-4 m-2 text-gray-500 backdrop-blur-md">
                  <h2 class="font-medium">Pro</h2>
                  <span class="text-lg mb-2">$5.00 <span class="text-sm">/mo</span></span>
                  <span class="block mb-1 font-medium">Request</span>
                  <span class="flex flex-row justify-between mb-2">
                  <span>5000/Month</span>
                  <span>Hard Limit</span>
                </span>
                  <div class="flex flex-row justify-center items-center">
                    <a href="https://rapidapi.com/aavoya-aavoya-default/api/avif-express/pricing" target="_blank" class="bg-blue-600 rounded-full px-4 py-1 !text-white">Choose</a>

                  </div>
                </div>
              </div>
              <div class="md:w-1/4 w-full">
                <div class="shadow rounded-md p-4 m-2 text-gray-500 backdrop-blur-md">
                  <h2 class="font-medium">Ultimate</h2>
                  <span class="text-lg mb-2">$9.00 <span class="text-sm">/mo</span></span>
                  <span class="block mb-1 font-medium">Request</span>
                  <span class="flex flex-row justify-between mb-2">
                  <span>10000/Month</span>
                  <span>Hard Limit</span>
                </span>
                  <div class="flex flex-row justify-center items-center">
                    <a href="https://rapidapi.com/aavoya-aavoya-default/api/avif-express/pricing" target="_blank" class="bg-blue-600 rounded-full px-4 py-1 !text-white">Choose</a>

                  </div>
                </div>
              </div>
              <div class="md:w-1/4 w-full">
                <div class="shadow rounded-md p-4 m-2 text-gray-500 backdrop-blur-md">
                  <h2 class="font-medium">Mega</h2>
                  <span class="text-lg mb-2">$17.00 <span class="text-sm">/mo</span></span>
                  <span class="block mb-1 font-medium">Request</span>
                  <span class="flex flex-row justify-between mb-2">
                  <span>20000/Month</span>
                  <span>Hard Limit</span>
                </span>
                  <div class="flex flex-row justify-center items-center">
                    <a href="https://rapidapi.com/aavoya-aavoya-default/api/avif-express/pricing" target="_blank" class="bg-blue-600 rounded-full px-4 py-1 !text-white">Choose</a>

                  </div>
                </div>
              </div>


            </div>

         </div>
        </div>
      </div>
			<div class="relative control-area flex flex-row w-full">
				<div class="border-r flex flex-col p-2">
					<span class="pb-2 first:pt-0 last:pb-0 cursor-pointer group relative" @click="tab = 0">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 border p-2 rounded-lg " v-bind:class="tab == 0 ? 'bg-gradient-to-r from-blue-800 to-blue-500 stroke-white shadow-md border-0':''"> <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm.75-12h9v9h-9v-9z" /> </svg>
						<div class="invisible group-hover:visible absolute w-max z-10 top-1  left-11 backdrop-blur-md border py-2 px-4 rounded-md shadow-md text-xs"> Image Processing </div>
					</span>
					<span class="pb-2 first:pt-0 last:pb-0  cursor-pointer group relative" @click="tab = 1">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 border p-2 rounded-lg" v-bind:class="tab == 1 ? 'bg-gradient-to-r from-blue-800 to-blue-500 stroke-white shadow-md border-0':''"> <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /> <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /> </svg>
						<div class="invisible group-hover:visible absolute w-max z-10 top-1 left-11 backdrop-blur-md border  py-2 px-4 rounded-md shadow-md text-xs "> Advance Settings </div>
					</span>

				</div>
				<div class=" flex flex-col w-full control-components relative">
					<div class="absolute inset-0 z-50" v-if="toWait.isWaiting == true"  >
						<div class="flex w-full h-full justify-center items-center backdrop-blur-sm rounded-b-2xl">
							<svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="64px" height="64px" viewBox="0 0 128 128" xml:space="preserve"><g><path d="M75.4 126.63a11.43 11.43 0 0 1-2.1-22.65 40.9 40.9 0 0 0 30.5-30.6 11.4 11.4 0 1 1 22.27 4.87h.02a63.77 63.77 0 0 1-47.8 48.05v-.02a11.38 11.38 0 0 1-2.93.37z" fill="#387cef"/><animateTransform attributeName="transform" type="rotate" from="0 64 64" to="360 64 64" dur="1800ms" repeatCount="indefinite"></animateTransform></g></svg>
						</div>
					</div>
					<template v-if="tab == 0">
						<CloudSwitcher/>
						<AutoConvStatus/>
						<OperationMode/>
            <LazyLoading/>
						<FallbackSetting/>
						<AvifOnTheFly/>
						<ImageQuality />
						<ImageCompression/>
						<BulkConv/>
						<ThemeConvVue/>
					</template>
					<template v-if="tab == 1">
						<Api/>
						<PhpInformation/>
						<!-- <EnableLogging/> -->
						<!-- <ViewLog/> -->
					</template>
					<template v-if="tab == 2">
						<span>Tab 2</span>
					</template>

				</div>
			</div>



		</div>
	</div>

</template>
<script setup>
import { waitingSatus } from '../../stores/state';
import AutoConvStatus from './body/AutoConvStatus.vue';
import OperationMode from './body/OperationMode.vue';

import FallbackSetting from './body/FallBackSetting.vue';
import ImageQuality from './body/ImageQuality.vue';
import ImageCompression from './body/ImageCompression.vue';
import BulkConv from './body/BulkConv.vue';
import ThemeConvVue from './body/ThemeConv.vue';
import CloudSwitcher from './body/CloudSwitcher.vue';
import AvifOnTheFly from './body/AvifOnTheFly.vue';
import {ref} from 'vue';
import EnableLogging from './body/EnableLogging.vue';
import ViewLog from './body/ViewLog.vue';
import Api from './body/Api.vue';
import PhpInformation from './body/PhpInformation.vue';
import LazyLoading from './body/LazyLoading.vue';

const toWait = waitingSatus();


const gdstatus = gd;
const avifsupportstatus = avifsupport;
const hasImagickstatus  = hasImagick;
const isEnabledCloud = isCloudEngine;

const tab = ref(0);


</script>
