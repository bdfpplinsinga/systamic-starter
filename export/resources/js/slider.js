/**
 * Alpine-component voor <x-slider>. Swiper wordt pas geladen als er een slider op de pagina staat.
 */
export default (options = {}) => ({
    swiper: null,

    async init() {
        const [{ default: Swiper }, { A11y, Keyboard, Navigation, Pagination }] = await Promise.all([
            import('swiper'),
            import('swiper/modules'),
            import('swiper/css'),
        ]);

        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const perView = Number(options.perView) || 1;

        this.swiper = new Swiper(this.$refs.swiper, {
            modules: [A11y, Keyboard, Navigation, Pagination],
            slidesPerView: 1,
            spaceBetween: 16,
            loop: Boolean(options.loop),
            speed: reducedMotion ? 0 : 400,
            breakpoints: {
                768: { slidesPerView: Math.min(perView, 2), spaceBetween: 24 },
                1024: { slidesPerView: perView, spaceBetween: 32 },
            },
            keyboard: { enabled: true, onlyInViewport: true },
            navigation: { prevEl: this.$refs.prev, nextEl: this.$refs.next },
            pagination: {
                el: this.$refs.pagination,
                clickable: true,
                bulletClass: 'slider-bullet',
                bulletActiveClass: 'slider-bullet-active',
            },
            a11y: {
                prevSlideMessage: 'Vorige slide',
                nextSlideMessage: 'Volgende slide',
                firstSlideMessage: 'Dit is de eerste slide',
                lastSlideMessage: 'Dit is de laatste slide',
                paginationBulletMessage: 'Ga naar slide {{index}}',
                slideLabelMessage: '{{index}} van {{slidesLength}}',
            },
        });
    },

    destroy() {
        this.swiper?.destroy();
    },
});
