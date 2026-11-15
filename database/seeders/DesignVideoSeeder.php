<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class DesignVideoSeeder extends Seeder
{
    /**
     * Design-manager library: 100 practical YouTube tutorials about video
     * editing — CapCut basics, transitions, editing tricks, beat sync,
     * sound design, text animation, keyframes, masking, and colour grading.
     *
     * Each entry: [youtube id, title, channel, category].
     *
     * These videos live in the `design` library and are only visible to
     * users with the design_manager role.
     *
     * @var array<int, array{0: string, 1: string, 2: string, 3: string}>
     */
    private const VIDEOS = [
        // ---- CapCut basics (12) -------------------------------------------
        ['j5_471mO14c', 'CapCut Tutorial for Beginners (2026) – Full Video Editing Guide', 'Metics Media', 'CapCut basics'],
        ['Zv9u0aLkfK0', 'CapCut Beginner Tutorial (2026) - Beginner to Pro', 'Matt Loui', 'CapCut basics'],
        ['ImKkcOE3y70', 'Learn CapCut in 10 Minutes - Beginner Tutorial (PC & MAC)', 'Gavin Herman', 'CapCut basics'],
        ['qfHX2cNA4MY', 'CapCut Tutorial for Beginners', 'ClipCookie', 'CapCut basics'],
        ['EMDrgqepVhM', 'CapCut Tutorial for Beginners 2026', 'Metics Media', 'CapCut basics'],
        ['P51CqlPOE_w', 'CapCut App Video Editing Tutorial - For Beginners', 'Dee Nimmin', 'CapCut basics'],
        ['68KNzsmBarM', 'CapCut Video Editing Tutorial for Beginners (2026) | Full Course', 'Lerin Nic', 'CapCut basics'],
        ['nEwHL9GRuFk', 'In Depth CapCut Mobile Video Editing Masterclass', 'Content Creators', 'CapCut basics'],
        ['gERszKkxZOw', 'In Depth CapCut Video Editing Tutorial - Mac & PC', 'Content Creators', 'CapCut basics'],
        ['924il_WauMk', 'Learn CapCut in 15 Minutes - Complete Mobile Video Editing Tutorial', 'Stewart and Alina', 'CapCut basics'],
        ['emp1iL8TRJg', 'Learn CapCut in 15 Minutes (Beginner Tutorial)', 'Joey Edits', 'CapCut basics'],
        ['pHMoPh6mzm4', 'CapCut Beginners Tutorial: Edit for Free on Mac & PC', 'Ben Claremont', 'CapCut basics'],

        // ---- Transitions (14) ---------------------------------------------
        ['qSmyogAJudM', "You Won't Use CapCut Transitions Again, After This Video!", 'Davinci', 'Transitions'],
        ['d0UVlG-cQmg', 'How to Make a Smooth Transition in CapCut - Easy Tutorial', 'THE APP EXPERTS', 'Transitions'],
        ['S9zIQqBQW94', 'CapCut: 3 Smooth Transitions Tutorial - Easy Method', 'SiLeX EDITZ', 'Transitions'],
        ['CrqKi3gTouA', 'Top 5 Cinematic Transitions in CapCut PC', 'Joey Edits', 'Transitions'],
        ['NtqmdaTzHN4', 'Top 10 Smooth & Seamless Transitions in CapCut PC', 'Joey Edits', 'Transitions'],
        ['Rqpnfu31FEo', 'How to Do Transitions on CapCut Mobile - Full Guide', 'BeamNG Terms TV', 'Transitions'],
        ['WJf4dCHAMDI', 'CapCut Trending Smooth Transition Tutorial', 'Zettai Edits', 'Transitions'],
        ['xKAfWs6Uy3U', 'Easy Transitions Without Using Pro | CapCut Edit Tutorial', 'VENOM BLINK', 'Transitions'],
        ['iHeizt3CT0o', 'Trending Transition Tutorial on CapCut', 'One4Eleven', 'Transitions'],
        ['CNWTF-6R50M', 'CapCut Slide Transition Tutorial', 'Way to Editz', 'Transitions'],
        ['pC9TYHhXzgA', 'CapCut Mask Transition - CapCut PC Tutorial', 'Faizan Amjed Official', 'Transitions'],
        ['ZArbr8KEggI', 'CapCut Smooth Slide Transition Tutorial - No Pro', 'celestialeclipse', 'Transitions'],
        ['u9rr3oCZdgg', 'Transition Ideas for You | CapCut Tutorial', 'YourMina', 'Transitions'],
        ['m3AWKeaXHGQ', 'CapCut Cool AMV Transitions - Full Tutorial', 'Editiverse', 'Transitions'],

        // ---- Editing tricks (18) ------------------------------------------
        ['pkyDzEHpZao', '21 Best CapCut Tricks to Take You from a Noob to Pro', 'Matt Loui', 'Editing tricks'],
        ['I49YEojjtf8', '10 CapCut Editing Hacks That Will Blow Your Mind', 'Modern Millie', 'Editing tricks'],
        ['ol7PQ_arWPg', 'Best CapCut Video Editing Tips and Tricks', 'Kevin Stratvert', 'Editing tricks'],
        ['HgE4SDjg6Qc', '4 Editing Tricks Famous Editors Use - But Never Teach! (CapCut Tutorial)', 'Vaze9h', 'Editing tricks'],
        ['odxTRtU0HH8', '7 Stupidly Simple CapCut Tricks You Need to Know', 'Zeven 7', 'Editing tricks'],
        ['LpPZlf5s31E', '10 Insane CapCut Editing Tricks', 'Trevor Jones', 'Editing tricks'],
        ['YUvG0hzwTsg', '8 Sneaky YouTube Editing Techniques and How to Do Them in CapCut', 'Matt Loui', 'Editing tricks'],
        ['m-pKiIE2UH4', '15 Pro Editing Techniques You Can Do in Free CapCut', 'Matt Loui', 'Editing tricks'],
        ['DDLlvJFTNw4', 'Insane CapCut Editing Tricks You Need to Master', 'Matt Loui', 'Editing tricks'],
        ['chjI9UVl6p8', "10 CapCut Tricks That Look Hard and Aren't", 'Matt Loui', 'Editing tricks'],
        ['gnWHHxr6TVs', '13 Mindblowing Video Editing Tips (With CapCut on Desktop)', 'FILM CRUX', 'Editing tricks'],
        ['SxFYxB5EhGo', 'CapCut Is Hard Until You Learn These 10 Tricks', 'Matt Loui', 'Editing tricks'],
        ['TnnQRfQafVA', 'Advanced CapCut Tutorial: How to Make Pro Level Edits', 'Collin Michael', 'Editing tricks'],
        ['-LdTocMoppA', '5 Effects in CapCut That Instantly Upgrade Your Edits', 'Zeven 7', 'Editing tricks'],
        ['R9BNOSurmM0', '15 Crazy CapCut Tricks to Go from Noob to Pro', 'Matt Loui', 'Editing tricks'],
        ['Ri9gUz9iNto', "10 CapCut Tricks Every Pro Editor Uses (But You Don't)", 'Patrik Key', 'Editing tricks'],
        ['KVws1GVHoA0', '20 CapCut Mobile Effects to Completely Change Your Editing', 'Matt Loui', 'Editing tricks'],
        ['VXDstEJSFeA', '5 Creative CapCut Edits You Need to Try', 'Luka B', 'Editing tricks'],

        // ---- Music & beat sync (12) ---------------------------------------
        ['LKeyhTQMFoE', 'Auto-Sync Your Video to the Music Beat in CapCut', 'AIVisualBlend', 'Beat sync'],
        ['glY9XA8Zbio', 'CapCut Beat Tracking Tutorial | Sync Clips to Music', 'Vid & Views', 'Beat sync'],
        ['VfPSA6t8FRY', 'How to Use CapCut Beat Sync on Phone - Step by Step', 'TechGuidePro', 'Beat sync'],
        ['eHYWEwuIW2Y', 'How to Auto Sync Beats in CapCut (Automatic Beat Editing)', 'Xpertise Logs', 'Beat sync'],
        ['KhKHX3dXzWA', 'CapCut Beat Sync Tutorial - Auto Beat Sync on PC', 'Graphic Guru', 'Beat sync'],
        ['y1x4we1T52s', 'How to Hit Beats in CapCut PC | Perfect Beat Sync Tutorial', 'Xpertise Logs', 'Beat sync'],
        ['ZCYBTcyOGAA', 'CapCut Beat Sync Tutorial | Manual Beats + Auto Beats Explained', 'MOTION RAJ FX', 'Beat sync'],
        ['rW77XjQVUiM', 'Best CapCut Tutorial for Syncing and Velocity', 'Boltzys', 'Beat sync'],
        ['GNnPcP9glI4', 'How to Auto-Sync Video to Music Beat in CapCut', 'GPSOLVED', 'Beat sync'],
        ['Z91XqK1ts_k', 'CapCut Auto Beat Sync Transitions (Full Tutorial)', 'Graphic Guru', 'Beat sync'],
        ['nApQplWQXrk', 'Perfect Beat Sync and Smooth Velocity on Mobile Using CapCut', 'THeOnE YT', 'Beat sync'],
        ['q-4CYD8RUj0', 'How to Make a Beat Sync Effect in CapCut', 'MJ Cinematics', 'Beat sync'],

        // ---- Sound design (14) --------------------------------------------
        ['853KtBoJnG4', 'Master Sound Effects in CapCut: Step-by-Step Guide', 'InstantTutorials', 'Sound design'],
        ['uG5CP9FtyCM', 'Make Your Videos 10x Better with CapCut Sound Effects', 'Harry Ortega', 'Sound design'],
        ['eDuEd5TMjSQ', '4 CapCut Sensory Sound Design Techniques', 'Matt Loui', 'Sound design'],
        ['05RfJDgJ4Pw', 'How I Sound Design My Video in CapCut (Secret Sauce)', 'byl', 'Sound design'],
        ['ZDqfifJ8nZw', 'CapCut Audio Editing for Beginners - Everything You Need to Know', 'Vince Opra', 'Sound design'],
        ['7jwbnsckq6M', 'How to Make Your CapCut Edits Sound Professional', 'Joey Edits', 'Sound design'],
        ['DFjfHMnAlsY', 'CapCut Sound Editing Tutorial: Music, Voice-Overs & SFX Explained', 'Miracamp', 'Sound design'],
        ['KpzkG1mRFgU', 'Master Smooth Song Transitions in CapCut', 'Knowfinity', 'Sound design'],
        ['zOTmSUlOEO0', 'How to Use Sound Effects in CapCut (Complete Guide)', 'Joey Edits', 'Sound design'],
        ['5SBB725PiqI', '20 Must-Have CapCut Sound Effects for Your Next Video', 'Creative Suite Tutorials', 'Sound design'],
        ['PnT2Xj-gv94', 'How to Add Sound Effects in CapCut PC', 'Guidie', 'Sound design'],
        ['wdVxTBneIVY', 'How to Extract Sound from a Video in CapCut', 'Babis Gabe', 'Sound design'],
        ['IyHO0qEBy9E', '5 Sound FX You Need in CapCut', 'RSquared Edits', 'Sound design'],
        ['EUH5luBMUDo', 'Top 10 Favorite Sound Effects in CapCut', 'Claire - UGC Creator', 'Sound design'],

        // ---- Text animation (12) ------------------------------------------
        ['FBhiUxlE2Sw', 'How to Master Smooth Text Animations in CapCut', 'Zeven 7', 'Text animation'],
        ['-KrVdWPhMV8', 'How to Master Text Animation in CapCut (Under 10 Minutes)', 'Patrik Key', 'Text animation'],
        ['rfS9nA2RmJA', '8 Most Viral Text Effects Inside CapCut - Detailed Tutorial', 'Anmol_type.', 'Text animation'],
        ['Shgr6TkJTDI', 'Typography Motion Graphics Animation in CapCut', 'Matt Loui', 'Text animation'],
        ['rFD_U3OZzTk', '8 Premium CapCut Text Effects', 'Matt Loui', 'Text animation'],
        ['3TtgmGpnNKQ', '11 Best Free Text Effects in CapCut', 'Matt Loui', 'Text animation'],
        ['WNS-n_O21TQ', '10 Viral CapCut Text Effects', 'Matt Loui', 'Text animation'],
        ['T0GAkQPAmVI', 'How to Make a Typography Edit in CapCut', 'BeTheCut', 'Text animation'],
        ['D0Z0PdBcfpQ', 'How to Make Smooth Animated Captions in CapCut (Step-by-Step)', 'dayotheecreator', 'Text animation'],
        ['ss9LAZqUroY', 'Viral Text Animation in CapCut - Beginner to Advanced MasterClass', 'Cr8ive Kane', 'Text animation'],
        ['oUU1Lj7hcNU', 'How to Make Viral Captions in CapCut', 'Aeovisuals', 'Text animation'],
        ['Q04FF5KZyt4', 'How to Create 10 Viral Caption Styles Using CapCut', 'Patrik Key', 'Text animation'],

        // ---- Keyframes (8) ------------------------------------------------
        ['fKNDlT5JbTY', 'Keyframe Mastery Guide in CapCut - For Next Level Edits', 'Zeven 7', 'Keyframes'],
        ['4eLZq5VgkBk', 'CapCut Keyframe Mastery: Level Up Your Edits', 'Collin Michael', 'Keyframes'],
        ['AmfGZA_-tIQ', 'How to Use Keyframes in CapCut Video Editor', 'Collin Michael', 'Keyframes'],
        ['nbER-edESt8', 'Keyframe Mastery Explained | CapCut Tutorial', 'Rene Mlekuz', 'Keyframes'],
        ['NhXA_Y34w0g', "The Only CapCut Keyframe Tutorial You'll Ever Need (Smooth Animation)", 'Graphic Guru', 'Keyframes'],
        ['4owzZ1q15Nc', 'How to Use Keyframes in CapCut (The Way Pros Actually Do It)', 'Dina and Agnes', 'Keyframes'],
        ['LixgCbC0QqI', 'Keyframe Graphs Tutorial - Smooth Animation in CapCut', 'AVR.EDIT1', 'Keyframes'],
        ['DI-ziBV87Ho', 'Master Keyframing in CapCut PC: A Complete Beginner Guide', 'Joey Edits', 'Keyframes'],

        // ---- Masking (6) --------------------------------------------------
        ['rrIMVxRPidI', '5 Must-Know Masking Tips in CapCut PC', 'Rene Mlekuz', 'Masking'],
        ['42VONJM74fs', 'How to Use Masking in CapCut for Cool Effects', 'Collin Michael', 'Masking'],
        ['v9Qxc7curqk', '5 Creative Masking Effects in CapCut', 'EditStudio', 'Masking'],
        ['mF6rK8AW0_4', 'Why You Should Be Using CapCut (Masking Tutorial)', 'Fusion Frames', 'Masking'],
        ['NZPz0v-51i8', 'CapCut Tutorial: 5 Creative Editing Tricks Using Masks', 'Deny King', 'Masking'],
        ['w-8P5qI2kfo', 'How to Mask in CapCut PC - Beginners Tutorial', 'Joey Edits', 'Masking'],

        // ---- Colour grading (4) -------------------------------------------
        ['TMnBLZy3WFg', 'Fast, Easy & Free! Cinematic Color Grading in CapCut', 'Joris Hermans', 'Color grading'],
        ['6ZQLtduEbZ4', 'Master CapCut Color Grading in 30 Minutes', 'Content Creators', 'Color grading'],
        ['3-AGFutV8CU', 'CapCut Color Grading 101 (Tutorial)', 'Collin Michael', 'Color grading'],
        ['kCvbnf3IFRc', 'How to Color Grade Like a Pro in CapCut - Free Course (Part 1)', 'Joey Edits', 'Color grading'],
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
                    'library' => Video::LIBRARY_DESIGN,
                ]
            );
        }
    }
}
