<?php

namespace App\Http\Controllers;

use App\Models\UserRecrute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RecruitmentController extends Controller
{
    /**
     * Display the recruitment application form.
     */
    public function create(): View
    {
        return view('recrute');
    }

    /**
     * Store a newly submitted recruitment application.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'cne' => ['required', 'string', 'max:30'],
            'filiere' => ['required', 'string', 'max:150'],
            'preferred_section' => ['required', 'string', 'in:AR,FR,EN'],
            'motivation' => ['required', 'string', 'min:15', 'max:2500'],
        ], [
            'first_name.required' => 'Please enter your first name.',
            'last_name.required' => 'Please enter your last name.',
            'phone_number.required' => 'Please enter a valid phone number (WhatsApp preferred).',
            'email.required' => 'Please enter your email address.',
            'cne.required' => 'Please provide your CNE / Massar code.',
            'filiere.required' => 'Please specify your filière / academic program.',
            'preferred_section.required' => 'Please select your preferred debate section.',
            'preferred_section.in' => 'Selected debate section is invalid.',
            'motivation.required' => 'Please share a brief motivation explaining why you want to join TGD.',
            'motivation.min' => 'Your motivation should be at least 15 characters long.',
        ]);

        try {
            UserRecrute::create([
                'first_name' => trim($validated['first_name']),
                'last_name' => trim($validated['last_name']),
                'phone_number' => trim($validated['phone_number']),
                'email' => strtolower(trim($validated['email'])),
                'cne' => strtoupper(trim($validated['cne'])),
                'filiere' => trim($validated['filiere']),
                'preferred_section' => $validated['preferred_section'],
                'motivation' => trim($validated['motivation']),
            ]);

            return redirect()->route('recrute')->with('success', 'Your recruitment application has been received! Welcome to The Great Debaters community. The Bureau will contact you shortly via WhatsApp / email.');
        } catch (\Throwable $e) {
            Log::error('Failed to store recruitment application', [
                'error' => $e->getMessage(),
                'payload' => $request->except(['_token']),
            ]);

            // If the table is not migrated yet in user's DB, still give clear guidance
            return redirect()->route('recrute')
                ->withInput()
                ->with('error', 'Could not save your application right now (' . $e->getMessage() . '). Please ensure the database table "users_recrute" exists.');
        }
    }
}
