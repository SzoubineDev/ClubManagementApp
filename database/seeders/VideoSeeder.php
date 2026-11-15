<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Media library: 100 hand-picked YouTube videos covering
     * self-improvement, motivation, growth mindset, productivity,
     * brainstorming, creativity, public speaking, debate, and English
     * learning.
     *
     * Each entry: [youtube id, title, channel, category].
     *
     * The media page shows 10 random picks per reload, so a library this
     * size keeps the feed fresh. The media manager can still add, edit,
     * and remove entries from the Media page.
     *
     * @var array<int, array{0: string, 1: string, 2: string, 3: string}>
     */
    private const VIDEOS = [
        // ---- Self-improvement & motivation (25) --------------------------
        ['iCvmsMzlF7o', 'The Power of Vulnerability | Brené Brown | TED', 'TED', 'Self-improvement & motivation'],
        ['c0KYU2j0TM4', 'The Power of Introverts | Susan Cain | TED', 'TED', 'Self-improvement & motivation'],
        ['-moW9jvvMr4', 'A Simple Way to Break a Bad Habit | Judson Brewer | TED', 'TED', 'Self-improvement & motivation'],
        ['gUV5DJb6KGs', 'How to talk to the worst parts of yourself | Karen Faith | TEDxKC', 'TEDx Talks', 'Self-improvement & motivation'],
        ['LNHBMFCzznE', 'After watching this, your brain will not be the same | Lara Boyd | TEDxVancouver', 'TEDx Talks', 'Self-improvement & motivation'],
        ['TQMbvJNRpLE', 'How to Achieve Your Most Ambitious Goals | Stephen Duneier | TEDxTucson', 'TEDx Talks', 'Self-improvement & motivation'],
        ['v1ojZKWfShQ', 'How To Eliminate Self Doubt Forever & The Power of Your Unconscious Mind | Peter Sage', 'TEDx Talks', 'Self-improvement & motivation'],
        ['rNxC16mlO60', 'The Secrets and Science of Mental Toughness | Joe Risser', 'TEDx Talks', 'Self-improvement & motivation'],
        ['tTb3d5cjSFI', 'The secret to self control | Jonathan Bricker | TEDxRainier', 'TEDx Talks', 'Self-improvement & motivation'],
        ['w-HYZv6HzAs', 'The skill of self confidence | Dr. Ivan Joseph | TEDxRyersonU', 'TEDx Talks', 'Self-improvement & motivation'],
        ['g85WsxE1gAU', 'How Long It Takes To Change Your Life? | Nwal Hadaki | TEDxSafirSchool', 'TEDx Talks', 'Self-improvement & motivation'],
        ['LnJwH_PZXnM', 'How not to take things personally? | Frederik Imbo | TEDxMechelen', 'TEDx Talks', 'Self-improvement & motivation'],
        ['8KkKuTCFvzI', 'What Makes a Good Life? Lessons from the Longest Study on Happiness | Robert Waldinger | TED', 'TED', 'Self-improvement & motivation'],
        ['qYvXk_bqlBk', 'Who are you, really? The puzzle of personality | Brian Little | TED', 'TED', 'Self-improvement & motivation'],
        ['eBz7iUJu9UM', 'How to overcome your mistakes', 'TED-Ed', 'Self-improvement & motivation'],
        ['iNyUmbmQQZg', 'Is it normal to talk to yourself?', 'TED-Ed', 'Self-improvement & motivation'],
        ['tbnzAVRZ9Xc', 'WATCH THIS EVERYDAY AND CHANGE YOUR LIFE - Denzel Washington Motivational Speech', 'AlexKaltsMotivation', 'Motivation'],
        ['JjvN_hYDp3g', 'KILL YOUR EXCUSES - Motivational Speech', 'Ben Lionel Scott', 'Motivation'],
        ['0cwtNOq_k4w', 'WHEN LIFE BREAKS YOU - Powerful Motivational Speech', 'Ben Lionel Scott', 'Motivation'],
        ['TBuIGBCF9jc', 'Admiral McRaven Leaves the Audience SPEECHLESS', 'MotivationHub', 'Motivation'],
        ['wnHW6o8WMas', 'NO EXCUSES - Best Motivational Video', 'Ben Lionel Scott', 'Motivation'],
        ['zzfREEPbUsA', 'WHEN IT HURTS - Powerful Motivational Speech', 'Ben Lionel Scott', 'Motivation'],
        ['tEt-Gsd_2S8', "IT'S TIME TO PUSH YOURSELF AND MAKE IT HAPPEN", 'Absolute Motivation', 'Motivation'],
        ['qp0HIF3SfI4', 'How Great Leaders Inspire Action | Simon Sinek', 'TED', 'Self-improvement & motivation'],
        ['PGAUdWfB8nLg', "Barack Obama's Inspirational Speech with Subtitles", 'English Motivational Videos', 'Motivation'],

        // ---- Growth mindset & productivity (15) --------------------------
        ['_X0mgOOSpLU', 'The power of believing that you can improve | Carol Dweck | TED', 'TED', 'Growth mindset'],
        ['H14bBuluwB8', 'Grit: The Power of Passion and Perseverance | Angela Lee Duckworth | TED', 'TED', 'Growth mindset'],
        ['hiiEeMN7vbQ', 'Developing a Growth Mindset with Carol Dweck', 'Stanford Alumni', 'Growth mindset'],
        ['px9CzSZsa0Y', 'The Mindset of a Champion | Carson Byblow | TEDxYouth@AASSofia', 'TEDx Talks', 'Growth mindset'],
        ['pN34FNbOKXc', 'The Power of belief -- mindset and success | Eduardo Briceno | TEDxManhattanBeach', 'TEDx Talks', 'Growth mindset'],
        ['0tqq66zwa7g', 'Change your mindset, change the game | Dr. Alia Crum | TEDxTraverseCity', 'TEDx Talks', 'Growth mindset'],
        ['KUWn_TJTrnU', 'Growth Mindset vs. Fixed Mindset', 'Sprouts', 'Growth mindset'],
        ['Pmd6knanPKw', 'Maximize Productivity, Physical & Mental Health With Daily Tools', 'Andrew Huberman', 'Productivity'],
        ['hSGt_rhu49U', 'How to Be So Productive it Feels ILLEGAL', 'Dan Martell', 'Productivity'],
        ['9E2SdLD3Kpc', '30 Habits That (Quietly) Changed My Life Forever', 'RelentX', 'Productivity'],
        ['7EWYjFqgmHM', '17 Micro-Habits to Improve Your Focus', 'Ali Abdaal', 'Productivity'],
        ['FbSNfj2S6Pw', '5 Essential Tips for Long-Lasting Productivity', 'Ali Abdaal', 'Productivity'],
        ['pw1b43X3u-s', 'Boring Everyday Habits That Completely Transform Your Life', 'EverythingProfessor', 'Productivity'],
        ['VBifDZwPiI4', 'How To Defuse Procrastination And Be Productive (Without Using Willpower)', 'HealthyGamerGG', 'Productivity'],
        ['uVzCPllwmqw', 'The Perfect Morning Habits (Backed by Science)', 'The Inspire Path', 'Productivity'],

        // ---- Brainstorming & creativity (15) ------------------------------
        ['kPnDkEwJmng', '6 ways to boost your creativity: Pixar Brainstorming Techniques', 'Find Your Voice', 'Brainstorming'],
        ['UZ_peeloxAA', 'How to generate the most Creative Ideas (even if you are not creative)', 'Nextcore', 'Creativity'],
        ['yAidvTKX6xM', 'Six Creative Ways To Brainstorm Ideas', 'Investis Digital', 'Brainstorming'],
        ['YXZamW4-Ysk', 'Brainstorming Techniques: How to Innovate in Groups', 'Sprouts', 'Brainstorming'],
        ['VvdJzeO9yN8', 'IDEO Brainstorming Video from IDEO U', 'Hackers Design Book', 'Brainstorming'],
        ['e4h53SRClNw', 'The secret to better brainstorming | Dr. Roger Firestien | TEDxOmaha', 'TEDx Talks', 'Brainstorming'],
        ['9mOIgNml-eg', 'How I Run Brainstorming Sessions (UX Framework)', 'Chris from rectangles', 'Brainstorming'],
        ['-OMJINQIZzA', 'How to Generate Ideas with the SCAMPER Technique', 'Michael Masters', 'Creativity'],
        ['8WHm9jzBWAw', '5 ways to create more ideas - brainstorming techniques', 'Ron White Memory Expert', 'Brainstorming'],
        ['mtn31hh6kU4', '4 simple ways to have a great idea | Richard St. John', 'TED Archive', 'Creativity'],
        ['zbj0C5aqE4E', '6-3-5 Brainwriting Method | Brainstorming Techniques', 'Ed Tchoi', 'Brainstorming'],
        ['GLpZ6RZHyoM', 'A Brainstorming Technique for Students that ACTUALLY Works', 'John Spencer', 'Brainstorming'],
        ['bEusrD8g-dM', 'Creative thinking - how to get out of the box and generate ideas | Giovanni Corazza', 'TEDx Talks', 'Creativity'],
        ['2B7aCIuEDxk', '7 Rules for the Perfect Brainstorming Session', 'IDEO U', 'Brainstorming'],
        ['iG9CE55wbtY', 'Do Schools Kill Creativity? | Sir Ken Robinson | TED', 'TED', 'Creativity'],

        // ---- Public speaking & debate (10) -------------------------------
        ['eIho2S0ZahI', 'How to Speak So That People Want to Listen | Julian Treasure | TED', 'TED', 'Public speaking'],
        ['Ks-_Mh1QhMc', 'Your Body Language May Shape Who You Are | Amy Cuddy | TED', 'TED', 'Public speaking'],
        ['D9Ihs241zeg', 'The Danger of a Single Story | Chimamanda Ngozi Adichie | TED', 'TED', 'Public speaking'],
        ['-FOCpMAww28', "TED's secret to great public speaking | Chris Anderson | TED", 'TED', 'Public speaking'],
        ['-VVFwNo_29I', 'How to debate in English: Vocabulary and Phrases', 'ABA English', 'Debate'],
        ['Edg54WnYqkE', 'NEVER Feel Anxiety Again When Giving A Speech (5 EASY Methods)', 'Vinh Giang', 'Public speaking'],
        ['5m-C5mwpmxU', 'The 3-2-1 Speaking Trick That Forces You To Stop Rambling!', 'Vinh Giang', 'Public speaking'],
        ['RWpeq4xPcaI', "Barack Obama's top 4 speaking techniques (no B.S.)", 'Ultraspeaking', 'Public speaking'],
        ['_WjUFuW2J0A', "Train for any argument with Harvard's former debate coach | Bo Seo", 'Big Think', 'Debate'],
        ['2pVdSEp-tT8', 'Debate world champion explains how to argue | Bo Seo', 'Big Think', 'Debate'],

        // ---- English learning (35) ---------------------------------------
        ['ySyPIeBxYBo', 'Improve Your English Speaking Skills | Practice English Listening, Vocabulary and Shadowing', 'English Easy Practice', 'English learning'],
        ['topBLaz4zgk', 'Speak With Me: 2 Hour English Speaking Practice', 'Speak English With Vanessa', 'English learning'],
        ['YGwlBRg4bvY', 'Improve Your English Speaking Skills | Shadowing English Practice', 'Confident English with Kirsty', 'English learning'],
        ['E_70wtDChh0', 'Best Way to Practice Speaking English: Shadowing English Conversation', 'English Easy Practice', 'English learning'],
        ['6E7i_7Mr_5c', 'Repeat-After-Me Speaking Practice with a fun little story', 'English Coach Chad', 'English learning'],
        ['kF9wC2I2Suc', 'Simple English Conversations | Real-Life English for Beginners', 'Proactive English Practice', 'English learning'],
        ['pKYQR9npunU', 'Learn Daily English Conversations in 30 Minutes | Practice English Speaking and Listening', 'Learn English with Jessica', 'English learning'],
        ['aRBzYEn7dhM', 'Improve your Speaking and Conversational skills with me', 'English Fluency Journey', 'English learning'],
        ['ag3RnEaB3zM', 'THINK AND SPEAK IN ENGLISH | How To Talk About Your Daily Life Fluently In English', 'Speak English With Tiffani', 'English learning'],
        ['v07kPyPkLYw', "30 Minutes of Real English Conversation You'll Actually Use (A2-B2)", 'Speak English with David & Alice', 'English learning'],
        ['sHFse8a8uik', 'Simple Daily English Conversations | Listening and Speaking Practice for Beginners', 'Proactive English Practice', 'English learning'],
        ['k0aLziRxyG8', 'Shadowing English Speaking Practice | Speak Fluently 5-Day English Speaking Challenge', 'Speak English with David & Alice', 'English learning'],
        ['QXVzmzhxWWc', 'Improve your English Grammar in One Hour | Basic English Grammar', 'linguamarina', 'English grammar'],
        ['Ljjiw9mC_Cg', 'Learn ALL 16 TENSES Easily in under 30 Minutes', 'English with Lucy', 'English grammar'],
        ['chjmnCSPnbw', 'The 8 Parts of Speech in English Grammar (+ Free PDF & Quiz)', 'English with Lucy', 'English grammar'],
        ['54xQ2CBCwrE', '15 Real-Life English Conversations for Beginners | Easy English Speaking Practice', 'Easy English Practice', 'English learning'],
        ['FfhZFRvmaVY', 'Advanced speaking practice: English Imitation Lesson', 'mmmEnglish', 'English learning'],
        ['GmdcD0QPiPk', '6 Useful English Words You Should Know | Everyday English Vocabulary', 'Learn English Today', 'English learning'],
        ['F30R0tDIXP0', '1 Hour English Vocabulary Course: Learn 250+ Important English Vocabulary Words', 'English with Lucy', 'English learning'],
        ['mgty3Bgu-YY', '600 English Words for Everyday Life - Basic Vocabulary #30', 'Learn English with EnglishClass101.com', 'English learning'],
        ['g50sjKXugwo', '1000 English Words For Daily Speech | English Vocabulary Masterclass', 'JForrest English', 'English learning'],
        ['FatDtR1lkKk', 'I use these words every day: English Vocabulary Lesson', 'Speak English With Vanessa', 'English learning'],
        ['EK8xd8fNV1U', 'How To Learn 100 English Vocabulary Words in 5 Days', 'Speak English With Tiffani', 'English learning'],
        ['ZM6Sg4SN7kU', 'How to Remember English Vocabulary', 'Learn English with Bob the Canadian', 'English learning'],
        ['5Y5TktzkeTI', '3 Things You Need to NEVER Forget Vocabulary Again', 'English Speaking Success', 'English learning'],
        ['ieCV6C-Qe28', 'B2 English Vocab That Native Speakers Use Every Day', 'Interactive English', 'English learning'],
        ['uLN6IdRtDhg', 'Advanced Vocabulary in 60 Minutes (Precise words you need to know!)', 'Interactive English', 'English learning'],
        ['hx36h6bVXZ0', 'Learn English Speaking Practice for Beginners | English Conversation', 'Best English Online', 'English learning'],
        ['DPmtnb8NBog', 'Learn English Conversation - Oxford English Daily Conversation Part 1', 'Learn English with English7Levels', 'English learning'],
        ['henIVlCPVIY', 'Everyday English Conversation Practice | 30 Minutes English Listening', 'English Easy Practice', 'English learning'],
        ['lhFU5H5KPFE', '1.5 HOUR English Conversation Lesson', 'Speak English With Vanessa', 'English learning'],
        ['XSxZGrBn0Vo', '50 Simple English Conversations for Beginners: Real English Dialogues (A1-A2)', 'Enjoy English Practice', 'English learning'],
        ['Mh6RLmlUtgo', 'Build your vocabulary fast: Live English Class', 'BBC Learning English', 'English learning'],
        ['LDkvRFCm8No', 'How can I speak English more fluently? Ask BBC Learning English', 'BBC Learning English', 'English learning'],
        ['I_tRSrPru94', 'How to introduce yourself: Easy English Conversations Episode 1', 'BBC Learning English', 'English learning'],
    ];

    public function run(): void
    {
        foreach (self::VIDEOS as $index => [$youtubeId, $title, $channel, $category]) {
            Video::updateOrCreate(
                ['youtube_url' => "https://www.youtube.com/watch?v={$youtubeId}"],
                [
                    'title' => $title,
                    'description' => "{$category} · {$channel}",
                    'sort_order' => $index + 1,
                    'library' => Video::LIBRARY_MEDIA,
                ]
            );
        }
    }
}
