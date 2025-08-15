<template>
    <div class="w-full flex flex-row justify-between items-center p-4 border-b"
        :class="lazyLoadMode == 'js' ? 'border-b-0' : ''">
        <label class="w-1/2 flex flex-col justify-start" for="lazyloadmode">
            <span class="flex flex-row items-center">
                Lazy Loading
                <span class="relative">
                    <svg @click="showToolTip = !showToolTip" xmlns="http://www.w3.org/2000/svg"  fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-1 w-4 inline cursor-pointer">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <span
                        class="absolute w-[350px] z-10 top-1/2 -translate-y-1/2 backdrop-blur-md border  rounded-md shadow-md text-xs "
                        :class="showToolTip == true ? 'visible' : 'hidden'">
                        <div class="title p-4 border-b flex flex-row justify-between items-center">
                            <span class="font-semibold ">Lazy Loading</span>
                            <svg @click="showToolTip = !showToolTip" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 cursor-pointer"> <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /> </svg>

                        </div>
                        <div class="content p-4 prose">
                            <ul class="">
                                <li>
                                    <p >HTML - Adds a simple loading="lazy" attribute to &lt;img&gt; and &lt;iframe&gt; tag.</p>
                                </li>
                                <li>
                                    <p >JavaScript - Removes the src/source from &lt;iframe&gt;, &lt;img&gt;, &lt;video&gt;, and &lt;audio&gt; elements, replacing them with data-src, and restores them when they become visible in the viewport. Also lazy loads inline background images - experimental </p>
                                </li>
                            </ul>



                        </div>

                    </span>
                </span>
            </span>


        </label>
        <div class="w-full md:w-1/2 flex justify-start md:justify-end">
            <select id="lazyloadmode" class="w-full md:w-auto" v-model="lazyLoadMode" v-on:change="setLazyLoadMode">
                <option value="off">{{ $t('Inactive') }}</option>
                <option value="html">{{ $t('Html') }}</option>
                <option value="js">{{ $t('Java Script') }}</option>
            </select>
        </div>
    </div>
    <div class="w-full flex flex-col items-start p-4 pt-0 border-b" v-if="lazyLoadMode === 'js'">
        <div class="border  rounded  w-full">

            <RootMargin/>

            <Threshold/>

        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue';
import RootMargin from './lazyloading/jslazyloading/RootMargin.vue';
import Threshold from './lazyloading/jslazyloading/Threshold.vue';
const lazyLoadMode = ref(false);


const showToolTip = ref(false);

const getLazyLoad = () => {
    const data = new FormData();
    data.append('avife_nonce', avife_nonce);
    data.append('action', 'ajaxGetLazyLoad');

    fetch(avife_ajax_path, {
        method: 'POST',
        credentials: 'same-origin',
        body: data
    })
        .then(res => res.json())
        .then(res => {
            lazyLoadMode.value = res;
        })
        .catch(err => { console.log(err) });
}
getLazyLoad();

const setLazyLoadMode = () => {
    const data = new FormData();
    data.append('avife_nonce', avife_nonce);
    data.append('action', 'ajaxSetLazyLoad');
    data.append('aviflazyload', lazyLoadMode.value);

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
