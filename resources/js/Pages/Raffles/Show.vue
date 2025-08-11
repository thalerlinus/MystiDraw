<template>
    <MainLayout 
        :title="`${raffle.name} - MystiDraw`" 
        :user="$page.props.auth?.user"
    >
        <!-- Hero Section mit Kategorie-Bild -->
        <div class="relative min-h-[300px] sm:min-h-[400px] md:min-h-[500px] overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800">
            <!-- Hero Background -->
            <div 
                v-if="raffle.category?.hero_image_path"
                class="absolute inset-0 bg-cover bg-center bg-no-repeat"
                :style="{ backgroundImage: `url(${getImageUrl(raffle.category.hero_image_path, bunny.pull_zone)})` }"
            >
                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/80 via-slate-900/60 to-slate-950/90"></div>
            </div>
            <!-- Fallback Background wenn kein Kategorie-Bild -->
            <div 
                v-else
                class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800"
            ></div>
            
            <!-- Decorative Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-yellow-400/5 to-transparent"></div>

            <!-- Hero Content -->
            <div class="relative z-10 container mx-auto px-4 py-8 sm:py-12 md:py-16 lg:py-24">
                <div class="max-w-4xl mx-auto text-center text-white">
                    <!-- Breadcrumb -->
                    <nav class="mb-4 sm:mb-6 md:mb-8">
                        <ol class="flex items-center justify-center space-x-2 sm:space-x-3 text-xs sm:text-sm">
                            <li>
                                <Link :href="route('home')" class="flex items-center text-slate-300 hover:text-yellow-400 transition-all duration-300 hover:scale-105">
                                    <font-awesome-icon :icon="['fas', 'home']" class="mr-1 sm:mr-2" />
                                    <span class="hidden sm:inline">Home</span>
                                </Link>
                            </li>
                            <li class="text-slate-500">
                                <font-awesome-icon :icon="['fas', 'chevron-right']" class="text-xs" />
                            </li>
                            <li>
                                <Link :href="route('raffles.index')" class="flex items-center text-slate-300 hover:text-yellow-400 transition-all duration-300 hover:scale-105">
                                    <font-awesome-icon :icon="['fas', 'dice']" class="mr-1 sm:mr-2" />
                                    <span class="hidden sm:inline">Raffles</span>
                                </Link>
                            </li>
                            <li class="text-slate-500">
                                <font-awesome-icon :icon="['fas', 'chevron-right']" class="text-xs" />
                            </li>
                            <li v-if="raffle.category" class="flex items-center text-yellow-400 font-medium">
                                <font-awesome-icon :icon="['fas', 'tag']" class="mr-1 sm:mr-2" />
                                {{ raffle.category.name }}
                            </li>
                        </ol>
                    </nav>

                    <!-- Kategorie Badge -->
                    <div v-if="raffle.category" class="mb-4 sm:mb-6">
                        <div class="inline-flex items-center px-3 py-2 sm:px-6 sm:py-3 bg-gradient-to-r from-yellow-400 to-yellow-500 text-slate-900 font-bold rounded-full shadow-lg border-2 border-yellow-300 transform hover:scale-105 transition-all duration-300">
                            <font-awesome-icon :icon="['fas', 'tag']" class="mr-2" />
                            {{ raffle.category.name }}
                        </div>
                    </div>

                    <!-- Haupttitel -->
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-black mb-6 sm:mb-8 leading-tight bg-gradient-to-r from-white via-yellow-100 to-white bg-clip-text text-transparent drop-shadow-2xl">
                        {{ raffle.name }}
                    </h1>
                    
                    <!-- Status und Verfügbarkeit -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 md:gap-8 mb-6 sm:mb-8 md:mb-10">
                        <div class="flex items-center space-x-2 sm:space-x-4 bg-white/10 backdrop-blur-sm px-4 py-2 sm:px-6 sm:py-3 rounded-full border border-white/20">
                            <div class="relative">
                                <div 
                                    class="w-3 h-3 sm:w-4 sm:h-4 rounded-full animate-pulse"
                                    :class="raffle.status === 'live' ? 'bg-green-400 shadow-green-400/50' : 'bg-gray-400'"
                                ></div>
                                <div 
                                    v-if="raffle.status === 'live'"
                                    class="absolute inset-0 w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-green-400 animate-ping opacity-75"
                                ></div>
                            </div>
                            <span class="text-sm sm:text-lg font-semibold text-white">
                                {{ getStatusText(raffle.status) }}
                            </span>
                        </div>
                        
                        <div class="flex items-center space-x-2 sm:space-x-3 text-sm sm:text-lg bg-white/10 backdrop-blur-sm px-4 py-2 sm:px-6 sm:py-3 rounded-full border border-white/20">
                            <div class="p-1 sm:p-2 bg-yellow-400/20 rounded-full">
                                <font-awesome-icon :icon="['fas', 'ticket']" class="text-yellow-400 text-sm sm:text-base" />
                            </div>
                            <span class="text-white">
                                <span class="font-bold text-yellow-400">{{ raffle.tickets_available }}</span>
                                <span class="hidden sm:inline">von {{ raffle.tickets_total }} verfügbar</span>
                                <span class="sm:hidden">/ {{ raffle.tickets_total }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Preis Information -->
                    <div class="text-center mb-6 sm:mb-8 md:mb-10">
                        <div class="inline-block bg-gradient-to-r from-white/10 to-white/5 backdrop-blur-sm p-4 sm:p-6 md:p-8 rounded-2xl border border-white/20 shadow-2xl">
                            <div class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black bg-gradient-to-r from-yellow-300 to-yellow-500 bg-clip-text text-transparent mb-2 sm:mb-3 drop-shadow-lg">
                                {{ formatPrice(raffle.base_ticket_price) }}
                            </div>
                            <div class="text-lg sm:text-xl text-slate-200 font-medium">pro Los</div>
                            <div class="w-12 sm:w-16 h-1 bg-gradient-to-r from-yellow-400 to-yellow-500 mx-auto mt-3 sm:mt-4 rounded-full"></div>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="flex justify-center">
                        <button
                            v-if="raffle.status === 'live' && raffle.tickets_available > 0"
                            @click="openPurchaseModal(1)"
                            class="group relative px-8 py-3 sm:px-12 sm:py-4 md:px-16 md:py-5 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-400 hover:from-yellow-300 hover:via-yellow-400 hover:to-yellow-300 text-slate-900 font-black text-lg sm:text-xl rounded-full transition-all duration-500 transform hover:scale-110 shadow-2xl hover:shadow-yellow-400/25 border-2 border-yellow-300"
                        >
                            <div class="absolute inset-0 bg-gradient-to-r from-yellow-300 to-yellow-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="relative flex items-center">
                                <div class="p-1 bg-slate-900/10 rounded-full mr-2 sm:mr-3">
                                    <font-awesome-icon :icon="['fas', 'ticket']" class="animate-pulse text-sm sm:text-base" />
                                </div>
                                <span class="hidden sm:inline">Jetzt Lose kaufen</span>
                                <span class="sm:hidden">Lose kaufen</span>
                                <font-awesome-icon :icon="['fas', 'arrow-right']" class="ml-2 sm:ml-3 transition-transform group-hover:translate-x-1" />
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-gradient-to-b from-slate-50 to-white">
            <div class="container mx-auto px-4 py-8 sm:py-12 md:py-16">
                <div class="max-w-6xl mx-auto">
                    <!-- Gewinn-Carousel Section -->
                    <section class="mb-12 sm:mb-16 md:mb-20">
                        <div class="text-center mb-8 sm:mb-12 md:mb-16">
                            <div class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-2 bg-gradient-to-r from-yellow-100 to-yellow-200 text-slate-800 rounded-full mb-4 sm:mb-6 font-semibold text-sm sm:text-base">
                                <font-awesome-icon :icon="['fas', 'gift']" class="mr-2 text-yellow-600" />
                                Deine Gewinnchancen
                            </div>
                            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 mb-4 sm:mb-6 leading-tight">
                                Mögliche <span class="bg-gradient-to-r from-yellow-500 to-yellow-600 bg-clip-text text-transparent">Gewinne</span>
                            </h2>
                            <p class="text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
                                Entdecke alle Überraschungen, die auf dich warten – jedes Los ist ein Gewinn!
                            </p>
                        </div>

                    <!-- Gewinn Carousel -->
                    <div v-if="raffle.items && raffle.items.length > 0" class="relative">
                        <div class="overflow-hidden rounded-xl shadow-lg">
                            <div 
                                class="flex transition-transform duration-500 ease-in-out"
                                :style="{ transform: `translateX(-${currentPrizeIndex * 100}%)` }"
                            >
                                <div 
                                    v-for="item in sortedCarouselItems" 
                                    :key="item.id"
                                    class="min-w-full relative"
                                >
                                    <!-- Produkt Bild -->
                                    <div class="h-64 sm:h-80 md:h-96 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center relative">
                                        <ProductImageGallery 
                                            v-if="item.product"
                                            :product="item.product"
                                            :pull-zone="bunny.pull_zone"
                                        />
                                        <div v-else class="text-gray-500 text-center">
                                            <svg class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mx-auto mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-lg sm:text-xl">Kein Bild verfügbar</p>
                                        </div>

                                        <!-- Tier Badge -->
                                        <div class="absolute top-2 sm:top-4 right-2 sm:right-4">
                                            <span 
                                                class="px-2 py-1 sm:px-4 sm:py-2 rounded-full font-bold text-white text-sm sm:text-lg shadow-lg"
                                                :class="getTierColor(item.tier)"
                                            >
                                                Tier {{ item.tier }}
                                            </span>
                                        </div>

                                        <!-- Last One Badge -->
                                        <div v-if="item.is_last_one" class="absolute top-2 sm:top-4 left-2 sm:left-4 z-10">
                                            <span class="px-2 py-1 sm:px-4 sm:py-2 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm sm:text-lg font-bold rounded-full animate-pulse shadow-lg border-2 border-white">
                                                <font-awesome-icon :icon="['fas', 'star']" class="mr-1 sm:mr-2" />
                                                <span class="hidden sm:inline">Last One!</span>
                                                <span class="sm:hidden">Last!</span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Produkt Info -->
                                    <div class="bg-white p-4 sm:p-6 text-center">
                                        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-2">
                                            {{ item.product?.name || 'Unbekanntes Produkt' }}
                                        </h3>
                                        <p v-if="item.product?.description" class="text-sm sm:text-base text-gray-600 mb-3 sm:mb-4 line-clamp-2">
                                            {{ item.product.description }}
                                        </p>
                                        
                                        <!-- Anzahl der verfügbaren Gewinne -->
                                        <div class="flex items-center justify-center mb-4">
                                            <div class="bg-slate-100 px-4 py-2 rounded-full border border-slate-200">
                                                <span class="text-sm sm:text-base font-semibold text-slate-700 flex items-center">
                                                    <font-awesome-icon :icon="['fas', 'trophy']" class="mr-2 text-slate-500" />
                                                    {{ item.quantity }}x zu gewinnen
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Last One Bonus Info -->
                                        <div v-if="item.is_last_one" class="mt-3 sm:mt-4 p-2 sm:p-3 bg-red-50 border border-red-200 rounded-lg">
                                            <div class="flex items-center justify-center text-red-700 text-sm sm:text-base">
                                                <font-awesome-icon :icon="['fas', 'gift']" class="mr-2 text-red-500" />
                                                <span class="font-bold">Last One Bonus!</span>
                                            </div>
                                            <p class="text-xs sm:text-sm text-red-600 mt-1">Extra Belohnung beim letzten verfügbaren Los</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Carousel Navigation -->
                        <button
                            v-if="sortedCarouselItems.length > 1"
                            @click="previousPrize"
                            class="absolute left-2 sm:left-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 sm:p-3 rounded-full shadow-lg transition-all duration-300 z-10"
                        >
                            <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        
                        <button
                            v-if="sortedCarouselItems.length > 1"
                            @click="nextPrize"
                            class="absolute right-2 sm:right-4 top-1/2 transform -translate-y-1/2 bg-white/80 hover:bg-white text-gray-800 p-2 sm:p-3 rounded-full shadow-lg transition-all duration-300 z-10"
                        >
                            <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>

                        <!-- Dots Indicator -->
                        <div 
                            v-if="sortedCarouselItems.length > 1"
                            class="flex justify-center space-x-1 sm:space-x-2 mt-4 sm:mt-6"
                        >
                            <button
                                v-for="(item, index) in sortedCarouselItems"
                                :key="index"
                                @click="currentPrizeIndex = index"
                                class="w-2 h-2 sm:w-3 sm:h-3 rounded-full transition-all duration-300"
                                :class="index === currentPrizeIndex 
                                    ? (item.is_last_one ? 'bg-red-500 scale-125 animate-pulse' : 'bg-yellow-500 scale-125')
                                    : (item.is_last_one ? 'bg-red-300 hover:bg-red-400' : 'bg-gray-300 hover:bg-gray-400')"
                            ></button>
                        </div>
                    </div>
                </section>

                    <!-- Preise & Staffelpreise Section -->
                    <section class="mb-12 sm:mb-16 md:mb-20" id="ticket-purchase">
                        <div class="text-center mb-8 sm:mb-12 md:mb-16">
                            <div class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-2 bg-gradient-to-r from-green-100 to-emerald-200 text-slate-800 rounded-full mb-4 sm:mb-6 font-semibold text-sm sm:text-base">
                                <font-awesome-icon :icon="['fas', 'tags']" class="mr-2 text-green-600" />
                                Smart sparen
                            </div>
                            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 mb-4 sm:mb-6 leading-tight">
                                Preise & <span class="bg-gradient-to-r from-green-500 to-emerald-600 bg-clip-text text-transparent">Staffelrabatte</span>
                            </h2>
                            <p class="text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
                                Je mehr Lose du kaufst, desto mehr sparst du – maximiere deine Gewinnchancen!
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8 pt-8">
                            <!-- Base Price Card -->
                            <div 
                                @click="openPurchaseModal(1)"
                                class="group bg-white rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 border-2 border-slate-200 hover:border-yellow-300 transition-all duration-500 hover:shadow-2xl hover:-translate-y-1 cursor-pointer"
                            >
                                <div class="text-center">
                                    <div class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 bg-gradient-to-r from-slate-100 to-slate-200 group-hover:from-yellow-100 group-hover:to-yellow-200 rounded-full flex items-center justify-center mb-4 sm:mb-6 mx-auto group-hover:scale-110 transition-all duration-500">
                                        <font-awesome-icon :icon="['fas', 'ticket']" class="text-lg sm:text-xl md:text-2xl text-slate-600 group-hover:text-yellow-600" />
                                    </div>
                                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 mb-3 sm:mb-4">Standardpreis</h3>
                                    <div class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 mb-2">{{ formatPrice(raffle.base_ticket_price) }}</div>
                                    <div class="text-sm sm:text-base text-slate-600 font-medium mb-4">pro Los</div>
                                    
                                    <div class="bg-gray-50 group-hover:bg-yellow-50 rounded-lg p-3 transition-colors duration-500">
                                        <div class="text-sm text-gray-700 group-hover:text-yellow-700 font-medium">
                                            <font-awesome-icon :icon="['fas', 'mouse-pointer']" class="mr-2" />
                                            Klicken zum Kaufen
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing Tiers -->
                            <div 
                                v-for="tier in raffle.pricing_tiers" 
                                :key="tier.min_qty"
                                @click="openPurchaseModal(tier.min_qty)"
                                class="group relative bg-gradient-to-br from-yellow-50 via-white to-yellow-100 rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 border-2 border-yellow-300 hover:border-yellow-400 transition-all duration-500 hover:shadow-2xl hover:-translate-y-1 overflow-visible cursor-pointer"
                            >
                                <!-- Best Value Badge -->
                                <div v-if="tier === raffle.pricing_tiers[raffle.pricing_tiers.length - 1]" class="absolute -top-4 sm:-top-5 left-1/2 transform -translate-x-1/2 z-20">
                                    <div class="bg-gradient-to-r from-red-500 to-red-600 text-white text-xs sm:text-sm font-bold px-3 py-1.5 sm:px-4 sm:py-2 rounded-full shadow-lg animate-pulse border-2 border-white whitespace-nowrap">
                                        <font-awesome-icon :icon="['fas', 'crown']" class="mr-1" />
                                        <span class="hidden sm:inline">BESTER WERT</span>
                                        <span class="sm:hidden">TOP</span>
                                    </div>
                                </div>
                                
                                <!-- Background decoration -->
                                <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-br from-yellow-200/30 to-transparent rounded-full -translate-y-12 translate-x-12 sm:-translate-y-16 sm:translate-x-16 group-hover:scale-150 transition-transform duration-700"></div>
                                
                                <div class="text-center relative z-10">
                                    <div class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center mb-4 sm:mb-6 mx-auto group-hover:scale-110 transition-transform duration-500 shadow-lg">
                                        <font-awesome-icon :icon="['fas', 'tags']" class="text-lg sm:text-xl md:text-2xl text-white" />
                                    </div>
                                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 mb-3 sm:mb-4">Ab {{ tier.min_qty }} Losen</h3>
                                    <div class="text-2xl sm:text-3xl md:text-4xl font-black bg-gradient-to-r from-yellow-600 to-yellow-700 bg-clip-text text-transparent mb-2">
                                        {{ formatPrice(tier.unit_price) }}
                                    </div>
                                    <div class="text-sm sm:text-base text-slate-600 mb-3 sm:mb-4 font-medium">pro Los</div>
                                    
                                    <!-- Ersparnis berechnen -->
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-2 sm:p-3 mb-4">
                                        <div class="flex items-center justify-center text-green-700 font-bold text-sm sm:text-base">
                                            <font-awesome-icon :icon="['fas', 'piggy-bank']" class="mr-2 text-green-600" />
                                            Spare {{ formatPrice(raffle.base_ticket_price - tier.unit_price) }} pro Los!
                                        </div>
                                        <div class="text-xs sm:text-sm text-green-600 mt-1">
                                            Gesamtersparnis bei {{ tier.min_qty }} Losen: {{ formatPrice((raffle.base_ticket_price - tier.unit_price) * tier.min_qty) }}
                                        </div>
                                    </div>
                                    
                                    <div class="bg-yellow-100 group-hover:bg-yellow-200 rounded-lg p-3 transition-colors duration-500">
                                        <div class="text-sm text-yellow-700 group-hover:text-yellow-800 font-medium">
                                            <font-awesome-icon :icon="['fas', 'mouse-pointer']" class="mr-2" />
                                            Klicken zum Kaufen
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Alle Gewinne nach Tiers Section -->
                    <section class="mb-12 sm:mb-16 md:mb-20">
                        <div class="text-center mb-8 sm:mb-12 md:mb-16">
                            <div class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-2 bg-gradient-to-r from-purple-100 to-purple-200 text-slate-800 rounded-full mb-4 sm:mb-6 font-semibold text-sm sm:text-base">
                                <font-awesome-icon :icon="['fas', 'list']" class="mr-2 text-purple-600" />
                                Vollständige Übersicht
                            </div>
                            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-slate-900 mb-4 sm:mb-6 leading-tight">
                                Alle Gewinne im <span class="bg-gradient-to-r from-purple-500 to-purple-600 bg-clip-text text-transparent">Überblick</span>
                            </h2>
                            <p class="text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed">
                                Geordnet nach Gewinn-Kategorien – entdecke alle Möglichkeiten
                            </p>
                        </div>
                        <!-- Tier Tabs -->
                        <div class="flex flex-wrap justify-center gap-2 sm:gap-3 mb-8 sm:mb-12">
                            <button
                                @click="selectedTier = 'all'"
                                class="px-4 py-2 sm:px-6 sm:py-3 md:px-8 md:py-4 rounded-full font-bold transition-all duration-300 transform hover:scale-105 shadow-lg text-sm sm:text-base"
                                :class="selectedTier === 'all' 
                                    ? 'bg-gradient-to-r from-slate-800 to-slate-900 text-white shadow-slate-800/25' 
                                    : 'bg-white text-slate-700 hover:bg-slate-50 border-2 border-slate-200 hover:border-slate-300 hover:shadow-lg'"
                            >
                                <font-awesome-icon :icon="['fas', 'layer-group']" class="mr-1 sm:mr-2" />
                                <span class="hidden sm:inline">Alle Tiers</span>
                                <span class="sm:hidden">Alle</span>
                            </button>
                            <button
                                @click="selectedTier = 'last_one'"
                                class="px-4 py-2 sm:px-6 sm:py-3 md:px-8 md:py-4 rounded-full font-bold transition-all duration-300 transform hover:scale-105 shadow-lg text-sm sm:text-base"
                                :class="selectedTier === 'last_one' 
                                    ? 'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-red-500/25 animate-pulse' 
                                    : 'bg-white text-slate-700 hover:bg-red-50 border-2 border-red-200 hover:border-red-300 hover:shadow-lg'"
                            >
                                <font-awesome-icon :icon="['fas', 'star']" class="mr-1 sm:mr-2" />
                                Last One
                            </button>
                            <button
                                v-for="tier in uniqueTiers"
                                :key="tier"
                                @click="selectedTier = tier"
                                class="px-4 py-2 sm:px-6 sm:py-3 md:px-8 md:py-4 rounded-full font-bold transition-all duration-300 transform hover:scale-105 shadow-lg text-sm sm:text-base"
                                :class="selectedTier === tier 
                                    ? `text-white shadow-lg ${getTierColor(tier)}` 
                                    : 'bg-white text-slate-700 hover:bg-slate-50 border-2 border-slate-200 hover:border-slate-300 hover:shadow-lg'"
                            >
                                <font-awesome-icon :icon="['fas', 'award']" class="mr-1 sm:mr-2" />
                                Tier {{ tier }}
                            </button>
                        </div>                    <!-- Filtered Items Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
                        <div 
                            v-for="item in filteredItems" 
                            :key="item.id"
                            class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02] border"
                            :class="item.is_last_one 
                                ? 'border-red-400 shadow-red-100 ring-2 ring-red-200 bg-gradient-to-br from-red-50 to-white' 
                                : 'border-gray-200'"
                        >
                            <!-- Item Image -->
                            <div class="h-32 sm:h-40 md:h-48 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center relative">
                                <ProductImageGallery 
                                    v-if="item.product"
                                    :product="item.product"
                                    :pull-zone="bunny.pull_zone"
                                />
                                <div v-else class="text-gray-400 text-center">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 mx-auto mb-1 sm:mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-xs sm:text-sm">Kein Bild</p>
                                </div>

                                <!-- Tier Badge -->
                                <div class="absolute top-1 sm:top-2 right-1 sm:right-2">
                                    <span 
                                        class="px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-full text-white text-xs sm:text-sm font-bold"
                                        :class="getTierColor(item.tier)"
                                    >
                                        {{ item.tier }}
                                    </span>
                                </div>

                                <!-- Last One Badge -->
                                <div v-if="item.is_last_one" class="absolute top-1 sm:top-2 left-1 sm:left-2 z-10">
                                    <span class="px-1.5 py-0.5 sm:px-3 sm:py-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs sm:text-sm font-bold rounded-full animate-pulse shadow-lg border border-white sm:border-2">
                                        <font-awesome-icon :icon="['fas', 'star']" class="mr-1" />
                                        <span class="hidden sm:inline">Last One!</span>
                                        <span class="sm:hidden">Last!</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Item Content -->
                            <div class="p-2 sm:p-3 md:p-4">
                                <h3 class="font-bold text-gray-900 mb-1 sm:mb-2 line-clamp-2 text-sm sm:text-base">
                                    {{ item.product?.name || 'Unbekanntes Produkt' }}
                                </h3>
                                <p v-if="item.product?.description" class="text-gray-600 text-xs sm:text-sm mb-2 sm:mb-3 line-clamp-2">
                                    {{ item.product.description }}
                                </p>
                                
                                <!-- Anzahl der verfügbaren Gewinne -->
                                <div class="flex items-center justify-center mb-2">
                                    <div class="bg-slate-100 px-2 py-1 rounded-full">
                                        <span class="text-xs sm:text-sm font-semibold text-slate-700 flex items-center">
                                            <font-awesome-icon :icon="['fas', 'trophy']" class="mr-1 text-slate-500" />
                                            {{ item.quantity }}x verfügbar
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Last One Bonus Info -->
                                <div v-if="item.is_last_one" class="mt-2 sm:mt-3 p-1.5 sm:p-2 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex items-center text-red-700 text-xs sm:text-sm">
                                        <font-awesome-icon :icon="['fas', 'gift']" class="mr-1 sm:mr-2 text-red-500" />
                                        <span class="font-medium">Last One Bonus!</span>
                                    </div>
                                    <p class="text-xs text-red-600 mt-1 hidden sm:block">Extra Belohnung beim letzten verfügbaren Los</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- CTA Section -->
                <section class="relative text-center py-12 sm:py-16 md:py-20 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 rounded-2xl sm:rounded-3xl text-white overflow-hidden shadow-2xl">
                    <!-- Background decoration -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-yellow-400/10 to-transparent"></div>
                    <div class="absolute top-0 left-0 w-64 h-64 sm:w-96 sm:h-96 bg-gradient-to-br from-yellow-400/20 to-transparent rounded-full -translate-x-32 -translate-y-32 sm:-translate-x-48 sm:-translate-y-48"></div>
                    <div class="absolute bottom-0 right-0 w-64 h-64 sm:w-96 sm:h-96 bg-gradient-to-tl from-yellow-400/20 to-transparent rounded-full translate-x-32 translate-y-32 sm:translate-x-48 sm:translate-y-48"></div>
                    
                    <div class="relative z-10 px-4">
                        <div class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-3 bg-white/10 backdrop-blur-sm rounded-full mb-6 sm:mb-8 border border-white/20">
                            <font-awesome-icon :icon="['fas', 'rocket']" class="mr-2 sm:mr-3 text-yellow-400" />
                            <span class="font-bold text-sm sm:text-base">Jetzt oder nie!</span>
                        </div>
                        
                        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black mb-4 sm:mb-6 leading-tight">
                            Bereit für deine 
                            <span class="bg-gradient-to-r from-yellow-300 to-yellow-500 bg-clip-text text-transparent">Überraschung?</span>
                        </h2>
                        <p class="text-base sm:text-lg md:text-xl text-slate-300 mb-8 sm:mb-10 max-w-3xl mx-auto leading-relaxed">
                            Jedes Los ist ein Gewinn! Bei MystiDraw gibt es keine Nieten, nur Überraschungen in verschiedenen Kategorien. 
                            Sichere dir jetzt deine Chance auf tolle Preise.
                        </p>
                        
                        <div class="space-y-4 sm:space-y-6">
                            <button
                                v-if="raffle.status === 'live' && raffle.tickets_available > 0"
                                @click="openPurchaseModal(1)"
                                class="group relative px-8 py-3 sm:px-12 sm:py-4 md:px-16 md:py-5 bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-400 hover:from-yellow-300 hover:via-yellow-400 hover:to-yellow-300 text-slate-900 font-black text-lg sm:text-xl rounded-full transition-all duration-500 transform hover:scale-110 shadow-2xl hover:shadow-yellow-400/25 border-2 border-yellow-300"
                            >
                                <div class="absolute inset-0 bg-gradient-to-r from-yellow-300 to-yellow-500 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <div class="relative flex items-center">
                                    <div class="p-1 sm:p-2 bg-slate-900/10 rounded-full mr-3 sm:mr-4">
                                        <font-awesome-icon :icon="['fas', 'ticket']" class="animate-pulse text-sm sm:text-lg" />
                                    </div>
                                    <span class="hidden sm:inline">Lose kaufen - {{ formatPrice(raffle.base_ticket_price) }}</span>
                                    <span class="sm:hidden">Lose kaufen</span>
                                    <font-awesome-icon :icon="['fas', 'arrow-right']" class="ml-3 sm:ml-4 transition-transform group-hover:translate-x-2" />
                                </div>
                            </button>
                            
                            <div v-else-if="raffle.tickets_available === 0" class="flex items-center justify-center text-slate-400 bg-white/5 backdrop-blur-sm px-6 py-3 sm:px-8 sm:py-4 rounded-full border border-white/10">
                                <font-awesome-icon :icon="['fas', 'exclamation-triangle']" class="mr-2 sm:mr-3 text-orange-400" />
                                <span class="text-sm sm:text-lg font-semibold">
                                    <span class="hidden sm:inline">Ausverkauft – Alle Lose wurden verkauft!</span>
                                    <span class="sm:hidden">Ausverkauft!</span>
                                </span>
                            </div>
                            
                            <div v-else class="flex items-center justify-center text-slate-400 bg-white/5 backdrop-blur-sm px-6 py-3 sm:px-8 sm:py-4 rounded-full border border-white/10">
                                <font-awesome-icon :icon="['fas', 'clock']" class="mr-2 sm:mr-3 text-blue-400" />
                                <span class="text-sm sm:text-lg font-semibold">
                                    <span class="hidden sm:inline">Derzeit nicht verfügbar</span>
                                    <span class="sm:hidden">Nicht verfügbar</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    
    <!-- Ticket Purchase Modal -->
    <TicketPurchaseModal
        :raffle="raffle"
        :is-open="showPurchaseModal"
        :initial-quantity="selectedQuantity"
        @close="closePurchaseModal"
        @purchase="handlePurchase"
    />
    </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import ProductImageGallery from '@/Components/ProductImageGallery.vue';
import TicketPurchaseModal from '@/Components/TicketPurchaseModal.vue';
import axios from 'axios';
import { getThumbUrl, getImageUrl } from '@/utils/cdn';

const route = window.route;

const props = defineProps({
    raffle: { type: Object, required: true },
    bunny: { type: Object, default: () => ({}) }
});

// Reactive data
const currentPrizeIndex = ref(0);
const selectedTier = ref('all');
const showPurchaseModal = ref(false);
const selectedQuantity = ref(1);

// Auto-rotate prize carousel
let prizeInterval;

onMounted(() => {
    if (props.raffle.items && props.raffle.items.length > 1) {
        prizeInterval = setInterval(() => {
            currentPrizeIndex.value = (currentPrizeIndex.value + 1) % sortedCarouselItems.value.length;
        }, 5000);
    }
});

onUnmounted(() => {
    if (prizeInterval) {
        clearInterval(prizeInterval);
    }
});

// Computed properties
const uniqueTiers = computed(() => {
    return [...new Set(props.raffle.items.map(item => item.tier))].sort();
});

const sortedCarouselItems = computed(() => {
    if (!props.raffle.items) return [];
    return [...props.raffle.items].sort((a, b) => {
        if (a.is_last_one && !b.is_last_one) return -1;
        if (!a.is_last_one && b.is_last_one) return 1;
        return 0;
    });
});

const filteredItems = computed(() => {
    let items = [];
    
    if (selectedTier.value === 'all') {
        items = [...props.raffle.items];
    } else if (selectedTier.value === 'last_one') {
        items = props.raffle.items.filter(item => item.is_last_one);
    } else {
        items = props.raffle.items.filter(item => item.tier === selectedTier.value);
    }
    
    // Sortiere so dass Last One Produkte immer zuerst kommen
    return items.sort((a, b) => {
        if (a.is_last_one && !b.is_last_one) return -1;
        if (!a.is_last_one && b.is_last_one) return 1;
        return 0;
    });
});

const availableTickets = computed(() => {
    const totalTickets = props.raffle.items?.reduce((sum, item) => sum + (item.quantity || 0), 0) || 0;
    const soldTickets = props.raffle.tickets?.length || 0;
    const pendingTickets = props.raffle.pending_quantity || 0;
    console.log('[Lose-Berechnung]', {
        totalTickets,
        soldTickets,
        pendingTickets,
        result: Math.max(0, totalTickets - soldTickets - pendingTickets)
    });
    return Math.max(0, totalTickets - soldTickets - pendingTickets);
});

// Methods
const formatPrice = (price) => {
    return new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency: props.raffle.currency || 'EUR'
    }).format(price);
};

const getStatusText = (status) => {
    const statusMap = {
        'live': 'Aktiv',
        'active': 'Aktiv',
        'draft': 'Entwurf',
        'ended': 'Beendet',
        'paused': 'Pausiert',
        'scheduled': 'Geplant'
    };
    return statusMap[status] || status;
};

const getTierColor = (tier) => {
    const colors = {
        'A': 'bg-gradient-to-r from-red-500 to-red-600',
        'B': 'bg-gradient-to-r from-orange-500 to-orange-600', 
        'C': 'bg-gradient-to-r from-yellow-500 to-yellow-600',
        'D': 'bg-gradient-to-r from-green-500 to-green-600',
        'E': 'bg-gradient-to-r from-blue-500 to-blue-600'
    };
    return colors[tier] || 'bg-gradient-to-r from-gray-500 to-gray-600';
};

const nextPrize = () => {
    currentPrizeIndex.value = (currentPrizeIndex.value + 1) % sortedCarouselItems.value.length;
};

const previousPrize = () => {
    currentPrizeIndex.value = currentPrizeIndex.value === 0 
        ? sortedCarouselItems.value.length - 1 
        : currentPrizeIndex.value - 1;
};

const scrollToTicketPurchase = () => {
    const element = document.getElementById('ticket-purchase');
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
};

const openPurchaseModal = (qty = 1) => {
    selectedQuantity.value = qty;
    showPurchaseModal.value = true;
};

const closePurchaseModal = () => {
    showPurchaseModal.value = false;
};

const handlePurchase = async (purchaseData) => {
    // Payment already confirmed in modal, here we could refresh raffle stats
    try {
        // optional: fetch updated raffle info endpoint (not implemented yet)
        // await axios.get(route('raffles.show', raffle.slug));
    } catch(e) {}
    closePurchaseModal();
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Custom animations */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.float-animation {
    animation: float 3s ease-in-out infinite;
}

/* Enhanced glow effects */
.glow-yellow {
    box-shadow: 0 0 30px rgba(251, 191, 36, 0.3);
}

.glow-yellow:hover {
    box-shadow: 0 0 40px rgba(251, 191, 36, 0.5);
}

/* Smooth background transitions */
.bg-hero {
    background: linear-gradient(135deg, 
        rgba(15, 23, 42, 0.95) 0%, 
        rgba(30, 41, 59, 0.9) 35%, 
        rgba(15, 23, 42, 0.95) 100%);
}

/* Enhanced backdrop blur */
.backdrop-blur-enhanced {
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
}

/* Improved text shadow for better readability */
.text-shadow-lg {
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}
</style>
