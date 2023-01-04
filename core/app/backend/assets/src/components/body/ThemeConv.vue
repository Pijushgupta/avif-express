<template>
<div class="w-full flex flex-row justify-between items-center p-4 border-b">
				<label class="w-1/2 flex justify-start" for="bulkcnvtbtn">Theme:</label>
				<div>

				</div>
</div>	
</template>
<script setup>

function getTheme() {
	const data = new FormData();
	data.append('avife_nonce', avife_nonce);
	data.append('action', 'ajaxGetCurrentTheme');
	fetch(avife_ajax_path, {
		method: 'POST',
		credentials: 'same-origin',
		body:data
	})
		.then(res => res.json())
		.then(res => {
			console.log(res);
		})
		.catch(err => console.log(err));
}
getTheme();

</script>