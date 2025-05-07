<template>
	<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex flex-col justify-start" for="bulkcnvtbtn">
                    <span>Enable lazy Loading</span>
                </label>
				<div class="w-1/2 flex justify-end">
					<div class="w-1/2 flex justify-end"><input id="lazyload" v-model="enableLazyLoad" v-on:click="setLazyLoad" type="checkbox" name="lazyload"/></div>
				</div>
			</div>
</template>
<script setup>
import {ref} from 'vue';
const enableLazyLoad = ref(false);

const getLazyLoad = () =>{
    const data = new FormData();
    data.append('avife_nonce',avife_nonce);
    data.append('action','ajaxGetLazyLoad');

    fetch(avife_ajax_path,{
        method: 'POST',
        credentials: 'same-origin',
        body:data
    })
        .then(res => res.json())
        .then(res => {
            enableLazyLoad.value  = res;
        })
        .catch(err => { console.log(err)});
}

getLazyLoad();

const setLazyLoad = () =>{
    const data = new FormData();
    data.append('avife_nonce',avife_nonce);
    data.append('action','ajaxSetLazyLoad');

    fetch(avife_ajax_path,{
        method: 'POST',
        credentials: 'same-origin',
        body: data
    })
    .then(res => res.json())
    .then(res => {})
    .catch(err => console.log(err));

}
</script>

