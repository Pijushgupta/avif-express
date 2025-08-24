<template>
    <div class="w-full flex flex-row justify-between items-center p-4">
        <label class="w-1/2 flex justify-start">Automatically scan Directory</label>
        <div class="w-1/2 flex justify-end items-center">
            
            <select id="cronjob" class="w-full md:w-auto" v-model="events" v-on:change="setBackgroundConvEvent">
                <option value="hourly">Hourly</option>
                <option value="twicedaily">Twice daily</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
            </select>
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue';
const events = ref('hourly');

function getBackgroundConvEvent() {
    const data = new FormData();
    data.append('avife_nonce', avife_nonce);
    data.append('action', 'ajaxGetBackgroundConvEvent');

    fetch(avife_ajax_path, {
        method: 'POST',
        credentials: 'same-origin',
        body: data
    })
        .then(res => res.json())
        .then(res => {
            events.value = res;

        })
        .catch(err => { console.log(err) });

}
getBackgroundConvEvent();

function setBackgroundConvEvent() {
    const data = new FormData();
    data.append('avife_nonce', avife_nonce);
    data.append('action', 'ajaxSetBackgroundConvEvent');
    data.append('avifbackgroundevents', events.value);

    fetch(avife_ajax_path, {
        method: 'POST',
        credentials: 'same-origin',
        body: data
    })
        .then(res => res.json())
        .then(res => {

            //console.log(res);
        })
        .catch(err => { console.log(err) });
}
</script>