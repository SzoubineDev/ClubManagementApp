@props([
'dayName' => 'birthday_day',
'monthName' => 'birthday_month',
'yearName' => 'birthday_year',
'label' => 'Birthday',
'dayValue' => null,
'monthValue' => null,
'yearValue' => null,
])

@php
$oldDay = old($dayName, $dayValue);
$oldMonth = old($monthName, $monthValue);
$oldYear = old($yearName, $yearValue);

$months = [
1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
];
$currentYear = (int) date('Y');
$years = range($currentYear - 15, $currentYear - 60);
@endphp

<div
    x-data="{
        month: '{{ $oldMonth }}',
        year: '{{ $oldYear }}',
        get daysInMonth() {
            if (! this.month || ! this.year) return 31
            return new Date(parseInt(this.year), parseInt(this.month), 0).getDate()
        }
    }"
    class="space-y-1">
    <label class="block font-medium text-sm text-bordeaux">{{ $label }}</label>

    <div class="grid grid-cols-3 gap-1.5">

        {{-- Day --}}
        <div class="relative">
            <select
                name="{{ $dayName }}"
                class="appearance-none w-full px-3 py-2 pr-8  text-bordeaux bg-white border border-blush rounded-xl shadow-sm focus:border-bordeaux focus:ring-2 focus:ring-bordeaux/20 focus:outline-none transition-colors cursor-pointer text-base">
                <option value="">Day</option>
                <template x-for="d in daysInMonth" :key="d">
                    <option :value="d" x-text="String(d).padStart(2, '0')" :selected="d == '{{ $oldDay }}'"></option>
                </template>
            </select>
            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-mauve" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        {{-- Month --}}
        <div class="relative">
            <select
                name="{{ $monthName }}"
                x-model="month"
                class="appearance-none w-full px-4 py-3 pr-10 text-bordeaux bg-white border border-blush rounded-xl shadow-sm focus:border-bordeaux focus:ring-2 focus:ring-bordeaux/20 focus:outline-none transition-colors cursor-pointer text-base">
                <option value="">Month</option>
                @foreach ($months as $num => $name)
                <option value="{{ $num }}" @selected($oldMonth==$num)>{{ $name }}</option>
                @endforeach
            </select>
            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-mauve" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        {{-- Year --}}
        <div class="relative">
            <select
                name="{{ $yearName }}"
                x-model="year"
                class="appearance-none w-full px-4 py-3 pr-10 text-bordeaux bg-white border border-blush rounded-xl shadow-sm focus:border-bordeaux focus:ring-2 focus:ring-bordeaux/20 focus:outline-none transition-colors cursor-pointer text-base">
                <option value="">Year</option>
                @foreach ($years as $y)
                <option value="{{ $y }}" @selected($oldYear==$y)>{{ $y }}</option>
                @endforeach
            </select>
            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-mauve" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
</div>