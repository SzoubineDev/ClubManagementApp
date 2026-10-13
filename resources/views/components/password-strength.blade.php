@props([
'name' => 'password',
'label' => 'Password',
'id' => null,
'rules' => true,
])

@php $id = $id ?? $name; @endphp

<div x-data="{
        show: false,
        value: @js(old($name)),
        score() {
            const v = this.value || ''
            let s = 0
            if (v.length >= 8) s++
            if (/[a-z]/.test(v) && /[A-Z]/.test(v)) s++
            if (/\d/.test(v)) s++
            if (/[^A-Za-z0-9]/.test(v)) s++
            return s
        },
        label(s) { return ['Too short', 'Weak', 'Fair', 'Good', 'Strong'][s] },
        color(s) { return ['#DC2626','#DC2626','#F5A623','#E76D89','#0E7C7B'][s] },
     }"
     class="stack--sm">

    <div class="field">
        <input :type="show ? 'text' : 'password'"
            id="{{ $id }}"
            name="{{ $name }}"
            x-model="value"
            placeholder=" "
            autocomplete="new-password"
            {{ $attributes->only(['required', 'autofocus']) }}
            class="field-input">
        <label class="field-label" for="{{ $id }}">{{ $label }}</label>

        <button type="button" class="field-affix" @click="show = !show" :aria-label="show ? 'Hide password' : 'Show password'">
            <svg x-show="!show" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <svg x-show="show" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3.98 8.223A10.477 10.477 0 002.358 12c1.262 3.282 3.994 5.5 9.642 5.5 1.805 0 3.377-.34 4.64-.926M9.879 4.558A11.7 11.7 0 0112 4.5c5.648 0 8.38 2.218 9.642 5.5a10.9 10.9 0 01-2.13 3.346M3 3l18 18M9.88 9.88a3 3 0 104.24 4.24" />
            </svg>
        </button>
    </div>

    {{-- Strength meter --}}
    @if ($rules)
    <div>
        <div class="flex items-center justify-between mb-2">
            <span class="meter" style="flex:1;">
                <span class="meter-fill"
                    :style="'width:' + ((value || '').length === 0 ? 0 : Math.max(12.5, (score() / 4) * 100)) + '%; background:' + color(score())"></span>
            </span>
            <span class="text-xs font-semibold ml-3" :style="'color:' + color(score())"
                x-text="(value || '').length === 0 ? '' : label(score())"></span>
        </div>

        <ul class="rule-list">
            <template x-for="rule in [
                { text: 'At least 8 characters', ok: (value || '').length >= 8 },
                { text: 'Upper & lowercase letters', ok: /[a-z]/.test(value || '') && /[A-Z]/.test(value || '') },
                { text: 'At least one number', ok: /\d/.test(value || '') },
                { text: 'At least one symbol', ok: /[^A-Za-z0-9]/.test(value || '') },
            ]">
                <li class="rule-item" :class="rule.ok && 'is-pass'">
                    <span class="rule-dot">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <span x-text="rule.text"></span>
                </li>
            </template>
        </ul>
    </div>
    @endif
</div>
