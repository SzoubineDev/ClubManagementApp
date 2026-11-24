<?php

namespace App\Http\Controllers;

use App\Models\UserRecrute;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class RecruitmentController extends Controller
{
    /**
     * Display the closed-recruitment notice (applications are over for the season).
     */
    public function create(): View
    {
        return view('recrute');
    }

    /**
     * Display the late application form.
     */
    public function createLate(): View
    {
        return view('late-recrute');
    }

    /**
     * Store a newly submitted recruitment application.
     *
     * The regular season is closed, so any application received from now on is
     * flagged as late, even if it reaches the old endpoint directly.
     */
    public function store(Request $request): RedirectResponse
    {
        return $this->storeApplication(
            $request,
            'recrute',
            'Your recruitment application has been received! Welcome to The Great Debaters community. The Bureau will contact you shortly via WhatsApp / email.'
        );
    }

    /**
     * Store a late recruitment application.
     */
    public function storeLate(Request $request): RedirectResponse
    {
        return $this->storeApplication(
            $request,
            'late_recrute',
            'Your late recruitment application has been received! The Bureau will review it and contact you shortly via WhatsApp / email.'
        );
    }

    /**
     * Validate and persist an application in the shared `users_recrute` table.
     */
    private function storeApplication(Request $request, string $redirectRoute, string $successMessage): RedirectResponse
    {
        // Normalize the identity fields BEFORE validation so the unique check
        // and the value we persist are compared in the same normalized form
        // (case/whitespace differences can no longer slip past the rule and
        // then explode against the table's UNIQUE index).
        $email = $request->input('email');
        $cne = $request->input('cne');

        $request->merge([
            'email' => is_string($email) ? strtolower(trim($email)) : $email,
            'cne' => is_string($cne) ? strtoupper(trim($cne)) : $cne,
        ]);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'cne' => ['required', 'string', 'max:30', 'unique:users_recrute,cne'],
            'filiere' => ['required', 'string', 'max:150'],
            'preferred_section' => ['required', 'string', 'in:AR,FR,EN'],
            'motivation' => ['required', 'string', 'min:15', 'max:2500'],
        ], [
            'first_name.required' => 'Please enter your first name.',
            'last_name.required' => 'Please enter your last name.',
            'phone_number.required' => 'Please enter a valid phone number (WhatsApp preferred).',
            'email.required' => 'Please enter your email address.',
            'cne.required' => 'Please provide your CNE / Massar code.',
            'cne.unique' => 'An application with this CNE / Massar code has already been submitted.',
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
                'email' => $validated['email'],
                'cne' => $validated['cne'],
                'filiere' => trim($validated['filiere']),
                'preferred_section' => $validated['preferred_section'],
                'motivation' => trim($validated['motivation']),
                'is_late' => true,
            ]);

            return redirect()->route($redirectRoute)->with('success', $successMessage);
        } catch (QueryException $e) {
            // Log the exception object only — never the request payload, which
            // contains the applicant's personal data (name, email, phone, CNE).
            Log::error('Failed to store recruitment application', ['exception' => $e]);

            // Never surface raw database errors to the applicant.
            return redirect()->route($redirectRoute)
                ->withInput()
                ->with('error', 'We could not save your application right now. Please try again in a moment or contact the club directly.');
        }
    }
}
