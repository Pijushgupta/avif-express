<template>
    <div class="  flex flex-row justify-between items-center p-4 ">
                <label class="w-1/2 flex flex-col justify-start" for="threshold">
                    <span class="flex flex-row items-center">
                Threshold
                <span class="relative">
                    <svg @click="showToolTip = !showToolTip" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        class="ml-1 w-4 inline cursor-pointer">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>

                    <span
                        class="absolute w-[350px] z-10 top-1/2 -translate-y-1/2 backdrop-blur-md border  rounded-md shadow-md text-xs "
                        :class="showToolTip == true ? 'visible' : 'hidden'">
                        <div class="title p-4 border-b flex flex-row justify-between items-center">
                            <span class="font-semibold ">Threshold</span>
                            <svg @click="showToolTip = !showToolTip" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 cursor-pointer">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>

                        </div>
                        <div class="content p-4 prose">
                            <p>When to Load — Set how much of the media should be visible before it loads. 0% = start loading immediately when seen, 100% = wait until fully visible.</p>
                        </div>

                    </span>
                </span>
            </span>
                    
                </label>
                <div class="w-1/2 flex justify-end">
                    <input id="threshold" type="range" min="0" max="1" step="0.1" name="" v-model="threshold"
                        class="w-full mr-2" v-on:mouseup="setThreshold" />
                    <span class="w-1/12 flex justify-end">{{ (threshold * 100).toFixed(0) }}%</span>
                </div>
            </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAnimateNumber } from '../../../../composables/useAnimateNumber';
const showToolTip = ref(false);

const { value: threshold, animateTo } = useAnimateNumber();


const getThreshold = () => {
    const data = new FormData();
    data.append('avife_nonce', avife_nonce);
    data.append('action', 'ajaxGetLazyLoadJsThreshold');

    fetch(avife_ajax_path, {
        method: 'POST',
        credentials: 'same-origin',
        body: data
    })
        .then(res => res.json())
        .then(res => {
           animateTo(parseFloat(res)); // animate instead of instant change
            //threshold.value = res;
        })
        .catch(err => { console.log(err) });
}

getThreshold();

const setThreshold = () => {
    const data = new FormData();
    data.append('avife_nonce', avife_nonce);
    data.append('action', 'ajaxSetLazyLoadJsThreshold');
    data.append('aviflazyloadjsthreshold', threshold.value);

    fetch(avife_ajax_path, {
        method: 'POST',
        credentials: 'same-origin',
        body: data
    })
        .then(res => res.json())
        .then(res => { })
        .catch(err => console.log(err));
}
</script>