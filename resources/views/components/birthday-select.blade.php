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

@php
// The server validates birthday_day / birthday_month / birthday_year, so the
// selects themselves must carry those names. A hidden "birthday" input is kept
// for controllers that expect a composed Y-m-d value.
@endphp

<div x-data="{
        year: {{ $selYear ?? 'null' }},
        month: {{ $selMonth ?? 'null' }},
        day: {{ $selDay ?? 'null' }},
        maxDay() {
            if (!this.month || !this.year) return 31;
            return new Date(this.year, this.month, 0).getDate();
        },
        sync() {
            // Clamp the day when the selected month has fewer days (e.g. Feb 30)
            if (this.day > this.maxDay()) this.day = this.maxDay();

            const el = this.$refs.composed;
            if (el) el.value = (this.year && this.month && this.day)
                ? `${this.year}-${String(this.month).padStart(2,'0')}-${String(this.day).padStart(2,'0')}`
                : '';
        }
     }">

    <label class="field-caption">{{ $label }}</label>

    {{-- Hidden composed value (Y-m-d) for endpoints that expect `birthday` --}}
    <input type="hidden" x-ref="composed" name="birthday" x-init="sync()">

    <div class="date-trio">
        <select class="field-select" name="birthday_year" x-model.number="year"
            x-init="$watch('year', () => sync()); $watch('month', () => sync()); $watch('day', () => sync())"
            aria-label="Year" required>
            <option value="" disabled selected>Year</option>
            @foreach (range((int) date('Y') - 14, (int) date('Y') - 80) as $y)
            <option value="{{ $y }}" @if ($selYear === $y) selected @endif>{{ $y }}</option>
            @endforeach
        </select>

        <select class="field-select" name="birthday_month" x-model.number="month" aria-label="Month" required>
            <option value="" disabled selected>Month</option>
            @foreach ([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'] as $m => $mlabel)
            <option value="{{ $m }}" @if ($selMonth === $m) selected @endif>{{ $mlabel }}</option>
            @endforeach
        </select>

        <select class="field-select" name="birthday_day" x-model.number="day" aria-label="Day" required>
            <option value="" disabled selected>Day</option>
            {{-- Rendered by Blade so old() values survive failed validation --}}
            @foreach (range(1, 31) as $d)
            <option value="{{ $d }}" x-show="!maxDay || {{ $d }} <= maxDay()"
                @if ($selDay === $d) selected @endif>{{ $d }}</option>
            @endforeach
        </select>
    </div>

    <p class="field-hint">You must be at least 14 years old to join.</p>
</div>
