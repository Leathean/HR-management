<!DOCTYPE html>
<html>
<head>
    <title>Job Openings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0 20px 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: white;
            border-bottom: 1px solid #ddd;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .site-name {
            font-weight: bold;
            font-size: 1.5em;
            color: #008e15;
            text-decoration: none;
        }

        nav a {
            margin-left: 20px;
            color: #555;
            text-decoration: none;
            font-weight: 600;
        }

        nav a:hover {
            color: #008e15;
        }

        h1 {
            text-align: center;
            color: #333;
            margin: 30px 0 30px;
        }

        /* Grid container for cards */
        .job-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto 40px;
        }

        .job-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }

        .job-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }

        .job-title {
            font-size: 1.5em;
            margin-bottom: 8px;
            color: #008e15;
        }

        .job-detail {
            margin-bottom: 6px;
            color: #555;
        }

        .apply-link {
            display: inline-block;
            margin-top: 12px;
            padding: 10px 16px;
            background-color: #008e15;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

        .apply-link:hover {
            background-color: #008e15;
        }

        @media (max-width: 900px) {
            .job-grid {
                grid-template-columns: repeat(2, 1fr);
                max-width: 800px;
            }
        }

        @media (max-width: 600px) {
            header {
                flex-direction: column;
                align-items: flex-start;
            }

            nav {
                margin-top: 10px;
            }

            nav a {
                margin-left: 0;
                margin-right: 15px;
            }

            .job-grid {
                grid-template-columns: 1fr;
                max-width: 400px;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="{{ route('home') }}" class="site-name">HRMs</a>
        <nav>
            {{-- <a href="/about-us">About Us</a> --}}
            <a href="/admin/login">Login</a>
        </nav>
    </header>

    <h1>Available Job Postings</h1>

    <div class="job-grid">
        @foreach ($jobPostings as $job)
            <div class="job-card">
                <div class="job-title">{{ $job->ejob->EJOB_NAME ?? 'Untitled Job' }}</div>
                <div class="job-detail"><strong>Department:</strong> {{ $job->department->DP_NAME ?? 'N/A' }}</div>
                <div class="job-detail"><strong>Posted:</strong> {{ $job->POSTED_DATE->format('F j, Y') }}</div>
                <a class="apply-link" href="{{ route('apply.show', $job->id) }}">Apply Now</a>
            </div>
        @endforeach
    </div>

</body>
</html>
