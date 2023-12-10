<template>
	<div class="w-full flex flex-col justify-between items-center  border-b">
		<div class="w-full flex flex-row justify-between items-center p-4">
			<label class="w-full flex justify-start  " for="apiContentArea">{{t('api')}}</label>
			<button id="apiContentArea" @click="showApiContent = !showApiContent">
				<svg v-show="showApiContent == false" xmlns="http://www.w3.org/2000/svg"  fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"> <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" /> </svg>
				<svg v-show="showApiContent == true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"> <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6.75-6.75M12 19.5l-6.75-6.75" /> </svg>

			</button>
		</div>
		<div class="w-full p-4 pt-0" v-show="showApiContent == true">
			
		</div>
				
	</div>
</template>
<script setup>
import { useToast } from 'vue-toastification';
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';

const { t } = useI18n({});
	
const showApiContent = ref(false);
</script>