import sys

file_path = r"D:\anaclean-redesign\web\login-acc-checkout\wp-content\themes\academist-child\page-our-courses.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

start_index = content.find("<!-- \n     PREMIUM PACKAGES CONFIGURATOR")
if start_index == -1:
    start_index = content.find("<!-- \r\n     PREMIUM PACKAGES CONFIGURATOR")

end_index = content.find('<div class="roc-filter-bar"')

if start_index != -1 and end_index != -1:
    fresh_code = r"""<!-- 
     PREMIUM PACKAGES CONFIGURATOR (VUE 3) - BENTO GRID & STICKY CART
-->
<?php 
// â”€â”€â”€ Packages & UI settings â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
if ( function_exists('rima_get_packages') ) {
    $pricing_data = rima_get_packages();
    $ui_settings  = rima_get_packages_ui();
} else {
    $pricing_data = get_option('rima_pricing_packages', []);
    $ui_settings  = array_merge([
        'title'    => 'Build Your Package',
        'subtitle' => 'Select your language, level, and addons.',
        'cta'      => 'PROCEED TO CHECKOUT',
        'features' => [
            'Full access to Rima LMS',
            'Interactive exercises',
            'Self-paced progress tracking',
            'Official Certificate included',
        ],
    ], $pricing_data['ui'] ?? []);
}
$encoded_pricing = json_encode($pricing_data);
$encoded_ui      = json_encode($ui_settings);
?>

<section class="roc-packages-configurator tw-py-24 tw-bg-[#030408] tw-font-sans tw-relative tw-overflow-hidden" id="rima-packages-app" v-cloak>
    
    <!-- Ambient Glows -->
    <div class="tw-absolute tw-top-[-10%] tw-left-[-10%] tw-w-[40%] tw-h-[50%] tw-bg-[#E11D48] tw-rounded-full tw-mix-blend-screen tw-filter tw-blur-[150px] tw-opacity-20 tw-pointer-events-none"></div>
    <div class="tw-absolute tw-bottom-[-10%] tw-right-[-10%] tw-w-[40%] tw-h-[50%] tw-bg-[#12308E] tw-rounded-full tw-mix-blend-screen tw-filter tw-blur-[150px] tw-opacity-20 tw-pointer-events-none"></div>

    <div class="tw-max-w-7xl tw-mx-auto tw-px-4 sm:tw-px-6 tw-relative tw-z-10">
        
        <div class="tw-mb-12 tw-text-center lg:tw-text-left">
            <h2 class="tw-text-5xl tw-font-display tw-font-extrabold tw-text-white tw-tracking-tight tw-mb-4"><?php echo esc_html($ui_settings['title']); ?></h2>
            <p class="tw-text-lg tw-text-gray-400 tw-max-w-2xl"><?php echo esc_html($ui_settings['subtitle']); ?></p>
        </div>

        <div class="tw-flex tw-flex-col-reverse lg:tw-flex-row tw-gap-10 tw-items-start">
            
            <!-- Left: Options Grid (Bento) -->
            <div class="tw-flex-1 tw-space-y-10 tw-w-full">
                
                <!-- 1. Language -->
                <div>
                    <h3 class="tw-text-2xl tw-font-bold tw-text-white tw-mb-6 tw-flex tw-items-center tw-gap-3">
                        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-text-sm">1</div>
                        Language
                    </h3>
                    <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 tw-gap-4">
                        <button v-for="lang in languages" :key="lang.id" 
                                @click="selection.language = lang.id"
                                :class="['tw-relative tw-p-6 tw-rounded-[1.5rem] tw-border tw-transition-all tw-duration-300 tw-ease-out tw-text-left tw-flex tw-flex-col tw-gap-4 group tw-overflow-hidden', 
                                         selection.language === lang.id ? 'tw-border-[#E11D48] tw-bg-gradient-to-br tw-from-[#E11D48]/20 tw-to-[#12308E]/20 tw-text-white' : 'tw-border-white/5 tw-bg-white/[0.03] hover:tw-bg-white/[0.06] hover:tw-border-white/20 tw-text-gray-300']">
                            
                            <img :src="lang.flag" class="tw-w-16 tw-h-12 tw-rounded-lg tw-shadow-md tw-object-cover group-hover:tw-scale-105 tw-transition-transform tw-duration-300" />
                            <div>
                                <div :class="['tw-font-bold tw-text-xl tw-tracking-wide tw-mb-1', selection.language === lang.id ? 'tw-text-white' : 'tw-text-white']">{{ lang.name }}</div>
                                <div :class="['tw-text-sm', selection.language === lang.id ? 'tw-text-gray-300' : 'tw-text-gray-500']">{{ lang.native }}</div>
                            </div>
                            
                            <!-- Check -->
                            <div v-if="selection.language === lang.id" class="tw-absolute tw-top-4 tw-right-4 tw-w-6 tw-h-6 tw-bg-[#E11D48] tw-text-white tw-rounded-full tw-flex tw-items-center tw-justify-center tw-shadow-lg">
                                <i data-lucide="check" class="tw-w-3.5 tw-h-3.5 tw-stroke-[3]"></i>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- 2. Level -->
                <div>
                    <h3 class="tw-text-2xl tw-font-bold tw-text-white tw-mb-6 tw-flex tw-items-center tw-gap-3">
                        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-text-sm">2</div>
                        CEFR Level
                    </h3>
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4">
                        <button v-for="level in levels" :key="level.id"
                                @click="selection.level = level.id"
                                :class="['tw-relative tw-p-6 tw-rounded-[1.5rem] tw-border tw-transition-all tw-duration-300 tw-ease-out tw-text-left tw-flex tw-flex-col group', 
                                         selection.level === level.id ? 'tw-border-[#12308E] tw-bg-gradient-to-br tw-from-[#12308E]/20 tw-to-transparent tw-shadow-[0_0_20px_rgba(18,48,142,0.2)]' : 'tw-border-white/5 tw-bg-white/[0.03] hover:tw-bg-white/[0.06] hover:tw-border-white/20']">
                            
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-3">
                                <span :class="['tw-font-bold tw-text-2xl', selection.level === level.id ? 'tw-text-white' : 'tw-text-white']">{{ level.name }}</span>
                                <div :class="['tw-w-3 tw-h-3 tw-rounded-full', selection.level === level.id ? 'tw-bg-[#12308E] tw-shadow-[0_0_8px_#12308E]' : 'tw-bg-white/20']"></div>
                            </div>
                            <div :class="['tw-font-semibold tw-mb-2', selection.level === level.id ? 'tw-text-[#60a5fa]' : 'tw-text-gray-300']">{{ level.title }}</div>
                            <div class="tw-text-sm tw-text-gray-400 tw-leading-relaxed">{{ level.desc }}</div>
                        </button>
                    </div>
                </div>

                <!-- 3. Format -->
                <div>
                    <h3 class="tw-text-2xl tw-font-bold tw-text-white tw-mb-6 tw-flex tw-items-center tw-gap-3">
                        <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-text-sm">3</div>
                        Learning Format
                    </h3>
                    <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-2 tw-gap-4">
                        <button v-for="format in formats" :key="format.id"
                                @click="selection.format = format.id; selection.duration = format.defaultDuration"
                                :class="['tw-relative tw-p-6 tw-rounded-[1.5rem] tw-border tw-transition-all tw-duration-300 tw-ease-out tw-text-left tw-flex tw-items-start tw-gap-5 group', 
                                         selection.format === format.id ? 'tw-border-white/30 tw-bg-white/10' : 'tw-border-white/5 tw-bg-white/[0.03] hover:tw-bg-white/[0.06] hover:tw-border-white/20']">
                            
                            <div :class="['tw-w-14 tw-h-14 tw-rounded-2xl tw-flex tw-items-center tw-justify-center tw-shrink-0 tw-transition-colors', selection.format === format.id ? 'tw-bg-white tw-text-black' : 'tw-bg-white/10 tw-text-gray-400 group-hover:tw-text-white']">
                                <i :data-lucide="format.icon" class="tw-w-6 tw-h-6"></i>
                            </div>
                            <div>
                                <div :class="['tw-font-bold tw-text-xl tw-mb-1', selection.format === format.id ? 'tw-text-white' : 'tw-text-gray-200']">{{ format.name }}</div>
                                <div class="tw-text-sm tw-text-gray-400">{{ format.desc }}</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- 4. Duration (Only for non-platform) -->
                <transition name="fade-up">
                    <div v-if="selection.format !== 'platform'">
                        <h3 class="tw-text-2xl tw-font-bold tw-text-white tw-mb-6 tw-flex tw-items-center tw-gap-3">
                            <div class="tw-w-8 tw-h-8 tw-rounded-full tw-bg-white/10 tw-flex tw-items-center tw-justify-center tw-text-sm">4</div>
                            Duration
                        </h3>
                        <div class="tw-grid tw-grid-cols-3 tw-gap-4">
                            <button v-for="dur in durations" :key="dur.id"
                                    @click="selection.duration = dur.id"
                                    :class="['tw-p-5 tw-rounded-[1.5rem] tw-border tw-transition-all tw-duration-300 tw-ease-out tw-text-center tw-font-bold', 
                                            selection.duration === dur.id ? 'tw-border-white/30 tw-bg-white/10 tw-text-white' : 'tw-border-white/5 tw-bg-white/[0.03] tw-text-gray-400 hover:tw-bg-white/[0.06] hover:tw-text-white hover:tw-border-white/20']">
                                {{ dur.name }}
                            </button>
                        </div>
                    </div>
                </transition>

            </div>

            <!-- Right: The "Apple Store" Sticky Cart -->
            <div class="tw-w-full lg:tw-w-[420px] tw-sticky tw-top-24 tw-shrink-0">
                <div class="tw-bg-[#0f111a] tw-border tw-border-white/10 tw-rounded-[2rem] tw-overflow-hidden tw-shadow-2xl">
                    
                    <div class="tw-p-8">
                        <h3 class="tw-text-sm tw-font-bold tw-text-gray-400 tw-uppercase tw-tracking-widest tw-mb-6">Your Package</h3>
                        
                        <!-- Dynamic Selections (Receipt style) -->
                        <div class="tw-space-y-4 tw-mb-8">
                            <!-- Course Selection -->
                            <div class="tw-flex tw-items-start tw-gap-4 tw-p-4 tw-bg-white/5 tw-rounded-2xl tw-border tw-border-white/5">
                                <img :src="currentLanguageFlag" class="tw-w-10 tw-h-8 tw-rounded tw-object-cover tw-shrink-0" />
                                <div class="tw-flex-1">
                                    <div class="tw-font-bold tw-text-white">{{ currentLanguageName }} {{ currentLevelName }}</div>
                                    <div class="tw-text-xs tw-text-gray-400">{{ currentFormatName }} <span v-if="selection.format !== 'platform'">â€¢ {{ currentDurationName }}</span></div>
                                </div>
                                <div class="tw-font-medium tw-text-white">
                                    {{ calculatedBasePrice }} RON
                                </div>
                            </div>
                        </div>

                        <!-- Add-ons -->
                        <div class="tw-mb-8">
                            <h4 class="tw-text-sm tw-font-bold tw-text-gray-400 tw-mb-4">Add-Ons</h4>
                            <div class="tw-space-y-3">
                                
                                <label class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-rounded-2xl tw-border tw-border-white/5 tw-bg-white/[0.02] tw-cursor-pointer hover:tw-bg-white/[0.04] tw-transition-colors">
                                    <div class="tw-flex-1">
                                        <div class="tw-font-bold tw-text-white tw-text-sm">1-on-1 Mentoring</div>
                                        <div class="tw-text-xs tw-text-gray-500">2h/week dedicated support</div>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-gap-4">
                                        <span class="tw-text-sm tw-font-medium tw-text-gray-300">+990 RON</span>
                                        <div :class="['tw-w-11 tw-h-6 tw-rounded-full tw-p-1 tw-transition-colors tw-duration-300 tw-ease-in-out', addons.mentoring ? 'tw-bg-[#E11D48]' : 'tw-bg-gray-600']">
                                            <input type="checkbox" v-model="addons.mentoring" class="tw-sr-only">
                                            <div :class="['tw-w-4 tw-h-4 tw-bg-white tw-rounded-full tw-shadow-sm tw-transition-transform tw-duration-300 tw-ease-in-out', addons.mentoring ? 'tw-translate-x-5' : 'tw-translate-x-0']"></div>
                                        </div>
                                    </div>
                                </label>

                                <label class="tw-flex tw-items-center tw-justify-between tw-p-4 tw-rounded-2xl tw-border tw-border-white/5 tw-bg-white/[0.02] tw-cursor-pointer hover:tw-bg-white/[0.04] tw-transition-colors">
                                    <div class="tw-flex-1">
                                        <div class="tw-font-bold tw-text-white tw-text-sm">Premium Certificate</div>
                                        <div class="tw-text-xs tw-text-gray-500">Physical copy sent globally</div>
                                    </div>
                                    <div class="tw-flex tw-items-center tw-gap-4">
                                        <span class="tw-text-sm tw-font-medium tw-text-gray-300">+190 RON</span>
                                        <div :class="['tw-w-11 tw-h-6 tw-rounded-full tw-p-1 tw-transition-colors tw-duration-300 tw-ease-in-out', addons.certificate ? 'tw-bg-[#E11D48]' : 'tw-bg-gray-600']">
                                            <input type="checkbox" v-model="addons.certificate" class="tw-sr-only">
                                            <div :class="['tw-w-4 tw-h-4 tw-bg-white tw-rounded-full tw-shadow-sm tw-transition-transform tw-duration-300 tw-ease-in-out', addons.certificate ? 'tw-translate-x-5' : 'tw-translate-x-0']"></div>
                                        </div>
                                    </div>
                                </label>

                            </div>
                        </div>

                        <!-- Total -->
                        <div class="tw-border-t tw-border-white/10 tw-pt-6 tw-mb-6">
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-2">
                                <span class="tw-text-gray-400">Total</span>
                                <div class="tw-flex tw-items-baseline tw-gap-1">
                                    <span class="tw-text-4xl tw-font-display tw-font-bold tw-text-white">{{ totalPrice }}</span>
                                    <span class="tw-text-lg tw-text-gray-400">RON</span>
                                </div>
                            </div>
                            <div class="tw-text-right tw-text-xs tw-text-[#10B981] tw-font-medium" v-if="addons.mentoring || addons.certificate">
                                Includes add-ons
                            </div>
                        </div>

                        <button class="tw-w-full tw-py-4 tw-bg-white hover:tw-bg-gray-200 tw-text-black tw-rounded-xl tw-font-bold tw-text-[15px] tw-tracking-wide tw-transition-colors tw-flex tw-items-center tw-justify-center tw-gap-2">
                            <?php echo esc_html($ui_settings['cta']); ?>
                        </button>
                    </div>

                    <!-- Features -->
                    <div class="tw-bg-black/30 tw-p-6">
                        <ul class="tw-space-y-3 tw-text-sm tw-text-gray-400">
                            <?php foreach ($ui_settings['features'] as $feature) : ?>
                            <li class="tw-flex tw-items-center tw-gap-3">
                                <i data-lucide="check-circle-2" class="tw-w-4 tw-h-4 tw-text-[#E11D48]"></i>
                                <?php echo esc_html($feature); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* Animations */
.scale-in-enter-active, .scale-in-leave-active { transition: all 0.3s cubic-bezier(0.32, 0.72, 0, 1); }
.scale-in-enter-from, .scale-in-leave-to { opacity: 0; transform: scale(0.5); }
.fade-up-enter-active, .fade-up-leave-active { transition: all 0.4s cubic-bezier(0.32, 0.72, 0, 1); }
.fade-up-enter-from, .fade-up-leave-to { opacity: 0; transform: translateY(10px); }
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

<!-- Include Vue 3 & Lucide -->
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const { createApp, ref, computed, watch, nextTick } = Vue;
    
    // Inject pricing data from PHP
    const pricingData = <?php echo $encoded_pricing; ?>;
    const uiSettings = <?php echo $encoded_ui; ?>;
    
    createApp({
        setup() {
            const languages = [
                { id: 'en', name: 'English', native: 'English', flag: 'https://flagcdn.com/w80/gb.png' },
                { id: 'ro', name: 'Romanian', native: 'RomÃ¢nÄƒ', flag: 'https://flagcdn.com/w80/ro.png' },
                { id: 'jp', name: 'Japanese', native: 'æ—¥æœ¬èªž', flag: 'https://flagcdn.com/w80/jp.png' }
            ];

            const levels = [
                { id: 'a1_a2', name: 'A1-A2', title: 'Beginner / Elem.', desc: 'Basic communication and everyday situations.', color: 'tw-bg-[#10B981]' },
                { id: 'b1_b2', name: 'B1-B2', title: 'Intermediate', desc: 'Express yourself with confidence.', color: 'tw-bg-[#3B82F6]' },
                { id: 'c1_c2', name: 'C1-C2', title: 'Advanced', desc: 'Mastery and near-native fluency.', color: 'tw-bg-[#8B5CF6]' }
            ];

            const formats = [
                { id: 'platform', name: 'Platform Only', desc: 'Self-paced learning on our advanced LMS.', icon: 'monitor', defaultDuration: null },
                { id: 'individual', name: '1-on-1 Sessions', desc: 'Zoom sessions with a dedicated tutor.', icon: 'user', defaultDuration: '1_month' },
                { id: 'group', name: 'Group Cohort', desc: 'Small groups (3-8 students).', icon: 'users', defaultDuration: '1_month' },
                { id: 'corporate', name: 'Corporate', desc: 'B2B custom plans for teams and employees.', icon: 'building-2', defaultDuration: '1_month' }
            ];

            const durations = [
                { id: '1_month', name: '1 Month' },
                { id: '3_months', name: '3 Months' },
                { id: '6_months', name: '6 Months' }
            ];

            const selection = ref({
                language: 'en',
                level: 'b1_b2',
                format: 'platform',
                duration: null
            });

            const addons = ref({
                mentoring: false,
                certificate: false
            });

            const currentLanguageName = computed(() => languages.find(l => l.id === selection.value.language)?.name || '');
            const currentLanguageFlag = computed(() => languages.find(l => l.id === selection.value.language)?.flag || '');
            const currentLevelName = computed(() => levels.find(l => l.id === selection.value.level)?.name || '');
            const currentFormatName = computed(() => formats.find(f => f.id === selection.value.format)?.name || '');
            const currentDurationName = computed(() => {
                if(selection.value.format === 'platform') return '-';
                return durations.find(d => d.id === selection.value.duration)?.name || '';
            });

            const calculatedBasePrice = computed(() => {
                try {
                    let price = 0;
                    const format = selection.value.format;
                    const level = selection.value.level;
                    const lang = selection.value.language;
                    
                    if (format === 'platform') {
                        price = pricingData.platform[level][lang];
                    } else if (format === 'individual' || format === 'corporate' || format === 'group') {
                        const duration = selection.value.duration || '1_month';
                        if (pricingData[format] && pricingData[format][level] && pricingData[format][level][duration]) {
                            price = pricingData[format][level][duration][lang];
                        } else {
                            // Fallback
                            price = (pricingData['individual'][level][duration][lang] * 0.7) || 0;
                        }
                    }
                    return Math.round(price) || 0;
                } catch (e) {
                    return 0;
                }
            });

            const totalPrice = computed(() => {
                let total = calculatedBasePrice.value;
                if (addons.value.mentoring) total += 990;
                if (addons.value.certificate) total += 190;
                return total;
            });

            watch(selection, () => {
                nextTick(() => {
                    if (window.lucide) lucide.createIcons();
                });
            }, { deep: true });

            watch(addons, () => {
            }, { deep: true });

            // Init icons on mount
            nextTick(() => { if (window.lucide) lucide.createIcons(); });

            return {
                languages, levels, formats, durations, selection, addons,
                currentLanguageName, currentLanguageFlag, currentLevelName, currentFormatName, currentDurationName,
                calculatedBasePrice, totalPrice
            }
        }
    }).mount('#rima-packages-app');
});
</script>

<!-- THE ACADEMY EXPERIENCE (BENTO GRID) -->
<section class="tw-bg-[#030408] tw-py-20 tw-font-sans tw-relative">
    <div class="tw-max-w-7xl tw-mx-auto tw-px-4 sm:tw-px-6 tw-relative tw-z-10">
        
        <div class="tw-mb-12 tw-text-center">
            <h2 class="tw-text-4xl tw-font-display tw-font-extrabold tw-text-white tw-tracking-tight tw-mb-4">The Academy Experience</h2>
            <p class="tw-text-gray-400 tw-text-lg">Premium learning features included in every plan.</p>
        </div>

        <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-6 tw-auto-rows-[300px]">
            
            <!-- Large Card (Span 2) -->
            <div class="md:tw-col-span-2 tw-relative tw-rounded-[2rem] tw-overflow-hidden tw-group">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1000&auto=format&fit=crop" class="tw-absolute tw-inset-0 tw-w-full tw-h-full tw-object-cover tw-transition-transform tw-duration-700 group-hover:tw-scale-105" />
                <div class="tw-absolute tw-inset-0 tw-bg-gradient-to-t tw-from-[#030408] tw-via-[#030408]/60 tw-to-transparent"></div>
                <div class="tw-absolute tw-bottom-0 tw-left-0 tw-p-8">
                    <div class="tw-w-12 tw-h-12 tw-bg-[#E11D48] tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mb-4">
                        <i data-lucide="video" class="tw-w-6 tw-h-6 tw-text-white"></i>
                    </div>
                    <h3 class="tw-text-2xl tw-font-bold tw-text-white tw-mb-2">Immersive Live Sessions</h3>
                    <p class="tw-text-gray-300 tw-max-w-md">Connect directly with native speakers and expert tutors in high-quality interactive environments.</p>
                </div>
            </div>

            <!-- Small Card -->
            <div class="tw-relative tw-rounded-[2rem] tw-overflow-hidden tw-bg-[#0f111a] tw-border tw-border-white/5 hover:tw-border-white/20 tw-transition-colors tw-group">
                <div class="tw-p-8 tw-flex tw-flex-col tw-h-full tw-justify-end">
                    <div class="tw-w-12 tw-h-12 tw-bg-[#12308E] tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mb-auto tw-shrink-0">
                        <i data-lucide="activity" class="tw-w-6 tw-h-6 tw-text-white"></i>
                    </div>
                    <div>
                        <h3 class="tw-text-xl tw-font-bold tw-text-white tw-mb-2">Advanced Analytics</h3>
                        <p class="tw-text-sm tw-text-gray-400">Track your fluency progress in real-time with our proprietary AI dashboard.</p>
                    </div>
                </div>
            </div>

            <!-- Small Card -->
            <div class="tw-relative tw-rounded-[2rem] tw-overflow-hidden tw-bg-[#0f111a] tw-border tw-border-white/5 hover:tw-border-white/20 tw-transition-colors tw-group">
                <div class="tw-p-8 tw-flex tw-flex-col tw-h-full tw-justify-end">
                    <div class="tw-w-12 tw-h-12 tw-bg-[#10B981] tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mb-auto tw-shrink-0">
                        <i data-lucide="file-check-2" class="tw-w-6 tw-h-6 tw-text-white"></i>
                    </div>
                    <div>
                        <h3 class="tw-text-xl tw-font-bold tw-text-white tw-mb-2">Detailed Feedback</h3>
                        <p class="tw-text-sm tw-text-gray-400">Receive precise corrections on pronunciation, grammar, and vocabulary.</p>
                    </div>
                </div>
            </div>

            <!-- Large Card (Span 2) -->
            <div class="md:tw-col-span-2 tw-relative tw-rounded-[2rem] tw-overflow-hidden tw-group">
                <img src="https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?q=80&w=1000&auto=format&fit=crop" class="tw-absolute tw-inset-0 tw-w-full tw-h-full tw-object-cover tw-transition-transform tw-duration-700 group-hover:tw-scale-105" />
                <div class="tw-absolute tw-inset-0 tw-bg-gradient-to-t tw-from-[#030408] tw-via-[#030408]/60 tw-to-transparent"></div>
                <div class="tw-absolute tw-bottom-0 tw-left-0 tw-p-8">
                    <div class="tw-w-12 tw-h-12 tw-bg-white/10 tw-backdrop-blur-md tw-rounded-full tw-flex tw-items-center tw-justify-center tw-mb-4 tw-border tw-border-white/20">
                        <i data-lucide="globe-2" class="tw-w-6 tw-h-6 tw-text-white"></i>
                    </div>
                    <h3 class="tw-text-2xl tw-font-bold tw-text-white tw-mb-2">Global Community</h3>
                    <p class="tw-text-gray-300 tw-max-w-md">Join thousands of professionals mastering new languages and expanding their horizons daily.</p>
                </div>
            </div>

        </div>
    </div>
</section>

