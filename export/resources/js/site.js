import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import slider from './slider';

Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.data('slider', slider);

window.Alpine = Alpine;
Alpine.start();
