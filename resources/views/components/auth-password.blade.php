@props([
'name' => 'password',
'label' => 'Password',
'minLength' => 8,
])

<div
    x-data="{
        value: '',
        show: false,
        get rules() {
            return {
                length: this.value.length >= {{ $minLength }},
                upper: /[A-Z]/.test(this.value),
                lower: /[a-z]/.test(this.value),
                number: /[0-9]/.test(this.value),
                special: /[^A-Za-z0-9]/.test(this.value),
            }
        },
        get score() { return Object.values(this.rules).filter(Boolean).length },
        get strength() {
            const s = this.score
            if (! this.value) return { color: '', width: '0%' }
            if (s <= 2) return { color: 'bg-red-500', width: '25%' }
            if (s === 3) return { color: 'bg-yellow-500', width: '50%' }
            if (s === 4) return { color: 'bg-[#A48395]', width: '75%' }
            return { color: 'bg-green-600', width: '100%' }
        }
    }">
    <div class="relative">
        <input
            :type="show ? 'text' : 'password'"
            id="{{ $name }}"
            name="{{ $name }}"
            x-model="value"
            placeholder=" "
            required
            autocomplete="new-password"
            {{ $attributes->merge(['class' => 'peer block w-full px-3.5 pt-5 pb-1.5 pr-10 text-sm text-gray-900 bg-white border border-gray-300 rounded-md focus:border-bordeaux focus:ring-1 focus:ring-bordeaux focus:outline-none']) }}>
        <label for="{{ $name }}"
            class="absolute left-3.5 top-3.5 text-gray-500 text-sm transition-all duration-150 ease-out pointer-events-none
                      peer-focus:top-1.5 peer-focus:text-[10px] peer-focus:text-bordeaux
                      peer-[:not(:placeholder-shown)]:top-1.5 peer-[:not(:placeholder-shown)]:text-[10px]
                      peer-[:not(:placeholder-shown)]:text-bordeaux">
            {{ $label }}
        </label>

        <button type="button" @click="show = ! show"
            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 p-1" tabindex="-1">
            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            </svg>
        </button>
    </div>

    <div x-show="value.length > 0" x-cloak class="mt-1.5 space-y-1">
        <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full transition-all duration-300 rounded-full"
                :class="strength.color"
                :style="`width: ${strength.width}`"></div>
        </div>

        <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-[10px] leading-tight text-gray-400">
            <span :class="rules.length ? 'text-green-600' : ''">
                <span x-text="rules.length ? '✓' : '○'"></span> {{ $minLength }}+
            </span>
            <span :class="rules.upper ? 'text-green-600' : ''">
                <span x-text="rules.upper ? '✓' : '○'"></span> A-Z
            </span>
            <span :class="rules.lower ? 'text-green-600' : ''">
                <span x-text="rules.lower ? '✓' : '○'"></span> a-z
            </span>
            <span :class="rules.number ? 'text-green-600' : ''">
                <span x-text="rules.number ? '✓' : '○'"></span> 0-9
            </span>
            <span :class="rules.special ? 'text-green-600' : ''">
                <span x-text="rules.special ? '✓' : '○'"></span> !@#
            </span>
        </div>
    </div>
</div>