<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;
use App\Models\JobPosting;
use App\Models\JobApplication;
use Illuminate\Http\Request;

// Show all job postings on home page
Route::get('/', function () {
    $jobPostings = JobPosting::with('ejob', 'department')->latest()->get();
    return view('home', compact('jobPostings'));
})->name('home');

// Show form to apply for a specific job posting
Route::get('/apply/{jobPosting}', function (JobPosting $jobPosting) {
    return view('apply', compact('jobPosting'));
})->name('apply.show');

// Handle form submission for job application
Route::post('/apply/{jobPosting}', function (Request $request, JobPosting $jobPosting) {
    $data = $request->validate([
        'FNAME' => 'required|string|max:255',
        'MNAME' => 'nullable|string|max:255',
        'LNAME' => 'required|string|max:255',
    ]);

    $data['jobpostings_id'] = $jobPosting->id;

    JobApplication::create($data);

    return back()->with('success', 'Application submitted successfully!');
})->name('apply.submit');
