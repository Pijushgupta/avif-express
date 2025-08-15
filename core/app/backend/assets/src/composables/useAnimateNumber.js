import { ref } from 'vue';

export function useAnimateNumber(initialValue = 0) {
  const value = ref(initialValue);

  const easeInOutCubic = (t) => {
    return t < 0.5
      ? 4 * t * t * t
      : 1 - Math.pow(-2 * t + 2, 3) / 2;
  };

  const animateTo = (target, duration = 600) => {
    const start = value.value;
    const change = target - start;
    const startTime = performance.now();

    const step = (now) => {
      const elapsed = now - startTime;
      const rawProgress = Math.min(elapsed / duration, 1);
      const easedProgress = easeInOutCubic(rawProgress);

      value.value = start + change * easedProgress;

      if (rawProgress < 1) {
        requestAnimationFrame(step);
      }
    };

    requestAnimationFrame(step);
  };

  return { value, animateTo };
}
