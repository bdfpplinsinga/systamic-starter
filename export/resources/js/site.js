import $ from 'jquery';
import Alpine from 'alpinejs';
import barba from '@barba/core';
import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
gsap.registerPlugin(ScrollTrigger);

// ********************************
// Global variables
// ********************************
let lenis;

// ********************************
// Alpine starten
// ********************************
function initializeAlpine() {
    window.Alpine = Alpine;
    Alpine.start();
}

// ********************************
// Lenis starten
// ********************************
function initializeLenis() {
    lenis = new Lenis();

    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);

    // Sync ScrollTrigger with Lenis
    lenis.on('scroll', ScrollTrigger.update);
}

// ********************************
// init Page Loader
// ********************************
function initPageLoader() {
    let tl = gsap.timeline();

    tl.set('html', {
        cursor: 'wait',
    });

    tl.to(
        '.loading-screen',
        {
            duration: 0.8,
            autoAlpha: 0,
            ease: 'power2.inOut',
        },
        1
    );

    tl.to(
        '.loading-screen svg',
        {
            yPercent: -100,
            opacity: 0,
            rotate: 0.001,
            duration: 1.2,
            ease: 'Expo.easeInOut',
        },
        0.2
    );

    tl.set('html', {
        cursor: 'auto',
    });
}

// ********************************
// Barba.js setup
// ********************************
barba.init({
    transitions: [
        {
            name: 'default',
            once(data) {
                initPageLoader();
            },
            leave(data) {
                return gsap.to(data.current.container, { opacity: 0 });
            },
            enter(data) {
                return gsap.from(data.next.container, { opacity: 0 });
            },
        },
    ],
});

// ********************************
// Delay function
// ********************************
function delay(n) {
    n = n || 2000;
    return new Promise((done) => {
        setTimeout(() => {
            done();
        }, n);
    });
}

// ********************************
// Check scroll direction
// ********************************
function initScrollCheck() {
    let lastScrollTop = 0;
    let threshold = 50;
    let thresholdTop = 50;

    function checkScroll() {
        lenis.on('scroll', (e) => {
            var nowScrollTop = e.targetScroll;

            if (Math.abs(lastScrollTop - nowScrollTop) >= threshold) {
                // Check Scroll Direction
                if (nowScrollTop > lastScrollTop) {
                    $('[data-scroll-direction]').attr('data-scroll-direction', 'down');
                } else {
                    $('[data-scroll-direction]').attr('data-scroll-direction', 'up');
                }

                lastScrollTop = nowScrollTop;

                // Check if Scroll Started
                if (nowScrollTop > thresholdTop) {
                    $('[data-scroll-started]').attr('data-scroll-started', 'true');
                } else {
                    $('[data-scroll-started]').attr('data-scroll-started', 'false');
                }
            }
        });
    }

    checkScroll();

    // Reset instance
    barba.hooks.after(() => {
        checkScroll();
    });
}

// ********************************
// Master initialization
// ********************************
const initMaster = () => {
    initializeAlpine();
    initializeLenis();
    initScrollCheck();
};

// ********************************
// Initialize the master function on DOM ready
// ********************************
$(function () {
    initMaster();
});
