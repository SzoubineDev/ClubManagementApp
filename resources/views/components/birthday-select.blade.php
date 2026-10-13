@props([
'name' => 'birthday',
'label' => 'Birthday',
'selected' => null,
])

@php
$parts = $selected ? explode('-', $selected) : null;
$selYear = old('birthday_year', $parts ? intval($parts[0]) : null);
$selMonth = old('birthday_month', $parts ? intval($parts[1]) : null);
$selDay = old('birthday_day', $parts ? intval($parts[2]) : null);
@endphp

<div x-data="{
        year: {{ $selYear ?? 'null' }},
        month: {{ $selMonth ?? 'null' }},
        day: {{ $selDay ?? 'null' }},
        daysInMonth() {
            if (!this.month || !this.year) return 31;
            return new Date(this.year, this.month, 0).getDate();
        },
        sync() {
            const el = this.$refs.composed;
            if (el) el.value = (this.year && this.month && this.day)
                ? `${this.year}-${String(this.month).padStart(2,'0')}-${String(this.day).padStart(2,'0')}`
                : '';
        }
     }"
     x-init="$watch('year', () => { sync() }); $watch('month', () => { sync() }); $watch('day', () => { sync() })">

    <label class="field-caption">{{ $label }}</label>

    {{-- Hidden composed value submitted to the server --}}
    <input type="hidden" x-ref="composed" name="{{ $name }}" x-init="sync()" {{ $attributes->only(['required']) }}>

    <div class="date-trio">
        <select class="field-select" x-model.number="year" aria-label="Year">
            <option value="" disabled selected>Year</option>
            @foreach (range((int) date('Y') - 14, (int) date('Y') - 80) as $y)
            <option value="{{ $y }}" @if ($selYear === $y) selected @endif>{{ $y }}</option>
            @endforeach
        </select>

        <select class="field-select" x-model.number="month" aria-label="Month">
            <option value="" disabled selected>Month</option>
            @foreach ([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'] as $m => $label)
            <option value="{{ $m }}" @if ($selMonth === $m) selected @endif>{{ $label }}</option>
            @endforeach
        </select>

        <select class="field-select" x-model.number="day" aria-label="Day">
            <option value="" disabled selected>Day</option>
            <template x-for="d in daysInMonth()">
                <option :value="d" x-text="d"></option>
            </template>
        </select>
    </div>

    <p class="field-hint">You must be at least 14 years old to join.</p>
</div>
