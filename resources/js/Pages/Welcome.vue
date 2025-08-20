<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
// Lazy load heavy carousel + below-the-fold sections to reduce main thread work for initial (hero) paint / LCP
import { defineAsyncComponent, onMounted, nextTick, ref } from 'vue';
import { Head, router, Link } from '@inertiajs/vue3';

const RaffleCarousel = defineAsyncComponent(() => import('@/Components/RaffleCarousel.vue'));

// Flags to mount below-the-fold content & deferred decorative hero animations
// Ursprünglich false -> führte dazu, dass nur Hero sichtbar war wenn Aktivierung ausblieb.
const showBelowFold = ref(true); // Sofort sichtbar
const showDecor = ref(true);

const route = window.route;

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    activeRaffles: {
        type: Array,
        default: () => []
    },
    bunny: {
        type: Object,
        default: () => ({})
    },
});

// Site / SEO config
const site = {
        name: 'MystiDraw',
        baseUrl: typeof window !== 'undefined' ? window.location.origin : 'https://mystidraw.com',
        logo: '/images/logo.webp',
        og: '/images/og/home-1200x630.jpg', // ensure this exists (1200x630)
        twitter: '@MystiDraw'
};

const seo = {
        title: 'MystiDraw – Lose ziehen, sofort gewinnen (keine Nieten)',
        desc: 'MystiDraw ist die moderne Raffle-Plattform für Anime, Gaming & mehr. Ziehe ein Los und erhalte sofort deinen Gewinn – 100% Gewinnchance, schneller Versand aus Deutschland.',
        canonical: site.baseUrl + '/'
};

// Structured Data objects
const orgLd = {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: site.name,
    url: site.baseUrl,
    logo: site.baseUrl + site.logo,
    contactPoint: [{
        '@type': 'ContactPoint',
        contactType: 'customer support',
        email: 'contact@mystidraw.com',
        availableLanguage: ['de']
    }]
};

const webSiteLd = {
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    name: site.name,
    url: site.baseUrl,
    inLanguage: 'de-DE'
};

const howToLd = {
    '@context': 'https://schema.org',
    '@type': 'HowTo',
    name: 'So funktioniert MystiDraw',
    description: 'In 3 Schritten zum garantierten Gewinn.',
    step: [
        { '@type': 'HowToStep', name: 'Los ziehen', text: 'Wähle ein Raffle aus Kategorien wie Anime, Gaming oder Lifestyle.', url: site.baseUrl + '/#how-it-works' },
        { '@type': 'HowToStep', name: 'Sofort gewinnen', text: 'Nach dem Ziehen siehst du sofort deinen Gewinn. Keine Nieten.', url: site.baseUrl + '/#how-it-works' },
        { '@type': 'HowToStep', name: 'Erhalten & Versenden', text: 'Gewinne im Inventar sammeln und für 7€ aus Deutschland versenden lassen.', url: site.baseUrl + '/#how-it-works' }
    ],
    totalTime: 'PT1M'
};

const faqLd = {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: [
        { '@type': 'Question', name: 'Wie funktioniert das Inventar-System?', acceptedAnswer: { '@type': 'Answer', text: 'Du sammelst deine Gewinne in deinem digitalen Inventar und kannst sie später als Paket versenden lassen.' } },
        { '@type': 'Question', name: 'Wann erhalte ich meinen Gewinn?', acceptedAnswer: { '@type': 'Answer', text: 'Sofort nach dem Ziehen erfährst du deinen Gewinn. Der Versand erfolgt auf Anfrage aus deinem Inventar.' } },
        { '@type': 'Question', name: 'Was kostet der Versand?', acceptedAnswer: { '@type': 'Answer', text: 'Der Versand kostet 7€ und erfolgt schnell und zuverlässig aus Deutschland.' } }
    ]
};

// JSON-LD will be injected manually (no SFC <script> tag inside template / Head component restrictions)

// Smooth scroll zu Abschnitten
const scrollToSection = (sectionId) => {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
};

const handleRaffleSelect = (raffle) => {
    // Navigation zur Raffle Detail-Seite
    router.get(route('raffles.show', raffle.slug));
};

// Handle URL fragment on page load
onMounted(() => {
    nextTick(() => {
        const hash = window.location.hash.substring(1);
        if (hash) {
            setTimeout(() => scrollToSection(hash), 80);
        }
    });

    // Defer mounting of below-the-fold sections to after first frame + idle
    requestAnimationFrame(() => {
        const activate = () => {
            showBelowFold.value = true;
            setTimeout(() => { showDecor.value = true; }, 400);
        };
        if ('requestIdleCallback' in window) {
            window.requestIdleCallback(activate, { timeout: 800 });
        } else {
            setTimeout(activate, 600);
        }
    });

    // Prefetch carousel chunk slightly earlier (even before mount) when user scrolls near where it will appear
    const maybePrefetch = () => import('@/Components/RaffleCarousel.vue');
    const rafOnceElId = 'raffles';
    const observeTarget = () => {
        const el = document.getElementById(rafOnceElId);
        if (!el || !('IntersectionObserver' in window)) return;
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    maybePrefetch();
                    obs.disconnect();
                }
            });
        }, { rootMargin: '600px 0px' });
        obs.observe(el);
    };
    // Try a few times until element exists (because it's lazily mounted)
    let tries = 0;
    const interval = setInterval(() => {
        if (showBelowFold.value) {
            observeTarget();
        }
        if (++tries > 10 || (showBelowFold.value && document.getElementById(rafOnceElId))) {
            clearInterval(interval);
        }
    }, 300);

    // Idle fallback
    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(() => maybePrefetch(), { timeout: 2500 });
    } else {
        setTimeout(() => maybePrefetch(), 2500);
    }

    // Inject structured data script once (idempotent)
    const injectJsonLd = (id, data) => {
        if (document.head.querySelector(`script[data-jsonld-id="${id}"]`)) return;
        const s = document.createElement('script');
        s.type = 'application/ld+json';
        s.setAttribute('data-jsonld-id', id);
        s.textContent = JSON.stringify(data);
        document.head.appendChild(s);
    };
    injectJsonLd('home-structured-data', [orgLd, webSiteLd, howToLd, faqLd]);

    // Falls wir später wieder Lazy-Loading wollen, kann hier optionally eine Verzögerung eingebaut werden.
    // Safety-Fallback (falls irgendwann wieder auf false gesetzt würde):
    setTimeout(() => {
        if (!showBelowFold.value) {
            showBelowFold.value = true;
        }
        if (!showDecor.value) {
            showDecor.value = true;
        }
    }, 1500);
});
</script>

<template>
    <MainLayout 
        title="MystiDraw - Lose, Gewinne, Überraschungen" 
        :user="$page.props.auth?.user" 
        :can-login="canLogin" 
        :can-register="canRegister"
    >
                <Head>
                    <title>{{ seo.title }}</title>
                    <meta name="description" :content="seo.desc" />
                    <link rel="canonical" :href="seo.canonical" />
                    <link rel="alternate" hreflang="de-DE" :href="seo.canonical" />
                    <link rel="alternate" hreflang="x-default" :href="seo.canonical" />
                    <!-- Performance Hints -->
                    <link v-if="bunny?.pull_zone" rel="preconnect" :href="`https://${bunny.pull_zone}`" crossorigin>
                    <link v-if="bunny?.pull_zone" rel="dns-prefetch" :href="`//${bunny.pull_zone}`">
                    <!-- OG -->
                    <meta property="og:type" content="website" />
                    <meta property="og:site_name" :content="site.name" />
                    <meta property="og:title" :content="seo.title" />
                    <meta property="og:description" :content="seo.desc" />
                    <meta property="og:url" :content="seo.canonical" />
                        <meta property="og:image" :content="site.og" />
                    <!-- Twitter -->
                    <meta name="twitter:card" content="summary_large_image" />
                    <meta name="twitter:title" :content="seo.title" />
                    <meta name="twitter:description" :content="seo.desc" />
                    <meta name="twitter:image" :content="site.og" />
                    <meta v-if="site.twitter" name="twitter:site" :content="site.twitter" />
                    
                    <!-- JSON-LD injected dynamically in onMounted to satisfy Inertia Head limitations -->
                </Head>
        <!-- Hero Section -->
    <section id="home" class="relative bg-hero-gradient overflow-hidden min-h-screen flex items-center">
            <div class="absolute inset-0 bg-black/10"></div>
            
            <!-- Animated background elements (deferred) -->
            <div v-if="showDecor" class="absolute inset-0 overflow-hidden" aria-hidden="true">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-gold-400/10 rounded-full animate-float"></div>
                <div class="absolute top-1/4 right-1/4 w-32 h-32 bg-gold-400/5 rounded-full animate-bounce"></div>
                <div class="absolute bottom-1/4 left-1/3 w-24 h-24 bg-gold-400/10 rounded-full animate-float" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 right-1/3 w-16 h-16 bg-white/5 rounded-full animate-pulse"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex justify-center mb-6 md:mb-8">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 bg-white/10 rounded-2xl md:rounded-3xl flex items-center justify-center shadow-2xl p-3 md:p-4">
                        <img src="/images/logo.webp" alt="MystiDraw – Logo" width="128" height="128" fetchpriority="high" loading="eager" decoding="async" class="w-full h-full object-contain filter drop-shadow-lg" />
                    </div>
                </div>
                
                <h1 v-once class="text-4xl sm:text-5xl md:text-6xl lg:text-8xl font-bold text-white mb-6 md:mb-8 tracking-tight" style="font-family:system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">
                    Willkommen bei 
                    <span class="bg-gradient-to-r from-gold-400 to-gold-500 bg-clip-text text-transparent">MystiDraw</span>
                </h1>
                <p v-once class="text-lg sm:text-xl md:text-2xl text-navy-200 mb-8 md:mb-12 max-w-4xl mx-auto leading-relaxed">
                    Entdecke unsere aufregende Raffle-Plattform! Ziehe ein Los und erhalte sofort deinen Gewinn - 
                    bei uns gibt es <span class="text-gold-400 font-semibold">keine Nieten</span>, nur Gewinne verschiedener Kategorien.
                </p>
                
                                <div class="flex flex-col sm:flex-row gap-4 md:gap-6 justify-center">
                    <button
                        @click="scrollToSection('raffles')"
                        class="group relative px-8 md:px-10 py-4 md:py-5 bg-gold-gradient text-navy-900 font-bold rounded-full hover:shadow-2xl hover:shadow-gold-500/25 transform hover:scale-105 transition-all duration-300 overflow-hidden text-sm md:text-base"
                        type="button"
                        aria-label="Zu den aktuellen Raffles springen"
                        title="Zu den aktuellen Raffles"
                    >
                        <span class="absolute inset-0 bg-white/20 scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-left"></span>
                        <span class="relative flex items-center justify-center space-x-2">
                            <span>Jetzt Lose ziehen</span>
                            <!-- Inline arrow icon -->
                            <svg aria-hidden="true" class="w-4 h-4 md:w-5 md:h-5 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
                        </span>
                    </button>
                    <button
                        @click="scrollToSection('how-it-works')"
                        class="group px-8 md:px-10 py-4 md:py-5 border-2 border-gold-400 text-gold-400 font-bold rounded-full hover:bg-gold-400 hover:text-navy-900 transform hover:scale-105 transition-all duration-300 text-sm md:text-base"
                        type="button"
                        aria-label="Zum Abschnitt Wie es funktioniert springen"
                        title="Wie es funktioniert"
                    >
                        <span class="flex items-center justify-center space-x-2">
                            <span>Wie es funktioniert</span>
                            <!-- Inline question-circle replacement -->
                            <svg aria-hidden="true" class="w-4 h-4 md:w-5 md:h-5 transition-transform duration-300 group-hover:rotate-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                        </span>
                    </button>
                </div>
                                <!-- Additional internal link for crawlers -->
                                <div class="mt-8">
                                    <Link :href="route('raffles.index')" class="text-gold-300 underline decoration-dotted hover:decoration-solid">Alle Raffles ansehen</Link>
                                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <div class="w-12 h-12 border-2 border-gold-400 rounded-full flex items-center justify-center">
                    <!-- Inline chevron-down -->
                    <svg aria-hidden="true" class="w-6 h-6 text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                </div>
            </div>
        </section>

        <!-- Active Raffles Section -->
        <section v-if="showBelowFold" id="raffles" class="py-16 md:py-20 bg-white" style="content-visibility:auto;contain-intrinsic-size:1200px;" v-once>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 md:mb-16">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-navy-900 mb-4 md:mb-6">Aktuelle Raffles</h2>
                    <p class="text-lg md:text-xl text-navy-600 max-w-3xl mx-auto leading-relaxed">
                        Wähle dein Lieblings-Raffle und ziehe dein Los für tolle Gewinne. <span class="text-gold-500 font-semibold">Jedes Los ist ein Gewinn!</span>
                    </p>
                </div>

                <div v-if="activeRaffles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8" v-once>
                    <RaffleCarousel 
                        v-for="raffle in activeRaffles" 
                        :key="raffle.id"
                        :raffle="raffle"
                        :pull-zone="bunny.pull_zone"
                        @selectRaffle="handleRaffleSelect"
                    />
                </div>

                <div v-else class="text-center py-12 md:py-16" v-once>
                    <div class="w-20 h-20 md:w-24 md:h-24 bg-navy-100 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                        <font-awesome-icon :icon="['fas', 'gift']" class="w-10 h-10 md:w-12 md:h-12 text-navy-600" />
                    </div>
                    <h3 class="text-xl md:text-2xl font-semibold text-navy-900 mb-3 md:mb-4">Bald verfügbar!</h3>
                    <p class="text-navy-600 text-sm md:text-base">Neue spannende Raffles werden bald freigeschaltet. Bleib dran!</p>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
    <section v-if="showBelowFold" id="how-it-works" class="py-16 md:py-20 bg-gradient-to-br from-navy-50 via-white to-gold-50" style="content-visibility:auto;contain-intrinsic-size:1400px;" v-once>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 md:mb-16">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-navy-900 mb-4 md:mb-6">So funktioniert's</h2>
                    <p class="text-lg md:text-xl text-navy-600 max-w-3xl mx-auto leading-relaxed">
                        In nur 3 einfachen Schritten zu deinem <span class="text-gold-500 font-semibold">garantierten Gewinn</span>
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12" v-once>
                    <!-- Schritt 1 -->
                    <div class="text-center group">
                        <div class="relative mb-6 md:mb-8">
                            <div class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 bg-navy-gradient rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto shadow-2xl group-hover:shadow-navy-500/25 transform group-hover:scale-110 transition-all duration-300">
                                <span class="text-white text-2xl sm:text-3xl md:text-4xl font-bold">1</span>
                            </div>
                            <div class="absolute -top-2 -right-2 md:-top-4 md:-right-4 w-8 h-8 md:w-12 md:h-12 bg-gold-gradient rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg animate-glow" aria-hidden="true">
                                <!-- Gift inline icon -->
                                <svg class="w-4 h-4 md:w-6 md:h-6 text-navy-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="8" width="18" height="13" rx="2"/><path d="M16 8v13"/><path d="M8 8v13"/><path d="M3 12h18"/><path d="M12 8s-3.5-.5-3.5-3A2.5 2.5 0 0112 6.5 2.5 2.5 0 0115.5 5c0 2.5-3.5 3-3.5 3z"/></svg>
                            </div>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold text-navy-900 mb-3 md:mb-4">Los ziehen</h3>
                        <p class="text-navy-600 leading-relaxed text-sm md:text-base">Wähle aus verschiedenen Kategorien wie <span class="font-semibold text-gold-600">Anime</span>, <span class="font-semibold text-gold-600">Gaming</span> oder <span class="font-semibold text-gold-600">Lifestyle</span> dein Lieblings-Raffle aus.</p>
                    </div>

                    <!-- Schritt 2 -->
                    <div class="text-center group">
                        <div class="relative mb-6 md:mb-8">
                            <div class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 bg-navy-gradient rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto shadow-2xl group-hover:shadow-navy-500/25 transform group-hover:scale-110 transition-all duration-300">
                                <span class="text-white text-2xl sm:text-3xl md:text-4xl font-bold">2</span>
                            </div>
                            <div class="absolute -top-2 -right-2 md:-top-4 md:-right-4 w-8 h-8 md:w-12 md:h-12 bg-gold-gradient rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg animate-glow" aria-hidden="true">
                                <!-- Bolt inline icon -->
                                <svg class="w-4 h-4 md:w-6 md:h-6 text-navy-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                            </div>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold text-navy-900 mb-3 md:mb-4">Sofort gewinnen</h3>
                        <p class="text-navy-600 leading-relaxed text-sm md:text-base">Nach dem Ziehen erfährst du sofort, was du gewonnen hast! Es gibt <span class="font-semibold text-gold-600">keine Nieten</span> - nur Gewinne verschiedener Preisklassen.</p>
                    </div>

                    <!-- Schritt 3 -->
                    <div class="text-center group">
                        <div class="relative mb-6 md:mb-8">
                            <div class="w-24 h-24 sm:w-28 sm:h-28 md:w-32 md:h-32 bg-navy-gradient rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto shadow-2xl group-hover:shadow-navy-500/25 transform group-hover:scale-110 transition-all duration-300">
                                <span class="text-white text-2xl sm:text-3xl md:text-4xl font-bold">3</span>
                            </div>
                            <div class="absolute -top-2 -right-2 md:-top-4 md:-right-4 w-8 h-8 md:w-12 md:h-12 bg-gold-gradient rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg animate-glow" aria-hidden="true">
                                <!-- Truck / shipping inline icon -->
                                <svg class="w-4 h-4 md:w-6 md:h-6 text-navy-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7h11v8H3z"/><path d="M14 11h4l3 3v1h-7z"/><circle cx="7.5" cy="18.5" r="2.5"/><circle cx="17.5" cy="18.5" r="2.5"/></svg>
                            </div>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold text-navy-900 mb-3 md:mb-4">Erhalten & Versenden</h3>
                        <p class="text-navy-600 leading-relaxed text-sm md:text-base">Deine Gewinne landen in deinem Inventar. Erstelle Pakete und lass sie dir für nur <span class="font-semibold text-gold-600">7€ Versand</span> aus Deutschland zusenden (1-3 Werktage).</p>
                    </div>
                </div>

                <!-- Zusätzliche Info -->
                <div class="mt-16 md:mt-20 relative overflow-hidden">
                    <div class="absolute inset-0 bg-navy-gradient rounded-2xl md:rounded-3xl"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-gold-400/10 to-transparent"></div>
                    <div class="relative p-8 md:p-12 text-center text-white">
                        <div class="max-w-4xl mx-auto">
                            <h3 class="text-2xl md:text-3xl font-bold mb-4 md:mb-6 flex items-center justify-center space-x-2 md:space-x-3">
                                <font-awesome-icon :icon="['fas', 'percentage']" class="text-gold-400 text-xl md:text-2xl" />
                                <span>100% Gewinn garantiert!</span>
                            </h3>
                            <p class="text-navy-100 mb-6 md:mb-8 text-base md:text-lg leading-relaxed">
                                Bei MystiDraw gibt es keine Enttäuschungen. Jedes Los ist ein Gewinn - von kleinen Überraschungen bis zu Premium-Artikeln der Kategorie A.
                            </p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 text-xs md:text-sm">
                                <div class="bg-white/10 px-4 md:px-6 py-3 md:py-4 rounded-full border border-gold-400/30 hover:border-gold-400 transition-colors duration-300 group">
                                    <div class="flex items-center justify-center space-x-1 md:space-x-2">
                                        <font-awesome-icon :icon="['fas', 'star']" class="text-gold-400 group-hover:animate-bounce text-xs md:text-sm" />
                                        <span class="font-semibold">Nur 7€ Versand</span>
                                    </div>
                                </div>
                                <div class="bg-white/10 px-4 md:px-6 py-3 md:py-4 rounded-full border border-gold-400/30 hover:border-gold-400 transition-colors duration-300 group">
                                    <div class="flex items-center justify-center space-x-1 md:space-x-2">
                                        <font-awesome-icon :icon="['fas', 'box']" class="text-gold-400 group-hover:animate-bounce text-xs md:text-sm" />
                                        <span class="font-semibold">Aus Deutschland</span>
                                    </div>
                                </div>
                                <div class="bg-white/10 px-4 md:px-6 py-3 md:py-4 rounded-full border border-gold-400/30 hover:border-gold-400 transition-colors duration-300 group">
                                    <div class="flex items-center justify-center space-x-1 md:space-x-2">
                                        <font-awesome-icon :icon="['fas', 'shipping-fast']" class="text-gold-400 group-hover:animate-bounce text-xs md:text-sm" />
                                        <span class="font-semibold">1-3 Werktage</span>
                                    </div>
                                </div>
                                <div class="bg-white/10 px-4 md:px-6 py-3 md:py-4 rounded-full border border-gold-400/30 hover:border-gold-400 transition-colors duration-300 group">
                                    <div class="flex items-center justify-center space-x-1 md:space-x-2">
                                        <font-awesome-icon :icon="['fas', 'trophy']" class="text-gold-400 group-hover:animate-bounce text-xs md:text-sm" />
                                        <span class="font-semibold">100% Gewinnchance</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
    <section v-if="showBelowFold" class="py-16 md:py-20 bg-white" style="content-visibility:auto;contain-intrinsic-size:1600px;" v-once>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 md:mb-16">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-navy-900 mb-4 md:mb-6">Warum MystiDraw?</h2>
                    <p class="text-lg md:text-xl text-navy-600 max-w-3xl mx-auto leading-relaxed">
                        Das macht uns zur <span class="text-gold-500 font-semibold">besten Wahl</span> für Raffle-Fans
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    <div class="group text-center p-6 md:p-8 rounded-2xl md:rounded-3xl bg-gradient-to-br from-navy-50 via-white to-navy-100 hover:shadow-2xl hover:shadow-navy-200/50 transition-all duration-500 border border-navy-100 hover:border-gold-200">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-navy-gradient rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                            <font-awesome-icon :icon="['fas', 'check-circle']" class="text-gold-400 text-xl md:text-2xl w-8 h-8 md:w-10 md:h-10" />
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-navy-900 mb-3 md:mb-4">100% Gewinngarantie</h3>
                        <p class="text-navy-600 leading-relaxed text-sm md:text-base">Jedes Los ist ein Gewinn. <span class="font-semibold text-gold-600">Keine Enttäuschungen</span>, nur Überraschungen!</p>
                    </div>

                    <div class="group text-center p-6 md:p-8 rounded-2xl md:rounded-3xl bg-gradient-to-br from-emerald-50 via-white to-emerald-100 hover:shadow-2xl hover:shadow-emerald-200/50 transition-all duration-500 border border-emerald-100 hover:border-gold-200">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-emerald-600 to-emerald-500 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                            <font-awesome-icon :icon="['fas', 'dollar-sign']" class="text-white text-xl md:text-2xl w-8 h-8 md:w-10 md:h-10" />
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-navy-900 mb-3 md:mb-4">Günstiger Versand</h3>
                        <p class="text-navy-600 leading-relaxed text-sm md:text-base">Versand für nur <span class="font-semibold text-gold-600">7€</span> aus Deutschland <span class="font-semibold text-gold-600">direkt zu dir</span> nach Hause.</p>
                    </div>

                    <div class="group text-center p-6 md:p-8 rounded-2xl md:rounded-3xl bg-gradient-to-br from-purple-50 via-white to-purple-100 hover:shadow-2xl hover:shadow-purple-200/50 transition-all duration-500 border border-purple-100 hover:border-gold-200">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-purple-600 to-purple-500 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                            <font-awesome-icon :icon="['fas', 'box-open']" class="text-white text-xl md:text-2xl w-8 h-8 md:w-10 md:h-10" />
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-navy-900 mb-3 md:mb-4">Inventar-System</h3>
                        <p class="text-navy-600 leading-relaxed text-sm md:text-base">Sammle Gewinne und erstelle später dein <span class="font-semibold text-gold-600">perfektes Paket</span> zum Versand.</p>
                    </div>

                    <div class="group text-center p-6 md:p-8 rounded-2xl md:rounded-3xl bg-gradient-to-br from-gold-50 via-white to-gold-100 hover:shadow-2xl hover:shadow-gold-200/50 transition-all duration-500 border border-gold-100 hover:border-gold-200">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gold-gradient rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                            <font-awesome-icon :icon="['fas', 'bolt']" class="text-navy-900 text-xl md:text-2xl w-8 h-8 md:w-10 md:h-10" />
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-navy-900 mb-3 md:mb-4">Sofortige Ziehung</h3>
                        <p class="text-navy-600 leading-relaxed text-sm md:text-base">Nach dem Ziehen erfährst du sofort deinen Gewinn. <span class="font-semibold text-gold-600">Keine langen Wartezeiten!</span></p>
                    </div>

                    <div class="group text-center p-6 md:p-8 rounded-2xl md:rounded-3xl bg-gradient-to-br from-rose-50 via-white to-rose-100 hover:shadow-2xl hover:shadow-rose-200/50 transition-all duration-500 border border-rose-100 hover:border-gold-200">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-rose-600 to-rose-500 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                            <font-awesome-icon :icon="['fas', 'heart']" class="text-white text-xl md:text-2xl w-8 h-8 md:w-10 md:h-10" />
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-navy-900 mb-3 md:mb-4">Vielfältige Kategorien</h3>
                        <p class="text-navy-600 leading-relaxed text-sm md:text-base">Anime, Gaming, Lifestyle und mehr - <span class="font-semibold text-gold-600">für jeden Geschmack</span> das Richtige.</p>
                    </div>

                    <div class="group text-center p-6 md:p-8 rounded-2xl md:rounded-3xl bg-gradient-to-br from-indigo-50 via-white to-indigo-100 hover:shadow-2xl hover:shadow-indigo-200/50 transition-all duration-500 border border-indigo-100 hover:border-gold-200">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-indigo-600 to-indigo-500 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                            <font-awesome-icon :icon="['fas', 'shield-alt']" class="text-white text-xl md:text-2xl w-8 h-8 md:w-10 md:h-10" />
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-navy-900 mb-3 md:mb-4">Sicher & Vertrauenswürdig</h3>
                        <p class="text-navy-600 leading-relaxed text-sm md:text-base">Sichere Bezahlung und <span class="font-semibold text-gold-600">zuverlässiger Versand</span> direkt aus Deutschland.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Social Proof Section -->
        <section v-if="showBelowFold" class="py-16 md:py-20 bg-gradient-to-br from-navy-900 via-navy-800 to-navy-900 relative overflow-hidden" style="content-visibility:auto;contain-intrinsic-size:1200px;" v-once>
            <!-- Background decoration -->
            <div class="absolute inset-0 opacity-20" aria-hidden="true">
                <div class="absolute top-10 left-1/4 w-64 h-64 bg-gold-400/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 right-1/4 w-80 h-80 bg-gold-400/5 rounded-full blur-3xl"></div>
            </div>
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 md:mb-16">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 md:mb-6">Vertraut von tausenden Spielern</h2>
                    <p class="text-lg md:text-xl text-navy-200 max-w-3xl mx-auto leading-relaxed">
                        Über <span class="text-gold-400 font-bold">25.000+</span> gezogene Lose und eine wachsende Community
                    </p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 mb-16">
                    <div class="text-center group">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl md:rounded-3xl p-4 md:p-6 border border-gold-400/20 hover:border-gold-400/40 transition-all duration-300 group-hover:scale-105">
                            <div class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gold-400 mb-1 md:mb-2">25k+</div>
                            <div class="text-navy-200 font-semibold text-xs sm:text-sm md:text-base">Gezogene Lose</div>
                        </div>
                    </div>
                    <div class="text-center group">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl md:rounded-3xl p-4 md:p-6 border border-gold-400/20 hover:border-gold-400/40 transition-all duration-300 group-hover:scale-105">
                            <div class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gold-400 mb-1 md:mb-2">98%</div>
                            <div class="text-navy-200 font-semibold text-xs sm:text-sm md:text-base">Zufriedenheit</div>
                        </div>
                    </div>
                    <div class="text-center group">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl md:rounded-3xl p-4 md:p-6 border border-gold-400/20 hover:border-gold-400/40 transition-all duration-300 group-hover:scale-105">
                            <div class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gold-400 mb-1 md:mb-2">15k+</div>
                            <div class="text-navy-200 font-semibold text-xs sm:text-sm md:text-base">Aktive Nutzer</div>
                        </div>
                    </div>
                    <div class="text-center group">
                        <div class="bg-white/10 backdrop-blur-sm rounded-2xl md:rounded-3xl p-4 md:p-6 border border-gold-400/20 hover:border-gold-400/40 transition-all duration-300 group-hover:scale-105">
                            <div class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gold-400 mb-1 md:mb-2">24h</div>
                            <div class="text-navy-200 font-semibold text-xs sm:text-sm md:text-base">Avg. Versand</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonials -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 mb-12 md:mb-16">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl md:rounded-3xl p-6 md:p-8 border border-white/20 hover:border-gold-400/30 transition-all duration-300 group">
                        <div class="flex items-center mb-4 md:mb-6">
                            <div class="w-10 h-10 md:w-12 md:h-12 bg-gold-gradient rounded-full flex items-center justify-center mr-3 md:mr-4">
                                <font-awesome-icon :icon="['fas', 'user']" class="text-navy-900 text-base md:text-lg" />
                            </div>
                            <div>
                                <div class="text-white font-semibold text-sm md:text-base">Sarah_Anime23</div>
                                <div class="flex text-gold-400">
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                </div>
                            </div>
                        </div>
                        <p class="text-navy-200 leading-relaxed group-hover:text-white transition-colors duration-300 text-sm md:text-base">
                            "Endlich eine Plattform wo man wirklich immer gewinnt! Die Anime-Figuren sind original und der Versand super schnell."
                        </p>
                    </div>
                    
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl md:rounded-3xl p-6 md:p-8 border border-white/20 hover:border-gold-400/30 transition-all duration-300 group">
                        <div class="flex items-center mb-4 md:mb-6">
                            <div class="w-10 h-10 md:w-12 md:h-12 bg-gold-gradient rounded-full flex items-center justify-center mr-3 md:mr-4">
                                <font-awesome-icon :icon="['fas', 'user']" class="text-navy-900 text-base md:text-lg" />
                            </div>
                            <div>
                                <div class="text-white font-semibold text-sm md:text-base">GamerMax_DE</div>
                                <div class="flex text-gold-400">
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                </div>
                            </div>
                        </div>
                        <p class="text-navy-200 leading-relaxed group-hover:text-white transition-colors duration-300 text-sm md:text-base">
                            "Das Inventar-System ist genial! Kann alles sammeln und später in einem Paket versenden lassen. Sehr durchdacht!"
                        </p>
                    </div>
                    
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl md:rounded-3xl p-6 md:p-8 border border-white/20 hover:border-gold-400/30 transition-all duration-300 group">
                        <div class="flex items-center mb-4 md:mb-6">
                            <div class="w-10 h-10 md:w-12 md:h-12 bg-gold-gradient rounded-full flex items-center justify-center mr-3 md:mr-4">
                                <font-awesome-icon :icon="['fas', 'user']" class="text-navy-900 text-base md:text-lg" />
                            </div>
                            <div>
                                <div class="text-white font-semibold text-sm md:text-base">MysteryFan2024</div>
                                <div class="flex text-gold-400">
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                    <font-awesome-icon :icon="['fas', 'star']" class="w-3 h-3 md:w-4 md:h-4" />
                                </div>
                            </div>
                        </div>
                        <p class="text-navy-200 leading-relaxed group-hover:text-white transition-colors duration-300 text-sm md:text-base">
                            "Schon über 50 Lose gezogen und nie enttäuscht worden. Die Vielfalt ist beeindruckend und Support top!"
                        </p>
                    </div>
                </div>

                <!-- Trust Badges -->
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl md:rounded-3xl p-6 md:p-8 border border-white/10">
                    <div class="text-center mb-6 md:mb-8">
                        <h3 class="text-xl md:text-2xl font-bold text-white mb-3 md:mb-4">Sicherheit & Vertrauen</h3>
                        <p class="text-navy-200 text-sm md:text-base">Deine Daten und Zahlungen sind bei uns sicher</p>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                        <div class="text-center group">
                            <div class="w-12 h-12 md:w-16 md:h-16 bg-emerald-600 rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-2 md:mb-3 group-hover:scale-110 transition-transform duration-300">
                                <font-awesome-icon :icon="['fas', 'shield-alt']" class="text-white text-lg md:text-xl" />
                            </div>
                            <div class="text-white font-semibold text-xs md:text-sm">SSL-Verschlüsselung</div>
                        </div>
                        <div class="text-center group">
                            <div class="w-12 h-12 md:w-16 md:h-16 bg-blue-600 rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-2 md:mb-3 group-hover:scale-110 transition-transform duration-300">
                                <font-awesome-icon :icon="['fas', 'credit-card']" class="text-white text-lg md:text-xl" />
                            </div>
                            <div class="text-white font-semibold text-xs md:text-sm">Sichere Zahlung</div>
                        </div>
                        <div class="text-center group">
                            <div class="w-12 h-12 md:w-16 md:h-16 bg-purple-600 rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-2 md:mb-3 group-hover:scale-110 transition-transform duration-300">
                                <font-awesome-icon :icon="['fas', 'truck']" class="text-white text-lg md:text-xl" />
                            </div>
                            <div class="text-white font-semibold text-xs md:text-sm">Versand-Tracking</div>
                        </div>
                        <div class="text-center group">
                            <div class="w-12 h-12 md:w-16 md:h-16 bg-gold-600 rounded-xl md:rounded-2xl flex items-center justify-center mx-auto mb-2 md:mb-3 group-hover:scale-110 transition-transform duration-300">
                                <font-awesome-icon :icon="['fas', 'headset']" class="text-white text-lg md:text-xl" />
                            </div>
                            <div class="text-white font-semibold text-xs md:text-sm">24/7 Support</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
    <section v-if="showBelowFold" id="contact" class="py-16 md:py-20 bg-gradient-to-br from-navy-50 via-white to-gold-50" style="content-visibility:auto;contain-intrinsic-size:1500px;" v-once>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-navy-900 mb-4 md:mb-6">
                        Kontakt
                    </h2>
                    <p class="text-lg md:text-xl text-navy-600 mb-8 md:mb-12">
                        Hast du Fragen oder Feedback? Wir freuen uns von dir zu hören!
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 mb-8 md:mb-12">
                        <div class="text-center group">
                            <div class="w-16 h-16 md:w-20 md:h-20 bg-navy-gradient rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                                <font-awesome-icon :icon="['fas', 'envelope']" class="text-gold-400 w-6 h-6 md:w-8 md:h-8" />
                            </div>
                            <h3 class="text-base md:text-lg font-semibold text-navy-900 mb-2">E-Mail</h3>
                            <p class="text-navy-600 text-sm md:text-base">contact@mystidraw.com</p>
                        </div>
                        
                        <div class="text-center group">
                            <div class="w-16 h-16 md:w-20 md:h-20 bg-emerald-600 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                                <font-awesome-icon :icon="['fas', 'clock']" class="text-white w-6 h-6 md:w-8 md:h-8" />
                            </div>
                            <h3 class="text-base md:text-lg font-semibold text-navy-900 mb-2">Support-Zeiten</h3>
                            <p class="text-navy-600 text-sm md:text-base">Mo-Fr 9:00-18:00 Uhr</p>
                        </div>
                        
                        <div class="text-center group">
                            <div class="w-16 h-16 md:w-20 md:h-20 bg-purple-600 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-4 md:mb-6 group-hover:scale-110 transform transition-all duration-300 shadow-lg">
                                <font-awesome-icon :icon="['fas', 'map-marker-alt']" class="text-white w-6 h-6 md:w-8 md:h-8" />
                            </div>
                            <h3 class="text-base md:text-lg font-semibold text-navy-900 mb-2">Standort</h3>
                            <p class="text-navy-600 text-sm md:text-base">Deutschland</p>
                        </div>
                    </div>
                    
                    <div class="bg-white/90 rounded-2xl md:rounded-3xl p-6 md:p-8 border border-navy-100 shadow-lg">
                        <h3 class="text-xl md:text-2xl font-semibold text-navy-900 mb-4 md:mb-6">Häufige Fragen</h3>
                        <div class="space-y-4 md:space-y-6 text-left">
                            <div class="p-3 md:p-4 rounded-xl md:rounded-2xl bg-navy-50 border border-navy-100">
                                <h4 class="font-semibold text-navy-900 flex items-center space-x-2 text-sm md:text-base">
                                    <font-awesome-icon :icon="['fas', 'question-circle']" class="text-gold-500 text-sm md:text-base" />
                                    <span>Wie funktioniert das Inventar-System?</span>
                                </h4>
                                <p class="text-navy-600 mt-2 text-sm md:text-base">Du sammelst deine Gewinne in deinem digitalen Inventar und kannst sie später als Paket versenden lassen.</p>
                            </div>
                            <div class="p-3 md:p-4 rounded-xl md:rounded-2xl bg-navy-50 border border-navy-100">
                                <h4 class="font-semibold text-navy-900 flex items-center space-x-2 text-sm md:text-base">
                                    <font-awesome-icon :icon="['fas', 'question-circle']" class="text-gold-500 text-sm md:text-base" />
                                    <span>Wann erhalte ich meinen Gewinn?</span>
                                </h4>
                                <p class="text-navy-600 mt-2 text-sm md:text-base">Sofort nach dem Ziehen erfährst du deinen Gewinn. Der Versand erfolgt auf Anfrage aus deinem Inventar.</p>
                            </div>
                            <div class="p-3 md:p-4 rounded-xl md:rounded-2xl bg-navy-50 border border-navy-100">
                                <h4 class="font-semibold text-navy-900 flex items-center space-x-2 text-sm md:text-base">
                                    <font-awesome-icon :icon="['fas', 'question-circle']" class="text-gold-500 text-sm md:text-base" />
                                    <span>Was kostet der Versand?</span>
                                </h4>
                                <p class="text-navy-600 mt-2 text-sm md:text-base">Der Versand kostet 7€ und erfolgt schnell und zuverlässig aus Deutschland!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>

<style scoped>
html { scroll-behavior: smooth; }
</style>

<!-- Global (not scoped) animation & performance adjustments -->
<style>
@media (prefers-reduced-motion: reduce) {
    .animate-glow, .animate-bounce, .animate-float, .animate-pulse { animation: none !important; }
}
</style>
