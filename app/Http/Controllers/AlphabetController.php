<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AlphabetController extends Controller
{
    /**
     * @return array<int, array{char:string, name:string, latin:?string}>
     */
    private function englishAlphabet(): array
    {
        return array_map(fn (string $letter): array => [
            'char' => $letter,
            'name' => strtolower($letter),
            'latin' => null,
        ], range('A', 'Z'));
    }

    /**
     * @return array<int, array{char:string, name:string, latin:?string}>
     */
    private function arabicAlphabet(): array
    {
        return [
            ['char' => 'ا', 'name' => 'ألف', 'latin' => 'alif'],
            ['char' => 'ب', 'name' => 'باء', 'latin' => 'bāʾ'],
            ['char' => 'ت', 'name' => 'تاء', 'latin' => 'tāʾ'],
            ['char' => 'ث', 'name' => 'ثاء', 'latin' => 'thāʾ'],
            ['char' => 'ج', 'name' => 'جيم', 'latin' => 'jīm'],
            ['char' => 'ح', 'name' => 'حاء', 'latin' => 'ḥāʾ'],
            ['char' => 'خ', 'name' => 'خاء', 'latin' => 'khāʾ'],
            ['char' => 'د', 'name' => 'دال', 'latin' => 'dāl'],
            ['char' => 'ذ', 'name' => 'ذال', 'latin' => 'dhāl'],
            ['char' => 'ر', 'name' => 'راء', 'latin' => 'rāʾ'],
            ['char' => 'ز', 'name' => 'زاي', 'latin' => 'zāy'],
            ['char' => 'س', 'name' => 'سين', 'latin' => 'sīn'],
            ['char' => 'ش', 'name' => 'شين', 'latin' => 'shīn'],
            ['char' => 'ص', 'name' => 'صاد', 'latin' => 'ṣād'],
            ['char' => 'ض', 'name' => 'ضاد', 'latin' => 'ḍād'],
            ['char' => 'ط', 'name' => 'طاء', 'latin' => 'ṭāʾ'],
            ['char' => 'ظ', 'name' => 'ظاء', 'latin' => 'ẓāʾ'],
            ['char' => 'ع', 'name' => 'عين', 'latin' => 'ʿayn'],
            ['char' => 'غ', 'name' => 'غين', 'latin' => 'ghayn'],
            ['char' => 'ف', 'name' => 'فاء', 'latin' => 'fāʾ'],
            ['char' => 'ق', 'name' => 'قاف', 'latin' => 'qāf'],
            ['char' => 'ك', 'name' => 'كاف', 'latin' => 'kāf'],
            ['char' => 'ل', 'name' => 'لام', 'latin' => 'lām'],
            ['char' => 'م', 'name' => 'ميم', 'latin' => 'mīm'],
            ['char' => 'ن', 'name' => 'نون', 'latin' => 'nūn'],
            ['char' => 'ه', 'name' => 'هاء', 'latin' => 'hāʾ'],
            ['char' => 'و', 'name' => 'واو', 'latin' => 'wāw'],
            ['char' => 'ي', 'name' => 'ياء', 'latin' => 'yāʾ'],
        ];
    }

    public function index(): View
    {
        $english = $this->englishAlphabet();
        $arabic = $this->arabicAlphabet();

        return view('alphabets', [
            'englishLetters' => $english,
            'arabicLetters' => $arabic,
            'englishTotal' => count($english),
            'arabicTotal' => count($arabic),
        ]);
    }
}
