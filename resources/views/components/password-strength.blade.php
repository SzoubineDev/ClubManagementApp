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
        get score() {
            return Object.values(this.rules).filter(Boolean).length
        },
        get strength() {
            const s = this.score
            if (! this.value) return { label: '', color: '', width: '0%' }
            if (s <= 2) return { label: 'Weak', color: 'bg-red-500', width: '25%' }
            if (s === 3) return { label: 'Fair', color: 'bg-yellow-500', width: '50%' }
            if (s === 4) return { label: 'Good', color: 'bg-[#A48395]', width: '75%' }
            return { label: 'Strong', color: 'bg-green-600', width: '100%' }
        },
        get valid() {
            return this.score === 5
        }
    }"
    class="space-y-2">
    <div class="relative">
        <input
            :type="show ? 'text' : 'password'"
            id="{{ $name }}"
            name="{{ $name }}"
            x-model="value"
            placeholder=" "
            required
            autocomplete="new-password"
            {{ $attributes->merge(['class' => 'peer block w-full px-4 pt-6 pb-2 pr-12 text-bordeaux bg-white border border-blush rounded-xl shadow-sm focus:border-bordeaux focus:ring-2 focus:ring-bordeaux/20 focus:outline-none transition-colors']) }}>

        <label
            for="{{ $name }}"
            class="absolute left-4 top-4 text-mauve text-base transition-all duration-150 ease-out pointer-events-none
                   peer-focus:top-2 peer-focus:text-xs peer-focus:text-bordeaux
                   peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:text-xs
                   peer-[:not(:placeholder-shown)]:text-bordeaux">
            {{ $label }}
        </label>

        <button type="button"
            @click="show = ! show"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-mauve hover:text-bordeaux p-1"
            tabindex="-1">
            <svg x-show="! show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            </svg>
        </button>
    </div>

    {{-- Strength bar --}}
    <div class="flex items-center gap-2" x-show="value.length > 0" x-cloak>
        <div class="flex-1 h-1.5 bg-blush rounded-full overflow-hidden">
            <div class="h-full transition-all duration-300 rounded-full"
                :class="strength.color"
                :style="`width: ${strength.width}`"></div>
        </div>
        <span class="text-xs font-medium text-mauve w-14 text-right" x-text="strength.label"></span>
    </div>

    {{-- Rules checklist --}}
    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-1 mt-1" x-show="value.length > 0" x-cloak>
        <template x-for="(passed, key) in rules" :key="key">
            <li class="flex items-center gap-2 text-xs"
                :class="passed ? 'text-green-600' : 'text-mauve'">
                <span class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0"
                    :class="passed ? 'bg-green-100 text-green-700' : 'bg-blush text-mauve'">
                    <svg x-show="passed" class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg x-show="! passed" class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </span>
                <span x-text="{
                    length: 'At least {{ $minLength }} characters',
                    upper: 'One uppercase letter',
                    lower: 'One lowercase letter',
                    number: 'One number',
                    special: 'One special character',
                }[key]"></span>
            </li>
        </template>
    </ul>
</div>