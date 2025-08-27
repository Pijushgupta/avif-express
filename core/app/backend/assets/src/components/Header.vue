<template>
	<div class="px-2 pt-2 mt-5 border border-b-0 rounded-t-2xl">
		
		<!-- Header with canvas game background -->
		<div class="avife-header relative flex flex-row justify-start items-center p-4 text-white rounded-t-xl bg-gradient-to-r from-blue-800 to-blue-500 min-h-[200px] overflow-hidden">
			
			<!-- Background canvas -->
			<canvas id="petGame" class="absolute inset-0 w-full h-full z-0 opacity-40"></canvas>
			
			<!-- Foreground content -->
			<div class="p-3 w-40 relative z-10">
				<img v-bind:src="logo" />
			</div>
			<div class="flex flex-col w-full relative z-10">
				<h2 class="text-2xl font-semibold text-white mb-3">{{ $t('pluginName') }}</h2>
				<div class="flex flex-col md:flex-row md:justify-between">
					<span>{{ $t('tagline') }}</span>
					<a href="https://wordpress.org/support/plugin/avif-express/reviews/" 
					   class="bg-gradient-to-r from-blue-500 to-blue-800 !text-white rounded-full px-4 py-2 max-w-fit mt-5 md:mt-0 shadow text-xs" 
					   target="_blank">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 inline mr-1">
							<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
						</svg>
						Submit Your Review
					</a>
				</div>
			</div>
		</div>
		
		<!-- Banner message -->
		<div class="bg-blue-500 text-white flex flex-row rounded-b-xl z-10 relative">
			<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 m-4">
				<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
			</svg>
			<span class="p-4">
				Unfortunately, I’ve had to temporarily discontinue the <strong>cloud image conversion</strong> feature because of high server costs and insufficient funding. I understand this may affect your workflow, and I sincerely apologize.
			</span>
		</div>
	</div>
</template>

<script setup>
import { onMounted } from 'vue'

let logo = assetPath + 'imgs/avif.png';

onMounted(() => {
	const canvas = document.getElementById("petGame");
	const ctx = canvas.getContext("2d");

	let w = canvas.width = canvas.offsetWidth;
	let h = canvas.height = canvas.offsetHeight;

	// 🎈 Balloons
	let balloons = [];
	const colors = ["#ff4d4f", "#40a9ff", "#ffd666", "#73d13d", "#9254de", "#ff85c0", "#ffa940"];

	for (let i = 0; i < 15; i++) {
		balloons.push({
			x: Math.random() * w,
			y: Math.random() * h,
			dx: (Math.random() - 0.5) * 1.5, // drift horizontally
			dy: (Math.random() - 0.5) * 1.5, // drift vertically
			radius: 15 + Math.random() * 15,
			color: colors[Math.floor(Math.random() * colors.length)]
		});
	}

	function drawBalloon(b) {
		// body
		ctx.beginPath();
		ctx.arc(b.x, b.y, b.radius, 0, Math.PI * 2);
		ctx.fillStyle = b.color;
		ctx.fill();
		ctx.closePath();

		// highlight
		ctx.beginPath();
		ctx.arc(b.x - b.radius / 3, b.y - b.radius / 3, b.radius / 5, 0, Math.PI * 2);
		ctx.fillStyle = "rgba(255,255,255,0.6)";
		ctx.fill();
		ctx.closePath();

		// string
		ctx.beginPath();
		ctx.moveTo(b.x, b.y + b.radius);
		ctx.lineTo(b.x, b.y + b.radius + 20);
		ctx.strokeStyle = "rgba(0,0,0,0.4)";
		ctx.stroke();
	}

	function update() {
		ctx.clearRect(0, 0, w, h);

		balloons.forEach(b => {
			// move
			b.x += b.dx;
			b.y += b.dy;

			// bounce from edges
			if (b.x - b.radius < 0 || b.x + b.radius > w) b.dx *= -1;
			if (b.y - b.radius < 0 || b.y + b.radius > h) b.dy *= -1;

			drawBalloon(b);
		});

		requestAnimationFrame(update);
	}

	update();

	// 🔄 resize handler
	window.addEventListener("resize", () => {
		w = canvas.width = canvas.offsetWidth;
		h = canvas.height = canvas.offsetHeight;
	});
});
</script>

