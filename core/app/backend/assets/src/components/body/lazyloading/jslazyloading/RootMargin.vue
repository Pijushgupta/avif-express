<template>
    <div class="  flex flex-row justify-between items-center  p-4 border-b">
        <label class="w-1/2 flex flex-col justify-start" for="rootmargin">
            <span class="flex flex-row items-center">
                Root Margin
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
                            <span class="font-semibold ">Root Margin</span>
                            <svg @click="showToolTip = !showToolTip" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 cursor-pointer">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>

                        </div>
                        <div class="content p-4 prose">
                            <p>Preload Distance — Load media early by extending the detection area outside the viewport.
                                Example: 0px 0px 200px 0px loads items 200px before they appear at the bottom of the
                                screen. Order: top right bottom left.</p>
                        </div>

                    </span>
                </span>
            </span>

        </label>
        <div class="w-1/2 flex justify-end">
            <input id="rootmargin" type="range" min="0" max="2048" name="" v-model.number="rootMargin"
                class="w-full mr-2" v-on:mouseup="setRootMargin" />
            <span class="w-1/12 flex justify-end">{{ (rootMargin).toFixed(0) }}px</span>
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue';

import { useAnimateNumber } from '../../../../composables/useAnimateNumber';
const showToolTip = ref(false);
const { value: rootMargin, animateTo } = useAnimateNumber();

const getRootMargin = () => {
    const data = new FormData();
    data.append('avife_nonce', avife_nonce);
    data.append('action', 'ajaxGetLazyLoadJsRootMargin');

    fetch(avife_ajax_path, {
        method: 'POST',
        credentials: 'same-origin',
        body: data
    })
        .then(res => res.json())
        .then(res => {
            animateTo(parseFloat(res));
            //rootMargin.value = res;
        })
        .catch(err => { console.log(err) });
}

getRootMargin();

const setRootMargin = () => {
    const data = new FormData();
    data.append('avife_nonce', avife_nonce);
    data.append('action', 'ajaxSetLazyLoadJsRootMargin');
    data.append('aviflazyloadrootmargin', rootMargin.value);

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